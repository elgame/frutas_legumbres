    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            <a href="<?php echo base_url('panel/productoresfac/?'.String::getVarsLink(array('id', 'msg')) ); ?>">Facturación</a> <span class="divider">/</span>
          </li>
          <li>
            Facturas
          </li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-file"></i> Facturas <?php echo (isset($datos_s['fact'][0])? $datos_s['fact'][0]->productor: ''); ?></h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            
            <a href="<?php echo base_url('panel/productoresfac/?'.String::getVarsLink(array('id', 'msg')) ); ?>"><i class="icon-chevron-left"></i> Atras</a>

            <form action="<?php echo base_url('panel/productoresfac/'); ?>" method="GET" class="form-search">
              <div class="form-actions form-filters">

                <label for="fecha" style="margin-top: 15px;">Fecha</label>
                <input type="text" name="fecha" class="input-medium search-query" id="fecha" value="<?php echo set_value_get('fecha'); ?>">
                
                <input type="submit" name="enviar" value="Buscar" class="btn">
              </div>
            </form>

            <table class="table table-striped table-bordered bootstrap-datatable">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Serie</th>
                  <th>Folio</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Opc</th>
                </tr>
              </thead>
              <tbody>
            <?php foreach($datos_s['fact'] as $fact) {?>
                <tr>
                  <td><?php echo $fact->fecha; ?></td>
                  <td><?php echo $fact->serie; ?></td>
                  <td><?php echo $fact->folio; ?></td>
                  <td><?php echo String::formatoNumero($fact->total); ?></td>
                  <td>
                    <?php
                      if($fact->status <> 'ca'){
                        $v_status    = 'Facturada';
                        $vlbl_status = 'label-success';
                      }else{
                        $v_status    = 'Cancelada';
                        $vlbl_status = 'label-important';
                      }
                    ?>
                    <span class="label <?php echo $vlbl_status; ?>"><?php echo $v_status; ?></span>
                  </td>
                  <td>
                    <?php
                    echo $this->usuarios_model->getLinkPrivSm('productoresfac/imprimir/', array(
                        'params'   => 'id='.$fact->id_factura,
                        'btn_type' => 'btn-info',
                        'attrs' => array('target' => '_blank'))
                      );
                    if ($fact->status <> 'ca')
                      echo $this->usuarios_model->getLinkPrivSm('productoresfac/cancelar/', array(
                        'params'   => 'id_factura='.$fact->id_factura.'&'.String::getVarsLink(array('pag')),
                        'btn_type' => 'btn-danger',
                        'attrs' => array('onclick' => "msb.confirm('Estas seguro de cancelar la factura?', 'Facturas productores', this); return false;"))
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
  $(document).ready(function(){
    noty({"text":"<?php echo $frm_errors['msg']; ?>", "layout":"topRight", "type":"<?php echo $frm_errors['ico']; ?>"});
  });
</script>
<?php }
}?>
<!-- Bloque de alertas -->
