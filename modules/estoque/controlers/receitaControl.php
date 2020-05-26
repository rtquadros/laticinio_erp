<?php
require_once('../../../loader.php');
$header = "../../../index.php?mod=estoque&pag=receita&func=visualizar";

if(isset($_GET['func']) && !empty($_GET['func'])){
  $receita = new Receita();

  // Trata os inputs
  if(isset($_POST) && !empty($_POST)){
    $args = array(
      "rec_descricao" => FILTER_SANITIZE_SPECIAL_CHARS,
      "rec_prod_id" => FILTER_SANITIZE_NUMBER_INT,
      "insumo_id" => array('filter' => FILTER_SANITIZE_NUMBER_INT, 'flags' => FILTER_REQUIRE_ARRAY), 
      "insumo_quant" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")),
      "processo_nome" => array('filter' => FILTER_SANITIZE_SPECIAL_CHARS, 'flags' => FILTER_REQUIRE_ARRAY),
      "processo_equip" => array('filter' => FILTER_SANITIZE_SPECIAL_CHARS, 'flags' => FILTER_REQUIRE_ARRAY),
      "processo_duracao" => array('filter' => FILTER_SANITIZE_SPECIAL_CHARS, 'flags' => FILTER_REQUIRE_ARRAY),
      "processo_limite" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney"))
    );
    $param_temp = filter_input_array(INPUT_POST, $args);
    
    $param["rec_descricao"] = $param_temp["rec_descricao"];
    $param["rec_prod_id"] = $param_temp["rec_prod_id"];
    // Monta os parametros rec_insumos e rec_processos
    foreach($param_temp["insumo_id"] as $key => $insumo_id){
      $param["rec_insumos"][$key] = array("insumo_id" => $insumo_id, "insumo_quant" => $param_temp["insumo_quant"][$key]);
    }
    $param["rec_insumos"] = serialize($param["rec_insumos"]);
    foreach ($param_temp["processo_nome"] as $key => $processo_nome) {
      $param["rec_processos"][$key] = array("processo_nome" => $processo_nome, "processo_equip" => $param_temp["processo_equip"][$key], "processo_duracao" => $param_temp["processo_duracao"][$key], "processo_limite" => $param_temp["processo_limite"][$key]);
    }
    $param["rec_processos"] = serialize($param["rec_processos"]);

  }

  if(isset($_GET) && !empty($_GET)){
    $id = filter_input(INPUT_GET, "rec_id", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertReceita'){
    $result = $receita->insertReceita($param);
		$header = "../../../index.php?mod=estoque&pag=receita&func=cadastrar";
  }
  if($_GET['func'] == 'updateReceita'){ 
    $result = $receita->updateReceita($param, $id);
  } 
  if($_GET['func'] == 'deleteReceita'){
    $rec_id_arr = explode(',', $id);
    foreach($rec_id_arr as $rec_id){
      $result = $receita->deleteReceita($rec_id);
    }
  }
  if($_GET['func'] == 'getReceita'){
    $result = $receita->selectReceita("*", "WHERE rec_id=?", array($id));
    $result[0]['rec_processos'] = unserialize($result[0]['rec_processos']);
    $result[0]['rec_insumos'] = unserialize($result[0]['rec_insumos']);
    echo json_encode($result[0]);
    exit();
  }

  require_once("../../../includes/controlersCallBack.php");
} else {
	header($header);
}
