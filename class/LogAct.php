<?php
require_once("ConnDb.php");

class LogAct extends ConnDb{
  
  private $campos = array("log_usuario_id", "log_data", "log_mod", "log_funcao", "log_objeto_id");

  public function selectLogAct($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "log_atividade", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertLogAct($result){
    $log_data = date('Y-m-d H:i:s');
    $param = array($_SESSION["usu_id"], $log_data, $result["mod"], $result["func"], $result["objeto_id"]);
    $crud = $this->insertDb("log_atividade", $this->campos, "?, ?, ?, ?, ?", $param);
  }

}