<?php
$objEstoque = new Estoque();
$objProduto = new Produto();
$objProducao = new Producao();

if(isset($_POST) && !empty($_POST)){
    $args = array(
      "prod_tipo" => FILTER_SANITIZE_SPECIAL_CHARS,
      "estoque_prod_id" => FILTER_SANITIZE_NUMBER_INT,
      "estoque_lote" => FILTER_SANITIZE_SPECIAL_CHARS,
      "estoque_data_entrada" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")),
      "estoque_fabricacao" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")),
      "estoque_validade" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")),
      "estoque_quant_atual" => FILTER_VALIDATE_BOOLEAN,
    );
    $param = filter_input_array(INPUT_POST, $args);
 
  // Forma os parâmetros de datas
  $arr_datas = array("estoque_data_entrada", "estoque_fabricacao", "estoque_validade");
  foreach($arr_datas as $key){
    if(!empty($param[$key][0])){ 
      $param[$key][0] = date("Y-m-d", strtotime($param[$key][0]));
      $param[$key][1] = isset($param) && !empty($param[$key][1]) ? date("Y-m-d", strtotime($param[$key][1])) : date("Y-m-d");
    } else $param[$key] = "";
  }
  $param = array_filter($param);
  
  if(!empty($param)){
    // Constrói as condiçoes de busca conforme os parâmetros enviados
    $arr_condicoes = array();
    foreach($param as $key=>$value){
      // Monta as condicções para tipos de itens SÓ caso nenhum item específico tenha sido selecionado
      if($key == "prod_tipo" && !isset($param["estoque_prod_id"])){
        $result = $objProduto->selectProduto("prod_id", "WHERE prod_tipo=?", array($value));
        foreach ($result as $produto) {
          $prod_ids[] = $produto["prod_id"];
        }
        $arr_condicoes[] = "estoque_prod_id IN(".implode($prod_ids, ",").")";
      }
      elseif($key == "estoque_prod_id") $arr_condicoes[] = "{$key}='{$value}'"; 
      elseif($key == "estoque_lote") $arr_condicoes[] = "{$key} LIKE '{$value}%'";
      // Monta as condições para datas
      elseif(in_array($key, $arr_datas)) $arr_condicoes[] = "{$key} BETWEEN {$value[0]} 00:00:00 AND {$value[1]} 00:00:00";
      elseif($key == "estoque_quant_atual") $arr_condicoes[] = "{$key} > 0";
      
    }
    $condicoes = implode($arr_condicoes, " AND ");
    $estoques = $objEstoque->selectEstoque("*", "WHERE {$condicoes}", array());
  }
}
?>
<div class="card mb-3">
  <img class="card-img-top" id="prod_imagem" src="" alt="">
  <div class="card-body mb-0 py-2">
    <form method="post" action="">
      <div class="form-row">
        <div class="col form-group">
          <label for="prod_tipo">Tipo de item</label><br />
          <select id="prod_tipo" name="prod_tipo" class="form-control">
              <?php
              $prod_tipos = $objProduto->getProdTipos();
              foreach($prod_tipos as $key=>$prod_tipo){
                echo "<option value='{$key}'";
                echo isset($param) && $param["prod_tipo"] == $key ? " selected " : "";
                echo ">{$prod_tipo}</option>";
              }
              ?>
          </select>
        </div>
        <div class="col form-group">
          <label for="estoque_prod_id">Item</label>
          <select id="estoque_prod_id" name="estoque_prod_id" class="form-control">
              <option value="0">--- Todos ---</option>
              <?php
              $produtos = $objProduto->selectProduto("*", "WHERE prod_tipo=?", array("produto"));
              foreach($produtos as $produto){
                echo "<option value='{$produto['prod_id']}'";
                echo isset($param) && !empty($param["estoque_prod_id"]) == $produto["prod_id"] ? " selected " : "";
                echo ">{$produto['prod_nome']}</option>";
              }
              ?>
          </select>
        </div>
        <div class="col form-group">
          <label for="estoque_lote">Lote</label>
          <input type="text" id="estoque_lote" name="estoque_lote" class="form-control" value="<?php echo isset($param) && !empty($param['estoque_lote']) ? $param['estoque_lote'] : "" ;?>">
        </div>
        <div class="col form-group">
          <label for="estoque_data_entrada">Data de entrada</label>
          <div class="input-daterange input-group">
            <input type="text" class="form-control" name="estoque_data_entrada[]" value="<?php echo isset($param) && !empty($param['estoque_data_entrada'][0]) ? date("d/m/Y", strtotime($param['estoque_data_entrada'][0])) : "" ;?>" />
            <div class="input-group-prepend input-group-append"><span class="input-group-text">até</span></div>
            <input type="text" class="form-control" name="estoque_data_entrada[]" value="<?php echo isset($param) && !empty($param['estoque_data_entrada'][1]) ? date("d/m/Y", strtotime($param['estoque_data_entrada'][1])) : "" ;?>" />
          </div>
        </div>
      </div>
      <div class="form-row align-items-center">
        <div class="col-3 form-group">
          <label for="estoque_fabricacao">Fabricação</label>
          <div class="input-daterange input-group">
            <input type="text" class="form-control" name="estoque_fabricacao[]" value="<?php echo isset($param) && !empty($param['estoque_fabricacao'][0]) ? date("d/m/Y", strtotime($param['estoque_fabricacao'][0])) : "" ;?>" />
            <div class="input-group-prepend input-group-append"><span class="input-group-text">até</span></div>
            <input type="text" class="form-control" name="estoque_fabricacao[]" value="<?php echo isset($param) && !empty($param['estoque_fabricacao'][1]) ? date("d/m/Y", strtotime($param['estoque_fabricacao'][1])) : "" ;?>" />
          </div>
        </div>
        <div class="col-3 form-group">
          <label for="estoque_validade">Validade</label>
          <div class="input-daterange input-group">
            <input type="text" class="form-control" name="estoque_validade[]" value="<?php echo isset($param) && !empty($param['estoque_validade'][0]) ? date("d/m/Y", strtotime($param['estoque_validade'][0])) : "" ;?>" />
            <div class="input-group-prepend input-group-append"><span class="input-group-text">até</span></div>
            <input type="text" class="form-control" name="estoque_validade[]" value="<?php echo isset($param) && !empty($param['estoque_validade'][1]) ? date("d/m/Y", strtotime($param['estoque_validade'][1])) : "" ;?>" />
          </div>
        </div>
        <div class="col-auto">
          <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" value="true" id="estoque_quant_atual" name="estoque_quant_atual" <?php echo isset($param) && !empty($param['estoque_quant_atual']) ? "checked" : "" ;?> >
            <label class="form-check-label" for="estoque_quant_atual">Apenas em estoque</label>
          </div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><span class="fas fa-file-medical-alt"></span> Gerar relatório</button>
      <button type="submit" class="btn btn-secondary" <?php echo isset($param) && !empty($param) ? "" : "disabled";?>><span class="fas fa-print"></span> Imprimir</button>
    </form>
  </div>
