<?php
require_once(ABSPATH."class/ConnDb.php");

class MovCategoria extends ConnDb{
  private $campos = array("cat_nome", "cat_mov_tipo");

  public function getCatNome($id){
  	$result = $this->selectMovCategoria("cat_nome", "WHERE cat_id=?", array($id));
  	return $result[0]["cat_nome"];
  }

  public function selectMovCategoria($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "categorias_movimentacao", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertMovCategoria($param){
    $crud = $this->insertDb("categorias_movimentacao", $this->campos, "?, ?", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Categoria cadastrada com êxito!', 'objeto_id'=>$this->lastInsertId());
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function updateMovCategoria($param, $id){
    $crud = $this->updateDb("categorias_movimentacao", $this->campos, "cat_id={$id}", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Categoria editada com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function deleteMovCategoria($id){
    $crud = $this->deleteDb("categorias_movimentacao", "cat_id={$id}", $id);
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Categoria excluída com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }
}