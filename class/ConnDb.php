<?php

abstract class ConnDb{

  protected static $conn;
  protected $servername = HOSTNAME;
  protected $username = DB_USER;
  protected $password = DB_PASSWORD;
  protected $db = DB_NAME;
  protected $backupFolder = BACKUPFOLDER;
  protected $maxNumberFiles = MAXNUMBERFILES;
  private $crud;
  private $nParam;

  // Conectaca com o banco de dados
  protected function conectaDb(){
    
    try{
        $conn = new PDO("mysql:host={$servername};dbname={$db}", $username, $password);
        return $conn;
    } catch (PDOException $Erro){
    	return $Erro->getMessage();
    }

  }

  // Prepara as declarações PDO
  private function preparaDeclaracao($query, $param){
  	$this->contarParam($param);
    $this->crud = $this->conectaDb()->prepare($query);
    
    if($this->nParam > 0){
      for($i=1; $i<=$this->nParam; $i++){
        $this->crud->bindValue($i, $param[$i - 1]);
      }
    }
    
    $this->crud->execute();
  }

  // Contador de parâmetros
  private function contarParam($param){
  	$this->nParam = count($param);
  }

}

?>