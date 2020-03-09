<?php
$estoque = new Estoque();
$produto = new Produto();
$producao = new Producao();

if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){
    if($_GET["func"] == 'cadastrar'){
      $action = "modules/estoque/controlers/estoqueControl.php?pag=estoque&func=insertEstoque";
      //Entrada no estoque pode ser por produção ou compra
      if(isset($_GET["compra"])) $estoque_entrada_id = array("compra"=>$_GET["compra"]);
      elseif(isset($_GET["producao"])) $estoque_entrada_id = array("producao"=>$_GET["producao"]);
    } elseif($_GET["func"] == 'editar'){
      if(isset($_GET['estoque_id']) && !empty($_GET['estoque_id'])){ 
        $estoque_id = filter_input(INPUT_GET, "estoque_id", FILTER_SANITIZE_NUMBER_INT);  
        $action = "modules/estoque/controlers/estoqueControl.php?pag=estoque&func=updateEstoque&estoque_id={$estoque_id}";
        $retorno = $estoque->selectEstoque("*", "WHERE estoque_id=?", array($estoque_id));
      } else {
        header('Location: index.php?mod=estoque&pag=estoque&func=visualizar');
      }
    }
?>
    <form enctype="multipart/form-data" method="post" action="<?php echo $action; ?>">
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <td>Produto</td>
              <td>Lote</td>
              <td>Data entrada</td>
              <td>Quantidade</td>
              <td>Fabricação</td>
              <td>Validade</td>
,            </tr>
          </thead>
          <tbody>
            <tr>
              <td></td>
              <td>
                <input type="text" class="form-control" name="estoque_lote" id="estoque_lote" value="<?php echo isset($retorno) ? $retorno[0]['estoque_lote'] : ""; ?>" required>
              </td>
              <td>
                <div class="input-group">
                  <input type="text" class="form-control datepicker" name="estoque_data_entrada" id="estoque_data_entrada" value="<?php echo isset($retorno) ? date('d/m/Y', strtotime($retorno[0]['estoque_data_entrada'])) : date('d/m/Y'); ?>" required>
                  <div class="input-group-append">
                      <div class="input-group-text"><span class="fas fa-th"></span></div>
                  </div>
                </div>
              </td>
              <td>
                <div class="input-group">
                  <input type="text" class="form-control peso" name="estoque_quant_entrada" id="estoque_quant_entrada" placeholder="0" value="<?php echo isset($retorno) ? $retorno[0]['estoque_quant_entrada'] : ""; ?>" required>
                  <div class="input-group-append"><span class="input-group-text">Kg/Lt</span></div>
                </div>
              </td>
              <td>
                <div class="input-group">
                  <input type="text" class="form-control datepicker" name="estoque_fabricacao" id="estoque_fabricacao" value="<?php echo isset($retorno) ? date('d/m/Y', strtotime($retorno[0]['estoque_fabricacao'])) : date('d/m/Y'); ?>" required>
                  <div class="input-group-append">
                      <div class="input-group-text"><span class="fas fa-th"></span></div>
                  </div>
                </div>
              </td>
              <td>
                <div class="input-group">
                  <input type="text" class="form-control datepicker" name="estoque_validade" id="estoque_validade" value="<?php echo isset($retorno) ? date('d/m/Y', strtotime($retorno[0]['estoque_validade'])) : date('d/m/Y'); ?>" required>
                  <div class="input-group-append">
                      <div class="input-group-text"><span class="fas fa-th"></span></div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>  
      <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
      <input class="btn btn-secondary" type="reset" value="Cancelar">
    </form>

<?php 
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar") {
?>        
    <p><a class="btn  btn-primary" href="?mod=estoque&pag=estoque&func=cadastrar" role="button">Cadastrar item</a></p>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-2" id="myTab" role="tablist">
    <?php
    $prod_tipos = $estoque->getProdTipos();
    $i = 0;
    foreach ($prod_tipos as $prod_tipo => $tipo) {
      echo "<li class='nav-item'><a class='nav-link";
      echo $i == 0 ? " active" : "";
      echo "' id='prod_tipo{$i}-tab' data-toggle='tab' href='#prod_tipo{$i}' role='tab' aria-controls='{$i}' aria-selected='true'>".ucfirst($prod_tipo)."</a></li>";
      $i++;
    }
    ?>
    </ul>
      
    <!-- Tab panes -->
    <div class="tab-content" id="myTabContent">
    <?php 
    $i = 0;
    foreach ($prod_tipos as $prod_tipo => $tipo) {
    ?>
      <div role="tabpanel" class="tab-pane fade <?php echo $i == 0 ? 'show active' : ''; ?>" id="<?php echo "prod_tipo".$i;?>" aria-labelledby="<?php echo "prod_tipo".$i;?>">
        <h3><?php echo $tipo; ?></h3>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-sm datatable">
                <thead> 
                    <tr> 
                        <th>#</th>
                        <th>Nome</th>
                        <th>Cod. Barras</th>
                        <th>Marca</th>
                        <th>Unidade</th>
                        <th>Estoque Min</th>
                        <th>Preço custo</th>
                        <th>Preço venda</th>
                        <th>Tipo</th>
                        <th></th> 
                    </tr> 
                </thead> 
                <tbody> 
                    <?php 
                    $rows = $estoque->selectEstoque("*", "WHERE prod_tipo=?", array($prod_tipo));
                    foreach($rows as $row){
                    ?>
                        <tr> 
                            <td><?php echo $row['estoque_id']; ?></td> 
                            <td><a href="?mod=estoque&pag=estoque&func=editar&estoque_id=<?php echo $row['estoque_id']; ?>"><?php echo $row['prod_nome']; ?></a></td>
                            <td><?php echo $row['prod_codbarras']; ?></td>
                            <td><?php echo $row['prod_marca']; ?></td>
                            <td><?php echo $row['prod_unidade']; ?></td>
                            <td><?php echo $row['prod_estoque_min']; ?></td>
                            <td><?php echo 'R$ '.number_format($row['prod_preco_custo'], 2, ',', '.');?></td>
                            <td><?php echo 'R$ '.number_format($row['prod_preco_venda'], 2, ',', '.');?></td>
                            <td><?php echo ucfirst($row['prod_tipo']); ?></td>
                            <td><a href="modules/estoque/controlers/estoqueControl.php?func=deleteEstoque&estoque_id=<?php echo $row['estoque_id']; ?>" class="text-danger delete-confirm"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
                        </tr> 
                    <?php }?>
                </tbody>
            </table>
        </div>
      </div>
    <?php
    $i++;
    }
    ?>
    </div>
<?php
}
?>