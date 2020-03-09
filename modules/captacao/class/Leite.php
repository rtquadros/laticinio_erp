<?php
require_once(ABSPATH."class/ConnDb.php");
require_once("Linha.php");
if(is_file(ABSPATH."/modules/financeiro/class/Movimentacao.php")) require_once(ABSPATH."/modules/financeiro/class/Movimentacao.php");
if(is_file(ABSPATH."/modules/administracao/class/Configuracoes.php")) require_once(ABSPATH."/modules/administracao/class/Configuracoes.php");

class Leite extends ConnDb{
  
  private $campos = array( "leite_data", "leite_prod_id", "leite_quantidade", "leite_preco", "leite_linha_id");
  
  public function getTotalLeite($data_ini, $data_fim){
    $result = $this->selectLeite("SUM(leite_quantidade) AS leite_total", "WHERE leite_data BETWEEN ? AND ?", array($data_ini, $data_fim));
    return $result["leite_total"];
  }

  public function getTotalLeiteProdutor($leite_prod_id, $data_ini, $data_fim){
    $result = $this->selectLeite("SUM(leite_quantidade) AS leite_total", "WHERE leite_prod_id=? AND leite_data BETWEEN ? AND ?", array($leite_prod_id, $data_ini, $data_fim));
    return $result[0]["leite_total"];
  }

  public function getValorLeiteProdutor($leite_prod_id, $data_ini, $data_fim){
    $retorno = $this->selectLeite("*", "WHERE leite_prod_id=? AND leite_data BETWEEN ? AND ?", array($leite_prod_id, $data_ini, $data_fim));
    $valor_total_produtor = 0;
    foreach ($retorno as $key => $value) {
      $valor_total_produtor += $value["leite_quantidade"] * $value["leite_preco"];
    }
    return $valor_total_produtor;
  }

  public function getPrecoLeiteProdutor($leite_prod_id, $data_ini, $data_fim){
    $valor_total_produtor = $this->getValorLeiteProdutor($leite_prod_id, $data_ini, $data_fim);
    $total_leite = $this->getTotalLeiteProdutor($leite_prod_id, $data_ini, $data_fim);
    $preco_leite_produtor = round($valor_total_produtor/$total_leite, 2);
    if($preco_leite_produtor !== 0) return $preco_leite_produtor;
    else return 0;
  }

  public function getTotalComissaoLinha($linha_id, $data_ini, $data_fim){
    $linha = new Linha();
    $result = $this->selectLeite("SUM(leite_quantidade) AS leite_total", "WHERE leite_linha_id=? AND leite_data BETWEEN ? AND ?", array($linha_id, $data_ini, $data_fim));
    $leite_total = $result[0]["leite_total"];
    
    $result = $linha->selectLinha("linha_comissao", "WHERE linha_id=?", array($linha_id));
    $linha_comissao = $result[0]["linha_comissao"];

    return $leite_total * $linha_comissao;
  }

  public function getCustoMedioLeite($data_ini, $data_fim){
    $total_leite = 0;
    $custo_total_leite = 0; 
    $custo_total_carreto = 0;
    $result = $this->selectLeite("*", "WHERE leite_data BETWEEN ? AND ?", array($data_ini, $data_fim));
    foreach ($result as $key => $value) {
      $total_leite += $value["leite_quantidade"];
      $custo_total_leite += $value["leite_quantidade"] * $value["leite_preco"];
    }
    $linha = new Linha();
    $result = $linha->selectLinha("*", "", array());
    foreach ($result as $key => $value) {
      $custo_total_carreto += $this->getTotalComissaoLinha($value["linha_id"], $data_ini, $data_fim);
    }
    if($total_leite !== 0)
      return number_format(($custo_total_leite + $custo_total_carreto)/$total_leite, 2, ',', '.');
    else
      return 0;
  }

