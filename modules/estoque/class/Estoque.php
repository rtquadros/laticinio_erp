<?php
require_once(ABSPATH."/class/ConnDb.php");

class Estoque extends ConnDb{
  
  private $campos = array("estoque_prod_id", "estoque_lote", "estoque_entrada_id", "estoque_data_entrada", "estoque_quant_entrada", "estoque_quantidade", "estoque_saida_id", "estoque_fabricacao", "estoque_validade", "estoque_custo");

  public function selectEstoque($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "estoque", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertEstoque($param){
    $crud = $this->insertDb("estoque", $this->campos, "?, ?, ?, ?, ?, ?, ?, ?, ?", array_values($param));
    if($crud) 
	    $result = array('erro'=>false, 'msg'=>'Estoque cadastrado com êxito!', 'objeto_id'=>$this->lastInsertId());
    else 
	    $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function updateEstoque($param, $id){
    $crud = $this->updateDb("estoque", $this->campos, "estoque_id={$id}", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Estoque editado com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function deleteEstoque($id){
    $crud = $this->deleteDb("estoque", "estoque_id={$id}", $id);
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Estoque excluído com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

}