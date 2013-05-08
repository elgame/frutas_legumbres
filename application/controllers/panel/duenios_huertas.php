<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Duenios_huertas extends MY_Controller {
  /**
   * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
   * @var unknown_type
   */
  private $excepcion_privilegio = array('productores/ajax_get_proveedor/');

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
   * Muestra el listado de duenio de huertas
   */
  public function index()
  {
    $this->carabiner->js(array(
      array('general/msgbox.js'),
    ));

    $this->load->model('duenios_huertas_model');
    $this->load->library('pagination');

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Administrar Dueños de Huertas'
    );

    $params['duenios'] = $this->duenios_huertas_model->getDueniosHuertas();

    if(isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/duenos_huertas/listado', $params);
    $this->load->view('panel/footer');
  }


  /**
   * Muestra el formulario para agregar un dueño
   */
  public function agregar()
  {
    $this->carabiner->css(array(
      array('libs/jquery.uniform.css', 'screen'),
    ));
    $this->carabiner->js(array(
      array('libs/jquery.numeric.js'),
      array('panel/productores/frm_addmod.js')
    ));

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Agregar Dueño de Huerta'
    );

    $this->validator();

    if($this->form_validation->run() == FALSE)
    {
      $params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
    }
    else
    {
      $this->load->model('duenios_huertas_model');
      $respons = $this->duenios_huertas_model->addDuenioHuerta();

      if($respons[0])
        redirect(base_url('panel/duenios_huertas/agregar/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
      else
        $params['frm_errors'] = $this->showMsgs(2, $respons[1]);
        echo 1;
    }

    if(isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/duenos_huertas/agregar', $params);
    $this->load->view('panel/footer');
  }


  /**
   * Muestra el formulario para modificar un dueño de huerta
   */
  public function modificar()
  {
    $this->carabiner->css(array(
      array('libs/jquery.uniform.css', 'screen'),
    ));
    $this->carabiner->js(array(
      array('libs/jquery.numeric.js'),
      array('general/msgbox.js'),
      // array('panel/productores/frm_addmod.js')
    ));

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
        'titulo' => 'Modificar Dueño de Huerta'
    );

    if(isset($_GET['id']{0}))
    {

        $this->validator();
        $this->load->model('duenios_huertas_model');

        if($this->form_validation->run() == FALSE)
        {
          $params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
        }
        else
        {
          $respons = $this->duenios_huertas_model->updateDuenioHuerta($_GET['id']);

          if($respons[0])
            redirect(base_url('panel/duenios_huertas/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
        }

        $params['info'] = $this->duenios_huertas_model->getInfoDuenioHuerta($_GET['id']);

      }
      else
        $params['frm_errors'] = $this->showMsgs(1);

      if(isset($_GET['msg']{0}))
          $params['frm_errors'] = $this->showMsgs($_GET['msg']);

        $this->load->view('panel/header', $params);
        $this->load->view('panel/general/menu', $params);
        $this->load->view('panel/duenos_huertas/modificar', $params);
        $this->load->view('panel/footer');
  }

  /**
   * Elimina a un productor, cambia el status a "e":eliminado
   */
  public function eliminar(){
    if(isset($_GET['id']{0})){
      $this->load->model('duenios_huertas_model');
      $respons = $this->duenios_huertas_model->updateDuenioHuerta($_GET['id'], array('status' => 'e'), false);
      if($respons[0])
        redirect(base_url('panel/duenios_huertas/?msg=5'));
    }else
      $params['frm_errors'] = $this->showMsgs(1);
  }

  /**
   * activa un productor eliminado, cambia el status a "ac":activado
   */
  public function activar(){
    if(isset($_GET['id']{0})){
      $this->load->model('duenios_huertas_model');
      $respons = $this->duenios_huertas_model->updateDuenioHuerta($_GET['id'], array('status' => 'ac'), false);
      if($respons[0])
        redirect(base_url('panel/duenios_huertas/?msg=6'));
    }else
      $params['frm_errors'] = $this->showMsgs(1);
  }


  /**
   * Validador para el formulario agregar o modificar dueño de huerta
   *
   * @return boolean
   */
  public function validator()
  {
    $this->load->library('form_validation');

    $rules = array(
      array('field' => 'dnombre',
          'label' => 'Nombre',
          'rules' => 'required|max_length[120]'),
      array('field' => 'dcalle',
          'label' => 'Calle',
          'rules' => 'max_length[60]'),
      array('field' => 'dno_exterior',
          'label' => 'No exterior',
          'rules' => 'max_length[7]'),
      array('field' => 'dno_interior',
          'label' => 'No interior',
          'rules' => 'max_length[7]'),
      array('field' => 'dcolonia',
          'label' => 'Colonia',
          'rules' => 'max_length[60]'),
      array('field' => 'dmunicipio',
          'label' => 'Municipio',
          'rules' => 'max_length[45]'),
      array('field' => 'destado',
          'label' => 'Estado',
          'rules' => 'max_length[45]'),
      array('field' => 'dcp',
          'label' => 'CP',
          'rules' => 'max_length[10]'),
      array('field' => 'dtelefono',
          'label' => 'Teléfono',
          'rules' => 'max_length[15]'),
      array('field' => 'dcelular',
          'label' => 'Celular',
          'rules' => 'max_length[20]'),
    );

    $this->form_validation->set_rules($rules);
  }


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
        $txt = 'El dueño de huerta se agrego correctamente.';
        $icono = 'success';
      break;
      case 4:
        $txt = 'El dueño de huerta se modifico correctamente.';
        $icono = 'success';
        break;
      case 5:
        $txt = 'El dueño de huerta se elimino correctamente.';
        $icono = 'success';
        break;
      case 6:
        $txt = 'El dueño de huerta se activo correctamente.';
        $icono = 'success';
      break;
    }

    return array(
      'title' => $title,
      'msg' => $txt,
      'ico' => $icono);
  }

}

/* End of file duenios.php */
/* Location: ./application/controllers/panel/duenios.php */