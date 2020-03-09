<?php
if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){
    
    if($_GET["func"] == 'cadastrar'){
      $action = "modules/administracao/controlers/usuarioControl.php?mod={$_GET['mod']}&pag={$_GET['pag']}&func=insertUsuario"; 
    } elseif($_GET["func"] == 'editar'){
      if(isset($_GET['usu_id']) && !empty($_GET['usu_id'])){ 
        $usu_id = filter_input(INPUT_GET, "usu_id", FILTER_SANITIZE_NUMBER_INT);  
        $action = "modules/administracao/controlers/usuarioControl.php?mod={$_GET['mod']}&pag={$_GET['pag']}&func=updateUsuario&usu_id={$usu_id}"; 
        $usuario = new Usuario();
        $retorno = $usuario->selectUsuario("*", "WHERE usu_id=?", array($usu_id));
        $usu_modulos = unserialize($retorno[0]['usu_modulos']);
      } else {
        header('Location: index.php?mod=administracao&pag=usuario&func=visualizar');
      }
    }
?>
    <form method="post" action="<?php echo $action; ?>">
        <div class="form-row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="usu_nome">Nome</label>
                    <input type="text" class="form-control" name="usu_nome" id="usu_nome" value="<?php echo isset($retorno) ? $retorno[0]['usu_nome'] : '';?>" required>
                </div>
                <div class="form-group">
                    <label for="usu_senha">Senha</label>
                    <input type="password" class="form-control" name="usu_senha" id="usu_senha" <?php echo !isset($retorno) ? "required" : "";?>>
                    <?php echo isset($retorno) ? "<small id='usu_senha_text' class='form-text text-muted'>Mantenha vazio para não alterar a senha</small>" : '';?>
                </div>
                <div class="form-group">
                    <label for="usu_nivel">Nível de acesso</label>
                    <select id="usu_nivel" name="usu_nivel" class="form-control">
                    	<option value="0" <?php echo  isset($retorno) && $retorno[0]['usu_nivel'] == 0 ? "selected" : ""; ?>>Cadastrar</option>
                        <option value="1" <?php echo  isset($retorno) && $retorno[0]['usu_nivel'] == 1 ? "selected" : ""; ?>>Cadastrar/Editar</option>
                        <option value="2" <?php echo  isset($retorno) && $retorno[0]['usu_nivel'] == 2 ? "selected" : ""; ?>>Cadastrar/Editar/Excluir</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="usu_modulos">Módulos</label>
                    <dl>
                    	<?php
						foreach($mod_arr as $modulo){
							echo "<div class='d-inline-block px-3'><dt>{$modulo["modulo"]}</dt>";
                            foreach($modulo["pagina"] as $key=>$pagina){
                              if(!empty($pagina)) {
                                $check = "";
                                if(isset($usu_modulos[$modulo["modulo"]]) && in_array($pagina, $usu_modulos[$modulo["modulo"]])) $check = "checked";
                                echo "<dd class='form-check'><input type='checkbox' class='form-check-input' id='usu_modulos_{$key}' name='usu_modulos[]' value='{$pagina}' {$check} /><label class='form-check-label' for='usu_modulos_{$key}'>{$pagina}</label></dd>";
                              }
                            }
                            echo "</div>";	
						}
						?>
                    </dl>
                </div>
            </div>
        </div>
        <span>
            <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>">
            <input class="btn btn-secondary" type="reset" value="Limpar">
        </span>
    </form>
<?php
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar"){
?>        	
    <p><a class="btn  btn-primary" href="?mod=administracao&pag=usuario&func=cadastrar" role="button">Cadastrar usuário</a> </p>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm datatable">
            <thead> 
                <tr> 
                    <th>#</th> 
                    <th>Nome</th>
                    <th>Permissões</th>
                    <th></th> 
                </tr> 
            </thead> 
            <tbody> 
                <?php 
                $retorno = $usuario->selectUsuario("*", "", array());
                foreach($retorno as $row){
                    $modules = unserialize($row['usu_modulos']);
                ?>
                    <tr> 
                        <th><?php echo $row['usu_id']; ?></th> 
                        <td>
                            <a href="?mod=administracao&pag=usuario&func=editar&usu_id=<?php echo $row['usu_id']; ?>"><?php echo $row['usu_nome']; ?></a>
                        </td>
                        <td>
                          <ul class="list-unstyled">
                          <?php 
                          foreach($modules as $key=>$value){
                            echo "<li><b>Módulo \"{$key}\":</b> ".implode(" / ", $value)."</li>";
                          }
                          ?>
                          </ul>
                        </td>
                        <td><a href="modules/administracao/controlers/usuarioControl.php?mod=<?php echo $_GET['mod']; ?>&pag=<?php echo $_GET['pag']; ?>&func=deleteUsuario&usu_id=<?php echo $row['usu_id']; ?>" class="btn btn-sm text-danger delete-confirm"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
                    </tr> 
                <?php }?>
            </tbody>
        </table>
    </div>
<?php
}
?>