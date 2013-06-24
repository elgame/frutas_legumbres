<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class empacadores extends MY_Controller {
  /**
   * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
   * @var unknown_type
   */
  private $excepcion_privilegio = array('empacadores/ajax_get_proveedor/');

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
   * Default. Mustra el listado de variedades para administrarlos
   */
  public function index()
  {
    $this->carabiner->js(array(
      array('general/msgbox.js'),
    ));

    $this->load->model('empacadores_model');
    $this->load->library('pagination');

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Administrar Maquiladores'
    );

    $params['empacadores'] = $this->empacadores_model->getEmpacadores();

    if(isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/empacadores/listado', $params);
    $this->load->view('panel/footer');
  }

  /**
   * Agrega una marca a la bd
   */
  public function agregar()
  {
    $this->carabiner->css(array(
      array('libs/jquery.uniform.css', 'screen'),
    ));
    $this->carabiner->js(array(
      array('libs/jquery.uniform.min.js'),
      array('libs/jquery.numeric.js'),
    ));

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Agregar Maquilador'
    );

    $this->configAddModEmpacador();

    if($this->form_validation->run() == FALSE){
      $params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
    }else{
      $this->load->model('empacadores_model');
      $respons = $this->empacadores_model->addEmpacador();

      if($respons[0])
        redirect(base_url('panel/empacadores/agregar/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
      else
        $params['frm_errors'] = $this->showMsgs(2, $respons[1]);
    }

    if(isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/empacadores/agregar', $params);
    $this->load->view('panel/footer');
  }

  /**
   * Modificar una marca a la bd
   */
  public function modificar()
  {
    $this->carabiner->css(array(
      array('libs/jquery.uniform.css', 'screen'),
    ));
    $this->carabiner->js(array(
      array('libs/jquery.uniform.min.js'),
      array('libs/jquery.numeric.js'),
      array('general/msgbox.js'),
    ));

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
        'titulo' => 'Modificar Maquilador'
    );

    if(isset($_GET['id']{0})){
        $this->configAddModEmpacador();
        $this->load->model('empacadores_model');

        if($this->form_validation->run() == FALSE){
          $params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
        }else{
          $respons = $this->empacadores_model->updateEmpacador($_GET['id']);

          if($respons[0])
            redirect(base_url('panel/empacadores/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
        }

        $params['info'] = $this->empacadores_model->getInfoEmpacador($_GET['id']);
      }else
        $params['frm_errors'] = $this->showMsgs(1);

      if(isset($_GET['msg']{0}))
          $params['frm_errors'] = $this->showMsgs($_GET['msg']);

        $this->load->view('panel/header', $params);
        $this->load->view('panel/general/menu', $params);
        $this->load->view('panel/empacadores/modificar', $params);
        $this->load->view('panel/footer');
  }

  /**
   * Elimina una marca, cambia el status a "e":eliminado
   */
  public function eliminar()
  {
    if(isset($_GET['id']{0})){
      $this->load->model('empacadores_model');
      $respons = $this->empacadores_model->updateEmpacador($_GET['id'], array('status' => 'e'), false);
      if($respons[0])
        redirect(base_url('panel/empacadores/?msg=5'));
    }else
      $params['frm_errors'] = $this->showMsgs(1);
  }

  /**
   * activa un productor eliminado, cambia el status a "e":eliminado
   */
  public function activar()
  {
    if(isset($_GET['id']{0})){
      $this->load->model('empacadores_model');
      $respons = $this->empacadores_model->updateEmpacador($_GET['id'], array('status' => 'ac'), false);
      if($respons[0])
        redirect(base_url('panel/empacadores/?msg=6'));
    }else
      $params['frm_errors'] = $this->showMsgs(1);
  }


  /**
   * Obtiene lostado de proveedores para el autocomplete, ajax
   */
  public function ajax_get_proveedor()
  {
    $this->load->model('variedades_model');
    $params = $this->variedades_model->getProveedoresAjax();

    echo json_encode($params);
  }

  /**
   * Configura los metodos de agregar y modificar
   */
  private function configAddModEmpacador(){
    $this->load->library('form_validation');

      $rules = array(
        array('field' => 'dnombre',
            'label' => 'Nombre',
            'rules' => 'required|max_length[40]'),
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
        $txt = 'El Maquilador se agrego correctamente.';
        $icono = 'success';
      break;
      case 4:
        $txt = 'El Maquilador se modifico correctamente.';
        $icono = 'success';
        break;
      case 5:
        $txt = 'El Maquilador se elimino correctamente.';
        $icono = 'success';
        break;
      case 6:
        $txt = 'El Maquilador se activo correctamente.';
        $icono = 'success';
      break;
      case 7:
        $txt = 'El contacto se elimino correctamente.';
        $icono = 'success';
      break;
    }

    return array(
      'title' => $title,
      'msg' => $txt,
      'ico' => $icono);
  }
}

?>