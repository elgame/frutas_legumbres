    <div id="content" class="span10">
      <!-- content starts -->

      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            <a href="<?php echo base_url('panel/cajas_carton/'); ?>" title="">Cajas Cartón</a> <span class="divider">/</span>
          </li>
          <li>
            Productor
          </li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-user"></i> <?php echo $marca['info']->nombre ?> | Movimientos de Cajas</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">

            <a href="<?php echo base_url('panel/cajas_carton/?'.String::getVarsLink(array('id', 'msg')) ); ?>"><i class="icon-chevron-left"></i> Atras</a>

            <form action="<?php echo base_url('panel/cajas_carton/marca/?'.String::getVarsLink(array('ffecha1', 'ffecha2', 'msg'))); ?>" method="get" class="form-search">
              <div class="form-actions form-filters">

                <div class="span12">
                  <div class="row-fluid">

                    <div class="span12">
                      <label for="ffecha1">Del:</label>
                      <input type="text" name="ffecha1" id="ffecha1" value="<?php echo $this->input->get('ffecha1'); ?>" size="1" style="width:14%;">

                      <label for="ffecha2">Al:</label>
                      <input type="text" name="ffecha2" id="ffecha2" value="<?php echo $this->input->get('ffecha2'); ?>" size="1" style="width:14%;">

                      <input type="hidden" name="id" value="<?php echo $this->input->get('id'); ?>">

                      <label for="ide">Empacador</label>
                      <select name="ide">
                        <option value=""></option>}
                        <?php foreach ($empacadores as $e){ ?>
                          <option value="<?php echo $e->id_empacador ?>" <?php echo set_select('ide', $e->id_empacador, false, $this->input->get('ide')); ?>><?php echo $e->nombre ?></option>
                        <?php } ?>
                      </select>

                      <button type="submit" class="btn">Buscar</button>
                    </div>

                    <div class="span12" style="padding-left: 11px;">
                      <a href="<?php echo base_url('panel/cajas_carton/marca_pdf/?'.String::getVarsLink(array('msg'))); ?>" title="Generar PDF" target="_BLANK">
                        <img src="<?php echo base_url('application/images/otros/doc_pdf.png');?>" width="64" height="64">
                      </a>
                    </div>

                  </div>
                </div>
              </div>
            </form>

            <table class="table table-striped table-bordered bootstrap-datatable table-condensed">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Concepto</th>
                  <th>Empacador</th>
                  <th>Salidas</th>
                  <th>Entradas</th>
                  <th>Desecho</th>
                </tr>
              </thead>
              <tbody>

                  <tr>
                    <td colspan="3" style="text-align: right;">ANTERIORES A <?php echo $_GET['ffecha1'] ?></td>
                    <td><?php echo $inventario['anteriores'][0]->salidas; ?></td>
                    <td><?php echo $inventario['anteriores'][0]->entradas; ?></td>
                    <td>Total: <?php echo $inventario['anteriores'][0]->total_anterior; ?></td>
                  </tr>

            <?php
                $total_salidas = floatval($inventario['anteriores'][0]->salidas);
                $total_entradas = floatval($inventario['anteriores'][0]->entradas);
                foreach($inventario['inventario'] as $inv) {
                  if ($inv->tipo === 's') $total_salidas += $inv->cantidad;
                  else $total_entradas += $inv->cantidad;
                ?>
                  <tr>
                    <td><?php echo $inv->fecha ?></td>
                    <td><?php echo $inv->concepto; ?></td>
                    <td><?php echo $inv->empacador; ?></td>
                    <td><?php echo ($inv->tipo === 's') ? $inv->cantidad : '' ?></td>
                    <td><?php echo ($inv->tipo === 'en') ? $inv->cantidad : '' ?></td>
                    <td><?php echo ($inv->es_desecho == 1) ? 'Si' : 'No'; ?></td>
                  </tr>
            <?php }?>
                  <tr style="font-weight: bold; font-size: 1.1em;">
                    <td colspan="3" style="background-color: #ccc;"></td>
                    <td style="background-color: #ccc;"><?php echo $total_salidas; ?></td>
                    <td style="background-color: #ccc;"><?php echo $total_entradas; ?></td>
                    <td style="background-color: #ccc;">Total: <?php echo floatval($total_entradas) - floatval($total_salidas); ?></td>
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
