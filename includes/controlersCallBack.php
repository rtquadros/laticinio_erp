<?php
// CÓDIGO REUTILIZADO NOS CONTROLERS DE MÓDULOS DO SISTEMA

// Sanitiza as variaveis que vão para o banco
$args = array(
  "mod" => FILTER_SANITIZE_SPECIAL_CHARS,
  "pag" => FILTER_SANITIZE_SPECIAL_CHARS,
  "func" => FILTER_SANITIZE_SPECIAL_CHARS
);
$_GET = filter_input_array(INPUT_GET, $args);
//Insere o log da atividade
$logAct = new logAct();
$logAct->insertLogAct($result + $_GET);
//Transmite o resultado para página original através de $_SESSION
$_SESSION['result'] = $result;
// Define o header
if(!isset($header)) $header = "../../../index.php";
header('Location:'.$header);