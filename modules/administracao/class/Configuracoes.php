<?php
require_once(ABSPATH."/class/ConnDb.php");

class Configuracoes extends ConnDb{

  public function getSiteNome(){
    $result = $this->selectDb("config_value", "configuracoes", "WHERE config_name=?", array("site_name"));
    $result = $result->fetch(PDO::FETCH_ASSOC);
    return $result["config_value"];
  }

  public function getSiteLogo(){
    $result = $this->selectDb("config_value", "configuracoes", "WHERE config_name=?", array("site_logo"));
    $result = $result->fetch(PDO::FETCH_ASSOC);
    return $result["config_value"];
  }

  public function selectConfiguracoes($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "configuracoes", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

}