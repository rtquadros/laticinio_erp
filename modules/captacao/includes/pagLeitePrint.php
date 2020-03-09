<?php
$data_ini = filter_input(INPUT_GET, "data_ini", FILTER_CALLBACK, array("options"=>array("FilterDb", "sanitizeDate")));
$data_fim = filter_input(INPUT_GET, "data_fim", FILTER_CALLBACK, array("options"=>array("FilterDb", "sanitizeDate")));
$prod_id = filter_input(INPUT_GET, "prod_id", FILTER_SANITIZE_NUMBER_INT);
$linha_id = filter_input(INPUT_GET, "linha_id", FILTER_SANITIZE_NUMBER_INT);
$filter = new FilterDb();

if(isset($_GET["modelo"]) && $_GET["modelo"] == 'geral'){
?>
  <div class="row">
    <div class="col">
      <h4>
	    <small>LATICÍNIO NOVA VISTA</small><br />
	    FOLHA DE PAGAMENTO GERAL<br />
	    <small>Período de <?php echo $filter->brDate($data_ini).' a '.$filter->brDate($data_fim);?></small>
	  </h4>
	</div>
  </div>
  <div class="table-responsive"> 
	<table class="table table-striped table-bordered table-sm">
	  <thead> 
		  <tr> 
		    <th>Produtor</th> 
		    <th>Detalhes</th>
		    <th>Leite</th>
		    <th>Preço/Lt</th>
		    <th>Valor parcial</th>
		    <th>Bonificação</th>
		    <th>Vales</th>
		    <th>Compras</th>
		    <th>Valor à pagar</th>
		  </tr> 
		</thead>
		<tbody>
		  <?php
		  $produtor = new Produtor();
		  $leite = new Leite();
		  $prod_ids = $leite->selectLeite("DISTINCT leite_prod_id", "WHERE leite_data BETWEEN ? AND ?", array($data_ini, $data_fim));
		  foreach ($prod_ids as $key => $prod_id) {
		    $prod = $produtor->selectProdutor("*", "WHERE pessoa_id=?", array($prod_id["leite_prod_id"]));
		    $retorno = $leite->selectLeite("*", "WHERE leite_prod_id=? AND leite_data BETWEEN ? AND ?", array($prod_id["leite_prod_id"], $data_ini, $data_fim));
		    $leite_total_produtor = 0;
		    $valor_total_produtor = 0;
		    foreach ($retorno as $key => $value) {
		    	$leite_total_produtor += $value["leite_quantidade"];
		    	$valor_total_produtor += $value["leite_quantidade"] * $value["leite_preco"];
		    }
		  ?>
		    <tr>
		      <td><?php echo $prod ? $prod[0]["pessoa_nome"] : "Produtor não encontrado (ID: {$prod_id["leite_prod_id"]})";?></td>
		      <td><?php echo $prod ? $prod[0]["pessoa_desc"] : "";?></td>
		      <td><?php echo $leite_total_produtor;?></td>
		      <td><?php echo number_format($valor_total_produtor/$leite_total_produtor, 2, ",", ".");?></td>
		      <td><?php echo number_format($valor_total_produtor, 2, ",", ".");?></td>
		      <td></td>
		      <td></td>
		      <td></td>
		      <td></td>
		    </tr>
		  <?php }?>
		</tbody>
	</table>
  </div>
<?php } elseif(isset($_GET["modelo"]) && $_GET["modelo"] == 'comprovantes'){
  $produtor = new Produtor();
  $leite = new Leite();
  $prod_ids = $leite->selectLeite("DISTINCT leite_prod_id", "WHERE leite_data BETWEEN ? AND ?", array($data_ini, $data_fim));
  foreach ($prod_ids as $key => $prod_id) {
    $prod = $produtor->selectProdutor("*", "WHERE pessoa_id=?", array($prod_id["leite_prod_id"]));
?>
	  <div class="col-4 pull-left mb-4">
		<h4 class="text-center">
			<small>LATICÍNIO NOVA VISTA</small><br />
			<u>RECIBO DE PAGAMENTO</u><br />
			<small>Período de <?php echo $filter->brDate($data_ini).' a '.$filter->brDate($data_fim);?></small>
		</h4>
		<p>
		PRODUTOR: <?php echo $prod ? $prod[0]["pessoa_nome"] : "Produtor não encontrado (ID: {$prod_id["leite_prod_id"]})";?> - <?php echo $prod ? $prod[0]['pessoa_apelido'] : ''; ?><br />
		CPF/CNPJ: <?php echo $prod ? $prod[0]['pessoa_documento'] : ''; ?><br />
		OBS: <?php echo $prod ? substr($prod[0]['pessoa_desc'], 0, 40) : ''; ?>
		</p>
		<table class="table table-sm">
			<tr>
				<th>Leite entregue</th>
				<td class="text-right"><?php echo $leite->getTotalLeiteProdutor($prod_id["leite_prod_id"], $data_ini, $data_fim);?></td>
			</tr>
			<tr>
				<th>Preço Médio/Lt</th>
				<td class="text-right"><?php echo 'R$ '.number_format($leite->getPrecoLeiteProdutor($prod_id["leite_prod_id"], $data_ini, $data_fim), 2, ',', '.');?></td>
			</tr>
	        <tr>
				<th>Valor parcial</th>
				<td class="text-right"><?php echo 'R$ '.number_format($leite->getValorLeiteProdutor($prod_id["leite_prod_id"], $data_ini, $data_fim), 2, ',', '.');?></td>
			</tr>
			<tr>
				<th>Bonificações</th>
				<td class="text-right"></td>
			</tr>
			<tr>
				<th>Total de vales</th>
				<td class="text-right">
				</td>
			</tr>
			<tr>
				<th>Total em compras</th>
				<td class="text-right">
				</td>
			</tr>
			<tr>
				<th>Total à receber</th>
				<td class="text-right">
				</td>
			</tr>
		</table>
		<!--<p class="text-right">Ass:______________________________________</p>-->
	</div>
<?php }
} elseif(isset($_GET["modelo"]) && $_GET["modelo"] == 'detalhado'){ ?>
  <div class="row">
    <div class="col">
      <h4>
	    <small>LATICÍNIO NOVA VISTA</small><br />
	    FOLHA DE PAGAMENTO DETALHADA<br />
	    <small>
	    <?php
        $produtor = new Produtor();
        $produtor_nome = $produtor->getProdutorNome($prod_id);
        echo $produtor_nome ? $produtor_nome : "Produtor não encontrado (ID: {$prod_id})";
	    ?>
	    </small><br />
	    <small>Período de <?php echo $filter->brDate($data_ini).' a '.$filter->brDate($data_fim);?></small>
	  </h4>
	</div>
  </div>
  <div class="table-responsive">
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
			$leite = new Leite();
			$retorno = $leite->selectLeite("*", "WHERE leite_prod_id=? AND leite_data BETWEEN ? AND ?", array($prod_id, $data_ini, $data_fim));
			$total_pagar = 0;
			foreach($retorno as $row){
			?>
				<tr> 
					<td><?php echo $filter->brDate($row['leite_data']); ?></td>
					<td><?php echo $row['leite_quantidade']; ?></td>
					<td><?php echo 'R$ '.number_format($row['leite_preco'], 2, ',', '.');  ?></td>
					<td></td>
					<td><?php echo 'R$ '.number_format($row['leite_quantidade']*$row['leite_preco'], 2, ',', '.'); ?></td>
				</tr> 
			<?php }?>
		</tbody>
		<tfoot>
			<tr>
				<th class="col-md-2"></th> 
				<th class="col-md-1"><?php echo $leite->getTotalLeiteProdutor($prod_id, $data_ini, $data_fim); ?></th>
				<th class="col-md-1"><?php echo 'R$ '.number_format($leite->getPrecoLeiteProdutor($prod_id, $data_ini, $data_fim), 2, ',', '.'); ?></th>
				<th class="col-md-1"></th>
				<th class="col-md-2"><?php echo 'R$ '.number_format($leite->getValorLeiteProdutor($prod_id, $data_ini, $data_fim), 2, ',', '.'); ?></th>
			</tr>
		</tfoot>
	</table>
  </div>

<?php } elseif(isset($_GET["modelo"]) && $_GET["modelo"] == 'detalhado_linha'){
?>
  <h4>
	<small>LATICÍNIO NOVA VISTA</small><br />
	FOLHA DE COMISSÃO DE CARRETO<br />
	<small><?php 
	    $linha = new Linha();
		$lin = $linha->selectLinha("*", "WHERE linha_id=?", array($linha_id));
		if($lin){ 
			echo 'Linha: '.$lin[0]['linha_nome'].' | ';
			//echo 'Carreteiro: '.$lin[0]['linha_carreteiro'].' | ';
			echo 'Comissão/Lt: R$ '.number_format($lin[0]['linha_comissao'], 3, ',', '.');
		} else "<span class='text-danger'>Linha não encontrada <span class='fas fa-exclamation-triangle text-danger'></span></span>";
	?></small><br />
	<small>Período de <?php echo $filter->brDate($data_ini).' a '.$filter->brDate($data_fim);?></small>
  </h4>
  <div class="table-responsive">
    <table class="table table-striped table-bordered" >
        <thead> 
            <tr> 
                <th class="col-md-2">Produtor</th>
                <?php 
                $data_ini = new DateTime($data_ini);
                $data_fim = new DateTime($data_fim);
                $diff = $data_fim->diff($data_ini)->format("%a") + $data_ini->format("j");
                for( $dia = $data_ini->format("j"); $dia <= $diff; $dia++){ 
                    echo "<th>{$dia}</th>"; 
                }?> 
                <th class="col-md-1">Total</th> 
            </tr> 
        </thead> 
        <tbody>
          <?php
          $total_dia = array();
          $produtor = new Produtor();
          $leite = new Leite();
          $prod_ids = $leite->selectLeite("DISTINCT leite_prod_id", "WHERE leite_linha_id=? AND leite_data BETWEEN ? AND ?", array($linha_id, $data_ini->format("Y-m-d"), $data_fim->format("Y-m-d")));
          foreach ($prod_ids as $key => $prod_id) {
            $total_produtor = 0;
            $produtor_nome = $produtor->getProdutorNome($prod_id["leite_prod_id"]);
            echo "<tr>";
            echo "<td>{$produtor_nome}</td>";
            for( $dia = $data_ini->format("j"); $dia <= $diff; $dia++){
              $data = $data_ini->format("Y-m-").$dia;
              $retorno = $leite->selectLeite("*", "WHERE leite_prod_id=? AND leite_linha_id=? AND leite_data=?", array($prod_id["leite_prod_id"], $linha_id, $data));
              if($retorno && $retorno[0]["leite_quantidade"] !== 0) {
                echo "<td><a href='?mod=captacao&pag=leite&func=editar&leite_id={$retorno[0]["leite_id"]}'>{$retorno[0]["leite_quantidade"]}</a></td>";
                $total_produtor += $retorno[0]["leite_quantidade"];
                if(isset($total_dia[$dia]) && is_numeric($total_dia[$dia])) $total_dia[$dia] += $retorno[0]["leite_quantidade"];
                else $total_dia[$dia] = $retorno[0]["leite_quantidade"];
              } else
                echo "<td>--</td>";
            }
            echo "<td>{$total_produtor}</td>";
            echo "</tr>";
          }
          ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total leite</td>
                <?php for( $dia = $data_ini->format("j"); $dia <= $diff; $dia++){  ?>
                    <td><?php echo isset($total_dia[$dia]) ? $total_dia[$dia] : 0; ?></td>	
                <?php }?>
                <td><?php echo array_sum($total_dia);?></td>
            </tr>
            <tr>
                <td>Total comissão</td>
                <?php for( $dia = $data_ini->format("j"); $dia <= $diff; $dia++){  ?>
                    <td><?php echo isset($total_dia[$dia]) && $total_dia[$dia] != 0 ? 'R$ '.number_format($total_dia[$dia] * $lin[0]['linha_comissao'], 2, ',', '.') : 0; ?></td>	
                <?php }?>
                <td><?php echo 'R$ '.number_format(array_sum($total_dia) * $lin[0]['linha_comissao'], 2, ',', '.');?></td>
            </tr>
        </tfoot>
    </table>
<?php }?>