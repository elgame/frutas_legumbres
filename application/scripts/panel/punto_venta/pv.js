$(function(){

  setDynamicHeight();

  calculadoraPv.init();

  var ul_level_0 = $('#familiaArea[data-level="0"]').find('ul'),
      ul_level_1 = $('#familiaArea[data-level="1"]').find('ul'),
      ul_level_2 = $('#familiaArea[data-level="2"]').find('ul');

  $('div#familiaArea').on('click', 'li#item', function(event) {
    // alert($(this).attr('data-id') + ' - ' + $(this).attr('data-last-nodo') + ' - ' + $(this).parent().parent().attr('data-level'));

    var data_id_padre  = $(this).attr('data-id-padre'), // almancea el id padre al que pertenece
        data_id        = $(this).attr('data-id'), // almacena el id
        data_last_nodo = $(this).attr('data-last-nodo'), // almacena el last_nodo
        data_level     = $(this).parent().parent().attr('data-level'), // almacena el nivel en que se encuentra
        data_precio    = parseFloat($(this).attr('data-precio')), // almacena el precio del producto
        nombre         = $(this).find('h3').html(), // almacena el nombre del producto
        li_html        = '',  // contendra los elemento <li> que se construyen para los hijos
        li_padres_html = ''; // contendra los elemento <li> que se construyen para los padres


    // Si el item <li> que se le da click es padre
    if (data_last_nodo == 0) {

      $.get(base_url + 'panel/punto_venta/ajax_get_hijos', {'id': data_id}, function(data) {
        // console.log(data);
        li_html = build_padres_hijos(data); // pasa data a la funcion para contruir los tag <li> de los hijos

        // Si el nivel o columna en la que esta actualmente es la primer columna
        if (data_level == 0) {

          // Si el item <li> que se le dio click no es padre principal
          if (data_id_padre != 1) {

            $.get(base_url + 'panel/punto_venta/ajax_get_padres', {'id_padre': data_id_padre}, function(data2) {

              li_padres_html = build_padres_hijos(data2); // pasa data2 a la funcion para contruir los tag <li> de los padres

              ul_level_1.html(ul_level_0.html()); // mueve el contenido del nivel 0 en el nivel 1
              ul_level_0.html(li_padres_html); // asigna en el nivel 1 el li_padres
              ul_level_2.html(li_html); // asigna en el nivel 2 el li

            }, "json");

          } else { // Si el item <li> que se le dio click es padre principal
            ul_level_1.html(li_html)
            ul_level_2.html('');
          }

        } else if (data_level == 1) { // Si el nivel o columna en la que esta actualmente es la segunda columna
          ul_level_2.html(li_html);

        } else if (data_level == 2) { // Si el nivel o columna en la que esta actualmente es la tercer columna
          ul_level_0.html(ul_level_1.html());
          ul_level_1.html(ul_level_2.html());
          ul_level_2.html(li_html);
        }

      }, "json");

    } else { // Si el item <li> que se le da click es ultimo nodo

      var fromCalculadora = calculadoraPv.fromCalculadora(); // Sirve para saber si existe un valor desde la calculadora

      if ($('#tr' + data_id).length === 0) { // Si el Producto no existe en el listado

        var cantidad = 1;
        if (fromCalculadora) { // Valida si existe una canti dad desde la calculadora
          cantidad = calculadoraPv.getValor(); // Obtiene la cantidad
        }

        var tr_html = '<tr id="tr' + data_id + '">' +
                        '<td id="td-nombre">' + nombre +
                            '<input type="hidden" id="iid" value="' + data_id + '">' +
                            '<input type="hidden" id="icantidad" value="' + cantidad + '">' +
                            '<input type="hidden" id="iprecio" value="' + data_precio + '">' +
                            '<input type="hidden" id="itotal" value="' + (cantidad * data_precio) + '">' +
                            '<input type="hidden" id="inombre" value="' + nombre + '">' +
                        '</td>' +
                        '<td id="td-cantidad">' + cantidad + '</td>' +
                        '<td id="td-total">' + util.darFormatoNum(cantidad * data_precio) + '</td>' +
                      '</tr>';

        $(tr_html).appendTo('#table-listado').find('tbody');

      } else { // Si el producto existe en el listado

        var new_cantidad = 1;
        if (fromCalculadora) { // Valida si existe una cantidad desde la calculadora
          new_cantidad = calculadoraPv.getValor(); // Obtiene la cantidad
        }

        var tr = $('#tr' + data_id),
            cantidad = parseInt(tr.find('#icantidad').val(), 10) + new_cantidad;

        tr.find('#td-cantidad').html(cantidad);
        tr.find('#icantidad').val(cantidad);
        tr.find('#td-total').html(util.darFormatoNum(cantidad * data_precio));
        tr.find('#itotal').val(cantidad * data_precio);

      }

      calcula_total();
    }

  });

  $('#table-listado').on('click', 'tbody tr', function(event) {
    if ($('.clicked').length === 3) {
      if ($('td.clicked').parent().attr('id') !== $(this).attr('id')) {
        $('td.clicked').toggleClass('clicked');
      }
    }
    $(this).find('td').toggleClass('clicked');
  });

  // Evento Click Btn "finalizar"
  $('#save-venta').on('click', function(event) {

    if (parseFloat($('#itvrecibido').val()) > 0) {

      if (parseFloat($('#itvcambio').val()) >= 0) {

        var venta = [], tr;

        venta.push($('#itotalv').val());
        venta.push($('#itvrecibido').val());
        venta.push($('#itvcambio').val());

        $('#table-listado').find('tbody tr').each(function(i, e) {
            tr = $(this);

            venta.push([tr.find('#iid').val(),
                          tr.find('#icantidad').val(),
                          tr.find('#iprecio').val(),
                          tr.find('#itotal').val()]);
        });

        $.post(base_url + 'panel/punto_venta/ajax_save_venta/', {'venta[]': venta}, function(data) {

          console.log(data);

          noty({"text": data[1].msg, "layout":"topRight", "type": data[1].ico});

          var win=window.open(base_url + 'panel/punto_venta/imprime_ticket?id=' + data[0] + '&e=' + $('#itvrecibido').val(), '_blank');
          win.focus();

          setTimeout("location.reload(true);",1500);
        },'json');

      } else {
        noty({"text": 'La cantidad recibida no puede ser menor que el total', "layout":"topRight", "type": 'error'});
      }

    } else {
      noty({"text": 'Especifique la cantidad recibida', "layout":"topRight", "type": 'error'});
    }

  });

});


