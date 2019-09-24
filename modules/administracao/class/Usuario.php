<?php
require_once(ABSPATH."/class/ConnDb.php");

class Usuario extends ConnDb{
  
  private $campos = array("usu_nome", "usu_senha", "usu_nivel", "usu_modulos");

  public function getUsuModulos($id){
    $result = $this->selectUsuario("usu_modulos", "WHERE usu_id=?", array($id));
    return unserialize($result[0]["usu_modulos"]);
  }

  public function getUsuNome($id){
    $result = $this->selectUsuario("usu_nome", "WHERE usu_id=?", array($id));
    return $result[0]["usu_nome"];
  }

  public function getUsuSenha($id){
    $result = $this->selectUsuario("usu_senha", "WHERE usu_id=?", array($id));
    return $result[0]["usu_senha"];
  }
  
  public function acessoModUsuario($modulo, $id){
    $usu_modulos = $this->getUsuModulos($id);
    if(isset($usu_modulos[$modulo["modulo"]])){
      if(is_string($modulo["pagina"])) $modulo["pagina"] = array($modulo["pagina"]);
      $mod_access = array_intersect($modulo['pagina'], $usu_modulos[$modulo["modulo"]]);
      if(count($mod_access) > 0){
        return $mod_access;
      }
    }
    return false;
  }

  public function selectUsuario($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "usuarios", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertUsuario($param){
    $crud = $this->insertDb("usuarios", $this->campos, "?, ?, ?, ?", array_values($param));
    if($crud) 
	    $result = array('erro'=>false, 'msg'=>'Usuário cadastrado com êxito!', 'objeto_id'=>$this->lastInsertId());
    else 
	    $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function updateUsuario($param, $id){
    $crud = $this->updateDb("usuarios", $this->campos, "usu_id={$id}", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Usuário editado com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function deleteUsuario($id){
    $crud = $this->deleteDb("usuarios", "usu_id={$id}", $id);
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Usuário excluído com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

}