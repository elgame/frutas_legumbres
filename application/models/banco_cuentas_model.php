<?php

class banco_cuentas_model extends banco_model{

	function __construct(){
		parent::__construct();
	}

	/**
	 * Obtiene el listado de cuentas bancarias
	 * si id_banco y id_cuenta son null, regresa todas las cuentas bancarias
	 * si id_banco tiene un valor y id_cuenta es null, regresa las cuentas del banco seleccionado
	 * si id_banco es 0 y id_cuenta tiene un valor, regresa 1 cuenta bancaria (la cuenta del id)
	 * @param  [type] $id_banco  [description] 
	 * @param  [type] $id_cuenta [description]
	 * @return [type]            [description]
	 */
	public function getCuentas($id_banco=null, $id_cuenta=null){
		$sql = '';

		//Filtros para buscar
		if($id_banco != null)
			$sql = "WHERE bb.id_banco = ".$id_banco;
		if($id_cuenta!=null && $id_banco == 0)
			$sql = "WHERE bc.id_cuenta = ".$id_cuenta;

		if($this->input->get('fnombre') != '')
			$sql .= " AND ( 
				lower(bc.numero) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
				lower(bc.alias) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
				lower(bb.nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' )";
		
		$fstatus = $this->input->get('fstatus')===false? 'ac': $this->input->get('fstatus');
		if($fstatus != '' && $fstatus != 'todos')
			$sql .= " AND bc.status = '".$fstatus."'";


		$res = $this->db->query("SELECT bc.id_cuenta, bb.id_banco, bb.nombre AS banco, bc.numero, bc.alias, bc.status,
					(SELECT Sum(monto) FROM bancos_movimientos WHERE id_cuenta = bc.id_cuenta AND tipo = 'd') AS depositos,
					(SELECT Sum(monto) FROM bancos_movimientos WHERE id_cuenta = bc.id_cuenta AND tipo = 'r') AS retiros
				FROM bancos_cuentas AS bc 
					INNER JOIN bancos_bancos AS bb ON bc.id_banco = bb.id_banco 
				".$sql."
				ORDER BY bc.alias ASC");

		$response = array(
			'cuentas'    => array()
		);
		if($res->num_rows() > 0)
			foreach ($res->result() as $key => $value) {
				$value->depositos      = floatval($value->depositos);
				$value->retiros        = floatval($value->retiros);
				$value->saldo          = $value->depositos - $value->retiros;
				$response['cuentas'][] = $value;
			}
		return $response;
	}

	/**
	 * Obtiene la informacion de una operacion
	 */
	public function getInfoOperacion($id, $info_basic=false){
		$res = $this->db
			->select('*')
			->from('bancos_movimientos AS bm')
			->where("bm.id_movimiento = '".$id."'")
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
	 * Agrega la info
	 */
	public function addOperacion($data=null, $data_cons=null){

		if ($data == null) {
			$data = array(
				'id_banco'         => $this->input->post('dbanco'),
				'id_cuenta'        => $this->input->post('dcuenta'),
				'id_fac_productor' => ($this->input->get('id')!==false? $this->input->get('id'): NULL),
				'fecha'            => $this->input->post('dfecha'),
				'concepto'         => $this->input->post('dconcepto'),
				'monto'            => $this->input->post('dmonto'),
				'tipo'             => $this->input->post('dtipo_operacion'),
				'metodo_pago'      => $this->input->post('dmetodo_pago'),
			);
			if ($this->input->post('dtipo_operacion') == 'r' && $this->input->post('dmetodo_pago') == 'cheque') {
				$data['anombre_de']   = $this->input->post('dchk_anombre');
				$data['moneda']       = $this->input->post('dmoneda');
				$data['abono_cuenta'] = ($this->input->post('dabono_cuenta')==='1'? '1': '0');
			}
		}
		$this->db->insert('bancos_movimientos', $data);
		$id_mov = $this->db->insert_id();

		if ($data_cons == null) {
			$data_cons = array();
			if( is_array($this->input->post('dconcep_conce')) ){
				foreach ($this->input->post('dconcep_conce') as $key => $value) {
					$data_cons[] = array(
						'id_movimiento' => $id_mov,
						'no_concepto'   => ($key+1),
						'concepto'      => $value,
						'monto'         => $_POST['dconcep_monto'][$key],
					);
				}
			}
		}
		if(count($data_cons) > 0)
			$this->db->insert_batch('bancos_movimientos_conceptos', $data_cons);

		$msg = 3;
		return array(true, '', $msg, $id_mov);
	}




	/**
	 * ***********  CUENTAS BANCARIAS  ***************
	 */
	public function addCuenta($id, $data=null){
		$msg = 4;
		if ($data == null) {
			$data = array(
				'id_banco' => $this->input->post('dbanco'),
				'numero'   => $this->input->post('dnumero'),
				'alias'    => $this->input->post('dalias'),
			);
		}
		$this->db->insert('bancos_cuentas', $data);

		return array(true, '', $msg);
	}

	/**
	 * Modifica la informacion de una cuenta
	 */
	public function updateCuenta($id, $data=null){
		$msg = 7;
		if ($data == null) {
			$data = array(
				'id_banco' => $this->input->post('dbanco'),
				'numero'   => $this->input->post('dnumero'),
				'alias'    => $this->input->post('dalias'),
			);
		}
		$this->db->update('bancos_cuentas', $data, "id_cuenta = '".$id."'");

		return array(true, '', $msg);
	}


	/**
	 * Obtiene el listado de proveedores para usar ajax
	 */
	public function getCuentasAjax(){
		$sql = '';
		$res = $this->db->query("
				SELECT id_cuenta, id_banco, numero, alias, status 
				FROM bancos_cuentas
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