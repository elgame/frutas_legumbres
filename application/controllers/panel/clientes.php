<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class clientes extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('clientes/ajax_frmAddGrupo/', 'clientes/ajax_frmAddSucu/',
			'productos/ajax_productos_tbl/', 'productos/ajax_productos_addmod/','clientes/ajax_get_clientes/');

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
	 * Default. Mustra el listado de clientes para administrarlos
	 */
	public function index(){
		$this->carabiner->js(array(
			array('general/msgbox.js'),
		));
		$this->load->model('clientes_model');
		$this->load->library('pagination');

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Administrar Clientes'
		);

		$params['clientes'] = $this->clientes_model->getClientes();

		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);

		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/clientes/listado', $params);
		$this->load->view('panel/footer');
	}

	/**
	 * Agrega un cliente a la bd
	 */
	public function agregar(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('libs/jquery.numeric.js'),
			array('panel/clientes/frm_addmod.js')
		));

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Agregar Cliente'
		);

		$this->configAddModEmpl();

		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$this->load->model('clientes_model');
				$respons = $this->clientes_model->addCliente();

			if($respons[0])
				redirect(base_url('panel/clientes/agregar/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
		}

		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);

		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/clientes/agregar', $params);
		$this->load->view('panel/footer');
	}

	/**
	 * Modificar una sucursal a un cliente a la bd
	 */
	public function modificar(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('libs/jquery.numeric.js'),
			array('general/msgbox.js'),
			array('panel/clientes/frm_addmod.js')
		));

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
				'titulo' => 'Modificar Cliente'
		);

		if(isset($_GET['id']{0})){
				$this->configAddModEmpl();
				$this->load->model('clientes_model');

				if($this->form_validation->run() == FALSE){
					$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
				}else{
					$respons = $this->clientes_model->updateCliente($_GET['id']);

					if($respons[0])
						redirect(base_url('panel/clientes/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
				}

				$params['info'] = $this->clientes_model->getInfoCliente($_GET['id']);
			}else
				$params['frm_errors'] = $this->showMsgs(1);

			if(isset($_GET['msg']{0}))
					$params['frm_errors'] = $this->showMsgs($_GET['msg']);

				$this->load->view('panel/header', $params);
				$this->load->view('panel/general/menu', $params);
				$this->load->view('panel/clientes/modificar_sucursal', $params);
				$this->load->view('panel/footer');
	}

	/**
	 * Elimina a un cliente, cambia el status a "e":eliminado
	 */
	public function eliminar(){
		if(isset($_GET['id']{0})){
			$this->load->model('clientes_model');
			$respons = $this->clientes_model->updateCliente($_GET['id'], array('status' => '0'), false);
			if($respons[0])
				redirect(base_url('panel/clientes/?msg=5'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}

	/**
	 * activa un cliente eliminado, cambia el status a "e":eliminado
	 */
	public function activar(){
		if(isset($_GET['id']{0})){
			$this->load->model('clientes_model');
			$respons = $this->clientes_model->updateCliente($_GET['id'], array('status' => '1'), false);
			if($respons[0])
				redirect(base_url('panel/clientes/?msg=6'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}


	/**
	 * Obtiene lostado de clientes para el autocomplete, ajax
	 */
	public function ajax_get_clientes(){
		$this->load->model('clientes_model');
		$params = $this->clientes_model->getClientesAjax();

		echo json_encode($params);
	}

	/**
	 * Configura los metodos de agregar y modificar
	 */
	private function configAddModEmpl(){
		$this->load->library('form_validation');

			$rules = array(
				array('field'	=> 'dnombre_fiscal',
						'label'	=> 'Nombre Fiscal',
						'rules'	=> 'required|max_length[120]'),
				array('field'	=> 'drfc',
						'label'	=> 'RFC',
						'rules'	=> 'required|max_length[13]'),
				array('field'	=> 'dcalle',
						'label'	=> 'Calle',
						'rules'	=> 'required|max_length[80]'),
				array('field'	=> 'dno_exterior',
						'label'	=> 'No exterior',
						'rules'	=> 'required|max_length[8]'),
				array('field'	=> 'dno_interior',
						'label'	=> 'No interior',
						'rules'	=> 'max_length[8]'),
				array('field'	=> 'dcolonia',
						'label'	=> 'Colonia',
						'rules'	=> 'required|max_length[80]'),
				array('field'	=> 'dmunicipio',
						'label'	=> 'Municipio',
						'rules'	=> 'required|max_length[60]'),
				array('field'	=> 'destado',
						'label'	=> 'Estado',
						'rules'	=> 'required|max_length[60]'),
				array('field'	=> 'dcp',
						'label'	=> 'CP',
						'rules'	=> 'max_length[10]'),
				array('field'	=> 'dtelefono',
						'label'	=> 'Teléfono',
						'rules'	=> 'max_length[20]'),
				array('field'	=> 'dcelular',
						'label'	=> 'Celular',
						'rules'	=> 'max_length[20]'),
				array('field'	=> 'demail',
						'label'	=> 'Email',
						'rules'	=> 'valid_email|max_length[70]'),
				array('field'	=> 'ddescuento',
						'label'	=> 'Descuento',
						'rules'	=> 'numeric|max_length[3]'),
				array('field'	=> 'denviar_factura',
						'label'	=> 'Enviar factura',
						'rules'	=> 'numeric|max_length[1]'),

				array('field'	=> 'denombre',
						'label'	=> 'Nombre',
						'rules'	=> 'max_length[120]'),
				array('field'	=> 'decalle',
						'label'	=> 'Calle',
						'rules'	=> 'max_length[80]'),
				array('field'	=> 'deno_exterior',
						'label'	=> 'No exterior',
						'rules'	=> 'max_length[8]'),
				array('field'	=> 'deno_interior',
						'label'	=> 'No interior',
						'rules'	=> 'max_length[8]'),
				array('field'	=> 'decolonia',
						'label'	=> 'Colonia',
						'rules'	=> 'max_length[80]'),
				array('field'	=> 'demunicipio',
						'label'	=> 'Municipio',
						'rules'	=> 'max_length[60]'),
				array('field'	=> 'deestado',
						'label'	=> 'Estado',
						'rules'	=> 'max_length[60]'),
				array('field'	=> 'decp',
						'label'	=> 'CP',
						'rules'	=> 'max_length[10]'),
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
				$txt = 'El cliente se agrego correctamente.';
				$icono = 'success';
			break;
			case 4:
				$txt = 'El cliente se modifico correctamente.';
				$icono = 'success';
				break;
			case 5:
				$txt = 'El cliente se elimino correctamente.';
				$icono = 'success';
				break;
			case 6:
				$txt = 'El cliente se activo correctamente.';
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