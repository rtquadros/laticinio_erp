<?php
$mov = new Movimentacao();
$cat = new MovCategoria();
$conta = new Conta();

if(isset($_POST) && !empty($_POST)){
    $args = array(
      "dre_tipo" => FILTER_SANITIZE_NUMBER_INT,
      "mov_conta_id" => FILTER_SANITIZE_NUMBER_INT, 
      "data_ref" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")),
      "data_comp" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate"))
    );
    $param = filter_input_array(INPUT_POST, $args);
}

// Define o período
if(isset($param["data_ref"]) && !empty($param["data_ref"])) 
  $data_ref = new DateTime($param["data_ref"]);
else $data_ref = new DateTime($mes_ref->format("Y-m-01"));
if(isset($param["data_comp"]) && !empty($param["data_comp"])) 
  $data_comp = new DateTime($param["data_comp"]);
else {
  $data_comp = new DateTime($mes_ref->format("Y-m-01"));
  $data_comp->sub(new DateInterval("P1M"));
}
?>
<style type="text/css">
    @media print {
        @page {
            size: A4 portrait;
            margin: 1cm;
        }
    }
</style>
<h4>
    <small>LATICÍNIO NOVA VISTA</small><br />
    DEMONSTRATIVO DE RESULTADOS DO EXERCÍCIO (DRE)<br />
    <small>Período de <?php echo $param["dre_tipo"] == 3 ? $data_ref->format("Y") : $data_ref->format("m/Y");?> 
    <?php 
    if($param["dre_tipo"] > 1) {
      echo 'comparado com período de '; 
      echo $param["dre_tipo"] == 3 ? $data_comp->format("Y") : $data_comp->format("m/Y");
    }?> 
    para  a conta '<?php echo $conta->getDesc($param["mov_conta_id"]); ?>'</small>
</h4>