/**
 * Obtiene el Alto visible del browser y lo reasigna a los elementos del
 * ticket y de las familias&productos
 */
var setDynamicHeight = function() {
  var browserVisibleHeight = $(window).outerHeight(), // Alto visible del browser
      titleHeight = $('.text-title').outerHeight(), // Alto de los titulos
      totalAreaHeight = $('#totalArea').outerHeight(), // Alto del Total
      calcAreaHeight = $('#calcArea').outerHeight(); // Alto de la calculadora

      // console.log('browser: ' + browserVisibleHeight);
      // console.log('titulos: ' + titleHeight);
      // console.log('total: ' + totalAreaHeight);
      // console.log('calculadora: ' + calcAreaHeight);

  // Si el Alto visible del browser es mayor a 0 entra
  if (browserVisibleHeight > 0) {
    // alert(browserVisibleHeight);

    // Reasigna el height y max-height de familias&productos
    $('#ticketTotalCalcArea').css({'height': browserVisibleHeight});
    $('#ticketArea').css({'height': browserVisibleHeight - titleHeight - totalAreaHeight - calcAreaHeight - 15});

    // Reasigna el height y max-height de familias&productos
    $('#productosArea').css({'height': browserVisibleHeight, 'max-height': browserVisibleHeight});
    $('div#familiaArea').css({'height': browserVisibleHeight - titleHeight , 'max-height': browserVisibleHeight - titleHeight});
  }
};

/**
 * Recibe data tipo json y contruye los tags <li> y los retorna
 */
var build_padres_hijos = function(data) {
  var li_html = '', i, tiene_imagen;
  // cliclo for para constriuir los elementos <li></li> de los productos o familias
  for (i in data) {

    // console.log(data[i]);
    tiene_imagen = false;
    if (data[i].imagen !== null && data[i].imagen !== '') {
      tiene_imagen = true;
    }

    li_html += '<li class="span12" id="item" data-id-padre="'+data[i].id_padre+'" data-id="'+data[i].id+'" data-last-nodo="'+data[i].ultimo_nodo+'" data-precio="'+data[i].precio_venta+'">' +
               '<div class="thumbnail" '+ ((data[i].color1 !== null && data[i].color2 !== null) ? 'style="background: -webkit-linear-gradient(top,  ' + data[i].color1 + ' 0%, ' + data[i].color2 + ' 100%);"' : '') + '>' +
                 '<div class="caption" ' + ((!tiene_imagen) ? 'style="display: table;"' : '') + '>' +
                   ' ' + ((tiene_imagen) ? '<img src="' + base_url + 'application/images/familias/' + data[i].imagen + '" width="80" height="80">' : '') +

                   '<h3 ' + ((!tiene_imagen) ? 'style="vertical-align: middle; display: table-cell;"' : '') + '>' + data[i].nombre + '</h3>' +
                 '</div>' +
               '</div>' +
             '</li>';
  }
  return li_html;
}

/**
 * Calcula el total de la venta
 */
var calcula_total = function() {
  var ttotal = 0;
  $('input#itotal').each(function(event) {
    ttotal += parseFloat($(this).val());
  });

  $('#ttotal').html(util.darFormatoNum(ttotal));
  $('#itotalv').val(ttotal);
}



