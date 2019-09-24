<?php
if(isset($func) && $func == 'cadastrar'){
?>
    <form method="post" action="modules/comercial/process.php?mod=<?php echo $_GET['mod']; ?>&func=insertRelacionamento">
        <div class="row">
            <div class="col-md-4">
            	<div class="form-group">
                    <label for="rel_categoria">Categoria</label>
                    <select class="form-control" name="rel_categoria" id="rel_categoria">
                        <option value="cliente">Cliente</option>
                        <option value="fornecedor">Fornecedor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rel_nome">Nome / Razão Social</label>
                    <input type="text" class="form-control" name="rel_nome" id="rel_nome" placeholder="Nome / Razão Social" required>
                </div>
                <div class="form-group">
                    <label for="rel_apelido">Apelido / Nome Fantasia</label>
                    <input type="text" class="form-control" name="rel_apelido" id="rel_apelido" placeholder="Apelido / Nome Fantasia">
                </div>
            </div>
            <div class="col-md-4">
            	<div class="row">
                	<div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_documento">CPF / CNPJ</label>
                            <input type="number" class="form-control" name="rel_documento" id="rel_documento" placeholder="000000000" >
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_inscricao">Insc. Estadual</label>
                            <input type="number" class="form-control" name="rel_inscricao" id="rel_inscricao" placeholder="000000000">
                        </div>
                    </div>
                </div>
                <div class="row">
                	<div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_email">E-mail</label>
                            <input type="email" class="form-control" name="rel_email" id="rel_email" placeholder="contato@email.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_tel">Telefone</label>
                            <input type="tel" class="form-control telefone" name="rel_tel" id="rel_tel" placeholder="(00) 00000-0000">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="rel_endereco">Endereço</label>
                    <input type="text" class="form-control" name="rel_endereco" id="rel_endereco" placeholder="Rua tal, nº01">
                </div>
            </div>
            <div class="col-md-4">
            	<div class="row">
                	<div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_bairro">Bairro</label>
                            <input type="text" class="form-control" name="rel_bairro" id="rel_bairro" placeholder="Bairro">
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_cep">CEP</label>
                            <input type="text" class="form-control cep" name="rel_cep" id="rel_cep" placeholder="00000-000">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="rel_municipio">Município</label>
                            <input type="text" class="form-control" name="rel_municipio" id="rel_municipio" placeholder="Município">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="rel_estado">Estado</label>
                            <select class="form-control" name="rel_estado" id="rel_estado">
                            	<?php 
								$estados = array('AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO');
								foreach($estados as $estado){
									echo '<option value="'.$estado.'"';
									echo $estado == 'BA' ? ' selected' : '';
									echo '>'.$estado.'</option>';
								}?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="rel_desc">Observações</label>
                    <textarea class="form-control" rows="1" name="rel_desc" id="rel_desc">
                    </textarea>
                </div>
            </div>
        </div>
        <span class="pull-right">
            <input class="btn btn-success" type="submit" value="Cadastrar">
            <input class="btn btn-default" type="reset" value="Limpar">
        </span>
    </form>
<?php
} elseif(isset($func) && $func == 'editar'){
	
	if(isset($_GET['rel_id']) && !empty($_GET['rel_id'])){	
		$result = $relacionamento->getRelacionamento(array('rel_id'=>$_GET['rel_id']));
		$var = unserialize($result[0]['rel_variaveis']);
	} else {
		header('Location: index.php?mod=captacao_produtor&func=listar');
	}
?>
     <form method="post" action="modules/comercial/process.php?mod=<?php echo $_GET['mod']; ?>&func=updateRelacionamento">
        <div class="row">
            <div class="col-md-4">
            	<div class="form-group">
                    <label for="rel_categoria">Categoria</label>
                    <select class="form-control" name="rel_categoria" id="rel_categoria">
                        <option value="cliente">Cliente</option>
                        <option value="fornecedor">Fornecedor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rel_nome">Nome / Razão Social</label>
                    <input type="text" class="form-control" name="rel_nome" id="rel_nome" placeholder="Nome / Razão Social" value="<?php echo $result[0]['rel_nome']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="rel_apelido">Apelido / Nome Fantasia</label>
                    <input type="text" class="form-control" name="rel_apelido" id="rel_apelido" placeholder="Apelido / Nome Fantasia" value="<?php echo $result[0]['rel_apelido']; ?>">
                </div>
            </div>
            <div class="col-md-4">
            	<div class="row">
                	<div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_documento">CPF / CNPJ</label>
                            <input type="number" class="form-control" name="rel_documento" id="rel_documento" placeholder="000000000" value="<?php echo $result[0]['rel_documetno']; ?>" >
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_inscricao">Insc. Estadual</label>
                            <input type="number" class="form-control" name="rel_inscricao" id="rel_inscricao" placeholder="000000000" value="<?php echo $result[0]['rel_inscricao']; ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                	<div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_email">E-mail</label>
                            <input type="email" class="form-control" name="rel_email" id="rel_email" placeholder="contato@email.com" value="<?php echo $result[0]['rel_email']; ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_tel">Telefone</label>
                            <input type="tel" class="form-control telefone" name="rel_tel" id="rel_tel" placeholder="(00) 00000-0000" value="<?php echo $result[0]['rel_tel']; ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="rel_endereco">Endereço</label>
                    <input type="text" class="form-control" name="rel_endereco" id="rel_endereco" placeholder="Rua tal, nº01" value="<?php echo $result[0]['rel_endereco']; ?>">
                </div>
            </div>
            <div class="col-md-4">
            	<div class="row">
                	<div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_bairro">Bairro</label>
                            <input type="text" class="form-control" name="rel_bairro" id="rel_bairro" placeholder="Bairro" value="<?php echo $result[0]['rel_bairro']; ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<div class="form-group">
                            <label for="rel_cep">CEP</label>
                            <input type="text" class="form-control cep" name="rel_cep" id="rel_cep" placeholder="00000-000" value="<?php echo $result[0]['rel_cep']; ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="rel_municipio">Município</label>
                            <input type="text" class="form-control" name="rel_municipio" id="rel_municipio" placeholder="Município" value="<?php echo $result[0]['rel_municipio']; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="rel_estado">Estado</label>
                            <select class="form-control" name="rel_estado" id="rel_estado">
                            	<?php 
								$estados = array('AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO');
								foreach($estados as $estado){
									echo '<option value="'.$estado.'"';
									echo $estado ==  $result[0]['rel_estado'] ? ' selected' : '';
									echo '>'.$estado.'</option>';
								}?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="rel_desc">Observações</label>
                    <textarea class="form-control" rows="1" name="rel_desc" id="rel_desc"><?php echo $result[0]['rel_desc']; ?>
                    </textarea>
                </div>
            </div>
        </div>
        <span class="pull-right">
            <input class="btn btn-success update-confirm" type="submit" value="Editar">
            <input class="btn btn-default" type="reset" value="Limpar">
        </span>
        <input type="hidden" name="rel_id" value="<?php echo $_GET['rel_id']; ?>" />
    </form>
<?php	
} else{
?>        	
    <p><a class="btn  btn-primary" href="?mod=comercial_relacionamento&func=cadastrar" role="button">Cadastrar relacionamento</a> </p>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm datatable">
            <thead> 
                <tr> 
                    <th>#</th> 
                    <th>Nome/Razão Social</th>
                    <th>Apelido/Nome Fantasia</th> 
                    <th>CPF/CNPJ</th>
                    <th>E-mail</th> 
                    <th>Telefone</th>
                    <th>Descrição</th>
                    <th></th> 
                </tr> 
            </thead> 
            <tbody> 
                <?php 
                $rows = $relacionamento->getRelacionamento(array('rel_categoria'=>array('"cliente"', '"fornecedor"')));
                foreach($rows as $row){
                ?>
                    <tr> 
                        <td><?php echo $row['rel_id']; ?></td> 
                        <td><a href="?mod=comercial_relacionamento&func=editar&rel_id=<?php echo $row['rel_id']; ?>"><?php echo $row['rel_nome']; ?></a></td> 
                        <td><?php echo $row['rel_apelido']; ?></td>
                        <td><?php echo $row['rel_documento']; ?></td>
                        <td><?php echo $row['rel_email']; ?></td>
                        <td><?php echo $row['rel_tel']; ?></td>
                        <td><?php echo $row['rel_desc']; ?></td>
                        <td><a href="modules/comercial/process.php?mod=<?php echo $_GET['mod']; ?>&func=deleteRelacionamento&rel_id=<?php echo $row['rel_id']; ?>" class="btn btn-xs text-danger delete-confirm"><span class="glyphicon glyphicon-trash"></span><span class="sr-only">Excluir</span></a></td>
                    </tr> 
                <?php }?>
            </tbody>
        </table>
    </div>
<?php
}
?>