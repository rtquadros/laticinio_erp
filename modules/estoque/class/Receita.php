<?php
require_once(ABSPATH."/class/ConnDb.php");

class Receita extends ConnDb{
  
  private $campos = array("rec_prod_id", "rec_insumos", "rec_processos");

  public function getRecProdId($id){
    $result = $this->selectReceita("rec_prod_id", "WHERE rec_id=?", array($id));
    return $result[0]["rec_prod_id"];
  }

  public function getDuracaoTotal($id){
    $result = $this->selectReceita("rec_processos", "WHERE rec_id=?", array($id));
    $processos = unserialize($result[0]["rec_processos"]);
    $duracao_total = array("h"=>0, "i"=>0);
    foreach($processos as $processo){
      $duracao = explode(":", $processo["processo_duracao"]);
      $duracao_total["h"] += $duracao[0];
      $duracao_total["i"] += $duracao[1];
    }
    return $duracao_total;
  }

  public function selectReceita($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "receita", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertReceita($param){
    $crud = $this->insertDb("receita", $this->campos, "?, ?, ?", array_values($param));
    if($crud) 
	    $result = array('erro'=>false, 'msg'=>'Receita cadastrada com êxito!', 'objeto_id'=>$this->lastInsertId());
    else 
	    $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function updateReceita($param, $id){
    $crud = $this->updateDb("receita", $this->campos, "rec_id={$id}", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Receita editada com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function deleteReceita($id){
    $crud = $this->deleteDb("receita", "rec_id={$id}", $id);
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Receita excluída com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

}