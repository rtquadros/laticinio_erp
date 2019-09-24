<?php
/* 
Configurações do módulo de Configurações do Sistema
*/
global $mod_config;

$mod_config = array(
	'menu_ordem' => 1, // Ordem no menu
	"menu_titulo" => "<span class='fas fa-users'></span> Pessoas",
	'modulo' => "pessoas",
	'pagina' => array(
		'Funcionários' => 'funcionario',
		'Clientes' => 'cliente',
		'Fornecedores' => 'fornecedor',
		'separator' => '',
		'Folha de pagamento' => 'pagFunc',
	)
);
?>