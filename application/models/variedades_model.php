<?php

class variedades_model extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	/**
	 * Obtiene el listado de Variedades
	 */
	public function getVariedades(){
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
				lower(nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' )";
		
		$fstatus = $this->input->get('fstatus')===false? 'ac': $this->input->get('fstatus');
		if($fstatus != '' && $fstatus != 'todos')
			$sql .= ($sql==''? ' WHERE ': ' AND ')." status = '".$fstatus."'";

		$query = BDUtil::pagination("
				SELECT id_variedad, nombre, tipo_pago, status
				FROM variedades
				".$sql."
				ORDER BY nombre ASC
				", $params, true);
		$res = $this->db->query($query['query']);

		$response = array(
			'variedades'    => array(),
			'total_rows'     => $query['total_rows'],
			'items_per_page' => $params['result_items_per_page'],
			'result_page'    => $params['result_page']
		);
		$response['variedades'] = $res->result();
		return $response;
	}

	/**
	 * Obtiene la informacion de una variedad
	 */
	public function getInfoVariedad($id, $info_basic=false){
		$res = $this->db
			->select('*')
			->from('variedades AS v')
			->where("v.id_variedad = '".$id."'")
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
	 * Agrega la info de un variedad a la bd
	 */
	public function addVariedad($data=null){

		if ($data == null) {
			$data = array(
				'nombre'    => $this->input->post('dnombre'),
				'tipo_pago' => $this->input->post('dtipo'),
			);
		}
		$this->db->insert('variedades', $data);

		$msg = 3;
		return array(true, '', $msg);
	}

	/**
	 * Modifica la informacion de un variedad
	 */
	public function updateVariedad($id, $data=null){
		$msg = 4;
		if ($data == null) {
			$data = array(
				'nombre'    => $this->input->post('dnombre'),
				'tipo_pago' => $this->input->post('dtipo'),
			);
		}
		$this->db->update('variedades', $data, "id_variedad = '".$id."'");

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