<?php
require_once('../../../loader.php');
$header = "../../../index.php?mod=estoque&pag=producao&func=visualizar";

if(isset($_GET['func']) && !empty($_GET['func'])){
  $producao = new Producao();
  $produto = new Produto();

  // Trata os inputs
  if(isset($_POST) && !empty($_POST)){
    $args = array( 
      "producao_ordem" => FILTER_SANITIZE_NUMBER_INT,
      "producao_data_ordem" => array('filter' => FILTER_SANITIZE_SPECIAL_CHARS, 'flags' => FILTER_REQUIRE_ARRAY),
      "producao_prod_id" => FILTER_SANITIZE_NUMBER_INT,
      "producao_func_id" => FILTER_SANITIZE_NUMBER_INT,
      "producao_quant" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney"))
    );
    $param = filter_input_array(INPUT_POST, $args);
    $data_entrega = array_reverse(explode("/", $param["producao_data_ordem"][0]));
    $param["producao_data_ordem"][0] = implode("-", $data_entrega);
    $param["producao_data_ordem"] = date( "Y-m-d H:i:s", strtotime(implode(" ", $param["producao_data_ordem"])));

    $param["producao_insumos"] = serialize(json_decode($_POST["producao_insumos"], true));
    $param["producao_processos"] = serialize(json_decode($_POST["producao_processos"], true));
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
  if($_GET['func'] == 'entradaEstoque'){
    $result = $producao->selectProducao("*", "WHERE producao_id = ?", array($id));
  
    $fabricacao = new DateTime($result[0]["producao_data_ordem"]);
    $validade = new DateTime($result[0]["producao_data_ordem"]);
    $prod_validade = $produto->getValidadeProduto($result[0]['producao_prod_id']);
    $validade->add(new DateInterval("P{$prod_validade}D"));

    $estoque_custo = number_format($produto->getCustoProduto($result[0]["producao_prod_id"]) * $result[0]["producao_quant"], 2, ",", "");

    $arr_json = array(
      "producao_id" => $id,
      "prod_nome" => $produto->getNomeProduto($result[0]["producao_prod_id"]),
      "prod_unidade" => $produto->getUnidadeProduto($result[0]["producao_prod_id"]),
      "estoque_lote" => $producao->gerarLote($result[0]["producao_id"]),
      "estoque_quant_entrada" => number_format($result[0]["producao_quant"], 2, ",", ""),
      "estoque_custo" => $estoque_custo,
      "estoque_fabricacao" => $fabricacao->format("d/m/Y"),
      "estoque_validade" => $validade->format("d/m/Y"),
      "estoque_prod_id" => $result[0]["producao_prod_id"],
      "estoque_entrada_id" => array("producao" => $result[0]["producao_id"])
    );

    echo json_encode($arr_json);
    exit();
  }

  require_once("../../../includes/controlersCallBack.php");
} else {
	header($header);
}
