		<div id="content" class="span10">
			<!-- content starts -->


			<div>
				<ul class="breadcrumb">
					<li>
						<a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="<?php echo base_url('panel/variedades/'); ?>">Variedades</a> <span class="divider">/</span>
					</li>
					<li>Agregar Variedad</li>
				</ul>
			</div>

			<form action="<?php echo base_url('panel/variedades/agregar'); ?>" method="post" class="form-horizontal">
				<div class="row-fluid">
					<div class="box span12">
						<div class="box-header well" data-original-title>
							<h2><i class="icon-leaf"></i> Variedad</h2>
							<div class="box-icon">
								<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
							</div>
						</div>
						<div class="box-content">
							  <fieldset>
									<legend></legend>

									<div class="control-group">
										<label class="control-label" for="dnombre">Nombre:</label>
										<div class="controls">
											<input type="text" name="dnombre" id="dnombre" class="span6" 
												value="<?php echo set_value('dnombre'); ?>" maxlength="40" placeholder="ataulfo, haden" required autofocus>
										</div>
									</div>

									<div class="control-group tipo3">
									  <label class="control-label" for="dtipo">Pagar por:</label>
									  <div class="controls">
											<select name="dtipo" id="dtipo" required>
												<option value="k" <?php echo set_select('dtipo', 'k', false, $this->input->post('dtipo')); ?>>Kilos</option>
												<option value="c" <?php echo set_select('dtipo', 'c', false, $this->input->post('dtipo')); ?>>Cajas</option>
											</select>
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
