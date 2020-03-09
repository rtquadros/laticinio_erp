<?php
/* 
Configurações do módulo de Configurações do Sistema
*/
global $mod_config;

$mod_config = array(
	'menu_ordem' => 1, // Ordem no menu
	"menu_titulo" => "<span class='fas fa-piggy-bank'></span> Financeiro",
	'modulo' => "financeiro",
	'pagina' => array(
		'Movimentações' => 'movimentacao',
		'Categorias' => 'categoria',
		'separator' => '',
		'Extratos financeiros' => 'extrato',
		'Demonstrativo (DRE)' => 'demonstrativo'
	)
);
?>