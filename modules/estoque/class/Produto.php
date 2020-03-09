<?php
require_once(ABSPATH."/class/ConnDb.php");

class Produto extends ConnDb{
  
  private $campos = array("prod_nome", "prod_codbarras", "prod_marca", "prod_unidade", "prod_preco_venda", "prod_preco_custo", "prod_imagem", "prod_estoque_min","prod_validade", "prod_tipo");

  private $unidades = array("KG"=>"Quilogramas", "LT"=>"Litros", "UND"=>"Unidades");

  private $prod_tipos = array("produto"=>"Produto próprio", "mercadoria"=>"Mercadoria para revenda", "insumo"=>"Insumo de produção");

  public function getProdUnidades(){
    return $this->unidades;
  }

  public function getProdTipos(){
    return $this->prod_tipos;
  }

  public function getNomeProduto($id){
    $result = $this->selectProduto("prod_nome", "WHERE prod_id=?", array($id));
    return $result[0]["prod_nome"];
  }

  public function getUnidadeProduto($id){
    $result = $this->selectProduto("prod_unidade", "WHERE prod_id=?", array($id));
    return $result[0]["prod_unidade"];
  }

  public function selectProduto($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "produto", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertProduto($param){
    $crud = $this->insertDb("produto", $this->campos, "?, ?, ?, ?, ?, ?, ?, ?, ?, ?", array_values($param));
    if($crud) 
	    $result = array('erro'=>false, 'msg'=>'Produto cadastrado com êxito!', 'objeto_id'=>$this->lastInsertId());
    else 
	    $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function updateProduto($param, $id){
    $crud = $this->updateDb("produto", $this->campos, "prod_id={$id}", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Produto editado com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function deleteProduto($id){
    $crud = $this->deleteDb("produto", "prod_id={$id}", $id);
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Produto excluído com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

}