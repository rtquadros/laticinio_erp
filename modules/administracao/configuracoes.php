
    <form enctype="multipart/form-data" method="post" action="modules/administracao/process.php?mod=<?php echo $_GET['mod']; ?>&funcao=updateConfiguracoes">
        <div class="row">
        	<div class="col-md-3">
            	<div class="imagePreview">
                	<img style="max-height:150px;height:150px;" class="img-responsive center-block img-thumbnail" alt="..." src="<?php echo $configuracoes->getConfigValue('site_logo'); ?>">
                    <div class="form-group">
                        <label for="prod_imagem">Logo do sistema</label>
                        <input type="file" class="form-control" name="imagem_upload" id="imagem_upload" placeholder="JPG, PNG ou GIF" />
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="usu_nome">Nome do sistema</label>
                    <input type="text" class="form-control" name="site_name" id="site_name" value="<?php echo $configuracoes->getConfigValue('site_name'); ?>" tabindex="1" required>
                </div>
            </div>
        </div>
        <span class="pull-right">
            <input class="btn btn-success update-confirm" type="submit" value="Editar">
            <input class="btn btn-default" type="reset" value="Limpar">
        </span>
    </form>