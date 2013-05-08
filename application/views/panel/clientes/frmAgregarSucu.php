
<h3 class="frmsec-acordion"><a href="#">Información Facturación</a></h3>
<div>
	<div class="control-group">
		<label class="control-label" for="dnombre_fiscal">*Nombre Fiscal:</label>
		<div class="controls">
			<input type="text" name="dnombre_fiscal" id="dnombre_fiscal" value="<?php echo set_value('dnombre_fiscal'); ?>" maxlength="130" autofocus>
	</div>
	<div class="control-group">
		<label class="control-label" for="drfc">RFC:</label>
		<div class="controls">
			<input type="text" name="drfc" id="drfc" value="<?php echo set_value('drfc'); ?>" maxlength="13">
	</div>
	<div class="clear"></div>
	
	<div class="control-group">
		<label class="control-label" for="dcalle">Calle:</label>
		<div class="controls">
			<input type="text" name="dcalle" id="dcalle" value="<?php echo set_value('dcalle'); ?>" maxlength="60">
	</div>
	<div class="control-group">
		<label class="control-label" for="dno_exterior">No exterior:</label>
		<div class="controls">
			<input type="text" name="dno_exterior" id="dno_exterior" value="<?php echo set_value('dno_exterior'); ?>" maxlength="7">
	</div>
	<div class="control-group">
		<label class="control-label" for="dno_interior">No interior:</label>
		<div class="controls">
			<input type="text" name="dno_interior" id="dno_interior" value="<?php echo set_value('dno_interior'); ?>" maxlength="7">
	</div>
	<div class="clear"></div>
	
	<div class="control-group">
		<label class="control-label" for="dcolonia">Colonia:</label>
		<div class="controls">
			<input type="text" name="dcolonia" id="dcolonia" value="<?php echo set_value('dcolonia'); ?>" maxlength="60">
	</div>
	<div class="control-group">
		<label class="control-label" for="dlocalidad">Localidad:</label>
		<div class="controls">
			<input type="text" name="dlocalidad" id="dlocalidad" value="<?php echo set_value('dlocalidad'); ?>" maxlength="45">
	</div>
	<div class="clear"></div>
	
	<div class="control-group">
		<label class="control-label" for="dmunicipio">Municipio / Delegación:</label>
		<div class="controls">
			<input type="text" name="dmunicipio" id="dmunicipio" value="<?php echo set_value('dmunicipio'); ?>" maxlength="45">
	</div>
	<div class="control-group">
		<label class="control-label" for="destado">Estado:</label>
		<div class="controls">
			<input type="text" name="destado" id="destado" value="<?php echo set_value('destado'); ?>" maxlength="45">
	</div>
	<div class="clear"></div>
	
	<div class="control-group">
		<label class="control-label" for="dcp">CP:</label>
		<div class="controls">
			<input type="text" name="dcp" id="dcp" value="<?php echo set_value('dcp'); ?>" maxlength="10">
	</div>
	<div class="clear"></div>
	
	<div class="control-group">
		<label class="control-label" for="dtelefono">Teléfono:</label>
		<div class="controls">
			<input type="text" name="dtelefono" id="dtelefono" value="<?php echo set_value('dtelefono'); ?>" maxlength="15">
	</div>
	<div class="control-group">
		<label class="control-label" for="dcelular">Celular:</label>
		<div class="controls">
			<input type="text" name="dcelular" id="dcelular" value="<?php echo set_value('dcelular'); ?>" maxlength="20">
	</div>
	<div class="clear"></div>
	
	<div class="control-group">
		<label class="control-label" for="demail">Email:</label>
		<div class="controls">
			<input type="text" name="demail" id="demail" value="<?php echo set_value('demail'); ?>" maxlength="70">
	</div>
	<div class="control-group">
		<label class="control-label" for="dpag_web">Pag Web:</label>
		<div class="controls">
			<input type="text" name="dpag_web" id="dpag_web" value="<?php echo set_value('dpag_web'); ?>" maxlength="80">
	</div>
	<div class="clear"></div>
	
	<div class="control-group">
		<label class="control-label" for="dcomentarios">Comentarios:</label>
		<textarea name="dcomentarios" id="dcomentarios" rows="3" cols="40" maxlength="400"><?php echo set_value('dcomentarios'); ?></textarea>
	</div>
	<div class="control-group">
		<label class="control-label" for="dlista_precio">Lista de precio:</label>
		<select name="dlista_precio" id="dlista_precio">
		<?php
			if(is_array($listas)){
				foreach($listas as $itm){
					echo '<option value="'.$itm->id_lista.'" '.set_select('dlista_precio', $itm->id_lista).'>'.$itm->nombre.'</option>';
				}
			}
		?>
		</select>
	</div>
	<div class="clear"></div>
	
	<div class="control-group">
		<label class="control-label" for="drecepcion_facturas">Recepción facturas:</label>
		<select name="drecepcion_facturas" id="drecepcion_facturas">
			<option value="Lunes" <?php echo set_select('drecepcion_facturas', 'Lunes'); ?>>Lunes</option>
			<option value="Martes" <?php echo set_select('drecepcion_facturas', 'Martes'); ?>>Martes</option>
			<option value="Miércoles" <?php echo set_select('drecepcion_facturas', 'Miércoles'); ?>>Miércoles</option>
			<option value="Jueves" <?php echo set_select('drecepcion_facturas', 'Jueves'); ?>>Jueves</option>
			<option value="Viernes" <?php echo set_select('drecepcion_facturas', 'Viernes'); ?>>Viernes</option>
			<option value="Sábado" <?php echo set_select('drecepcion_facturas', 'Sábado'); ?>>Sábado</option>
			<option value="Domingo" <?php echo set_select('drecepcion_facturas', 'Domingo'); ?>>Domingo</option>
		</select>
	</div>
	<div class="control-group">
		<label class="control-label" for="ddias_pago">Dias pago:</label>
		<select name="ddias_pago" id="ddias_pago">
			<option value="Lunes" <?php echo set_select('ddias_pago', 'Lunes'); ?>>Lunes</option>
			<option value="Martes" <?php echo set_select('ddias_pago', 'Martes'); ?>>Martes</option>
			<option value="Miércoles" <?php echo set_select('ddias_pago', 'Miércoles'); ?>>Miércoles</option>
			<option value="Jueves" <?php echo set_select('ddias_pago', 'Jueves'); ?>>Jueves</option>
			<option value="Viernes" <?php echo set_select('ddias_pago', 'Viernes'); ?>>Viernes</option>
			<option value="Sábado" <?php echo set_select('ddias_pago', 'Sábado'); ?>>Sábado</option>
			<option value="Domingo" <?php echo set_select('ddias_pago', 'Domingo'); ?>>Domingo</option>
		</select>
	</div>
	<div class="clear"></div>
	
	<p class="w50">
		<label class="control-label" for="ddias_credito">Dias credito:</label>
		<div class="controls">
			<input type="number" name="ddias_credito" id="ddias_credito" class="vpositive" 
					value="<?php echo set_value('ddias_credito', 0); ?>" min="0" max="120"> dias
	</div>
	<div class="clear"></div>
	
	<div class="control-group">
		<label class="control-label" for="ddescuento">Descuento:</label>
		<div class="controls">
			<input type="text" name="ddescuento" id="ddescuento" value="<?php echo set_value('ddescuento'); ?>" class="vpositive" maxlength="3"> %
	</div>
	<div class="control-group">
		<label class="control-label" for="dretencion">Retención ISR:</label>
		<div class="controls">
			<input type="checkbox" value="1" name="dretencion" id="dretencion">
		<?php /*
		<select name="dretencion" id="dretencion">
			<option value="0" <?php echo set_select('dretencion', '0'); ?>>No</option>
			<option value="66.66" <?php echo set_select('dretencion', '66.66'); ?>>2terceras</option>
			<option value="100" <?php echo set_select('dretencion', '100'); ?>>100</option>
		</select> %*/?>
	</div>
	<div class="clear"></div>
