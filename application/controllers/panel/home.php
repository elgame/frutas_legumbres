<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class home extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('');

	public function _remap($method){

		$this->load->model("usuarios_model");
		if($this->usuarios_model->checkSession()){
			$this->usuarios_model->excepcion_privilegio = $this->excepcion_privilegio;
			$this->info_empleado                         = $this->usuarios_model->get_usuario_info($this->session->userdata('id'), true);

			$this->{$method}();
		}else
			$this->{'login'}();
	}

	public function index(){

    $this->carabiner->js(array(
      array('general/msgbox.js'),
      array('panel/home.js'),
    ));

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Panel de Administración'
		);
		$params['venta_dia'] = $params['venta_semana'] = $params['venta_mes'] = 0;

		$this->load->model('reportes_model');

		// $data = $this->reportes_model->getDataRVentasProductos(
		// 		date('Y-m-d'), 
		// 		date('Y-m-d') );
		// foreach ($data['info'] as $key => $value) {
		// 	$params['venta_dia'] += $value->total;
		// }

		// $calcWeek = date('W', strtotime(''))-1;
		// $data = $this->reportes_model->getDataRVentasProductos(
		// 		date('Y-m-d', strtotime('Monday ' . ($calcWeek-1) . ' weeks')), 
		// 		date('Y-m-d', strtotime('Sunday ' . $calcWeek . ' weeks')) );
		// foreach ($data['info'] as $key => $value) {
		// 	$params['venta_semana'] += $value->total;
		// }

		// $data = $this->reportes_model->getDataRVentasProductos(
		// 		date('Y-m')."-01", 
		// 		date('Y-m').'-'.String::ultimoDia(date('Y'), date('m')) );
		// foreach ($data['info'] as $key => $value) {
		// 	$params['venta_mes'] += $value->total;
		// }

		// $data = $this->reportes_model->getDataRBajoInventario(null, date("Y-m-d"));
		// $params['bajos_inventario'] = count($data['info']);

		// $data = $this->reportes_model->productos_vendidos(null, null, 'cantidad DESC LIMIT 10');
		// $params['mas_vendidos'] = $data['info'];
		// $data = $this->reportes_model->productos_vendidos(null, null, 'cantidad ASC LIMIT 10');
		// $params['menos_vendidos'] = $data['info'];
		
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/general/home', $params);
		$this->load->view('panel/footer');
	}




	/**
	 * carga el login para entrar al panel
	 */
	public function login(){

		$params['seo'] = array(
			'titulo' => 'Login'
		);

		$this->load->library('form_validation');
		$rules = array(
			array('field'	=> 'usuario',
				'label'		=> 'Usuario',
				'rules'		=> 'required'),
			array('field'	=> 'pass',
				'label'		=> 'Contraseña',
				'rules'		=> 'required')
		);
		$this->form_validation->set_rules($rules);
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = array(
					'title' => 'Error al Iniciar Sesión!',
					'msg' => preg_replace("[\n|\r|\n\r]", '', validation_errors()),
					'ico' => 'error');
		}else{
			$data = array('usuario' => $this->input->post('usuario'), 'pass' => $this->input->post('pass'));
			$mdl_res = $this->usuarios_model->setLogin($data);
			if ($mdl_res[0] && $this->usuarios_model->checkSession()) {
				redirect(base_url('panel/home'));
			}
			else{
				$params['frm_errors'] = array(
					'title' => 'Error al Iniciar Sesión!',
					'msg' => 'El usuario y/o contraseña son incorrectos, o no cuenta con los permisos necesarios para loguearse',
					'ico' => 'error');
			}
		}

		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/login', $params);
		$this->load->view('panel/footer');
	}

	/**
	 * cierra la sesion del usuario
	 */
	public function logout(){
		$this->session->sess_destroy();
		redirect(base_url('panel/home'));
	}
}

?>