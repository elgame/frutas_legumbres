$(function(){

  // Evento Change al select del tipo de pago
  $('#dmetodo').on('change', function(event) {
    var selected = $('#dmetodo option:selected').val();
    if (selected === 'cheque') $('#well').css({display: 'block'})
    else $('#well').css({display: 'none'})
  });

  $('#add-abono').on('click', function(event) {
      validator.init('validator');

      if (validator.existErrors()) {
        noty({"text": validator.errors(), "layout":"topRight", "type": 'error'});
      } else {
        guardaAbono();
      }
  });

  $('#dbanco').on('change', function(event) {
    var select = $(this);
        option = select.find('option:selected').val(),
        optionHtml = '<option value=""></option>',
        selectCuentas = $('#dcuenta');

    $.post(base_url + 'panel/abonos/ajax_get_cuentas_banco/', {'id': option},
      function(data) {
        data.cuentas.forEach(function (e, i) {
          optionHtml += '<option value="'+e.id_cuenta+'">' +
                          e.alias + ' - ' + e.numero + ' (' + util.darFormatoNum(e.saldo) + ')' +
                        '</option>';
        });
        selectCuentas.html(optionHtml);
      }, 'json');
  });


  if ($('#id_abono_masivo').length > 0) {
    var tbody = $('#table-tbody');
    $('#dmonto').prop('disabled','disabled');
    tbody.find('td#masivo').on('click', function(event) {
      var td = $(this),
          status = td.attr('data-status'),
          monto = $('#dmonto');

      // console.log($(this).attr('data-id')  + ' ' + $(this).attr('data-saldo'));

      if (status === 'off') {
        td.attr('data-status', 'on').css('background-color', '#F9C861');
        abono = parseFloat(monto.val()) + parseFloat(td.attr('data-saldo'));
      } else {
        td.attr('data-status', 'off').css('background-color', '#FFF');
        abono = parseFloat(monto.val()) - parseFloat(td.attr('data-saldo'));
      }
      monto.val(abono);

      if (parseFloat(monto.val()) > 0) {
        $('#btn-modal').css({'display': 'inline-block'});

        var ids = [];
        tbody.find('td[data-status="on"]').each(function(event) {
          ids.push($(this).attr('data-id'));
        });
        $('#id_entrada').val(ids.join(','));
      }
      else $('#btn-modal').css({'display': 'none'});

      // console.log(monto.val());
    }).css({'cursor': 'pointer'});
  }

});

var urlTipoAbono = function () {
  if ($('#id_abono_masivo').length > 0)
    return 'panel/abonos/ajax_guarda_abono_masivo/';
  return 'panel/abonos/ajax_guarda_abono/';
};

var guardaAbono = function () {
  var data = {};

  data.id_caja      = $('#id_entrada').val();
  data.id_productor = $('#id_productor').val();

  data.fecha     = $('#dfecha').val();
  data.id_banco  = $('#dbanco option:selected').val();
  data.id_cuenta = $('#dcuenta option:selected').val();
  data.concepto  = $('#dconcepto').val();
  data.monto     = $('#dmonto').val();
  data.tipo      = $('#dtipo').val();
  data.metodo    = $('#dmetodo option:selected').val();

  if ($('#dmetodo option:selected').val() === 'cheque') {
    data.anombrede = $('#danombrede').val();
    data.moneda    = $('#dmoneda option:selected').val();
  }

  $.post(base_url + urlTipoAbono(), data,
    function(response) {
      noty({"text": response.msg, "layout":"topRight", "type": response.ico});
      if (response.passes) {

        if (data.metodo === 'cheque') {

        }
        setTimeout("location.reload(true);",1500);
      }
    },'json');

};