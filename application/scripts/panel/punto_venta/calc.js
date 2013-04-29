$(function(){
  calculadora.init('#calculadora');
});

var calculadora = (function($) {
  var out = {},
      value = '',
      displayCalc,
      total = recibido = cambio = 0;

  var initialize = function(wrapper) {
    displayCalc = $('#calcDisplay');
    $(wrapper).find('button').on('click', function(event) {
      setBtnEvent($(this));
    });
  };

  var setBtnEvent = function(obj) {
    var btn = obj.html();
    switch(btn) {
      case '1':
      case '2':
      case '3':
      case '4':
      case '5':
      case '6':
      case '7':
      case '8':
      case '9':
      case '0':
                addNumber(btn);
                break;
      case '.':
                addPoint();
                break;
      case 'C':
                resetValue();
                break;
      case 'ACEPTAR':
                aceptValue();
                break;
      default:
            noty({"text": 'Boton no validado!', "layout":"topRight", "type": 'error'});
    }
  };

  /**
   * Agrega un Numero al valor actual
   */
  var addNumber = function(num) {

    if (value === '0') {
      value = '';
    }

    value += num;
    displayCalc.val(value);
  };

  /**
   * Agrega el punto "." al valor
   */
  var addPoint = function() {
    // console.log(/^(\d+\.\d*|\.\d+)$/.test(value));
    if (/^(\d+\.\d*|\.\d+)$/.test(value) === false) {
      value += '.';
      displayCalc.val(value);
    }
  };

  /**
   * Resetea la calculadora
   */
  var resetValue = function() {
    value = '0';
    displayCalc.val(value)
  };

  /**
   * Este metodo realiza alguna operacion al momento de dar click al boton
   * ACEPTAR
   */
  var aceptValue = function() {

    if (value !== '0') {
      total    = parseFloat($('#itotalv').val());
      recibido = parseFloat(value);
      cambio   = recibido - total;

      $('#tvrecibido').html(util.darFormatoNum(recibido));
      $('#itvrecibido').val(recibido);

      $('#tvcambio').html(util.darFormatoNum(cambio));
      $('#itvcambio').val(cambio);

      resetValue();
    }

  };

  // METODOS PUBLICOS
  out.init = initialize;
  out.reset = resetValue;

  return out;
})(jQuery);