    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a>
              <span class="divider">/</span>
          </li>
          <li>
             <a href="<?php echo base_url('panel/cajas/'); ?>">Cajas</a>
              <span class="divider">/</span>
          </li>
          <li>
            <li>Reporte Relación de Lavado por Lotes</li>
          </li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-book"></i> Reporte</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round">
                <i class="icon-chevron-up"></i></a>
            </div>
          </div>

          <div class="box-content">

            <form action="<?php echo base_url('panel/cajas_reportes/rll_pdf/'); ?>"
                method="get" class="form-search" target="rllReporte">

              <div class="form-actions form-filters">

                <label for="ffecha1">Del:</label>
                <input type="text" name="ffecha1" id="ffecha1"
                  value="<?php echo date('Y-m-01'); ?>">

                <label for="ffecha2">Al:</label>
                <input type="text" name="ffecha2" id="ffecha2"
                  value="<?php echo date('Y-m-d'); ?>">

                <button type="submit" class="btn">Buscar</button>

              </div>

            </form>


            <div class="row-fluid">
              <iframe name="rllReporte" id="iframe-reporte" class="span12"
                src="<?php echo base_url('panel/cajas_reportes/rll_pdf')?>"
                  style="height:600px;"></iframe>
            </div>

          </div>
        </div><!--/span-->

      </div><!--/row-->




          <!-- content ends -->
    </div><!--/#content.span10-->
