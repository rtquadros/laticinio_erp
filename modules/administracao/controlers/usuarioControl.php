<?php
require_once('../../../loader.php');

if(isset($_GET['func']) && !empty($_GET['func'])){
  $usuario = new Usuario();
  
  // Trata os inputs
  if(isset($_POST) && !empty($_POST)){
    $args = array(
      "usu_nome" => FILTER_SANITIZE_SPECIAL_CHARS,
      "usu_senha" => FILTER_SANITIZE_SPECIAL_CHARS,
      "usu_nivel" => FILTER_SANITIZE_NUMBER_INT,
      "usu_modulos" => array("filter" => FILTER_SANITIZE_SPECIAL_CHARS, "flags" => FILTER_REQUIRE_ARRAY)
    );
    $param = filter_input_array(INPUT_POST, $args);
    
    // usu_senha
    $param["usu_senha"] = md5($param["usu_senha"]);

    // usu_modulos
    $usu_modulos = array();
    foreach($mod_arr as $modulo){
      $arr = array_intersect($modulo["pagina"], $_POST["usu_modulos"]);
      if(count($arr) > 0)
        $usu_modulos += array($modulo["modulo"] => array_values($arr));
    }
    $param["usu_modulos"] = serialize($usu_modulos);
  }

  if(isset($_GET) && !empty($_GET)){
    $id = filter_input(INPUT_GET, "usu_id", FILTER_SANITIZE_NUMBER_INT);
  }

  if($_GET['func'] == 'insertUsuario'){
    $result = $usuario->insertUsuario($param);
		$header = '../../../index.php?mod=administracao&pag=usuario&func=cadastrar';
  }
  if($_GET['func'] == 'updateUsuario'){ 
    if($param["usu_senha"] == "") $param["usu_senha"] = $usuario->getUsuSenha($id);
    $result = $usuario->updateUsuario($param, $id);
  } 
  if($_GET['func'] == 'deleteUsuario') $result = $usuario->deleteUsuario($id); 
	
  require_once("../../../includes/controlersCallBack.php");
} else {
	header("Location:../../../index.php?mod=administracao&pag=usuario&func=visualizar");
}
