<!-- Plugin para reorganização de listas -->
<link rel="stylesheet" type="text/css" href="modules/estoque/css/sortable.css" />

<?php
$receita = new Receita();
$produto = new Produto();

if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){
if($_GET["func"] == 'cadastrar'){
  $action = "modules/estoque/controlers/receitaControl.php?pag=receita&func=insertReceita"; 
} elseif($_GET["func"] == 'editar'){
  if(isset($_GET['rec_id']) && !empty($_GET['rec_id'])){ 
    $rec_id = filter_input(INPUT_GET, "rec_id", FILTER_SANITIZE_NUMBER_INT);  
    $action = "modules/estoque/controlers/receitaControl.php?pag=receita&func=updateReceita&rec_id={$rec_id}";
    $retorno = $receita->selectReceita("*", "WHERE rec_id=?", array($rec_id));
  } else {
    header('Location: index.php?mod=estoque&pag=receita&func=visualizar');
  }
}
?>
<form method="post" action="<?php echo $action; ?>">
  <div class="row">
    <div class="col-md-3">
      <div class="form-group">
        <label for="rec_prod_id">Produto</label>
        <select id="rec_prod_id" name="rec_prod_id" class="form-control" required>
        <?php
        $produto = new Produto();
        $rows = $produto->selectProduto("*", "WHERE prod_tipo=?", array("produto"));
        foreach($rows as $row){
          echo "<option value='{$row['prod_id']}'";
          echo isset($retorno) && $retorno[0]["rec_prod_id"] == $row["prod_id"] ? " selected " : "";
          echo ">{$row['prod_nome']}</option>";
        }
        ?>
        </select>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <table id="lista-insumos" class="table table-condensed">
        <thead>
          <tr>
            <th class="col-md-5">Insumo</th>
            <th class="col-md-3">Consumido</th>
            <th class="col-md-3">Estoque</th>
            <th class="col-md-1"></th>
          </tr>
        </thead>
        <tbody>
        <?php 
        if(isset($retorno)){
          foreach(unserialize($retorno[0]['rec_insumos']) as $key=>$item_insumos){
        ?>
          <tr class="item-insumos">
            <td>
                <select id="insumo_id_<?php echo $key; ?>" name="insumo_id[]" class="form-control" required="">
                <?php
                $rows = $produto->selectProduto("*", "WHERE prod_tipo=?", array("insumo"));
                foreach($rows as $row){
                  echo "<option value='{$row['prod_id']}'";
                  echo isset($retorno) && $item_insumos["insumo_id"] == $row["prod_id"] ? " selected " : "";
                  echo ">{$row['prod_nome']}</option>";
                }
                ?>
                </select>
            </td>
            <td>
                <input type="text" class="form-control peso" id="insumo_quant_<?php echo $key; ?>" name="insumo_quant[]" placeholder="0,00" size="2" value="<?php echo $item_insumos['insumo_quant']; ?>" required>
            </td>
            <td>
                <input type="text" class="form-control" id="insumo_estoque_<?php echo $key; ?>" name="insumo_estoque[]" size="2" value="" placeholder="0,00" readonly>
            </td>
            <td><a href="#" class="btn btn-xs text-danger btn-remover"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
          </tr>
		<?php }
        }?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4"><button type="button" class="btn btn-xs btn-primary pull-right" id="btn-inserir-insumo"><span class="fas fa-plus-circle"></span> Insumo</button></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="col-md-8">
      <table id="lista-processos" class="table table-condensed sorted_table">
        <thead>
          <tr>
            <th></th>
            <th>Processo</th>
            <th>Equipamento</th>
            <th>Duração</th>
            <th>Limite</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <?php 
        if(isset($retorno)){
          foreach(unserialize($retorno[0]['rec_processos']) as $key=>$item_processos){
        ?>
          <tr class="item-processos" draggable="true">
            <td><span class="btn btn-xs"><span class="fas fa-arrows-alt"></span><span class="sr-only">Mover</span></span></td>
            <td><input type="text" class="form-control" id="processo_nome_<?php echo $key; ?>" name="processo_nome[]" placeholder="Identificação" value="<?php echo $item_processos['processo_nome']; ?>" required></td>
            <td><input type="text" class="form-control" id="processo_equip_<?php echo $key; ?>" name="processo_equip[]" placeholder="Equipamento" value="<?php echo $item_processos['processo_equip']; ?>"></td>
            <td><input type="text" class="form-control duracao" id="processo_duracao_<?php echo $key; ?>" name="processo_duracao[]" placeholder="00:00" size="2" value="<?php echo $item_processos['processo_duracao']; ?>" required></td>
            <td><div class="input-group"><input type="text" class="form-control peso" id="processo_limite_<?php echo $key; ?>" name="processo_limite[]" placeholder="0,00" size="2" value="<?php echo $item_processos['processo_limite']; ?>" required><div class="input-group-append"><span class="input-group-text">Kg/Lt</span></div></div></td>
            <td><a href="#" class="btn btn-xs text-danger btn-remover"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
          </tr>
        <?php }
        }?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="7"><button type="button" class="btn btn-xs btn-primary pull-right" id="btn-inserir-processo"><span class="fas fa-plus-circle"></span> Processo</button></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
  <input class="btn btn-secondary" type="reset" value="Limpar">
</form>

<?php 
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar") {
?>        
    <p><a class="btn  btn-primary" href="?mod=estoque&pag=receita&func=cadastrar" role="button">Cadastrar receita</a></p>

    <div class="table-responsive">
      <table class="table table-sm datatable">
        <thead class="thead-light"> 
          <tr> 
            <th>#</th> 
            <th>Receita</th>
            <th>Insumos</th>
            <th>Processos</th>
            <th></th>
          </tr> 
        </thead> 
        <tbody>
    	<?php
		$rows = $receita->selectReceita("*", "", array());
        foreach($rows as $row){
		?>
    	  <tr>
        	<td><?php echo $row['rec_id']; ?></td>
            <td><a href="?mod=estoque&pag=receita&func=editar&rec_id=<?php echo $row['rec_id']; ?>"><?php echo $produto->getNomeProduto($row['rec_prod_id']); ?></a></td>
            <td>
              <table class="table table-sm table-bordered table-striped">
            	<thead>
            	  <tr>
                	<th>Insumo</th>
                    <th>Quant.</th>
                    <th>Unidade</th>
                  </tr>
                </thead>
                <?php foreach(unserialize($row['rec_insumos']) as $insumo){ ?>
            	  <tr>
                	<td><?php echo $produto->getNomeProduto($insumo['insumo_id']); ?></td>
                    <td><?php echo $insumo['insumo_quant']; ?></td>
                    <td><?php echo $produto->getUnidadeProduto($insumo['insumo_id']); ?></td>
                  </tr>
                <?php }?>
              </table>
            </td>
            <td>
              <table class="table table-sm table-bordered table-striped">
            	<thead>
            	  <tr>
                	<th>#</th>
                    <th>Processo</th>
                    <th>Equipamento</th>
                    <th>Duração</th>
                    <th>Limite</th>
                  </tr>
                </thead>
                <?php foreach(unserialize($row['rec_processos']) as $key=>$processo){ ?>
            	  <tr>
                	<td><?php echo '#'.$key; ?></td>
                	<td><?php echo $processo['processo_nome']; ?></td>
                	<td><?php echo $processo['processo_equip']; ?></td>
                	<td><?php echo $processo['processo_duracao']; ?></td>
                	<td><?php echo $processo['processo_limite']; ?></td>
                  </tr>
                <?php }?>
              </table>
            </td>
            <td><a href="modules/estoque/controlers/receitaControl.php?func=deleteReceita&rec_id=<?php echo $row['rec_id']; ?>" class="btn btn-xs text-danger delete-confirm"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
          </tr>
        <?php }?>
        </tbody>
      </table>
    </div>
<?php
}
?>