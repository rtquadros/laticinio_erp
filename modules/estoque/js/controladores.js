// JavaScript Document
$(document).ready( function() {
	//Funções entrada em estoque
	$("#modal_estoque").on("show.bs.modal", function(e){
		var modal = $(this);
		var producao_id = $(e.relatedTarget).data("producao-id");
		var url = 'modules/estoque/controlers/producaoControl.php?func=entradaEstoque&producao_id='+producao_id;
		getDados(url, function(result){
			$.each(result, function(index, element){
				if(index == "estoque_fabricacao" || index == "estoque_validade"){
					$(modal).find("#"+index).datepicker('setDate', element);
				} else if (index == "prod_nome" || index == "prod_unidade"){
					$(modal).find("#"+index).html(element);
				} else if (index == "estoque_entrada_id" ){
					$(modal).find("#"+index).val(JSON.stringify(element));
				} else {
					$(modal).find("#"+index).val(element);
				}
			});
		});
	});

	//Funções para inserir insumos e processos
	var rec_insumos = [];
	var rec_processos = [];

	$('#modal_empenho').modal({
		show: false
	});

	function insereInsumo(insumo_index){
		var insumo = rec_insumos[insumo_index];
		var url = 'modules/estoque/controlers/produtoControl.php?func=getProduto&prod_id='+insumo.insumo_id;
		getDados(url, function(produto){
			//Adiciona os detalhes do produto ao objeto insumo
			rec_insumos[insumo_index].insumo_detalhes = produto;
			var modelo_insumo = '<tr class="item-insumos" id="insumo-'+insumo_index+'"><td>'+insumo.insumo_id+'</td><td>'+produto.prod_nome+'</td>';
			modelo_insumo += '<td>'+produto.prod_unidade+'</td><td class="insumo-quant">'+insumo.insumo_quant+'</td><td class="total-empenhado"></td>';
			modelo_insumo += '<td class="text-right"><button type="button" class="btn btn-sm btn-primary empenhar-insumo" data-insumo-index="'+insumo_index+'"><span class="fas fa-plus-circle"></span> Empenhar</button></td></tr>';
			$(modelo_insumo).appendTo($("#lista-insumos tbody"));
		});
	}

	function insereProcesso(processo){
		var table = $("#lista-processos tbody");
		var n = $(table).find(".item-processos").length + 1;
		var modelo_processo = '<tr class="item-processos" id="processo-'+n+'"><td>'+n+'</td><td>'+processo.processo_nome+'</td><td>'+processo.processo_equip+'</td><td>'+processo.processo_duracao+'</td><td>'+processo.processo_limite+'</td></tr>';
		$(modelo_processo).appendTo(table);
	}

	function detalheEstoque(insumo_index, estoque_index){
		var estoque = rec_insumos[insumo_index].insumo_estoque[estoque_index];
		var linha = '<tr><td>'+formatData(estoque.estoque_data_entrada)+'</td><td>'+estoque.estoque_quant_atual+'</td>';
		linha += '<td>'+formatData(estoque.estoque_validade)+'</td></tr>';
		$("#detalhe-estoque tbody").html(linha);
		$("#estoque_quant_empenhada").val("");
		$("#estoque_quant_empenhada").data("maxval", estoque.estoque_quant_atual);
	}

	function insereEmpenho(insumo_index, estoque_index, form){
		var post = {};
		var valido = true;
		var insumo_estoque = rec_insumos[insumo_index].insumo_estoque[estoque_index];

		$(form).each(function(i, e){
			post[e.name] = e.value;
		});

		// Testa se o lote já foi empenhado
		if(!rec_insumos[insumo_index].insumo_empenhos) rec_insumos[insumo_index].insumo_empenhos = [];
		else {
			$(rec_insumos[insumo_index].insumo_empenhos).each(function(index, element){
				if(element !== null && element.estoque_id == post.estoque_id) valido = false;
			});
		}

		if(valido){
			var empenho = {
				"estoque_id": post.estoque_id,
				"estoque_quant_empenhada": parseFloat(post.estoque_quant_empenhada.replace(',','.'))
			};

			// Testa se há um espaço vazio no array para ser preenchido pelo novo empenho
			var empenho_index = rec_insumos[insumo_index].insumo_empenhos.indexOf(null);
	    if (empenho_index > -1) {
	        rec_insumos[insumo_index].insumo_empenhos[empenho_index] = empenho;
	    } else {
	        rec_insumos[insumo_index].insumo_empenhos.push(empenho);
	        empenho_index = rec_insumos[insumo_index].insumo_empenhos.length - 1;
	    }

			if($("#insumo-empenho-"+insumo_index).length == 0){
				var tabela = '<tr id="insumo-empenho-'+insumo_index+'"> <td colspan="6" class="pl-5"> <table class="table table-sm table-borderless mb-0" style="border-left:3px solid #000;" >';
				tabela += '<thead><tr class="bg-white"><th class="pl-4">Lote</th> <th>Entrada</th> <th>Validade</th> <th>Em estoque</th> <th>Empenhado</th> <th></th> </tr> </thead>';
				tabela +='<tbody></tbody></table></td></tr>'; 
				$("#insumo-"+insumo_index).after(tabela);
			}

			var linha = '<tr class="bg-white" id="empenho-'+empenho_index+'"> <td class="pl-4 pb-0">'+insumo_estoque.estoque_lote+'</td>';
			linha += '<td class="pb-0">'+formatData(insumo_estoque.estoque_data_entrada)+'</td> <td class="pb-0">'+formatData(insumo_estoque.estoque_validade)+'</td>';
			linha += '<td class="pb-0">'+insumo_estoque.estoque_quant_atual+'</td> <td class="pb-0">'+post.estoque_quant_empenhada+'</td>';
			linha += '<td class="pb-0"><button title="Excluir" class="btn btn-sm removeEmpenho text-danger" data-insumo-index="'+insumo_index+'" data-empenho-index="'+empenho_index+'"><span class="fas fa-trash"></span></button></td> </tr>'; 
			$("#insumo-empenho-"+insumo_index).find("tbody").append(linha);
			empenhoTotal(insumo_index);
		} else bootbox.alert("Lote já empenhado.");
	}

	function empenhoTotal(insumo_index){
		var total = 0;
		$.each(rec_insumos[insumo_index].insumo_empenhos, function(i, e){
			if(e !== null) total += e.estoque_quant_empenhada;
		});
		$("#insumo-"+insumo_index).find(".total-empenhado").html(total.toFixed(2).replace('.',','));
	}

	//Funções formulário de produção
	$(document).on('change', '#producao_rec_id', function(e){
		e.preventDefault();
		
		$('#lista-insumos tbody').html('');
		$('#lista-processos tbody').html('');
		
		if($(this).val() == ''){ 
			$("#producao_quant").attr("disabled", "disabled");
			$("#producao_prod_und span").html("--");
		} else{
			var url = 'modules/estoque/controlers/receitaControl.php?func=getReceita&rec_id=' + $(this).val();
			getDados(url, function(retorno){
				rec_insumos = retorno.rec_insumos;
				rec_processos = retorno.rec_processos;

				//Adicona o insumo da receita na lista de insumos
				$(rec_insumos).each(function(index, element) {
					insereInsumo(index);
        });
				
				//Adiciona o processo 
				$(rec_processos).each(function(index, element) {
					insereProcesso(rec_processos[index]);
        });

        // Adiciona detalhes do produto
        $("#producao_prod_id").val(retorno.rec_prod_id);
				url = 'modules/estoque/controlers/produtoControl.php?func=getProduto&prod_id='+retorno.rec_prod_id;
				getDados(url, function(retorno){	
					$("#producao_quant").removeAttr("disabled");
					$.each(retorno, function(key, value){
						$("#"+key).html(value);
						if(key == "prod_imagem") $("#"+key).attr("src", value);
					});
				});
			});
		}

		$('#producao_quant').on('change', function(e){
			e.preventDefault();
		
			var quant = $(this).val().replace(',','.');
			$('#lista-insumos .item-insumos').each(function(i,e){
				if(rec_insumos.length > i){
					var insumo_quant = rec_insumos[i].insumo_quant.replace(',','.');
					var result = parseFloat(insumo_quant) * parseFloat(quant);
					$(e).find('.insumo-quant').html(result.toFixed(2).replace('.',','));	
				}
			});
		});
	});

	//Funções modal de empenho de insumos
	$(document).on("click", ".empenhar-insumo", function(e){
		e.preventDefault();
		var insumo_index = $(this).data("insumo-index");
		var url = "modules/estoque/controlers/estoqueControl.php?func=getEstoque&prod_id="+rec_insumos[insumo_index].insumo_id;
		getDados(url, function(estoque){
			if(typeof estoque !== 'undefined' && estoque.length > 0){
				rec_insumos[insumo_index].insumo_estoque = estoque;
				var produto = rec_insumos[insumo_index].insumo_detalhes;
				//Prepara o modal para o novo insumo
				$("#modal_empenho input, #modal_empenho #estoque_id, #detalhe-estoque tbody").each(function(i, e){ 
					$(e).val('').html('');	
				});
				$('#modal_empenho .modal-title').html('Empenhar "'+produto.prod_nome+'":');
				$("#modal_empenho #estoque_prod_und span").html(produto.prod_unidade);
				$("#modal_empenho #insumo_index").val(insumo_index);
				$(estoque).each(function(i, e) {
					var options = '<option value="'+ estoque[i].estoque_id +'" data-estoque-index="'+i+'">'+ estoque[i].estoque_lote +'</option>';
					$("#modal_empenho #estoque_id").append(options);
				});
				$('#modal_empenho').modal("show");
			} else {
				bootbox.alert("Insumo sem estoque cadastrado.");
			}
		});
	});

	$(document).on("shown.bs.modal", "#modal_empenho", function(){
		detalheEstoque($(this).find("#insumo_index").val(), $(this).find("#estoque_id option:selected").data("estoque-index"));
	});

	$(document).on("change", "#modal_empenho #estoque_id", function(){
		detalheEstoque($("#modal_empenho #insumo_index").val(), $(this).find("option:selected").data("estoque-index"));
	});

	$(document).on("submit", "#modal_empenho form", function(e){
		if(!e.isDefaultPrevented()){
			e.preventDefault();
			insereEmpenho($(this).find("#insumo_index").val(), $(this).find("#estoque_id option:selected").data("estoque-index"), $(this).serializeArray());
			$('#modal_empenho').modal("hide");
		}
	});

	$(document).on("click", ".removeEmpenho", function(e){
		e.preventDefault();
		var insumo_index = $(this).data("insumo-index");
		var empenho_index = $(this).data("empenho-index");
		$("#insumo-empenho-"+insumo_index+" #empenho-"+empenho_index).remove();
		rec_insumos[insumo_index].insumo_empenhos[empenho_index] = null;
		empenhoTotal(insumo_index);
	});

	var submit = false;
	$(document).on("submit", "#form-producao", function(e){
		if(!submit){
			e.preventDefault();
			// Testa se todos os insumo tem lotes empenhados
			var erros = 0;
			var array_post = {};
			$.each(rec_insumos, function(index, element){
				if($.isEmptyObject(element.insumo_empenhos)) erros += 1;
				else {
					$.each(element.insumo_empenhos, function(index, element){
						if(element === null) erros += 1;
					});
				} 
			});
			if(erros == 0) {
				array_post.producao_insumos = [];
				array_post.producao_processos = rec_processos;
				$.each(rec_insumos, function(index, element){
					var array_temp = {
						"insumo_id": element.insumo_id, 
						"insumo_quant": element.insumo_quant, 
						"insumo_empenhos": element.insumo_empenhos
					};
					array_post.producao_insumos.push(array_temp);
				});
				$("#producao_insumos").val(JSON.stringify(array_post.producao_insumos));
				$("#producao_processos").val(JSON.stringify(array_post.producao_processos));
				submit = true;
				$(this).submit();
			} else bootbox.alert("Todos os insumos devem ter lotes empenhados nessa produção.");
		}
	});
});