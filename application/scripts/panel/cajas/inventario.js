$(function(){
  $("#ffecha1, #ffecha2").datepicker({
     dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
     changeMonth: true, //permite modificar los meses (true o false)
     changeYear: true, //permite modificar los años (true o false)
     numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
   });

  //reporte de cajas recibidas
  if($("#rcr")){
  	$("#ffecha1, #ffecha2").on("change", function(){
  		$("#rcr").attr("href", ($("#rcr").attr("data-href")+"?ffecha1="+$("#ffecha1").val()+"&ffecha2="+$("#ffecha2").val()) );
  	});
  }
});