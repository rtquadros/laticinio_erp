<?php
// Evita que usuários acesse este arquivo diretamente
if ( ! defined('ABSPATH')) exit;
 
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
	$file = strtolower(ABSPATH . '/class/class.' . $class_name . '.php');
 
	if ( ! file_exists( $file ) ) {
		$file = '';
		$mods_folders = scandir( ABSPATH. '/modules/' );
		foreach( $mods_folders as $folder ){
			if( is_dir( ABSPATH. '/modules/' .$folder. '/class' )){
				$temp_file = strtolower(ABSPATH . '/modules/'.$folder.'/class/class.' . $class_name . '.php');
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

// Cria os objetos báscios
$db = new Db();
$loguser = new Loguser();
$usuario = new Usuario();
$relacionamento = new Relacionamento();
$configuracoes = new Configuracoes();


/**
 * Carrega todos os modulos organizados em pastas na pasta 'modules', lê os seus arquivos config.php
 */
$default_modules_folder = ABSPATH.'/modules';
$mods_folders = scandir( $default_modules_folder );
$mod_arr = array(); // Todos os modulos carregados

foreach( $mods_folders as $folder ){
	if( is_dir( $default_modules_folder. '/' .$folder )){
		if( is_file( $default_modules_folder. '/' .$folder . '/config.php' )){
			$mod_path = $default_modules_folder. '/' .$folder . '/config.php';
			include_once( $mod_path );
			if( isset( $mod_config )){ 
				array_push( $mod_arr, $mod_config ); // Adiciona as informações do módulo
				//Adiciona os modulos à variavel da classe usuario
				foreach($mod_config['cod_modulos'] as $cod_modulos){
					array_push( $usuario->modulos, $cod_modulos);	
				}
			}
		}
	}
}

// Carrega configurações do sitema
if($db->tableExists('configuracoes')){
	$config = $configuracoes->getConfiguracoes();
	foreach($config as $name => $value){
		define(strtoupper($value['config_name']), $value['config_value']);
	}
}

// Define parâmetros de data
if(isset($_POST['mes_ref'])) $_SESSION['mes_ref'] = $_POST['mes_ref'];

if(isset($_SESSION['mes_ref']) && !empty($_SESSION['mes_ref'])) $mes_ref = $_SESSION['mes_ref'];
else $mes_ref = date('m/Y');
$data = explode('/', $mes_ref);
$mes = $data[0];
$ano = $data[1];
$total_dias_mes = cal_days_in_month(CAL_GREGORIAN,$mes, $ano);
$data_ini_mes = '01/'.$mes_ref;
$data_fim_1quinzena = '15/'.$mes_ref;
$data_ini_2quinzena = '16/'.$mes_ref;
$data_fim_mes = $total_dias_mes.'/'.$mes_ref;
?>