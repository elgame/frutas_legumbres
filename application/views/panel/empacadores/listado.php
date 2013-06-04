    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            Empacadores
          </li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-user"></i> Empacadores</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form action="<?php echo base_url('panel/empacadores'); ?>" method="get" class="form-search">
              <div class="form-actions form-filters">
                <label for="fnombre">Buscar</label>
                <input type="text" name="fnombre" id="fnombre" value="<?php echo set_value_get('fnombre'); ?>"
                  class="input-xlarge" placeholder="" autofocus>

                <label for="fstatus">Estado</label>
                <select name="fstatus">
                  <option value="ac" <?php echo set_select('fstatus', 'ac', false, $this->input->get('fstatus')); ?>>ACTIVOS</option>
                  <option value="e" <?php echo set_select('fstatus', 'e', false, $this->input->get('fstatus')); ?>>ELIMINADOS</option>
                  <option value="todos" <?php echo set_select('fstatus', 'todos', false, $this->input->get('fstatus')); ?>>TODOS</option>
                </select>

                <button type="submit" class="btn">Buscar</button>
              </div>
            </form>

            <?php
            echo $this->usuarios_model->getLinkPrivSm('empacadores/agregar/', array(
                    'params'   => '',
                    'btn_type' => 'btn-success pull-right',
                    'attrs' => array('style' => 'margin-bottom: 10px;') )
                );
             ?>
            <table class="table table-striped table-bordered bootstrap-datatable">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Status</th>
                  <th>Opc</th>
                </tr>
              </thead>
              <tbody>
            <?php foreach($empacadores['empacadores'] as $empacador){ ?>
                <tr>
                  <td><?php echo $empacador->nombre; ?></td>
                  <td>
                    <?php
                      if($empacador->status == 'ac'){
                        $v_status    = 'Activo';
                        $vlbl_status = 'label-success';
                      }else{
                        $v_status    = 'Eliminado';
                        $vlbl_status = 'label-important';
                      }
                    ?>
                    <span class="label <?php echo $vlbl_status; ?>"><?php echo $v_status; ?></span>
                  </td>
                  <td class="center">
                      <?php
                      echo $this->usuarios_model->getLinkPrivSm('empacadores/modificar/', array(
                          'params'   => 'id='.$empacador->id_empacador,
                          'btn_type' => 'btn-success')
                      );
                      if ($empacador->status == 'ac') {


                        echo $this->usuarios_model->getLinkPrivSm('empacadores/eliminar/', array(
                            'params'   => 'id='.$empacador->id_empacador,
                            'btn_type' => 'btn-danger',
                            'attrs' => array('onclick' => "msb.confirm('Estas seguro de eliminar el empacador?', 'Empacadores', this); return false;"))
                        );
                      }else{
                        echo $this->usuarios_model->getLinkPrivSm('empacadores/activar/', array(
                            'params'   => 'id='.$empacador->id_empacador,
                            'btn_type' => 'btn-danger',
                            'attrs' => array('onclick' => "msb.confirm('Estas seguro de activar el empacador?', 'Empacadores', this); return false;"))
                        );
                      }

                      ?>
                  </td>
                </tr>
            <?php }?>
              </tbody>
            </table>

            <?php
            //Paginacion
            $this->pagination->initialize(array(
                'base_url'      => base_url($this->uri->uri_string()).'?'.String::getVarsLink(array('pag')).'&',
                'total_rows'    => $empacadores['total_rows'],
                'per_page'      => $empacadores['items_per_page'],
                'cur_page'      => $empacadores['result_page']*$empacadores['items_per_page'],
                'page_query_string' => TRUE,
                'num_links'     => 1,
                'anchor_class'  => 'pags corner-all',
                'num_tag_open'  => '<li>',
                'num_tag_close' => '</li>',
                'cur_tag_open'  => '<li class="active"><a href="#">',
                'cur_tag_close' => '</a></li>'
            ));
            $pagination = $this->pagination->create_links();
            echo '<div class="pagination pagination-centered"><ul>'.$pagination.'</ul></div>';
            ?>
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
