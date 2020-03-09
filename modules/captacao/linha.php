<?php
if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){
    
    if($_GET["func"] == 'cadastrar'){
      $action = "modules/captacao/controlers/linhaControl.php?mod={$_GET['mod']}&pag={$_GET['pag']}&func=insertLinha"; 
    } elseif($_GET["func"] == 'editar'){
      if(isset($_GET['linha_id']) && !empty($_GET['linha_id'])){ 
        $linha_id = filter_input(INPUT_GET, "linha_id", FILTER_SANITIZE_NUMBER_INT);  
        $action = "modules/captacao/controlers/linhaControl.php?mod={$_GET['mod']}&pag={$_GET['pag']}&func=updateLinha&linha_id={$linha_id}"; 
        $linha = new Linha();
        $retorno = $linha->selectLinha("*", "WHERE linha_id=?", array($linha_id));
      } else {
        header('Location: index.php?mod=captacao&pag=linha&func=visualizar');
      }
    }
?>
    <form method="post" action="<?php echo $action; ?>">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="linha_nome">Nome</label>
                    <input type="text" class="form-control " name="linha_nome" id="linha_nome" placeholder="Nome da linha" value="<?php echo isset($retorno) ? $retorno[0]['linha_nome'] : '';?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <div class="form-group">
                        <label for="linha_carreteiro">Carreteiro</label>
                        <select class="form-control " name="linha_carreteiro" id="linha_carreteiro" required>
                            <option disabled selected >Selecione o carreteiro</option>
                            <?php
                            $pessoa = new Pessoa();
                            $rows = $pessoa->selectPessoa("*", "ORDER BY pessoa_nome ASC", array());
                            foreach($rows as $row){
                              echo "<option value='{$row["pessoa_id"]}'";
                              echo isset($retorno) && $retorno[0]["linha_carreteiro"] == $row["pessoa_id"] ? " selected " : '';
                              echo ">{$row['pessoa_nome']}</option>";
                            }?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="linha_comissao">Comissão/Lt</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                        <input type="text" class="form-control  comissao" name="linha_comissao" id="linha_comissao" placeholder="0,00" value="<?php echo isset($retorno) ? $retorno[0]['linha_comissao'] : '';?>" required>
                    </div>
                </div>
            </div>
        </div>
        <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
        <input class="btn btn-secondary" type="reset" value="Limpar">
    </form>
<?php
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar"){
?>        
	
    <p><a class="btn  btn-primary" href="?mod=captacao&pag=linha&func=cadastrar" role="button">Cadastrar linha</a> </p>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm datatable">
            <thead> 
                <tr> 
                    <th>#</th> 
                    <th>Nome</th> 
                    <th>Carreteiro</th> 
                    <th>Comissão/Lt</th>
                    <th></th> 
                </tr> 
            </thead> 
            <tbody> 
                <?php 
                $linha = new Linha();
                $rows = $linha->selectLinha("*", "", array());
                $pessoa = new Pessoa();
                foreach($rows as $row){
                ?>
                    <tr> 
                        <th scope="row"><?php echo $row['linha_id']; ?></th> 
                        <td><a href="?mod=captacao&pag=linha&func=editar&linha_id=<?php echo $row['linha_id']; ?>"><?php echo $row['linha_nome']; ?></a></td>
                        <td>
                        <?php 
                        if($row['linha_carreteiro'] != 0) 
                          echo $pessoa->getPessoaNome($row['linha_carreteiro']); 
                        ?>
                        </td>
                        <td><?php echo 'R$ '.number_format($row['linha_comissao'], 3, ',', '.');?></td>
                        <td><a href="modules/captacao/controlers/linhaControl.php?mod=<?php echo $_GET['mod']; ?>&pag=<?php echo $_GET['pag']; ?>&func=deleteLinha&linha_id=<?php echo $row['linha_id']; ?>" class="btn btn-sm text-danger delete-confirm"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
                    </tr> 
                <?php }?>
            </tbody>
        </table>
    </div>
<?php
}
?>