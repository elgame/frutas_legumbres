    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            Cajas Cartón
          </li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-user"></i> Movimientos de Cajas de Cartón</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form action="<?php echo base_url('panel/cajas_carton/'); ?>" method="get" class="form-search">
              <div class="form-actions form-filters">

                <label for="ffecha1">Del:</label>
                <input type="text" name="ffecha1" id="ffecha1" value="<?php echo $this->input->get('ffecha1'); ?>" size="10">

                <label for="ffecha2">Al:</label>
                <input type="text" name="ffecha2" id="ffecha2" value="<?php echo $this->input->get('ffecha2'); ?>" size="10">

                <label for="ide">Empacador</label>
                <select name="ide">
                  <option value=""></option>}
                  <?php foreach ($empacadores as $e){ ?>
                    <option value="<?php echo $e->id_empacador ?>" <?php echo set_select('ide', $e->id_empacador, false, $this->input->get('ide')); ?>><?php echo $e->nombre ?></option>
                  <?php } ?>
                </select>

                <button type="submit" class="btn btn-info">Buscar</button>

                <?php
                  echo $this->usuarios_model->getLinkPrivSm('cajas_carton/agregar/', array(
                          'params'   => '',
                          'btn_type' => 'btn-success pull-right',
                          'attrs' => array('style' => 'margin-bottom: 10px;') )
                      );
                   ?>
              </div>
            </form>


            <table class="table table-striped table-bordered bootstrap-datatable">
              <thead>
                <tr>
                  <th>Marca</th>
                  <th>Cajas</th>
                </tr>
              </thead>
              <tbody>
            <?php
                $total_deben = 0;
                foreach($inventario['inventario'] as $marca) {
                  $total_deben += $marca->total_debe;
                ?>
                <tr>
                  <td><a href="<?php echo base_url('panel/cajas_carton/marca').'/?id='.$marca->id_marca.'&'.
                                String::getVarsLink(array('id', 'msg')); ?>"><?php echo $marca->marca; ?></a></td>
                  <td><?php echo $marca->total_debe; ?></td>
                </tr>
            <?php }?>
                 <tr style="background-color:#ccc;font-weight: bold;">
                    <td class="a-r">Total x Página:</td>
                    <td><?php echo $total_deben; ?></td>
                  </tr>
                  <tr style="background-color:#ccc;font-weight: bold;">
                    <td class="a-r">Total:</td>
                    <td><?php echo $inventario['ttotal']; ?></td>
                  </tr>

              </tbody>
            </table>

            <?php
            //Paginacion
            $this->pagination->initialize(array(
                'base_url'      => base_url($this->uri->uri_string()).'?'.String::getVarsLink(array('pag')).'&',
                'total_rows'    => $inventario['total_rows'],
                'per_page'      => $inventario['items_per_page'],
                'cur_page'      => $inventario['result_page']*$inventario['items_per_page'],
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
