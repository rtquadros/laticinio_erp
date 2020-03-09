<?php 
$mov = new Movimentacao();
$conta = new Conta();
$pessoa = new Pessoa();

if(isset($_POST) && !empty($_POST)){
    $args = array(
      "mov_conta_id" => FILTER_SANITIZE_NUMBER_INT, 
      "mov_data_ini" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")),
      "mov_data_fim" => array("filter"=>FILTER_CALLBACK, "options"=>array("FilterDb", "sanitizeDate")),
      "mov_tipo" => FILTER_SANITIZE_NUMBER_INT,
      "mov_categoria" => FILTER_SANITIZE_NUMBER_INT, 
      "mov_pessoa_id" => FILTER_SANITIZE_NUMBER_INT,
      "mov_pago" => FILTER_SANITIZE_NUMBER_INT,
    );
    $param = filter_input_array(INPUT_POST, $args);

    $extrato = $mov->getExtractMov($param);
    foreach($extrato as $key=>$value){ 
      if($value["mov_tipo"] == 1) $mov_arr["receitas"][] = $value;
      else $mov_arr["despesas"][] = $value;
    }
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
    EXTRATO FINANCEIRO<br />
    <small>Período de <?php echo date("d/m/Y", strtotime($param['mov_data_ini'])).' a '.date("d/m/Y", strtotime($param['mov_data_fim']));?> para  a conta '<?php echo $conta->getDesc($param["mov_conta_id"]); ?>'</small>
</h4>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-sm datatable" id="extrato">
        <thead> 
            <tr> 
                <th style="width:8em;">Data</th>
                <th>Descrição</th>
                <th>Recebido/Pago</th>
                <th>Categoria</th>
                <th class="text-right" style="width:8em;">Valor</th>
                <th class="text-center" style="width:4em;">Pago</th>
            </tr> 
        </thead>
        <tbody>
            
            <?php 
            $total_receitas = 0;
            $total_despesas = 0;
            foreach($mov_arr as $tipo=>$movimentos){
              echo "<tr><th colspan='6'>".ucfirst($tipo)."</th></tr>"; 
              foreach($movimentos as $key=>$value){ 
        ?>
                <tr>
                    <td>
                    <?php 
                    echo date("d/m/Y", strtotime($value['mov_data']));
                    if(!$value['mov_pago'] && strtotime(date('Y-m-d')) >= strtotime($value['mov_data'])){
                        echo ' <small><span class="fas fa-exclamation-triangle text-danger" title="Em atraso"></span></small>';    
                    }
                    ?> 
                    </td>
                    <td><?php echo '<b>#'.$value['mov_id'].'</b> - '.$value['mov_desc'];?></td>
                    <td><?php if(!empty($value['mov_pessoa_id']) || $value['mov_pessoa_id'] != 0) echo $pessoa->getPessoaNome($value['mov_pessoa_id']);?></td>
                    <td>
                    <?php 
                    if(!empty($value['mov_categoria'])){
                        $cat = new MovCategoria();
                        echo $cat->getCatNome($value['mov_categoria']);
                    }
                    ?>
                    </td>
                    <td class="text-right">
                        <span class="<?php echo $value['mov_tipo'] == 1 ? 'text-success' : 'text-danger';?>">
                            <?php echo 'R$ '.number_format($value['mov_valor'], 2, ',', '.');?>
                            &nbsp;&nbsp;
                            <?php 
                            if($value['mov_tipo'] == 1){
                                $total_receitas += $value["mov_valor"];
                                echo 'C';
                            } else {
                                $total_despesas += $value["mov_valor"];
                                echo 'D';
                            }
                            ?>
                        </span>
                    </td>
                    <td class="text-success">
                      <?php if($value['mov_pago']) echo '<span class="fas fa-check"></span>'; ?>
                    </td>
                </tr>
        <?php } 
            }?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>Total de receitas do período</strong></td>
                <td class="text-right" colspan="3"><span class="text-success">
                <?php echo 'R$ '.number_format($total_receitas, 2, ',', '.');?>
                </span></td>
            </tr>
            <tr>
                <td colspan="4"><strong>Total de despesas do período</strong></td>
                <td class="text-right" colspan="3"><span class="text-danger">
                <?php echo 'R$ '.number_format($total_despesas, 2, ',', '.');?>
                </span></td>
            </tr>
            <tr>
                <td colspan="4"><strong>Balanço do período</strong></td>
                <td class="text-right" colspan="3">
                <?php 
                $balanço = $total_receitas - $total_despesas;
                echo 'R$ '.number_format($balanço, 2, ',', '.');
                ?>
                </td>
            </tr>
            <tr>
                <td colspan="4"><strong>Saldo atual da conta (<?php echo date('d/m/Y'); ?>)</strong></td>
                <td class="text-right" colspan="3"><?php echo 'R$ '.number_format($conta->getSaldo($param['mov_conta_id']), 2, ',', '.');?></td>
            </tr>
        </tfoot>
    </table>
</div>