<div class="modal fade" id="modal_estoque" tabindex="-1" role="dialog" aria-labelledby="estoqueModal" aria-hidden="true">
  <div class="modal-dialog modal-xlg" role="document">
    <div class="modal-content">
    	<form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="estoqueModal">Entrada em estoque </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="input-group input-group-sm">
            <input type="text" class="form-control form-control-sm datepicker" name="estoque_data_entrada" id="estoque_data_entrada" value="<?php echo date('d/m/Y'); ?>" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-th"></span></div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-striped table-sm table-nowrap">
              <thead>
                <tr>
                  <td>#</td>
                  <td>Produto</td>
                  <td>Lote</td>
                  <td>Quantidade</td>
                  <td>Fabricação</td>
                  <td>Validade</td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td class="estoque_produto">
                    <span class="nome_produto">Requeijão do norte</span>
                    <input type="hidden" name="estoque_prod_id" id="estoque_prod_id" value="">
                  </td>
                  <td>
                    <input type="text" class="form-control form-control-sm" name="estoque_lote" id="estoque_lote" value="" required>
                  </td>
                  <td>
                    <div class="input-group input-group-sm">
                      <input type="text" class="form-control form-control-sm peso" name="estoque_quant_entrada" id="estoque_quant_entrada" placeholder="0" value="" required>
                      <div class="input-group-append"><span class="input-group-text">Kg/Lt</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="input-group input-group-sm">
                      <input type="text" class="form-control form-control-sm datepicker" name="estoque_fabricacao" id="estoque_fabricacao" value="<?php echo date('d/m/Y'); ?>" required>
                      <div class="input-group-append">
                          <div class="input-group-text"><span class="fas fa-th"></span></div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="input-group input-group-sm">
                      <input type="text" class="form-control form-control-sm datepicker" name="estoque_validade" id="estoque_validade" value="<?php echo date('d/m/Y'); ?>" required>
                      <div class="input-group-append">
                          <div class="input-group-text"><span class="fas fa-th"></span></div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div> 
        </div>
        <div class="modal-footer">
          <input class="btn btn-success" type="submit" value="Cadastrar">
          <button class="btn btn-danger" type="button" data-dismiss="modal" aria-label="Close">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>