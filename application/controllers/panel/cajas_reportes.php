<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class cajas_reportes extends MY_Controller {
  /**
   * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
   * @var unknown_type
   */
  private $excepcion_privilegio = array('cajas_reportes/rcr_pdf/',
                                        'cajas_reportes/rcr_xls/',
                                        'cajas_reportes/rll_pdf/');

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
   * Muestra la vista para el Reporte "RELACION DE CAJAS RECIBIDAS"
   *
   * @return void
   */
  public function rcr()
  {
    $this->carabiner->js(array(
      array('general/msgbox.js'),
      array('panel/cajas/inventario.js'),
    ));

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Reporte Relacion de Cajas Recibidas'
    );

    if(isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/cajas/reportes/rcr', $params);
    $this->load->view('panel/footer');
  }

  /**
   * Procesa los datos para mostrar el reporte rcr en pdf
   * @return void
   */
  public function rcr_pdf()
  {
    $this->load->model('cajas_model');
    $this->cajas_model->rcr_pdf();
  }

  /**
   * Procesa los datos para mostrar el reporte rcr en pdf
   * @return void
   */
  public function rcr_xls()
  {
    $this->load->model('cajas_model');
    $this->cajas_model->rcr_xls();
  }

  /**
   * Muestra la vista para el Reporte "RELACION DE LAVADO POR LOTES"
   *
   * @return void
   */
  public function rll()
  {
    $this->carabiner->js(array(
      array('general/msgbox.js'),
      array('panel/cajas/inventario.js'),
    ));

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Reporte Relación de Lavado por Lotes'
    );

    if(isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/cajas/reportes/rll', $params);
    $this->load->view('panel/footer');
  }

  /**
   * Procesa los datos para mostrar el reporte rll en pdf
   * @return void
   */
  public function rll_pdf()
  {
    $this->load->model('cajas_model');
    $this->cajas_model->rll_pdf();
  }

}