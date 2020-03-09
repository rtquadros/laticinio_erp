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

	// Atualização via ajax dos saldos
	function updateSaldos(){
	  var conta_id = $("#saldo-conta").data("conta-id");
	  var url = 'modules/financeiro/controlers/contaControl.php?func=getSaldo&conta_id='+conta_id;
	  getDados(url, function(dados){
	  	dados = parseFloat(dados);
	  	$('#saldo-conta').html(dados.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL' }))
	  });
	}
	
	//Ativa plugin popover
	$(function () {
	  $('[data-toggle="popover"]').popover({html:true});
	})

	//Funções movimentar e duplicar movimentação financeira
	function btnAcoes(obj){
		$(obj).on('click', '.delete-movimentacao, .move-movimentacao, .duplica-movimentacao', function(e){
			e.preventDefault();

			if($(this).data('mov-id')){
				var mov_id = $(this).data('mov-id');
			} else {
				var mov_id = [];
				$.each($(this).parents('.tab-pane').find("input[type='checkbox']:checked"), function(){
					if($(this).attr('value') != undefined) mov_id.push($(this).attr('value'));
				});
			}
			
			if($(this).hasClass('duplica-movimentacao')) {
				$('#modal_acoes .modal-title').html('Duplicar registro '+mov_id+' para:');
				$('#modal_acoes .btn-success').html('Duplicar');
				$('#modal_acoes form #func').val('duplicateMovimentacao');
				$('#modal_acoes form #mov_id').val(mov_id);
				$('#modal_acoes').modal();	
			}else if($(this).hasClass('move-movimentacao')) {
				$('#modal_acoes .modal-title').html('Mover registro '+mov_id+' para:');
				$('#modal_acoes .btn-success').html('Mover');	
				$('#modal_acoes form #func').val('moveMovimentacao');
				$('#modal_acoes form #mov_id').val(mov_id);
				$('#modal_acoes').modal();
			}else if($(this).hasClass('delete-movimentacao')){
				$(this).attr('href',  $(this).attr('href') + '&mov_id=' +  mov_id );
			}
		});
	};
	btnAcoes($(this));

	//Plugin datatable
	$('table.datatable').each(function(i,e) {
		//Função atualizar status de pagamento 
		$(this).on('click', '.mov_pago', function(e){
		  var value;
		  if($(this).is(":checked")) value = 1;
		  else value = 0;
		  var url = 'modules/financeiro/controlers/movimentacaoControl.php?func=setPago&mov_id='+$(this).data('id')+'&mov_pago='+value;
		  getDados(url, function(dados){
		  	if(!dados.erro) updateSaldos();
		  });
		});

		btnAcoes($(this));
		
	});
	
	//Switch de input
	$('.switch').on('change', function(){
		var id = $(this).attr('id');
		var input = $(this).parents('form').find("[data-switch='" + id + "']");
		if($(this).val() != '') $(input).prop("readonly", false);
		else $(input).prop("readonly", true);
	});
	
	//Switch de botão movimentacao
	$('.switch-btn').on('change', function(){
		var check_num = $(this).parents('.tab-pane').find(".switch-btn:checked");
		if($(check_num).length > 0) $(this).parents('.tab-pane').find('.mov-actions').prop('disabled', false);
		else $(this).parents('.tab-pane').find('.mov-actions').prop('disabled', true);
	});

	// Carrega categorias de movimentação de acordo com o tipo selecionado
	$(".load-mov-cat").on("change", function(){
	  $(".place-mov-cat").html('<option value="" selected >Todas</option>');
	  var mov_tipo = $(this).find("option:selected").val();
	  var url = 'modules/financeiro/controlers/movCategoriaControl.php?func=getCategorias&mov_tipo='+mov_tipo;
	  getDados(url, function(dados){
	  	$(dados).each(function(i, e){
	  		var html = "<option value='"+dados[i].cat_id+"'>"+dados[i].cat_nome+"</option>";
	  		$(".place-mov-cat").append(html);
	  	});
	  });
	});
	
});