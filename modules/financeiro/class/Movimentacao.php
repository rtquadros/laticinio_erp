<?php
require_once(ABSPATH."class/ConnDb.php");
require_once("Conta.php");

class Movimentacao extends ConnDb{
  private $campos = array("mov_tipo", "mov_data", "mov_desc", "mov_pessoa_id", "mov_valor", "mov_categoria", "mov_pago", "mov_forma_pag", "mov_detalhes", "mov_nf", "mov_conta_id", "mov_variaveis");
  private $mov_tipo = array(1=>"receita", 2=>"despesa fixa", 3=>"despesa variável", 4=>"pessoas", 5=>"impostos");
  private $mov_forma_pag = array('Nota', 'Dinheiro', 'Boleto', 'Crédito', 'Tranferência', 'Cheque' );

  public function getMovTipos(){
  	return $this->mov_tipo;
  }

  public function getMovFormaPag(){
  	return $this->mov_forma_pag;
  }

  public function getMovContaId($id){
    $retorno = $this->selectMovimentacao("mov_conta_id", "WHERE mov_id=?", array($id));
    return $retorno[0]["mov_conta_id"];
  }

  public function getExtractMov($param){
    foreach($param as $key => $value){
      if($value != ""){
        if($key == "mov_data_ini" || $key == "mov_data_fim"){
          $param_data[] = $value;
        } else {
          if(is_array($value)) $sql_arr[] = "$key IN (".implode(",", $value).")";
          else $sql_arr[] = "$key = ?";
          $param_temp[] = $value;
        }
      }
    }
    array_push($param_temp, $param_data[0], $param_data[1]);
    $condicoes = "WHERE ";
    if(!empty($sql_arr)) $condicoes .= implode(" AND ", $sql_arr);
    $condicoes .= ' AND mov_data BETWEEN ? AND ? ORDER BY mov_data DESC';

    $result = $this->selectMovimentacao("*", $condicoes, array_values($param_temp));
    return $result;
  }

  public function getMovTotal($param){
    $param = array_filter($param);
    $condicoes = "WHERE mov_conta_id=? AND mov_tipo=? AND mov_pago=1";
    if(isset($param["mov_categoria"]) && !empty($param["mov_categoria"])) $condicoes .= " AND mov_categoria=?";
    $condicoes .= " AND mov_data BETWEEN ? AND ?";
    
    $result = $this->selectMovimentacao("SUM(mov_valor) AS mov_valor", $condicoes, array_values($param));
    return $result[0]["mov_valor"];
  }

  public function setValor($id, $value){
    $result = $this->updateMovimentacao(array($value), $id, array("mov_valor"));
    return $result;
  }

  public function setPago($id, $value){
    if(!$value) $value = "0";
    $result = $this->updateMovimentacao(array($value), $id, array("mov_pago"));
    return $result;
  }

  public function selectMovimentacao($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "movimentacao", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertMovimentacao($param){
    $crud = $this->insertDb("movimentacao", $this->campos, "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?", array_values($param));
    if($crud) {
      // Atualiza saldo da conta
      $retorno = $this->updateSaldoConta($param["mov_conta_id"]);
      if(!$retorno["erro"])
        $result = array('erro'=>false, 'msg'=>'Movimentação cadastrada com êxito!', 'objeto_id'=>$this->lastInsertId());
      else
      	$result = array('erro'=>true, 'msg'=>'Movimentação cadastrada, mas houve erro na atualização do saldo da conta!', 'objeto_id'=>$this->lastInsertId());
    } else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function updateMovimentacao($param, $id, $campos = NULL){
    if($campos === NULL) $campos = $this->campos;
    $mov_conta_id = $this->getMovContaId($id);
    $crud = $this->updateDb("movimentacao", $campos, "mov_id={$id}", array_values($param));
    if($crud) {
      // Atualiza saldo da conta
      $retorno = $this->updateSaldoConta($mov_conta_id);
      if(!$retorno["erro"])
        $result = array('erro'=>false, 'msg'=>'Movimentação editada com êxito!', 'objeto_id'=>$id);
      else
      	$result = array('erro'=>true, 'msg'=>'Movimentação editada, mas houve erro na atualização do saldo da conta!', 'objeto_id'=>$id);
    } else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function deleteMovimentacao($id){
    $mov_conta_id = $this->getMovContaId($id);
    $crud = $this->deleteDb("movimentacao", "mov_id=?", array($id));
    if($crud){ 
      // Atualiza saldo da conta
      $retorno = $this->updateSaldoConta($mov_conta_id);
      if(!$retorno["erro"])
        $result = array('erro'=>false, 'msg'=>'Movimentação excluída com êxito!', 'objeto_id'=>$id);
      else
      	$result = array('erro'=>true, 'msg'=>'Movimentação excluída, mas houve erro na atualização do saldo da conta!', 'objeto_id'=>$id);
    } else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function duplicateMovimentacao($param, $id){
    $mov_conta_id = $this->getMovContaId($id);
    $retorno = $this->selectMovimentacao("*", "WHERE mov_id=?", array($id));
    unset($retorno[0]["mov_id"]);
    $retorno[0]["mov_conta_id"] = $param["mov_conta_id"];
    $retorno[0]["mov_tipo"] = $param["mov_tipo"];
    
    $result = $this->insertMovimentacao($retorno[0]);

    // Atualiza saldo da conta de origem da movimentação
    $retorno = $this->updateSaldoConta($mov_conta_id);

    if(!$result["erro"]) 
      $result["msg"] = 'Movimentação duplicada com êxito!';

    return $result;
  }

  public function moveMovimentacao($param, $id){
    $mov_conta_id = $this->getMovContaId($id);
    $retorno = $this->selectMovimentacao("*", "WHERE mov_id=?", array($id));
    unset($retorno[0]["mov_id"]);
    $retorno[0]["mov_conta_id"] = $param["mov_conta_id"];
    $retorno[0]["mov_tipo"] = $param["mov_tipo"];
    
    $result = $this->updateMovimentacao($retorno[0], $id);

    // Atualiza saldo da conta de origem da movimentação
    $retorno = $this->updateSaldoConta($mov_conta_id);

    if(!$result["erro"]) 
      $result["msg"] = 'Movimentação movida com êxito!';

    return $result;
  }

  private function updateSaldoConta($id){ 
    $arr_saldos = array();
    for($i = 0; $i<=1; $i++){
      if($i == 1) $condicoes = "WHERE mov_conta_id=? AND ";
      else $condicoes = "WHERE mov_conta_id=? AND mov_pago=1 AND ";
      $receita = $this->selectMovimentacao("SUM(mov_valor) AS mov_valor", $condicoes."mov_tipo=1", array($id));
      $despesa = $this->selectMovimentacao("SUM(mov_valor) AS mov_valor", $condicoes."mov_tipo IN (2,3,4,5)", array($id));
      $arr_saldos[$i] = $receita[0]["mov_valor"] - $despesa[0]["mov_valor"];
    }

    $conta = new Conta();
    $result = $conta->updateConta(array("conta_saldo", "conta_saldo_projetado"), $arr_saldos, $id);

    return $result;
  }
}