    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            <a href="<?php echo base_url('panel/empacadores/'); ?>">Maquiladores</a> <span class="divider">/</span>
          </li>
          <li>Modificar Maquilador</li>
        </ul>
      </div>

      <form action="<?php echo base_url('panel/empacadores/modificar/?'.String::getVarsLink(array('msg'))); ?>" method="post"
          class="form-horizontal" enctype="multipart/form-data">
        <div class="row-fluid">
          <div class="box span12">
            <div class="box-header well" data-original-title>
              <h2><i class="icon-certificate"></i> Marca</h2>
              <div class="box-icon">
                <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
              </div>
            </div>
            <div class="box-content">
                <fieldset>
                  <legend></legend>

                  <div class="control-group">
                    <label class="control-label" for="dnombre">*Nombre:</label>
                    <div class="controls">
                      <input type="text" name="dnombre" id="dnombre" class="span6"
                        value="<?php echo (isset($info['info']->nombre)? $info['info']->nombre: ''); ?>"
                         placeholder="ataulfo, haden" maxlength="40" required autofocus>
                    </div>
                  </div>

                </fieldset>

            </div>
          </div><!--/box span-->

        </div><!--/row-->

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <a href="<?php echo base_url('panel/empacadores/'); ?>" class="btn">Cancelar</a>
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