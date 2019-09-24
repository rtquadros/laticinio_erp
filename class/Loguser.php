<?php
require_once("ConnDB.php");

class Loguser extends ConnDB {
 
  //table fields
  private $user_table = 'usuarios';          //Users table name
  private $user_column = 'usu_nome';     //USERNAME column (value MUST be valid email)
  private $pass_column = 'usu_senha';      //PASSWORD column
  private $user_level = 'usu_nivel';      //(optional) userlevel column
  private $user_modules = '';
  private $encrypt = true;       //set to true to use md5 encryption for the password
	
  //login function
  public function login($username, $password){
		
    //check if encryption is used
    if($this->encrypt == true){
      $password = md5($password);
    }
		
    if($this->tabelaExiste($this->user_table)){
      $result = $this->selectDb("*", $this->user_table, "WHERE {$this->user_column}=? AND {$this->pass_column} = ?", array($username, $password));
      $row = $result->fetch(PDO::FETCH_ASSOC);
      if($row != "Error"){
        if($row[$this->user_column] !="" && $row[$this->pass_column] !=""){
		  // Registra a sessão
          $_SESSION['loggedin'] = $row[$this->user_column];
          $_SESSION['usu_id'] = $row['usu_id'];
          $_SESSION['enterTime'] = time();
          //$_SESSION['userlevel'] = $row[$this->user_level];
          return true;
        } else
	      session_destroy();
      }
      return false;
    }
  }
 
  //logout function
  public function logout(){
    session_destroy();
    return;
  }
 
  //check if loggedin
  public function logincheck($logincode, $logintime){
    $timeDiffernce = time() - $logintime;
    if ($timeDiffernce > 86400){
      $this->logout();
    } else {
      $result = $this->selectDb("*", $this->user_table, "WHERE {$this->user_column} = ?", array($logincode));
      //return true if logged in and false if not
      if($result){
        $row = $result->fetchAll();
        $rownum = count($row);
        if($rownum > 0){
          //Seta os modules que o usuário tem acesso
          $this->user_modules = $row[0]['usu_modulos'];
          return true;
        }
      }
      return false;
    }
  }
}