</div>

<h3 class="frmsec-acordion"><a href="#">Información Empresa</a></h3>
<div>
	<div class="control-group">
		<label class="control-label" for="demismos_facturacion">Los mismos de Facturación:</label>
		<div class="controls">
			<input type="checkbox" name="demismos_facturacion" id="demismos_facturacion">
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="denombre">Nombre:</label>
		<div class="controls">
			<input type="text" name="denombre" id="denombre" value="<?php echo set_value('denombre'); ?>" maxlength="130">
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="decalle">Calle:</label>
		<div class="controls">
			<input type="text" name="decalle" id="decalle" value="<?php echo set_value('decalle'); ?>" maxlength="60">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="deno_exterior">No exterior:</label>
		<div class="controls">
			<input type="text" name="deno_exterior" id="deno_exterior" value="<?php echo set_value('deno_exterior'); ?>" maxlength="7">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="deno_interior">No interior:</label>
		<div class="controls">
			<input type="text" name="deno_interior" id="deno_interior" value="<?php echo set_value('deno_interior'); ?>" maxlength="7">
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="decolonia">Colonia:</label>
		<div class="controls">
			<input type="text" name="decolonia" id="decolonia" value="<?php echo set_value('decolonia'); ?>" maxlength="60">
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="delocalidad">Localidad:</label>
		<div class="controls">
			<input type="text" name="delocalidad" id="delocalidad" value="<?php echo set_value('delocalidad'); ?>" maxlength="45">
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="demunicipio">Municipio / Delegación:</label>
		<div class="controls">
			<input type="text" name="demunicipio" id="demunicipio" value="<?php echo set_value('demunicipio'); ?>" maxlength="45">
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="deestado">Estado:</label>
		<div class="controls">
			<input type="text" name="deestado" id="deestado" value="<?php echo set_value('deestado'); ?>" maxlength="45">
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="decp">CP:</label>
		<div class="controls">
			<input type="text" name="decp" id="decp" value="<?php echo set_value('decp'); ?>" maxlength="10">
		</div>
	</div>
	
	<div class="clear"></div>
