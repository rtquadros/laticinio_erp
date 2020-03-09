<?php
if(isset($_GET["modelo"]) && $_GET["modelo"] == 'captacao_geral'){ 
?>
  <style type="text/css">
    @media print {
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
    }
  </style>
<?php
  for($i=1; $i<=2; $i++){
?>
    <h4>
      <small>LATICÍNIO NOVA VISTA</small><br />
      FOLHA DE COLETA<br />
      <small>Período de <?php echo $mes_ref->format("{$data_arr[$i][0]}/m/Y").' a '.$mes_ref->format("{$data_arr[$i][1]}/m/Y");?></small>
    </h4>
    <table class="table table-bordered table-sm" id="1quinzena">
      <col style="width: 3em;">
      <col style="width: 10%;">
      <thead> 
        <tr> 
          <th>ID</th>
          <th>Produtor</th> 
          <?php 
          for($dia = $data_arr[$i][0]; $dia <= $data_arr[$i][1]; $dia++){ 
            echo $dia < 10 ? "<th>0{$dia}</th>" : "<th>{$dia}</th>"; 
          }?>
        </tr> 
      </thead>
      <tbody> 
        <?php
        $produtor = new Produtor();
        $rows = $produtor->selectProdutor("*", "ORDER BY pessoa_nome ASC", array());
        foreach($rows as $row){
        ?>
          <tr> 
            <td><?php echo $row['pessoa_id']; ?></td>
            <td><?php echo $row['pessoa_nome']; ?></td>
            <?php for( $dia = $data_ini; $dia <= $diff; $dia++) echo '<td></td>';?>
          </tr> 
        <?php }?>
        <tr>
            <td colspan="2">Total diário</td>
            <?php  for( $dia = $data_ini; $dia <= $diff; $dia++) echo '<td></td>';?>
        </tr>
        <tr>
            <td colspan="2">Usado</td>
            <?php  for( $dia = $data_ini; $dia <= $diff; $dia++) echo '<td></td>';?>
        </tr>
        <tr>
            <td colspan="2">Lavagem</td>
            <?php  for( $dia = $data_ini; $dia <= $diff; $dia++) echo '<td></td>';?>
        </tr>
      </tbody>
    </table>
    <div class="clearfix page-break" style="clear: both; page-break-after: always;"></div>
<?php
  }
} elseif(isset($_GET["modelo"]) && $_GET["modelo"] == 'captacao_produtor'){
?>
	<style type="text/css">
        @media print {
            @page {
                size: A4 portrait;
                margin: 1cm;
            }
        }
    </style>
    <div class="row">
	<?php
    $produtor = new Produtor();
    $rows = $produtor->selectProdutor("*", "ORDER BY pessoa_nome ASC", array());
	$i = 1;
    foreach($rows as $row){
    ?>
    	<div class="col-6">
            <h4>
                <small>LATICÍNIO NOVA VISTA</small><br />
                CONTROLE DE LEITE ENTREGUE<br />
                PRODUTOR: <?php echo $row['pessoa_nome']; ?> | PERÍODO: <?php echo $mes_ref->format('m/Y');?>
            </h4>
            <table class="table table-bordered table-sm">
                <thead> 
                    <tr> 
                        <th>Dia</th>
                        <th>Quant</th>
                        <th>Temp.</th>
                        <th>Dens.</th>
                        <th>Acidez</th>
                        <th>Água</th>
                    </tr> 
                </thead> 
                <tbody>
                    <?php for($dia=1; $dia<=$mes_ref->format("t"); $dia++){ ?>
                        <tr>
                            <td><?php echo $dia;?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php } ?> 
                </tbody>
            </table>
        </div> 
    <?php
		if($i == 2){ 
			echo '<div class="clearfix page-break col-12 d-block"></div>';
			$i = 1;
		} else $i++;  
	}?>
    </div>
<?php 
} elseif(isset($_GET["modelo"]) && $_GET["modelo"] == 'controle_produtor'){
?>
	<style type="text/css">
        @media print {
            @page {
                size: A4 portrait;
                margin: 1cm;
            }
        }
    </style>
  <div class="row">
  <?php for($i=1; $i<=2; $i++){?>
    <div class="col-6">
        <h4>
            <small>LATICÍNIO NOVA VISTA</small><br />
            CONTROLE DE LEITE ENTREGUE<br />
            <small>PRODUTOR: </small>
        </h4>
        <table class="table table-bordered table-sm">
            <thead> 
                <tr> 
                    <th class="col-2">Dia</th> 
                    <th class="col-1">Hora</th>
                    <th class="col-4">Quant</th>
                    <th>Visto</th>
                </tr> 
            </thead> 
            <tbody>
                <?php for($dia=1; $dia<32; $dia++){ ?>
                    <tr>
                        <td><?php echo $dia;?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php 
                    if($dia == 15 || $dia == 31){
                ?>
                    <tr>
                        <td>Total</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php } 
                } ?> 
            </tbody>
        </table>
    </div>
<?php 
  }
?>
  </div>
<?php
} 	
