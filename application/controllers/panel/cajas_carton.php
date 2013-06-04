<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cajas_carton extends MY_Controller {

  /**
   * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
   * @var unknown_type
   */
  private $excepcion_privilegio = array('cajas_carton/ajax_get_empacadores/',
                                        // 'cajas/ajax_get_duenos_huertas/',
                                        'cajas_carton/marca/',
                                        'cajas_carton/marca_pdf/',
                                        // 'cajas/productor_xls/',
                                        // 'cajas/cuentas_pagar_productor/',
                                        // 'cajas/cpp_pdf/',
                                        // 'cajas/cpp_xls/',
                                        // 'cajas/detalle/',
                                        // 'cajas/detalle_pdf/',
                                        );

  public function _remap($method){
    $this->load->model("usuarios_model");
    if($this->usuarios_model->checkSession()){
      $this->usuarios_model->excepcion_privilegio = $this->excepcion_privilegio;
      $this->info_empleado = $this->usuarios_model->get_usuario_info($this->session->userdata('id_usuario'), true);

      if($this->usuarios_model->tienePrivilegioDe('', get_class($this).'/'.$method.'/')){
        $this->{$method}();
      }else
        redirect(base_url('panel/home?msg=1'));
    }else
      redirect(base_url('panel/home'));
  }

  /**
   * Muestra el listado de movimientos de cajas (Entradas Salidas)
   *
   * @return void
   */
  public function index()
  {
    $this->carabiner->js(array(
      array('general/msgbox.js'),
      array('panel/cajas/inventario.js'),
    ));

    $this->load->model('cajas_carton_model');
    $this->load->model('empacadores_model');
    $this->load->library('pagination');

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Cajas Cartón (Entradas  y Salidas)'
    );

    $params['inventario'] = $this->cajas_carton_model->get_inventario('40');

    $params['empacadores'] = $this->empacadores_model->getEmpacadoresAll();

    if(isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/cajas_carton/listado', $params);
    $this->load->view('panel/footer');
  }

  /**
   * Muestra el formulario para agregar un movimiento de cajas tipo salida o
   * entrada
   *
   * @return void
   */
  public function agregar()
  {
    $this->carabiner->js(array(
      array('libs/jquery.numeric.js'),
      array('panel/cajas_carton/frm_addmod.js'),
    ));

    $params['info_empleado'] = $this->info_empleado['info'];
    $params['seo'] = array(
      'titulo' => 'Agregar Movimiento de Cajas de Cartón'
    );

    $this->load->model('cajas_carton_model');
    $this->load->model('marcas_model');

    $this->validator();
    if ($this->form_validation->run() === false)
    {
      $params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
    }
    else
    {
      $respons = $this->cajas_carton_model->addCaja();

      if ($respons[0])
        redirect(base_url('panel/cajas_carton/agregar/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
      else
       $params['frm_errors'] = $this->showMsgs(2, $respons[1]);
    }

    $params['marcas'] = $this->marcas_model->getMarcasAll();

    if (isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/cajas_carton/agregar', $params);
    $this->load->view('panel/footer', $params);
  }

  /**
   * Muestra el historial de cajas (entradas y salidas) de una marca
   * @return void
   */
  public function marca()
  {
    if (isset($_GET['id']{0}))
    {
      $this->carabiner->js(array(
        array('general/msgbox.js'),
        array('panel/cajas/inventario.js'),
      ));

      $this->load->model('cajas_carton_model');
      $this->load->model('marcas_model');
      $this->load->model('empacadores_model');
      $this->load->library('pagination');

      $params['info_empleado'] = $this->info_empleado['info']; //info empleado
      $params['seo'] = array(
        'titulo' => 'Entradas y Salidas de Productor'
      );

      $params['inventario'] = $this->cajas_carton_model->get_marca_inventario();

      $params['empacadores'] = $this->empacadores_model->getEmpacadoresAll();

      $params['marca'] = $this->marcas_model->getInfoMarca($_GET['id']);

      if(isset($_GET['msg']{0}))
        $params['frm_errors'] = $this->showMsgs($_GET['msg']);

      $this->load->view('panel/header', $params);
      $this->load->view('panel/general/menu', $params);
      $this->load->view('panel/cajas_carton/marca', $params);
      $this->load->view('panel/footer');
    }
    else redirect(base_url('panel/cajas_carton/?'.String::getVarsLink(array('msg', 'ffecha1', 'ffecha2')).'&msg=1'));
  }

    /**
   * Visualiza/Descarga un pdf con el listado de las entradas y salidas de un productor
   * en especifico en un rango de fechas.
   */
  public function marca_pdf()
  {
    if (isset($_GET['id']{0}))
    {
      $this->load->model('cajas_carton_model');
      $this->cajas_carton_model->marca_inventario_pdf();
    }
    else
      redirect(base_url('panel/cajas_carton/?'.String::getVarsLink(array('ffecha1', 'ffecha2', 'msg')).'&msg=1'));
  }

  /*******************  VALIDADORES DE FORMULARIOS  **************************/

  /**
   * Validador del formulario para agregar o modificar un moviemiento de cajas
   * @return void
   */
  public function validator()
  {
    $this->load->library('form_validation');

    $rules = array(
                array('field' => 'dmovimiento',
                      'label' => 'Movimiento',
                      'rules' => 'required'),
                array('field' => 'dmarca',
                      'label' => 'Marca',
                      'rules' => 'required'),
                array('field' => 'dfecha',
                      'label' => 'Fecha',
                      'rules' => 'required|max_length[10]'),
                array('field' => 'dconcepto',
                      'label' => 'Concepto',
                      'rules' => 'max_length[250]'),
                array('field' => 'dcantidad',
                      'label' => 'Cantidad',
                      'rules' => 'required|integer'),
                array('field' => 'ddesecho',
                      'label' => 'Desecho',
                      'rules' => ''),
    );

    if (count($_POST) > 0)
    {
      if ($_POST['dmovimiento'] === 's' && ! isset($_POST['ddesecho']))
      {
        $rules[] = array('field' => 'dempacador',
                         'label' => 'Empacador',
                         'rules' => '');

        $rules[] = array('field' => 'did_empacador',
                         'label' => 'Empacador',
                         'rules' => 'required');
      }
    }

    $this->form_validation->set_rules($rules);
  }

  /************************** AJAX *******************************/

  /**
   * Obtiene los empecadores mediante ajax
   * @return void
   */
  public function ajax_get_empacadores()
  {
    $this->load->model('empacadores_model');
    $respons = $this->empacadores_model->get_empacadores_ajax();

    echo json_encode($respons);
  }

  /************************** MENSAJES *******************************/

  /**
   * Muestra mensajes cuando se realiza alguna accion
   * @param unknown_type $tipo
   * @param unknown_type $msg
   * @param unknown_type $title
   */
  private function showMsgs($tipo, $msg='', $title='Clientes!'){
    switch($tipo){
      case 1:
        $txt = 'El campo ID es requerido.';
        $icono = 'error';
      break;
      case 2: //Cuendo se valida con form_validation
        $txt = $msg;
        $icono = 'error';
      break;
      case 3:
        $txt = 'El movimiento se agrego correctamente.';
        $icono = 'success';
      break;
      case 4:
        $txt = 'La entrada se agrego correctamente';
        $icono = 'success';
        break;
      case 5:
        $txt = 'La entrada se modifico correctamente.';
        $icono = 'success';
        break;
      case 6:
        $txt = 'El entrada se elimino correctamente.';
        $icono = 'success';
      break;
      case 7:
        $txt = 'El abono se elimino correctamente';
        $icono = 'success';
      break;
    }

    return array(
      'title' => $title,
      'msg' => $txt,
      'ico' => $icono);
  }

}