  public function selectLeite($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "leite", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertLeite($param){
    // Checa se o leite já foi cadastrado antes
    if($retorno = $this->selectLeite("*", "WHERE leite_data=? AND leite_prod_id=?", array($param["leite_data"], $param["leite_prod_id"]))){
      $result = array('erro'=>true, 'msg'=>'Leite já cadastrado nesta data!', 'objeto_id'=>$retorno[0]["leite_id"]);
    } else {
      $crud = $this->insertDb("leite", $this->campos, "?, ?, ?, ?, ?", array_values($param));
      if($crud) {
        // Adiciona movimentacao financeira
        $retorno = $this->movimentacaoLeite($param);
        if(!$retorno["erro"])
          $result = array('erro'=>false, 'msg'=>'Leite cadastrado com êxito!', 'objeto_id'=>$this->lastInsertId());
        else
          $result = array('erro'=>true, 'msg'=>'Leite cadastrado, mas houve erro no registro da movimentação financeira!', 'objeto_id'=>$this->lastInsertId());
      } else 
        $result = array('erro'=>true, 'msg'=>$crud->errorInfo());
    }

    return $result;
  }

  public function updateLeite($param, $id){
    $crud = $this->updateDb("leite", $this->campos, "leite_id={$id}", array_values($param));
    if($crud) {
      // Adiciona movimentacao financeira
      $retorno = $this->movimentacaoLeite($param);
      if(!$retorno["erro"])
        $result = array('erro'=>false, 'msg'=>'Leite editado com êxito!', 'objeto_id'=>$this->lastInsertId());
      else
        $result = array('erro'=>true, 'msg'=>'Leite editado, mas houve erro no registro da movimentação financeira!', 'objeto_id'=>$this->lastInsertId());
    } else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function movimentacaoLeite($param){
    if (class_exists('Movimentacao') && class_exists("Configuracoes")) {
      $mov = new Movimentacao();
      $config = new Configuracoes();
    }
    else return array("erro"=>true, "msg"=>"Classe 'Movimentacao' ou 'Conta' não foi encontrada!");

    $linha = new Linha();
    $arr = array("Leite coletado", "Comissão carreto");
    
    // Determina datas de início e fim da quinzena
    $leite_data = new Datetime($param["leite_data"]);
    if($leite_data->format("j") <= 15) $leite_data = array($leite_data->format("Y-m-01"), $leite_data->format("Y-m-15"));
    else $leite_data = array($leite_data->format("Y-m-16"), $leite_data->format("Y-m-t"));

    foreach($arr as $key=>$value){
      if($key === 0){
        $mov_pessoa_id = $param["leite_prod_id"];
        $mov_valor = $this->getValorLeiteProdutor($param["leite_prod_id"], $leite_data[0], $leite_data[1]);
        $mov_categoria = $config->getConfig("mov_cat_leite");
      } else {
        $mov_pessoa_id = $linha->getLinhaCarreteiro($param["leite_linha_id"]);
        $mov_pessoa_id = $mov_pessoa_id["pessoa_id"];
        $mov_valor = $this->getTotalComissaoLinha($param["leite_linha_id"], $leite_data[0], $leite_data[1]);
        $mov_categoria = $config->getConfig("mov_cat_carreto");
      }
      //Verifica se o movimento já existe e caso positivo altera o valor
      if($retorno = $mov->selectMovimentacao("mov_id", "WHERE mov_pessoa_id=? AND mov_variaveis=? AND mov_data BETWEEN ? AND ?", array($mov_pessoa_id, "modCaptacao", $leite_data[0], $leite_data[1]))){
        $result = $mov->setValor($retorno[0]["mov_id"], $mov_valor);
      }else {
        $mov_dados = array(
          'mov_tipo' => 3, 
          'mov_data' => $leite_data[1], 
          'mov_desc' => $value, 
          'mov_pessoa_id' => $mov_pessoa_id, 
          'mov_valor' => $mov_valor, 
          'mov_categoria' => $mov_categoria, 
          'mov_pago' => false, 
          'mov_forma_pag' => '', 
          'mov_detalhes' => '', 
          'mov_nf' => '', 
          'mov_conta_id' => $config->getConfig("conta_padrao"), 
          'mov_variaveis' => 'modCaptacao'
        );
        $result = $mov->insertMovimentacao($mov_dados);
      }
    }
  }

}