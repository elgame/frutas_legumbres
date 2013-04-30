<?php

class productores_model extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	/**
	 * Obtiene el listado de Productores
	 */
	public function getProductores(){
		$sql = '';
		//paginacion
		$params = array(
				'result_items_per_page' => '20',
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);

		//Filtros para buscar
		if($this->input->get('fnombre') != '')
			$sql = " WHERE ( 
				lower(nombre_fiscal) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
				lower(rfc) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
				lower(email) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
				lower(calle) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
				lower(colonia) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
				lower(municipio) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
				lower(estado) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%'
				)";
		
		$fstatus = $this->input->get('fstatus')===false? 'ac': $this->input->get('fstatus');
		if($fstatus != '' && $fstatus != 'todos')
			$sql .= ($sql==''? ' WHERE ': ' AND ')." status = '".$fstatus."'";

		$query = BDUtil::pagination("
				SELECT id_productor, nombre_fiscal, rfc, telefono, email, 
					CONCAT(calle, ' #', no_exterior, ', ', colonia, ', ', municipio, ', ', estado) AS direccion, status
				FROM productores
				".$sql."
				ORDER BY nombre_fiscal ASC
				", $params, true);
		$res = $this->db->query($query['query']);

		$response = array(
			'productores'    => array(),
			'total_rows'     => $query['total_rows'],
			'items_per_page' => $params['result_items_per_page'],
			'result_page'    => $params['result_page']
		);
		$response['productores'] = $res->result();
		return $response;
	}

	/**
	 * Obtiene la informacion de un proveedor
	 */
	public function getInfoProveedor($id, $info_basic=false){
		$res = $this->db
			->select('*')
			->from('proveedores AS p')
			->where("p.id = '".$id."'")
		->get();
		if($res->num_rows() > 0){
			$response['info'] = $res->row();
			$res->free_result();
			if($info_basic)
				return $response;


			return $response;
		}else
			return false;
	}

	/**
	 * Agrega la info de un productor a la bd
	 */
	public function addProductor($data=null){

		if ($data == null) {
			$logo = '';
			//valida la imagen
			$upload_res = UploadFiles::uploadProductorLogo();

			if(is_array($upload_res)){
				if($upload_res[0] == false)
					return array(false, $upload_res[1]);
				$logo = APPPATH.'images/productor/logos/'.$upload_res[1]['file_name'];
			}

			$data = array(
				'nombre_fiscal'  => $this->input->post('dnombre_fiscal'),
				'rfc'            => $this->input->post('drfc'),
				'calle'          => $this->input->post('dcalle'),
				'no_exterior'    => $this->input->post('dno_exterior'),
				'no_interior'    => $this->input->post('dno_interior'),
				'colonia'        => $this->input->post('dcolonia'),
				'municipio'      => $this->input->post('dmunicipio'),
				'estado'         => $this->input->post('destado'),
				'cp'             => $this->input->post('dcp'),
				'telefono'       => $this->input->post('dtelefono'),
				'celular'        => $this->input->post('dcelular'),
				'email'          => $this->input->post('demail'),
				'logo'           => $logo,
				'regimen_fiscal' => $this->input->post('dregimen_fiscal'),
				'tipo'           => $this->input->post('dtipo'),
			);
		}
		$this->db->insert('productores', $data);

		$msg = 3;
		return array(true, '', $msg);
	}

	/**
	 * Modifica la informacion de un proveedor
	 */
	public function updateProveedor($id_cliente, $data=null){
		$msg = 4;
		if ($data == null) {
			$data = array(
				'nombre_fiscal' => $this->input->post('dnombre_fiscal'),
				'rfc'           => $this->input->post('drfc'),
				'calle'         => $this->input->post('dcalle'),
				'no_exterior'   => $this->input->post('dno_exterior'),
				'no_interior'   => $this->input->post('dno_interior'),
				'colonia'       => $this->input->post('dcolonia'),
				'municipio'     => $this->input->post('dmunicipio'),
				'estado'        => $this->input->post('destado'),
				'cp'            => $this->input->post('dcp'),
				'telefono1'     => $this->input->post('dtelefono1'),
				'telefono2'     => $this->input->post('dtelefono2'),
				'celular'       => $this->input->post('dcelular'),
				'email'         => $this->input->post('demail'),
			);
		}
		$this->db->update('proveedores', $data, "id = '".$id_cliente."'");

		return array(true, '', $msg);
	}


	/**
	 * Obtiene el listado de proveedores para usar ajax
	 */
	public function getProveedoresAjax(){
		$sql = '';
		$res = $this->db->query("
				SELECT id, nombre_fiscal, rfc, calle, no_exterior, no_interior, colonia, municipio, estado, cp, telefono1 
				FROM proveedores
				WHERE status = 1 AND lower(nombre_fiscal) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'
				ORDER BY nombre_fiscal ASC
				LIMIT 20");

		$response = array();
		if($res->num_rows() > 0){
			foreach($res->result() as $itm){
				$response[] = array(
						'id'    => $itm->id,
						'label' => $itm->nombre_fiscal,
						'value' => $itm->nombre_fiscal,
						'item'  => $itm,
				);
			}
		}

		return $response;
	}

}