<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cajas extends MY_Controller {

  /**
   * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
   * @var unknown_type
   */
  private $excepcion_privilegio = array('cajas/ajax_get_productores/', 'cajas/ajax_get_duenos_huertas/');

  public function _remap($method){
    $this->load->model("usuarios_model");
    if($this->usuarios_model->checkSession()){
      $this->usuarios_model->excepcion_privilegio = $this->excepcion_privilegio;
      $this->info_empleado                         = $this->usuarios_model->get_usuario_info($this->session->userdata('id_usuario'), true);

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
    ));

    $this->load->model('cajas_model');
    $this->load->library('pagination');

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Administrar de Cajas (Salidas y Entradas)'
    );

    $params['cajas'] = $this->cajas_model->get_cajas();

    if(isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/cajas/listado', $params);
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
      array('panel/cajas/frm_addmod.js'),
    ));

    $params['info_empleado'] = $this->info_empleado['info'];
    $params['seo'] = array(
      'titulo' => 'Agregar Movimiento de Cajas'
    );

    $this->load->model('cajas_model');

    $this->validator();
    if ($this->form_validation->run() === false)
    {
      $params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
    }
    else
    {
      $respons = $this->cajas_model->insert_cajas();

      if ($respons[0])
        redirect(base_url('panel/cajas/agregar/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
      else
       $params['frm_errors'] = $this->showMsgs(2, $respons[1]);
    }

    $params['variedades'] = $this->cajas_model->get_variedades();

    if (isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/cajas/agregar', $params);
    $this->load->view('panel/footer', $params);
  }

  /****************************** ENTRADAS ***********************************/

  /**
   * Muestra el listado de la entregas o entradas de cajas
   * @param string $value [description]
   */
  public function entradas()
  {
    $this->carabiner->js(array(
      array('general/msgbox.js'),
    ));

    $this->load->model('cajas_model');
    $this->load->library('pagination');

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Administrar Cajas de Entrada'
    );

    $params['cajas'] = $this->cajas_model->get_cajas_entrada();

    if(isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/cajas/listado_entradas', $params);
    $this->load->view('panel/footer');
  }

  /**
   * Muestra el formulario para registrar entregas de cajas
   * @param string $value [description]
   */
  public function agregar_entrada()
  {
    $this->carabiner->js(array(
      array('libs/jquery.numeric.js'),
      array('panel/cajas/frm_entregas.js'),
    ));

    $params['info_empleado'] = $this->info_empleado['info'];
    $params['seo'] = array(
      'titulo' => 'Agregar Entrada'
    );

    $this->load->model('cajas_model');

    $this->validatorEntradas();
    if ($this->form_validation->run() === false)
    {
      $params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
    }
    else
    {
      $respons = $this->cajas_model->insert_entrada();

      if ($respons[0])
        redirect(base_url('panel/cajas/agregar_entrada/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
      else
       $params['frm_errors'] = $this->showMsgs(2, $respons[1]);
    }

    $params['variedades'] = $this->cajas_model->get_variedades();

    if (isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/cajas/agregar_entradas', $params);
    $this->load->view('panel/footer', $params);
  }

  /**
   * Muestra el formulario para modificar una entrada de cajas
   * @return void
   */
  public function modificar_entrada()
  {
    $this->carabiner->js(array(
      array('libs/jquery.numeric.js'),
      array('panel/cajas/frm_entregas.js'),
    ));

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
        'titulo' => 'Modificar Entrada'
    );

    if(isset($_GET['id']{0}))
    {
      $this->load->model('cajas_model');

      $this->validatorEntradas(true);
      if($this->form_validation->run() == FALSE)
      {
        $params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
      }
      else
      {
        $respons = $this->cajas_model->update_entrada($_GET['id']);

        if($respons[0])
          redirect(base_url('panel/cajas/modificar_entrada/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
      }

      $params['variedades'] = $this->cajas_model->get_variedades();

      $params['info'] = $this->cajas_model->get_info_entrada($_GET['id']);
    }
    else
      $params['frm_errors'] = $this->showMsgs(1);

    if(isset($_GET['msg']{0}))
        $params['frm_errors'] = $this->showMsgs($_GET['msg']);

      $this->load->view('panel/header', $params);
      $this->load->view('panel/general/menu', $params);
      $this->load->view('panel/cajas/modificar_entradas', $params);
      $this->load->view('panel/footer');
  }

  public function eliminar_entrada()
  {
    if (isset($_GET['id']{0}))
    {
      $this->load->model('cajas_model');
      $respons = $this->cajas_model->delete_entrada($_GET['id']);

      redirect(base_url('panel/cajas/entradas/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
    }
    else
      redirect(base_url('panel/cajas/entradas/?'.String::getVarsLink(array('msg')).'&msg=1'));
  }

  /************************** AJAX *******************************/

  /**
   * Obtiene los productos mediante ajax
   * @return void
   */
  public function ajax_get_productores()
  {
    $this->load->model('cajas_model');
    $respons = $this->cajas_model->get_productores_ajax();

    echo json_encode($respons);
  }

  /**
   * Obtiene los productos mediante ajax
   * @return void
   */
  public function ajax_get_duenos_huertas()
  {
    $this->load->model('duenios_huertas_model');
    $respons = $this->duenios_huertas_model->get_duenos_huertas_ajax();

    echo json_encode($respons);
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
                array('field' => 'dproductor',
                      'label' => 'Productor',
                      'rules' => 'required'),
                array('field' => 'did_productor',
                      'label' => 'Productor',
                      'rules' => 'required'),
                array('field' => 'dvariedad',
                      'label' => 'Variedad',
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
                array('field' => 'dchofer',
                      'label' => 'Chofer',
                      'rules' => 'max_length[30]'),
                array('field' => 'dmovimiento',
                      'label' => 'Movimiento',
                      'rules' => 'required'),
    );

    $this->form_validation->set_rules($rules);
  }

  /**
   * Validador del formulario para agregar o modificar un moviemiento de cajas
   * @return void
   */
  public function validatorEntradas($extra = false)
  {
    $this->load->library('form_validation');

    $rules = array(
                array('field' => 'dfecha',
                      'label' => 'Fecha',
                      'rules' => 'required|max_length[10]'),

                array('field' => 'ddueno',
                      'label' => 'Dueño',
                      'rules' => 'required|max_length[120]'),
                array('field' => 'did_dueno',
                      'label' => 'Dueño',
                      'rules' => 'required'),
                array('field' => 'dproductor',
                      'label' => 'Productor',
                      'rules' => 'required|max_length[120]'),
                array('field' => 'did_productor',
                      'label' => 'Productor',
                      'rules' => 'required'),
                array('field' => 'dvariedad',
                      'label' => 'Variedad',
                      'rules' => 'required'),


                array('field' => 'dcertificado_tarjeta',
                      'label' => 'Cerficado Tarjeta',
                      'rules' => 'max_length[40]'),

                array('field' => 'dcodigo_huerta',
                      'label' => 'Codigo de Huerta',
                      'rules' => 'max_length[40]'),
                array('field' => 'dno_lote',
                      'label' => 'Numero de Lote',
                      'rules' => 'integer'),
                array('field' => 'dunidad_transporte',
                      'label' => 'Unidad de Transporte',
                      'rules' => 'max_length[60]'),
                array('field' => 'ddueno_carga',
                      'label' => 'Dueño de la Carga',
                      'rules' => 'max_length[60]'),
                array('field' => 'dobservaciones',
                      'label' => 'Observaciones',
                      'rules' => 'max_length[250]'),

                array('field' => 'dcajas',
                      'label' => 'Cajas',
                      'rules' => 'required|integer'),
                array('field' => 'dcajas_rezaga',
                      'label' => 'Cajas de Rezaga',
                      'rules' => 'integer'),
                array('field' => 'dno_ticket',
                      'label' => 'Numero de Ticket',
                      'rules' => 'max_length[10]'),
                array('field' => 'dkilos',
                      'label' => 'Kilos',
                      'rules' => 'integer'),
                array('field' => 'dprecio',
                      'label' => 'Precio',
                      'rules' => 'numeric'),

                array('field' => 'des_organico',
                      'label' => 'Es organico?',
                      'rules' => ''),
    );


    if ($extra)
    {
      if (isset($_POST['dcantidad_trata']) && isset($_POST['did_tratamiento']))
      {
        $r = ($_POST['dcantidad_trata'] !== '') ? 'required' : '';
        $rules[] = array('field' => 'did_tratamiento',
                          'label' => 'Tipo de tratamiento',
                          'rules' => $r);

        $r = ($_POST['did_tratamiento'] !== '') ? 'required|' : '';
        $rules[] = array('field' => 'dcantidad_trata',
                          'label' => 'Cantidad',
                          'rules' => $r.'numeric');
      }
    }

    $this->form_validation->set_rules($rules);
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
    }

    return array(
      'title' => $title,
      'msg' => $txt,
      'ico' => $icono);
  }

}

/* End of file cajas.php */
/* Location: ./application/controllers/panel/cajas.php */