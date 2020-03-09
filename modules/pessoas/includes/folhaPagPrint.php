<?php
$total_pagar = 0;
$total_semanal_pagar = 0;
$subtotal_pagar = 0;
$i = 0;
$r = 0;
$semana_ref = new DateTime(date('Y-m-d'));
$semana_ref = $semana_ref->format("W");

$pessoa = new Pessoa();
$rows = $pessoa->selectPessoa("*", "WHERE pessoa_categoria=?", array("funcionario"));
foreach($rows as $row){
	$var = unserialize($row['pessoa_variaveis']);
	echo $i == 0 ? "<div class='row'>" : "";
?>
	<style type="text/css">
		@media print {
			@page {
				size: A4 landscape;
				margin: 1cm;
			}
			body{
				font-size: .9em;
			}
			h4{
				font-size: 1.2em
			}
		}
	</style>

	<div class="col-md-4 pull-left">
		<h4 class="text-center" style="margin-top:0;">
			<small>LATICÍNIO NOVA VISTA</small><br />
			<u>RECIBO DE ADIANTAMENTO SEMANAL</u><br />
			<small>DATA: <?php echo date('d/m/Y');?> | Semana: <?php echo $semana_ref;?></small>
		</h4>
		<p>
		FUNC.: <?php echo $row['pessoa_nome']; ?><br />
		CPF: <?php echo $row['pessoa_documento']; ?><br />
		OBS: <?php echo $row['pessoa_desc']; ?>
		</p>
		<table class="table table-sm">
			<tr>
				<th>Salário base</th>
				<td class="text-right">
				<?php 
				$subtotal_pagar += $var['salario'];
				echo 'R$ '.number_format($var['salario'], 2, ',', '.');
				?>
				</td>
			</tr>
			<tr>
				<th>Bonificações</th>
				<td class="text-right">
				<?php 
				if(empty($var['bonificacao'])) $var['bonificacao'] = 0;
				$subtotal_pagar += $var['bonificacao'];
				echo 'R$ '.number_format($var['bonificacao'], 2, ',', '.');
				?>
				</td>
			</tr>
			<tr>
				<th>Subtotal</th>
				<td class="text-right">
				<?php 
				echo 'R$ '.number_format($subtotal_pagar, 2, ',', '.');
				?>
				</td>
			</tr>
			<tr>
				<th>Total à receber</th>
				<td class="text-right" style="font-size:150%;">
				<?php 
				$subtotal_semanal_pagar = ($subtotal_pagar*12)/52;
				$total_semanal_pagar += $subtotal_semanal_pagar;
				echo 'R$ '.number_format($subtotal_semanal_pagar, 2, ',', '.');
				$subtotal_pagar = 0;
				$subtotal_semanal_pagar = 0;
				?>
				</td>
			</tr>
		</table>
		<p class="text-right">_________________________________________<br /> Assinatura</p>
	</div> 
<?php 
  $i++; 
  if($i == 3){
    echo "</div>";
    $i = 0;
    $r++;
  }
  if($r == 2) {
  	echo "<div class='page-break clearfix'></div>";
    $r = 0;
  }
}
?>