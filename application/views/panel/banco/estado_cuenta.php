		<div id="content" class="span10">
			<!-- content starts -->


			<div>
				<ul class="breadcrumb">
					<li>
						<a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="<?php echo base_url('panel/banco?'.String::getVarsLink(array('id', 'msg'))); ?>">Banco</a> <span class="divider">/</span>
					</li>
					<li>
						Estado de ceunta
					</li>
				</ul>
			</div>

			<div class="row-fluid">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-hdd"></i> Estado de ceutna | <?php echo $movimientos['cuenta']->banco.' ('.$movimientos['cuenta']->alias.')' ?></h2>
						<div class="box-icon">
							<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
						</div>
					</div>
					<div class="box-content">
						<a href="<?php echo base_url('panel/banco?'.String::getVarsLink(array('id', 'msg'))); ?>"><i class="icon-chevron-left"></i> Atras</a>
						<form action="<?php echo base_url('panel/banco/estado_cuenta'); ?>" method="get" class="form-search">
							<div class="form-actions form-filters">
								<label for="ffecha1">Del: </label>
								<input type="text" name="ffecha1" id="ffecha1" value="<?php echo set_value_get('ffecha1'); ?>" 
									placeholder="fecha 1" autofocus> 
								<label for="ffecha2">Al: </label>
								<input type="text" name="ffecha2" id="ffecha2" value="<?php echo set_value_get('ffecha2'); ?>" 
									placeholder="fecha 2">

								<input type="hidden" name="id" id="id" value="<?php echo set_value_get('id'); ?>">

								<button type="submit" class="btn">Buscar</button>

								<a href="<?php echo base_url('panel/banco/estado_cuenta_pdf?'.String::getVarsLink(array('msg'))); ?>" class="pull-right" title="Generar PDF" target="_BLANK">
                  <img src="<?php echo base_url('application/images/otros/doc_pdf.png'); ?>" width="64" height="64">
                </a>
							</div>
						</form>

						<?php
						echo $this->usuarios_model->getLinkPrivSm('banco/agregar_operacion/', array(
										'params'   => '',
										'btn_type' => 'btn-success pull-right',
										'attrs' => array('style' => 'margin-bottom: 10px;') )
								);
						 ?>
						<table class="table table-striped table-bordered bootstrap-datatable">
							<thead>
							  <tr>
								  <th>Fecha</th>
									<th>Concepto</th>
									<th>Retiros</th>
									<th>Depósitos</th>
								  <th>Saldo</th>
								  <th></th>
							  </tr>
						  </thead>
						  <tbody>
						<?php 
						foreach($movimientos['movimientos'] as $key => $mov){
						?>
								<tr <?php echo (count($mov['conceptos'])>0? 'class="show_consr"': '').' data-toggle="consr'.$mov['id_movimiento'].'"'; ?>>
								  <td><?php echo $mov['fecha']; ?></td>
									<td><?php echo $mov['concepto']; ?></td>
									<td><?php echo $mov['retiros']; ?></td>
									<td><?php echo $mov['depositos']; ?></td>
								  <td><?php echo $mov['saldo']; ?></td>
								  <td>
								  <?php
								  	if($key > 0)
								  	echo $this->usuarios_model->getLinkPrivSm('banco/eliminar_operacion/', array(
												'params'   => 'id_mov='.$mov['id_movimiento'].'&'.String::getVarsLink(array('id_mov', 'msg')),
												'btn_type' => 'btn-danger',
												'attrs' => array('onclick' => "msb.confirm('Estas seguro de eliminar la operacion?', '', this); return false;"))
										);
								  ?>
								  </td>
							  </tr>
						<?php 
							if(count($mov['conceptos']) > 0){
								foreach ($mov['conceptos'] as $key1 => $cons) {
						?>
								<tr class="conceptos_reales consr<?php echo $mov['id_movimiento']; ?>">
								  <td></td>
									<td><?php echo $cons->concepto; ?></td>
									<td colspan="2"><?php echo $cons->monto; ?></td>
									<td></td>
								  <td></td>
							  </tr>
						<?php
								}
							}
						}?>
								<tr>
								  <td></td>
									<td style="text-align: right;">Total:</td>
									<td><?php echo $movimientos['retiros']; ?></td>
									<td><?php echo $movimientos['depositos']; ?></td>
								  <td></td>
								  <td></td>
							  </tr>
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