</div>

<h3><a href="#">Contactos</a></h3>
<div>
	<table class="tblListados corner-all8">
		<tr class="header btn-gray">
			<td>Nombre</td>
			<td>Puesto</td>
			<td>Teléfono</td>
			<td>Extensión</td>
			<td>Celular</td>
			<td>Nextel</td>
			<td>ID Nextel</td>
			<td>Fax</td>
		</tr>
		<tr>
			<td><div class="controls">
			<input type="text" name="dcnombre" value="<?php echo set_value('dcnombre'); ?>" size="30"></td>
			<td><div class="controls">
			<input type="text" name="dcpuesto" value="<?php echo set_value('dcpuesto'); ?>" size="10"></td>
			<td><div class="controls">
			<input type="text" name="dctelefono" value="<?php echo set_value('dctelefono'); ?>" size="14"></td>
			<td><div class="controls">
			<input type="text" name="dcextension" value="<?php echo set_value('dcextension'); ?>" size="10"></td>
			<td><div class="controls">
			<input type="text" name="dccelular" value="<?php echo set_value('dccelular'); ?>" size="14"></td>
			<td><div class="controls">
			<input type="text" name="dcnextel" value="<?php echo set_value('dcnextel'); ?>" size="14"></td>
			<td><div class="controls">
			<input type="text" name="dcnextel_id" value="<?php echo set_value('dcnextel_id'); ?>" size="14"></td>
			<td><div class="controls">
			<input type="text" name="dcfax" value="<?php echo set_value('dcfax'); ?>" size="14"></td>
		</tr>
	</table>
</div>
