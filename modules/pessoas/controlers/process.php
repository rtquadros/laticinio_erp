<?php
require_once('../../global_config.php');

if(isset($_GET['func']) && !empty($_GET['func'])){ 
	//Venda
	if($_GET['func'] == 'insertVenda') $result = $venda->insertVenda($_POST);
	if($_GET['func'] == 'updateVenda') $result = $venda->updateVenda($_POST);
	if($_GET['func'] == 'cancelarVenda') $result = $venda->cancelarVenda($_GET['venda_id']);
	//Compra
	if($_GET['func'] == 'insertCompra') $result = $compra->insertCompra($_POST);
	if($_GET['func'] == 'entregaCompra') $result = $compra->entregaCompra($_GET['compra_id']);
	if($_GET['func'] == 'cancelarCompra') $result = $compra->cancelarCompra($_GET['compra_id']);
	if($_GET['func'] == 'getProduto'){
		$row = $produto->getProduto(array('prod_id'=>$_GET['prod_id']));
		echo json_encode($row[0]);
		exit();
	}
	if($_GET['func'] == 'getPedido'){
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
	if($_GET['func'] == 'insertRelacionamento') $result = $relacionamento->insertRelacionamento($_POST);
	if($_GET['func'] == 'updateRelacionamento') $result = $relacionamento->updateRelacionamento($_POST);
	if($_GET['func'] == 'deleteRelacionamento') $result = $relacionamento->deleteRelacionamento($_GET['rel_id']);
	
	//Define o log do usuário
	$result['mod'] = $_GET['mod'];
	$result['func'] = $_GET['func'];
	$loguser->setLog($result);
	//Transmite o resultado para página original através de $_SESSION
	$_SESSION['result'] = $result;
	// Define o header
	if(!isset($header)) $header = '../../index.php?mod='.$_GET['mod'];
	header('Location:'.$header);
}
?>