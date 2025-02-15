<?php
// Carrega as configurações globais
require_once("global_config.php");

// Evita que usuários acesse este arquivo diretamente
if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] )) {
  header( 'HTTP/1.0 404 Forbidden', TRUE, 404 );
  die( header( 'Location: index.php' ) );
}
 
// Inicia a sessão
session_start();

// Verifica o modo para debugar
if ( ! defined('DEBUG') || DEBUG === false ) {
  // Esconde todos os erros
  error_reporting(0);
  ini_set("display_errors", 0); 
} else {
  // Mostra todos os erros
  error_reporting(E_ALL);
  ini_set("display_errors", 1); 
}

/**
 * Função para carregar automaticamente todas as classes padrão
 * Ver: http://php.net/manual/pt_BR/language.oop5.autoload.php
 * Nossas classes estão na pasta class/.
 * O nome do arquivo deverá ser class.NomeDaClasse.php.
 */
spl_autoload_register(function ($class_name) {
  $file = strtolower(ABSPATH . 'class/' . $class_name . '.php');
 
  if ( ! file_exists( $file ) ) {
    $file = '';
    $mods_folders = scandir( MODULES_ABSPATH );
    foreach( $mods_folders as $folder ){
      if( is_dir( MODULES_ABSPATH ."/".$folder. '/class' )){
        $temp_file = strtolower(MODULES_ABSPATH."/".$folder.'/class/' . $class_name . '.php');
        if ( file_exists( $temp_file ) ) $file = $temp_file;
      }
    }
    if ( $file == '' ) {
      echo 'Erro ao incluir o arquivo: '.$temp_file;
      return;	
    }
  }
 
  // Inclui o arquivo da classe
  require_once $file;
}); 

// Carrega todos os modulos organizados em pastas na pasta 'modules', lê os seus arquivos config.php

$mods_folders = scandir( MODULES_ABSPATH );
$mod_arr = array(); // Todos os modulos carregados

foreach( $mods_folders as $folder ){
  if( is_dir( MODULES_ABSPATH ."/".$folder )){
    if( is_file( MODULES_ABSPATH."/".$folder . '/config.php' )){
      $mod_path = MODULES_ABSPATH."/".$folder . '/config.php';
      include_once( $mod_path );
      if( isset( $mod_config )){ 
        array_push( $mod_arr, $mod_config ); // Adiciona as informações do módulo
      }
    }
  }
}

// Define parâmetros de data do sistema
if(isset($_POST['mes_ref']) && !empty($_POST['mes_ref'])){ 
  $mes_ref = filter_input(INPUT_POST, "mes_ref", FILTER_SANITIZE_SPECIAL_CHARS);
  $mes_ref = explode("/", $mes_ref);
  $mes_ref = new DateTime("{$mes_ref[1]}-$mes_ref[0]-01");
  $_SESSION['mes_ref'] = $mes_ref->format("Y-m-d");
} elseif(isset($_SESSION['mes_ref']) && !empty($_SESSION['mes_ref']) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$_SESSION['mes_ref'])) {
  $mes_ref = new DateTime($_SESSION['mes_ref']);
} else {
  $mes_ref = new DateTime();
}
$today = new DateTime();

$data_arr = array(1=>array(1, 15), 2=>array(16, $mes_ref->format("t")));