<?php
$pessoa = new Pessoa();

if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){
    if($_GET["func"] == 'cadastrar'){
      $action = "modules/pessoas/controlers/colaboradorControl.php?pag=colaborador&func=insertPessoa"; 
    } elseif($_GET["func"] == 'editar'){
      if(isset($_GET['pessoa_id']) && !empty($_GET['pessoa_id'])){ 
        $pessoa_id = filter_input(INPUT_GET, "pessoa_id", FILTER_SANITIZE_NUMBER_INT);  
        $action = "modules/pessoas/controlers/colaboradorControl.php?pag=colaborador&func=updatePessoa&pessoa_id={$pessoa_id}";
        $retorno = $pessoa->selectPessoa("*", "WHERE pessoa_id=?", array($pessoa_id));
        $pessoa_variaveis = unserialize($retorno[0]["pessoa_variaveis"]); 
      } else {
        header('Location: index.php?mod=pessoas&pag=colaborador&func=visualizar');
      }
    }
?>
  <form method="post" action="<?php echo $action; ?>">
    <div class="form-row">
        <div class="col">
            <div class="form-group">
                <label for="pessoa_nome">Nome</label>
                <input type="text" class="form-control" name="pessoa_nome" id="pessoa_nome" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_nome'] : ""; ?>" tabindex="1" required>
            </div>
            <div class="form-row">
              <div class="col">
                  <div class="form-group">
                        <label for="pessoa_documento">CPF</label>
                        <input type="number" class="form-control" name="pessoa_documento" id="pessoa_documento" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_documento'] : ""; ?>" maxlength="11" tabindex="2">
                    </div>
                </div>
                <div class="col">
                  <div class="form-group">
                        <label for="pessoa_inscricao">CTPS</label>
                        <input type="text" class="form-control" name="pessoa_inscricao" id="pessoa_inscricao" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_inscricao'] : ""; ?>" maxlength="11" tabindex="3">
                    </div>
                </div>
            </div>
            <div class="form-row">
              <div class="col">
                    <div class="form-group">
                        <label for="data_admissao">Data de admissão</label>
                        <div class="input-group">
                            <input type="text" class="form-control datepicker" name="data_admissao" id="data_admissao" value="<?php echo  isset($retorno) ? date("d/m/Y", strtotime($pessoa_variaveis['data_admissao'])) : ""; ?>" tabindex="4" required>
                            <div class="input-group-append">
                              <span class="input-group-text"><span class="fas fa-th"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                  <div class="form-group">
                        <label for="cargo">Cargo</label>
                        <input type="text" class="form-control" name="cargo" id="cargo" value="<?php echo isset($retorno) ? $pessoa_variaveis['cargo'] : ""; ?>" placeholder="Quejeiro" tabindex="5">
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="form-row">
              <div class="col">
                  <div class="form-group">
                        <label for="salario">Salário</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                          </div>
                          <input type="text" class="form-control dinheiro" name="salario" id="salario" value="<?php echo isset($retorno) ? $pessoa_variaveis['salario'] : ""; ?>" placeholder="00,00" tabindex="6">
                        </div>
                    </div>
                </div>
                <div class="col">
                  <div class="form-group">
                        <label for="bonificacao">Bonificação</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                          </div>
                          <input type="text" class="form-control dinheiro" name="bonificacao" id="bonificacao" value="<?php echo isset($retorno) ? $pessoa_variaveis['bonificacao'] : ""; ?>" placeholder="00,00" tabindex="7">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
              <div class="col">
                <div class="form-group">
                      <label for="pessoa_tel">Telefone</label>
                      <input type="tel" class="form-control telefone" name="pessoa_tel" id="pessoa_tel" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_tel'] : ""; ?>" placeholder="(00) 00000-0000" tabindex="9">
                  </div>
              </div>
              <div class="col">
                <div class="form-group">
                      <label for="pessoa_cep">CEP</label>
                      <input type="text" class="form-control cep" name="pessoa_cep" id="pessoa_cep" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_cep'] : ""; ?>" placeholder="00000-000" tabindex="12">
                  </div>
              </div>
            </div>
            <div class="form-row">
              <div class="col">  
                <div class="form-group">
                  <label for="pessoa_endereco">Endereço</label>
                  <input type="text" class="form-control" name="pessoa_endereco" id="pessoa_endereco" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_endereco'] : ""; ?>" placeholder="Rua tal, nº01" tabindex="10">
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="pessoa_bairro">Bairro</label>
                  <input type="text" class="form-control" name="pessoa_bairro" id="pessoa_bairro" value="<?php echo isset($retorno) ? $retorno[0]['pessoa_bairro'] : ""; ?>" placeholder="Bairro" tabindex="11">
                </div>
              </div>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="pessoa_desc">Observações</label>
                <textarea class="form-control" rows="5" name="pessoa_desc" id="pessoa_desc" tabindex="13">
                 <?php echo isset($retorno) ? $retorno[0]['pessoa_desc'] : ""; ?>
                </textarea>
            </div>
            <div class="form-check">
              <input type="checkbox" class="form-check-input" name="vendedor_comissionado" id="vendedor_comissionado" value="1" <?php echo isset($pessoa_variaveis['vendedor_comissionado']) && $pessoa_variaveis['vendedor_comissionado'] ? "checked" : ""; ?>>
              <label for="vendedor_comissionado" class="form-check-label">Vendedor comissionado</label>
            </div>
        </div>
    </div>
    <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
    <input class="btn btn-secondary" type="reset" value="Limpar">
    <input type="hidden" name="pessoa_categoria" value="funcionario" />
    <input type="hidden" name="pessoa_municipio" value="Amargosa" />
    <input type="hidden" name="pessoa_estado" value="BA" />
    <input type="hidden" name="pessoa_apelido" value="" />
  </form>
<?php 
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar") {
?> 
  <p><a class="btn  btn-primary" href="?mod=pessoas&pag=colaborador&func=cadastrar" role="button">Cadastrar colaborador</a></p>
  <div class="table-responsive">
    <table class="table table-striped table-bordered table-sm datatable">
      <thead> 
          <tr> 
            <th>#</th> 
            <th>Nome</th> 
            <th>CPF</th> 
            <th>CTPS</th>
            <th>Cargo</th>
            <th>Data de admissão</th> 
            <th>Salário</th>
            <th>Bonificação</th>
            <th></th>
          </tr> 
      </thead> 
      <tbody> 
        <?php 
        $rows = $pessoa->selectPessoa("*", "WHERE pessoa_categoria=? ORDER BY pessoa_nome ASC", array("funcionario"));
        foreach($rows as $row){
            $pessoa_variaveis = unserialize($row['pessoa_variaveis']);
        ?>
          <tr> 
              <th scope="row"><?php echo $row['pessoa_id']; ?></th> 
              <td><a href="?mod=pessoas&pag=colaborador&func=editar&pessoa_id=<?php echo $row['pessoa_id']; ?>"><?php echo $row['pessoa_nome']; ?></a></td>
              <td><?php echo $row['pessoa_documento']; ?></td>
              <td><?php echo $row['pessoa_inscricao']; ?></td>
              <td><?php echo isset($pessoa_variaveis['cargo']) ? $pessoa_variaveis['cargo'] : '';?></td>
              <td><?php echo isset($pessoa_variaveis['data_admissao']) ? date("d/m/Y", strtotime($pessoa_variaveis['data_admissao'])) : '';?></td>  
              <td><?php echo isset($pessoa_variaveis['salario']) && !empty($pessoa_variaveis['salario']) ? 'R$ '.number_format($pessoa_variaveis["salario"], 2, ',', '.') : '';?></td>
              <td><?php echo isset($pessoa_variaveis['bonificacao']) && !empty($pessoa_variaveis['bonificacao']) ? 'R$ '.number_format($pessoa_variaveis["bonificacao"], 2, ',', '.') : '';?></td>
              <td><a href="modules/pessoas/controlers/colaboradorControl.php?func=deletePessoa&pessoa_id=<?php echo $row['pessoa_id']; ?>" class="text-danger delete-confirm"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
          </tr> 
        <?php }?>
      </tbody>
    </table>
  </div>
<?php }?>