</div>
<div class="row">
  <div class="col">
    <?php if(isset($estoques)){ 
      if(empty($estoques)) echo "Não foram encontrados estoques que combinem com os parâmetros procurados.";
      else{
    ?>
        <h3>Relatório de estoque</h3>
        <table class="table table-sm table-stripped table-bordered datatable">
          <thead>
            <tr>
              <th>Lote</th>
              <th>Entrada</th>
              <th>Data entrada</th>
              <th>Quant. entrada</th>
              <th>Quant. atual</th>
              <th>Fabricação</th>
              <th>Validade</th>
              <th>Custo</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($estoques as $estoque){ ?>
            <tr>
              <td><?php echo $estoque["estoque_lote"];?></td>
              <td><?php 
              $entrada = unserialize($estoque["estoque_entrada_id"]);
              if(array_key_exists("producao", $entrada)) 
                echo "<a href='?mod=estoque&pag=producao&func=esitar&producao_id={$entrada['producao']}'>Produção #".$objProducao->getOrdemProducao($entrada["producao"])."</a>";
              ?></td>
              <td><?php echo date("d/m/Y", strtotime($estoque["estoque_data_entrada"]));?></td>
              <td><?php echo $estoque["estoque_quant_entrada"];?></td>
              <td><?php echo $estoque["estoque_quant_atual"];?></td>
              <td><?php echo date("d/m/Y", strtotime($estoque["estoque_fabricacao"]));?></td>
              <td><?php echo date("d/m/Y", strtotime($estoque["estoque_validade"]));?></td>
              <td><?php echo $estoque["estoque_custo"];?></td>
            </tr>
          <?php }?>
          </tbody>
        </table>
    <?php 
      }
    }?>
  </div>
</div>