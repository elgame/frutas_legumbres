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
            Productor
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
            <form action="<?php echo base_url('panel/cajas/cuentas_pagar_productor/?'.String::getVarsLink(array('ffecha1', 'ffecha2', 'msg'))); ?>" method="get" class="form-search">
              <div class="form-actions form-filters">

                <div class="span6">
                  <div class="row-fluid">

                    <div class="span12">
                      <label for="ffecha1">Del:</label>
                      <input type="text" name="ffecha1" id="ffecha1" value="<?php echo $this->input->get('ffecha1'); ?>" size="1" style="width:14%;">

                      <label for="ffecha2">Al:</label>
                      <input type="text" name="ffecha2" id="ffecha2" value="<?php echo $this->input->get('ffecha2'); ?>" size="1" style="width:14%;">

                      <input type="hidden" name="id" value="<?php echo $this->input->get('id'); ?>">

                      <button type="submit" class="btn">Buscar</button>
                    </div>

                    <div class="span12" style="padding-left: 11px;">
                      <a href="<?php echo base_url('panel/cajas/cpp_pdf/?'.String::getVarsLink(array('msg'))); ?>" title="Generar PDF" target="_BLANK">
                        <img src="<?php echo base_url('application/images/otros/doc_pdf.png');?>" width="64" height="64">
                      </a>

                      <a href="<?php echo base_url('panel/cajas/cpp_xls/?'.String::getVarsLink(array('msg'))); ?>" title="Generar EXCEL" target="_BLANK">
                        <img src="<?php echo base_url('application/images/otros/doc_xls.png');?>" width="64" height="64">
                      </a>

                      <a href="#modal-abonos" role="button" data-toggle="modal" title="Abono" id="btn-modal" style="display:none;">
                        <img src="<?php echo base_url('application/images/otros/creditcard.png');?>" width="64" height="64">
                      </a>
                    </div>

                  </div>
                </div>

                <div class="span6">
                  <address>
                    <?php
                      $dir = array();
                      if (!empty($info['info']->calle)) $dir[] = 'Calle: ' . $info['info']->calle;
                      if (!empty($info['info']->no_exterior)) $dir[] = 'No. Exterior: ' . $info['info']->no_exterior;
                      if (!empty($info['info']->no_interior)) $dir[] = 'No. Interior: ' . $info['info']->no_interior;
                      if (!empty($info['info']->colonia)) $dir[] = 'Colonia: ' . $info['info']->colonia;
                      if (!empty($info['info']->municipio)) $dir[] = 'Municipio: ' . $info['info']->municipio;
                      if (!empty($info['info']->estado)) $dir[] = 'Estado: ' . $info['info']->estado;
                      if ($info['info']->cp != 0) $dir[] = 'C.P.: ' . $info['info']->cp;
                    ?>

                    <strong>Información de Productor</strong><br>
                    <strong>Nombre</strong>: <?php echo $info['info']->nombre_fiscal ?><br>
                    <strong>Domicilio</strong>: <?php echo implode(', ', $dir) ?><br>
                    <strong><abbr title="Phone">Telefono</abbr></strong>: <?php echo $info['info']->telefono ?><br>
                    <strong><abbr title="Phone">Celular</abbr></strong>: <?php echo $info['info']->celular ?><br>
                  </address>
                </div>

              </div>
            </form>

            <table class="table table-bordered bootstrap-datatable table-condensed">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>#</th>
                  <th># Ticket</th>
                  <th># Cajas</th>
                  <th>Cajas Rezaga</th>
                  <th>Kilos Recibidos</th>
                  <th>Promedio</th>
                  <th>Kilos Rezaga</th>
                  <th>Total Kilos/Cajas Pagar</th>
                  <th>Precio x Kilo/Caja</th>
                  <th>Importe</th>
                  <th>Abonos</th>
                  <th>Saldo</th>
                  <th>Tipo</th>
                  <!-- <th>Observaciones</th> -->
                </tr>
              </thead>
              <tbody id="table-tbody">

                  <?php

                    $total_entradas = (isset($productor['anterior'][0]->total_entradas)) ? $productor['anterior'][0]->total_entradas : 0;
                    $total_abonos   = (isset($productor['anterior'][0]->total_abonos)) ? $productor['anterior'][0]->total_abonos : 0;
                    $total_pagar    = (isset($productor['anterior'][0]->total_pagar)) ? $productor['anterior'][0]->total_pagar : 0;

                   ?>

                  <tr>
                    <td colspan="9" style="text-align: center;">ANTERIORES A <?php echo $_GET['ffecha1'] ?></td>
                    <td><?php echo String::formatoNumero($total_entradas); ?></td>
                    <td><?php echo String::formatoNumero($total_abonos); ?></td>
                    <td><?php echo String::formatoNumero($total_pagar); ?></td>
                    <td colspan="5"></td>
                  </tr>

            <?php
                $ttotal_cajas           = 0;
                $ttotal_cajas_rezaga    = 0;
                $ttotal_kilos_recibidos = 0;
                $ttotal_kilos_rezaga    = 0;
                $ttotal_kilos_pagar     = 0;

                $ttotal_importe         = $total_entradas;
                $ttotal_abonos          = $total_abonos;
                $ttotal_saldo           = $total_pagar;
                foreach($productor['cajas'] as $caja) {

                  $ttotal_cajas           += $caja->cajas;
                  $ttotal_cajas_rezaga    += $caja->cajas_rezaga;
                  $ttotal_kilos_recibidos += $caja->kilos;
                  $ttotal_kilos_rezaga    += $caja->kilos_rezaga;
                  $ttotal_kilos_pagar     += $caja->total_pagar_kc;

                  $ttotal_importe         += $caja->importe;
                  $ttotal_abonos          += $caja->abonos;
                  $ttotal_saldo           += $caja->saldo;

                  $promedio = 0;
                  if ($caja->variedad !== 'ATAULFO')  $promedio = round(floatval($caja->kilos) / floatval($caja->cajas), 2);

                ?>
                  <tr>
                    <td><?php echo $caja->fecha ?></td>
                    <td><a href="<?php echo base_url('panel/cajas/detalle/').'?idc='.$caja->id_caja.'&'.
                                  String::getVarsLink(array('idc')); ?>"><?php echo $caja->id_caja; ?></a></td>
                    <td><?php echo $caja->no_ticket; ?></td>
                    <td><?php echo $caja->cajas; ?></td>
                    <td><?php echo $caja->cajas_rezaga; ?></td>
                    <td><?php echo $caja->kilos; ?></td>
                    <td><?php echo $promedio ?></td>
                    <td><?php echo $caja->kilos_rezaga; ?></td>
                    <td><?php echo $caja->total_pagar_kc; ?></td>
                    <td><?php echo String::formatoNumero($caja->precio); ?></td>
                    <td><?php echo String::formatoNumero($caja->importe); ?></td>
                    <td><?php echo String::formatoNumero($caja->abonos); ?></td>
                    <td id="<?php echo ($caja->saldo > 0) ? 'masivo' : '' ?>"
                      data-id="<?php echo $caja->id_caja; ?>"
                        data-saldo="<?php echo $caja->saldo; ?>"
                          data-status="off">
                        <?php echo String::formatoNumero($caja->saldo); ?>
                    </td>
                    <td><?php echo $caja->variedad; ?></td>
                    <!-- <td><?php echo $caja->observaciones; ?></td> -->
                  </tr>
            <?php }?>

                  <tr style="font-weight: bold; font-size: 1.1em;">
                    <td style="background-color:#ccc;">TOTALES</td>
                    <td style="background-color:#ccc;"></td>
                    <td style="background-color:#ccc;"></td>
                    <td style="background-color:#ccc;"><?php echo $ttotal_cajas?></td>
                    <td style="background-color:#ccc;"><?php echo $ttotal_cajas_rezaga?></td>
                    <td style="background-color:#ccc;"><?php echo $ttotal_kilos_recibidos?></td>
                    <td style="background-color:#ccc;"></td>
                    <td style="background-color:#ccc;"><?php echo $ttotal_kilos_rezaga?></td>
                    <td style="background-color:#ccc;"><?php echo $ttotal_kilos_pagar?></td>
                    <td style="background-color:#ccc;"></td>

                    <td style="background-color:#ccc;"><?php echo String::formatoNumero($ttotal_importe); ?></td>
                    <td style="background-color:#ccc;"><?php echo String::formatoNumero($ttotal_abonos); ?></td>
                    <td style="background-color:#ccc;"><?php echo String::formatoNumero($ttotal_saldo); ?></td>
                    <td style="background-color:#ccc;"></td>
                    <td style="background-color:#ccc;"></td>
                  </tr>

              </tbody>
            </table>

          </div>
        </div><!--/span-->

      </div><!--/row-->

          <!-- content ends -->
    </div><!--/#content.span10-->


<?php echo $this->load->view('panel/cajas/abonos-modal.php', $bancos, true) ?>

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
