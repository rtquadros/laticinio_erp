<?php
require_once('../../../loader.php');
$header = "../../../index.php?mod=financeiro&pag=categoria&func=visualizar";

if(isset($_GET['func']) && !empty($_GET['func'])){
  $cat = new MovCategoria();

  // Trata os inputs
  if(isset($_POST) && !empty($_POST)){
    $args = array(
      "cat_nome" => FILTER_SANITIZE_SPECIAL_CHARS,
      "cat_mov_tipo" => FILTER_SANITIZE_NUMBER_INT
    );
    $param = filter_input_array(INPUT_POST, $args);
  }

  if(isset($_GET) && !empty($_GET)){
    $id = filter_input(INPUT_GET, "cat_id", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertMovCategoria'){
    $result = $cat->insertMovCategoria($param);
		$header = "../../../index.php?mod=financeiro&pag=categoria&func=cadastrar";
  }
  if($_GET['func'] == 'updateMovCategoria'){ 
    $result = $cat->updateMovCategoria($param, $id);
  } 
  if($_GET['func'] == 'deleteMovCategoria'){
    $cat_id_arr = explode(',', $id);
    foreach($cat_id_arr as $cat_id){
      $result = $cat->deleteMovCategoria($cat_id);
    }
  }
  if($_GET['func'] == 'getCategorias'){ 
    $result = $cat->selectMovCategoria("*", "WHERE cat_mov_tipo=?", array($mov_tipo));
    echo json_encode($result);
    exit();
  } 
	
  require_once("../../../includes/controlersCallBack.php");
} else {
	header($header);
}
