<?php
require_once('../../../loader.php');

if(isset($_GET['func']) && !empty($_GET['func'])){
  $produtor = new Produtor();
  
  // Trata os inputs
  if(isset($_POST)){
    $args = array(
      "pessoa_nome" => FILTER_SANITIZE_SPECIAL_CHARS,
      "pessoa_apelido" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_documento" => array("filter"=>FILTER_SANITIZE_NUMBER_FLOAT, "flags"=>FILTER_FLAG_ALLOW_FRACTION), 
      "pessoa_inscricao" => array("filter"=>FILTER_SANITIZE_NUMBER_FLOAT, "flags"=>FILTER_FLAG_ALLOW_FRACTION), 
      "pessoa_email" => FILTER_SANITIZE_EMAIL, 
      "pessoa_tel" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_endereco" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_bairro" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_cep" => array("filter"=>FILTER_SANITIZE_NUMBER_FLOAT, "flags"=>FILTER_FLAG_ALLOW_FRACTION), 
      "pessoa_municipio" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_estado" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_desc" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_categoria" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "preco_leite" => array("filter"=>FILTER_SANITIZE_NUMBER_FLOAT, "flags"=>FILTER_FLAG_ALLOW_THOUSAND), 
      "linha_coleta" => FILTER_SANITIZE_NUMBER_INT
    );
    $param = filter_input_array(INPUT_POST, $args);

    // Preenche a chave 'pessoa_variaveis' e exclui 'preço_leite' e 'linha_coleta' pra inserção no banco 
    $param["pessoa_variaveis"] = serialize(array("preco_leite"=>str_replace(",", ".", $param["preco_leite"]), "linha_coleta"=>$param["linha_coleta"]));
    unset($param["preco_leite"]);
    unset($param["linha_coleta"]);

  }

  if(isset($_GET)){
    $id = filter_input(INPUT_GET, "pessoa_id", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertProdutor'){
    $result = $produtor->insertProdutor($param);
		$header = '../../../index.php?mod=captacao&pag=produtor&func=cadastrar';
  }
  if($_GET['func'] == 'updateProdutor'){ 
    $result = $produtor->updateProdutor($param, $id);
  } 
  if($_GET['func'] == 'deleteProdutor') $result = $produtor->deleteProdutor($id); 
	
  require_once("../../../includes/controlersCallBack.php");
} else {
	header("Location:../../../index.php?mod=captacao&pag=produtor&func=visualizar");
}
