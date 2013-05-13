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
					<li>Agregar Operación</li>
				</ul>
			</div>

			<form action="<?php echo base_url('panel/banco/agregar_operacion?'.String::getVarsLink(array('msg', 'id_mov'))); ?>" method="post" class="form-inline">
				<div class="row-fluid">
					<div class="box span12">
						<div class="box-header well" data-original-title>
							<h2><i class="icon-plus-sign"></i> Operación</h2>
							<div class="box-icon">
								<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
							</div>
						</div>
						<div class="box-content">
							  <fieldset>
									<legend>Datos de la operación</legend>

									<div class="row-fluid">
										<div class="control-group span4 nomarg">
											<label class="control-label" for="dfecha">Fecha:</label>
											<div class="controls">
												<!-- <input type="text" name="dfecha" id="dfecha" class="span6" 
													value="<?php echo set_value('dfecha'); ?>" maxlength="40" required autofocus> -->
												<input type="datetime-local" name="dfecha" id="dfecha" class="span8" 
													value="<?php echo set_value('dfecha', str_replace(' ', 'T', date("Y-m-d H:i")) ); ?>" maxlength="40" required autofocus>
											</div>
										</div>

										<div class="control-group span4 nomarg">
											<label class="control-label" for="dbanco">Banco:</label>
											<div class="controls">
												<select name="dbanco" id="dbanco" required>
													<option value=""></option>
											<?php 
											foreach ($bancos['bancos'] as $key => $value) {
												echo '<option value="'.$value->id_banco.'" '.set_select('dbanco', $value->id_banco, false, $dbanco_load).'>'.$value->nombre.'</option>';
											}
											?>
												</select>
											</div>
										</div>

										<div class="control-group span4 nomarg">
											<label class="control-label" for="dcuenta">Cuenta:</label>
											<div class="controls">
												<select name="dcuenta" id="dcuenta" required>
												</select>
												<span style="display: none;" id="dcuenta_load"><?php echo $dcuenta_load; ?></span>
											</div>
										</div>
									</div>

									<div class="row-fluid mtop">
										<div class="control-group span4 nomarg">
											<label class="control-label" for="dconcepto">Concepto:</label>
											<div class="controls">
												<textarea name="dconcepto" id="dconcepto" class="span11" 
													required><?php 
													$concepto = (isset($fac['info']->folio)? 'Pago de la factura '.
														($fac['info']->serie!=''? $fac['info']->folio.'-': '').$fac['info']->folio: '');
													echo set_value('dconcepto', $concepto); ?></textarea>
											</div>
										</div>

										<div class="control-group span2 nomarg">
											<label class="control-label" for="dmonto">Monto:</label>
											<div class="controls">
												<input type="text" name="dmonto" id="dmonto" class="span11 vpositive" 
													value="<?php echo set_value('dmonto', (isset($fac['info']->total)? $fac['info']->total: 0) ); ?>" maxlength="40" required>
											</div>
										</div>

										<div class="control-group span3 nomarg">
			                <label class="control-label" for="dmetodo_pago">Metodo de pago</label>
			                <div class="controls">
			                	<?php $metodo_pago = (isset($fac['info']->metodo_pago)? $fac['info']->metodo_pago: ''); ?>
			                  <select name="dmetodo_pago" class="span11" id="dmetodo_pago">
			                    <option value="efectivo" <?php echo set_select('dmetodo_pago', 'efectivo', false, $metodo_pago); ?>>Efectivo</option>
			                    <option value="cheque" <?php echo set_select('dmetodo_pago', 'cheque', false, $metodo_pago); ?>>Cheque</option>
			                    <option value="tarjeta" <?php echo set_select('dmetodo_pago', 'tarjeta', false, $metodo_pago); ?>>Tarjeta</option>
			                    <option value="transferencia" <?php echo set_select('dmetodo_pago', 'transferencia', false, $metodo_pago); ?>>Transferencia</option>
			                    <option value="deposito" <?php echo set_select('dmetodo_pago', 'deposito', false, $metodo_pago); ?>>Deposito</option>
			                  </select>
			                </div>
			              </div>

			              <div class="control-group span3 nomarg">
			                <label class="control-label" for="dtipo_operacion">Tipo de Operación</label>
			                <div class="controls">
			                  <select name="dtipo_operacion" class="span11" id="dtipo_operacion">
			                    <option value="d" <?php echo set_select('dtipo_operacion', 'd', false, $tipo_operacion); ?>>Deposito</option>
			                    <option value="r" <?php echo set_select('dtipo_operacion', 'r', false, $tipo_operacion); ?>>Retiro</option>
			                  </select>
			                </div>
			              </div>

			              <div class="control-group span4 nomarg only_cheques hide" style="padding-top: 10px;">
											<label class="control-label" for="dchk_anombre">A nombre de:</label>
											<div class="controls">
												<input type="text" name="dchk_anombre" id="dchk_anombre" class="span11" 
													value="<?php echo set_value('dchk_anombre', (isset($fac['info']->productor->nombre_fiscal)? $fac['info']->productor->nombre_fiscal: '') ); ?>" maxlength="100">
											</div>
										</div>

										<div class="control-group span2 nomarg only_cheques hide" style="padding-top: 10px;">
											<label class="control-label" for="dmoneda">Moneda:</label>
											<div class="controls">
												<select name="dmoneda" class="span11" id="dmoneda">
			                    <option value="M.N." <?php echo set_select('dmoneda', 'M.N.'); ?>>M.N.</option>
			                    <option value="USD" <?php echo set_select('dmoneda', 'USD'); ?>>USD</option>
			                  </select>
											</div>
										</div>

										<div class="control-group span2 nomarg only_cheques hide" style="padding-top: 10px;">
											<label class="control-label" for="dabono_cuenta">Para abono en cuenta:</label>
											<div class="controls">
												<input type="checkbox" name="dabono_cuenta" id="dabono_cuenta" value="1" <?php echo set_checkbox('dabono_cuenta', '1'); ?>>
											</div>
										</div>

									</div>

							  </fieldset>

							  <fieldset>
							  	<legend>Conceptos Reales</legend>

							  	<div>
							  		<input type="text" id="addcons_concep" maxlength="254" placeholder="Concepto"> 
							  		<input type="text" id="addcons_monto" class="vpositive" placeholder="Monto ($)">
							  		<button type="button" class="btn" id="addcons_agregar">Agregar</button>
							  	</div>

							  	<table class="table table-striped table-bordered table-hover table-condensed" style="margin-top: 10px;" id="table_prod">
		                <caption></caption>
		                <thead>
		                  <tr>
		                    <th>Concepto</th>
		                    <th>Monto</th>
		                    <th></th>
		                  </tr>
		                </thead>
		                <tbody>
		               <?php
		               if(is_array($this->input->post('dconcep_conce')))
			               foreach ($this->input->post('dconcep_conce') as $key => $value) {
			               	echo '<tr id="prd'.$key.'">'+
			                  '<td><input type="text" name="dconcep_conce[]" value="'.$value.'" class="span12" maxlength="254"></td>'+
			                  '<td><input type="text" name="dconcep_monto[]" value="'.$_POST['dconcep_monto'][$key].'" class="span12 vpositive"></td>'+
			                  '<td><button type="button" class="btn btn-danger" data-id="'.$key.'"><i class="icon-trash"></i></button></td>'+
			                '</tr>';
		               }
		               ?>
		                </tbody>
		                <tfoot>
		                	<tr>
		                		<td></td>
		                		<td id="total_concepts" style="font-weight: bold"></td>
		                	</tr>
		                </tfoot>
		              </table>

							  </fieldset>

						</div>
					</div><!--/box span-->

				</div><!--/row-->

				<div class="form-actions">
				  <button type="submit" class="btn btn-primary">Guardar</button>
				  <a href="<?php echo base_url('panel/banco/'); ?>" class="btn">Cancelar</a>
				</div>

			</form>


					<!-- content ends -->
		</div><!--/#content.span10-->



<!-- Bloque de alertas -->
<?php if(isset($frm_errors)){
	if($frm_errors['msg'] != ''){
?>
<script type="text/javascript" charset="UTF-8">
	<?php if($frm_errors['ico'] === 'success' && isset($_GET['met_pago'])) {
		if($_GET['met_pago'] == 'cheque')
    	echo 'window.open("'.base_url('panel/banco/print_cheque/?id='.$_GET['id_mov']).'");';
  }?>

	$(document).ready(function(){
		noty({"text":"<?php echo $frm_errors['msg']; ?>", "layout":"topRight", "type":"<?php echo $frm_errors['ico']; ?>"});
	});
</script>
<?php }
}?>
<!-- Bloque de alertas -->
