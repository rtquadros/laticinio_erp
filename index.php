<?php
require_once("loader.php");
require_once("includes/loginCheck.php");
require_once("header.php");
require_once("navigation.php");
?>
<div id="main" class="row">
  <div class="col">
    <?php
    $path = "dashboard.php";
    if(isset($_GET["mod"]) && isset($_GET["pag"])){
      $modulo = filter_input(INPUT_GET, "mod", FILTER_SANITIZE_SPECIAL_CHARS);
      $pagina = filter_input(INPUT_GET, "pag", FILTER_SANITIZE_SPECIAL_CHARS);
      $usuario = new Usuario();
      $mod_acess = $usuario->acessoModUsuario(array("modulo"=>$modulo, "pagina"=>$pagina), $_SESSION["usu_id"]);
      if(isset($mod_acess) && count($mod_acess) > 0){
      	// AQUI FICA DEFINIDO AS FUNÇÕES POSSÍVEIS NO SISTEMA, ISSO DEVERIA ESTAR EM UM LUGAR MAIS APROPRIADO
      	$funcoes = array("cadastrar", "editar", "excluir", "visualizar", "imprimir");
      	if(isset($_GET["func"]) && in_array($_GET["func"], $funcoes)) $func = $_GET["func"]; else $func = "visualizar";
      	$path = "modules/{$modulo}/{$pagina}.php";
      	?>
      	  <nav aria-label="breadcrumb d-none d-md-block">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><?php echo ucfirst($modulo);?></li>
              <li class="breadcrumb-item"><a href="<?php echo "?mod={$modulo}&pag={$pagina}";?>"><?php echo ucfirst($pagina);?></a></li>
              <li class="breadcrumb-item active" aria-current="page"><?php echo ucfirst($func);?></li>
            </ol>
          </nav>
      	<?php
      } else {
        $_SESSION["result"] = array("erro"=>true, "msg"=>"Usuário não tem permissão de acesso à página \"{$pagina}\" do módulo \"{$modulo}\"");
      }
    } 
    
    if(isset($_SESSION["result"]) && !empty($_SESSION["result"])){ 
      $result = $_SESSION["result"];
      if($result["erro"]){
        echo "<div class='alert alert-danger' role='alert'>{$result["msg"]}</div>";
      } else {
        echo "<div class='alert alert-success' role='alert'>{$result["msg"]}</div>";	
      }				
    }
    
    require_once($path);
    ?>
  </div>
</div>
<?php
require_once("footer.php");
?>