<?php
require_once('../../global_config.php');

if(isset($_GET['funcao']) && !empty($_GET['funcao'])){ 
	//Venda
	if($_GET['funcao'] == 'insertVenda') $result = $venda->insertVenda($_POST);
	if($_GET['funcao'] == 'updateVenda') $result = $venda->updateVenda($_POST);
	if($_GET['funcao'] == 'cancelarVenda') $result = $venda->cancelarVenda($_GET['venda_id']);
	//Compra
	if($_GET['funcao'] == 'insertCompra') $result = $compra->insertCompra($_POST);
	if($_GET['funcao'] == 'entregaCompra') $result = $compra->entregaCompra($_GET['compra_id']);
	if($_GET['funcao'] == 'cancelarCompra') $result = $compra->cancelarCompra($_GET['compra_id']);
	if($_GET['funcao'] == 'getProduto'){
		$row = $produto->getProduto(array('prod_id'=>$_GET['prod_id']));
		echo json_encode($row[0]);
		exit();
	}
	if($_GET['funcao'] == 'getPedido'){
		$row = $venda->getVenda(array('venda_id'=>$_GET['venda_id']));
		$pedido = unserialize($row[0]['venda_pedido']);
		foreach($pedido as $iten){
			$i = json_decode($iten);
			if($i->prod_id == $_GET['prod_id']){
				echo $iten;
				exit();	
			}
		}
	}
	//Relacionamento
	if($_GET['funcao'] == 'insertRelacionamento') $result = $relacionamento->insertRelacionamento($_POST);
	if($_GET['funcao'] == 'updateRelacionamento') $result = $relacionamento->updateRelacionamento($_POST);
	if($_GET['funcao'] == 'deleteRelacionamento') $result = $relacionamento->deleteRelacionamento($_GET['rel_id']);
	
	//Define o log do usuário
	$result['mod'] = $_GET['mod'];
	$result['func'] = $_GET['funcao'];
	$loguser->setLog($result);
	//Transmite o resultado para página original através de $_SESSION
	$_SESSION['result'] = $result;
	// Define o header
	if(!isset($header)) $header = '../../index.php?mod='.$_GET['mod'];
	header('Location:'.$header);
}
?>