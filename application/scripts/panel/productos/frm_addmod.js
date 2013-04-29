$(function(){

	$("#dproveedor").autocomplete({
      source: base_url+'panel/proveedores/ajax_get_proveedor',
      minLength: 1,
      selectFirst: true,
      select: function( event, ui ) {
        $("#did_proveedor").val(ui.item.id);
        $("#dproveedor").css("background-color", "#B0FFB0");
      }
  }).on("keydown", function(event){
      if(event.which == 8 || event == 46){
        $("#dproveedor").css("background-color", "#FFD9B3");
        $("#did_proveedor").val("");
      }
  });

});
