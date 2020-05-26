<?php
require_once(ABSPATH."/class/ConnDb.php");
require_once("Produto.php");

class Producao extends ConnDb{
  
  private $campos = array("producao_ordem", "producao_data_ordem", "producao_prod_id", "producao_func_id", "producao_quant", "producao_insumos", "producao_processos");

  public function getOrdemProducao($id = null){
    if(!is_null($id) && !empty($id)){
      $result = $this->selectProducao("producao_ordem", "WHERE producao_id = ?", array($id));
    } else {
      $result = $this->selectProducao("producao_ordem", "WHERE producao_data_ordem > ? ORDER BY producao_id DESC LIMIT 1", array(date("Y-01-01 00:00:00")));
      $result[0]["producao_ordem"] = $result[0]["producao_ordem"] + 1;
    }
    $ordem = sprintf('%05d', $result[0]["producao_ordem"]);
    return $ordem;
  }

  public function getEntregaEstimada($id){
    $result = $this->selectProducao("producao_processos", "WHERE producao_id=?", array($id));
    $processos = unserialize($result[0]["producao_processos"]);
    $duracao_total = array("h"=>0, "i"=>0);
    foreach($processos as $processo){
      $duracao = explode(":", $processo["processo_duracao"]);
      $duracao_total["h"] += $duracao[0];
      $duracao_total["i"] += $duracao[1];
    }
    return $duracao_total;
  }

  public function setProducaoDataEntrega($data, $id){
    $crud = $this->updateDb("producao", "producao_data_entrega", "producao_id={$id}", array($data));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Produção entregue com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function selectProducao($campos, $condicoes, $param){
    $result = $this->selectDb($campos, "producao", $condicoes, $param);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertProducao($param){
    $crud = $this->insertDb("producao", $this->campos, "?, ?, ?, ?, ?, ?, ?", array_values($param));
    if($crud) 
	    $result = array('erro'=>false, 'msg'=>'Produção cadastrada com êxito!', 'objeto_id'=>$this->lastInsertId());
    else 
	    $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function updateProducao($param, $id){
    $crud = $this->updateDb("producao", $this->campos, "producao_id={$id}", array_values($param));
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Produção editada com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function deleteProducao($id){
    $crud = $this->deleteDb("producao", "producao_id={$id}", $id);
    if($crud) 
      $result = array('erro'=>false, 'msg'=>'Produção excluída com êxito!', 'objeto_id'=>$id);
    else 
      $result = array('erro'=>true, 'msg'=>$crud->errorInfo());

    return $result;
  }

  public function gerarLote($id){
    $producao = $this->selectProducao("*", "WHERE producao_id=?", array($id));
    
    $objProduto = new Produto();
    $prod_codbarras = $objProduto->getCodbarrasProduto($producao[0]["producao_prod_id"]);
    
    $prod_codbarras = substr($prod_codbarras, -2);
    $producao_quant = sprintf('%04d', $producao[0]["producao_quant"]);
    $dia_mes = date("d", strtotime($producao[0]["producao_data_ordem"]));

    return $prod_codbarras.$producao_quant.$dia_mes;
  }

}