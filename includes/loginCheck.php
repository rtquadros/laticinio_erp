<?php
// Verifica a sessão de login
$loguser = new Loguser();
if(isset($_GET["logout"]) && $_GET["logout"] == true){ 
  $loguser->logout();
  header("Location: login.php");
}
if(isset($_SESSION["loggedin"]) && !empty($_SESSION["loggedin"])){
  if(!$loguser->logincheck($_SESSION["loggedin"], $_SESSION["enterTime"])){
    $loguser->logout();
    header("Location: login.php");
  }
} else {
  header("Location: login.php");  
}