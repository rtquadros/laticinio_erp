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
  <div class="modal fade" id="modal_empenho" tabindex="-1" role="dialog" aria-labelledby="empenhoModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="form_empenho">
          <div class="modal-header py-1">
            <h5 class="modal-title" id="empenhoModal">Empenhar insumo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="estoque_id">Lote</label>
                  <select class="form-control" id="estoque_id" name="estoque_id" required></select>
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="estoque_quant_empenhada">Empenhar</label>
                  <div class="input-group">
                    <input class="form-control peso" type="number" id="estoque_quant_empenhada" name="estoque_quant_empenhada" required data-maxval="">
                    <div id="estoque_prod_und" class="input-group-append"><span class="input-group-text"></span></div>
                  </div>
                  <small class="form-text text-muted help-block with-errors"></small>
                </div>
              </div>
            </div>
            <table id="detalhe-estoque" class="table table-sm">
              <thead>
                <tr>
                  <th>Data entrada</th>
                  <th>Estoque</th>
                  <th>Validade</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success" type="submit">Cadastrar</button>
            <button class="btn btn-danger" type="button" data-dismiss="modal" aria-label="Close">Cancelar</button>
          </div>
          <input type="hidden" name="insumo_index" id="insumo_index">
        </form>
      </div>
    </div>
  </div>

  <form method="post" id="form-producao" action="<?php echo $action; ?>">
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
                echo !empty($row["rec_descricao"]) ? ">".$row["rec_descricao"]."</option>" : ">".$produto->getNomeProduto($row["rec_prod_id"])."</option>";
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
                  $rows = $pessoa->selectPessoa("*", "WHERE pessoa_categoria=? ORDER BY pessoa_nome ASC", array("funcionario"));
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
                  <input type="text" class="form-control peso" name="producao_quant" id="producao_quant" placeholder="0" value="<?php echo isset($retorno) ? $retorno[0]['producao_quant'] : ""; ?>" <?php echo isset($retorno) ? "disabled" : "" ; ?> required disabled>
                  <div class="input-group-append"><span class="input-group-text" id="prod_unidade">--</span></div>
              </div>
          </div>
      </div>
    </div>
    <div class="row">
      <div class="col-3">
        <div class="card mb-3">
          <img class="card-img-top" id="prod_imagem" src="" alt="">
          <div class="card-body mb-0 py-2">
            <h5 class="card-title mb-0">Ordem #<?php echo $producao->getOrdemProducao(); ?></h5>
          </div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item"><b>Produto:</b> <span id="prod_nome"></span></li>
            <li class="list-group-item"><b>Cód:</b> <span id="prod_codbarras"></span></li>
            <li class="list-group-item"><b>Marca:</b> <span id="prod_marca"></span></li>
            <li class="list-group-item"><b>Validade:</b> <span id="prod_validade"></span> dias</li>
          </ul>
        </div>
      </div>
    	<div class="col">
        <ul class="nav nav-tabs mb-2" id="myTab" role="tablist">
          <li class="nav-item"><a class="nav-link active" id="insumos-tab" data-toggle="tab" href="#insumos" role="tab" aria-controls="1" aria-selected="true">Insumos</a></li>
          <li class="nav-item"><a class="nav-link" id="processos-tab" data-toggle="tab" href="#processos" role="tab" aria-controls="2" aria-selected="true">Processos</a></li>      
        </ul>

        <div class="tab-content" id="myTabContent">
          <div role="tabpanel" class="tab-pane fade show active" id="insumos" aria-labelledby="insumos">
            <table id="lista-insumos" class="table table-sm table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Insumo</th>
                  <th>Unidade</th>
                  <th title="Quantidade exigida">Qt. exigida</th>
                  <th>Total empenhado</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <div role="tabpanel" class="tab-pane fade" id="processos" aria-labelledby="processos">
            <table id="lista-processos" class="table table-striped table-bordered table-sm">
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
              if(isset($retorno)){
                foreach(unserialize($retorno[0]['producao_processos']) as $key=>$item_processos){
              ?>
                <tr class="item-processos">
                  <td><?php echo $key;?></td>
                  <td><?php echo $item_processos['processo_nome']; ?></td>
                  <td><?php echo $item_processos['processo_equip']; ?></td>
                  <td><?php echo $item_processos['processo_duracao']["duracao"]; ?></td>
                  <td><?php echo $item_processos['processo_limite']; ?></td>
                </tr>
              <?php }
              }?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" name="producao_ordem" id="producao_ordem" value="<?php echo $producao->getOrdemProducao(); ?>">
    <input type="hidden" name="producao_insumos" id="producao_insumos">
    <input type="hidden" name="producao_processos" id="producao_processos">
    <input type="hidden" name="producao_prod_id" id="producao_prod_id">
    <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
    <input class="btn btn-secondary" type="reset" value="Limpar">
  </form>

