<?php
$mov = new Movimentacao();
$conta = new Conta();

// Define a conta à ser administrada
if(isset($_POST['conta_id']) && !empty($_POST['conta_id'])){
  $conta_id = filter_input(INPUT_POST, "conta_id", FILTER_SANITIZE_NUMBER_INT); 
  $_SESSION['conta_id'] = $conta_id;
} elseif(isset($_SESSION['conta_id']) && !empty($_SESSION['conta_id']))  
  $conta_id = $_SESSION['conta_id'];
else {
  $conta_id = $conta->selectConta("conta_id", "", array());
  $conta_id = $conta_id[0]["conta_id"];
}

$mov_tipo_nome = $mov->getMovTipos();

if(isset($_GET["func"]) && ($_GET["func"] == 'cadastrar' || $_GET["func"] == 'editar')){
    $mov_tipo = filter_input(INPUT_GET, "mov_tipo", FILTER_SANITIZE_NUMBER_INT);
    if($_GET["func"] == 'cadastrar'){
      $action = "modules/financeiro/controlers/movimentacaoControl.php?mov_tipo={$mov_tipo}&func=insertMovimentacao"; 
    } elseif($_GET["func"] == 'editar'){
      if(isset($_GET['mov_id']) && !empty($_GET['mov_id'])){ 
        $mov_id = filter_input(INPUT_GET, "mov_id", FILTER_SANITIZE_NUMBER_INT);  
        $action = "modules/financeiro/controlers/movimentacaoControl.php?mov_tipo={$mov_tipo}&func=updateMovimentacao&mov_id={$mov_id}";
        $retorno = $mov->selectMovimentacao("*", "WHERE mov_id=?", array($mov_id));
      } else {
        header('Location: index.php?mod=financeiro&pag=movimentacao&func=visualizar');
      }
    }
?>
	<div class="row mb-2">
    	<div class="col">
            <h3 class="d-inline-block mb-0 mt-2 mr-2"><?php echo ucfirst($mov_tipo_nome[$mov_tipo]); ?> <small>na conta</small></h3>
            <form class="form-inline d-inline-block" method="post">
                <div class="form-group">
                    <select class="form-control " name="conta_id" id="conta_id" onchange="submit();">
                        <?php
                        $rows = $conta->selectConta("*", "", array());
                        foreach($rows as $row){
                          echo "<option value='{$row["conta_id"]}'";
                          echo $row['conta_id'] == $conta_id ? "selected" : "";
                          echo ">{$row['conta_desc']}</option>";
                        }?>
                    </select>
                </div>
            </form>
    	</div>
    </div>    
    
    <form method="post" action="<?php echo $action; ?>">
        <input type="hidden" name="mov_tipo" id="mov_tipo" value="<?php echo $mov_tipo; ?>" />
        <div class="form-row">
            <div class="col-md-4">
            	<div class="form-row">
                    <div class="col">
                    	<div class="form-group">
                            <label for="mov_data">Data</label>
                            <div class="input-group">
                                <?php if(isset($retorno)){ ?> 
                                  <input type="text" class="form-control datepicker" name="mov_data" id="mov_data" value="<?php echo filter_var($retorno[0]['mov_data'], FILTER_CALLBACK, array("options"=>array("FilterDb", "brDate")));?>" required tabindex="1">
                                <?php } else {?>
                                  <input type="text" class="form-control datepicker" name="mov_data" id="mov_data" value="<?php echo date('d/m/Y');?>" required  tabindex="1">
                                <?php }?>
                                <div class="input-group-append">
                                  <span class="input-group-text"><span class="fas fa-th"></span></span>
                                </div>
                            </div>
                        </div>
            		</div>
                	<div class="col">
                        <div class="form-group">
                            <label for="mov_valor">Valor</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                  <span class="input-group-text">R$</span>
                                </div>
                                <input type="text" class="form-control dinheiro" name="mov_valor" id="mov_valor" value="<?php echo isset($retorno) ? $retorno[0]['mov_valor'] : '';?>" placeholder="0,00" tabindex="2" required>
                            </div>
                        </div>
            		</div>
                </div>
                
                <div class="form-group">
                    <label for="mov_desc">Descrição</label>
                    <input type="text" class="form-control" name="mov_desc" id="mov_desc" value="<?php echo isset($retorno) ? $retorno[0]['mov_desc'] : '';?>" placeholder="Descrição" tabindex="3">
                </div>
                <div class="form-group">
                    <label for="mov_pessoa_id">Recebido de / Pago à</label>
                    <select class="form-control" name="mov_pessoa_id" id="mov_pessoa_id" tabindex="4">
                        <option disabled selected >Selecione o relacionamento</option>
                        <?php
                          $pessoa = new Pessoa();
                          $rows = $pessoa->selectPessoa("*", "ORDER BY pessoa_nome ASC", array());
                          foreach($rows as $row){
                            echo "<option value='{$row["pessoa_id"]}'";
                            if(isset($retorno)){
                              echo $retorno[0]["mov_pessoa_id"] == $row["pessoa_id"] ? " selected " : "";
                            }
                            echo ">{$row['pessoa_nome']}</option>";  
                          }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="mov_categoria">Categoria</label>
                    <select class="form-control" name="mov_categoria" id="mov_categoria" tabindex="5">
                    	<option disabled selected >Selecione a categoria</option>
                        <?php
                          $movCategoria = new MovCategoria();
                          $rows = $movCategoria->selectMovCategoria("*", "WHERE cat_mov_tipo=? ORDER BY cat_nome ASC", array($mov_tipo));
                          foreach($rows as $row){
                            echo "<option value='{$row["cat_id"]}'";
                            if(isset($retorno)){
                              echo $retorno[0]["mov_categoria"] == $row["cat_id"] ? " selected " : "";
                            }
                            echo ">{$row['cat_nome']}</option>";  
                          }
                        ?>
                    </select>
                </div>
                <div class="form-row">
                	<div class="col">
                        <div class="form-group">
                            <label for="mov_forma_pag">Modo de pagamento</label>
                            <select class="form-control" name="mov_forma_pag" id="mov_forma_pag" tabindex="6">
                                <?php
								foreach($mov->getMovFormaPag() as $forma_pag){
									echo "<option value='{$forma_pag}'";
                                    if(isset($retorno)){
                                      echo $retorno[0]["mov_forma_pag"] == $forma_pag ? " selected " : "";
                                    }
                                    echo ">{$forma_pag}</option>";
								}
								?>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                    	<div class="form-group">
                            <div class="custom-control custom-switch my-4">
                              <input type="checkbox" class="custom-control-input" id="mov_pago" name="mov_pago" value="1" <?php echo isset($retorno) && $retorno[0]['mov_pago'] ? 'checked' : ''; ?>>
                              <label class="custom-control-label" for="mov_pago"> Pago</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="mov_nf">Nota fiscal</label>
                    <input type="text" class="form-control" name="mov_nf" id="mov_nf" value="<?php echo isset($retorno) ? $retorno[0]['mov_nf'] : '';?>" tabindex="8">
                </div>
            </div>
            <div class="col-md-4">
            	<div class="form-row">
                    <div class="col">
                        <div class="form-group">
                            <label for="mov_parcela_n">Nº de parcelas</label>
                            <input type="number" class="form-control" name="mov_parcela_n" id="mov_parcela_n" value="1" tabindex="10">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="mov_parcela">Prazo entre parcelas (dias)</label>
                            <input type="number" class="form-control" name="mov_parcela" id="mov_parcela" value="" tabindex="11" placeholder="0">
                        </div>
                    </div>
            	</div>
                <div class="form-group">
                    <label for="mov_detalhes">Detalhes</label>
                    <textarea class="form-control" name="mov_detalhes" id="mov_detalhes" rows="5" tabindex="11"><?php echo isset($retorno) ? $retorno[0]['mov_detalhes'] : '';?></textarea>
                </div>
            </div>
        </div>
        <input class="btn btn-success <?php echo $_GET["func"] == 'editar' ? "update-confirm" : "";?>" type="submit" value="<?php echo ucfirst($_GET['func']);?>" tabindex="12">
        <input class="btn btn-secondary" type="reset" value="Limpar" tabindex="13">
        <input type="hidden" name="mov_conta_id" id="mov_conta_id" value="<?php echo $conta_id; ?>" />
        <input type="hidden" name="mov_variaveis" id="mov_variaveis" value="" />
    </form>
<?php
} elseif(!isset($_GET["func"]) || $_GET["func"] == "visualizar") {
?>  
	<!-- Modal de dialogo de ações -->
	<div class="modal fade" id="modal_acoes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
          	<form method="get" action="modules/financeiro/controlers/movimentacaoControl.php">
                <div class="modal-header py-1">
                  <h5 class="modal-title" id="exampleModalLabel">Mover registro para:</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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

    <div class="row mb-2">
        <div class="col-2">
          <form method="post" class="form-inline">
            <div class="form-group">
                <label class="my-1 mr-2" for="conta_id">Conta</label>
                <select class="form-control " name="conta_id" id="conta_id" onchange="submit();">
                    <?php
                    $retorno = $conta->selectConta("*", "", array());
                    foreach($retorno as $itens){
                      echo "<option value='{$itens["conta_id"]}'";
                      echo $itens['conta_id'] == $conta_id ? "selected" : "";
                      echo ">{$itens['conta_desc']}</option>";
                    }?>
                </select>
            </div>
          </form>
        </div>
        <div class="col-3 ml-auto">
          <form method="post">
            <div class="input-group">
                <input type="text" class="form-control  monthpicker" name="mes_ref" id="mes_ref" value="<?php echo $mes_ref->format('m/Y'); ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn  btn-secondary"><span class="fas fa-search"></span> Buscar</button>
                </div>
            </div>
          </form>
        </div>
    </div>

    <div class="form-row my-4" id="resumo-conta">
    	<div class="col">
            <div class="card border-secondary">
                <div class="card-body py-1">	
                    <h4><small>Saldo da conta</small><br/><span id="saldo-conta" data-conta-id="<?php echo $conta_id;?>"><?php echo 'R$ '.number_format($conta->getSaldo($conta_id), 2, ',', '.');?></span></h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-secondary">
                <div class="card-body py-1">	
                    <h4><small>Saldo projetado</small><br/><?php echo 'R$ '.number_format($conta->getSaldoProjetado($conta_id), 2, ',', '.');?></h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-success">
            	<div class="card-body py-1">
                    <h4><small class="text-success">Receitas do mês</small><br/>
                    <?php 
                    $retorno = $mov->selectMovimentacao("SUM(mov_valor) AS valor_total", "WHERE mov_conta_id=? AND mov_tipo=1 AND mov_data BETWEEN ? AND ?", array($conta_id, $mes_ref->format("Y-m-01"), $mes_ref->format("Y-m-t")));
                    $receita = $retorno[0]["valor_total"];
                    echo 'R$ '.number_format($receita, 2, ',', '.');
                    ?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-danger">
            	<div class="card-body py-1">
                    <?php
                    $data_content = "";
                    $despesas = 0;
                    for($i=2; $i<=5; $i++){
                      $retorno = $mov->selectMovimentacao("SUM(mov_valor) AS valor_total", "WHERE mov_conta_id=? AND mov_tipo=? AND mov_data BETWEEN ? AND ?", array($conta_id, $i, $mes_ref->format("Y-m-01"), $mes_ref->format("Y-m-t")));
                      $despesas += $retorno[0]["valor_total"];
                      $data_content .= "<p>".ucfirst($mov_tipo_nome[$i]).": R$".number_format($retorno[0]["valor_total"], 2, ",", ".")."</p>";
                    }
                    ?>
                    <h4><small class="text-danger">Despesas do mês <span class="fas fa-info-circle" data-container="body" data-toggle="popover" data-placement="bottom" data-content="<?php echo $data_content;?>"></span></small><br/>
                    <?php echo 'R$ '.number_format($despesas, 2, ',', '.');?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-info">
                <div class="card-body py-1">	
                    <h4><small class="text-info">Balanço do mês</small><br/><?php echo 'R$ '.number_format($receita - $despesas, 2, ',', '.');?></h4>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">    
      <div class="col">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
    	<?php 
		foreach($mov_tipo_nome as $key=>$tipo){
		  echo "<li role='presentation' class='nav-item'><a href='#tab-{$key}' class='nav-link";
          echo $key == 1 ? " active" : "";
          echo "' aria-controls='tab-{$key}' role='tab' data-toggle='tab'>".ucfirst($tipo)."</a></li>";
		}
		?>
        </ul>    
        <!-- Tab panes -->
        <div class="tab-content">
        <?php foreach($mov_tipo_nome as $key=>$tipo){ ?>
		  <div role="tabpanel" class="tab-pane <?php echo $key == 1 ? 'active' : '';?>" id="<?php echo "tab-{$key}"; ?>">
            <div class="row my-2">
              <div class="col text-right">
                <a class="btn btn-<?php echo $key == 1 ? 'success' : 'danger';?> d-inline-block" href="?mod=financeiro&pag=movimentacao&func=cadastrar&mov_tipo=<?php echo $key;?>" role="button">Nova <?php echo $tipo;?></a>
                <div class="dropdown d-inline-block">
                  <button type="button" class="btn btn-secondary btn-responsive mov-actions dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled>Ações</button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item delete-movimentacao delete-confirm" href="modules/financeiro/controlers/movimentacaoControl.php?func=deleteMovimentacao" role="button"><span class="fas fa-trash"></span> Excluir</a>
                    <a class="dropdown-item move-movimentacao" href="#" role="button"><span class="fas fa-share-alt"></span> Mover</a>
                    <a class="dropdown-item duplica-movimentacao" href="#" role="button"><span class="fas fa-copy"></span> Duplicar</a>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-sm datatable" id="<?php echo str_replace(' ', '-',$tipo);?>">
                <thead> 
                  <tr> 
                	<th></th>
                    <th style="width:8em;">Data</th>
                    <th>Descrição</th>
                    <th>Recebido/Pago</th>
                    <th>Categoria</th>
                    <th class="text-right" style="width:8em;">Valor</th>
                    <th class="text-center" style="width:4em;">Pago</th>
                    <th class="d-none d-sm-table-cell"></th>
                  </tr> 
                </thead>
                <tbody>  
                <?php
                $retorno = $mov->selectMovimentacao("*", "WHERE mov_conta_id=? AND mov_tipo=? AND mov_data BETWEEN ? AND ?", array($conta_id, $key, $mes_ref->format("Y-m-01"), $mes_ref->format("Y-m-t")));
                foreach($retorno as $key=>$value){
                ?>
                  <tr> 
                	<td class="text-center"><div class="form-check"><input class="form-check-input position-static switch-btn" type="checkbox" value="<?php echo $value['mov_id']; ?>" /></div></td>
                    <td>      
                      <?php 
                      $filter = new FilterDb();
					  echo $filter->brDate($value['mov_data']);
					  if(!$value['mov_pago'] && strtotime(date('Y-m-d')) >= strtotime($value['mov_data']))
						echo ' <small><span class="fas fa-exclamation-triangle text-danger" title="Em atraso"></span></small>';	
                      ?>
                    </td>
                    <td>
                      <?php
                      echo "<a href='?mod=financeiro&pag=movimentacao&func=editar&mov_tipo={$value["mov_tipo"]}&mov_id={$value["mov_id"]}'><b>#{$value["mov_id"]}</b> - {$value["mov_desc"]}</a>";
                      ?>
                    </td>
                    <td>
                      <?php 
                      if(!empty($value['mov_pessoa_id']) || $value['mov_pessoa_id'] != 0){
                        $pessoa = new Pessoa();
                        echo $pessoa->getPessoaNome($value['mov_pessoa_id']);
                      }
                      ?>
                    </td>
                    <td>
                      <?php 
                      if(!empty($value['mov_categoria'])){
                        $movCategoria = new MovCategoria();
                        echo $movCategoria->getCatNome($value["mov_categoria"]);
                      }
                      ?>
                    </td>
                    <td class="text-right">
						<span class="<?php echo $value['mov_tipo'] == 1 ? 'text-success' : 'text-danger';?>">
							<?php echo 'R$ '.number_format($value['mov_valor'], 2, ',', '.');?>
                    	</span>
                    </td>
                    <td class="text-center">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input mov_pago" id="receita-<?php echo $value['mov_id'];?>" <?php if($value['mov_pago']) echo 'checked'; ?> data-id="<?php echo $value['mov_id'];?>">
                          <label class="custom-control-label" for="receita-<?php echo $value['mov_id'];?>"><span class="sr-only">Movimento pago</span></label>
                        </div>
                    </td>
                    <td class="text-center d-none d-sm-table-cell">
                      <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#" title="Mover" class="move-movimentacao" data-mov-id="<?php echo $value['mov_id']; ?>"><span class="fas fa-exchange-alt"></span> <span class="sr-only">Mover</span></a></li>
                        <li class="list-inline-item"><a href="#" title="Copiar" class="duplica-movimentacao" data-mov-id="<?php echo $value['mov_id']; ?>"><span class="fas fa-copy"></span> <span class="sr-only">Copiar</span></a></li>
                        <li class="list-inline-item"><a href="modules/financeiro/controlers/movimentacaoControl.php?func=deleteMovimentacao&mov_id=<?php echo $value['mov_id']; ?>" title="Excluir" class="delete-confirm text-danger"><span class="fas fa-trash"></span> <span class="sr-only">Excluir</span></a></li>
                      </ul>
                    </td>
                </tr>
        <?php }?>
                </tbody>
                </table>
            </div>
        </div>
    		<?php }?>
        </div>
        </div>
    </div>
<?php
}
?>