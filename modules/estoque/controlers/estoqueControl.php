<?php
require_once('../../../loader.php');
$header = "../../../index.php?mod=estoque&pag=estoque&func=visualizar";

if(isset($_GET['func']) && !empty($_GET['func'])){
  $estoque = new Estoque();
  $producao = new Producao();

  // Trata os inputs
  if(isset($_POST) && !empty($_POST)){
    $args = array(
      "estoque_prod_id" => FILTER_SANITIZE_NUMBER_INT,
      "estoque_lote" => FILTER_SANITIZE_SPECIAL_CHARS,
      "estoque_data_entrada" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")),
      "estoque_quant_entrada" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")),
      "estoque_fabricacao" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")),
      "estoque_validade" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")),
      "estoque_custo" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney"))
    );
    $param = filter_input_array(INPUT_POST, $args);
    $param = array_slice($param, 0, 2, true) + array("estoque_entrada_id" => serialize(json_decode($_POST["estoque_entrada_id"], true))) + array_slice($param, 2, 5, true);
    $param = array_slice($param, 0, 5, true) + array("estoque_quant_atual" => $param["estoque_quant_entrada"]) + array_slice($param, 5, 5, true);

    //print_r($param);
  }

  if(isset($_GET) && !empty($_GET)){
    $id = filter_input(INPUT_GET, "estoque_id", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertEstoque'){
    $producao_id = filter_input(INPUT_POST, "producao_id", FILTER_SANITIZE_NUMBER_INT);
    $result = $producao->setProducaoDataEntrega($param["estoque_data_entrada"], $producao_id);
    if(!$result["erro"]){
      $result = $estoque->insertEstoque($param);
      $header = "../../../index.php?mod={$_GET['mod']}&pag={$_GET['pag']}&func=visualizar";
    }
  }
  if($_GET['func'] == 'updateEstoque'){ 
    $result = $estoque->updateEstoque($param, $id);
  } 
  if($_GET['func'] == 'deleteEstoque'){
    $estoque_id_arr = explode(',', $id);
    foreach($estoque_id_arr as $estoque_id){
      $result = $estoque->deleteEstoque($estoque_id);
    }
  }
  if($_GET['func'] == 'getEstoque'){
    $prod_id = filter_input(INPUT_GET, "prod_id", FILTER_SANITIZE_NUMBER_INT);
    $result = $estoque->selectEstoque("*", "WHERE estoque_prod_id=? AND estoque_quant_atual>0 ORDER BY estoque_validade ASC", array($prod_id));
    echo json_encode($result);
    exit();
  }

  require_once("../../../includes/controlersCallBack.php");
} else {
	header($header);
}
