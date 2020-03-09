<?php
$produto = new Produto();

if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){
    if($_GET["func"] == 'cadastrar'){
      $action = "modules/estoque/controlers/produtoControl.php?pag=produto&func=insertProduto"; 
    } elseif($_GET["func"] == 'editar'){
      if(isset($_GET['prod_id']) && !empty($_GET['prod_id'])){ 
        $prod_id = filter_input(INPUT_GET, "prod_id", FILTER_SANITIZE_NUMBER_INT);  
        $action = "modules/estoque/controlers/produtoControl.php?pag=produto&func=updateProduto&prod_id={$prod_id}";
        $retorno = $produto->selectProduto("*", "WHERE prod_id=?", array($prod_id));
      } else {
        header('Location: index.php?mod=estoque&pag=produto&func=visualizar');
      }
    }
?>
    <form enctype="multipart/form-data" method="post" action="<?php echo $action; ?>">
      <div class="row">
        <div class="col">
        	<div class="row">
            <div class="col">
              <div class="form-group">
                <label for="prod_tipo">Tipo de item</label>
                <select class="form-control" name="prod_tipo" id="prod_tipo">
                    <?php
                    $prod_tipos = $produto->getProdTipos();
                    foreach($prod_tipos as $prod_tipo=>$tipo){
                      echo "<option value='{$prod_tipo}'";
                      echo isset($retorno[0]['prod_tipo']) && $retorno[0]['prod_tipo'] == $prod_tipo ? " selected " : '';
                      echo ">{$tipo}</option>";
                    }?>
                </select>
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label for="prod_nome">Nome</label>
                <input type="text" class="form-control" name="prod_nome" id="prod_nome" value="<?php echo isset($retorno) ? $retorno[0]['prod_nome'] : ""; ?>" required>
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label for="prod_marca">Marca</label>
                <input type="text" class="form-control" name="prod_marca" id="prod_marca" value="<?php echo isset($retorno) ? $retorno[0]['prod_marca'] : ""; ?>">
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label for="prod_codbarras">Código de barras</label>
                <input type="text" class="form-control" name="prod_codbarras" id="prod_codbarras" value="<?php echo isset($retorno) ? $retorno[0]['prod_codbarras'] : ""; ?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="form-group">
                <label for="prod_unidade">Unidade</label>
                <select class="form-control" name="prod_unidade" id="prod_unidade">
                    <?php
                    $unidades = $produto->getProdUnidades();
                    foreach($unidades as $und=>$unidade){
                      echo "<option value='{$und}'";
                      echo isset($retorno) && $retorno[0]["prod_unidade"] == $und ? " selected " : '';
                      echo ">{$unidade}</option>";
                    }?>
                </select>
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label for="prod_validade">Validade (em dias)</label>
                <input type="number" class="form-control" name="prod_validade" id="prod_validade" value="<?php echo isset($retorno) ? $retorno[0]['prod_validade'] : ""; ?>" placeholder="0" required>
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label for="prod_estoque_min">Estoque mín.</label>
                <input type="text" class="form-control peso" name="prod_estoque_min" id="prod_estoque_min" value="<?php echo isset($retorno) ? $retorno[0]['prod_estoque_min'] : ""; ?>" placeholder="00,00" required>
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label for="prod_preco_custo">Preço custo</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">R$</span>
                    </div>
                    <input type="text" class="form-control dinheiro" name="prod_preco_custo" id="prod_preco_custo" value="<?php echo isset($retorno) ? $retorno[0]['prod_preco_custo'] : ""; ?>" placeholder="00,00" required>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label for="prod_preco_venda">Preço venda</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">R$</span>
                    </div>
                    <input type="text" class="form-control dinheiro" name="prod_preco_venda" id="prod_preco_venda" value="<?php echo isset($retorno) ? $retorno[0]['prod_preco_venda'] : ""; ?>" placeholder="00,00" required>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="imagePreview">
            <img style="max-height:150px;height:150px;" class="img-responsive center-block img-thumbnail" src="<?php echo $retorno[0]['prod_imagem']; ?>">
            <div class="form-group">
                <label for="prod_imagem">Imagem</label>
                <input type="file" class="form-control" name="prod_imagem_upload" id="prod_imagem_upload" placeholder="JPG, PNG ou GIF">
                <input type="hidden" class="form-control" name="prod_imagem" id="prod_imagem" value="<?php echo isset($retorno) ? $retorno[0]['prod_imagem'] : ""; ?>">
            </div>
          </div>
        </div>
      </div>
      <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
      <input class="btn btn-secondary" type="reset" value="Limpar">
    </form>

<?php 
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar") {
?>        
    <p><a class="btn  btn-primary" href="?mod=estoque&pag=produto&func=cadastrar" role="button">Cadastrar item</a></p>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-2" id="myTab" role="tablist">
    <?php
    $prod_tipos = $produto->getProdTipos();
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
                        <th title="Unidade de medida">Und.</th>
                        <th title="Validade em dias">Validade</th>
                        <th>Estoque Min</th>
                        <th>Preço custo</th>
                        <th>Preço venda</th>
                        <th></th> 
                    </tr> 
                </thead> 
                <tbody> 
                    <?php 
                    $rows = $produto->selectProduto("*", "WHERE prod_tipo=?", array($prod_tipo));
                    foreach($rows as $row){
                    ?>
                        <tr> 
                            <td><?php echo $row['prod_id']; ?></td> 
                            <td><a href="?mod=estoque&pag=produto&func=editar&prod_id=<?php echo $row['prod_id']; ?>"><?php echo $row['prod_nome']; ?></a></td>
                            <td><?php echo $row['prod_codbarras']; ?></td>
                            <td><?php echo $row['prod_marca']; ?></td>
                            <td><?php echo $row['prod_unidade']; ?></td>
                            <td><?php echo $row['prod_validade']; ?></td>
                            <td><?php echo $row['prod_estoque_min']; ?></td>
                            <td><?php echo 'R$ '.number_format($row['prod_preco_custo'], 2, ',', '.');?></td>
                            <td><?php echo 'R$ '.number_format($row['prod_preco_venda'], 2, ',', '.');?></td>
                            <td><a href="modules/estoque/controlers/produtoControl.php?func=deleteProduto&prod_id=<?php echo $row['prod_id']; ?>" class="text-danger delete-confirm"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
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