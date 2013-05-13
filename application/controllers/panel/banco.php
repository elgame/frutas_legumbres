<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class banco extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('banco/ajax_get_cuentas/', 'banco/print_cheque/', 'banco/estado_cuenta_pdf/');

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

	
	public function index(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
			array('panel/frutas_legumbres.css', 'screen'),
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('panel/banco/estados_cuenta.js'),
		));
		$this->load->model('banco_cuentas_model');
		$this->load->library('pagination');

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Estados de ceuntas'
		);

		$_GET['ffecha1'] = isset($_GET['ffecha1'])? $_GET['ffecha1']: date("Y-m").'-01';
		$_GET['ffecha2'] = isset($_GET['ffecha2'])? $_GET['ffecha2']: date("Y-m-d");
		$fecha = ($_GET['ffecha1']>$_GET['ffecha2'])? $_GET['ffecha1']: $_GET['ffecha2'];

		$params['bancos'] = $this->banco_cuentas_model->getDataSaldos($fecha);

		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);

		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/banco/listado', $params);
		$this->load->view('panel/footer');
	}

	public function estado_cuenta(){
		if(isset($_GET['id']{0})){
			$this->carabiner->css(array(
				array('libs/jquery.uniform.css', 'screen'),
				array('panel/frutas_legumbres.css', 'screen'),
			));
			$this->carabiner->js(array(
				array('libs/jquery.uniform.min.js'),
				array('general/msgbox.js'),
				array('panel/banco/estados_cuenta.js'),
			));
			$this->load->model('banco_cuentas_model');
			$this->load->library('pagination');

			$params['info_empleado'] = $this->info_empleado['info']; //info empleado
			$params['seo'] = array(
				'titulo' => 'Estados de ceuntas'
			);

			$_GET['ffecha1'] = isset($_GET['ffecha1'])? $_GET['ffecha1']: date("Y-m").'-01';
			$_GET['ffecha2'] = isset($_GET['ffecha2'])? $_GET['ffecha2']: date("Y-m-d");

			$params['movimientos'] = $this->banco_cuentas_model->getDataEstadoCuenta($_GET['id'], $_GET['ffecha1'], $_GET['ffecha2']);

			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);

			$this->load->view('panel/header', $params);
			$this->load->view('panel/general/menu', $params);
			$this->load->view('panel/banco/estado_cuenta', $params);
			$this->load->view('panel/footer');
		}else
			redirect(base_url('panel/banco/?'.String::getVarsLink(array('id', 'msg')) ));
	}

	public function estado_cuenta_pdf(){
		if(isset($_GET['id']{0})){
      $this->load->model('banco_cuentas_model');

      $_GET['ffecha1'] = isset($_GET['ffecha1'])? $_GET['ffecha1']: date("Y-m").'-01';
			$_GET['ffecha2'] = isset($_GET['ffecha2'])? $_GET['ffecha2']: date("Y-m-d");
			
      $this->banco_cuentas_model->printEstadoCuenta($_GET['id'], $_GET['ffecha1'], $_GET['ffecha2']);
    }else
			redirect(base_url('panel/banco/?'.String::getVarsLink(array('id', 'msg')) ));
	}

	

	/**
	 * agrega una operacion a una cuenta de banco (depositos o retiros)
	 * @return [type] [description]
	 */
	public function agregar_operacion(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
			array('panel/frutas_legumbres.css', 'screen'),
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('libs/jquery.numeric.js'),
			array('general/util.js'),
			array('panel/banco/agregar_operacion.js'),
		));

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Agregar Operacion'
		);

		$this->load->model('banco_cuentas_model');

		$this->configAddOperacion();

		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$respons = $this->banco_cuentas_model->addOperacion();

			if($respons[0]){
				if($_GET['redirec'] == '1') //redirecciona a facturacion de productores
					redirect(base_url('panel/productoresfac/?'.String::getVarsLink(array('msg', 'id', 'tipo', 'redirec')).'&msg=10&id_mov='.$respons[3].'&met_pago='.$respons[4]));
				else
					redirect(base_url('panel/banco/agregar_operacion/?'.String::getVarsLink(array('msg', 'id_mov')).'&msg='.$respons[2].'&id_mov='.$respons[3].'&met_pago='.$respons[4]));
			}else
        $params['frm_errors'] = $this->showMsgs(2, $respons[1]);
		}

		//informacion de la factura
		$this->load->model('productoresfac_model');
		$params['fac'] = $this->productoresfac_model->getInfoFactura($this->input->get('id'));

		$params['tipo_operacion'] = isset($_GET['tipo']{0})? $_GET['tipo']: 'd';

		$params['dbanco_load'] = ( isset($_GET['banco']{0})? $_GET['banco']: '');
		$params['dcuenta_load'] = ( isset($_GET['cuenta']{0})? $_GET['cuenta']: '');

		$this->load->model('banco_model');
		$params['bancos'] = $this->banco_model->getBancos();

		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);

		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/banco/agregar_operacion', $params);
		$this->load->view('panel/footer');
	}

	public function eliminar_operacion(){
		if(isset($_GET['id_mov']{0})){
      $this->load->model('banco_cuentas_model');
      $this->banco_cuentas_model->eliminarOperacion($_GET['id_mov']);

      redirect(base_url('panel/banco/estado_cuenta?msg=8&'.String::getVarsLink(array('id_mov', 'msg')) ));
    }else
      redirect(base_url('panel/banco/estado_cuenta?msg=1&'.String::getVarsLink(array('id_mov', 'msg')) ));
	}

	/**
	 * imprime un cheque de la cuenta que se relizo el retiro
	 * @return [type] [description]
	 */
	public function print_cheque(){
    if(isset($_GET['id']{0})){
      $cheque = new Cheque();
			$cheque->generaCheque($_GET['id']);
    }else
      redirect(base_url('panel/banco'));
	}

	

	/**
	 * ************** ADMINISTRAR CUENTAS ***********************
	 * 
	 * listado de cuentas registradas
	 * @return [type] [description]
	 */
	public function cuentas(){
		$this->carabiner->js(array(
			array('general/msgbox.js'),
		));
		$this->load->model('banco_cuentas_model');
		$this->load->library('pagination');

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Administrar Cuentas de Banco'
		);

		$params['cuentas'] = $this->banco_cuentas_model->getCuentas();

		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);

		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/banco/cuentas/listado', $params);
		$this->load->view('panel/footer');
	}

	public function agregar_cuenta(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('libs/jquery.numeric.js'),
		));

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Agregar Cuenta Bancaria'
		);

		$this->load->model('banco_cuentas_model');

		$this->configAddModCuenta();

		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$respons = $this->banco_cuentas_model->addCuenta();

			if($respons[0])
				redirect(base_url('panel/banco/agregar_cuenta/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
			else
        $params['frm_errors'] = $this->showMsgs(2, $respons[1]);
		}

		$params['bancos'] = $this->banco_cuentas_model->getBancos();

		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);

		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/banco/cuentas/agregar', $params);
		$this->load->view('panel/footer');
	}

	public function modificar_cuenta(){
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
				'titulo' => 'Modificar Cuenta Bancaria'
		);

		$this->load->model('banco_cuentas_model');

		if(isset($_GET['id']{0})){
				$this->configAddModCuenta();

				if($this->form_validation->run() == FALSE){
					$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
				}else{
					$respons = $this->banco_cuentas_model->updateCuenta($_GET['id']);

					if($respons[0])
						redirect(base_url('panel/banco/cuentas/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
				}

				$info_cuenta   = $this->banco_cuentas_model->getCuentas(0, $_GET['id']);
				$params['info']   = $info_cuenta['cuentas'];
				$params['bancos'] = $this->banco_cuentas_model->getBancos();
			}else
				$params['frm_errors'] = $this->showMsgs(1);

			if(isset($_GET['msg']{0}))
					$params['frm_errors'] = $this->showMsgs($_GET['msg']);

				$this->load->view('panel/header', $params);
				$this->load->view('panel/general/menu', $params);
				$this->load->view('panel/banco/cuentas/modificar', $params);
				$this->load->view('panel/footer');
	}

	/**
	 * Elimina a una cuenta, cambia el status a "e":eliminado
	 */
	public function eliminar_cuenta(){
		if(isset($_GET['id']{0})){
			$this->load->model('banco_cuentas_model');
			$respons = $this->banco_cuentas_model->updateCuenta($_GET['id'], array('status' => 'e'), false);
			if($respons[0])
				redirect(base_url('panel/banco/cuentas/?msg=5'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}

	/**
	 * activa una cuenta, cambia el status a "e":eliminado
	 */
	public function activar_cuenta(){
		if(isset($_GET['id']{0})){
			$this->load->model('banco_cuentas_model');
			$respons = $this->banco_cuentas_model->updateCuenta($_GET['id'], array('status' => 'ac'), false);
			if($respons[0])
				redirect(base_url('panel/banco/cuentas/?msg=6'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}






	/**********************************************************
	 * ***************** CUENTAS ******************************
	 */
	public function ajax_get_cuentas(){
		if(isset($_GET['id']{0})){
			$this->load->model('banco_cuentas_model');
			$params = $this->banco_cuentas_model->getCuentas($_GET['id']);

			echo json_encode($params);
		}else
			echo json_encode($this->showMsgs(1));
	}




	/**
	 * Configura los metodos de agregar y modificar
	 */
	private function configAddOperacion(){
		$this->load->library('form_validation');

			$rules = array(
				array('field'	=> 'dfecha',
						'label'	=> 'Fecha',
						'rules'	=> 'required|max_length[22]'),
				array('field'	=> 'dbanco',
						'label'	=> 'Banco',
						'rules'	=> 'required|numeric'),
				array('field'	=> 'dcuenta',
						'label'	=> 'Cuenta',
						'rules'	=> 'required|numeric'),
				array('field'	=> 'dconcepto',
						'label'	=> 'Concepto',
						'rules'	=> 'required|max_length[254]'),
				array('field'	=> 'dmonto',
						'label'	=> 'Monto',
						'rules'	=> 'required|numeric|callback_val_total'),
				array('field'	=> 'dmetodo_pago',
						'label'	=> 'Metodo de pago',
						'rules'	=> 'required|max_length[20]'),
				array('field'	=> 'dtipo_operacion',
						'label'	=> 'Tipo operacion',
						'rules'	=> 'required|max_length[1]'),

				array('field'	=> 'dchk_anombre',
						'label'	=> 'A nombre de',
						'rules'	=> 'max_length[100]'),
				array('field'	=> 'dmoneda',
						'label'	=> 'Moneda',
						'rules'	=> 'max_length[6]'),
				array('field'	=> 'dabono_cuenta',
						'label'	=> 'Para abono en cuenta',
						'rules'	=> 'max_length[1]'),
				

				array('field'	=> 'dconcep_conce[]',
						'label'	=> 'Conceptos reales',
						'rules'	=> 'max_length[254]'),
				array('field'	=> 'dconcep_monto[]',
						'label'	=> 'Conceptos monto',
						'rules'	=> 'numeric'),
			);
		if ($this->input->post('dtipo_operacion') == 'r' && $this->input->post('dmetodo_pago') == 'cheque') {
			$rules[7]['rules'] = 'required|max_length[100]';
		}
		$this->form_validation->set_rules($rules);
	}

	public function val_total($str){
    if($str <= 0){
      $this->form_validation->set_message('val_total', 'El Total no puede ser 0, verifica los datos ingresados.');
      return false;
    }elseif($this->input->post('dtipo_operacion') == 'r'){
      $cuentas = $this->banco_cuentas_model->getCuentas(0, $this->input->post('dcuenta'));
      if($cuentas['cuentas'][0]->saldo < $this->input->post('dmonto')){
        $this->form_validation->set_message('val_total', 'El Saldo de la cuenta es insuficiente para realizar el retiro.');
        return false;
      }
    }
    return true;
  }


  private function configAddModCuenta(){
		$this->load->library('form_validation');

			$rules = array(
				array('field'	=> 'dbanco',
						'label'	=> 'Banco',
						'rules'	=> 'required|numeric'),
				array('field'	=> 'dnumero',
						'label'	=> 'Numero',
						'rules'	=> 'required|max_length[20]'),
				array('field'	=> 'dalias',
						'label'	=> 'Alias',
						'rules'	=> 'required|max_length[40]'),
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
				$txt = 'La operación se agrego correctamente.';
				$icono = 'success';
			break;

			case 4:
				$txt = 'La cuenta se agrego correctamente.';
				$icono = 'success';
				break;
			case 5:
				$txt = 'La cuenta se elimino correctamente.';
				$icono = 'success';
				break;
			case 6:
				$txt = 'La cuenta se activo correctamente.';
				$icono = 'success';
			break;
			case 7:
				$txt = 'La cuenta se modifico correctamente.';
				$icono = 'success';
			break;

			case 8:
				$txt = 'La operación se elimino correctamente.';
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