<?php 
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar") {
?>  
  <!-- Modal tem o conteúdo preenchido por AJAX -->
  <div class="modal fade" id="modal_estoque" tabindex="-1" role="dialog" aria-labelledby="estoqueModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="post" action="modules/estoque/controlers/estoqueControl.php?mod=estoque&pag=producao&func=insertEstoque">
          <div class="modal-header py-1">
            <h5 class="modal-title" id="estoqueModal">Entrada em estoque </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <p class="lead"><small>Produto:</small> <span id="prod_nome"></span></p>
            <div class="form-row">
              <div class="form-group col">
                <label for="estoque_data_entrada">Data de entrada</label>
                <div class="input-group">
                  <input type="text" class="form-control datepicker" name="estoque_data_entrada" id="estoque_data_entrada" value="<?php echo date('d/m/Y'); ?>" required>
                  <div class="input-group-append">
                      <div class="input-group-text"><span class="fas fa-th"></span></div>
                  </div>
                </div>
              </div>
              <div class="form-group col">
                <label for="estoque_lote">Lote</label>
                <input type="text" class="form-control" name="estoque_lote" id="estoque_lote" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col">
                <label for="estoque_quant_entrada">Quantidade</label>
                <div class="input-group">
                  <input type="text" class="form-control peso" name="estoque_quant_entrada" id="estoque_quant_entrada" placeholder="0" required>
                  <div class="input-group-append"><span class="input-group-text" id="prod_unidade"></span></div>
                </div>
              </div>
              <div class="form-group col">
                <label for="estoque_custo">Custo</label>
                <div class="input-group">
                  <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                  <input type="text" class="form-control dinheiro" name="estoque_custo" id="estoque_custo" placeholder="0">
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col">
                <label for="estoque_fabricacao">Fabricação</label>
                <div class="input-group">
                  <input type="text" class="form-control datepicker" name="estoque_fabricacao" id="estoque_fabricacao" required>
                  <div class="input-group-append">
                      <div class="input-group-text"><span class="fas fa-th"></span></div>
                  </div>
                </div>
              </div>
              <div class="form-group col">
                <label for="estoque_validade">Validade</label>
                <div class="input-group">
                  <input type="text" class="form-control datepicker" name="estoque_validade" id="estoque_validade" required>
                  <div class="input-group-append">
                      <div class="input-group-text"><span class="fas fa-th"></span></div>
                  </div>
                </div>
              </div>
            </div>
            <input type="hidden" name="producao_id" id="producao_id">
            <input type="hidden" name="estoque_prod_id" id="estoque_prod_id">
            <input type="hidden" name="estoque_entrada_id" id="estoque_entrada_id">
          </div>
          <div class="modal-footer">
            <input class="btn btn-success" type="submit" value="Estocar">
            <button class="btn btn-danger" type="button" data-dismiss="modal" aria-label="Close">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

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
    <?php
    $estatus = array("ativos", "entregues");
    foreach($estatus as $key=>$estado){
    ?>  
      <div role="tabpanel" class="tab-pane fade table-responsive <?php echo $key == 0 ? "show active" : ""; ?>" id="<?php echo $estado;?>" aria-labelledby="<?php echo $estado;?>">
        <table id="<?php echo $estado;?>-table" class="table table-striped table-bordered table-sm datatable">
          <thead> 
              <tr>  
                <th>Produto/Receita</th>
                <th>Quant.</th>
                <th>Und.</th>
                <th>Data da ordem</th>
                <th><?php echo $estado == "ativos" ? "Previsão" : "Data"; ?> de entrega</th>
                <th>Responsável</th>
                <th></th>
              </tr> 
          </thead> 
          <tbody>
          <?php
          $if_ativos = $estado == "ativos" ? "IS" : "IS NOT";
          $rows = $producao->selectProducao("*", "WHERE producao_data_entrega {$if_ativos} NULL AND producao_data_ordem BETWEEN ? AND ? ORDER BY producao_data_ordem DESC", array($mes_ref->format("Y-m-01"), $mes_ref->format("Y-m-t")));

          foreach($rows as $row){
          ?>
            <tr>
              <td>
                <?php echo "#".$producao->getOrdemProducao($row["producao_id"])." - ";?>
                <?php echo $row["producao_rec_id"] != 0 ? $produto->getNomeProduto($receita->getRecProdId($row["producao_rec_id"])) : $produto->getNomeProduto($row["producao_prod_id"]);?>
              </td>
              <td><?php echo $row['producao_quant']; ?></td>
              <td>
                <?php echo $row["producao_rec_id"] != 0 ? $produto->getUnidadeProduto($receita->getRecProdId($row["producao_rec_id"])) : $produto->getUnidadeProduto($row["producao_prod_id"]);?>
              </td>
              <td><?php echo date('d/m/Y', strtotime($row['producao_data_ordem'])); ?></td>
              <td>
              <?php 
              if($estado == "ativos"){
                $duracao = $producao->getEntregaEstimada($row["producao_id"]);
                $data_entrega = new DateTime($row['producao_data_ordem']);
                $data_entrega->add(new DateInterval("PT{$duracao["h"]}H{$duracao["i"]}M"));
                echo $data_entrega->format("d/m/Y");
                if( $today > $data_entrega){
                  echo ' <span class="fas fa-exclamation-triangle text-danger" title="Em atraso"></span>';  
                }
              } else {
                $data_entrega = new DateTime($row['producao_data_entrega']);
                echo $data_entrega->format("d/m/Y");
              }
              ?>
              </td>
              <td><?php echo $pessoa->getPessoaNome($row["producao_func_id"]);?></td>
              <td class="text-center">
              <?php if($estado == "ativos"){ ?>  
                <ul class="list-inline mb-0">
                  <li class="list-inline-item"><a href="#" title="Entregar produção" class="entrada-estoque" data-toggle="modal" data-target="#modal_estoque" data-producao-id="<?php echo $row['producao_id'];?>"><span class="fas fa-dolly-flatbed"></span> <span class="sr-only">Entregar</span></a></li>
                  <li class="list-inline-item"><a title="Imprimir ordem" href="print.php?mod=estoque&pag=producao&func=imprimir&modelo=ordem&producao_id=<?php echo $row['producao_id'];?>" role="button" target="_blank"><span class="fas fa-print"></span> <span class="sr-only">Imprimir</span></a></li>
                  <li class="list-inline-item"><a href="modules/estoque/controlers/producaoControl.php?mod=estoque&pag=producao&func=deleteProducao&producao_id=<?php echo $row['producao_id']; ?>" title="Excluir" class="delete-confirm text-danger"><span class="fas fa-trash"></span> <span class="sr-only">Excluir</span></a></li>
                </ul>
              <?php } else {?>
                <a title="Imprimir ordem" href="print.php?mod=estoque&pag=producao&func=imprimir&modelo=ordem&producao_id=<?php echo $row['producao_id'];?>" role="button" target="_blank"><span class="fas fa-print"></span> <span class="sr-only">Imprimir</span></a>
              <?php }?>
              </td>
            </tr>
          <?php }?>
          </tbody>
        </table>
      	<div class="clearfix"></div>
        <div style="position:relative" class="gantt" id="GanttChartDIV"></div>
      </div>
    <?php }?>
    </div>
  </div>
<?php } ?>