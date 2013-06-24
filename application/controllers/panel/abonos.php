<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class abonos extends MY_Controller {

  /**
   * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
   * @var unknown_type
   */
  private $excepcion_privilegio = array('abonos/ajax_get_cuentas_banco/',
                                        'abonos/ajax_guarda_abono/',
                                        'abonos/ajax_guarda_abono_masivo/',
                                        'abonos/reales_xls/',
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
    $cantidadd = 0;
    $abonor_subabonos = array();

    $this->load->model('abonos_model');
    $response = $this->abonos_model->addAbono(null, null, false, true, null);

    //Se registran los abonos reales
    if(isset($response['info'])){
      $abonor_subabonos[] = array('id_abonoh' => 0, 'id_abono' => $response['info']['abonoInfo']['insert_id']);
      $cantidadd += $response['info']['abonoInfo']['monto'];
    }
    $abono_real = $this->abonos_model->addAbonoReal(array(
                  'fecha'        => str_replace('T', ' ', $_POST['fecha']),
                  'id_productor' => $this->input->post('id_productor'),
                  'concepto'     => $this->input->post('concepto'),
                  'cantidad'     => $cantidadd ),
                $abonor_subabonos
      );

    echo json_encode($response);
  }

  public function ajax_guarda_abono_masivo()
  {
    $this->load->model('abonos_model');
    // $this->load->model('banco_cuentas_model');

    // $cuenta_info = $this->banco_cuentas_model
    //                     ->getCuentas(0, $_POST['id_cuenta']);

    // if (floatval($_POST['monto']) > $cuenta_info['cuentas'][0]->saldo)
    //   $response = array('passes'=>false,
    //           'msg'=>'El monto especificado es mayor al saldo de la cuenta',
    //           'ico'=>'error');

    if (!isset($response))
    {
      $cantidadd = 0;
      $abonor_subabonos = array();

      $ids = explode(',', $_POST['id_caja']);
      foreach ($ids as $key => $id)
      {
        // $masivo = $banco = (($key == (count($ids) - 1)) ? true : false);
        $banco = false;
        $masivo = true;
        $response = $this->abonos_model->addAbono($id, null, true, $banco, null, $masivo);

        if(isset($response['info'])){
          $abonor_subabonos[] = array('id_abonoh' => 0, 'id_abono' => $response['info']['abonoInfo']['insert_id']);
          $cantidadd += $response['info']['abonoInfo']['monto'];
        }
      }

      //Se registran los abonos reales
      $abono_real = $this->abonos_model->addAbonoReal(array(
                    'fecha'        => str_replace('T', ' ', $_POST['fecha']),
                    'id_productor' => $this->input->post('id_productor'),
                    'concepto'     => $this->input->post('concepto'),
                    'cantidad'     => $cantidadd ),
                  $abonor_subabonos
        );

    }
    echo json_encode($response);
  }

  public function eliminar()
  {
    $this->load->model('abonos_model');
    $this->abonos_model->eliminar();

    redirect(base_url('panel/cajas/detalle/?'.String::getVarsLink(array('ida', 'msg')).'&msg=7'));
  }


  /**
   * ABONOS REALES COMO LOS REGISTRARON ELLOS, Y LOS ABONES ECHOS A CADA CAJA
   * **************************************
   * Listado de abonos echos
   * @return [type] [description]
   */
  public function reales(){
    $this->carabiner->js(array(
      array('general/msgbox.js'),
      array('panel/cajas/inventario.js'),
    ));

    $this->load->model('abonos_model');
    $this->load->library('pagination');

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Listado de abonos'
    );

    $params['abonos'] = $this->abonos_model->getListaAbonos();

    if(isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/cajas/listado_abonos', $params);
    $this->load->view('panel/footer');
  }

  public function reales_xls(){
    $this->load->model('abonos_model');
    
    $this->abonos_model->xlsListaAbonos();
  }

  public function reales_eliminar(){
    if(isset($_GET['id']{0})){
      $this->load->model('abonos_model');
      $this->abonos_model->eliminarReales($_GET['id']);

      redirect(base_url('panel/abonos/reales?msg=3&'.String::getVarsLink(array('id', 'msg')) ));
    }else
      redirect(base_url('panel/abonos/reales?msg=1&'.String::getVarsLink(array('id', 'msg')) ));
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
        $txt = 'El abono se elimino correctamente.';
        $icono = 'success';
      break;
    }

    return array(
      'title' => $title,
      'msg' => $txt,
      'ico' => $icono);
  }
}