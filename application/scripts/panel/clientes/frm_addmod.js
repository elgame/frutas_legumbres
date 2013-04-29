$(function(){
	asignaAccordion();
	
	//Cambia el formulario si tiene o no sucursales
	$("#dtiene_sucur").on('change', function(){
		var method = ($(this).is(":checked")? "clientes/ajax_frmAddGrupo/": "clientes/ajax_frmAddSucu/");
		loader.create();
		$.get(base_url+"panel/"+method, "", function(resp){
			$("#frmsec-acordion").accordion("destroy");
			$("#frmsec-acordion").html(resp);
			asignaAccordion();
		}).complete(function(){ 
	    	loader.close(); 
	    });
	});
	
	$(document).on('change', "#demismos_facturacion", mismosDatosFacturacion);
});

function mismosDatosFacturacion(){
	if($(this).is(":checked")){
		$("#denombre").val($("#dnombre_fiscal").val());
		$("#decalle").val($("#dcalle").val());
		$("#deno_exterior").val($("#dno_exterior").val());
		$("#deno_interior").val($("#dno_interior").val());
		$("#decolonia").val($("#dcolonia").val());
		$("#delocalidad").val($("#dlocalidad").val());
		$("#demunicipio").val($("#dmunicipio").val());
		$("#deestado").val($("#destado").val());
		$("#decp").val($("#dcp").val());
	}else{
		$("#denombre").val("");
		$("#decalle").val("");
		$("#deno_exterior").val("");
		$("#deno_interior").val("");
		$("#decolonia").val("");
		$("#delocalidad").val("");
		$("#demunicipio").val("");
		$("#deestado").val("");
		$("#decp").val("");
	}
	$("#denombre").focus();
}

/**
 * Crea el accordion
 */
function asignaAccordion(){
	$("#frmsec-acordion").accordion({
		autoHeight: false
	});
}


var contador_contacto = 0;
/**
 * Agrega un row form para registrar un nuevo contacto
 * @param id_tbl
 * @param obj
 */
function addContacto(id_tbl, obj){
	if(contador_contacto == 0){
		//creo un row form para agregar contacto
		$("#"+id_tbl).append(
			'<tr id="tr_addcontacto'+contador_contacto+'">'+
			'	<td><input type="text" name="dcnombre" value="" class="input-small"></td>'+
			'	<td><input type="text" name="dcpuesto" value="" class="input-small"></td>'+
			'	<td><input type="text" name="dctelefono" value="" class="input-small"></td>'+
			'	<td><input type="text" name="dcextension" value="" class="input-small"></td>'+
			'	<td><input type="text" name="dccelular" value="" class="input-small"></td>'+
			'	<td><input type="text" name="dcnextel" value="" class="input-small"></td>'+
			'	<td><input type="text" name="dcnextel_id" value="" class="input-small"></td>'+
			'	<td><input type="text" name="dcfax" value="" class="input-small"></td>'+
			'	<td><a href="'+obj.href+'" title="Guardar contacto" class="btn btn-success" onclick="agregarContacto(\'tr_addcontacto'+contador_contacto+'\', this); return false;">'+
			'		<i class="icon-plus icon-white"></i> </a></td>'+
			'</tr>');
		$("#tr_addcontacto"+contador_contacto+" input[name=dcnombre]").focus();
	}
	contador_contacto++;
}
/**
 * Permite enviar la peticion por Ajax para agregar el contacto
 * @param id_tr
 * @param obj
 */
function agregarContacto(id_tr, obj){
	var data = "";
	$("#"+id_tr+" input").each(function(){
		data += this.name+"="+this.value+"&";
	});
	
	loader.create();
	$.post(obj.href, data, function(resp){
		noty({"text": resp.msg.msg, "layout":"topRight", "type": resp.msg.ico});
		if(resp.msg.ico == 'success'){
			//si es OK se elimina el row form
			$("#"+id_tr).remove();
			contador_contacto = 0;
		}
		
		if(resp.info){
			//comprueba si tiene el permiso de eliminar contacto
			var priv_eliminar_contacto = '';
			if($("#priv_eliminar_contacto").length > 0)
				priv_eliminar_contacto = '<a class="btn btn-danger" href="'+base_url+'panel/clientes/eliminar_contacto/?id='+resp.info.id_contacto+'" onclick="msb.confirm(\'Estas seguro de eliminar el contacto?\', \'Contacto\', this, eliminaContacto); return false;" title="Eliminar Contacto">'
							+'<i class="icon-remove icon-white"></i> <span class="hide">Eliminar Contacto</span></a>';
			
			//Agrego el tr con la informacion del contacto agregado
			$("#tbl_contactos").append(
			'<tr>'+
			'	<td>'+resp.info.nombre+'</td>'+
			'	<td>'+resp.info.puesto+'</td>'+
			'	<td>'+resp.info.telefono+'</td>'+
			'	<td>'+resp.info.extension+'</td>'+
			'	<td>'+resp.info.celular+'</td>'+
			'	<td>'+resp.info.nextel+'</td>'+
			'	<td>'+resp.info.nextel_id+'</td>'+
			'	<td>'+resp.info.fax+'</td>'+
			'	<td>'+
					priv_eliminar_contacto+
			'	</td>'+
			'</tr>');
		}
	}, "json").complete(function(){ 
    	loader.close(); 
    });
}

function eliminaContacto(obj){
	loader.create();
	$.post(obj.href, "", function(resp){
		noty({"text": resp.msg.msg, "layout":"topRight", "type": resp.msg.ico});
		if(resp.msg.ico == 'success'){
			//si es OK se elimina el row form
			$(obj).parents("tr").remove();
		}
	}, "json").complete(function(){ 
    	loader.close(); 
    });
}

