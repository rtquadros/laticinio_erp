<?php
require_once("../loader.php");

// Faz o login
$loguser = new Loguser();
if(isset($_POST['usu_nome']) && isset($_POST['usu_senha'])){
	$usu_nome = filter_input(INPUT_POST, "usu_nome", FILTER_SANITIZE_SPECIAL_CHARS);
	$usu_senha = filter_input(INPUT_POST, "usu_senha", FILTER_SANITIZE_SPECIAL_CHARS);
	if($loguser->login($usu_nome, $usu_senha)){
		unset($_SESSION['result']);
		header('location: ../index.php');
	} else {
		$_SESSION['result'] = array('erro'=> true, 'msg'=> 'Houve um erro no login.');	
	}
}