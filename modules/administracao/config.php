<?php
/* 
Configurações do módulo de Configurações do Sistema
*/
global $mod_config;

$mod_config = array(
	'menu_ordem' => 1, // Ordem no menu
	"menu_titulo" => "<span class='fas fa-cogs'></span> Administração",
	'modulo' => "administracao",
	'pagina' => array(
		'Usuários' => 'usuario',
		'Log de usuários' => 'logUsuario',
		'Configura&ccedil;&otilde;es' => 'configuracoes',
		'separator' => '',
		'Backup banco de dados' => 'dbbackup'
	)
);
?>