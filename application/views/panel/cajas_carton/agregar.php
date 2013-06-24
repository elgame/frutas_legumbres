    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            <a href="<?php echo base_url('panel/cajas_carton/'); ?>">Cajas de Cartón</a> <span class="divider">/</span>
          </li>
          <li>Agregar Movimiento</li>
        </ul>
      </div>

      <form action="<?php echo base_url('panel/cajas_carton/agregar'); ?>" method="POST" class="form-horizontal">
        <div class="row-fluid">
          <div class="box span12">
            <div class="box-header well" data-original-title>
              <h2><i class="icon-list-alt"></i> Información</h2>
              <div class="box-icon">
                <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
              </div>
            </div>
            <div class="box-content">
                <fieldset>
                  <div class="span6 mquit">

                    <div class="control-group tipo3">
                      <label class="control-label" for="dmovimiento">Tipo de Movimiento </label>
                      <div class="controls">
                        <select name="dmovimiento" id="dmovimiento" class="span6">
                          <option value="s" <?php echo set_select('dmovimiento', 's', false, $this->input->post('dmovimiento')); ?>>SALIDA</option>
                          <option value="en" <?php echo set_select('dmovimiento', 'en', false, $this->input->post('dmovimiento')); ?>>ENTRADA</option>
                        </select>
                      </div>
                    </div>

                    <div class="control-group req_field" id="bloq-emp">
                      <label class="control-label" for="dempacador">Maquilador:</label>
                      <div class="controls">
                        <input type="text" name="dempacador" id="dempacador" class="span12"
                          value="<?php echo set_value('dempacador'); ?>" maxlength="120" autofocus>

                        <input type="hidden" name="did_empacador" id="did_empacador" value="<?php echo set_value('did_empacador'); ?>">
                      </div>
                    </div>

                    <div class="control-group tipo3">
                      <label class="control-label" for="dmarca">Marca </label>
                      <div class="controls">
                        <select name="dmarca" id="dmarca" class="span6">
                          <?php foreach($marcas as $m) { ?>
                            <option value="<?php echo $m->id_marca ?>" <?php echo set_select('dmarca', $m->id_marca, false, $this->input->post('dmarca')); ?>><?php echo $m->nombre ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="control-group">
                      <label class="control-label" for="dfecha">Fecha:</label>
                      <div class="controls">
                        <input type="text" name="dfecha" id="dfecha" class="span6"
                          value="<?php echo (isset($_POST['dfecha'])) ? $_POST['dfecha']: date('Y-m-d'); ?>" maxlength="10">
                      </div>
                    </div>

                    <div class="control-group req_field">
                      <label class="control-label" for="dconcepto">Concepto:</label>
                      <div class="controls">
                        <textarea name="dconcepto" id="dconcepto" class="span12" maxlength="250"><?php echo set_value('dconcepto'); ?></textarea>
                      </div>
                    </div>

                    <div class="control-group req_field">
                      <label class="control-label" for="dcantidad">Cantidad:</label>
                      <div class="controls">
                        <input type="text" name="dcantidad" id="dcantidad" class="span12 vinteger"
                          value="<?php echo set_value('dcantidad'); ?>" maxlength="">

                      </div>
                    </div>

                    <div class="control-group" id="bloq-desecho">
                      <label class="control-label" for="dchofer">Desecho</label>
                      <div class="controls">
                        <input type="checkbox" name="ddesecho" id="ddesecho" value="1" <?php echo set_checkbox('ddesecho', '1', (isset($_POST['ddesecho'])?true:false)) ?>>
                      </div>
                    </div>

                  </div> <!--/span-->

                </fieldset>

            </div>
          </div><!--/box span-->

        </div><!--/row-->

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <a href="<?php echo base_url('panel/cajas_carton/'); ?>" class="btn">Cancelar</a>
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
