<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class banco extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('banco/ajax_get_cuentas/');

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
	public function index(){
		$this->carabiner->js(array(
			array('general/msgbox.js'),
		));
		$this->load->model('variedades_model');
		$this->load->library('pagination');

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Administrar Variedades'
		);

		$params['variedades'] = $this->variedades_model->getVariedades();

		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);

		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/variedades/listado', $params);
		$this->load->view('panel/footer');
	}

	


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
					redirect(base_url('panel/productoresfac/?'.String::getVarsLink(array('msg', 'id', 'tipo', 'redirec')).'&msg=10'));
				else
					redirect(base_url('panel/banco/agregar_operacion/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
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

	/**
	 * Modificar una variedades a la bd
	 */
	public function modificar(){
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
				'titulo' => 'Modificar Variedad'
		);

		if(isset($_GET['id']{0})){
				$this->configAddOperacion();
				$this->load->model('variedades_model');

				if($this->form_validation->run() == FALSE){
					$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
				}else{
					$respons = $this->variedades_model->updateVariedad($_GET['id']);

					if($respons[0])
						redirect(base_url('panel/variedades/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
				}

				$params['info'] = $this->variedades_model->getInfoVariedad($_GET['id']);
			}else
				$params['frm_errors'] = $this->showMsgs(1);

			if(isset($_GET['msg']{0}))
					$params['frm_errors'] = $this->showMsgs($_GET['msg']);

				$this->load->view('panel/header', $params);
				$this->load->view('panel/general/menu', $params);
				$this->load->view('panel/variedades/modificar', $params);
				$this->load->view('panel/footer');
	}

	/**
	 * Elimina a una variedad, cambia el status a "e":eliminado
	 */
	public function eliminar(){
		if(isset($_GET['id']{0})){
			$this->load->model('variedades_model');
			$respons = $this->variedades_model->updateVariedad($_GET['id'], array('status' => 'e'), false);
			if($respons[0])
				redirect(base_url('panel/variedades/?msg=5'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}

	/**
	 * activa un productor eliminado, cambia el status a "e":eliminado
	 */
	public function activar(){
		if(isset($_GET['id']{0})){
			$this->load->model('variedades_model');
			$respons = $this->variedades_model->updateVariedad($_GET['id'], array('status' => 'ac'), false);
			if($respons[0])
				redirect(base_url('panel/variedades/?msg=6'));
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
	 * Obtiene lostado de proveedores para el autocomplete, ajax
	 */
	public function ajax_get_proveedor(){
		$this->load->model('variedades_model');
		$params = $this->variedades_model->getProveedoresAjax();

		echo json_encode($params);
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
				$txt = 'La operación se modifico correctamente.';
				$icono = 'success';
				break;
			case 5:
				$txt = 'La operación se elimino correctamente.';
				$icono = 'success';
				break;
			case 6:
				$txt = 'La operación se activo correctamente.';
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