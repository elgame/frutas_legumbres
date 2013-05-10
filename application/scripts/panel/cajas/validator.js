var validator = (function () {

  // Variables
  var out = {}, // Objeto para retornar las funciones que seran globales
      parent, // Elem padre donde estan todos los elementos a verificar
      arrayElemReq = [], // Almacena los elementos que son requeridos
      errors = []; // Almacena los errores de los elementos requeridos

  /**
   * Inicializa las validaciones
   * @param  string id [Id del elemento que contiene los campos a validar]
   * @return void
   */
  var initialize = function (id) {
    parent = id;
    clearErrors();
    getRequiredFields();
  };

  /**
   * Obtiene los elementos del HTML que son requeridos (aquellos que tienen el
   * atributo "required") y los almacena en un array para posteriormente
   * validarlos
   *
   * @return void
   */
  var getRequiredFields = function () {
    $('#'+parent).find('[required]').each(function (i, e) {
      var elem = $(e);
      arrayElemReq.push(elem);
    });
    validator();
  };

  /**
   * Recorre el array de los elementos "required" y llama otras funciones
   * dependiendo del tipo de elemento (es decir si es un input, select etc etc)
   *
   * @return void
   */
  var validator = function () {
    arrayElemReq.forEach(function (e, i) {
      if (isVisible(e)) {
        if (isInput(e)) validInput(e)
        else if (isSelect(e)) validSelect(e)
        else if (isTextArea(e)) validTextArea(e)
      }
    });
  };

  /*
  --------------------------------------------------------------------------
  |                 VALIDACIONES PARA CADA TIPO DE
  |             ELEMENTO, YA SEA INPUT, SELECT, ETC ETC
  --------------------------------------------------------------------------
  */

  /**
   * Realiza la validacion para un elemento tipo "input", si el elemento
   * que se esta validando lanza un error entonces inyecta el error en el
   * array de los errores.
   *
   * @param  obj elem [Elemento a validar]
   *
   * @return void
   */
  var validInput = function (elem) {
    var value = elem.val();

    if (value === '') {
      errors.push('El campo ' + getElemName(elem) + ' es requerido');
    }
  };

  /**
   * Realiza la validacion para un elemento tipo "select", si el elemento
   * que se esta validando lanza un error entonces inyecta el error en el
   * array de los errores.
   *
   * @param  obj elem [Elemento a validar]
   *
   * @return void
   */
  var validSelect = function (elem) {
    var value = elem.find('option:selected').val();

    if (value === '') {
      errors.push('El campo ' + getElemName(elem) + ' es requerido');
    }
  };

  /**
   * Realiza la validacion para un elemento tipo "select", si el elemento
   * que se esta validando lanza un error entonces inyecta el error en el
   * array de los errores.
   *
   * @param  obj elem [Elemento a validar]
   *
   * @return void
   */
  var validTextArea = function (elem) {
    var value = elem.val();

    if (value === '') {
      errors.push('El campo ' + getElemName(elem) + ' es requerido');
    }
  };

  /*
  --------------------------------------------------------------------------
  |                             HELPERS
  --------------------------------------------------------------------------
  */

  /**
   * Obtiene el nombre que sera asociado con el elemento que tiene error de
   * validacion. Para obtener el nombre se basa en el attributo "name" del
   * elemento validado y busca un elemento tipo LABEL que coincida con el "name"
   * del elemento validado.
   *
   * Si no encuentra un LABEL entonces el nombre asosciado al elemento lo toma
   * del attributo "data-name".
   *
   * Si no encuentra ningo de los dos entonces regresara undefined.
   *
   * @param  obj elem [Elemento a validar]
   *
   * @return String || undefined
   */
  var getElemName = function (elem) {
    var elemAttrName = elem.context.name,
        label = $('label[for="'+elemAttrName+'"]');

    if (label.length !== 0) return label.html();
    else return elem.attr('data-name');
  };

  /**
   * Detecta el tipo de tagName o tag del elemento validado
   *
   * @param  obj elem [Elemento a validar]
   *
   * @return String
   */
  var detectType = function (elem) {
    return elem.context.tagName.toLowerCase();
  };

  /**
   * Valida si un elemento es tipo Input
   *
   * @param  obj elem [Elemento a validar]
   *
   * @return boolean
   */
  var isInput = function (elem) {
    return elem.is("input");
  };

  /**
   * Valida si un elemento es tipo Select
   *
   * @param  obj elem [Elemento a validar]
   *
   * @return boolean
   */
  var isSelect = function (elem) {
    return elem.is("select");
  };

  /**
   * Valida si un elemento es tipo Select
   *
   * @param  obj elem [Elemento a validar]
   *
   * @return boolean
   */
  var isTextArea = function (elem) {
    return elem.is("textarea");
  };

  /**
   * Valida si un elemento esta visible
   *
   * @param  obj elem [Elemento a validar]
   *
   * @return boolean
   */
  var isVisible = function (elem) {
    return elem.is(":visible")
  };

  /*
  --------------------------------------------------------------------------
  |                   FUNCIONES PARA USO GLOBAL
  --------------------------------------------------------------------------
  */

  /**
   * Devuelve un String con los errores de los elementos
   *
   * @param  String separator [Especifica el separador entre cada error]
   * @return String
   */
  var getErrors = function (separator) {
    var e = errors;
    return e.join(typeof separator !== 'undefined' ? separator : '<br>');
  };

  /**
   * Devuelve True si existe algun error
   *
   * @return boolean
   */
  var existErrors = function () {
    return errors.length > 0;
  }

  /**
   * Limpia los arrays que contiene los Objetos de los elementos y el array que
   * almacena los mensajes
   *
   * @return void
   */
  var clearErrors = function () {
    arrayElemReq = [];
    errors = [];
  };

  out.init = initialize;
  out.errors = getErrors;
  out.existErrors = existErrors;

  return out;
})(jQuery);