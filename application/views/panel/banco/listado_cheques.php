    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            <a href="<?php echo base_url('panel/banco/'); ?>">Banco</a> <span class="divider">/</span>
          </li>
          <li>Lista de cheques</li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-th-list"></i> Cheques</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form action="<?php echo base_url('panel/banco/cheques/'); ?>" method="get" class="form-search">
              <div class="form-actions form-filters">
                <label for="fnombre">Buscar</label>
                <input type="text" name="fnombre" id="fnombre" value="<?php echo set_value_get('fnombre'); ?>"
                  class="input-xlarge" placeholder="Banamex, Carlos Perez" autofocus>

                <label for="ffecha1">Del:</label>
                <input type="text" name="ffecha1" id="ffecha1" value="<?php echo $this->input->get('ffecha1'); ?>" size="1" style="width:14%;">

                <label for="ffecha2">Al:</label>
                <input type="text" name="ffecha2" id="ffecha2" value="<?php echo $this->input->get('ffecha2'); ?>" size="1" style="width:14%;">

                <button type="submit" class="btn">Buscar</button>

                <a href="<?php echo base_url('panel/banco/cheques_xls?'.String::getVarsLink(array('msg'))); ?>" class="pull-right" title="Generar xls" target="_BLANK">
                  <img src="<?php echo base_url('application/images/otros/doc_xls.png'); ?>" width="64" height="64">
                </a>
              </div>
            </form>

            <table class="table table-striped table-bordered bootstrap-datatable table-fixed-header">
              <thead class="header">
                <tr>
                  <th>Fecha</th>
                  <th>Banco</th>
                  <th>Cuenta</th>
                  <th>Monto</th>
                  <th># Cheque</th>
                  <th>A nombre de</th>
                  <th>Opc</th>
                </tr>
              </thead>
              <tbody>
            <?php foreach($cheques['cheques'] as $movi){ ?>
                <tr>
                  <td><?php echo substr($movi->fecha, 0, 10); ?></td>
                  <td><?php echo $movi->banco; ?></td>
                  <td><?php echo $movi->alias; ?></td>
                  <td><?php echo String::formatoNumero($movi->monto); ?></td>
                  <td><?php echo $movi->no_cheque; ?></td>
                  <td><?php echo $movi->anombre_de; ?></td>

                  <td class="center">
                    <a class="btn btn-info" target="_blank" href="<?php echo base_url('panel/banco/print_cheque/?id='.$movi->id_movimiento); ?>" title="Imprimir">
                      <i class="icon-print icon-white"></i> <span class="hidden-tablet">Reimprimir</span></a>
                      <?php
                      echo $this->usuarios_model->getLinkPrivSm('banco/print_cheque/', array(
                          'params'   => 'id='.$movi->id_movimiento,
                          'btn_type' => 'btn-danger',
                          )
                      );
                      if($movi->status == 1){
                        echo $this->usuarios_model->getLinkPrivSm('banco/cancelar_cheque/', array(
                            'params'   => 'id='.$movi->id_movimiento,
                            'btn_type' => 'btn-danger',
                            'attrs' => array('onclick' => "msb.confirm('Estas seguro de cancelar el cheque?', 'Banco', this); return false;"))
                        );
                      }else{
                        echo $this->usuarios_model->getLinkPrivSm('banco/activar_cheque/', array(
                            'params'   => 'id='.$movi->id_movimiento,
                            'btn_type' => 'btn-danger',
                            'attrs' => array('onclick' => "msb.confirm('Estas seguro de activar el cheque?', 'Banco', this); return false;"))
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
                'total_rows'    => $cheques['total_rows'],
                'per_page'      => $cheques['items_per_page'],
                'cur_page'      => $cheques['result_page']*$cheques['items_per_page'],
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
