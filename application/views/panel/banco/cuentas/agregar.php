		<div id="content" class="span10">
			<!-- content starts -->


			<div>
				<ul class="breadcrumb">
					<li>
						<a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="<?php echo base_url('panel/banco/'); ?>">Banco</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="<?php echo base_url('panel/banco/cuentas/'); ?>">Cuentas</a> <span class="divider">/</span>
					</li>
					<li>Agregar Cuenta</li>
				</ul>
			</div>

			<form action="<?php echo base_url('panel/banco/agregar_cuenta'); ?>" method="post" class="form-horizontal">
				<div class="row-fluid">
					<div class="box span12">
						<div class="box-header well" data-original-title>
							<h2><i class="icon-inbox"></i> Información de la cuenta</h2>
							<div class="box-icon">
								<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
							</div>
						</div>
						<div class="box-content">
							  <fieldset>
									<legend></legend>

									<div class="control-group">
		                <label class="control-label" for="dbanco">Banco:</label>
		                <div class="controls">
		                  <select name="dbanco" id="dbanco" required autofocus>
		                    <option value=""></option>
		                    <?php 
		                    foreach ($bancos['bancos'] as $key => $value) {
		                      echo '<option value="'.$value->id_banco.'" '.set_select('dbanco', $value->id_banco).'>'.$value->nombre.'</option>';
		                    }
		                    ?>
		                  </select>
		                </div>
		              </div>

									<div class="control-group">
										<label class="control-label" for="dnumero">Numero:</label>
										<div class="controls">
											<input type="text" name="dnumero" id="dnumero" class="span6" 
												value="<?php echo set_value('dnumero'); ?>" maxlength="20" placeholder="049382, 083281923" required>
										</div>
									</div>

									<div class="control-group">
										<label class="control-label" for="dalias">Alias:</label>
										<div class="controls">
											<input type="text" name="dalias" id="dalias" class="span6" 
												value="<?php echo set_value('dalias'); ?>" maxlength="40" placeholder="Cuenta personal, Cuenta 1" required>
										</div>
									</div>

							  </fieldset>

						</div>
					</div><!--/box span-->

				</div><!--/row-->

				<div class="form-actions">
				  <button type="submit" class="btn btn-primary">Guardar</button>
				  <a href="<?php echo base_url('panel/variedades/'); ?>" class="btn">Cancelar</a>
				</div>

			</form>


					<!-- content ends -->
		</div><!--/#content.span10-->



<!-- Bloque de alertas -->
<?php if(isset($frm_errors)){
	if($frm_errors['msg'] != ''){
?>
<script type="text/javascript" charset="UTF-8">
	$(document).ready(function(){
		noty({"text":"<?php echo $frm_errors['msg']; ?>", "layout":"topRight", "type":"<?php echo $frm_errors['ico']; ?>"});
	});
</script>
<?php }
}?>
<!-- Bloque de alertas -->
