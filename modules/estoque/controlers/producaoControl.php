<?php
require_once('../../../loader.php');
$header = "../../../index.php?mod=estoque&pag=producao&func=visualizar";

if(isset($_GET['func']) && !empty($_GET['func'])){
  $producao = new Producao();

  // Trata os inputs
  if(isset($_POST) && !empty($_POST)){
    $args = array(
      "producao_rec_id" => FILTER_SANITIZE_NUMBER_INT, 
      "producao_data_ordem" => array('filter' => FILTER_SANITIZE_SPECIAL_CHARS, 'flags' => FILTER_REQUIRE_ARRAY),
      "producao_data_entrega" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")), 
      "producao_quant" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")),
      "producao_func_id" => FILTER_SANITIZE_NUMBER_INT
    );
    $param = filter_input_array(INPUT_POST, $args);
    $data_entrega = array_reverse(explode("/", $param["producao_data_ordem"][0]));
    $param["producao_data_ordem"][0] = implode("-", $data_entrega);
    $param["producao_data_ordem"] = date( "Y-m-d H:i:s", strtotime(implode(" ", $param["producao_data_ordem"])));

    //print_r($param);
  }

  if(isset($_GET) && !empty($_GET)){
    $id = filter_input(INPUT_GET, "producao_id", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertProducao'){
    $result = $producao->insertProducao($param);
		$header = "../../../index.php?mod=estoque&pag=producao&func=cadastrar";
  }
  if($_GET['func'] == 'updateProducao'){ 
    $result = $producao->updateProducao($param, $id);
  } 
  if($_GET['func'] == 'deleteProducao'){
    $producao_id_arr = explode(',', $id);
    foreach($producao_id_arr as $producao_id){
      $result = $producao->deleteProducao($producao_id);
    }
  }

  require_once("../../../includes/controlersCallBack.php");
} else {
	header($header);
}
