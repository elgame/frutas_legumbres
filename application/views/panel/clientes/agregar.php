		<div id="content" class="span10">
			<!-- content starts -->


			<div>
				<ul class="breadcrumb">
					<li>
						<a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="<?php echo base_url('panel/clientes/'); ?>">Clientes</a> <span class="divider">/</span>
					</li>
					<li>Agregar cliente</li>
				</ul>
			</div>

			<form action="<?php echo base_url('panel/clientes/agregar'); ?>" method="post" class="form-horizontal">
				<div class="row-fluid">
					<div class="box span12">
						<div class="box-header well" data-original-title>
							<h2><i class="icon-list-alt"></i> Información Facturación</h2>
							<div class="box-icon">
								<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
							</div>
						</div>
						<div class="box-content">
							  <fieldset>
									<legend></legend>

									<div class="span6 mquit">
										<div class="control-group">
											<label class="control-label" for="dnombre_fiscal">*Nombre Fiscal:</label>
											<div class="controls">
												<input type="text" name="dnombre_fiscal" id="dnombre_fiscal" class="span12" 
													value="<?php echo set_value('dnombre_fiscal'); ?>" maxlength="120" autofocus>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="drfc">*RFC:</label>
											<div class="controls">
												<input type="text" name="drfc" id="drfc" class="span12" value="<?php echo set_value('drfc'); ?>" maxlength="13">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dcalle">*Calle:</label>
											<div class="controls">
												<input type="text" name="dcalle" id="dcalle" class="span12" value="<?php echo set_value('dcalle'); ?>" maxlength="60">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dno_exterior">*No exterior:</label>
											<div class="controls">
												<input type="text" name="dno_exterior" id="dno_exterior" class="span12" 
													value="<?php echo set_value('dno_exterior'); ?>" maxlength="8">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dno_interior">No interior:</label>
											<div class="controls">
												<input type="text" name="dno_interior" id="dno_interior" class="span12" 
													value="<?php echo set_value('dno_interior'); ?>" maxlength="8">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dcolonia">*Colonia:</label>
											<div class="controls">
												<input type="text" name="dcolonia" id="dcolonia" class="span12" value="<?php echo set_value('dcolonia'); ?>" 
													maxlength="80">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dmunicipio">*Municipio / Delegación:</label>
											<div class="controls">
												<input type="text" name="dmunicipio" id="dmunicipio" class="span12" 
													value="<?php echo set_value('dmunicipio'); ?>" maxlength="60">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="destado">*Estado:</label>
											<div class="controls">
												<input type="text" name="destado" id="destado" class="span12" 
													value="<?php echo set_value('destado'); ?>" maxlength="60">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dcp">CP:</label>
											<div class="controls">
												<input type="text" name="dcp" id="dcp" class="span12" value="<?php echo set_value('dcp'); ?>" maxlength="10">
											</div>
										</div>

									</div> <!--/span-->

									<div class="span6 mquit">
										<div class="control-group">
											<label class="control-label" for="dtelefono">Teléfono:</label>
											<div class="controls">
												<input type="text" name="dtelefono" id="dtelefono" class="span12" 
													value="<?php echo set_value('dtelefono'); ?>" maxlength="20">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dcelular">Celular:</label>
											<div class="controls">
												<input type="text" name="dcelular" id="dcelular" class="span12" 
													value="<?php echo set_value('dcelular'); ?>" maxlength="20">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="demail">Email:</label>
											<div class="controls">
												<input type="text" name="demail" id="demail" class="span12" 
													value="<?php echo set_value('demail'); ?>" maxlength="70">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="ddescuento">Descuento:</label>
											<div class="controls">
												<input type="text" name="ddescuento" id="ddescuento" class="span6 vpositive pull-left" value="<?php echo set_value('ddescuento'); ?>" maxlength="3"> %
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="denviar_factura">Enviar factura:</label>
											<div class="controls">
												<input type="checkbox" name="denviar_factura" id="denviar_factura" class="span6 pull-left" 
													value="1" <?php echo set_checkbox('denviar_factura', '1'); ?>>
											</div>
										</div>

		              </div> <!--/span-->

							  </fieldset>

						</div>
					</div><!--/box span-->

				</div><!--/row-->

				<div class="row-fluid">
					<div class="box span12">
						<div class="box-header well" data-original-title>
							<h2><i class="icon-list-alt"></i> Información Empresa</h2>
							<div class="box-icon">
								<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
							</div>
						</div>
						<div class="box-content">
							  <fieldset>
									<legend></legend>

									<div class="control-group">
										<label class="control-label" for="demismos_facturacion">Los mismos de Facturación:</label>
										<div class="controls">
											<input type="checkbox" name="demismos_facturacion" id="demismos_facturacion">
										</div>
									</div>

									<div class="control-group">
										<label class="control-label" for="denombre">Nombre:</label>
										<div class="controls">
											<input type="text" name="denombre" id="denombre" class="span6" value="<?php echo set_value('denombre'); ?>" maxlength="120">
										</div>
									</div>

									<div class="control-group">
										<label class="control-label" for="decalle">Calle:</label>
										<div class="controls">
											<input type="text" name="decalle" id="decalle" class="span6" value="<?php echo set_value('decalle'); ?>" maxlength="80">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="deno_exterior">No exterior:</label>
										<div class="controls">
											<input type="text" name="deno_exterior" id="deno_exterior" class="span6" value="<?php echo set_value('deno_exterior'); ?>" 
												maxlength="8">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="deno_interior">No interior:</label>
										<div class="controls">
											<input type="text" name="deno_interior" id="deno_interior" class="span6" value="<?php echo set_value('deno_interior'); ?>" 
												maxlength="8">
										</div>
									</div>

									<div class="control-group">
										<label class="control-label" for="decolonia">Colonia:</label>
										<div class="controls">
											<input type="text" name="decolonia" id="decolonia" class="span6" value="<?php echo set_value('decolonia'); ?>" 
												maxlength="80">
										</div>
									</div>

									<div class="control-group">
										<label class="control-label" for="demunicipio">Municipio / Delegación:</label>
										<div class="controls">
											<input type="text" name="demunicipio" id="demunicipio" class="span6" value="<?php echo set_value('demunicipio'); ?>" 
												maxlength="60">
										</div>
									</div>

									<div class="control-group">
										<label class="control-label" for="deestado">Estado:</label>
										<div class="controls">
											<input type="text" name="deestado" id="deestado" class="span6" value="<?php echo set_value('deestado'); ?>" 
												maxlength="60">
										</div>
									</div>

									<div class="control-group">
										<label class="control-label" for="decp">CP:</label>
										<div class="controls">
											<input type="text" name="decp" id="decp" class="span6" value="<?php echo set_value('decp'); ?>" maxlength="10">
										</div>
									</div>
							  </fieldset>

						</div>
					</div><!--/box span-->

				</div><!--/row-->

				<div class="form-actions">
				  <button type="submit" class="btn btn-primary">Guardar</button>
				  <a href="<?php echo base_url('panel/clientes/'); ?>" class="btn">Cancelar</a>
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
