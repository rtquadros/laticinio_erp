<div class="row">
  <div class="col">
    <h3>Folha de pagamento</h3>
  </div>
  <div class="col text-right">
    <p><a class="btn  btn-secondary btnPrint" href="print.php?mod=pessoas&pag=folhaPag&func=imprimir" role="button"><span class="fas fa-print"></span>  Comprovantes da semana</a></p>
  </div>
</div>

<div class="table-responsive"> 
  <table class="table table-striped table-bordered table-sm datatable" id="folhaPag">
    <thead> 
      <tr> 
        <th>Funcionário</th> 
        <th>Salário</th>
        <th>Bonificação</th>
        <th>Valor total</th>
        <th>Valor semanal</th>
        <th>Observações</th>
      </tr> 
    </thead> 
    <tbody> 
        <?php 
		$total_pagar = 0;
		$total_semana_pagar = 0;
		$subtotal_pagar = 0;
		
		$pessoa = new Pessoa();
        $rows = $pessoa->selectPessoa("*", "WHERE pessoa_categoria=?", array("funcionario"));
        foreach($rows as $row){
          $var = unserialize($row['pessoa_variaveis']);
        ?>
        <tr> 
            <td><?php echo $row['pessoa_nome']; ?></td>
            <td>
            <?php 
            $subtotal_pagar += $var['salario'];
            echo 'R$ '.number_format($var['salario'], 2, ',', '.');
            ?>
            </td>
            <td>
            <?php 
			if(empty($var['bonificacao'])) $var['bonificacao'] = 0;
            $subtotal_pagar += $var['bonificacao'];
            echo 'R$ '.number_format($var['bonificacao'], 2, ',', '.');
            ?>
            </td>
            <td>
            <?php 
            $total_pagar += $subtotal_pagar;
            echo 'R$ '.number_format($subtotal_pagar, 2, ',', '.');
            ?>
            </td>
            <td>
            <?php 
            $subtotal_semana_pagar = ($subtotal_pagar*12)/52;
			$total_semana_pagar += $subtotal_semana_pagar;
            echo 'R$ '.number_format($subtotal_semana_pagar, 2, ',', '.');
			$subtotal_pagar = 0;
			$subtotal_semana_pagar = 0;
            ?>
            </td>
            <td><?php echo $row['pessoa_desc']; ?></td>
        </tr> 
        <?php }?>
    </tbody>
    <tfoot>
        <tr>
        	<th></th>
            <th></th>
            <th></th>
            <th><?php echo 'R$ '.number_format($total_pagar, 2, ',', '.'); ?></th>
            <th><?php echo 'R$ '.number_format($total_semana_pagar, 2, ',', '.'); ?></th>
            <th></th>
        </tr>
    </tfoot>
  </table>
</div>