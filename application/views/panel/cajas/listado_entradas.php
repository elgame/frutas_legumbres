    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            Entradas
          </li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-share-alt"></i> Administrar Cajas de Entrada</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form action="<?php echo base_url('panel/cajas/entradas/'); ?>" method="get" class="form-search">
              <div class="form-actions form-filters">
                <label for="fnombre">Buscar</label>
                <input type="text" name="fnombre" id="fnombre" value="<?php echo set_value_get('fnombre'); ?>"
                  class="input-xlarge" placeholder="Gamaliel Mendoza" autofocus>

                <!-- <label for="fstatus">Estado</label>
                <select name="fstatus">
                  <option value="ac" <?php echo set_select('fstatus', 'ac', false, $this->input->get('fstatus')); ?>>ACTIVOS</option>
                  <option value="e" <?php echo set_select('fstatus', 'e', false, $this->input->get('fstatus')); ?>>ELIMINADOS</option>
                  <option value="todos" <?php echo set_select('fstatus', 'todos', false, $this->input->get('fstatus')); ?>>TODOS</option>
                </select> -->

                <button type="submit" class="btn">Buscar</button>

                 <?php
                    echo $this->usuarios_model->getLinkPrivSm('cajas/agregar_entrada/', array(
                        'params'   => '',
                        'btn_type' => 'btn-success pull-right',
                        'attrs' => array('style' => 'margin-bottom: 10px;') )
                    );
                  ?>
              </div>
            </form>

            <table class="table table-striped table-bordered bootstrap-datatable table-fixed-header">
              <thead class="header">
                <tr>
                  <th>Fecha</th>
                  <th>Productor</th>
                  <th>Variedad</th>
                  <th>Cajas</th>
                  <th>Opc</th>
                </tr>
              </thead>
              <tbody>
            <?php foreach($cajas['cajas'] as $movi){ ?>
                <tr>
                  <td><?php echo substr($movi->fecha, 0, 10); ?></td>
                  <td><?php echo $movi->productor; ?></td>
                  <td><?php echo $movi->variedad; ?></td>
                  <td><?php echo $movi->cajas; ?></td>

                  <td class="center">
                      <?php
                      echo $this->usuarios_model->getLinkPrivSm('cajas/modificar_entrada/', array(
                          'params'   => 'id='.$movi->id_caja,
                          'btn_type' => 'btn-success')
                      );

                      echo $this->usuarios_model->getLinkPrivSm('cajas/eliminar_entrada/', array(
                          'params'   => 'id='.$movi->id_caja,
                          'btn_type' => 'btn-danger',
                          'attrs' => array('onclick' => "msb.confirm('Estas seguro de eliminar la entrada de cajas?', 'Cajas', this); return false;"))
                      );

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
                'total_rows'    => $cajas['total_rows'],
                'per_page'      => $cajas['items_per_page'],
                'cur_page'      => $cajas['result_page']*$cajas['items_per_page'],
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
