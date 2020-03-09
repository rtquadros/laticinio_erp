<?php
$producao = new Producao();
$pessoa = new Pessoa();
$produto = new Produto();
$receita = new Receita();

if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){
  if($_GET["func"] == 'cadastrar'){
    $action = "modules/estoque/controlers/producaoControl.php?pag=producao&func=insertProducao"; 
  } elseif($_GET["func"] == 'editar'){
    if(isset($_GET['producao_id']) && !empty($_GET['producao_id'])){ 
      $producao_id = filter_input(INPUT_GET, "producao_id", FILTER_SANITIZE_NUMBER_INT);  
      $action = "modules/estoque/controlers/producaoControl.php?pag=producao&func=updateProducao&producao_id={$producao_id}";
      $retorno = $producao->selectProducao("*", "WHERE producao_id=?", array($producao_id));
    } else {
      header('Location: index.php?mod=estoque&pag=producao&func=visualizar');
    }
  }
?>
  <form method="post" action="<?php echo $action; ?>">
  	<div class="row">
      <div class="col">
          <div class="form-group">
              <label for="producao_data_ordem">Data</label>
              <div class="input-group">
                  <input type="text" class="form-control datepicker" name="producao_data_ordem[]" id="producao_data_ordem" value="<?php echo isset($retorno) ? date('d/m/Y', strtotime($retorno[0]['producao_data_ordem'])) : date('d/m/Y'); ?>" <?php echo isset($retorno) ? "disabled" : "" ; ?> required>
                  <div class="input-group-append">
                      <div class="input-group-text"><span class="fas fa-th"></span></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-1">
          <div class="form-group">
              <label for="producao_data_ordem">Hora</label>
              <input type="text" class="form-control hora" name="producao_data_ordem[]" id="producao_data_ordem[]" value="<?php echo isset($retorno) ? date('H:i', strtotime($retorno[0]['producao_data_ordem'])) : date('H:i'); ?>" <?php echo isset($retorno) ? "disabled" : "" ; ?> required>
          </div>
      </div>
    	<div class="col">
      	<div class="form-group">
              <label for="producao_rec_id">Receita</label>
              <select id="producao_rec_id" name="producao_rec_id" class="form-control" <?php echo isset($retorno) ? "disabled" : "" ; ?> required>
                  <option value="">--- Selecione ---</option>
                  <?php
                  $rows = $receita->selectReceita("*", "", array());
                  foreach($rows as $row){
                    echo "<option value='{$row['rec_id']}'";
                    echo isset($retorno) && $retorno[0]["producao_rec_id"] == $row["rec_id"] ? " selected " : "";
                    echo ">".$produto->getNomeProduto($row["rec_prod_id"])."</option>";
                  }
                  ?>
              </select>
          </div>
      </div>
      <div class="col">
          <div class="form-group">
              <label for="producao_func_id">Responsável</label>
              <select id="producao_func_id" name="producao_func_id" class="form-control" <?php echo isset($retorno) ? "disabled" : "" ; ?> required>
                  <option value="">--- Selecione ---</option>
                  <?php
                  $rows = $pessoa->selectPessoa("*", "WHERE pessoa_categoria=?", array("funcionario"));
                  foreach($rows as $row){
                    echo "<option value='{$row['pessoa_id']}'";
                    echo isset($retorno) && $retorno[0]["producao_func_id"] == $row["pessoa_id"] ? " selected " : "";
                    echo ">{$row["pessoa_nome"]}</option>";
                  }
                  ?>
              </select>
          </div>
      </div>
      <div class="col">
          <div class="form-group">
              <label for="producao_quant">Quant. produto</label>
              <div class="input-group">
                  <input type="text" class="form-control peso" name="producao_quant" id="producao_quant" placeholder="0" value="<?php echo isset($retorno) ? $retorno[0]['producao_quant'] : ""; ?>" <?php echo isset($retorno) ? "disabled" : "" ; ?> required>
                  <div id="producao_prod_und" class="input-group-append"><span class="input-group-text"></span></div>
              </div>
          </div>
      </div>
    </div>
    <div class="row">
    	<div class="col">
        <ul class="nav nav-tabs mb-2" id="myTab" role="tablist">
          <li class="nav-item"><a class="nav-link active" id="insumos-tab" data-toggle="tab" href="#insumos" role="tab" aria-controls="1" aria-selected="true">Insumos</a></li>
          <li class="nav-item"><a class="nav-link" id="processos-tab" data-toggle="tab" href="#processos" role="tab" aria-controls="2" aria-selected="true">Processos</a></li>      
        </ul>

        <div class="tab-content" id="myTabContent">
          <div role="tabpanel" class="tab-pane fade show active" id="insumos" aria-labelledby="insumos">
            <table id="lista-insumos" class="table table-sm">
              <thead class="thead-light">
                <tr>
                  <th>Insumo</th>
                  <th>Unidade</th>
                  <th>Quant. receita</th>
                  <th>Lote</th>
                  <?php if(isset($retorno)){ ?>
                    <th>Quant. empenhada</th>
                  <?php } else {?>
                    <th>Estoque</th>
                    <th>Quant. empenhada</th>
                    <th></th>
                  <?php }?>
                </tr>
              </thead>
              <tbody>
                <tr class="item-insumos">
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input type="number" id="quant_lote" name="quant_lote" class="form-control" required></td>
                  <td><a href="#" class="btn btn-xs text-danger btn-remover"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
                </tr>
              <?php 
              if(isset($retorno)){
                foreach(unserialize($retorno[0]['producao_insumos']) as $key=>$item_insumos){ ?>
                <tr class="item-insumos">
                  <td><?php echo $item_insumos["insumo_id"] .'-'. $produto->getNomeProduto($item_insumos["insumo_id"]); ?></td>
                  <td><?php echo $produto->getUnidadeProduto($item_insumos["insumo_id"]); ?></td>
                  <td><?php echo $item_insumos['insumo_quant']; ?></td>
                  <td>000</td>
                  <td>0000</td>
                </tr>
              <?php } 
              }?>
              </tbody>
              <?php if(!isset($retorno)){ ?>
                <tfoot>
                  <tr>
                    <td colspan="8"><button type="button" class="btn btn-xs btn-primary pull-right" id="btn-inserir-insumo"><span class="fas fa-plus-circle"></span> Insumo</button></td>
                  </tr>
                </tfoot>
              <?php } ?>
            </table>
          </div>
          <div role="tabpanel" class="tab-pane fade" id="processos" aria-labelledby="processos">
            <table id="lista-processos" class="table table-sm sorted_table">
              <thead class="thead-light">
                <tr>
                  <th></th>
                  <th>Descrição</th>
                  <th>Equipamento</th>
                  <th>Duração</th>
                  <th>Limite</th>
                  <?php if(!isset($retorno)){ echo "<td></td>"; } ?>
                </tr>
              </thead>
              <tbody>
              <?php 
              if(isset($retorno)){
                foreach(unserialize($retorno[0]['producao_processos']) as $key=>$item_processos){
              ?>
                <tr class="item-processos">
                  <td></td>
                  <td><?php echo $item_processos['processo_nome']; ?></td>
                  <td><?php echo $item_processos['processo_equip']; ?></td>
                  <td><?php echo $item_processos['processo_duracao']["duracao"]; ?></td>
                  <td><?php echo $item_processos['processo_limite']; ?></td>
                </tr>
              <?php }
              }?>
              </tbody>
              <?php if(!isset($retorno)){ ?>
                <tfoot>
                  <tr>
                    <td colspan="5"><button type="button" class="btn btn-xs btn-primary pull-right" id="btn-inserir-processo"><span class="fas fa-plus-circle"></span> Processo</button></td>
                  </tr>
                </tfoot>
              <?php } ?>
            </table>
          </div>
        </div>
      </div>
    </div>
    <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
    <input class="btn btn-secondary" type="reset" value="Limpar">
  </form>

<?php 
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar") {
  require_once("includes/estoqueModal.php");
?>  

  <div class="form-row">
    <div class="col-md-auto mb-2">
      <a class="btn btn-primary btn-responsive" href="?mod=estoque&pag=producao&func=cadastrar" role="button">Ordem de produção</a>
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
      <li class="nav-item"><a class="nav-link active" id="tab-ativos" data-toggle="tab" href="#ativos" role="tab" aria-controls="tab-ativos" aria-selected="true">Em produção</a></li>
      <li class="nav-item"><a class="nav-link" id="tab-entregues" data-toggle="tab" href="#entregues" role="tab" aria-controls="tab-entregues">Entregues</a></li>
    </ul>
    
    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane fade table-responsive show active" id="ativos" aria-labelledby="ativos">
        <table id="ativos-table" class="table table-striped table-bordered table-sm datatable">
          <thead> 
              <tr>
                  <th>Produto</th>
                  <th>Quant.</th>
                  <th>Und.</th>
                  <th>Data da ordem</th>
                  <th>Data de entrega provável</th>
                  <th>Responsável</th>
                  <th></th>
                  <th></th>
              </tr> 
          </thead> 
          <tbody>
          <?php
          $rows = $producao->selectProducao("*", "WHERE producao_data_entrega IS NULL AND producao_data_ordem BETWEEN ? AND ? ORDER BY producao_data_ordem ASC", array($mes_ref->format("Y-m-01"), $mes_ref->format("Y-m-t")));
          foreach($rows as $row){
          ?>
            <tr>
              <td>
                <a href="?mod=estoque&pag=producao&func=editar&producao_id=<?php echo $row['producao_id']; ?>"><?php echo $produto->getNomeProduto($receita->getRecProdId($row["producao_rec_id"]));?></a>
              </td>
              <td><?php echo $row['producao_quant']; ?></td>
              <td><?php echo $produto->getUnidadeProduto($receita->getRecProdId($row["producao_rec_id"]));?></td>
              <td><?php echo date('d/m/Y \| H:i', strtotime($row['producao_data_ordem'])); ?></td>
              <td>
              <?php 
              $duracao = $receita->getDuracaoTotal($row["producao_rec_id"]);
              $data_entrega = new DateTime($row['producao_data_ordem']);
              $data_entrega->add(new DateInterval("PT{$duracao["h"]}H{$duracao["i"]}M"));
              echo $data_entrega->format("d/m/Y \| H:i");
              if( $today > $data_entrega){
              	echo ' <span class="fas fa-exclamation-triangle text-danger" title="Em atraso"></span>';	
              }
              ?>
              </td>
              <td><?php echo $pessoa->getPessoaNome($row["producao_func_id"]);?></td>
              <td>
                <a class="btn btn-sm btn-primary entrada-estoque" href="#" data-producao-id="<?php echo $row['producao_id'];?>"><span class="fas fa-dolly-flatbed"></span> Entregar</a>
              </td>
              <td>
                <a href="modules/estoque/controlers/producaoControl.php?mod=estoque&pag=producao&func=deleteProducao&producao_id=<?php echo $row['producao_id']; ?>" class="text-danger delete-confirm"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a>
              </td>
            </tr>
          <?php }?>
          </tbody>
        </table>
      	<div class="clearfix"></div>
        <div style="position:relative" class="gantt" id="GanttChartDIV"></div>
      </div>

      <div role="tabpanel" class="tab-pane fade table-responsive" id="entregues" aria-labelledby="entregues">
        <table id="entregues-table" class="table table-striped table-bordered table-sm datatable">
          <thead> 
            <tr>
              <th>Produto</th>
              <th>Quant.</th>
              <th>Und.</th>
              <th>Data da ordem</th>
              <th>Data de entrega</th>
              <th>Responsável</th>
            </tr> 
          </thead> 
          <tbody>
            <?php
            $rows = $producao->selectProducao("*", "WHERE producao_data_entrega BETWEEN ? AND ? ORDER BY producao_data_ordem DESC", array($mes_ref->format("Y-m-01"), $mes_ref->format("Y-m-t")));
            foreach($rows as $row){
            ?>
              <tr>
                <td>
                  <a href="?mod=estoque&pag=producao&func=editar&producao_id=<?php echo $row['producao_id']; ?>"><?php echo $produto->getNomeProduto($receita->getRecProdId($row["producao_rec_id"]));?></a>
                </td>
                <td><?php echo $row['producao_quant']; ?></td>
                <td><?php echo $produto->getUnidadeProduto($receita->getRecProdId($row["producao_rec_id"]));?></td>
                <td><?php echo date('d/m/Y \| H:i', strtotime($row['producao_data_ordem'])); ?></td>
                <td><?php echo date('d/m/Y \| H:i', strtotime($row['producao_data_entrega'])); ?></td>
                <td><?php echo $pessoa->getPessoaNome($row["producao_func_id"]);?></td>
              </tr>
            <?php }?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php } ?>