<?php
$pessoa = new Pessoa();

if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){
    if($_GET["func"] == 'cadastrar'){
      $action = "modules/pessoas/controlers/relacionamentoControl.php?func=insertPessoa"; 
    } elseif($_GET["func"] == 'editar'){
      if(isset($_GET['pessoa_id']) && !empty($_GET['pessoa_id'])){ 
        $pessoa_id = filter_input(INPUT_GET, "pessoa_id", FILTER_SANITIZE_NUMBER_INT);  
        $action = "modules/pessoas/controlers/relacionamentoControl.php?func=updatePessoa&pessoa_id={$pessoa_id}";
        $retorno = $pessoa->selectPessoa("*", "WHERE pessoa_id=?", array($pessoa_id));
        $pessoa_variaveis = unserialize($retorno[0]["pessoa_variaveis"]); 
      } else {
        header('Location: index.php?mod=pessoas&pag=relacionamento&func=visualizar');
      }
    }
?>
     <form method="post" action="<?php echo $action; ?>">
        <div class="form-row">
            <div class="col-md-4">
            	<div class="form-group">
                    <label for="pessoa_categoria">Categoria</label>
                    <select class="form-control" name="pessoa_categoria" id="pessoa_categoria">
                        <option value="cliente" <?php echo isset($retorno) && $retorno[0]["pessoa_categoria"] == "cliente" ? "selected" : ""; ?>>Cliente</option>
                        <option value="fornecedor" <?php echo isset($retorno) && $retorno[0]["pessoa_categoria"] == "fornecedor" ? "selected" : ""; ?>>Fornecedor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="pessoa_nome">Nome / Razão Social</label>
                    <input type="text" class="form-control" name="pessoa_nome" id="pessoa_nome" placeholder="Nome / Razão Social" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_nome'] : ""; ?>" required>
                </div>
                <div class="form-group">
                    <label for="pessoa_apelido">Apelido / Nome Fantasia</label>
                    <input type="text" class="form-control" name="pessoa_apelido" id="pessoa_apelido" placeholder="Apelido / Nome Fantasia" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_apelido'] : ""; ?>">
                </div>
            </div>
            <div class="col-md-4">
            	<div class="form-row">
                	<div class="col-md-6">
                    	<div class="form-group">
                            <label for="pessoa_documento">CPF / CNPJ</label>
                            <input type="number" class="form-control" name="pessoa_documento" id="pessoa_documento" placeholder="000000000" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_documento'] : ""; ?>" >
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<div class="form-group">
                            <label for="pessoa_inscricao">Insc. Estadual</label>
                            <input type="number" class="form-control" name="pessoa_inscricao" id="pessoa_inscricao" placeholder="000000000" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_inscricao'] : ""; ?>">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                	<div class="col-md-6">
                    	<div class="form-group">
                            <label for="pessoa_email">E-mail</label>
                            <input type="email" class="form-control" name="pessoa_email" id="pessoa_email" placeholder="contato@email.com" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_email'] : ""; ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<div class="form-group">
                            <label for="pessoa_tel">Telefone</label>
                            <input type="tel" class="form-control telefone" name="pessoa_tel" id="pessoa_tel" placeholder="(00) 00000-0000" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_tel'] : ""; ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pessoa_endereco">Endereço</label>
                    <input type="text" class="form-control" name="pessoa_endereco" id="pessoa_endereco" placeholder="Rua tal, nº01" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_endereco'] : ""; ?>">
                </div>
            </div>
            <div class="col-md-4">
            	<div class="form-row">
                	<div class="col-md-6">
                    	<div class="form-group">
                            <label for="pessoa_bairro">Bairro</label>
                            <input type="text" class="form-control" name="pessoa_bairro" id="pessoa_bairro" placeholder="Bairro" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_bairro'] : ""; ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<div class="form-group">
                            <label for="pessoa_cep">CEP</label>
                            <input type="text" class="form-control cep" name="pessoa_cep" id="pessoa_cep" placeholder="00000-000" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_cep'] : ""; ?>">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="pessoa_municipio">Município</label>
                            <input type="text" class="form-control" name="pessoa_municipio" id="pessoa_municipio" placeholder="Município" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_municipio'] : ""; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pessoa_estado">Estado</label>
                            <select class="form-control" name="pessoa_estado" id="pessoa_estado">
                            	<?php 
								$estados = array('AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO');
								foreach($estados as $estado){
									echo '<option value="'.$estado.'"';
									echo  isset($retorno) && $estado ==  $retorno[0]['pessoa_estado'] ? ' selected' : '';
									echo '>'.$estado.'</option>';
								}?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pessoa_desc">Observações</label>
                    <textarea class="form-control" rows="1" name="pessoa_desc" id="pessoa_desc"><?php echo isset($retorno) ? $retorno[0]['pessoa_desc'] : ""; ?>
                    </textarea>
                </div>
            </div>
        </div>
        <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
        <input class="btn btn-secondary" type="reset" value="Limpar">
    </form>
<?php	
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar") {
?>        	
    <p><a class="btn  btn-primary" href="?mod=pessoas&pag=relacionamento&func=cadastrar" role="button">Cadastrar relacionamento</a></p>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm datatable">
            <thead> 
                <tr> 
                    <th>#</th> 
                    <th>Nome/Razão Social</th>
                    <th>Apelido/<br />Nome Fantasia</th> 
                    <th>CPF/CNPJ</th>
                    <th>E-mail</th> 
                    <th>Telefone</th>
                    <th></th> 
                </tr> 
            </thead> 
            <tbody> 
                <?php 
                $rows = $pessoa->selectPessoa("*", "WHERE pessoa_categoria IN (?, ?) ORDER BY pessoa_nome ASC", array("cliente", "fornecedor"));
                foreach($rows as $row){
                ?>
                    <tr> 
                        <td><?php echo $row['pessoa_id']; ?></td> 
                        <td><a href="?mod=pessoas&pag=relacionamento&func=editar&pessoa_id=<?php echo $row['pessoa_id']; ?>"><?php echo $row['pessoa_nome']; ?></a></td> 
                        <td><?php echo $row['pessoa_apelido']; ?></td>
                        <td><?php echo $row['pessoa_documento']; ?></td>
                        <td><?php echo $row['pessoa_email']; ?></td>
                        <td><?php echo $row['pessoa_tel']; ?></td>
                        <td><a href="modules/pessoas/controlers/relacionamentoControl.php?func=deletePessoa&pessoa_id=<?php echo $row['pessoa_id']; ?>" class="text-danger delete-confirm"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
                    </tr> 
                <?php }?>
            </tbody>
        </table>
    </div>
<?php
}
?>