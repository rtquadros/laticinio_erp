// JavaScript Document
$(document).ready( function() {
	//Consulta banco via Ajax
	function getDados(url, handleData){
		$.ajax({
			type: 'GET',
			url: url,
			dataType: "json",
			success:function(data, textStatus, jqXHR){
				//console.log('ok');
				if(data != ''){
					handleData(data);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown);    
			}	
		});	
	}

	function getHTML(url, handleData){
		$.ajax({
			type: 'GET',
			url: url,
			dataType: "html",
			success:function(data, textStatus, jqXHR){
				if(data != ''){
					handleData(data);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(errorThrown);    
			}	
		});	
	}
	
	//Funções entrada em estoque
	function btnAcoes(obj){
		$(obj).on('click', '.entrada-estoque', function(e){
			e.preventDefault();

			$('#modal_estoque').modal();	
		});
	};
	btnAcoes($(this));

	//Funções para a lista de insumos
	function insereInsumo(insumo){
		var table = $("#lista-insumos tbody");
		var n = $(table).find(".item-insumos").length + 1;
		var modelo_insumo;
		if(insumo){
			var url = 'modules/estoque/controlers/produtoControl.php?func=getProduto&prod_id='+insumo.insumo_id;
			getDados(url, function(produto){
				modelo_insumo = '<tr class="item-insumos"><td>'+produto.prod_nome+'</td><td>'+produto.prod_unidade+'</td><td>'+insumo.insumo_quant+'</td><td></td><td></td><td></td></tr>';
				$(modelo_insumo).appendTo(table);
			});
		} else {
			modelo_insumo = '<tr class="item-insumos"><td></td><td><select id="insumo_id_'+n+'" name="insumo_id[]" class="form-control" required=""><option>-- Selecione --</option></select></td><td></td><td></td><td></td><td></td><td><input type="number" id="quant_lote" name="quant_lote" class="form-control" required></td><td><a href="#" class="btn btn-xs text-danger btn-remover"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td></tr>';
			$(modelo_insumo).appendTo(table);
		}
		
	}
	
	/*$('#lista-insumos').on('change', '#insumo_id', function(e) {
		e.preventDefault();
		atualizaEstoque($(this).parents('tr'));	
	});*/
	
	/*function atualizaEstoque(insumo){
		var insumo_id = $(insumo).find('#insumo_id').val();
		var estoque_input = $(insumo).find('#insumo_estoque');
		var url = 'modules/estoque/receitaControl.php?func=getInsumos';
		getDados(url, function(retorno){
			produtos = retorno.produtos;
			$(produtos).each(function(i, e) {
				if(produtos[i].prod_id == insumo_id){
					insumo_estoque = parseFloat(parseFloat(produtos[i].prod_estoque).toFixed(2));
					insumo_estoque_min = parseFloat(parseFloat(produtos[i].prod_estoque_min).toFixed(2));	
				}
			});
			
			//Adiciona o valor de estoque do insumo e caso esteja a baixo do mínimo alerta
			if(insumo_estoque <= insumo_estoque_min){
				$(estoque_input).parents('td').html('<div class="form-group has-error has-feedback"><input type="text" class="form-control" id="insumo_estoque" name="insumo_estoque[]" size="2" value="'+ insumo_estoque +'" readonly><span class="fas fa-exclamation-triangle form-control-feedback" aria-hidden="true" title="Estoque baixo!"></span></div>');
			} else {
				$(estoque_input).parents('td').html('<input type="text" class="form-control" id="insumo_estoque" name="insumo_estoque[]" size="2" value="'+ insumo_estoque +'" readonly>'); 	
			}
		});	
	}*/
	
	$('#btn-inserir-insumo').on('click', function(e){
		e.preventDefault();
		insereInsumo();
		$('form').validator('update');
		$('.peso').mask("#.##0,00", {reverse: true});
	});
	
	//Funções para a lista de processos
	function insereProcesso(table, item_processo){
		var n = $(table).find(".item-processos").length + 1;
		var modelo_processos = '<tr class="item-processos" draggable="true"><td><span class="btn btn-xs"><span class="fas fa-arrows-alt"></span><span class="sr-only">Mover</span></span></td><td><input type="text" class="form-control" id="processo_nome_'+n+'" name="processo_nome[]" placeholder="Identificação" value="" required></td><td><input type="text" class="form-control" id="processo_equip_'+n+'" name="processo_equip[]" placeholder="Equipamento" value=""></td><td><input type="text" class="form-control duracao" id="processo_duracao_'+n+'" name="processo_duracao[]" placeholder="00:00" size="2" value="" required></td><td><div class="input-group"><input type="text" class="form-control peso" id="processo_limite_'+n+'" name="processo_limite[]" placeholder="0,00" size="2" value="" required><div class="input-group-append"><span class="input-group-text">Kg/Lt</span></div></div></td><td><a href="#" class="btn btn-xs text-danger btn-remover"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td></tr>';
		//Insere o modelo de item
		var processo = $(modelo_processos).appendTo(table);
		if(item_processo != null){
			$(processo).find('#processo_nome_'+n).val(item_processo.processo_nome);
			$(processo).find('#processo_equip_'+n).val(item_processo.processo_equip);
			$(processo).find('#processo_duracao_'+n).val(item_processo.processo_duracao);
			$(processo).find('#processo_limite_'+n).val(item_processo.processo_limite);	
		}
		return processo;
	}
	
	$('#btn-inserir-processo').on('click', function(e){
		e.preventDefault();
		insereProcesso('#lista-processos tbody');
		$('form').validator('update');
		$('.sorted_table').sortable({
			containerSelector: 'table',
			itemPath: '> tbody',
			itemSelector: 'tr',
			placeholder: '<tr class="placeholder"/>',
			handle: '.fa-arrows-alt'
		});	
		$('.peso').mask("#.##0,00", {reverse: true});
		$('.duracao').mask('##00:00', {reverse: true});
	});
		
	//Controle de produção
	$('#producao_rec_id').on('change', function(e){
		e.preventDefault();
		
		var itens_insumos = new Array();
		var itens_processos = new Array();
		
		$('#lista-insumos tbody').html('');
		$('#lista-processos tbody').html('');
		
		if($(this).val() == ''){ 
			$('#lista-insumos tbody').html('');
			$('#lista-processos tbody').html('');
		} else{
			var url = 'modules/estoque/controlers/receitaControl.php?func=getReceita&rec_id=' + $(this).val();
			getDados(url, function(retorno){
				var produtoUnd = retorno.produtoUnd;
				itens_insumos = retorno.itens_insumos;
				itens_processos = retorno.itens_processos;
				
				//Adiciona a unidade do produto no input de quantidade
				$("#producao_prod_und span").html(produtoUnd);

				//Adicona o insumo da receita na lista de insumos
				$(itens_insumos).each(function(index, element) {
					var insumo = insereInsumo(itens_insumos[index]);
        });
				
				//Adiciona o processo 
				$(itens_processos).each(function(index, element) {
					var processo = insereProcesso('#lista-processos tbody', itens_processos[index]);
        });
			});
		}

		$('#producao_quant').on('change', function(e){
			e.preventDefault();
		
			var quant = $(this).val().replace(',','.');
			$('#lista-insumos tbody tr').each(function(i,e){
				if(itens_insumos.length > i){
					var insumo_quant = itens_insumos[i].insumo_quant.replace(',','.');
					var result = parseFloat(insumo_quant) * parseFloat(quant);
					$(e).find('[id*=insumo_quant]').val(result.toFixed(2).replace('.',','));	
				}
			});
		});
	});

	//Plugin inserir multiplos itens
	if(jQuery().sortable) {
		$('.sorted_table').sortable({
			containerSelector: 'table',
			  itemPath: '> tbody',
			  itemSelector: 'tr',
			  placeholder: '<tr class="placeholder"/>',
			handle: '.fa-arrows-alt'
		});	
	}
	
	$('#lista-insumos, #lista-processos').on('click', '.btn-remover', function(e){
		e.preventDefault();
		if($(this).parents('tbody').find('tr').length > 1) $(this).parents('tr').remove();
		$('form').validator('update');
	});
	
});