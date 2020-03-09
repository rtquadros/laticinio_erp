// JavaScript Document
$(document).ready( function() {
	function formPlugins(){
		if( !/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			// You are in mobile browser
			//Máscaras de formulário
			$('.dinheiro').attr('type', 'text').mask("###0,00", {reverse: true});
			$('.comissao').attr('type', 'text').mask("###0,000", {reverse: true});
			$('.peso').attr('type', 'text').mask("###0,00", {reverse: true});
			$('.telefone').attr('type', 'text').mask("(00) 0000-0000");
			$('.cep').attr('type', 'text').mask("00000-000");
			$('.hora').attr('type', 'text').mask("00:00");
			$('.duracao').attr('type', 'text').mask('##00:00', {reverse: true});
		}
		
		//Plugin Datepicker
		$('.datepicker').attr('type', 'text').datepicker({
			language: 'pt-BR',
			autoclose: true,
			todayHighlight: true
		});

		$('.monthpicker').attr('type', 'text').datepicker({
			language: 'pt-BR',
			autoclose: true,
			format: "mm/yyyy",
			startView: "months", 
			minViewMode: "months"
		});
		
		$('.yearpicker').attr('type', 'text').datepicker({
			language: 'pt-BR',
			autoclose: true,
			format: "yyyy",
			startView: "years", 
			minViewMode: "years"
		});
		
		$('.input-daterange input').each(function() {
			$(this).attr('type', 'text').datepicker({
				language: 'pt-BR',
				autoclose: true,
				todayHighlight: true
			});
		});
		
		//Validação de formulários
		$('form').validator();
	};
	formPlugins();
	
	//Confirmação para excluir itens
	$(this).on('click', '.delete-confirm', function(e){
		e.preventDefault();
		var a = $(this);
		bootbox.confirm({
			size: "small",
			message: "Tem certeza que quer exluir este itens?",
			buttons: {
				confirm: {
					label: 'Sim',
					className: 'btn-success'
				},
				cancel: {
					label: 'Não',
					className: 'btn-danger'
				}
			},
			callback: function (result) {
				if(result){
					//var form = $(this).parents('form');
					var href = $(a).attr("href");
					
					//$(form).submit();
					window.location = href;	
				}	
			}
		});
	});
	
	//Confirmação para atualizar item
	$(this).on('click', '.update-confirm', function(e){
		e.preventDefault();
		
		var obj = $(this);
		
		bootbox.confirm({
			size: "small",
			message: "Tem certeza que quer editar este item?",
			buttons: {
				confirm: {
					label: 'Sim',
					className: 'btn-success'
				},
				cancel: {
					label: 'Não',
					className: 'btn-danger'
				}
			},
			callback: function (result) {
				if(result){
					if($(obj).parents('form').length > 0) $(obj).parents('form').submit();
					else window.location = $(obj).attr('href');	
				}	
			}
		});
	});
	
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
	
	//botão para impressão
	$('.btnPrint').on('click', function(e){
		e.preventDefault();
		var url = $(this).attr('href');
		var win = window.open(url, '_blank');
		if (win) {
			//Browser has allowed it to be opened
			win.focus();
		} else {
			//Browser has blocked it
			alert('Please allow popups for this website');
		}	
	});
	
	$('.print').on('click', function(e){
		window.print();	
	});
	
	
	//Plugin datatable
	$('table.datatable').each(function(i,e) {
		$(this).DataTable({
			"order": [],
            "language": {
                "url": "js/datatable_ptbr.json"
            }
        })
	});
	
	// Preview de imagem 
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
	
			reader.onload = function (e) {
				$(input).parents('.imagePreview').find('img').attr('src', e.target.result);
			}
	
			reader.readAsDataURL(input.files[0]);
		}
	}
	
	$(".imagePreview input").change(function(){
		readURL(this);
	});
	
	// Formato da moeda
	var formato = { minimumFractionDigits: 2 , style: 'currency', currency: 'BRL' }
	
	//Apagar alerta
	$('.alert').delay(2000).hide('slow');
	
	//Switch de input
	$('.switch').on('change', function(){
		var id = $(this).attr('id');
		var input = $(this).parents('form').find("[data-switch='" + id + "']");
		if($(this).val() != '') $(input).prop("readonly", false);
		else $(input).prop("readonly", true);
	});
	
	//Table-hover link em linha
	$('.table-hover tbody tr').on('click', function(e){
		e.preventDefault();
		if($(this).attr('data-href')) window.location = $(this).data("href");
	});
	
});