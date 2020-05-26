<?php
/* 
Configurações do módulo de Configurações do Sistema
*/
global $mod_config;

$mod_config = array(
	'menu_ordem' => 1, // Ordem no menu
	'menu_titulo' => '<span class="fas fa-boxes"></span> Estoque',
	'modulo' => 'estoque',
	'pagina' => array(
		'Itens' => 'produto',
		'Receitas de produção' => 'receita',
		'Controle de produção' => 'producao',
		'separator' => '',
		'Extrato produ&ccedil;&atilde;o' => 'extratoProducao',
		'Relat&oacute;rio de estoque' => 'estoqueReport'
	)
);
?>
