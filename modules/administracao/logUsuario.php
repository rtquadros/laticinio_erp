
	<div class="row">
        <div class="col-xs-12 col-md-2">
        	<p>
            <form method="post" action="">
                <div class="input-group">
                    <input type="text" class="form-control  monthpicker" name="mes_ref" id="mes_ref" value="<?php echo $mes_ref; ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn  btn-secondary"><span class="fas fa-search"></span> Buscar</button>
                    </div>
                </div>
            </form>
            </p>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm datatable">
            <thead> 
                <tr> 
                    <th>Data/hora</th> 
                    <th>Usuário</th>
                    <th>Módulo</th>
                    <th>Função</th>
                    <th>Id do Objeto</th> 
                </tr> 
            </thead> 
            <tbody> 
                <?php 
                $log = new LogAct();
                $rows = $log->selectLogAct("*", "WHERE log_data BETWEEN ? AND ?", array($data_ini_mes->format("Y-m-d"), $data_fim_mes->format("Y-m-d")));
                foreach($rows as $row){
                    $usuario = new Usuario();
                ?>
                    <tr> 
                        <th><?php echo date('d/m/Y \| H:i', strtotime($row['log_data'])); ?></th> 
                        <td><a href="?mod=administracao&pag=usuario&func=editar&usu_id=<?php echo $row['log_usuario_id']; ?>"><?php echo $usuario->getUsuNome($row['log_usuario_id']); ?></a></td>
                        <td><?php echo $row['log_mod'];?></td>
                        <td><?php echo $row['log_funcao'];?></td>
                        <td><?php echo $row['log_objeto_id'];?></td>
                    </tr> 
                <?php }?>
            </tbody>
        </table>
    </div>
