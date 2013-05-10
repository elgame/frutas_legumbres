<!-- MODAL ABONOS -->
<div id="modal-abonos" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Abonos</h3>
  </div>
  <div class="modal-body">

      <div class="row-fluid" id="validator">
        <div class="span6">

          <div class="control-group">
            <label class="control-label" for="dfecha">Fecha</label>
            <div class="controls">
              <input type="datetime-local" name="dfecha" id="dfecha" class="span12"
                value="<?php echo set_value('dfecha', str_replace(' ', 'T', date("Y-m-d H:i")) ); ?>" required>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="dbanco">Banco</label>
            <div class="controls">
              <select name="dbanco" id="dbanco" class="span12" required>
                <option value=""></option>
                <?php
                    foreach($bancos as $b) { ?>
                      <option value="<?php echo $b->id_banco ?>"><?php echo $b->nombre ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="dcuenta">Cuenta</label>
            <div class="controls">
              <select name="dcuenta" id="dcuenta" class="span12" required>
                <option value=""></option>
              </select>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="dconcepto">Concepto</label>
            <div class="controls">
              <textarea name="dconcepto" id="dconcepto" class="span12"></textarea>
            </div>
          </div>

        </div>

        <div class="span6">

          <div class="control-group">
            <label class="control-label" for="dmonto">Cantidad ($)</label>
            <div class="controls">
              <input type="text" name="dmonto" id="dmonto" class="span12 vpositive" value="0" required>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="dtipo">Tipo</label>
            <div class="controls">
              <select name="dtipo" id="dtipo" class="span12" disabled>
                <option value="d">Deposito</option>
                <option value="r" selected>Retiro</option>
              </select>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="dmetodo">Metodo de Pago</label>
            <div class="controls">
              <select name="dmetodo" id="dmetodo" class="span12">
                <option value="efectivo">EFECTIVO</option>
                <option value="cheque">CHEQUE</option>
                <option value="tarjeta">TARJETA</option>
                <option value="transferencia">TRANSFERENCIA</option>
                <option value="deposito">DEPOSITO</option>
              </select>
            </div>
          </div>


          <div class="well" id="well" style="display: none;">
            <div class="control-group">
              <label class="control-label" for="danombrede">A nombre de</label>
              <div class="controls">
                <input type="text" name="danombrede" id="danombrede" class="span12" value="" required>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="dmoneda">Moneda</label>
              <div class="controls">
                <select name="dmoneda" class="span12" id="dmoneda">
                  <option value="M.N.">M.N.</option>
                  <option value="USD">USD</option>
                </select>
              </div>
            </div>
          </div>

        </div>
      </div>

  </div>
  <div class="modal-footer">
    <input type="text" id="id_productor" value="<?php echo isset($_GET['id'])?$_GET['id']:''; ?>">
    <input type="text" id="id_entrada" value="<?php echo isset($_GET['idc'])?$_GET['idc']:''; ?>">

    <?php if(!isset($_GET['idc'])) { ?>
       <input type="hidden" id="id_abono_masivo" value="masivo">
    <?php } ?>

    <button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
    <button class="btn btn-success" id="add-abono">Abonar</button>
  </div>
</div>
<!-- /MODAL ABONOS -->