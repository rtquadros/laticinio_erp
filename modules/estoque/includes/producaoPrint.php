<?php
$producao_id = filter_input(INPUT_GET, "producao_id", FILTER_SANITIZE_NUMBER_INT);
$objProducao = new Producao();

if(isset($_GET["modelo"]) && $_GET["modelo"] == 'ordem'){
	$producao = $objProducao->selectProducao("*", "WHERE producao_id=?", array($producao_id));
	if($producao){
		$objPessoa = new Pessoa();
		$objEstoque = new Estoque();
		$objProduto = new Produto();
		$produto = $objProduto->selectProduto("*", "WHERE prod_id=?", array($producao[0]["producao_prod_id"]));
?>
		<style type="text/css">
			@media print {
				@page {
					size: A4 landscape;
					margin: 0.8cm;
				}
			}
		</style>

		<div class="row">
		  <div class="col-5">
		    <h4>
		    <small>LATICÍNIO NOVA VISTA</small><br />
		    ORDEM DE PRODUÇÃO #<?php echo $objProducao->getOrdemProducao($producao[0]["producao_id"]);?><br />
		  	</h4>
			</div>
			<div class="col align-self-end pb-2">
				<b>Data da ordem:</b><br /> <?php echo date("d/m/Y | H:i", strtotime($producao[0]["producao_data_ordem"]));?>
			</div>
			<div class="col align-self-end pb-2">
				<b>Responsável:</b><br /> <?php echo $objPessoa->getPessoaNome($producao[0]["producao_func_id"]); ?>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<h5>Detalhes do produto</h5>
				<table class="table table-sm">
					<thead>
						<tr>
							<th>Produto</th>
							<th>Cod</th>
							<th>Marca</th>
							<th>Validade</th>
							<th>Unidade</th>
							<th>Quantidade esperada</th>
							<th>Quantidade produzida</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $produto ? $produto[0]["prod_nome"] : "Produto não encontrado." ;?></td>
							<td><?php echo $produto ? $produto[0]["prod_codbarras"] : "" ;?></td>
							<td><?php echo $produto ? $produto[0]["prod_marca"] : "" ;?></td>
							<td><?php echo $produto ? $produto[0]["prod_validade"]." dias" : "" ;?></td>
							<td><?php echo $produto ? $produto[0]["prod_unidade"] : "" ;?></td>
							<td><?php echo $producao[0]["producao_quant"] ;?></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<h5>Insumos</h5>
				<table class="table table-sm table-bordered">
					<thead>
            <tr>
              <th>#</th>
              <th>Insumo</th>
              <th>Unidade</th>
              <th title="Quantidade exigida">Qt. exigida</th>
            </tr>
          </thead>
					<tbody>
					<?php 
					$producao_insumos = unserialize($producao[0]["producao_insumos"]);
					foreach($producao_insumos as $key=>$insumo){
					?>
						<tr>
							<td><?php echo $insumo["insumo_id"];?></td>
							<td><?php echo $objProduto->getNomeProduto($insumo["insumo_id"]);?></td>
							<td><?php echo $objProduto->getUnidadeProduto($insumo["insumo_id"]);?></td>
							<td><?php echo $insumo["insumo_quant"];?></td>
						</tr>
						<tr>
							<td colspan="4" class="pl-5">
								<table class="table table-sm table-borderless mb-0" style="border-left:3px solid #000;">
									<thead>
										<tr class="bg-white">
											<th class="pl-4">Lote</th> 
											<th>Entrada</th> 
											<th>Validade</th>
											<th>Empenhado</th>
										</tr> 
									</thead>
									<tbody>
									<?php 
									foreach($insumo["insumo_empenhos"] as $key=>$empenho){ 
										$estoque = $objEstoque->selectEstoque("*", "WHERE estoque_id=?", array($empenho["estoque_id"]));
									?>
										<tr class="bg-white"> 
											<td class="pl-4 pb-0"><?php echo $estoque[0]["estoque_lote"];?></td>
											<td class="pb-0"><?php echo date("d/m/Y", strtotime($estoque[0]["estoque_data_entrada"]));?></td> 
											<td class="pb-0"><?php echo date("d/m/Y", strtotime($estoque[0]["estoque_validade"]));?></td> 
											<td class="pb-0"><?php echo $empenho["estoque_quant_empenhada"];?></td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<h5>Processos</h5>
				<table class="table table-sm">
					<thead>
            <tr>
              <th>#</th>
              <th>Descrição</th>
              <th>Equipamento</th>
              <th>Duração</th>
              <th>Limite</th>
            </tr>
          </thead>
					<tbody>
					<?php 
					$producao_processos = unserialize($producao[0]["producao_processos"]);
					foreach($producao_processos as $key=>$processo){
					?>
						<tr>
							<td><?php echo $key;?></td>
							<td><?php echo $processo["processo_nome"];?></td>
							<td><?php echo $processo["processo_equip"];?></td>
							<td><?php echo $processo["processo_duracao"];?></td>
							<td><?php echo $processo["processo_limite"];?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
<?php
	}
}
?>