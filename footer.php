	<footer class="d-none d-md-block row">
	  <section class="col">
        <p class="m-0 py-1"> 
        <?php 
        echo "Versão <b>".ERP_VERSION."</b> do sistema";
	  	$filename = ABSPATH."global_config.php";
	  	if(file_exists($filename)){
	  	  echo ", última atualização em <b>" . date ("d/m/Y H:i:s", filemtime($filename))."</b>";
	  	}
	  	?>
	  	</p>
	  </section>
	</footer>

	<!-- jQuery 3.2.1 -->
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>

	<!-- Bootstrap 4.3 -->
	<script type="text/javascript" src="js/popper.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>

	<!-- Plugins jQuery -->
	<!-- Bootbox 4.4.0 - Controla caixas modais de confirmação -->
	<script type="text/javascript" src="js/bootbox.min.js"></script>

	<!-- Bootstrap Datepicker 3 - Controla iinputs de data -->
	<script type="text/javascript" src="js/bootstrap-datepicker.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-datepicker.pt-BR.min.js"></script>

	<!-- Datatables - Controla tabelas -->
	<script type="text/javascript" src="js/datatables.min.js"></script>
	<script type="text/javascript" src="js/pdfmake.min.js"></script>
	<script type="text/javascript" src="js/vfs_fonts.js"></script>

	<!-- Máscaras e validação de formulários-->
	<script type="text/javascript" src="js/jquery.mask.js"></script>
	<script type="text/javascript" src="js/validator.js"></script>

	<!-- Controladores de plugins -->
	<script type="text/javascript" src="js/controladores.js"></script>

	<?php
    // Load de scripts do módulo carregado
    if(isset($_GET["mod"])){
      $modulo = filter_input(INPUT_GET, "mod", FILTER_SANITIZE_SPECIAL_CHARS);
	  if(file_exists(MODULES_ABSPATH."/{$modulo}/js")){ 
        $js_files = scandir( MODULES_ABSPATH."/{$modulo}/js" );
	    foreach( $js_files as $file ){
	      if( is_file( MODULES_ABSPATH."/{$modulo}/js/{$file}" )){
	        echo "<script type='text/javascript' src='modules/{$modulo}/js/{$file}'></script>";
	      }
	    }
	  }
    }
	?>
  </body>
</html>
<?php
// Limpa a sessão 'Result'
unset($_SESSION['result']);
?>