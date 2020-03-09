<?php
require_once('../../../loader.php');

if(isset($_GET['func']) && !empty($_GET['func'])){
  $conta = new Conta();
  
  // Trata os inputs
  if(isset($_POST) && !empty($_POST)){
    $args = array(
    "conta_desc" => FILTER_SANITIZE_SPECIAL_CHARS, 
    "conta_data_abertura" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")), 
    "conta_saldo_inicial" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")), 
    "conta_saldo" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")), 
    "conta_saldo_projetado" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney"))
    );
    $param = filter_input_array(INPUT_POST, $args);
  }

  if(isset($_GET) && !empty($_GET)){
    $id = filter_input(INPUT_GET, "conta_id", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertConta'){
    $result = $conta->insertConta($param);
		$header = '../../../index.php?mod=financeiro&pag=conta&func=cadastrar';
  }
  if($_GET['func'] == 'updateConta'){ 
    $result = $conta->updateConta($param, $id);
  } 
  if($_GET['func'] == 'deleteConta') $result = $conta->deleteConta($id);
  if($_GET['func'] == 'getSaldo'){
    $result = $conta->getSaldo($id);
    echo json_encode($result);
    exit();
  }
	
  require_once("../../../includes/controlersCallBack.php");
} else {
	header("Location:../../../index.php?mod=financeiro&pag=conta&func=visualizar");
}
