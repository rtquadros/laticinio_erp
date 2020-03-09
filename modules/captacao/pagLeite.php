
    <div class="form-row">
      <div class="col-3 mb-2 ml-auto">
        <form method="post" action="">
            <div class="input-group">
                <input type="text" class="form-control  monthpicker" name="mes_ref" id="mes_ref" value="<?php echo $mes_ref->format("m/Y"); ?>">
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
      $data_ini = $mes_ref->format("Y-m-{$data_arr[$i][0]}");
      $data_fim = $mes_ref->format("Y-m-{$data_arr[$i][1]}");
    ?>
      <div role="tabpanel" class="tab-pane fade <?php echo $today->format("j") >= $data_arr[$i][0] && $today->format("j") <= $data_arr[$i][1] ? 'show active' : ''; ?>" id="<?php echo "quinzena".$i;?>" aria-labelledby="<?php echo "quinzena".$i;?>">
        <div class="row">
          <div class="col mb-2"><h3>Pagamento de produtores</h3></div>
          <div class="col mb-2 hidden-xs text-right">
            <h4><small>Custo médio/Lt:</small>
            <?php 
            $leite = new Leite();
            echo 'R$ '.$leite->getCustoMedioLeite($data_ini, $data_fim); 
            ?>
            </h4>
          </div>
          <div class="col mb-2 text-right">
          	<div class="dropdown">
              <button type="button" class="btn btn-info btn-responsive dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-print"></span> Folhas de pagamento</button>
              <div class="dropdown-menu">
              	<a class="dropdown-item" href="print.php?mod=captacao&pag=pagLeite&func=imprimir&modelo=geral&data_ini=<?php echo $data_ini;?>&data_fim=<?php echo $data_fim;?>" role="button" target="_blank">Folha geral</a>
                <a class="dropdown-item" href="print.php?mod=captacao&pag=pagLeite&func=imprimir&modelo=comprovantes&data_ini=<?php echo $data_ini;?>&data_fim=<?php echo $data_fim;?>" role="button" target="_blank">Comprovantes</a>
              </div>
	          </div>
          </div>
        </div>
        <div class="table-responsive"> 
          <table class="table table-striped table-bordered table-sm datatable" id="<?php echo "table-quinzena".$i;?>">
          	<thead> 
              <tr> 
                <th>Produtor</th> 
                <th>Detalhes</th>
                <th>Leite</th>
                <th>Preço Médio/Lt</th>
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
              $produtor = new Produtor();
              $leite = new Leite();
              $prod_ids = $leite->selectLeite("DISTINCT leite_prod_id", "WHERE leite_data BETWEEN ? AND ?", array($data_ini, $data_fim));
              foreach ($prod_ids as $key => $prod_id) {
                $prod = $produtor->selectProdutor("*", "WHERE pessoa_id=?", array($prod_id["leite_prod_id"]));
              ?>
                <tr>
                  <td><?php echo $prod ? $prod[0]["pessoa_nome"] : "Produtor não encontrado (ID: {$prod_id["leite_prod_id"]})";?></td>
                  <td><?php echo $prod ? $prod[0]["pessoa_desc"] : "";?></td>
                  <td><?php echo $leite->getTotalLeiteProdutor($prod_id["leite_prod_id"], $data_ini, $data_fim);?></td>
                  <td><?php echo number_format($leite->getPrecoLeiteProdutor($prod_id["leite_prod_id"], $data_ini, $data_fim), 2, ",", ".");?></td>
                  <td><?php echo number_format($leite->getValorLeiteProdutor($prod_id["leite_prod_id"], $data_ini, $data_fim), 2, ",", ".");?></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>
                  	<a href="print.php?mod=captacao&pag=pagLeite&func=imprimir&modelo=detalhado&data_ini=<?php echo $data_ini;?>&data_fim=<?php echo $data_fim;?>&prod_id=<?php echo $prod_id["leite_prod_id"];?>" role="button" title="Imprimir detalhamento" target="_blank"><span class="fas fa-print"></span> <span class="sr-only">Imprimir</span></a>
                  </td>
                </tr>
              <?php }?>
            </tbody>
          </table>
        </div>

        <div class="row"><div class="col"><h3>Comissão de linhas de coleta</h3></div></div>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-sm datatable" >
            <thead> 
              <tr> 
                <th>Linha</th>
                <th>Carreteiro</th>
                <th>Detalhes</th>
                <th>Leite</th>
                <th>Comissão/Lt</th>
                <th>Valor à pagar</th>
                <th></th>
              </tr> 
            </thead> 
            <tbody> 
              <?php
              $linha = new Linha();
              $leite = new Leite();
              $pessoa = new Pessoa();
              $linha_ids = $leite->selectLeite("DISTINCT leite_linha_id", "WHERE leite_data BETWEEN ? AND ?", array($data_ini, $data_fim));
              foreach ($linha_ids as $key => $linha_id) {
                $lin = $linha->selectLinha("*", "WHERE linha_id=?", array($linha_id["leite_linha_id"]));
                if($lin){
                  $retorno = $leite->selectLeite("SUM(leite_quantidade) AS leite_total", "WHERE leite_linha_id=? AND leite_data BETWEEN ? AND ?", array($linha_id["leite_linha_id"], $data_ini, $data_fim));
                  $carreteiro = $pessoa->selectPessoa("pessoa_nome, pessoa_desc", "WHERE pessoa_id=?", array($lin[0]["linha_carreteiro"]));
              ?>
                  <tr>
                    <td><?php echo $lin[0]["linha_nome"];?></td>
                    <td><?php echo $carreteiro ? $carreteiro[0]["pessoa_nome"] : "Carreteiro não encontrado";?></td>
                    <td><?php echo $carreteiro ? $carreteiro[0]["pessoa_desc"] : "";?></td>
                    <td><?php echo $retorno[0]["leite_total"];?></td>
                    <td><?php echo number_format($lin[0]["linha_comissao"], 3, ",", ".");?></td>
                    <td><?php echo number_format($lin[0]["linha_comissao"]*$retorno[0]["leite_total"], 2, ",", ".");?></td>
                    <td>
                      <a href="print.php?mod=captacao&pag=pagLeite&func=imprimir&modelo=detalhado_linha&data_ini=<?php echo $data_ini;?>&data_fim=<?php echo $data_fim;?>&linha_id=<?php echo $lin[0]["linha_id"];?>" role="button" title="Imprimir detalhamento" target="_blank"><span class="fas fa-print"></span> <span class="sr-only">Imprimir</span></a>
                    </td>
                  </tr>
              <?php 
                }
              }?>
            </tbody>
          </table>
        </div>
      </div>
    <?php }?>
  </div>
</div>