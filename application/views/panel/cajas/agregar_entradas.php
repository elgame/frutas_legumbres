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
          <li>Agregar Entrada</li>
        </ul>
      </div>

      <form action="<?php echo base_url('panel/cajas/agregar_entrada'); ?>" method="POST" class="form-horizontal">
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

                    <div class="control-group">
                      <label class="control-label" for="dfecha">Fecha:</label>
                      <div class="controls">
                        <input type="text" name="dfecha" id="dfecha" class="span6"
                          value="<?php echo isset($_POST['dfecha']) ? $_POST['dfecha'] : date('Y-m-d'); ?>" maxlength="10" required>
                      </div>
                    </div>

                    <div class="control-group req_field">
                      <label class="control-label" for="ddueno">Dueño:</label>
                      <div class="controls">
                        <input type="text" name="ddueno" id="ddueno" class="span12"
                          value="<?php echo set_value('ddueno'); ?>" maxlength="120" required autofocus>

                        <input type="hidden" name="did_dueno" id="did_dueno" value="<?php echo set_value('did_dueno'); ?>">
                      </div>
                    </div>

                    <div class="control-group req_field">
                      <label class="control-label" for="dproductor">Productor:</label>
                      <div class="controls">
                        <input type="text" name="dproductor" id="dproductor" class="span12"
                          value="<?php echo set_value('dproductor'); ?>" maxlength="120" required>

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

                    <div class="control-group req_field">
                      <label class="control-label" for="dcertificado_tarjeta">Cerficado Tarjeta:</label>
                      <div class="controls">
                        <input type="text" name="dcertificado_tarjeta" id="dcertificado_tarjeta" class="span12"
                          value="<?php echo set_value('dcertificado_tarjeta'); ?>" maxlength="40">
                      </div>
                    </div>

                    <div class="control-group req_field">
                      <label class="control-label" for="dcodigo_huerta">Codigo de Huerta:</label>
                      <div class="controls">
                        <input type="text" name="dcodigo_huerta" id="dcodigo_huerta" class="span12"
                          value="<?php echo set_value('dcodigo_huerta'); ?>" maxlength="40">
                      </div>
                    </div>

                    <div class="control-group">
                      <label class="control-label" for="dno_lote">Numero de Lote:</label>
                      <div class="controls">
                        <input type="text" name="dno_lote" id="dno_lote" class="span12 vinteger"
                          value="<?php echo set_value('dno_lote'); ?>">
                      </div>
                    </div>

                    <div class="control-group">
                      <label class="control-label" for="dunidad_transporte">Unidad de Transporte</label>
                      <div class="controls">
                        <input type="text" name="dunidad_transporte" id="dunidad_transporte" class="span12"
                          value="<?php echo set_value('dunidad_transporte'); ?>" maxlength="60">
                      </div>
                    </div>

                    <div class="control-group">
                      <label class="control-label" for="ddueno_carga">Dueño de la Carga:</label>
                      <div class="controls">
                        <input type="text" name="ddueno_carga" id="ddueno_carga" class="span12"
                          value="<?php echo set_value('ddueno_carga'); ?>" maxlength="60">
                      </div>
                    </div>

                    <div class="control-group">
                      <label class="control-label" for="dobservaciones">Observaciones:</label>
                      <div class="controls">
                        <textarea name="dobservaciones" id="dobservaciones" class="span12" maxlength="250"><?php echo set_value('dobservaciones'); ?></textarea>
                      </div>
                    </div>

                  </div> <!--/span-->

                  <div class="span6 mquit">
                    <div class="control-group">
                      <label class="control-label" for="dcajas">Cajas:</label>
                      <div class="controls">
                        <input type="text" name="dcajas" id="dcajas" class="span12 vinteger"
                          value="<?php echo set_value('dcajas'); ?>" required>
                      </div>
                    </div>

                    <div class="control-group">
                      <label class="control-label" for="dcajas_rezaga">Cajas de Rezaga:</label>
                      <div class="controls">
                        <input type="text" name="dcajas_rezaga" id="dcajas_rezaga" class="span12 vinteger"
                          value="<?php echo set_value('dcajas_rezaga'); ?>">
                      </div>
                    </div>

                    <div class="control-group">
                      <label class="control-label" for="dno_ticket">Numero de Ticket:</label>
                      <div class="controls">
                        <input type="text" name="dno_ticket" id="dno_ticket" class="span12"
                          value="<?php echo set_value('dno_ticket'); ?>" maxlength="10">
                      </div>
                    </div>

                    <div class="control-group">
                      <label class="control-label" for="dkilos">Kilos Recibidos:</label>
                      <div class="controls">
                        <input type="text" name="dkilos" id="dkilos" class="span12 vinteger"
                          value="<?php echo set_value('dkilos'); ?>">
                      </div>
                    </div>

                    <div class="control-group">
                      <label class="control-label" for="dprecio">Precio:</label>
                      <div class="controls">
                        <input type="text" name="dprecio" id="dprecio" class="span12 vnumeric"
                          value="<?php echo set_value('dprecio'); ?>">
                      </div>
                    </div>

                    <div class="control-group tipo3">
                      <label class="control-label" for="des_organico">Es organico? </label>
                      <div class="controls">
                        <select name="des_organico" id="des_organico" class="span6">
                          <option value="0" <?php echo set_select('des_organico', '0', false, $this->input->post('des_organico')); ?>>CONVENCIONAL</option>
                          <option value="1" <?php echo set_select('des_organico', '1', false, $this->input->post('des_organico')); ?>>ORGANICO</option>
                        </select>
                      </div>
                    </div>

                  </div>

                </fieldset>

            </div>
          </div><!--/box span-->

        </div><!--/row-->

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <a href="<?php echo base_url('panel/cajas/entradas'); ?>" class="btn">Cancelar</a>
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
