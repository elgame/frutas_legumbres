    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            <a href="<?php echo base_url('panel/cajas/'); ?>">Cajas</a> <span class="divider">/</span>
          </li>
          <li>Agregar Movimiento</li>
        </ul>
      </div>

      <form action="<?php echo base_url('panel/cajas/agregar'); ?>" method="POST" class="form-horizontal">
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
                  <legend></legend>

                  <div class="span6 mquit">

                     <div class="control-group req_field">
                      <label class="control-label" for="dproductor">Productor:</label>
                      <div class="controls">
                        <input type="text" name="dproductor" id="dproductor" class="span12"
                          value="<?php echo set_value('dproductor'); ?>" maxlength="120" required autofocus>

                        <input type="hidden" name="did_productor" id="did_productor" value="<?php echo set_value('did_productor'); ?>">
                      </div>
                    </div>

                    <div class="control-group tipo3">
                      <label class="control-label" for="dvariedad">Variedad </label>
                      <div class="controls">
                        <select name="dvariedad" id="dvariedad" class="span6">
                          <?php foreach($variedades as $v) { ?>
                            <option value="<?php echo $v->id_variedad ?>" <?php echo set_select('dvariedad', $v->id_variedad, false, $this->input->post('dvariedad')); ?>><?php echo $v->nombre ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="control-group">
                      <label class="control-label" for="dfecha">Fecha:</label>
                      <div class="controls">
                        <input type="text" name="dfecha" id="dfecha" class="span6"
                          value="<?php echo (isset($_POST['dfecha'])) ? $_POST['dfecha']: date('Y-m-d'); ?>" maxlength="10" required>
                      </div>
                    </div>

                    <div class="control-group req_field">
                      <label class="control-label" for="dconcepto">Concepto:</label>
                      <div class="controls">
                        <textarea name="dconcepto" id="dconcepto" class="span12" maxlength="250"><?php echo set_value('dno_exterior'); ?></textarea>
                      </div>
                    </div>

                    <div class="control-group req_field">
                      <label class="control-label" for="dcantidad">Cantidad:</label>
                      <div class="controls">
                        <input type="text" name="dcantidad" id="dcantidad" class="span12 vinteger"
                          value="<?php echo set_value('dcantidad'); ?>" maxlength="" required>

                      </div>
                    </div>

                    <div class="control-group">
                      <label class="control-label" for="dchofer">Chofér:</label>
                      <div class="controls">
                        <input type="text" name="dchofer" id="dchofer" class="span12"
                          value="<?php echo set_value('dchofer'); ?>" maxlength="30">
                      </div>
                    </div>

                    <div class="control-group tipo3">
                      <label class="control-label" for="dmovimiento">Tipo de Movimiento </label>
                      <div class="controls">
                        <select name="dmovimiento" id="dmovimiento" class="span6">
                          <option value="s" <?php echo set_select('dmovimiento', 's', false, $this->input->post('dmovimiento')); ?>>SALIDA</option>
                          <option value="en" <?php echo set_select('dmovimiento', 'en', false, $this->input->post('dmovimiento')); ?>>ENTRADA</option>
                        </select>
                      </div>
                    </div>

                  </div> <!--/span-->

                </fieldset>

            </div>
          </div><!--/box span-->

        </div><!--/row-->

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <a href="<?php echo base_url('panel/cajas/'); ?>" class="btn">Cancelar</a>
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
