<?php
require_once('../../../loader.php');
$header = "../../../index.php?mod=captacao&pag=leite&func=visualizar";

if(isset($_GET['func']) && !empty($_GET['func'])){
  $config = new Configuracoes();
  $leite = new Leite();
  $produtor = new Produtor();
  $linha = new Linha();
  
  // Trata os inputs
  if(isset($_POST) && !empty($_POST)){
    $args = array(
      "leite_data" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")),
      "leite_prod_id" => FILTER_SANITIZE_NUMBER_INT, 
      "leite_quantidade" => FILTER_SANITIZE_NUMBER_INT, 
      "leite_preco" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeMoney")), 
      "leite_linha_id" => FILTER_SANITIZE_NUMBER_INT
    );
    $param = filter_input_array(INPUT_POST, $args);
  }

  if(isset($_GET) && !empty($_GET)){
    $id = filter_input(INPUT_GET, "leite_id", FILTER_SANITIZE_NUMBER_INT);
    $prod_id = filter_input(INPUT_GET, "prod_id", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertLeite'){
    $result = $leite->insertLeite($param);
		$header = '../../../index.php?mod=captacao&pag=leite&func=cadastrar';
  }
  if($_GET['func'] == 'updateLeite'){ 
    $result = $leite->updateLeite($param, $id);
  }  

  if($_GET['func'] == 'ajaxGetLeite'){ 
    $row = $produtor->selectProdutor("*", "WHERE pessoa_id=?", array($prod_id));
    $var = unserialize($row[0]['pessoa_variaveis']);
    //Nome da linha de coleta
    $linha_nome = $linha->getLinhaNome($var['linha_coleta']);
    $arr = array('leite_preco'=>$var['preco_leite'],'linha_id'=>$var['linha_coleta'],'linha_nome'=>$linha_nome);
    echo json_encode($arr);
    exit();
  }
	
  require_once("../../../includes/controlersCallBack.php");
} else {
	header($header);
}
