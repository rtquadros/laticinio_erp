<?php
require_once('../../../loader.php');

if(isset($_GET['func']) && !empty($_GET['func'])){
  $linha = new Linha();
  
  // Trata os inputs
  if(isset($_POST)){
    $args = array(
      "linha_nome" => FILTER_SANITIZE_SPECIAL_CHARS,
      "linha_carreteiro" => FILTER_SANITIZE_NUMBER_INT, 
      "linha_comissao" => array("filter"=>FILTER_SANITIZE_NUMBER_FLOAT, "flags"=>FILTER_FLAG_ALLOW_THOUSAND)
    );
    $param = filter_input_array(INPUT_POST, $args);
 
    $param["linha_comissao"] = str_replace(",", ".", $param["linha_comissao"]);
  }

  if(isset($_GET)){
    $id = filter_input(INPUT_GET, "linha_id", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertLinha'){
    $result = $linha->insertLinha($param);
		$header = '../../../index.php?mod=captacao&pag=linha&func=cadastrar';
  }
  if($_GET['func'] == 'updateLinha'){ 
    $result = $linha->updateLinha($param, $id);
  } 
  if($_GET['func'] == 'deleteLinha') $result = $linha->deleteLinha($id); 
	
  require_once("../../../includes/controlersCallBack.php");
} else {
	header("Location:../../../index.php?mod=captacao&pag=linha&func=visualizar");
}
