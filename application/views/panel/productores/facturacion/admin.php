    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            Facturación
          </li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-file"></i> Facturas productores</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form action="<?php echo base_url('panel/productoresfac/'); ?>" method="GET" class="form-search">
              <div class="form-actions form-filters">
                
                <label for="fecha" style="margin-top: 15px;">Fecha</label>
                <input type="text" name="fecha" class="input-medium search-query" id="fecha" value="<?php echo set_value_get('fecha'); ?>">
                
                <label for="fnombre" style="margin-top: 15px;">Nombre</label>
                <input type="text" name="fnombre" class="input-medium search-query" id="fnombre" value="<?php echo set_value_get('fnombre'); ?>">

                <input type="submit" name="enviar" value="Buscar" class="btn">
              </div>
            </form>

            <table class="table table-striped table-bordered bootstrap-datatable table-fixed-header">
              <thead class="header">
                <tr>
                  <th>Productor</th>
                  <th>Limite</th>
                  <th>Saldo</th>
                  <th>Restante</th>
                  <th>Opc</th>
                </tr>
              </thead>
              <tbody>
            <?php foreach($datos_s['productores'] as $fact) {?>
                <tr>
                  <td>
                    <a href="<?php echo base_url('panel/productoresfac/detalles_facturas?id='.$fact->id_productor.'&fecha='.$this->input->get('fecha').'&fnombre='.$this->input->get('fnombre') ); ?>"><?php echo $fact->nombre_fiscal; ?></a></td>
                  <td><?php echo $fact->limite; ?></td>
                  <td><?php echo $fact->saldo; ?></td>
                  <td><?php echo $fact->restante; ?></td>
                  <td>
                  <?php
                    echo $this->usuarios_model->getLinkPrivSm('productoresfac/agregar/', array(
                        'params'   => 'id='.$fact->id_productor,
                        'btn_type' => 'btn-success', )
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
                'total_rows'    => $datos_s['total_rows'],
                'per_page'      => $datos_s['items_per_page'],
                'cur_page'      => $datos_s['result_page']*$datos_s['items_per_page'],
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
  <?php 
  if(isset($id_mov) && $this->input->get('met_pago') == 'cheque') //imprime el cheque
    echo 'window.open("'.base_url('panel/banco/print_cheque/?id='.$id_mov).'");';

  ?>

  $(document).ready(function(){
    noty({"text":"<?php echo $frm_errors['msg']; ?>", "layout":"topRight", "type":"<?php echo $frm_errors['ico']; ?>"});
  });
</script>
<?php }
}?>
<!-- Bloque de alertas -->
