<?php
require_once(ABSPATH."class/ConnDb.php");
require_once("Linha.php");

class Leite extends ConnDb{
  
  private $campos = array( "leite_data", "leite_prod_id", "leite_quantidade", "leite_preco", "leite_linha_id");
  
  public function getTotalLeite($data_ini, $data_fim){
    $result = $this->selectLeite("SUM(leite_quantidade) AS leite_total", "WHERE leite_data BETWEEN ? AND ?", array($data_ini, $data_fim));
    return $result["leite_total"];
  }

  public function getTotalComissaoLinha($linha_id, $data_ini, $data_fim){
    $linha = new Linha();
    $result = $this->selectLeite("SUM(leite_quantidade) AS leite_total", "WHERE leite_linha_id=? AND leite_data BETWEEN ? AND ?", array($linha_id, $data_ini, $data_fim));
    $leite_total = $result[0]["leite_total"];
    
    $result = $linha->selectLinha("linha_comissao", "WHERE linha_id=?", array($linha_id));
    $linha_comissao = $result[0]["linha_comissao"];

    return number_format($leite_total * $linha_comissao, 2, ",", ".");
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
      return number_format(($custo_total_leite + $custo_total_leite)/$total_leite, 2, ',', '.');
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
      if($crud) 
        $result = array('erro'=>false, 'msg'=>'Leite cadastrado com êxito!', 'objeto_id'=>$this->lastInsertId());
      else 
        $result = array('erro'=>true, 'msg'=>$crud->errorInfo());
    }

    return $result;
  }

  public function updateLeite($param, $id){
    $crud = $this->updateDb("leite", $this->campos, "leite_id={$id}", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Leite editado com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

}