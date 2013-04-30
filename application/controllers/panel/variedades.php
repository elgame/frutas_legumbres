<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class variedades extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('variedades/ajax_get_proveedor/');

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

	/**
	 * Agrega un productor a la bd
	 */
	public function agregar(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('libs/jquery.numeric.js'),
		));

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Agregar Variedad'
		);

		$this->configAddModVariedad();

		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$this->load->model('variedades_model');
			$respons = $this->variedades_model->addVariedad();

			if($respons[0])
				redirect(base_url('panel/variedades/agregar/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
			else
        $params['frm_errors'] = $this->showMsgs(2, $respons[1]);
		}

		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);

		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/variedades/agregar', $params);
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
				$this->configAddModVariedad();
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
	private function configAddModVariedad(){
		$this->load->library('form_validation');

			$rules = array(
				array('field'	=> 'dnombre',
						'label'	=> 'Nombre',
						'rules'	=> 'required|max_length[40]'),
				array('field'	=> 'dtipo',
						'label'	=> 'Pagar por',
						'rules'	=> 'required|max_length[1]'),
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
				$txt = 'La variedad se agrego correctamente.';
				$icono = 'success';
			break;
			case 4:
				$txt = 'La variedad se modifico correctamente.';
				$icono = 'success';
				break;
			case 5:
				$txt = 'La variedad se elimino correctamente.';
				$icono = 'success';
				break;
			case 6:
				$txt = 'La variedad se activo correctamente.';
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