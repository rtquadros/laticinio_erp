<?php
require_once(ABSPATH."/class/ConnDb.php");

class Pessoa extends ConnDb{
  
  private $campos = array("pessoa_nome", "pessoa_apelido", "pessoa_documento", "pessoa_inscricao", "pessoa_email", "pessoa_tel", "pessoa_endereco", "pessoa_bairro", "pessoa_cep", "pessoa_municipio", "pessoa_estado", "pessoa_desc", "pessoa_categoria", "pessoa_variaveis");

  public function getPessoaNome($id){
    $result = $this->selectPessoa("pessoa_nome", "WHERE pessoa_id=?", array($id));
    return $result[0]["pessoa_nome"];
  }

  public function getPessoaVar($id){
    $result = $this->selectPessoa("pessoa_variaveis", "WHERE pessoa_id=?", array($id));
    return unserialize($result[0]["pessoa_variaveis"]);
  }

  public function selectPessoa($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "pessoas", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertPessoa($param){
    $crud = $this->insertDb("pessoas", $this->campos, "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?", array_values($param));
    if($crud) 
	    $result = array('erro'=>false, 'msg'=>'Pessoa cadastrada com êxito!', 'objeto_id'=>$this->lastInsertId());
    else 
	    $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function updatePessoa($param, $id){
    $crud = $this->updateDb("pessoas", $this->campos, "pessoa_id={$id}", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Pessoa editada com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function deletePessoa($id){
    $crud = $this->deleteDb("pessoas", "pessoa_id={$id}", $id);
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Pessoa excluída com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

}