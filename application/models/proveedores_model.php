<?php

class proveedores_model extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	/**
	 * Obtiene el listado de proveedores
	 */
	public function getProveedores(){
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
		
		$fstatus = $this->input->get('fstatus')===false? '1': $this->input->get('fstatus');
		if($fstatus != '' && $fstatus != 'todos')
			$sql .= ($sql==''? ' WHERE ': ' AND ')." status = '".$fstatus."'";

		$query = BDUtil::pagination("
				SELECT id, nombre_fiscal, rfc, telefono1, email, 
					CONCAT(calle, ' #', no_exterior, ', ', colonia, ', ', municipio, ', ', estado) AS direccion, status
				FROM proveedores
				".$sql."
				ORDER BY nombre_fiscal ASC
				", $params, true);
		$res = $this->db->query($query['query']);

		$response = array(
			'proveedores'    => array(),
			'total_rows'     => $query['total_rows'],
			'items_per_page' => $params['result_items_per_page'],
			'result_page'    => $params['result_page']
		);
		$response['proveedores'] = $res->result();
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
	 * Agrega la info de un proveedor a la bd
	 */
	public function addProveedor($data=null){

		if ($data == null) {
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
				'telefono1'      => $this->input->post('dtelefono1'),
				'telefono2'      => $this->input->post('dtelefono2'),
				'celular'        => $this->input->post('dcelular'),
				'email'          => $this->input->post('demail'),
			);
		}
		$this->db->insert('proveedores', $data);

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