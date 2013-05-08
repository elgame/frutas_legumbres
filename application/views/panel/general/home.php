		<div id="content" class="span10">
			<!-- content starts -->

			<div>
				<ul class="breadcrumb">
					<li>
						<a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
					</li>
					<li>
						Panel principal
					</li>
				</ul>
			</div>

			<div class="sortable row-fluid">
        <a data-rel="tooltip" title="<?php echo $venta_dia; ?>" class="well span3 top-block">
          <span class="icon32 icon-red icon-shopping-cart"></span>
          <div>Ventas del dia</div>
          <div><?php echo String::formatoNumero($venta_dia); ?></div>
        </a>

				<a data-rel="tooltip" title="<?php echo $venta_semana; ?>" class="well span3 top-block">
					<span class="icon32 icon-red icon-shopping-cart"></span>
					<div>Ventas semanal</div>
					<div><?php echo String::formatoNumero($venta_semana); ?></div>
				</a>

				<a data-rel="tooltip" title="<?php echo $venta_mes; ?>" class="well span3 top-block">
					<span class="icon32 icon-color icon-shopping-cart"></span>
					<div>Ventas del mes</div>
					<div><?php echo String::formatoNumero($venta_mes); ?></div>
				</a>
      <?php 
      $tienep = $this->usuarios_model->tienePrivilegioDe('', 'reportes/bajos_inventario/');
      ?>
        <a <?php echo ($tienep? 'href="'.base_url('panel/reportes/bajos_inventario').'" target="_blank"': ''); ?> data-rel="tooltip" title="<?php echo $bajos_inventario; ?>" class="well span3 top-block">
          <span class="icon32 icon-color icon-th-list"></span>
          <div>Productos bajos inventario</div>
          <div><?php echo $bajos_inventario; ?></div>
        </a>
			</div>

      <div class="row-fluid sortable">
        <div class="box span6">
          <div class="box-header well">
            <h2>Productos mas vendidos</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form class="form-inline" id="frmmas_vendidos">
              <input type="text" class="input-small fecha1" placeholder="2013-02-01">
              <input type="text" class="input-small fecha2" placeholder="2013-02-20">
              <button type="submit" class="btn">Enviar</button>
            </form>

            <table class="table table-striped table-bordered bootstrap-datatable">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Cantidad</th>
                  <th>Importe</th>
                </tr>
              </thead>
              <tbody id="product_mas_vend">
            <?php 
            if (isset($mas_vendidos))
              foreach($mas_vendidos as $product) {?>
                <tr>
                  <td><?php echo $product->nombre; ?></td>
                  <td><?php echo $product->cantidad; ?></td>
                  <td><?php echo String::formatoNumero($product->importe); ?></td>
                </tr>
            <?php }?>
              </tbody>
            </table>

          </div>
        </div><!--/span-->

        <div class="box span6">
          <div class="box-header well">
            <h2>Productos menos vendidos</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form class="form-inline" id="frmmenos_vendidos">
              <input type="text" class="input-small fecha1" placeholder="2013-02-01">
              <input type="text" class="input-small fecha2" placeholder="2013-02-20">
              <button type="submit" class="btn">Enviar</button>
            </form>

            <table class="table table-striped table-bordered bootstrap-datatable">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Cantidad</th>
                  <th>Importe</th>
                </tr>
              </thead>
              <tbody id="product_menos_vend">
            <?php 
            if (isset($menos_vendidos))
              foreach($menos_vendidos as $product) {?>
                <tr>
                  <td><?php echo $product->nombre; ?></td>
                  <td><?php echo $product->cantidad; ?></td>
                  <td><?php echo String::formatoNumero($product->importe); ?></td>
                </tr>
            <?php }?>
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