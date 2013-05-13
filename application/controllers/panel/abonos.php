<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class abonos extends MY_Controller {

  /**
   * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
   * @var unknown_type
   */
  private $excepcion_privilegio = array('abonos/ajax_get_cuentas_banco/',
                                        'abonos/ajax_guarda_abono/',
                                        'abonos/ajax_guarda_abono_masivo/'
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

  public function ajax_get_cuentas_banco()
  {
    $this->load->model('banco_cuentas_model');
    $respons = $this->banco_cuentas_model
                  ->getCuentas($this->input->post('id'));

    echo json_encode($respons);
  }

  public function ajax_guarda_abono()
  {
    $this->load->model('abonos_model');
    $response = $this->abonos_model->addAbono(null, null, false, true, null);

    echo json_encode($response);
  }

  public function ajax_guarda_abono_masivo()
  {
    $this->load->model('abonos_model');
    $this->load->model('banco_cuentas_model');

    $cuenta_info = $this->banco_cuentas_model
                        ->getCuentas(0, $_POST['id_cuenta']);

    if (floatval($_POST['monto']) > $cuenta_info['cuentas'][0]->saldo)
      $response = array('passes'=>false,
              'msg'=>'El monto especificado es mayor al saldo de la cuenta',
              'ico'=>'error');

    if (!isset($response))
    {
      $ids = explode(',', $_POST['id_caja']);
      foreach ($ids as $key => $id)
      {
        $masivo = $banco = ($key === (count($ids) - 1)) ? true : false;
        $response = $this->abonos_model->addAbono($id, null, true, $banco, null, $masivo);
      }
    }

    echo json_encode($response);
  }

}