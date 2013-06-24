		<div id="content" class="span10">
			<!-- content starts -->


			<div>
				<ul class="breadcrumb">
					<li>
						<a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="<?php echo base_url('panel/banco'); ?>">Banco</a> <span class="divider">/</span>
					</li>
					<li>
						Saldos
					</li>
				</ul>
			</div>

			<div class="row-fluid">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-hdd"></i> Saldos</h2>
						<div class="box-icon">
							<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
						</div>
					</div>
					<div class="box-content">
						<form action="<?php echo base_url('panel/banco'); ?>" method="get" class="form-search">
							<div class="form-actions form-filters">
								<label for="ffecha1">Del: </label>
								<input type="text" name="ffecha1" id="ffecha1" value="<?php echo set_value_get('ffecha1'); ?>" 
									placeholder="fecha 1" autofocus> 
								<label for="ffecha2">Al: </label>
								<input type="text" name="ffecha2" id="ffecha2" value="<?php echo set_value_get('ffecha2'); ?>" 
									placeholder="fecha 2">

								<button type="submit" class="btn">Buscar</button>
							</div>
						</form>

						<?php
						echo $this->usuarios_model->getLinkPrivSm('banco/agregar_operacion/', array(
										'params'   => '',
										'btn_type' => 'btn-success pull-right',
										'attrs' => array('style' => 'margin-bottom: 10px;') )
								);
						 ?>
						<table class="table table-striped table-bordered bootstrap-datatable table-fixed-header">
						  <tbody class="header">
						<?php 
						foreach($bancos as $banco){ 
						?>
								<tr style="font-weight: bold;">
									<td colspan="2" style="background-color: #e5e5e5"><?php echo $banco->nombre; ?></td>
									<td style="background-color: #e5e5e5"><?php //echo String::formatoNumero($banco->saldo); ?></td>
								</tr>
						<?php
						if(is_array($banco->cuentas))
							foreach ($banco->cuentas as $key => $cuenta) { ?>
								<tr>
									<td><?php echo $cuenta->numero; ?></td>
									<td><a href="<?php echo base_url('panel/banco/estado_cuenta?id='.$cuenta->id_cuenta.'&'.
                                String::getVarsLink(array('id', 'msg')) ); ?>"><?php echo $cuenta->alias; ?></a></td>
									<td><?php echo String::formatoNumero($cuenta->saldo); ?></td>
								</tr>
						<?php 
							}
						}?>
						  </tbody>
					  </table>

					</div>
				</div><!--/span-->

			</div><!--/row-->




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
