    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            <a href="<?php echo base_url('panel/cajas/'); ?>" title="">Cajas</a> <span class="divider">/</span>
          </li>
          <li>
            Cuentas por Pagar
          </li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-user"></i> Cuentas por Pagar</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form action="<?php echo base_url('panel/cajas/cuentas_pagar'); ?>" method="get" class="form-search">
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


            <table class="table table-striped table-bordered bootstrap-datatable">
              <thead>
                <tr>
                  <th>Productor</th>
                  <th>Entradas</th>
                  <th>Abonos</th>
                  <th>Total a Pagar</th>
                </tr>
              </thead>
              <tbody>
            <?php
                $total_pagina = 0;
                foreach($cuentas_pagar['cuenta_pagar'] as $productor) {
                  $total_pagina += $productor->total_pagar;
                ?>
                <tr>
                  <td><a href="<?php echo base_url('panel/cajas/cuentas_pagar_productor').'/?id='.$productor->id_productor.'&'.
                                String::getVarsLink(array('id', 'msg')); ?>"><?php echo $productor->nombre; ?></a></td>
                  <td><?php echo String::formatoNumero($productor->total_entradas); ?></td>
                  <td><?php echo String::formatoNumero($productor->total_abonos); ?></td>
                  <td><?php echo String::formatoNumero($productor->total_pagar); ?></td>
                </tr>
            <?php }?>
                 <tr style="background-color:#ccc;font-weight: bold;">
                    <td class="a-r">Total x Página:</td>
                    <td></td>
                    <td></td>
                    <td><?php echo String::formatoNumero($total_pagina); ?></td>
                  </tr>
                  <tr style="background-color:#ccc;font-weight: bold;">
                    <td class="a-r">Total:</td>
                    <td></td>
                    <td></td>
                    <td><?php echo String::formatoNumero($cuentas_pagar['total_pagar_todos']); ?></td>
                  </tr>

              </tbody>
            </table>

            <?php
            //Paginacion
            $this->pagination->initialize(array(
                'base_url'      => base_url($this->uri->uri_string()).'?'.String::getVarsLink(array('pag')).'&',
                'total_rows'    => $cuentas_pagar['total_rows'],
                'per_page'      => $cuentas_pagar['items_per_page'],
                'cur_page'      => $cuentas_pagar['result_page']*$cuentas_pagar['items_per_page'],
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
