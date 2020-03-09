<?php
require_once('../../../loader.php');
$header = "../../../index.php?mod=financeiro&pag=movimentacao&func=visualizar";

if(isset($_GET['func']) && !empty($_GET['func'])){
  $mov = new Movimentacao();

  // Trata os inputs
  if(isset($_POST) && !empty($_POST)){
    $args = array(
      "mov_tipo" => FILTER_SANITIZE_NUMBER_INT, 
      "mov_data" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")), 
      "mov_desc" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "mov_pessoa_id" => FILTER_SANITIZE_NUMBER_INT, 
      "mov_valor" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")), 
      "mov_categoria" => FILTER_SANITIZE_NUMBER_INT, 
      "mov_pago" => FILTER_SANITIZE_NUMBER_INT, 
      "mov_forma_pag" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "mov_detalhes" => FILTER_SANITIZE_SPECIAL_CHARS, 
      "mov_nf" => FILTER_SANITIZE_NUMBER_INT, 
      "mov_conta_id" => FILTER_SANITIZE_NUMBER_INT, 
      "mov_variaveis" => FILTER_SANITIZE_SPECIAL_CHARS
    );
    $param = filter_input_array(INPUT_POST, $args);

    // Corrige erro com valor bollean mov_pago
    if(empty($param["mov_pago"]) || !isset($param["mov_pago"])) $param["mov_pago"] = "0";
  }

  if(isset($_GET) && !empty($_GET)){
    $id = filter_input(INPUT_GET, "mov_id", FILTER_SANITIZE_NUMBER_INT);
    $mov_conta_id = filter_input(INPUT_GET, "mov_conta_id", FILTER_SANITIZE_NUMBER_INT);
    $mov_tipo = filter_input(INPUT_GET, "mov_tipo", FILTER_SANITIZE_NUMBER_INT);
    $mov_pago = filter_input(INPUT_GET, "mov_pago", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertMovimentacao'){
    //Caso haja parcelas da movimentação
    $mov_parcela_n = filter_input(INPUT_POST, "mov_parcela_n", FILTER_SANITIZE_NUMBER_INT);
    $mov_parcela = filter_input(INPUT_POST, "mov_parcela", FILTER_SANITIZE_NUMBER_INT);
    if(isset($mov_parcela_n) && $mov_parcela_n > 1){
      $data = new DateTime($param["mov_data"]);
      $descricao = $param["mov_desc"];
      for($i=1 ; $i<=$mov_parcela_n ; $i++){
        $param['mov_desc'] = $descricao.' - '.$i.'/'.$mov_parcela_n;
        print_r($param);
        $result = $mov->insertMovimentacao($param);
        $data->add(new DateInterval("P{$mov_parcela}D"));
        $param['mov_data'] = $data->format('Y-m-d');
      }
    } else 
      $result = $mov->insertMovimentacao($param);
		$header = "../../../index.php?mod=financeiro&pag=movimentacao&func=cadastrar&mov_tipo={$mov_tipo}";
  }
  if($_GET['func'] == 'updateMovimentacao'){ 
    $result = $mov->updateMovimentacao($param, $id);
  } 
  if($_GET['func'] == 'deleteMovimentacao'){
    $mov_id_arr = explode(',', $_GET['mov_id']);
    foreach($mov_id_arr as $mov_id){
      $result = $mov->deleteMovimentacao($mov_id);
    }
  }
  if($_GET['func'] == 'setPago'){
    $result = $mov->setPago($id, $mov_pago);
    echo json_encode($result);
    exit();
  }
  if($_GET["func"] == "duplicateMovimentacao"){
    $mov_id_arr = explode(',', $_GET['mov_id']);
    foreach($mov_id_arr as $mov_id){
      $result = $mov->duplicateMovimentacao(array("mov_conta_id" => $mov_conta_id, "mov_tipo" => $mov_tipo), $mov_id);
    }
  }
  if($_GET["func"] == "moveMovimentacao"){
    $mov_id_arr = explode(',', $_GET['mov_id']);
    foreach($mov_id_arr as $mov_id){
      $result = $mov->moveMovimentacao(array("mov_conta_id" => $mov_conta_id, "mov_tipo" => $mov_tipo), $mov_id);
    }
  }
	
  require_once("../../../includes/controlersCallBack.php");
} else {
	header($header);
}
