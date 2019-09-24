<?php
require_once("loader.php");

if(isset($_SESSION['loggedin']) && !empty($_SESSION['loggedin'])) header('location: index.php');

require_once("header.php");
?>
  <div class="row">
    <div class="col-sm">
    <?php
    if(isset($_SESSION['result']) && !empty($_SESSION['result'])){ 
      $result = $_SESSION['result'];
      if($result['erro']){
        echo '<div class="alert alert-danger" role="alert">'.$result['msg'].'</div>';
      } else {
        echo '<div class="alert alert-success" role="alert">'.$result['msg'].'</div>';	
      }				
    }
    ?>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-4 mx-auto">
      <div id="loginbox" class="card">
        <div class="card-header">
          <h3 class="card-title">Faça login!</h3>
        </div>
        <div class="card-body">
          <form method="post" action="controlers/logControl.php">
            <div class="form-group">
              <label for="usu_nome">Usuário</label>
              <input type="text" class="form-control" name="usu_nome" id="usu_nome" placeholder="Email">
            </div>
            <div class="form-group">
              <label for="usu_senha">Senha</label>
              <input type="password" class="form-control" name="usu_senha" id="usu_senha" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php
require_once("footer.php");
?>