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
	
	// Carregar valor do leite
	$('#leite_prod_id').on('change', function(e){
		var input = $(this);
		var url = 'modules/captacao/controlers/leiteControl.php?func=ajaxGetLeite&prod_id='+$(this).find('option:selected').val();
		getDados(url, function(retorno){
			$(input).parents('form').find('#leite_preco').val(retorno.leite_preco);
			$(input).parents('form').find("#leite_linha_id option").attr("selected", false);
			$(input).parents('form').find('#leite_linha_id option[value="'+retorno.linha_id+'"]').attr("selected",true);
		});	
	});
	
});