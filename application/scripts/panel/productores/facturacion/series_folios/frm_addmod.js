$(document).ready(function(){

	$("#fano_aprobacion").datepicker({
		 dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
		 // //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
		 // changeMonth: true, //permite modificar los meses (true o false)
		 // changeYear: true, //permite modificar los años (true o false)
		 // //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
		 numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
	 });

	$("#dproductor").autocomplete({
      source: base_url+'panel/productores/ajax_get_productores?type=f',
      minLength: 1,
      selectFirst: true,
      select: function( event, ui ) {
        $("#did_productor").val(ui.item.id);
        $("#dproductor").css("background-color", "#B0FFB0");
      }
  }).on("keydown", function(event){
      if(event.which == 8 || event == 46){
        $("#dproductor").css("background-color", "#FFD9B3");
        $("#did_productor").val("");
      }
  });

});
