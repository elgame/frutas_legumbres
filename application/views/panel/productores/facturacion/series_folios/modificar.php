<div id="content" class="span10">
  <!-- content starts -->
  <div>
    <ul class="breadcrumb">
      <li>
        <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
      </li>
      <li>
        <a href="<?php echo base_url('panel/productoresfac/'); ?>">Facturación</a> <span class="divider">/</span>
      </li>
      <li>
        <a href="<?php echo base_url('panel/productoresfac/series_folios/'); ?>">Series y Folios</a> <span class="divider">/</span>
      </li>
      <li>Modificar Series y Folios</li>
    </ul>
  </div>

  <form class="form-horizontal" action="<?php echo base_url('panel/productoresfac/modificar_serie_folio?id='.$_GET['id']);?>" method="POST" enctype="multipart/form-data">
       
      <div class="control-group">
        <label for="dproductor" class="control-label">Productores</label>
        <div class="controls">
          <input type="text" name="dproductor" class="span6" id="dproductor" value="<?php echo (isset($serie_info[0]->productor)) ? $serie_info[0]->productor : set_value('dproductor') ?>" required autofocus>
          <input type="hidden" name="did_productor" id="did_productor" value="<?php echo (isset($serie_info[0]->id_productor)) ? $serie_info[0]->id_productor : set_value('did_productor') ?>">
        </div>
      </div>

      <div class="control-group">
        <label for="fserie" class="control-label">Serie</label>
        <div class="controls">
          <input type="text" name="fserie" id="fserie" value="<?php echo (isset($serie_info[0]->serie)) ? $serie_info[0]->serie : set_value('fserie') ?>" 
            size="30" maxlength="30" placeholder="Serie">
        </div>
      </div>

      <div class="control-group">
        <label for="fno_aprobacion" class="control-label">No Aprobación</label>
        <div class="controls">
          <input type="text" name="fno_aprobacion" id="fno_aprobacion" value="<?php echo (isset($serie_info[0]->no_aprobacion)) ? $serie_info[0]->no_aprobacion :set_value('fno_aprobacion') ?>" 
            maxlength="30" placeholder="No Aprobación" required>
        </div>
      </div>

       <div class="control-group">
        <label for="ffolio_inicio" class="control-label">Folio Inicio</label>
        <div class="controls">
          <input type="text" name="ffolio_inicio" id="ffolio_inicio" value="<?php echo (isset($serie_info[0]->folio_inicio)) ? $serie_info[0]->folio_inicio :set_value('ffolio_inicio') ?>" 
            maxlength="30" placeholder="Folio Inicio" required>
        </div>
      </div>

       <div class="control-group">
        <label for="ffolio_fin" class="control-label">Folio Fin</label>
        <div class="controls">
          <input type="text" name="ffolio_fin" id="ffolio_fin" value="<?php echo (isset($serie_info[0]->folio_fin)) ? $serie_info[0]->folio_fin :set_value('ffolio_fin') ?>" 
            maxlength="30" placeholder="Folio Fin" required>
        </div>
      </div>

      <div class="control-group">
        <label for="fano_aprobacion" class="control-label">Fecha Aprobación</label>
        <div class="controls">
          <input type="text" name="fano_aprobacion" class="datepicker" id="fano_aprobacion" value="<?php echo (isset($serie_info[0]->ano_aprobacion)) ? $serie_info[0]->ano_aprobacion :set_value('fano_aprobacion') ?>" 
            maxlength="10" placeholder="Fecha Aprobación" required>
        </div>
      </div>

      <div class="control-group">
        <label for="durl_img" class="control-label">Imagen</label>
        <div class="controls">
          <input type="file" name="durl_img" id="durl_img" value="<?php echo set_value('durl_img') ?>" size="30">
        </div>
      </div>

      <div class="control-group">
        <label for="fleyenda" class="control-label">Leyenda</label>
        <div class="controls">
          <input type="text" name="fleyenda" class="input-xxlarge" id="fleyenda" value="<?php echo (isset($serie_info[0]->leyenda)) ? $serie_info[0]->leyenda :set_value('fleyenda') ?>" size="72" placeholder="Leyenda">
        </div>
      </div>

      <div class="control-group">
        <label for="fleyenda1" class="control-label">Leyenda 1</label>
        <div class="controls">
          <input type="text" name="fleyenda1" class="input-xxlarge" id="fleyenda1" value="<?php echo (isset($serie_info[0]->leyenda1)) ? $serie_info[0]->leyenda1 :set_value('fleyenda1') ?>" size="72" placeholder="Leyenda 1">
        </div>
      </div>

      <div class="control-group">
        <label for="fleyenda2" class="control-label">Leyenda 2</label>
        <div class="controls">
          <input type="text" name="fleyenda2" class="input-xxlarge" id="fleyenda2" value="<?php echo (isset($serie_info[0]->leyenda2)) ? $serie_info[0]->leyenda2 :set_value('fleyenda2') ?>" size="72" placeholder="Leyenda 1">
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="<?php echo base_url('panel/productoresfac/series_folios') ?>" class="btn">Cancelar</a>
      </div>
  </form>
</div>


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