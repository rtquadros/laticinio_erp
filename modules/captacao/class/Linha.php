<?php
require_once(ABSPATH."class/ConnDb.php");

class Linha extends ConnDb{
  
  private $campos = array("linha_nome", "linha_carreteiro", "linha_comissao");

  public function getLinhaNome($id){
    $result = $this->selectLinha("linha_nome", "WHERE linha_id=?", array($id));
    return $result[0]["linha_nome"];
  }

  public function getLinhaCarreteiro($id){
    $carreteiro_id = $this->selectlinha("linha_carreteiro", "WHERE linha_id=?", array($id));
    $carreteiro_nome = $this->selectDb("pessoa_nome", "pessoas", "WHERE pessoa_id=?", array($id));
    $result = array("pessoa_id" => $carreteiro_id, "pessoa_nome" => $carreteiro_nome[0]["pessoa_nome"]);
    return $result;
  }

  public function getLinhaComissao($id){
    $result = $this->selectLinha("linha_comissao", "WHERE linha_id=?", array($id));
    return number_format($result[0]["linha_nome"], 3, ',', '.');
  }

  public function selectLinha($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "linha", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertLinha($param){
    $crud = $this->insertDb("linha", $this->campos, "?, ?, ?", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Linha cadastrada com êxito!', 'objeto_id'=>$this->lastInsertId());
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function updateLinha($param, $id){
    $crud = $this->updateDb("linha", $this->campos, "linha_id={$id}", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Linha editada com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function deleteLinha($id){
    $crud = $this->deleteDb("linha", "linha_id={$id}", $id);
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Linha excluída com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

}