var calculadoraPv  = (function($){

  var out = {}, // Objeto para almacenar los metodos que estaran publicos
      arrayCant = []; // array que contendra la cantidad de veces que se agregara un producto. ['5', '*']

  /**
   * Inicializa la Calculadora
   */
  var initialize = function() {
    $('#calcArea').find("button").on('click', function(event) {
      detectButton(this);
    });
  };

  /**
   * Asigan metodos segun el tipo de boton detectado
   */
  var detectButton = function(button) {
    var val = $(button).html();

    switch (val) {
      case '0':
      case '1':
      case '2':
      case '3':
      case '4':
      case '5':
      case '6':
      case '7':
      case '8':
      case '9':
                setCantidad(val);
                break;
      case '*':
                setSignoPor();
                break;
      case '-':
                delItem();
                break;
      case 'Supr':
                suprItem();
                break;
      case 'C':
                clearLista();
                break;
      case 'ENTER':
                terminarVenta();
                break;
      default:
                noty({"text": 'Boton no validado!', "layout":"topRight", "type": 'error'});
    }
  };

  /**
   * Metodo para los botonos 1 al 9
   * Contruye el array que almacenara la cantidad de veces que se agregara un
   * producto al darle click (touch)
   * @return {[type]} [description]
   */
  var setCantidad = function(num) {
    var leng = arrayCant.length;
    if (leng === 2) {
      arrayCant = [num];
    } else if (leng === 1){
      arrayCant[0] = arrayCant[0] + '' + num;
    } else {
      arrayCant.push(num)
    }
  };

  /**
   * Metodo para el boton "*"
   */
  var setSignoPor = function() {
    if (arrayCant.length !== 0) {
      if (arrayCant.length !== 2) {
        arrayCant.push("*");
      } else {
        noty({"text": 'Seleccione un Producto', "layout":"topRight", "type": 'error'});
      }
    } else {
      noty({"text": 'Seleccione una Cantidad', "layout":"topRight", "type": 'error'});
    }
  };

  /**
   * Retorna el arrayCant
   * @return {Array}
   */
  var getArrayCant = function() {
    return arrayCant;
  };

  /**
   * Retorna la cantidad que se almaceno desde la calculadora
   */
  var getValor = function() {
    var val = arrayCant[0];
    arrayCant = [];
    return parseInt(val, 10);
  };

  /**
   * Valida si el arrayCant esta contruido correctamente
   * @return boolean
   */
  var isCantidadValida = function() {
    if (arrayCant.length === 2) {
      return true;
    } else {
      return false;
    }
  };

  /**
   * Valida si se pasara un valor desde la calculadora
   * @return boolean
   */
  var existValFromCalc = function() {
    if (arrayCant.length > 0) {
      return true;
    } else {
      return false;
    }
  };

  /**
   * Decrementa la cantidad de un Item en 1 y se recalcula el total, si el item
   * llega a ser 0 se elimina del listado.
   */
  var suprItem = function() {
    var item = $('.clicked'),
        parent = item.parent();

    if (item.length !== 0) { // Si Existe un Item del listado seleccionado
      new_cantidad = parseInt(parent.find('#icantidad').val(), 10) - 1; // Obtiene la cantidad del listado y le resta 1

      if (new_cantidad === 0) { // Si la cantidad es 0 entonces lo elimina del listado
        parent.remove();
      } else {
       parent.find('#icantidad').val(new_cantidad);
       parent.find('#td-cantidad').html(new_cantidad);

       parent.find('#itotal').val(new_cantidad * parseFloat($(parent).find('#iprecio').val()));
       parent.find('#td-total').html(util.darFormatoNum(new_cantidad * parseFloat($(parent).find('#iprecio').val())));
      }
      calcula_total(); // Recalcula el total de la venta
    } else { // Si no existe ningun item del listado seleccionado muestra un msg
      noty({"text": 'Seleccione un item del listado para poder realizar la operación', "layout":"topRight", "type": 'error'});
    }
  };

  /**
   * Elimina un Item del listado completamente.
   */
  var delItem = function() {
    var item = $('.clicked');

    if (item.length !== 0) {
      item.parent().remove();
      calcula_total();
    } else {
      noty({"text": 'Seleccione un item del listado para poder realizar la operación', "layout":"topRight", "type": 'error'});
    }
  }

  /**
   * Limpia el listado, elimina todo lo que contenga.
   */
  var clearLista = function() {
    $('#table-listado').find('tbody').html('');
    calcula_total();
  };

  var terminarVenta = function() {
    if ($('#table-listado').find('tbody tr').length > 0) {
      $('#myModal').modal('toggle');
      $('#tvtotal').html(util.darFormatoNum($('#itotalv').val()));

      $('#tvrecibido').html('$0.00');
      $('#itvrecibido').val(0);

      $('#tvcambio').html('$0.00');
      $('#itvcambio').val(0);

      calculadora.reset();
    } else {
      noty({"text": 'Agrege Items/Productos al listado', "layout":"topRight", "type": 'error'});
    }
  };

  // Declara los metodos que seran publicos
  out.init            = initialize;
  out.getArrayCant    = getArrayCant;
  out.fromCalculadora = existValFromCalc;
  out.isValido        = isCantidadValida;
  out.getValor        = getValor;

  return out;
})(jQuery);
