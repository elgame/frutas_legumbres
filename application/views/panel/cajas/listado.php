    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            Cajas
          </li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-user"></i> Movimientos de Cajas (Salidas y Entradas)</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form action="<?php echo base_url('panel/cajas'); ?>" method="get" class="form-search">
              <div class="form-actions form-filters">

                <label for="ffecha1">Del:</label>
                <input type="text" name="ffecha1" id="ffecha1" value="<?php echo $this->input->get('ffecha1'); ?>" size="10">

                <label for="ffecha2">Al:</label>
                <input type="text" name="ffecha2" id="ffecha2" value="<?php echo $this->input->get('ffecha2'); ?>" size="10">

                <button type="submit" class="btn">Buscar</button>

                <?php
                  echo $this->usuarios_model->getLinkPrivSm('cajas/agregar/', array(
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
                  <th>Productor</th>
                  <th>Cajas sin entregar</th>
                </tr>
              </thead>
              <tbody>
            <?php
                $total_deben = 0;
                foreach($inventario['inventario'] as $productor) {
                  $total_deben += $productor->total_debe;
                ?>
                <tr>
                  <td><a href="<?php echo base_url('panel/cajas/productor').'/?id='.$productor->id_productor.'&'.
                                String::getVarsLink(array('id', 'msg')); ?>"><?php echo $productor->nombre; ?></a></td>
                  <td><?php echo $productor->total_debe; ?></td>
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
