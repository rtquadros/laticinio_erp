<?php
$cat = new MovCategoria();
$mov = new Movimentacao();
$mov_tipo_nome = $mov->getMovTipos();

if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){
    $cat_tipo = filter_input(INPUT_GET, "cat_tipo", FILTER_SANITIZE_NUMBER_INT);
    if($_GET["func"] == 'cadastrar'){
      $action = "modules/financeiro/controlers/movCategoriaControl.php?func=insertMovCategoria"; 
    } elseif($_GET["func"] == 'editar'){
      if(isset($_GET['cat_id']) && !empty($_GET['cat_id'])){ 
        $cat_id = filter_input(INPUT_GET, "cat_id", FILTER_SANITIZE_NUMBER_INT);  
        $action = "modules/financeiro/controlers/movCategoriaControl.php?func=updateMovCategoria&cat_id={$cat_id}";
        $retorno = $cat->selectMovCategoria("*", "WHERE cat_id=?", array($cat_id));
      } else {
        header('Location: index.php?mod=financeiro&pag=categoria&func=visualizar');
      }
    }
?>
    <form method="post" action="<?php echo $action; ?>">
        <input type="hidden" name="cat_tipo" id="cat_tipo" value="<?php echo $cat_tipo; ?>" />
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="cat_nome">Nome</label>
                    <input type="text" class="form-control" name="cat_nome" id="cat_nome" tabindex="1" value="<?php echo isset($retorno) ? $retorno[0]['cat_nome'] : '';?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="cat_mov_tipo">Tipo de movimentação</label>
                    <select id="cat_mov_tipo" name="cat_mov_tipo" class="form-control" tabindex="2" required>
                        <?php
                        foreach($mov_tipo_nome as $key=>$value){
                          echo "<option value='$key'";
                          echo isset($retorno) && $retorno[0]["cat_mov_tipo"] == $key ? "selected" : "";
                          echo ">".ucfirst($value)."</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
        <input class="btn btn-secondary" type="reset" value="Limpar">
    </form>

<?php } elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar") { ?>        	
    
    <p><a class="btn  btn-primary" href="?mod=financeiro&pag=categoria&func=cadastrar" role="button">Cadastrar categoria</a> </p>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm datatable">
            <thead> 
                <tr> 
                    <th>#</th> 
                    <th>Nome</th>
                    <th>Tipo de movimentação</th>
                    <th></th> 
                </tr> 
            </thead> 
            <tbody> 
                <?php 
                $rows = $cat->selectMovCategoria("*", "", array());
                foreach($rows as $row){
                ?>
                    <tr> 
                        <th><?php echo $row['cat_id']; ?></th> 
                        <td><a href="?mod=financeiro&pag=categoria&func=editar&cat_id=<?php echo $row['cat_id']; ?>"><?php echo $row['cat_nome']; ?></a></td>
                        <td>
                        <?php
                        echo ucfirst($mov_tipo_nome[$row["cat_mov_tipo"]]);
						?>                  
                        </td>
                        <td><a href="modules/financeiro/controlers/movCategoriaControl.php?mod=<?php echo $_GET['mod']; ?>&pag=<?php echo $_GET['pag']; ?>&func=deleteMovCategoria&cat_id=<?php echo $row['cat_id']; ?>" class="btn btn-sm text-danger delete-confirm"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
                    </tr> 
                <?php }?>
            </tbody>
        </table>
    </div>
<?php
}
?>