<?php
require_once(ABSPATH."/class/ConnDb.php");

class Configuracoes extends ConnDb{

  public function getConfig($config_name){
    $result = $this->selectConfiguracoes("config_value", "WHERE config_name=?", array($config_name));
    return $result[0]["config_value"];
  }

  public function selectConfiguracoes($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "configuracoes", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

}