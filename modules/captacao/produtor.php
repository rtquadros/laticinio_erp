<?php
if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){
    
    if($_GET["func"] == 'cadastrar'){
      $action = "modules/captacao/controlers/produtorControl.php?mod={$_GET['mod']}&pag={$_GET['pag']}&func=insertProdutor"; 
    } elseif($_GET["func"] == 'editar'){
      if(isset($_GET['pessoa_id']) && !empty($_GET['pessoa_id'])){ 
        $pessoa_id = filter_input(INPUT_GET, "pessoa_id", FILTER_SANITIZE_NUMBER_INT);  
        $action = "modules/captacao/controlers/produtorControl.php?mod={$_GET['mod']}&pag={$_GET['pag']}&func=updateProdutor&pessoa_id={$pessoa_id}"; 
        $produtor = new Produtor();
        $retorno = $produtor->selectProdutor("*", "WHERE pessoa_id=?", array($pessoa_id));
        $var = unserialize($retorno[0]['pessoa_variaveis']);
      } else {
        header('Location: index.php?mod=captacao&pag=produtor&func=visualizar');
      }
    }
?>
    <form method="post" action="<?php echo $action;?>">
        <div class="form-row">
            <div class="col">
                <div class="form-group">
                    <label for="pessoa_nome">Nome / Razão Social</label>
                    <input type="text" class="form-control " name="pessoa_nome" id="pessoa_nome" placeholder="Nome / Razão Social" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_nome'] : '';?>" required>
                </div>
                <div class="form-group">
                    <label for="pessoa_apelido">Apelido / Nome Fantasia</label>
                    <input type="text" class="form-control " name="pessoa_apelido" id="pessoa_apelido" placeholder="Apelido / Nome Fantasia" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_apelido'] : '';?>">
                </div>
                <div class="form-row">
                	<div class="col">
                    	<div class="form-group">
                            <label for="pessoa_documento">CPF / CNPJ</label>
                            <input type="number" class="form-control " name="pessoa_documento" id="pessoa_documento" placeholder="000000000" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_documento'] : '';?>" >
                        </div>
                    </div>
                    <div class="col">
                    	<div class="form-group">
                            <label for="pessoa_inscricao">Insc. Estadual</label>
                            <input type="number" class="form-control " name="pessoa_inscricao" id="pessoa_inscricao" placeholder="000000000" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_inscricao'] : '';?>">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                	<div class="col">
                    	<div class="form-group">
                            <label for="pessoa_email">E-mail</label>
                            <input type="email" class="form-control " name="pessoa_email" id="pessoa_email" placeholder="contato@email.com" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_email'] : '';?>">
                        </div>
                    </div>
                    <div class="col">
                    	<div class="form-group">
                            <label for="pessoa_tel">Telefone</label>
                            <input type="tel" class="form-control  telefone" name="pessoa_tel" id="pessoa_tel" placeholder="(00) 00000-0000" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_telefone'] : '';?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="pessoa_variaveis">Preço do leite</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                        <input type="text" class="form-control dinheiro" name="preco_leite" id="preco_leite" placeholder="00,00" value="<?php echo isset($var) ? $var['preco_leite'] : '';?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pessoa_endereco">Endereço</label>
                    <input type="text" class="form-control " name="pessoa_endereco" id="pessoa_endereco" placeholder="Rua tal, nº01" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_endereco'] : '';?>">
                </div>
                <div class="form-row">
                	<div class="col">
                    	<div class="form-group">
                            <label for="pessoa_bairro">Bairro</label>
                            <input type="text" class="form-control " name="pessoa_bairro" id="pessoa_bairro" placeholder="Bairro" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_bairro'] : '';?>">
                        </div>
                    </div>
                    <div class="col">
                    	<div class="form-group">
                            <label for="pessoa_cep">CEP</label>
                            <input type="text" class="form-control  cep" name="pessoa_cep" id="pessoa_cep" placeholder="00000-000" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_cep'] : '';?>">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="pessoa_municipio">Município</label>
                            <input type="text" class="form-control " name="pessoa_municipio" id="pessoa_municipio" placeholder="Município" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_municipio'] : '';?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="pessoa_estado">Estado</label>
                            <select class="form-control " name="pessoa_estado" id="pessoa_estado">
                            	<?php 
								$estados = array('AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO');
								foreach($estados as $estado){
									echo "<option value='{$estado}'"; 
                                    echo isset($retorno) && $retorno[0]["pessoa_estado"] == $estado ? " selected " : '';
                                    echo ">{$estado}</option>";
								}?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="pessoa_variaveis">Linha de coleta</label>
                    <select class="form-control " name="linha_coleta" id="linha_coleta" required>
                    	<?php
						$linha = new Linha();
						$rows = $linha->selectLinha("*", "", array());
						foreach($rows as $row){
                          echo "<option value='{$row["linha_id"]}'";
                          echo isset($var) && $var["linha_coleta"] == $row["linha_id"] ? " selected " : '';
                          echo ">{$row['linha_nome']}</option>";
						}?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="pessoa_desc">Observações</label>
                    <textarea class="form-control " rows="5" name="pessoa_desc" id="pessoa_desc">
                      <?php echo isset($retorno) ? $retorno[0]['pessoa_desc'] : '';?>
                    </textarea>
                </div>
            </div>
        </div>
        <span>
            <input class="btn btn-success" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
            <input class="btn btn-secondary" type="reset" value="Limpar">
        </span>
        <input type="hidden" name="pessoa_categoria" value="produtor" />
    </form>
<?php
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar"){
?>        	
    <p><a class="btn  btn-primary" href="?mod=captacao&pag=produtor&func=cadastrar" role="button">Cadastrar produtor</a></p>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-striped table-bordered table-sm datatable">
                <thead> 
                    <tr> 
                        <th>#</th> 
                        <th>Nome</th> 
                        <th>Documento</th> 
                        <th>Insc. Estadual</th>
                        <th>Preço do leite</th> 
                        <th>Linha de coleta</th>
                        <th></th> 
                    </tr> 
                </thead> 
                <tbody> 
                    <?php 
                    $produtor = new Produtor();
                    $rows = $produtor->selectProdutor("*", "WHERE pessoa_categoria=?", array('produtor'));
                    foreach($rows as $row){
                        $var = unserialize($row['pessoa_variaveis']);
                    ?>
                        <tr> 
                            <th scope="row"><?php echo $row['pessoa_id']; ?></th> 
                            <td><a href="?mod=captacao&pag=produtor&func=editar&pessoa_id=<?php echo $row['pessoa_id']; ?>"><?php echo $row['pessoa_nome']; ?></a></td>
                            <td><?php echo $row['pessoa_documento']; ?></td>
                            <td><?php echo $row['pessoa_inscricao']; ?></td>
                            <td><?php echo "R$ {$produtor->getPrecoProdutor($row['pessoa_id'])}";?></td>  
                            <td>
                            <?php 
                            $linha = new Linha();
                            echo $linha->getLinhaNome($var['linha_coleta']);
                            ?>
                            </td>
                            <td><a href="modules/captacao/controlers/linhaControl.php?mod=<?php echo $_GET['mod']; ?>&pag=<?php echo $_GET['pag']; ?>&funcao=deleteProdutor&pessoa_id=<?php echo $row['pessoa_id']; ?>" class="btn btn-sm text-danger delete-confirm"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
                        </tr> 
                    <?php }?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}
?>