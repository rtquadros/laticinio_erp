<?php
$pessoa = new Pessoa();
?>
<h3>Controle de férias</h3>

<div class="table-responsive">
	<table class="table table-striped table-bordered table-sm datatable">
	  <thead> 
	      <tr> 
	        <th>Nome</th> 
	        <th colspan="2">Período aquisitivo</th> 
	        <th>Situação</th>
	        <th colspan="2">Período concessivo</th>
	        <th colspan="2">Período de gozo</th> 
	        <th>Situação</th>
	      </tr> 
	  </thead> 
	  <tbody> 
	    <?php 
	    $rows = $pessoa->selectPessoa("*", "WHERE pessoa_categoria=? ORDER BY pessoa_nome ASC", array("funcionario"));
	    foreach($rows as $row){
	        $pessoa_variaveis = unserialize($row['pessoa_variaveis']);
	        $data_atual = new DateTime();
	        $data_admissao = new DateTime($pessoa_variaveis["data_admissao"]);
	        $data_ref = new DateTime($data_admissao->format(date("Y")."-m-d"));
	        $intervalo = new DateInterval("P364D");

	        // Formando o período aquisitivo
	        $data_ref->sub(new DateInterval("P2Y"));
	        $periodo_aquisitivo["ini"] = $data_ref->format("d/m/Y");
	        $data_ref->add($intervalo);
	        $periodo_aquisitivo["fim"] = $data_ref->format("d/m/Y");

	        // Formando o período concessivo
	        $data_ref->modify("+1 day");
	        $periodo_concessivo["ini"] = $data_ref->format("d/m/Y");
	        $data_ref->add($intervalo);
	        $periodo_concessivo["fim"] = $data_ref->format("d/m/Y");
	    ?>
	      <tr>
	          <td><a href="?mod=pessoas&pag=colaborador&func=editar&pessoa_id=<?php echo $row['pessoa_id']; ?>"><?php echo $row['pessoa_nome']; ?></a></td>
	          <td><?php echo $periodo_aquisitivo["ini"]; ?></td>
	          <td><?php echo $periodo_aquisitivo["fim"]; ?></td>
	          <td>
	          <?php 
	          if($data_atual > $periodo_aquisitivo["fim"]) echo "Férias a agendar";
	          ?>
	          </td>
	          <td><?php echo $periodo_concessivo["ini"]; ?></td>
	          <td><?php echo $periodo_concessivo["fim"]; ?></td>
	          <td><?php echo isset($pessoa_variaveis['bonificacao']) && !empty($pessoa_variaveis['bonificacao']) ? 'R$ '.number_format($pessoa_variaveis["bonificacao"], 2, ',', '.') : '';?></td>
	          <td><a href="modules/pessoas/controlers/colaboradorControl.php?func=deletePessoa&pessoa_id=<?php echo $row['pessoa_id']; ?>" class="text-danger delete-confirm"><span class="fas fa-trash"></span><span class="sr-only">Excluir</span></a></td>
	      </tr> 
	    <?php }?>
	  </tbody>
	</table>
</div>