$(function(){

	tbl_conceptos.init();
});


var tbl_conceptos = (function($){
	var objr={}, conta_products=0;

	function init(){
		//cargar cuentas de bancos
		var dbanco = $("#dbanco").on('change', loadCuentasBanco);
		if(dbanco.val() != '') //si esta seleccionado 1
			loadCuentasBanco(this, dbanco);

		//evitar enviar el form en los conceptos reales
		$("form :input").on("keypress", function(e) {
	    return e.keyCode != 13;
	  });
		$("#addcons_concep, #addcons_monto, #addcons_agregar").on('keydown', function(event){
			if (event.which == 13) {
				addConcepto();
				event.preventDefault();
      	return false;
			}
		});
		$("#addcons_agregar").on('click', addConcepto);

		//evento quitar produc
		$(document).on('click', '#table_prod .btn.btn-danger', quitProduct);

		//si es retiro y se paga con cheque se imprime un cheque
		$("#dmetodo_pago, #dtipo_operacion").on('change', datosParaCheque);
		datosParaCheque();
	}

	function loadCuentasBanco(obj, banco){
		var dcuenta_load = $("#dcuenta_load").text(); //cuando se carga el form de facturacion productores
		banco = banco? banco: $(this);
		$.getJSON(base_url+'panel/banco/ajax_get_cuentas', "id="+banco.val(), function(data){
			var cuentas = $("#dcuenta")
			cuentas.html('');
			if (data.ico){
				noty({"text": data.msg, "layout":"topRight", "type": data.ico});
			}else{
				var html = '';
				for (var i = 0; i < data.cuentas.length; i++) {
	        html += '<option value="'+data.cuentas[i].id_cuenta+'" '+(dcuenta_load==data.cuentas[i].id_cuenta? 'selected': '')+'>'+
	                  data.cuentas[i].alias+' ('+util.darFormatoNum(data.cuentas[i].saldo)+')</option>';
				};
				cuentas.html(html);
			}
		});
	}

	function addConcepto(){
		if (valAddConcepto()){
			var obj = $("#table_prod tbody"), html = '<tr id="prd'+conta_products+'">'+
	                  '<td><input type="text" name="dconcep_conce[]" value="'+($("#addcons_concep").val())+'" class="span12" maxlength="254"></td>'+
	                  '<td><input type="text" name="dconcep_monto[]" value="'+($("#addcons_monto").val())+'" class="span12 vpositive"></td>'+
	                  '<td><button type="button" class="btn btn-danger" data-id="'+conta_products+'"><i class="icon-trash"></i></button></td>'+
	                '</tr>';
			$("#table_prod tbody").append(html);
			$("#prd"+conta_products+" .vpositive").numeric({ negative: false });
			conta_products++;

			calculaTotal();
			cleanFields();
		}else{
			noty({"text": "Completa los campos para agregar el concepto.", "layout":"topRight", "type": 'warning'});
		}
	}

	function quitProduct(){
		$("#prd"+$(this).attr("data-id")).remove();
		calculaTotal();
	}

	function calculaTotal(){
		var total = 0;
		$("#table_prod tbody tr input[name^=dconcep_monto]").each(function(){
			total += parseFloat($(this).val());
		});
		$("#total_concepts").text( util.darFormatoNum(total) );
	}

	function cleanFields(){
		$("#addcons_concep").val('').focus();
		$("#addcons_monto").val('');
	}

	function valAddConcepto(){
		if ($.trim($("#addcons_concep").val()) != '' && $.trim($("#addcons_monto").val()) != '') {
			return true;
		}
		return false;
	}


	/**
	 * muestra o no, los datos extra que van en un cheque
	 */
	function datosParaCheque(){
		var dmetodo_pago = $("#dmetodo_pago"), dtipo_operacion = $("#dtipo_operacion");
		if (dtipo_operacion.val() == 'r' && dmetodo_pago.val() == 'cheque') {
			$(".only_cheques").removeClass('hide');
			$("#dchk_anombre").attr('required', 'true').focus();
		}else{
			$(".only_cheques").addClass('hide');
			$("#dchk_anombre").removeAttr('required');
		}
	}

	objr.init = init;
	return objr;
})(jQuery);