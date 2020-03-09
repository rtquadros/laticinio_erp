<?php
require_once('../../../loader.php');
$header = "../../../index.php?mod=estoque&pag=produto&func=visualizar";

if(isset($_GET['func']) && !empty($_GET['func'])){
  $produto = new Produto();

  // Trata os inputs
  if(isset($_POST) && !empty($_POST)){
    $args = array(
      "prod_nome" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "prod_codbarras" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "prod_marca" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "prod_unidade" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "prod_preco_venda" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")), 
      "prod_preco_custo" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")), 
      "prod_imagem" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "prod_estoque_min" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")),
      "prod_validade" => FILTER_SANITIZE_NUMBER_INT, 
      "prod_tipo" => FILTER_SANITIZE_SPECIAL_CHARS
    );
    $param = filter_input_array(INPUT_POST, $args);
    //print_r($param);
  }

  if(isset($_GET) && !empty($_GET)){
    $id = filter_input(INPUT_GET, "prod_id", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertProduto'){
    $result = $produto->insertProduto($param);
		$header = "../../../index.php?mod=estoque&pag=produto&func=cadastrar";
  }
  if($_GET['func'] == 'updateProduto'){ 
    $result = $produto->updateProduto($param, $id);
  } 
  if($_GET['func'] == 'deleteProduto'){
    $prod_id_arr = explode(',', $id);
    foreach($prod_id_arr as $prod_id){
      $result = $produto->deleteProduto($prod_id);
    }
  }
  if($_GET['func'] == 'getProduto'){
    $result = $produto->selectProduto("*", "WHERE prod_id=?", array($id));
    echo json_encode($result[0]);
    exit();
  }

  require_once("../../../includes/controlersCallBack.php");
} else {
	header($header);
}
