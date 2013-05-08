    <div id="content" class="span10">
      <!-- content starts -->

      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            <a href="<?php echo base_url('panel/cajas/cuentas_pagar'); ?>" title="">Cuentas por Pagar</a> <span class="divider">/</span>
          </li>
           <li>
            <a href="<?php echo base_url('panel/cajas/cuentas_pagar_productor/?'.String::getVarsLink(array('idc'))); ?>" title="">Productor</a> <span class="divider">/</span>
          </li>
          <li>
            Detalle de Entrega
          </li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-user"></i> <?php echo $info['info']->nombre_fiscal ?></h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form action="<?php echo base_url('panel/cajas/detalle/?'.String::getVarsLink(array('ffecha1', 'ffecha2', 'msg'))); ?>" method="get" class="form-search">
              <div class="form-actions form-filters">

                <div class="span6">
                  <div class="row-fluid">

                    <div class="span12">
                      <label for="ffecha1">Del:</label>
                      <input type="text" name="ffecha1" id="ffecha1" value="<?php echo $this->input->get('ffecha1'); ?>" size="1" style="width:14%;">

                      <label for="ffecha2">Al:</label>
                      <input type="text" name="ffecha2" id="ffecha2" value="<?php echo $this->input->get('ffecha2'); ?>" size="1" style="width:14%;">

                      <input type="hidden" name="id" value="<?php echo $this->input->get('id'); ?>">
                      <input type="hidden" name="idc" value="<?php echo $this->input->get('idc'); ?>">

                      <button type="submit" class="btn">Buscar</button>
                    </div>

                    <div class="span12" style="padding-left: 11px;">
                      <a href="<?php echo base_url('panel/cajas/detalle_pdf/?'.String::getVarsLink(array('msg'))); ?>" title="Generar PDF" target="_BLANK">
                        <img src="<?php echo base_url('application/images/otros/doc_pdf.png');?>" width="64" height="64">
                      </a>

                      <!-- <a href="<?php echo base_url('panel/cajas/detalle_xls/?'.String::getVarsLink(array('msg'))); ?>" title="Generar EXCEL" target="_BLANK">
                        <img src="<?php echo base_url('application/images/otros/doc_xls.png');?>" width="64" height="64">
                      </a> -->
                    </div>

                  </div>
                </div>

                <div class="span6">
                  <address>
                    <strong>Información de Entrega</strong><br>
                    <strong>Fecha</strong>: <?php echo substr($entrega['info']->fecha, 0, 10)?><br>

                    <strong>Dueño Carga</strong>: <?php echo $entrega['info']->dueno_carga ?><br>

                    <strong>Certificado Tarjeta</strong>: <?php echo $entrega['info']->certificado_tarjeta ?>
                    <strong>Codigo Huerta</strong>: <?php echo $entrega['info']->codigo_huerta ?>
                    <strong>No Lote</strong>: <?php echo $entrega['info']->no_lote ?><br>

                    <strong>No Tcket</strong>: <?php echo $entrega['info']->no_ticket ?>
                    <strong>Organico?</strong>: <?php echo $entrega['info']->es_organico === 1 ? 'Si' : 'No' ?>
                    <strong>Unidad Transporte</strong>: <?php echo $entrega['info']->unidad_transporte ?><br>

                    <strong>Observaciones</strong>: <?php echo $entrega['info']->observaciones ?><br>

                    <strong>Tratamientos</strong>: <?php foreach ($entrega['info']->tratamientos as $t) echo $t->nombre."[{$t->cantidad}], " ?>

                  </address>
                </div>

              </div>
            </form>

            <?php $total_entrega = floatval($entrega['info']->total_pagar_kc) * floatval($entrega['info']->precio) ; ?>
            <table class="table table-bordered bootstrap-datatable table-condensed">
              <thead>
                <tr style="font-weight: bold; font-size: 1.1em;background-color:#ccc;">
                  <th></th>
                  <th colspan="2">TOTAL: <?php echo String::formatoNumero($total_entrega) ?></th>
                </tr>
                <tr>
                  <th>Fecha</th>
                  <th>Abono</th>
                  <th>Saldo</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    $total_abono = 0;
                    $total_saldo = $total_entrega;
                    foreach($abonos['abonos'] as $abono) {
                      $total_abono += floatval($abono->abono);
                      $total_saldo -= floatval($abono->abono);
                    ?>
                      <tr>
                        <td><?php echo $abono->fecha ?></td>
                        <td><?php echo String::formatoNumero($abono->abono); ?></td>
                        <td><?php echo String::formatoNumero($total_saldo); ?></td>
                      </tr>
                <?php }?>

                      <tr style="font-weight: bold; font-size: 1.1em;">
                        <td style="background-color:#ccc;">TOTAL</td>
                        <td style="background-color:#ccc;"><?php echo String::formatoNumero($total_abono); ?></td>
                        <td style="background-color:#ccc;"><?php echo String::formatoNumero($total_saldo); ?></td>
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
