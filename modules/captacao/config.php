<?php
/* 
Configurações do módulo de Configurações do Sistema
*/
global $mod_config;

$mod_config = array(
	'menu_ordem' => 1, // Ordem no menu
	"menu_titulo" => "<span class='fas fa-tint'></span> Captação",
	'modulo' => "captacao",
	'pagina' => array(
		'Produtores' => 'produtor',
		'Linhas de coleta' => 'linha',
		'Chegada de leite' => 'leite',
		'separator' => '',
		'Pagamento de leite' => 'pagLeite',
	)
);
?>