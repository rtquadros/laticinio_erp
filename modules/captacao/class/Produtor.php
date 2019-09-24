<?php
require_once(ABSPATH."modules/pessoas/class/Pessoa.php");

class Produtor extends Pessoa{
  
  private $campos = array("preco_leite", "linha_coleta");

  public function getLinhaProdutor($id){
    $result = $this->selectPessoa("pessoa_variaveis", "WHERE pessoa_id=?", array($id));
    $variaveis = unserialize($result[0]['pessoa_variaveis']);
    return $variaveis['linha_coleta'];
  }
  
  public function getPrecoProdutor($id){
    $result = $this->selectPessoa("pessoa_variaveis", "WHERE pessoa_id=?", array($id));
    $variaveis = unserialize($result[0]['pessoa_variaveis']);
    return number_format($variaveis['preco_leite'], 2, ',', '.');
  }

  public function getProdutorNome($id){
    return $this->getPessoaNome($id);
  }

  public function getProdutorVar($id){
    return $this->getPessoaVar($id);
  }

  public function selectProdutor($campos, $condicoes, $param){
    $result = $this->selectPessoa($campos, $condicoes, $param);
    foreach ($result as $key => $value) {
      if($value["pessoa_categoria"] == "produtor") $result_filter[$key] = $value;  
    }
    return $result_filter;
  }

  public function insertProdutor($param){
    $result = $this->insertPessoa($param);
    if(!$result["erro"]) $result["msg"] = "Produtor cadastrado com êxito!";
    return $result;
  }

  public function updateProdutor($param, $id){
    $result = $this->updatePessoa($param, $id);
    if(!$result["erro"]) $result["msg"] = "Produtor editado com êxito!";
    return $result;
  }

  public function deleteProdutor($id){
    $result = $this->deletePessoa($id);
    if(!$result["erro"]) $result["msg"] = "Produtor excluído com êxito!";
    return $result;
  }

}