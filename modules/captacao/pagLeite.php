<?php	
if(isset($_GET['funcao']) && $_GET['funcao'] == 'imprime'){

	$data_ini = $_GET['data_ini'];
	$data_fim = $_GET['data_fim'];
	$total_leite = 0;
	$total_vale = 0;
	$total_compra = 0;
	$total_pagar = 0;
	$preco_medio = 0;
	$rows = $movimentacao->getMovimentacao(array(
		'mov_data'=>$data_fim,
		'mov_categoria'=>$leite->cat_id['Leite']
	));
	
	if($_GET['pag'] == 'geral'){
	?>
	
	  <div class="row">
		  <div class="col-md-4">
			  <h4>
				  <small>LATICÍNIO NOVA VISTA</small><br />
				  FOLHA DE PAGAMENTO GERAL<br />
				  <small>Período de <?php echo $db->brDate($data_ini).' a '.$db->brDate($data_fim);?></small>
			  </h4>
		  </div>
		  <div class="col-md-4 hidden-xs text-right">
			  <h4><small>Custo médio/Lt</small><br />
				  <?php 
				  echo 'R$ '.number_format($leite->getPrecoMedio(array('data_ini'=>$data_ini, 'data_fim'=>$data_fim)), 2, ',', '.'); 
				  ?>
			  </h4>
		  </div>
	  </div>
	  <table class="table table-striped table-bordered">
		  <thead> 
			  <tr> 
				  <th>Produtor</th> 
				  <th>Detalhes</th>
				  <th>Leite total</th>
				  <th class="col-md-1">Preço/Lt</th>
                  <th>Valor parcial</th>
				  <th>Bonificação</th>
				  <th>Vales</th>
				  <th>Compras</th>
				  <th>Valor à pagar</th>
			  </tr> 
		  </thead>  
          <tbody> 
              <?php
              foreach($rows as $row){
                  $prod = $produtor->getProdutor(array('rel_id'=>$row['mov_rel_id']));
              ?>
                  <tr> 
                      <td><?php echo !empty($prod) ? $prod[0]['rel_nome'] : "<span class='text-danger'>Produtor não encontrado <span class='glyphicon glyphicon-alert text-danger'></span></span>"; ?></td>
                      <td><?php echo !empty($prod) ? $prod[0]['rel_desc'] : ''; ?></td>
                      <td>
                      <?php 
                      echo $total = $leite->getTotalLeite(array('leite_prod_id'=>$row['mov_rel_id'], 'leite_data'=>$data_ini, 'leite_data_fim'=>$data_fim));
                      $total_leite += $total;
                      ?>
                      </td>
                      <td>
                      <?php 
                      $prod_var = $leite->getPrecoMedioProdutor(array('leite_prod_id'=>$row['mov_rel_id'], 'leite_data'=>$data_ini, 'leite_data_fim'=>$data_fim));
                      $preco_medio += $prod_var;
                      echo 'R$ '.number_format($prod_var, 2, ',', '.'); 
                      ?>
                      </td>
                      <td><?php echo 'R$ '.number_format($row['mov_valor'], 2, ',', '.'); ?></td>
                      <td></td>
                      <td>
                      <?php
                      $subtotal_vale = $movimentacao->getMovTotal(array(
                          'mov_data_ini'=>date('2000-01-01 00:00:00'),
                          'mov_data_fim'=>$data_fim,
                          'mov_rel_id'=>$row['mov_rel_id'],
                          'mov_pago'=>'false',
                          'mov_tipo'=>1,
                          'mov_categoria'=>array(2,33),
                          'mov_conta_id'=>1
                      ));
                      $total_vale += $subtotal_vale;
                      echo 'R$ '.number_format($subtotal_vale, 2, ',', '.');
                      ?>
                      </td>
                      <td>
                      <?php
                      $subtotal_compra = $movimentacao->getMovTotal(array(
                          'mov_data_ini'=>date('2000-01-01 00:00:00'),
                          'mov_data_fim'=>$data_fim,
                          'mov_rel_id'=>$row['mov_rel_id'],
                          'mov_pago'=>'false',
                          'mov_tipo'=>1,
                          'mov_categoria'=>array(3,34),
                          'mov_conta_id'=>1
                      ));
                      $total_compra += $subtotal_compra;
                      echo 'R$ '.number_format($subtotal_compra, 2, ',', '.');
                      ?>
                      </td>
                      <td>
                      <?php 
                      $subtotal_pagar = $row['mov_valor'] - $subtotal_vale - $subtotal_compra;
                      $total_pagar += $subtotal_pagar;
                      echo 'R$ '.number_format($subtotal_pagar, 2, ',', '.'); 
                      $subtotal_pagar = 0;
                      ?>
                      </td>
                  </tr> 
              <?php }?>
          </tbody>
          <tfoot>
              <tr>
                  <th></th>
                  <th></th> 
                  <th><?php echo $total_leite; ?></th>
                  <th><?php if(count($rows)>0) echo 'R$'.number_format(round($preco_medio/count($rows),2), 2, ',', '.'); ?></th>
                  <th></th>
                  <th></th>
                  <th><?php echo 'R$ '.number_format($total_vale, 2, ',', '.'); ?></th>
                  <th><?php echo 'R$ '.number_format($total_compra, 2, ',', '.'); ?></th>
                  <th ><?php echo 'R$ '.number_format($total_pagar, 2, ',', '.'); ?></th>
              </tr>
          </tfoot>
	  </table>
		
	<?php 
	} elseif($_GET['pag'] == 'detalhado'){
	?>
	<style type="text/css">
		@media print {
			@page {
				size: A4 portrait;
				margin: 1cm;
			}
		}
	</style>
	
	<h4>
		<small>LATICÍNIO NOVA VISTA</small><br />
		FOLHA DE PAGAMENTO DETALHADA<br />
		<small><?php 
			$prod = $produtor->getProdutor(array('rel_id'=>$_GET['prod_id']));
			echo !empty($prod) ? $prod[0]['rel_nome'] : "<span class='text-danger'>Produtor não encontrado <span class='glyphicon glyphicon-alert text-danger'></span></span>";
		?></small><br />
		<small>Período de <?php echo $db->brDate($data_ini).' a '.$db->brDate($data_fim);?></small>
	</h4>
	<table class="table table-striped table-bordered">
		<thead> 
			<tr> 
				<th class="col-md-2">Data</th> 
				<th class="col-md-1">Leite</th>
				<th class="col-md-1">Preço/Lt</th>
				<th class="col-md-1">Bonificação</th>
				<th class="col-md-2">Valor parcial</th>
			</tr> 
		</thead> 
		<tbody> 
			<?php 
			$rows = $leite->getLeite(array(
				'leite_prod_id'=> $_GET['prod_id'],
				'leite_data'=>$data_ini,
				'leite_data_fim'=>$data_fim
			));
			foreach($rows as $row){
			?>
				<tr> 
					<td><?php echo $db->brDate($row['leite_data']); ?></td>
					<td><?php echo $row['leite_quantidade']; $total_leite += $row['leite_quantidade']; ?></td>
					<td><?php echo 'R$ '.number_format($row['leite_preco'], 2, ',', '.'); $preco_medio += $row['leite_preco'];  ?></td>
					<td></td>
					<td>
					<?php 
					$subtotal_pagar = $row['leite_quantidade']*$row['leite_preco'];
					$total_pagar += $subtotal_pagar;
					echo 'R$ '.number_format($subtotal_pagar, 2, ',', '.'); 
					?>
					</td>
				</tr> 
			<?php }?>
		</tbody>
		<tfoot>
			<tr>
				<th class="col-md-2"></th> 
				<th class="col-md-1"><?php echo $total_leite; ?></th>
				<th class="col-md-1"><?php echo 'R$ '.number_format(round($preco_medio/count($rows), 2), 2, ',', '.'); ?></th>
				<th class="col-md-1"></th>
				<th class="col-md-2"><?php echo 'R$ '.number_format($total_pagar, 2, ',', '.'); ?></th>
			</tr>
		</tfoot>
	</table>
	<div class="row">
		<div class="col-md-4 pull-left">
        	<h4>Vales</h4>
        	<table class="table table-condensed">
                <tbody>
					<?php
                    $vales = $movimentacao->getMovimentacao(array(
                        'mov_data_ini'=>date('2000-01-01 00:00:00'),
                        'mov_data_fim'=>$data_fim,
                        'mov_rel_id'=>$_GET['prod_id'],
                        'mov_tipo'=>1,
                        'mov_categoria'=>array(2,33),
                        'mov_pago'=>'false',
						'mov_conta_id'=>1
                    ));
                    $subtotal_vale = 0;
                    foreach($vales as $vale){
                        $subtotal_vale += $vale['mov_valor'];
					?>
                    	<tr>
                        	<td><?php echo $db->brDate($vale['mov_data']); ?></td>
                            <td><?php echo 'R$ '.number_format($vale['mov_valor'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php	
                    }
                    $total_vale += $subtotal_vale;
                    ?>
            	</tbody>
                <tfoot>
                	<tr>
                    	<td>Total em vales</td>
                    	<td><?php echo 'R$ '.number_format($subtotal_vale, 2, ',', '.');?></td>
                    </tr>
                </tfoot>
            </table>
		</div>
		<div class="col-md-4 pull-left">
			<h4>Compras</h4>
            <table class="table table-condensed">
                <tbody>
					<?php
                    $compras = $movimentacao->getMovimentacao(array(
                        'mov_data_ini'=>date('2000-01-01 00:00:00'),
                        'mov_data_fim'=>$data_fim,
                        'mov_rel_id'=>$_GET['prod_id'],
                        'mov_tipo'=>1,
                        'mov_categoria'=>array(3,34),
                        'mov_pago'=>'false',
						'mov_conta_id'=>1
                    ));
                    $subtotal_compra = 0;
                    foreach($compras as $compra){
                        $subtotal_compra += $compra['mov_valor'];	
                    ?>
                    	<tr>
                        	<td><?php echo $db->brDate($compra['mov_data']); ?></td>
                            <td><?php echo 'R$ '.number_format($compra['mov_valor'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php
					}
                    $total_compra += $subtotal_compra;
                    ?>
                </tbody>
                <tfoot>
                	<tr>
                    	<td>Total em compras</td>
                    	<td><?php echo 'R$ '.number_format($subtotal_compra, 2, ',', '.');?></td>
                    </tr>
                </tfoot>
            </table>
		</div>
		<div class="col-md-4 pull-left">
			<h4>Saldo</h4>
            <table class="table table-condensed">
                <tbody>
                    <tr>
                        <td>Leite</td>
                        <td><?php echo 'R$ '.number_format($total_pagar, 2, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Vales</td>
                        <td><?php echo '- R$ '.number_format($subtotal_vale, 2, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Compras</td>
                        <td><?php echo '- R$ '.number_format($subtotal_compra, 2, ',', '.'); ?></td>
                    </tr>
                </tbody>
                <tfoot>
                	<tr>
                    	<td>Total à receber</td>
                    	<td>
							<?php
							$total_pagar = $total_pagar - $subtotal_vale - $subtotal_compra;
							echo 'R$ '.number_format($total_pagar, 2, ',', '.'); 
							?>
                        </td>
                    </tr>
                </tfoot>
            </table>
		</div>
	</div>
	<hr />
		
	<?php 
	} elseif($_GET['pag'] == 'comprovante'){
		$i = 0;
		foreach($rows as $row){
			$prod = $produtor->getProdutor(array('rel_id'=>$row['mov_rel_id']));
			if($i == 6){ 
				echo '<div class="page-break clearfix"></div>';
				$i = 0;
			}
		?>
		
			<div class="col-md-4 pull-left" style="margin-bottom:20px;">
				<h4 class="text-center" style="margin-top:0;">
					<small>LATICÍNIO NOVA VISTA</small><br />
					<u>RECIBO DE PAGAMENTO</u><br />
					<small>Período de <?php echo $db->brDate($data_ini).' a '.$db->brDate($data_fim);?></small>
				</h4>
				<p>
				PRODUTOR: <?php echo !empty($prod) ? $prod[0]['rel_nome'] : "<span class='text-danger'>Produtor não encontrado <span class='glyphicon glyphicon-alert text-danger'></span></span>"; ?> - <?php echo !empty($prod) ? $prod[0]['rel_apelido'] : ''; ?><br />
				CPF/CNPJ: <?php echo !empty($prod) ? $prod[0]['rel_documento'] : ''; ?><br />
				OBS: <?php echo !empty($prod) ? substr($prod[0]['rel_desc'], 0, 40) : ''; ?>
				</p>
				<table class="table table-condensed">
					<tr>
						<th>Leite entregue</th>
						<td class="text-right">
							<?php 
							$total_leite = $leite->getTotalLeite(array('leite_prod_id'=>$row['mov_rel_id'], 'leite_data'=>$data_ini, 'leite_data_fim'=>$data_fim));
							echo $total_leite; 
							?>
                        </td>
					</tr>
					<tr>
						<th>Preço/Lt</th>
						<td class="text-right">
							<?php 
                            $prod_var = $leite->getPrecoMedioProdutor(array('leite_prod_id'=>$row['mov_rel_id'], 'leite_data'=>$data_ini, 'leite_data_fim'=>$data_fim));
                            echo 'R$ '.number_format($prod_var, 2, ',', '.');  
                            ?>
						</td>
					</tr>
                    <tr>
						<th>Valor parcial</th>
						<td class="text-right"><?php echo 'R$ '.number_format($row['mov_valor'], 2, ',', '.');?></td>
					</tr>
					<tr>
						<th>Bonificações</th>
						<td class="text-right"></td>
					</tr>
					<tr>
						<th>Total de vales</th>
						<td class="text-right">
							<?php
							$vales = $movimentacao->getMovimentacao(array(
								'mov_data_ini'=>date('2000-01-01 00:00:00'),
								'mov_data_fim'=>$data_fim,
								'mov_rel_id'=>$row['mov_rel_id'],
								'mov_tipo'=>1,
								'mov_categoria'=>array(2,33),
								'mov_pago'=>'false',
								'mov_conta_id'=>1
							));
							$subtotal_vale = 0;
							foreach($vales as $vale){
								$subtotal_vale += $vale['mov_valor'];	
							}
							$total_vale += $subtotal_vale;
							echo '- R$ '.number_format($subtotal_vale, 2, ',', '.');
							?>
						</td>
					</tr>
					<tr>
						<th>Total em compras</th>
						<td class="text-right">
						<?php
						$compras = $movimentacao->getMovimentacao(array(
							'mov_data_ini'=>date('2000-01-01 00:00:00'),
							'mov_data_fim'=>$data_fim,
							'mov_rel_id'=>$row['mov_rel_id'],
							'mov_tipo'=>1,
							'mov_categoria'=>array(3,34),
							'mov_pago'=>'false',
							'mov_conta_id'=>1
						));
						$subtotal_compra = 0;
						foreach($compras as $compra){
							$subtotal_compra += $compra['mov_valor'];	
						}
						$total_compra += $subtotal_compra;
						echo '- R$ '.number_format($subtotal_compra, 2, ',', '.');
						?>
						</td>
					</tr>
					<tr>
						<th>Total à receber</th>
						<td class="text-right" style="font-size:150%;">
							<?php
							$total_pagar = $row['mov_valor'] - $subtotal_vale - $subtotal_compra;
							echo 'R$ '.number_format($total_pagar, 2, ',', '.'); 
							?>
						</td>
					</tr>
				</table>
				<!--<p class="text-right">Ass:______________________________________</p>-->
			</div> 
		<?php $i++; }?>
	<?php 
	} elseif($_GET['pag'] == 'geral_linhas'){
	?>
	<style type="text/css">
		@media print {
			@page {
				size: A4 portrait;
				margin: 1cm;
			}
		}
	</style>
	
	<h4>
		<small>LATICÍNIO NOVA VISTA</small><br />
		FOLHA DE COMISSÃO DE CARRETO GERAL<br />
		<small>Período de <?php echo $db->brDate($data_ini).' a '.$db->brDate($data_fim);?></small>
	</h4>
	<table class="table table-striped table-bordered">
		<thead> 
			<tr> 
				<th>Linha</th>
				<th>Carreteiro</th>
				<th>Detalhes</th>
				<th class="col-md-1">Leite</th>
				<th class="col-md-1">Comissão/Lt</th>
				<th>Valor à pagar</th>
			</tr> 
		</thead> 
		<tbody> 
			<?php 
			$total_leite = 0;
			$total_pagar = 0;
			$rows = $movimentacao->getMovimentacao(array(
				'mov_data'=>$data_fim,
				'mov_categoria'=>$leite->cat_id['Carreto']
			));
			foreach($rows as $row){
				$linha_id = unserialize($row['mov_variaveis']);
				$linha_id = $linha_id['linha_id'];
				$lin = $linha->getLinha(array(
					'linha_id' => $linha_id
				));
				if($lin[0]['linha_carreteiro'] != 0) $carr = $produtor->getProdutor(array('rel_id'=>$lin[0]['linha_carreteiro']));
				else $carr[0] = array('rel_nome'=>'Próprio produtor', 'rel_desc'=>'');
			?>
				<tr> 
					<td><?php echo $lin[0]['linha_nome']; ?></td>
					<td><?php echo $carr[0]['rel_nome']; ?></td>
					<td><?php echo $carr[0]['rel_desc']; ?></td>
					<td>
					<?php 
					echo $total = $leite->getTotalLeite(array('leite_linha_id'=>$linha_id, 'leite_data'=>$data_ini, 'leite_data_fim'=>$data_fim));
					$total_leite += $total;
					?>
					</td>
					<td><?php echo 'R$ '.number_format($lin[0]['linha_comissao'], 3, ',', '.'); ?></td>
					<td>
					<?php 
					$total_pagar += $row['mov_valor'];
					echo 'R$ '.number_format($row['mov_valor'], 2, ',', '.'); 
					?>
					</td>
				</tr> 
			<?php }?>
		</tbody>
		<tfoot>
			<tr>
				<th></th> 
				<th></th> 
				<th></th>
				<th class="col-md-1"><?php echo $total_leite; ?></th>
				<th class="col-md-1"></th>
				<th><?php echo 'R$ '.number_format($total_pagar, 2, ',', '.'); ?></th>
			</tr>
		</tfoot>
	</table>
	<?php 
	} elseif($_GET['pag'] == 'detalhado_linha'){
	?>
	<h4>
		<small>LATICÍNIO NOVA VISTA</small><br />
		FOLHA DE COMISSÃO DE CARRETO<br />
		<small><?php 
			$lin = $linha->getLinha(array('linha_id'=>$_GET['linha_id']));
			if(!empty($lin)){ 
				echo 'Linha: '.$lin[0]['linha_nome'].' | ';
				//echo 'Carreteiro: '.$lin[0]['linha_carreteiro'].' | ';
				echo 'Comissão/Lt: R$ '.number_format($lin[0]['linha_comissao'], 3, ',', '.');
			} else "<span class='text-danger'>Linha não encontrada <span class='glyphicon glyphicon-alert text-danger'></span></span>";
		?></small><br />
		<small>Período de <?php echo $db->brDate($data_ini).' a '.$db->brDate($data_fim);?></small>
	</h4>
	<table class="table table-striped table-bordered" >
        <thead> 
            <tr> 
                <th class="col-md-2">Produtor</th> 
                <?php 
				$dia_ini = explode('-', $data_ini);
				$dia_fim = explode('-', $data_fim);
                for( $dia = $dia_ini[2]; $dia <= $dia_fim[2]; $dia++){ 
                    echo '<th>'.$dia.'</th>'; 
                }?>
                <th class="col-md-1">Total</th> 
            </tr> 
        </thead> 
        <tbody> 
            <?php
            // Busca todos os registros de coleta e ordena em uma nova array com a ID do produtor como key
            $coleta_arr = array();
            $coletas = $leite->getLeite(array(
                'leite_data'=>$data_ini,
                'leite_data_fim'=>$data_fim,
				'leite_linha_id'=>$_GET['linha_id']
            ));
            foreach($coletas as $coleta){
                if(!empty($coleta_arr[$coleta['leite_prod_id']])) array_push($coleta_arr[$coleta['leite_prod_id']], $coleta);
                else $coleta_arr[$coleta['leite_prod_id']] = array($coleta);
            }
            $total_dia = array(0=>'');
            
            foreach($coleta_arr as $key=>$col){
            ?>
                <tr> 
                    <td>
                    <?php 
					if($prod_nome = $produtor->getNomeProdutor($key))
                       echo $prod_nome;
					else 
						echo "<span class='text-danger'>Produtor não encontrado <span class='glyphicon glyphicon-alert text-danger'></span></span>"; ?>
                    </td>
                    <?php
                    $quant = '';
                    $total_produtor = 0;
                    for( $dia = $dia_ini[2]; $dia <= $dia_fim[2]; $dia++){  
                        foreach($col as $coleta){
                            if ( date('d', strtotime($coleta['leite_data'])) == $dia){
                                $leite_id = $coleta['leite_id'];
                                $quant = $coleta['leite_quantidade'];
                                $total_produtor += $quant;	
                            }
                        }
                        // Define o total do dia
                        if(isset($total_dia[$dia])) $total_dia[$dia] += $quant;
                        else $total_dia[$dia] = $quant;  
                    ?>
                        <td>
						<?php 
							if(isset($quant) && !empty($quant) || $quant == '0') echo $quant; 
							else echo '--'; 
						?>
                        </td>
                    <?php 
                        $quant = '';
                    }?>
                    <td><?php echo $total_produtor;?></td>
                </tr> 
            <?php }?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total leite</td>
                <?php for( $dia = $dia_ini[2]; $dia <= $dia_fim[2]; $dia++){  ?>
                    <td><?php echo isset($total_dia[$dia]) ? $total_dia[$dia] : 0; ?></td>	
                <?php }?>
                <td><?php echo array_sum($total_dia);?></td>
            </tr>
            <tr>
                <td>Total comissão</td>
                <?php for( $dia = $dia_ini[2]; $dia <= $dia_fim[2]; $dia++){  ?>
                    <td><?php echo isset($total_dia[$dia]) && $total_dia[$dia] != 0 ? 'R$ '.number_format($total_dia[$dia] * $lin[0]['linha_comissao'], 2, ',', '.') : 0; ?></td>	
                <?php }?>
                <td><?php echo 'R$ '.number_format(array_sum($total_dia) * $lin[0]['linha_comissao'], 2, ',', '.');?></td>
            </tr>
        </tfoot>
    </table>
	<?php }?>
<?php	
} else{
?>  
    <div class="form-row">
      <div class="col ml-auto">
        <a class="btn btn-info" href="#">Fechar folha de pagamento</a>
      </div>
      <div class="col-3 mb-2 ml-auto">
        <form method="post" action="">
            <div class="input-group">
                <input type="text" class="form-control  monthpicker" name="mes_ref" id="mes_ref" value="<?php echo $mes_ref; ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn  btn-secondary"><span class="fas fa-search"></span> Buscar</button>
                </div>
            </div>
        </form>
      </div>
    </div>
    
    <div>
      <!-- Nav tabs -->
      <ul class="nav nav-tabs mb-2" id="myTab" role="tablist">
        <?php
        for($i=1; $i<=2; $i++){
          echo "<li class='nav-item'><a class='nav-link";
          if(date("j") > $data_arr[$i][0]->format("j") && date("j") < $data_arr[$i][1]->format("j")) echo " active";
          echo "'  id='quinzena{$i}-tab' data-toggle='tab' href='#quinzena{$i}' role='tab' aria-controls='{$i}' aria-selected='true'>{$i}º Quinzena</a></li>";
        }
        ?>
      </ul>
      
      <!-- Tab panes -->
      <div class="tab-content" id="myTabContent">
        <?php 
        for($i=1; $i<=2; $i++){ 
        ?>
          <div role="tabpanel" class="tab-pane fade <?php echo date("j") > $data_arr[$i][0]->format("j") && date("j") < $data_arr[$i][1]->format("j") ? 'show active' : ''; ?>" id="<?php echo "quinzena".$i;?>" aria-labelledby="<?php echo "quinzena".$i;?>">
            <div class="row">
              <div class="col"><h3>Pagamento de produtores</h3></div>
              <div class="col hidden-xs text-right">
                <h4><small>Custo médio/Lt</small><br />
                <?php 
                echo 'R$ '.$leite->getPrecoMedio($data_arr[$i][0]->format("Y-m-d"), $data_arr[$i][1]->format("Y-m-d")); 
                ?>
                </h4>
              </div>
            </div>
            <table class="table table-striped table-bordered table-sm datatable" id="<?php echo "table-quinzena".$i;?>">
        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane <?php echo date('d-m-y') <= $data_fim_1quinzena ? 'active' : ''; ?>" id="1-quinzena">
            	<?php
				$total_leite = 0;
				$total_vale = 0;
				$total_compra = 0;
				$total_pagar = 0;
				$preco_medio = 0;
				?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed dynatable" id="1_quinzena">
                        <thead> 
                            <tr> 
                                <th>Produtor</th> 
                                <th class="col-md-3">Detalhes</th>
                                <th class="col-md-1">Leite</th>
                                <th>Preço/Lt</th>
                                <th>Valor parcial</th>
                                <th>Bonificação</th>
                                <th>Vales</th>
                                <th>Compras</th>
                                <th>Valor à pagar</th>
                                <th></th>
                            </tr> 
                        </thead> 
                        <tbody> 
                            <?php 
                            $rows = $movimentacao->getMovimentacao(array(
                                'mov_data'=>$data_fim_1quinzena,
                                'mov_categoria'=>31
                            ));
                            foreach($rows as $row){
                                $prod = $produtor->getProdutor(array('rel_id'=>$row['mov_rel_id']));
                            ?>
                                <tr> 
                                    <td><?php echo !empty($prod) ? $prod[0]['rel_nome'] : "<span class='text-danger'>Produtor não encontrado <span class='glyphicon glyphicon-alert text-danger'></span></span>"; ?></td>
                                    <td><?php echo !empty($prod) ? $prod[0]['rel_desc'] : ''; ?></td>
                                    <td>
                                    <?php 
                                    echo $total = $leite->getTotalLeite(array('leite_prod_id'=>$row['mov_rel_id'], 'leite_data'=>$data_ini_mes, 'leite_data_fim'=>$data_fim_1quinzena));
                                    $total_leite += $total;
                                    ?>
                                    </td>
                                    <td>
                                    <?php 
									$prod_var = $leite->getPrecoMedioProdutor(array('leite_prod_id'=>$row['mov_rel_id'], 'leite_data'=>$data_ini_mes, 'leite_data_fim'=>$data_fim_1quinzena));
                                    $preco_medio += $prod_var;
                                    echo 'R$ '.number_format($prod_var, 2, ',', '.'); 
                                    ?>
                                    </td>
                                    <td><?php echo 'R$ '.number_format($row['mov_valor'], 2, ',', '.'); ?></td>
                                    <td></td>
                                    <td>
                                    <?php
                                    $subtotal_vale = $movimentacao->getMovTotal(array(
										'mov_data_ini'=>date('2000-01-01 00:00:00'),
                                        'mov_data_fim'=>$data_fim_1quinzena,
                                        'mov_rel_id'=>$row['mov_rel_id'],
										'mov_pago'=>'false',
                                        'mov_tipo'=>1,
                                        'mov_categoria'=>array(2,33),
										'mov_conta_id'=>1
                                    ));
                                    $total_vale += $subtotal_vale;
                                    echo 'R$ '.number_format($subtotal_vale, 2, ',', '.');
                                    ?>
                                    </td>
                                    <td>
                                    <?php
                                    $subtotal_compra = $movimentacao->getMovTotal(array(
										'mov_data_ini'=>date('2000-01-01 00:00:00'),
                                        'mov_data_fim'=>$data_fim_1quinzena,
                                        'mov_rel_id'=>$row['mov_rel_id'],
										'mov_pago'=>'false',
                                        'mov_tipo'=>1,
                                        'mov_categoria'=>array(3,34),
										'mov_conta_id'=>1
                                    ));
                                    $total_compra += $subtotal_compra;
                                    echo 'R$ '.number_format($subtotal_compra, 2, ',', '.');
                                    ?>
                                    </td>
                                    <td>
                                    <?php 
                                    $subtotal_pagar = $row['mov_valor'] - $subtotal_vale - $subtotal_compra;
                                    $total_pagar += $subtotal_pagar;
                                    echo 'R$ '.number_format($subtotal_pagar, 2, ',', '.'); 
									$subtotal_pagar = 0;
                                    ?>
                                    </td>
                                    <td><a class="btn btn-xs btnPrint" href="print.php?mod=captacao_pagLeite&funcao=imprime&pag=detalhado&data_ini=<?php echo $db->mysqlDate($data_ini_mes);?>&data_fim=<?php echo $db->mysqlDate($data_fim_1quinzena);?>&prod_id=<?php echo $row['mov_rel_id'];?>" role="button" title="Imprimir detalhamento" target="_blank"><span class="glyphicon glyphicon-print"></span></a></td>
                                </tr> 
                            <?php }?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th> 
                                <th><?php echo $total_leite; ?></th>
                                <th><?php if(count($rows)>0) echo 'R$'.number_format(round($preco_medio/count($rows),2), 2, ',', '.'); ?></th>
                                <th></th>
                                <th></th>
                                <th><?php echo 'R$ '.number_format($total_vale, 2, ',', '.'); ?></th>
                                <th><?php echo 'R$ '.number_format($total_compra, 2, ',', '.'); ?></th>
                                <th ><?php echo 'R$ '.number_format($total_pagar, 2, ',', '.'); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="row">
                	<div class="col-md-6">
                        <h3>Comissão de linhas de coleta</h3>
                    </div>
                    <div class="col-md-6 text-right">
                    	<p>
                            <a class="btn  btn-default btnPrint" href="print.php?mod=captacao_pagLeite&funcao=imprime&pag=geral_linhas&data_ini=<?php echo $db->mysqlDate($data_ini_mes);?>&data_fim=<?php echo $db->mysqlDate($data_fim_1quinzena);?>" role="button" target="_blank"><span class="glyphicon glyphicon-print"></span>  Folha geral</a>
                        </p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed" >
                        <thead> 
                            <tr> 
                                <th>Linha</th>
                                <th>Carreteiro</th>
                                <th>Detalhes</th>
                                <th class="col-md-1">Leite</th>
                                <th class="col-md-1">Comissão/Lt</th>
                                <th>Valor à pagar</th>
                                <th></th>
                            </tr> 
                        </thead> 
                        <tbody> 
                            <?php 
                            $total_leite = 0;
                            $total_pagar = 0;
                            $rows = $movimentacao->getMovimentacao(array(
                                'mov_data'=>$data_fim_1quinzena,
                                'mov_categoria'=>$leite->cat_id['Carreto']
                            ));
                            foreach($rows as $row){
                                $linha_id = unserialize($row['mov_variaveis']);
                                $linha_id = $linha_id['linha_id'];
                                $lin = $linha->getLinha(array(
                                    'linha_id' => $linha_id
                                ));
                                if($lin[0]['linha_carreteiro'] != 0) $carr = $produtor->getProdutor(array('rel_id'=>$lin[0]['linha_carreteiro']));
                                else $carr[0] = array('rel_nome'=>'Próprio produtor', 'rel_desc'=>'');
                            ?>
                                <tr> 
                                    <td><?php echo $lin[0]['linha_nome']; ?></td>
                                    <td><?php echo $carr[0]['rel_nome']; ?></td>
                                    <td><?php echo $carr[0]['rel_desc']; ?></td>
                                    <td>
                                    <?php 
                                    echo $total = $leite->getTotalLeite(array('leite_linha_id'=>$linha_id, 'leite_data'=>$data_ini_mes, 'leite_data_fim'=>$data_fim_1quinzena));
                                    $total_leite += $total;
                                    ?>
                                    </td>
                                    <td><?php echo 'R$ '.number_format($lin[0]['linha_comissao'], 3, ',', '.'); ?></td>
                                    <td>
                                    <?php 
                                    $total_pagar += $row['mov_valor'];
                                    echo 'R$ '.number_format($row['mov_valor'], 2, ',', '.'); 
                                    ?>
                                    </td>
                                    <td><a class="btn btn-xs btnPrint" href="print.php?mod=captacao_pagLeite&funcao=imprime&pag=detalhado_linha&data_ini=<?php echo $db->mysqlDate($data_ini_mes);?>&data_fim=<?php echo $db->mysqlDate($data_fim_1quinzena);?>&linha_id=<?php echo $linha_id;?>" role="button" title="Imprimir detalhamento" target="_blank"><span class="glyphicon glyphicon-print"></span></a></td>
                                </tr> 
                            <?php }?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th> 
                                <th></th> 
                                <th></th>
                                <th class="col-md-1"><?php echo $total_leite; ?></th>
                                <th class="col-md-1"></th>
                                <th><?php echo 'R$ '.number_format($total_pagar, 2, ',', '.'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
	</div>
<?php
}
?>