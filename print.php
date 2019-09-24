<?php
require_once("loader.php");
require_once("includes/loginCheck.php");
require_once("header.php");
?>
  <script type="text/javascript">
    window.onload = function() { window.print(); }
  </script>
    <div class="row">
      <div class="col">
        <?php
        $redirecionar = true;
        if(isset($_GET["mod"]) && isset($_GET["pag"])){
          $modulo = filter_input(INPUT_GET, "mod", FILTER_SANITIZE_SPECIAL_CHARS);
          $pagina = filter_input(INPUT_GET, "pag", FILTER_SANITIZE_SPECIAL_CHARS);
          $usuario = new Usuario();
          $mod_acess = $usuario->acessoModUsuario(array("modulo"=>$modulo, "pagina"=>$pagina), $_SESSION["usu_id"]);
          if(isset($mod_acess) && count($mod_acess) > 0){
          	if(isset($_GET["func"]) && $_GET["func"] == "imprimir"){
          	  require_once("modules/{$modulo}/includes/{$pagina}Print.php");
              $redirecionar = false;
            }
          } else {
            $_SESSION["result"] = array("erro"=>true, "msg"=>"Usuário não tem permissão de acesso à página \"{$pagina}\" do módulo \"{$modulo}\"");
          }
        }

        if($redirecionar) header("Location: index.php");
        ?>
      </div>
    </div>
  </body>
</html>