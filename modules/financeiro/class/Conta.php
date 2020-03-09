<?php
require_once(ABSPATH."class/ConnDb.php");

class Conta extends ConnDb{
  private $campos = array("conta_desc", "conta_data_abertura", "conta_saldo_inicial", "conta_saldo", "conta_saldo_projetado");
  
  public function getDesc($id){
    $result = $this->selectConta("conta_desc", "WHERE conta_id=?", array($id));
    return $result[0]["conta_desc"];
  }

  public function getSaldo($id){
  	$result = $this->selectConta("conta_saldo", "WHERE conta_id=?", array($id));
  	return $result[0]["conta_saldo"];
  }

  public function getSaldoProjetado($id){
  	$result = $this->selectConta("conta_saldo_projetado", "WHERE conta_id=?", array($id));
  	return $result[0]["conta_saldo_projetado"];
  }

  public function selectConta($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "conta", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function updateConta($campos, $param, $id){
  	if(empty($campos)) $campos = $this->campos;
    $crud = $this->updateDb("conta", $campos, "conta_id={$id}", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Conta editada com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }
}