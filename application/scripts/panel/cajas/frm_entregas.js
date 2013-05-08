$(function(){
  $('#dfecha').datepicker({
    dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
    //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
    changeMonth: true, //permite modificar los meses (true o false)
    changeYear: true, //permite modificar los años (true o false)
    //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
    numberOfMonths: 1, //muestra mas de un mes en el calendario, depende del numero
  });

  $("#dproductor").autocomplete({
    source: base_url + 'panel/cajas/ajax_get_productores',
    minLength: 1,
    selectFirst: true,
    select: function( event, ui ) {
      $("#did_productor").val(ui.item.id);
      $("#dproductor").val(ui.item.label);
    }
  }).keydown(function(e){
    if (e.which === 8) {
      $(this).val('');
      $('#did_productor').val('');
    }
  });

  $("#ddueno").autocomplete({
    source: base_url + 'panel/cajas/ajax_get_duenos_huertas',
    minLength: 1,
    selectFirst: true,
    select: function( event, ui ) {
      $("#did_dueno").val(ui.item.id);
      $("#ddueno").val(ui.item.label);
    }
  }).keydown(function(e){
    if (e.which === 8) {
      $(this).val('');
      $('#did_dueno').val('');
    }
  });

  $('#btn-add-trata').on('click', function(e) {
    var trata = $('#did_tratamiento option:selected'),
        cant = $('#dcantidad_trata'),
        tableTbody = $('#table-tratamientos').find('tbody'),
        trHtml = '';

        if (trata.val() !== '' && cant.val() !== '') {
          if ($('#trata'+trata.val()).length === 0){
            trHtml = '<tr id="trata'+trata.val()+'">' +
                      '<td>'+trata.text()+'<input type="hidden" name="did_tratamiento[]" value="'+trata.val()+'"></td>' +
                      '<td><input type="text" name="dcantidad_trata[]" class="span6 vinteger" value="'+cant.val()+'" style="display: inline-block;">' +
                          '<a href="javascript:void(0)" attr-del="'+trata.val()+'" style="margin-left: 3px;"><i class="icon-remove"></i></a>' +
                      '</td>' +
                    '</tr>';

            $(trHtml).appendTo(tableTbody);

            $(".vinteger").removeNumeric().numeric({ decimal: false });

            $('#did_tratamiento').val('');
            cant.val('');
          }
        } else {
          noty({"text": 'Seleccione un tipo de tratamiento y/o cantidad', "layout":"topRight", "type": 'error'});
        }
  });

  $('#table-tratamientos').on('click', 'a', function(event) {
    var id = $(this).attr('attr-del');
    $('#trata'+id).remove();
  });

});