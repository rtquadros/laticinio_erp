<?php
require_once(ABSPATH."/class/ConnDb.php");

class Producao extends ConnDb{
  
  private $campos = array("producao_rec_id", "producao_data_ordem", "producao_data_entrega", "producao_quant", "producao_func_id");

  public function setProducaoDataEntrega($data, $id){
    $crud = $this->updateDb("producao", "producao_data_entrega", "producao_id={$id}", array($data));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Produção entregue com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function selectProducao($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "producao", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertProducao($param){
    $crud = $this->insertDb("producao", $this->campos, "?, ?, ?, ?, ?", array_values($param));
    if($crud) 
	    $result = array('erro'=>false, 'msg'=>'Produção cadastrada com êxito!', 'objeto_id'=>$this->lastInsertId());
    else 
	    $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function updateProducao($param, $id){
    $crud = $this->updateDb("producao", $this->campos, "producao_id={$id}", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Produção editada com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function deleteProducao($id){
    $crud = $this->deleteDb("producao", "producao_id={$id}", $id);
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Produção excluída com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

}