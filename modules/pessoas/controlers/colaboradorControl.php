<?php
require_once('../../../loader.php');
$header = "../../../index.php?mod=pessoas&pag=colaborador&func=visualizar";

if(isset($_GET['func']) && !empty($_GET['func'])){
  $pessoa = new Pessoa();

  // Trata os inputs
  if(isset($_POST) && !empty($_POST)){
    $args = array(
      "pessoa_nome" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_apelido" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_documento" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_inscricao" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_email" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_tel" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_endereco" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_bairro" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_cep" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_municipio" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_estado" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_desc" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "pessoa_categoria" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "data_admissao" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")), 
      "cargo" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "salario" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")), 
      "bonificacao" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")), 
      "vendedor_comissionado" => FILTER_SANITIZE_NUMBER_INT
    );
    $param = filter_input_array(INPUT_POST, $args);

    // Preenche a chave "pessoa_variaveis"
    $arr = array("data_admissao", "cargo", "salario", "bonificacao", "vendedor_comissionado");
    foreach ($arr as $key => $value) {
      $pessoa_variaveis[$value] = $param[$value];
      unset($param[$value]);
    }
    $param["pessoa_variaveis"] = serialize($pessoa_variaveis);
    //print_r($param);
  }

  if(isset($_GET) && !empty($_GET)){
    $id = filter_input(INPUT_GET, "pessoa_id", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertPessoa'){
    $result = $pessoa->insertPessoa($param);
		$header = "../../../index.php?mod=pessoas&pag=colaborador&func=cadastrar";
  }
  if($_GET['func'] == 'updatePessoa'){ 
    $result = $pessoa->updatePessoa($param, $id);
  } 
  if($_GET['func'] == 'deletePessoa'){
    $pessoa_id_arr = explode(',', $id);
    foreach($pessoa_id_arr as $pessoa_id){
      $result = $pessoa->deletePessoa($pessoa_id);
    }
  }
	
  require_once("../../../includes/controlersCallBack.php");
} else {
	header($header);
}
