<?php
$mov = new Movimentacao();
$conta = new Conta();

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
}

// Define a conta à ser administrada
if(isset($param['mov_conta_id']) && !empty($param['mov_conta_id'])){ 
  $conta_id = $param["mov_conta_id"];
  $_SESSION['conta_id'] = $conta_id;
} elseif(isset($_SESSION['conta_id']) && !empty($_SESSION['conta_id']))  
  $conta_id = $_SESSION['conta_id'];
else {
  $conta_id = $conta->selectConta("conta_id", "", array());
  $conta_id = $conta_id[0]["conta_id"];
}

// Define o período
if(isset($param["mov_data_ini"]) && !empty($param["mov_data_ini"])) $mov_data_ini = date("d/m/Y", strtotime($param["mov_data_ini"]));
else $mov_data_ini = $data_ini_mes->format("d/m/Y");
if(isset($param["mov_data_fim"]) && !empty($param["mov_data_fim"])) $mov_data_fim = date("d/m/Y", strtotime($param["mov_data_fim"]));
else $mov_data_fim = $data_fim_mes->format("d/m/Y");

$mov_tipo_nome = $mov->getMovTipos();
?>
<!-- Modal de dialogo de ações -->
<div class="modal fade" id="modal_acoes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <form method="get" action="modules/financeiro/controlers/movimentacaoControl.php">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h5 class="modal-title" id="exampleModalLabel">Mover registro para:</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="quant">Conta</label>
                    <select class="form-control" name="mov_conta_id" id="mov_conta_id">
                        <?php 
                        $contas = $conta->selectConta("*", "", array());
                        foreach($contas as $itens){
                            if($itens['conta_id'] == $conta_id) $select = 'selected';
                            else $select = '';
                        ?>
                            <option value="<?php  echo $itens['conta_id'];?>" <?php  echo $select;?>><?php  echo $itens['conta_desc'];?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="preco">Tipo de movimentação</label>
                    <select class="form-control" name="mov_tipo" id="mov_tipo">
                        <option value="1">Receitas</option>
                        <option value="2">Despesas fixas</option>
                        <option value="3">Despesas variáveis</option>
                        <option value="4">Pessoal</option>
                        <option value="5">Impostos</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn  btn-success" id="concluir-move">Mover</button>
              <button type="button" class="btn  btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
            <input type="hidden" id="mov_id" name="mov_id" />
            <input type="hidden" id="func" name="func" />
        </form>
      </div>
    </div>
</div>

<h3>Extratos financeiros</h3>

<form method="post">
    <div class="form-row">
        <div class="col-md-2">
            <div class="form-group">
                <label for="mov_conta_id">Conta</label>
                <select class="form-control" name="mov_conta_id" id="mov_conta_id">
                    <?php 
                    $contas = $conta->selectConta("*", "", array());
                    foreach($contas as $row){
                      echo "<option value='{$row["conta_id"]}'";
                      echo $row['conta_id'] == $conta_id ? "selected" : "";
                      echo ">{$row['conta_desc']}</option>";
                    }?>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="mov_data_ini">Período</label>
                <div class="input-daterange input-group">
                    <input type="text" class="form-control" name="mov_data_ini" value="<?php echo $mov_data_ini;?>" required />
                    <div class="input-group-prepend input-group-append"><span class="input-group-text">até</span></div>
                    <input type="text" class="form-control" name="mov_data_fim" value="<?php echo $mov_data_fim;?>" required />
                </div>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col">
            <div class="form-group">
                <label for="mov_tipo">Tipo de movimentação</label>
                <select class="form-control load-mov-cat" name="mov_tipo" id="mov_tipo">
                    <option value="" selected >Todas</option>
                    <?php 
                    foreach($mov_tipo_nome as $key=>$tipo){
                      echo "<option value='$key'";
                      echo isset($param["mov_tipo"]) && $param["mov_tipo"] == $key ? "selected" : "";
                      echo ">".ucfirst($tipo)."</option>";
                    }?>
                </select>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="mov_categoria">Categoria</label>
                <select class="form-control place-mov-cat" name="mov_categoria" id="mov_categoria">
                    <option value="" selected >Todas</option>
                    <?php
                    if(isset($param["mov_tipo"]) && !empty($param["mov_tipo"])){
                      $cat = new MovCategoria();
                      $categorias = $cat->selectMovCategoria("*", "WHERE cat_mov_tipo=?", array($param["mov_tipo"]));
                      foreach($categorias as $key=>$value){
                        echo "<option value='".$value["cat_id"]."'";
                        echo isset($param["mov_categoria"]) && $param["mov_categoria"] == $value["cat_id"] ? "selected" : "";
                        echo ">".ucfirst($value["cat_nome"])."</option>";
                      }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="mov_pessoa_id">Recebido de/Pago à</label>
                <select class="form-control" name="mov_pessoa_id" id="mov_pessoa_id">
                    <option value="" selected >Todos</option>
                    <?php
                    $pessoa = new Pessoa();
                    $pessoas = $pessoa->selectPessoa("*", "ORDER BY pessoa_nome ASC", array());
                    foreach($pessoas as $item){
                    ?>
                        <option value="<?php echo $item['pessoa_id']?>" <?php echo isset($param['mov_pessoa_id']) && $param['mov_pessoa_id'] == $item['pessoa_id'] ? 'selected' : '';?>><?php echo $item['pessoa_nome']?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="mov_pago">Lançamento</label>
                <select name="mov_pago" class="form-control">
                	<option value="">Todos</option>
                	<option value="1" <?php echo isset($param['mov_pago']) && $param['mov_pago'] === '1' ? 'selected' : '';?>>Pagos</option>
                	<option value="0" <?php echo isset($param['mov_pago']) && $param['mov_pago'] === '0' ? 'selected' : '';?>>Não pagos</option>
                </select>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary"><span class="fas fa-th-list"></span> Gerar extrato</button>
</form>

<form action="print.php?mod=financeiro&pag=extrato&func=imprimir" method="post" target="_blank" class="float-right" style="margin-top:-2.5em;">
  <?php 
  if(isset($param) && !empty($param)){
    foreach($param as $key=>$value){
      echo "<input type='hidden' name='$key' value='$value' />";
    }
  }
  ?>
  <p><button type="submit" class="btn btn-secondary" <?php echo isset($param) && !empty($param) ? "" : "disabled";?>><span class="fas fa-print"></span> Imprimir</button></p>
</form>

<?php
if(isset($param) && !empty($param)){
	$extrato = $mov->getExtractMov($param);
?>
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
                    <th></th>
                </tr> 
            </thead>
            <tbody>
                <?php 
                $total_receitas = 0;
                $total_despesas = 0;
                foreach($extrato as $key=>$value){ ?>
                	<tr>
                        <td>
                        <?php 
                        echo date("d/m/Y", strtotime($value['mov_data']));
                        if(!$value['mov_pago'] && strtotime(date('Y-m-d')) >= strtotime($value['mov_data'])){
                            echo ' <small><span class="fas fa-exclamation-triangle text-danger" title="Em atraso"></span></small>';	
                        }
                        ?> 
                        </td>
                        <td><a href="?mod=financeiro_movimentacao&funcao=editar&tipo=<?php echo $value['mov_tipo'];?>&mov_id=<?php echo $value['mov_id']; ?>"><?php echo '<b>#'.$value['mov_id'].'</b> - '.$value['mov_desc'];?></a></td>
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
                        <td>
                            <div class="custom-control custom-switch">
                              <input type="checkbox" class="custom-control-input mov_pago" id="receita-<?php echo $value['mov_id'];?>" <?php if($value['mov_pago']) echo 'checked'; ?> data-id="<?php echo $value['mov_id'];?>">
                              <label class="custom-control-label" for="receita-<?php echo $value['mov_id'];?>"><span class="sr-only">Movimento pago</span></label>
                            </div>
                        </td>
                        <td>
                            <div class="hidden-xs">
                                <a href="modules/financeiro/controlers/movimentacaoControl.php?func=deleteMovimentacao&mov_id=<?php echo $value['mov_id']; ?>" title="Excluir" class="delete-confirm text-danger"><span class="fas fa-trash"></span></a>
                                &nbsp;
                                <a href="#" title="Mover" class="move-movimentacao" data-mov-id="<?php echo $value['mov_id']; ?>"><span class="fas fa-exchange-alt"></span></a>
                                &nbsp;
                                <a href="#" title="Copiar" class="duplica-movimentacao" data-mov-id="<?php echo $value['mov_id']; ?>"><span class="fas fa-copy"></span></a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
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
<?php }?>