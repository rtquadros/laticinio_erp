<nav class="navbar navbar-expand-md navbar-light" tabindex="-1">
  <a class="navbar-brand h1" href="index.php">
  	<img class="d-inline-block align-top" src="<?php echo file_exists($config->getConfig('site_logo')) ? $config->getConfig('site_logo') : 'images/icons/ms-icon-70x70.png';?>" title="<?php echo $config->getConfig('site_name');?>" width="30" height="30">
  </a>

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
    <?php
    //Monta o menu de acordo com os módulos que o usuário tem acesso
    $usuario = new Usuario();
    foreach($mod_arr as $modulo){
      $mod_access = $usuario->acessoModUsuario($modulo, $_SESSION["usu_id"]);
      if($mod_access && count($mod_access) > 0){
    ?>
        <li class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <?php echo $modulo['menu_titulo'];?>
          </a>
          <div class="dropdown-menu" >
          <?php 
          foreach($modulo['pagina'] as $pagina_nome => $pagina_cod){
            if(in_array($pagina_cod, $mod_access) || $pagina_cod == ''){
              if($pagina_nome == 'separator') echo '<div role="separator" class="dropdown-divider"></div>';
              else echo "<a class='dropdown-item' href='?mod={$modulo["modulo"]}&pag={$pagina_cod}'>{$pagina_nome}</a>";
            }
          }?>
          </div>
        </li>
      <?php
      }
    }
    ?>
    </ul>
    <span class="navbar-text mr-3">Olá, <?php echo $_SESSION['loggedin']; ?></span>
    <form action="index.php" method="get" class="form-inline">
      <button class="btn btn-sm btn-outline-danger" type="submit"><span class="fas fa-power-off"></span> Logout</button>
      <input type="hidden" name="logout" value="true">
    </form>
  </div><!--/.nav-collapse -->
</nav>