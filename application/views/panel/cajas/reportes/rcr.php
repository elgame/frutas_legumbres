    <div id="content" class="span10">
      <!-- content starts -->

      <div class="row-fluid">
        <div class="box span12">

          <div class="box-content">

            <form action="<?php echo base_url('panel/cajas_reportes/rcr_pdf/'); ?>" method="get" class="form-search" target="rcrReporte">
              <div class="form-actions form-filters">

                <label for="ffecha1">Del:</label>
                <input type="text" name="ffecha1" id="ffecha1" value="<?php echo date('Y-m-01'); ?>">

                <label for="ffecha2">Al:</label>
                <input type="text" name="ffecha2" id="ffecha2" value="<?php echo date('Y-m-d'); ?>">

                <button type="submit" class="btn">Buscar</button>

              </div>
            </form>


            <div class="row-fluid">
              <iframe name="rcrReporte" id="iframe-reporte" class="span12" src="<?php echo base_url('panel/cajas_reportes/rcr_pdf')?>" style="height:600px;"></iframe>
            </div>

          </div>
        </div><!--/span-->

      </div><!--/row-->




          <!-- content ends -->
    </div><!--/#content.span10-->
