$(function(){
	//evento del buscador de productos
	$("#buscar_pr").on('keydown', function(event){
		if(event.which == 13){
			event.preventDefault();
			buscarProductos(0);
		}
	});
	
	//seleccionar productos registrados
	$(document).on('click', ".tr-producreg", selProductoRegistrado);
	$(document).on('dblclick', ".tr-producreg", function(){
		addProductoLista(this, $(this));
	});
	//seleccionar productos de consumo
	$(document).on('click', ".tr-produclista", selProductoConsumo);
	$(document).on('dblclick', ".tr-produclista", function(){
		quitProductoLista(this, $(this));
	});
	
	//agregar productos a la lista de consumo
	$(".btnaddpro").on("click", addProductoLista);
	//quita productos de la lista de consumo
	$(".btnquitpro").on("click", quitProductoLista);
});


/**
 * Selecciona un producto de la lista de productos de consumo
 * para quitarlo
 */
function selProductoConsumo(){
	// $(".tr-produclista").removeClass("tractiva-pl");
	var vthis = $(this);
	if (vthis.is('.tractiva-pl'))
		vthis.removeClass("tractiva-pl");
	else
		vthis.addClass("tractiva-pl");
}

/**
 * Selecciona un producto de la lista de productos registrados,
 * para agregarlos a consumos
 */
function selProductoRegistrado(){
	// $(".tr-producreg").removeClass("tractiva-pr");
	var vthis = $(this);
	if (vthis.is('.tractiva-pr'))
		vthis.removeClass("tractiva-pr");
	else
		vthis.addClass("tractiva-pr");
}

/**
 * Agrega un producto registrado al listado de productos
 * de consumo
 */
function addProductoLista(obj, trsel){
	trsel = (trsel==undefined? $(".tractiva-pr"): trsel), tagid="";
	if(trsel.length > 0){ //valida q este seleccionado uno
		trsel.each(function(){
			var trselts = $(this);
			tagid = trselts.attr("data-id");
			
			if($("#tr-pl"+tagid).length == 0){ //valida q no exista en la lista
				$("#tbl-pl").append(
				'<tr id="tr-pl'+tagid+'" class="tr-produclista">'+
				'	<td style="width:66%;">'+$("td:first", trselts).text()+'<input type="hidden" name="dpcnombres[]" value="'+$("td:first", trselts).text()+'">'+
				'		<input type="hidden" name="dpcids[]" value="'+tagid+'"></td>'+
				'	<td style="width:33%;"><input type="text" name="dpccantidad[]" value="1" class="span12"></td>'+
				'</tr>');
			}else
				noty({"text":"El producto ya está agregado a la lista.", "layout":"topRight", "type":"ok"});
		});
		
		$(".tr-producreg").removeClass("tractiva-pr");
	}else
		noty({"text":"Selecciona un producto de la lista -Productos registrados-", "layout":"topRight", "type":"info"});
}

function quitProductoLista(obj, trsel){
	var trsel = (trsel==undefined? $(".tractiva-pl"): trsel);
	if(trsel.length > 0){
		trsel.remove();
		//$(".tr-produclista").removeClass("tractiva-pl");
	}else
		noty({"text":"Selecciona un producto de la lista -Productos que consume-", "layout":"topRight", "type":"info"});
}

/**
 * Busca productos registrados en agregar y modificar productos
 * @param pag
 */
function buscarProductos(pag){
	pag = parseInt(pag);
	var txtbuscar = $("#buscar_pr").val(), pagin = (pag>0? '&pag='+pag: ''), modf=$("#id_producto").val();
	
	modf = (modf != undefined? '&id_producto='+modf: '');
	
	$.get(base_url+"panel/productos/ajax_productos_addmod/",
		"fnombre="+txtbuscar+pagin+modf,
		function(data){
			$("#tbl_productos_r").html(data);
	});
}

