<?php
class Relacionamento{
	
	function __construct(){
		$db = new Db;
		//Create list of columns
		$columns_arr = array(
			array("column_name"=>"rel_id", "column_attr"=> "int(11) NOT NULL AUTO_INCREMENT"),
			array("column_name"=>"rel_nome", "column_attr"=> "varchar(255) NOT NULL"),
			array("column_name"=>"rel_apelido", "column_attr"=> "varchar(255) DEFAULT NULL"),
			array("column_name"=>"rel_documento", "column_attr"=> "varchar(255) DEFAULT NULL"),
			array("column_name"=>"rel_inscricao", "column_attr"=> "varchar(255) DEFAULT NULL"),
			array("column_name"=>"rel_email", "column_attr"=> "varchar(255) DEFAULT NULL"),
			array("column_name"=>"rel_tel", "column_attr"=> "varchar(20) DEFAULT NULL"),
			array("column_name"=>"rel_endereco", "column_attr"=> "varchar(255) DEFAULT NULL"),
			array("column_name"=>"rel_bairro", "column_attr"=> "varchar(255) DEFAULT NULL"),
			array("column_name"=>"rel_cep", "column_attr"=> "varchar(10) DEFAULT NULL"),
			array("column_name"=>"rel_municipio", "column_attr"=> "varchar(255) DEFAULT NULL"),
			array("column_name"=>"rel_estado", "column_attr"=> "varchar(2) DEFAULT NULL"),
			array("column_name"=>"rel_desc", "column_attr"=> "text"),
			array("column_name"=>"rel_variaveis", "column_attr"=> "varchar(255) DEFAULT NULL"),
			array("column_name"=>"rel_categoria", "column_attr"=> "varchar(20) NOT NULL")
		);
		// Check if table exists
		if(!$db->tableExists('relacionamento')){
			$columns = array();
			foreach($columns_arr as $column){
				array_push($columns, "`".$column['column_name']."` ".$column['column_attr']);
			}
			
			$sql = "CREATE TABLE IF NOT EXISTS `relacionamento` (".implode(",", $columns).", PRIMARY KEY (`".$columns_arr[0]['column_name']."`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;";
			
			if($db->query($sql)){
				return true;
			} else {
				echo 'Couldn´t create new table in DB.';
				echo $db->error();	
			}
		} else {
			foreach($columns_arr as $column){
				$db->addColumn('relacionamento', $column['column_name'], $column['column_attr']);	
			}
		}
		return false;	
	}
	
	public function getRelacionamento($array=''){
		$db = new Db;
		$sql = 'SELECT * FROM relacionamento';
		if(isset($array) && !empty($array)){
			foreach($array as $key => $param){
				if(!empty($param)){ 
					if(is_array($param)) $sql_arr[] = $key.' IN ('.implode(',',$param).')';
					else $sql_arr[] = $key.'="'.$param.'"';
				}
			}
			if(isset($sql_arr)) $sql .= ' WHERE ' . implode(' AND ', $sql_arr);
		} 
		$sql .= ' ORDER BY rel_nome ';
		return $db->select($sql);
	}
	
	public function getRelNome($id){
		$db = new Db;
		$sql = 'SELECT rel_nome FROM relacionamento WHERE rel_id='.$id;
		$rel_nome = $db->select($sql);
		return !empty($rel_nome[0]) ? $rel_nome[0]['rel_nome'] : '';
	}
	
	public function insertRelacionamento($dados){
		$db = new Db;
		
		if(isset($dados['rel_variaveis']) && !empty($dados['rel_variaveis'])) $rel_variaveis = $db->quote(serialize($dados['rel_variaveis']));
		else $rel_variaveis = '';
		
		$sql = 'INSERT INTO relacionamento (rel_nome, rel_apelido, rel_documento, rel_inscricao, rel_email, rel_tel, rel_endereco, rel_bairro, rel_cep, rel_municipio, rel_estado, rel_desc, rel_variaveis, rel_categoria) VALUES ("'.$dados['rel_nome'].'", "'.$dados['rel_apelido'].'", "'.$dados['rel_documento'].'", "'.$dados['rel_inscricao'].'", "'.$dados['rel_email'].'", "'.$dados['rel_tel'].'", "'.$dados['rel_endereco'].'", "'.$dados['rel_bairro'].'", "'.$dados['rel_cep'].'", "'.$dados['rel_municipio'].'", "'.$dados['rel_estado'].'", "'.$dados['rel_desc'].'", "'.$rel_variaveis.'", "'.$dados['rel_categoria'].'")';
		
		if($db->query($sql)) $result = array('erro'=>false, 'msg'=>'Relacionamento cadastrado com êxito!', 'objeto_id'=>$db->insert_id());
		else $result = array('erro'=>true, 'msg'=>$db->error());
		
		return $result;
	}
	
	public function updateRelacionamento($dados){
		$db = new Db;
		
		if(isset($dados['rel_variaveis']) && !empty($dados['rel_variaveis'])) $rel_variaveis = $db->quote(serialize($dados['rel_variaveis']));
		else $rel_variaveis = '';
		
		$sql = 'UPDATE relacionamento SET rel_nome="'.$dados['rel_nome'].'", rel_apelido="'.$dados['rel_apelido'].'", rel_documento="'.$dados['rel_documento'].'", rel_inscricao="'.$dados['rel_inscricao'].'", rel_email="'.$dados['rel_email'].'", rel_tel="'.$dados['rel_tel'].'", rel_endereco="'.$dados['rel_endereco'].'", rel_bairro="'.$dados['rel_bairro'].'", rel_cep="'.$dados['rel_cep'].'", rel_municipio="'.$dados['rel_municipio'].'", rel_estado="'.$dados['rel_estado'].'", rel_desc="'.$dados['rel_desc'].'", rel_variaveis="'.$rel_variaveis.'", rel_categoria="'.$dados['rel_categoria'].'" WHERE rel_id="'.$dados["rel_id"].'"';
		
		if($db->query($sql)) $result = array('erro'=>false, 'msg'=>'Relacionamento editado com êxito!', 'objeto_id'=>$dados["rel_id"]);
		else $result = array('erro'=>true, 'msg'=>$db->error());
		
		return $result;
	}
	
	public function deleteRelacionamento($id){
		$db = new Db;
		
		$sql = 'DELETE FROM relacionamento WHERE rel_id='.$id;
		
		if($db->query($sql)) $result = array('erro'=>false, 'msg'=>'Relacionamento excluído com êxito!', 'objeto_id'=>$id);
		else $result = array('erro'=>true, 'msg'=>$db->error());
		
		return $result;	
	}
}
?>