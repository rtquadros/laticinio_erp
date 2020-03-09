<?php

abstract class ConnDb{

  protected $conn;
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
      $this->conn = new PDO("mysql:host={$this->servername};dbname={$this->db}", $this->username, $this->password);
      return $this->conn;
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

    //print_r($this->crud);
    //print_r($this->crud->errorInfo());
    //print_r($param);
  }

  // Contador de parâmetros
  private function contarParam($param){
  	$this->nParam = count($param);
  }

  // Checa se a tabela existe
  public function tabelaExiste($table){
    $result = $this->conectaDb()->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '{$this->db}' AND table_name = '{$table}' LIMIT 1;"); 
    
    if($result->fetchColumn() > 0)
      return true;
    else 
      return false;
  }

  // Select no DB
  public function selectDb($campos, $tabela, $condicoes, $param){
    $this->preparaDeclaracao("SELECT {$campos} FROM {$tabela} {$condicoes}", $param);
    return $this->crud;
  }

  // Insert no DB
  public function insertDb($tabela, $campos, $condicoes, $param){
    $campos = implode(",", $campos);
    $this->preparaDeclaracao("INSERT INTO {$tabela} ({$campos}) VALUES ({$condicoes})", $param);
    return $this->crud;
  }

  // Update no DB
  public function updateDb($tabela, $campos, $condicoes, $param){
    if(is_array($campos) && sizeof($campos) > 1) $campos = implode("=?,", $campos)."=?";
    else $campos = $campos."=?";
    $this->preparaDeclaracao("UPDATE {$tabela} SET {$campos} WHERE {$condicoes}", $param);
    return $this->crud;
  }

  // Delete no DB
  public function deleteDb($tabela, $condicoes, $param){
    $this->preparaDeclaracao("DELETE FROM {$tabela} WHERE ({$condicoes})", $param);
    return $this->crud;
  }

  // Último id inserido
  public function lastInsertId(){
    return $this->conn->lastInsertId();
  }

}

?>