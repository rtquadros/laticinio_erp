
<?php
if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){

    if($_GET["func"] == 'cadastrar'){
      $action = "modules/captacao/controlers/leiteControl.php?mod={$_GET['mod']}&pag={$_GET['pag']}&func=insertLeite"; 
    } elseif($_GET["func"] == 'editar'){
      if(isset($_GET['leite_id']) && !empty($_GET['leite_id'])){ 
        $leite_id = filter_input(INPUT_GET, "leite_id", FILTER_SANITIZE_NUMBER_INT);  
        $action = "modules/captacao/controlers/leiteControl.php?mod={$_GET['mod']}&pag={$_GET['pag']}&func=updateLeite&leite_id={$leite_id}"; 
        $leite = new Leite();
        $retorno = $leite->selectLeite("*", "WHERE leite_id=?", array($leite_id));
      } else {
        header('Location: index.php?mod=captacao&pag=leite&func=visualizar');
      }
    }
?>
    <form method="post" action="<?php echo $action; ?>">
        <div class="form-row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="leite_data">Data</label>
                    <div class="input-group">
                        <?php if(isset($retorno)){ ?> 
                          <input type="text" class="form-control" name="leite_data" id="leite_data" value="<?php echo filter_var($retorno[0]['leite_data'], FILTER_CALLBACK, array("options"=>array("FilterDb", "brDate")));?>" required tabindex="1" readonly>
                        <?php } else {?>
                          <input type="text" class="form-control datepicker" name="leite_data" id="leite_data" value="<?php echo date('d/m/Y');?>" required  tabindex="1">
                        <?php }?>
                        <div class="input-group-append">
                          <span class="input-group-text"><span class="fas fa-th"></span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="leite_prod_id">Produtor</label>
                    <select class="form-control" name="leite_prod_id" id="leite_prod_id" required  tabindex="2" <?php echo isset($retorno) ? "readonly" : '';?>>
                    	<option disabled selected >Selecione o produtor</option>
                    	<?php
    					  $produtor = new Produtor();
    					  $rows = $produtor->selectProdutor("*", "ORDER BY pessoa_nome ASC", array());
    					  foreach($rows as $row){
    					    echo "<option value='{$row["pessoa_id"]}'";
                            if(isset($retorno)){
                              echo $retorno[0]["leite_prod_id"] == $row["pessoa_id"] ? " selected " : " disabled ";
                            }
                            echo ">{$row['pessoa_nome']}</option>";  
                          }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="leite_quantidade">Litros entregues</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="leite_quantidade" id="leite_quantidade" placeholder="0" value="<?php echo isset($retorno) ? $retorno[0]['leite_quantidade'] : '';?>" required  tabindex="3">
                        <div class="input-group-append"><span class="input-group-text">Lt</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="leite_preco">Preço do leite</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                        <input type="text" class="form-control dinheiro" name="leite_preco" id="leite_preco" placeholder="0,00" value="<?php echo isset($retorno) ? $retorno[0]['leite_preco'] : '';?>" <?php echo !isset($retorno) ? "readonly" : '';?> tabindex="4">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="leite_linha_id">Linha de coleta</label>
                    <select class="form-control" name="leite_linha_id" id="leite_linha_id" tabindex="5" >
                    	<option disabled selected>Selecione a linha</option>
                        <?php
    					$linha = new Linha();
                        $rows = $linha->selectLinha("*", "", array());
    					foreach($rows as $row){
                          echo "<option value='{$row["linha_id"]}'";
                          echo isset($retorno) && $retorno[0]["leite_linha_id"] == $row["linha_id"] ? " selected " : '';
                          echo ">{$row['linha_nome']}</option>";  
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
        <input class="btn btn-secondary" type="reset" value="Limpar">
    </form>
<?php
} elseif(isset($_GET["func"]) && $_GET["func"] == 'importar'){
?>
	<div class="row">
        <div class="col-xs-12 col-md-5">
            <form method="post" action="modules/captacao/process.php?funcao=exportLeite" target="_blank">
				<div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="mes_ref">Mês para exportação</label>
                            <input type="text" class="form-control  monthpicker" name="mes_ref" id="mes_ref" value="<?php echo $mes_ref->format('m/Y'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                    	<br />
                        <button type="submit" class="btn  btn-default"><span class="fas fa-open"></span> Exportar</button>
                    </div>
            	</div>
            </form>
        </div>
        <div class="col-xs-12 col-md-5">
            <form enctype="multipart/form-data"  method="post" action="modules/captacao/process.php?mod=<?php echo $_GET['mod']; ?>&funcao=importLeite">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="leite_csv_upload">Arquivo CSV</label>
                            <input type="file" class="form-control" name="leite_csv_upload" id="leite_csv_upload" placeholder="CSV" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <br />
                        <button type="submit" class="btn  btn-default"><span class="glyphicon glyphicon-save"></span> Importar</button>
                    </div>
                </div>
            </form>
    	</div>
	</div>
<?php	
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar") {
?>  
    <div class="form-row">
      <div class="col-md-auto mb-2">
        <a class="btn btn-primary btn-responsive" href="?mod=captacao&pag=leite&func=cadastrar" role="button">Cadastrar leite</a>
      </div>
      <div class="col-md-auto mb-2">
        <a class="btn btn-info btn-responsive" href="?mod=captacao&pag=leite&func=importar" role="button">Importar/Exportar</a>
      </div>
      <div class="col-md-auto mb-2">
        <div class="dropdown">
            <button type="button" class="btn btn-info btn-responsive dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-print"></span> Folhas de anotação</button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="print.php?mod=captacao&pag=leite&func=imprimir&modelo=captacao_geral&mes_ref=<?php echo $mes_ref->format('m/Y'); ?>" role="button" target="_blank">Captação geral</a>
                <a class="dropdown-item" href="print.php?mod=captacao&pag=leite&func=imprimir&modelo=captacao_produtor&mes_ref=<?php echo $mes_ref->format('m/Y'); ?>" role="button" target="_blank">Captação por produtor</a>
                <a class="dropdown-item" href="print.php?mod=captacao&pag=leite&func=imprimir&modelo=controle_produtor&mes_ref=<?php echo $mes_ref->format('m/Y'); ?>" role="button" target="_blank">Controle do produtor</a>
            </div>
        </div>
      </div>
      <div class="col-3 mb-2 ml-auto">
        <form method="post" action="">
            <div class="input-group">
                <input type="text" class="form-control  monthpicker" name="mes_ref" id="mes_ref" value="<?php echo $mes_ref->format('m/Y'); ?>">
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
          if($today->format("j") >= $data_arr[$i][0] && $today->format("j") <= $data_arr[$i][1]) echo " active";
          echo "'  id='quinzena{$i}-tab' data-toggle='tab' href='#quinzena{$i}' role='tab' aria-controls='{$i}' aria-selected='true'>{$i}º Quinzena</a></li>";
        }
        ?>
      </ul>
        
      <!-- Tab panes -->
      <div class="tab-content" id="myTabContent">
        <?php 
        for($i=1; $i<=2; $i++){ 
        ?>
          <div role="tabpanel" class="tab-pane fade <?php echo $today->format("j") >= $data_arr[$i][0] && $today->format("j") <= $data_arr[$i][1] ? 'show active' : ''; ?>" id="<?php echo "quinzena".$i;?>" aria-labelledby="<?php echo "quinzena".$i;?>">
            	<table class="table table-striped table-bordered table-sm datatable" id="<?php echo "table-quinzena".$i;?>">
                    <thead> 
                        <tr> 
                            <th>Produtor</th> 
                            <?php 
                            for($dia = $data_arr[$i][0]; $dia <= $data_arr[$i][1]; $dia++){ 
                                echo "<th>{$dia}</th>"; 
                            }?>
                            <th>Total</th> 
                        </tr> 
                    </thead>
                    <tbody>
                      <?php
                      $total_dia = array();
                      $produtor = new Produtor();
                      $leite = new Leite();
                      $prod_ids = $leite->selectLeite("DISTINCT leite_prod_id", "WHERE leite_data BETWEEN ? AND ?", array($mes_ref->format("Y-m-{$data_arr[$i][0]}"), $mes_ref->format("Y-m-{$data_arr[$i][1]}")));
                      foreach ($prod_ids as $key => $prod_id) {
                        $total_produtor = 0;
                        $produtor_nome = $produtor->getProdutorNome($prod_id["leite_prod_id"]);
                        echo "<tr>";
                        echo "<td>{$produtor_nome}</td>";
                        for($dia = $data_arr[$i][0]; $dia <= $data_arr[$i][1]; $dia++){
                          $retorno = $leite->selectLeite("*", "WHERE leite_prod_id=? AND leite_data=?", array($prod_id["leite_prod_id"], $mes_ref->format("Y-m-{$dia}")));
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
                            <td>Total diário</td>
                            <?php for($dia = $data_arr[$i][0]; $dia <= $data_arr[$i][1]; $dia++){?>
                              <td><?php echo isset($total_dia[$dia]) ? $total_dia[$dia] : 0; ?></td>  
                            <?php }?>
                            <td><?php echo array_sum($total_dia);?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php }?>
        </div>
	</div>
<?php
}
?>