<?php

class banco_model extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	/**
	 * Obtiene el listado de Variedades
	 */
	public function getBancos(){
		$sql = '';
		// //paginacion
		// $params = array(
		// 		'result_items_per_page' => '20',
		// 		'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		// );
		// if($params['result_page'] % $params['result_items_per_page'] == 0)
		// 	$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);

		//Filtros para buscar
		if($this->input->get('fnombre') != '')
			$sql = " WHERE ( 
				lower(nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' )";
		
		$fstatus = $this->input->get('fstatus')===false? 'ac': $this->input->get('fstatus');
		if($fstatus != '' && $fstatus != 'todos')
			$sql .= ($sql==''? ' WHERE ': ' AND ')." status = '".$fstatus."'";


		$res = $this->db->query("SELECT id_banco, nombre, status
				FROM bancos_bancos
				".$sql."
				ORDER BY nombre ASC");

		$response = array(
			'bancos'    => array()
		);
		if($res->num_rows() > 0)
			$response['bancos'] = $res->result();
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
	public function getBancosAjax(){
		$sql = '';
		$res = $this->db->query("
				SELECT id_banco, nombre, status 
				FROM bancos_bancos
				WHERE status = 'ac' AND lower(nombre) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'
				ORDER BY nombre ASC
				LIMIT 20");

		$response = array();
		if($res->num_rows() > 0){
			foreach($res->result() as $itm){
				$response[] = array(
						'id'    => $itm->id_banco,
						'label' => $itm->nombre,
						'value' => $itm->nombre,
						'item'  => $itm,
				);
			}
		}

		return $response;
	}

}