<?php
if(isset($param) && !empty($param)){
  $param_ref = array(
    "mov_conta_id" => $param["mov_conta_id"],
    "mov_tipo" => "",
    "mov_categoria" => "",
    "mov_data_ini" => $data_ref->format("Y-m-01"),
    "mov_data_fim" => $data_ref->format("Y-m-t")
  );
  if($param["dre_tipo"] == 2 || $param["dre_tipo"] == 3){
    $param_comp = array(
      "mov_conta_id" => $param["mov_conta_id"],
      "mov_tipo" => "",
      "mov_categoria" => "",
      "mov_data_ini" => $data_comp->format("Y-m-01"),
      "mov_data_fim" => $data_comp->format("Y-m-t")
    );
    if($param["dre_tipo"] == 3){
      $param_ref["mov_data_ini"] = $data_ref->format("Y-01-01");
      $param_ref["mov_data_fim"] = $data_ref->format("Y-12-01");
      $param_comp["mov_data_ini"] = $data_comp->format("Y-01-01");
      $param_comp["mov_data_fim"] = $data_comp->format("Y-12-01");
    }
  }
?>
  <div class="row">
    <div class="col">
      <div class="table-responsive">
      <table class="table table-striped table-bordered table-sm" id="demonstrativo-1">
        <thead>
      <tr>
        <th></th>
        <th></th>
        <th class="text-right"><?php echo $param["dre_tipo"] == 3 ? $data_ref->format("Y") : $data_ref->format("m/Y");?></th>
        <?php if(isset($param_comp)){ ?>
        <th class="text-right"><?php echo $param["dre_tipo"] == 3 ? $data_comp->format("Y") : $data_comp->format("m/Y");?></th>
        <th class="text-right">Balanço</th>
        <?php }?>
      </tr>
      </thead>
      <tbody>
        <tr>
        <th colspan="2">1 - Receita Operacional Bruta</th>
        <td class="text-right"><span class="text-success">
          <?php 
          $param_ref['mov_tipo'] = 1;
          $receitas_ref = $mov->getMovTotal($param_ref);
          echo 'R$ '.number_format($receitas_ref, 2, ',', '.');
          ?>
        </span></td>
        <?php if(isset($param_comp)){ ?>
          <td class="text-right"><span class="text-success">
            <?php 
            $param_comp['mov_tipo'] = 1;
            $receitas_comp = $mov->getMovTotal($param_comp);
            echo 'R$ '.number_format($receitas_comp, 2, ',', '.');
            ?>
          </span></td>
          <td class="text-right"><span class="text-success">
            <?php 
            if($receitas_ref != 0){
              $receitas_bal = round(($receitas_ref - $receitas_comp) * 100 / $receitas_ref);
              echo $receitas_ref > $receitas_comp ? '+' : '';
              echo $receitas_bal.'%';
            }
            ?>
          </span></td>
        <?php }?>
      </tr>
      <tr>
        <th colspan="2">2 - Impostos</th>
        <td class="text-right"><span class="text-danger">
          <?php 
          $param_ref['mov_tipo'] = 5;
          $impostos_ref = $mov->getMovTotal($param_ref);
          echo 'R$ '.number_format($impostos_ref, 2, ',', '.');
          ?>
        </span></td>
        <?php if(isset($param_comp)){ ?>
          <td class="text-right"><span class="text-danger">
            <?php 
            $param_comp['mov_tipo'] = 5;
            $impostos_comp = $mov->getMovTotal($param_comp);
            echo 'R$ '.number_format($impostos_comp, 2, ',', '.');
            ?>
          </span></td>
          <td class="text-right"><span class="text-danger">
            <?php 
            if($impostos_ref != 0){
              $impostos_bal = round(($impostos_ref - $impostos_comp) * 100 / $impostos_ref);
              echo $impostos_ref > $impostos_comp ? '+' : '';
              echo $impostos_bal.'%';
            }
            ?>
          </span></td>
        <?php }?>
      </tr>
      <?php
      //DETALHA OS IMPOSTOS
      $categorias = $cat->selectMovCategoria("*", "WHERE cat_mov_tipo=?", array(5));
      foreach($categorias as $key=>$value){
      ?>
        <tr>
          <td></td>
          <td><?php echo $value['cat_nome']?></td>
          <td class="text-right">
            <?php 
            $param_ref['mov_categoria'] = $value['cat_id'];
            $itens_ref = $mov->getMovTotal($param_ref);
            echo 'R$ '.number_format($itens_ref, 2, ',', '.');
            ?>
          </td>
          <?php if(isset($param_comp)){ ?>
            <td class="text-right">
              <?php 
              $param_comp['mov_categoria'] = $value['cat_id'];
              $itens_comp = $mov->getMovTotal($param_comp);
              echo 'R$ '.number_format($itens_comp, 2, ',', '.');
              ?>
            </td>
            <td class="text-right">
              <?php 
              if($itens_ref != 0){
                $itens_bal = round(($itens_ref - $itens_comp) * 100 / $itens_ref);
                echo $itens_ref > $itens_comp ? '+' : '';
                echo $itens_bal.'%';
              }
              ?>
            </td>
          <?php }?>
        </tr>
      <?php }?>
      <tr>
        <th colspan="2">3 - Receita Líquida (3)=(1)-(2)</th>
        <td class="text-right"><span class="text-success">
          <?php 
          $receitas_ref = $receitas_ref - $impostos_ref;
          echo 'R$ '.number_format($receitas_ref, 2, ',', '.');
          ?>
        </span></td>
        <?php if(isset($param_comp)){ ?>
          <td class="text-right"><span class="text-success">
            <?php 
            $receitas_comp = $receitas_comp - $impostos_comp;
            echo 'R$ '.number_format($receitas_comp, 2, ',', '.');
            ?>
          </span></td>
          <td class="text-right"><span class="text-success">
            <?php
            if($receitas_ref != 0){ 
              $receitas_bal = round(($receitas_ref - $receitas_comp) * 100 / $receitas_ref);
              echo $receitas_ref > $receitas_comp ? '+' : '';
              echo $receitas_bal.'%';
            }
            ?>
          </span></td>
        <?php }?>
      </tr>
      <tr>
        <th colspan="2">4 - Despesas Variáveis</th>
        <td class="text-right"><span class="text-danger">
          <?php 
          $param_ref['mov_tipo'] = 3;
          $param_ref['mov_categoria'] = '';
          $despesas_var_ref = $mov->getMovTotal($param_ref);
          echo 'R$ '.number_format($despesas_var_ref, 2, ',', '.');
          ?>
        </span></td>
        <?php if(isset($param_comp)){ ?>
          <td class="text-right"><span class="text-danger">
            <?php 
            $param_comp['mov_tipo'] = 3;
            $param_comp['mov_categoria'] = '';
            $despesas_var_comp = $mov->getMovTotal($param_comp);
            echo 'R$ '.number_format($despesas_var_comp, 2, ',', '.');
            ?>
          </span></td>
          <td class="text-right"><span class="text-danger">
            <?php 
            if($despesas_var_ref != 0){
              $despesas_var_bal = round(($despesas_var_ref - $despesas_var_comp) * 100 / $despesas_var_ref);
              echo $despesas_var_ref > $despesas_var_comp ? '+' : '';
              echo $despesas_var_bal.'%';
            }
            ?>
          </span></td>
        <?php }?>
      </tr>
      <?php
      //DETALHA AS DESPESAS VARIÁVEIS
      $categorias = $cat->selectMovCategoria("*", "WHERE cat_mov_tipo=?", array(3));
      foreach($categorias as $key=>$value){
      ?>
        <tr>
          <td></td>
          <td><?php echo $value['cat_nome']?></td>
          <td class="text-right">
            <?php 
            $param_ref['mov_categoria'] = $value['cat_id'];
            $itens_ref = $mov->getMovTotal($param_ref);
            echo 'R$ '.number_format($itens_ref, 2, ',', '.');
            ?>
          </td>
          <?php if(isset($param_comp)){ ?>
            <td class="text-right">
              <?php 
              $param_comp['mov_categoria'] = $value['cat_id'];
              $itens_comp = $mov->getMovTotal($param_comp);
              echo 'R$ '.number_format($itens_comp, 2, ',', '.');
              ?>
            </td>
            <td class="text-right">
              <?php 
              if($itens_ref != 0){
                $itens_bal = round(($itens_ref - $itens_comp) * 100 / $itens_ref);
                echo $itens_ref > $itens_comp ? '+' : '';
                echo $itens_bal.'%';
              }
              ?>
            </td>
          <?php }?>
        </tr>
      <?php }?>
      </tbody>
      </table>
    </div>
    </div>
    <div class="col">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm" id="demonstrativo-2">
          <thead>
        <tr>
          <th></th>
          <th></th>
          <th class="text-right"><?php echo $param["dre_tipo"] == 3 ? $data_ref->format("Y") : $data_ref->format("m/Y");?></th>
          <?php if(isset($param_comp)){ ?>
          <th class="text-right"><?php echo $param["dre_tipo"] == 3 ? $data_comp->format("Y") : $data_comp->format("m/Y");?></th>
          <th class="text-right">Balanço</th>
          <?php }?>
        </tr>
        </thead>
        <tbody>
        <tr>
          <th colspan="2">5 - Margem de Contribuição (5)=(3)-(4)</th>
          <td class="text-right"><span class="text-success">
            <?php 
            $margem_ref = $receitas_ref - $despesas_var_ref;
            echo 'R$ '.number_format($margem_ref, 2, ',', '.');
            ?>
          </span></td>
          <?php if(isset($param_comp)){ ?>
            <td class="text-right"><span class="text-success">
              <?php 
              $margem_comp = $receitas_comp - $despesas_var_comp;
              echo 'R$ '.number_format($margem_comp, 2, ',', '.');
              ?>
            </span></td>
            <td class="text-right"><span class="text-success">
              <?php 
              if($margem_ref != 0){
                $margem_bal = round(($margem_ref - $margem_comp) * 100 / $margem_ref);
                echo $margem_ref > $margem_comp ? '+' : '';
                echo $margem_bal.'%';
              }
              ?>
            </span></td>
          <?php }?>
        </tr>
        <tr>
          <th colspan="2">6 - Despesas Fixas</th>
          <td class="text-right"><span class="text-danger">
            <?php 
            $param_ref['mov_tipo'] = 2;
            $param_ref['mov_categoria'] = '';
            $despesas_fix_ref = $mov->getMovTotal($param_ref);
            echo 'R$ '.number_format($despesas_fix_ref, 2, ',', '.');
            ?>
          </span></td>
          <?php if(isset($param_comp)){ ?>
            <td class="text-right"><span class="text-danger">
              <?php 
              $param_comp['mov_tipo'] = 2;
              $param_comp['mov_categoria'] = '';
              $despesas_fix_comp = $mov->getMovTotal($param_comp);
              echo 'R$ '.number_format($despesas_fix_comp, 2, ',', '.');
              ?>
            </span></td>
            <td class="text-right"><span class="text-danger">
              <?php 
              $despesas_fix_bal = round(($despesas_fix_ref - $despesas_fix_comp) * 100 / $despesas_fix_ref);
              echo $despesas_fix_ref > $despesas_fix_comp ? '+' : '';
              echo $despesas_fix_bal.'%';
              ?>
            </span></td>
          <?php }?>
        </tr>
        <?php
        //DETALHA AS DESPESAS FIXAS
        $categorias = $cat->selectMovCategoria("*", "WHERE cat_mov_tipo=?", array(2));
        foreach($categorias as $key=>$value){
        ?>
          <tr>
            <td></td>
            <td><?php echo $value['cat_nome']?></td>
            <td class="text-right">
              <?php 
              $param_ref['mov_categoria'] = $value['cat_id'];
              $itens_ref = $mov->getMovTotal($param_ref);
              echo 'R$ '.number_format($itens_ref, 2, ',', '.');
              ?>
            </td>
            <?php if(isset($param_comp)){ ?>
              <td class="text-right">
                <?php 
                $param_comp['mov_categoria'] = $value['cat_id'];
                $itens_comp = $mov->getMovTotal($param_comp);
                echo 'R$ '.number_format($itens_comp, 2, ',', '.');
                ?>
              </td>
              <td class="text-right">
                <?php 
                if($itens_ref != 0){
                  $itens_bal = round(($itens_ref - $itens_comp) * 100 / $itens_ref);
                  echo $itens_ref > $itens_comp ? '+' : '';
                  echo $itens_bal.'%';
                }
                ?>
              </td>
            <?php }?>
          </tr>
        <?php }?>
        <tr>
          <th colspan="2">7 - Despesas Pessoal</th>
          <td class="text-right"><span class="text-danger">
            <?php 
            $param_ref['mov_tipo'] = 4;
            $param_ref['mov_categoria'] = '';
            $despesas_func_ref = $mov->getMovTotal($param_ref);
            echo 'R$ '.number_format($despesas_func_ref, 2, ',', '.');
            ?>
          </span></td>
          <?php if(isset($param_comp)){ ?>
            <td class="text-right"><span class="text-danger">
              <?php 
              $param_comp['mov_tipo'] = 4;
              $param_comp['mov_categoria'] = '';
              $despesas_func_comp = $mov->getMovTotal($param_comp);
              echo 'R$ '.number_format($despesas_func_comp, 2, ',', '.');
              ?>
            </span></td>
            <td class="text-right"><span class="text-danger">
              <?php 
              $despesas_func_bal = round(($despesas_func_ref - $despesas_func_comp) * 100 / $despesas_func_ref);
              echo $despesas_func_ref > $despesas_func_comp ? '+' : '';
              echo $despesas_func_bal.'%';
              ?>
            </span></td>
          <?php }?>
        </tr>
        <?php
        //DETALHA AS DESPESAS PESSOAL
        $categorias = $cat->selectMovCategoria("*", "WHERE cat_mov_tipo=?", array(4));
        foreach($categorias as $key=>$value){
        ?>
          <tr>
            <td></td>
            <td><?php echo $value['cat_nome']?></td>
            <td class="text-right">
              <?php 
              $param_ref['mov_categoria'] = $value['cat_id'];
              $itens_ref = $mov->getMovTotal($param_ref);
              echo 'R$ '.number_format($itens_ref, 2, ',', '.');
              ?>
            </td>
            <?php if(isset($param_comp)){ ?>
              <td class="text-right">
                <?php 
                $param_comp['mov_categoria'] = $value['cat_id'];
                $itens_comp = $mov->getMovTotal($param_comp);
                echo 'R$ '.number_format($itens_comp, 2, ',', '.');
                ?>
              </td>
              <td class="text-right">
                <?php 
                if($itens_ref != 0){
                  $itens_bal = round(($itens_ref - $itens_comp) * 100 / $itens_ref);
                  echo $itens_ref > $itens_comp ? '+' : '';
                  echo $itens_bal.'%';
                }
                ?>
              </td>
            <?php }?>
          </tr>
        <?php }?>
        <tr>
          <th colspan="2">8 - Resultado do Período (8)=(5)-(6)-(7) </th>
          <td class="text-right"><span class="text-success">
            <?php 
            $resultado_ref = $margem_ref - $despesas_fix_ref - $despesas_func_ref;
            echo 'R$ '.number_format($resultado_ref, 2, ',', '.');
            ?>
          </span></td>
          <?php if(isset($param_comp)){ ?>
            <td class="text-right"><span class="text-success">
              <?php 
              $resultado_comp = $margem_comp - $despesas_fix_comp - $despesas_func_comp;
              echo 'R$ '.number_format($resultado_comp, 2, ',', '.');
              ?>
            </span></td>
            <td class="text-right"><span class="text-success">
              <?php 
              if($resultado_ref != 0){
                $resultado_bal = round(($resultado_ref - $resultado_comp) * 100 / $resultado_ref);
                echo $resultado_ref > $resultado_comp ? '+' : '';
                echo $resultado_bal.'%';
              }
              ?>
            </span></td>
          <?php }?>
        </tr>
        </tbody>
      </table>
    </div>
    </div>
  </div>
<?php }?>