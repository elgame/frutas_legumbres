<?php

class clientes_model extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	/**
	 * Obtiene el listado de clientes
	 */
	public function getClientes(){
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
				SELECT id AS id_cliente, nombre_fiscal, rfc, telefono, email, status
				FROM clientes
				".$sql."
				ORDER BY nombre_fiscal ASC
				", $params, true);
		$res = $this->db->query($query['query']);

		$response = array(
			'clientes' 			=> array(),
			'total_rows' 		=> $query['total_rows'],
			'items_per_page' 	=> $params['result_items_per_page'],
			'result_page' 		=> $params['result_page']
		);
		$response['clientes'] = $res->result();
		return $response;
	}

	/**
	 * Obtiene la informacion de un cliente
	 */
	public function getInfoCliente($id, $info_basic=false){
		$res = $this->db
			->select('*')
			->from('clientes AS c')
			->where("c.id = '".$id."'")
		->get();
		if($res->num_rows() > 0){
			$response['info'] = $res->row();
			$res->free_result();
			if($info_basic)
				return $response;

			//info extra
			$res = $this->db
				->select('*')
				->from('clientes_extra')
				->where("cliente_id = '".$id."'")
			->get();
			if($res->num_rows() > 0){
				$response['info_extra'] = $res->row();
			}
			$res->free_result();

			return $response;
		}else
			return false;
	}

	/**
	 * Agrega la info de un cliente a la bd
	 */
	public function addCliente($data=null, $data_ext=null){

		if ($data == null) {
			$data = array(
				'nombre_fiscal'      => $this->input->post('dnombre_fiscal'),
				'rfc'                => $this->input->post('drfc'),
				'calle'              => $this->input->post('dcalle'),
				'no_exterior'        => $this->input->post('dno_exterior'),
				'no_interior'        => $this->input->post('dno_interior'),
				'colonia'            => $this->input->post('dcolonia'),
				'municipio'          => $this->input->post('dmunicipio'),
				'estado'             => $this->input->post('destado'),
				'cp'                 => $this->input->post('dcp'),
				'telefono'           => $this->input->post('dtelefono'),
				'celular'            => $this->input->post('dcelular'),
				'email'              => $this->input->post('demail'),
				'descuento'          => floatval($this->input->post('ddescuento')),
				'enviar_factura'     => ($this->input->post('denviar_factura')==='1'? '1': '0'),
			);
		}
		$this->db->insert('clientes', $data);
		$id_cliente = $this->db->insert_id();

		//Informacion Extra
		if ($data_ext == null) {
			$data_ext = array(
				'cliente_id'  => $id_cliente,
				'nombre'      => $this->input->post('denombre'),
				'calle'       => $this->input->post('decalle'),
				'no_exterior' => $this->input->post('deno_exterior'),
				'no_interior' => $this->input->post('deno_interior'),
				'colonia'     => $this->input->post('decolonia'),
				'municipio'   => $this->input->post('demunicipio'),
				'estado'      => $this->input->post('deestado'),
				'cp'          => $this->input->post('decp')
			);
		}
		$data_ext['cliente_id'] = $id_cliente;
		$this->db->insert('clientes_extra', $data_ext);

		//Contacto
		if(isset($_POST['dcnombre']{0})){
			$this->addContacto($id_cliente);
		}
		$msg = 3;
		return array(true, '', $msg);
	}

	/**
	 * Modifica la informacion de un cliente
	 */
	public function updateCliente($id_cliente, $data=null, $data_ext=null){
		$msg = 4;
		if ($data == null) {
			$data = array(
				'nombre_fiscal'      => $this->input->post('dnombre_fiscal'),
				'rfc'                => $this->input->post('drfc'),
				'calle'              => $this->input->post('dcalle'),
				'no_exterior'        => $this->input->post('dno_exterior'),
				'no_interior'        => $this->input->post('dno_interior'),
				'colonia'            => $this->input->post('dcolonia'),
				'municipio'          => $this->input->post('dmunicipio'),
				'estado'             => $this->input->post('destado'),
				'cp'                 => $this->input->post('dcp'),
				'telefono'           => $this->input->post('dtelefono'),
				'celular'            => $this->input->post('dcelular'),
				'email'              => $this->input->post('demail'),
				'descuento'          => floatval($this->input->post('ddescuento')),
				'enviar_factura'     => ($this->input->post('denviar_factura')==='1'? '1': '0'),
			);
		}
		$this->db->update('clientes', $data, "id = '".$id_cliente."'");

		//Informacion Extra
		if ($data_ext != false) {
			if ($data_ext == null) {
				$data_ext = array(
					'nombre'      => $this->input->post('denombre'),
					'calle'       => $this->input->post('decalle'),
					'no_exterior' => $this->input->post('deno_exterior'),
					'no_interior' => $this->input->post('deno_interior'),
					'colonia'     => $this->input->post('decolonia'),
					'municipio'   => $this->input->post('demunicipio'),
					'estado'      => $this->input->post('deestado'),
					'cp'          => $this->input->post('decp')
				);
			}
			$this->db->update('clientes_extra', $data_ext, "cliente_id = '".$id_cliente."'");
		}

		return array(true, '', $msg);
	}


	/**
	 * Elimina a un cliente, cambia su status a "e":eliminado
	 */
	public function eliminarCliente(){
		$this->db->update('clientes', array('status' => 'e'), "id_cliente = '".$_GET['id']."'");
		return array(true, '');
	}

	/**
	 * Agrega contactos al cliente
	 * @param unknown_type $id_sucursal
	 */
	public function addContacto($id_cliente=null){
		$id_cliente = $id_cliente==null? $this->input->post('id'): $id_cliente;

		$id_conta = BDUtil::getId();
		$data = array(
			'id_cliente'  => $id_cliente,
			'nombre'      => $this->input->post('dcnombre'),
			'puesto'      => $this->input->post('dcpuesto'),
			'telefono'    => $this->input->post('dctelefono'),
			'extension'   => $this->input->post('dcextension'),
			'celular'     => $this->input->post('dccelular'),
			'nextel'      => $this->input->post('dcnextel'),
			'nextel_id'   => $this->input->post('dcnextel_id'),
			'fax'         => $this->input->post('dcfax')
		);
		$this->db->insert('clientes_contacto', $data);
		$id_conta = $this->db->insert_id();
		return array(true, 'Se agregó el contacto correctamente.', $id_conta);
	}
	/**
	 * Elimina un contacto de un cliente de la bd
	 * @param unknown_type $id_contacto
	 */
	public function deleteContacto($id_contacto){
		$this->db->delete('clientes_contacto', "id_contacto = '".$id_contacto."'");
		return array(true, '');
	}

	/**
	 * Obtiene el listado de clientes para usar ajax
	 */
	public function getClientesAjax(){
		$sql = '';
		$res = $this->db->query("
				SELECT id_cliente, nombre_fiscal, calle, no_exterior, no_interior, colonia, localidad, municipio, estado, cp, telefono, dias_pago, rfc, descuento
				FROM clientes
				WHERE status = 'ac' AND lower(nombre_fiscal) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'
				ORDER BY nombre_fiscal ASC
				LIMIT 20");

		$response = array();
		if($res->num_rows() > 0){
			foreach($res->result() as $itm){
				$response[] = array(
						'id' => $itm->id_cliente,
						'label' => $itm->nombre_fiscal,
						'value' => $itm->nombre_fiscal,
						'item' => $itm,
				);
			}
		}

		return $response;
	}

}