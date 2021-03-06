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
					(SELECT Sum(monto) FROM bancos_movimientos WHERE id_cuenta = bc.id_cuenta AND status = 1 AND tipo = 'd') AS depositos,
					(SELECT Sum(monto) FROM bancos_movimientos WHERE id_cuenta = bc.id_cuenta AND status = 1 AND tipo = 'r') AS retiros
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
				$data['no_cheque']    = $this->input->post('dno_cheque');
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
		return array(true, '', $msg, $id_mov, $this->input->post('dmetodo_pago'), $this->input->post('dtipo_operacion'));
	}

	public function eliminarOperacion($id_movimiento){
		$this->db->delete('bancos_movimientos', 'id_movimiento = '.$id_movimiento);
	}

	/**
	 * Obtiene el listado de productores para usar ajax
	 */
	public function getChequesNombresAjax(){
		$sql = '';
		if ($this->input->get('term') !== false)
			$sql = " AND lower(anombre_de) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'";
		$res = $this->db->query("SELECT Distinct(anombre_de) 
			FROM bancos_movimientos 
			WHERE anombre_de IS NOT NULL ".$sql."
			ORDER BY anombre_de ASC
			LIMIT 20");

		$response = array();
		if($res->num_rows() > 0){
			foreach($res->result() as $itm){
				$response[] = array(
						'id'    => $itm->anombre_de,
						'label' => $itm->anombre_de,
						'value' => $itm->anombre_de,
						'item'  => $itm,
				);
			}
		}

		return $response;
	}


	public function getListaCheques($paginar=true)
  {
    $sql = '';
    //paginacion
    $params = array(
        'result_items_per_page' => '20',
        'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
    );
    if($params['result_page'] % $params['result_items_per_page'] == 0)
      $params['result_page'] = ($params['result_page']/$params['result_items_per_page']);

    //Filtros para buscar
    $_GET['ffecha1'] = isset($_GET['ffecha1'])? $_GET['ffecha1']: date("Y-m").'-01';
		$_GET['ffecha2'] = isset($_GET['ffecha2'])? $_GET['ffecha2']: date("Y-m-d");
		$sql = " AND Date(bm.fecha) BETWEEN '".$_GET['ffecha1']."' AND '".$_GET['ffecha2']."'";

    if($this->input->get('fnombre') != '')
      $sql .= " AND ( lower(b.nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
      	bm.no_cheque LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
      	lower(bm.anombre_de) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
      	lower(bc.alias) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' )";

    $query = BDUtil::pagination("
        SELECT bm.id_movimiento, bm.fecha, bm.monto, bm.no_cheque, bm.anombre_de, b.nombre AS banco, bc.alias, bm.status
        FROM bancos_movimientos AS bm
	        INNER JOIN bancos_cuentas AS bc ON bc.id_cuenta = bm.id_cuenta
	        INNER JOIN bancos_bancos AS b ON b.id_banco = bm.id_banco
        WHERE metodo_pago = 'cheque' AND tipo = 'r' ".$sql."
        ORDER BY bm.fecha DESC
        ", $params, true);
    if($paginar)
    	$res = $this->db->query($query['query']);
    else
    	$res = $query['resultset'];

    $response = array(
      'cheques'          => array(),
      'total_rows'     => $query['total_rows'],
      'items_per_page' => $params['result_items_per_page'],
      'result_page'    => $params['result_page']
    );
    $response['cheques'] = $res->result();
    return $response;
  }
  /**
   * Cansela o activa un cheque
   * @param  [type]  $id_mov [description]
   * @param  integer $status [description]
   * @return [type]          [description]
   */
  public function cancelarCheque($id_mov, $status=0){
  	$this->db->update('bancos_movimientos', array('status' => $status), 'id_movimiento = '.$id_mov);
  }

  public function xlsListaCheques(){
  	$data = $this->getListaCheques();

  	$this->load->library('myexcel');
    $xls = new myexcel();

    $worksheet =& $xls->workbook->addWorksheet();

    $xls->titulo2 = 'Lista de cheques';
    $xls->titulo3 = '';
    $xls->titulo4 = 'Del: '.$this->input->get('ffecha1').' Al '.$this->input->get('ffecha2')."\n";

    $row=0;
    // //Header
    $xls->excelHead($worksheet, $row, 8, array(
                    array($xls->titulo2, 'format_title2'),
                    array($xls->titulo3, 'format_title3'),
                    array($xls->titulo4, 'format_title3')
    ));


    foreach ($data['cheques'] as $key => $value) {
    	$data['cheques'][$key]->status = $value->status==0? 'Cancelado': '';
    }
    $row +=3;
    $xls->excelContent($worksheet, $row, $data['cheques'], array(
                    'head' => array('Fecha', 'Banco', 'Cuenta', 'Monto', '# Cheque', 'A nombre de', 'Status'),
                    'conte' => array(
                                    array('name' => 'fecha', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'banco', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'alias', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'monto', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'no_cheque', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'anombre_de', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'status', 'format' => 'format4', 'sum' => -1))
    ));

    $xls->workbook->send('lista_cheques.xls');
    $xls->workbook->close();
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



	/**
	 * *************  SALDOS Y ESTADOS DE CUENTA  ****************
	 */
	/**
	 * obtiene los saldos de las cuentas bancarias
	 * @param  [type] $fecha [description]
	 * @return [type]        [description]
	 */
	public function getDataSaldos($fecha){
		$bancos = $this->getBancos()['bancos'];
		foreach ($bancos as $key => $value) {
			$bancos[$key]->saldo   = 0;
			$bancos[$key]->cuentas = $this->db->query("SELECT bc.id_cuenta, bc.alias, bc.numero, 
					IFNULL(d.monto, 0) AS depositos, IFNULL(r.monto, 0) AS retiros, 
					(IFNULL(d.monto, 0) - IFNULL(r.monto, 0)) AS saldo
				FROM bancos_cuentas AS bc 
					LEFT JOIN (
						SELECT id_cuenta, Sum(monto) AS monto FROM bancos_movimientos 
						WHERE status = 1 AND tipo = 'd' AND Date(fecha) <= '".$fecha."' GROUP BY id_cuenta ) AS d ON d.id_cuenta = bc.id_cuenta 
					LEFT JOIN (
						SELECT id_cuenta, Sum(monto) AS monto FROM bancos_movimientos 
						WHERE status = 1 AND tipo = 'r' AND Date(fecha) <= '".$fecha."' GROUP BY id_cuenta ) AS r ON r.id_cuenta = bc.id_cuenta
				WHERE bc.status = 'ac' AND bc.id_banco = ".$value->id_banco." 
				GROUP BY id_cuenta")->result();	
			foreach ($bancos[$key]->cuentas as $key1 => $cun) {
				$bancos[$key]->saldo += $cun->saldo;
			}
		}
		return $bancos;
	}

	/**
	 * obtiene la informacion del estado de cuenta seleccionado
	 * @param  [type] $id_cuenta [description]
	 * @param  [type] $fecha1    [description]
	 * @param  [type] $fecha2    [description]
	 * @return [type]            [description]
	 */
	public function getDataEstadoCuenta($id_cuenta, $fecha1, $fecha2){
		$response = array(
			'cuenta'      => '',
			'movimientos' => array(),
			'depositos'   => 0,
			'retiros'     => 0,
			);

		$cuenta = $this->getCuentas(0, $id_cuenta);
		$response['cuenta'] = $cuenta['cuentas'][0];

		//saldo anterior
		$saldo_anterior = $this->db->query("SELECT bc.id_cuenta, bc.alias, bc.numero, 
					IFNULL(d.monto, 0) AS depositos, IFNULL(r.monto, 0) AS retiros, 
					(IFNULL(d.monto, 0) - IFNULL(r.monto, 0)) AS saldo
				FROM bancos_cuentas AS bc 
					LEFT JOIN (
						SELECT id_cuenta, Sum(monto) AS monto FROM bancos_movimientos 
						WHERE status = 1 AND tipo = 'd' AND Date(fecha) < '".$fecha1."' GROUP BY id_cuenta ) AS d ON d.id_cuenta = bc.id_cuenta 
					LEFT JOIN (
						SELECT id_cuenta, Sum(monto) AS monto FROM bancos_movimientos 
						WHERE status = 1 AND tipo = 'r' AND Date(fecha) < '".$fecha1."' GROUP BY id_cuenta ) AS r ON r.id_cuenta = bc.id_cuenta
				WHERE bc.id_cuenta = ".$id_cuenta." 
				GROUP BY id_cuenta")->row();
		$response['movimientos'][] = array(
			'id_movimiento' => '',
			'fecha'         => '',
			'concepto'      => 'SALDO ANTERIOR AL '.$fecha1,
			'depositos'     => String::formatoNumero($saldo_anterior->depositos),
			'retiros'       => String::formatoNumero($saldo_anterior->retiros),
			'saldo'         => String::formatoNumero($saldo_anterior->saldo),
			'conceptos'     => array(),
			);
		
		$saldo = $saldo_anterior->saldo;
		$total_depositos = $total_retiros = $depositos = $retiros = 0;
		$movimientos = $this->db->query("SELECT id_movimiento, Date(fecha) AS fecha, concepto, monto, tipo, metodo_pago, no_cheque, status
			FROM bancos_movimientos 
			WHERE id_cuenta = ".$id_cuenta." AND Date(fecha) BETWEEN '".$fecha1."' AND '".$fecha2."' 
			ORDER BY fecha ASC, id_movimiento ASC")->result();
		foreach ($movimientos as $key => $value) {
			$depositos = $retiros = $masconcepto = '';
			if ($value->tipo == 'd') {
				$depositos       = String::formatoNumero($value->monto);
				if ($value->status == 1){
					$total_depositos += $value->monto;
					$saldo           += $value->monto;
				}
			}else{
				$retiros       = String::formatoNumero($value->monto);
				if ($value->status == 1){
					$total_retiros += $value->monto;
					$saldo         -= $value->monto;
				}
				if($value->metodo_pago=='cheque'){
					$masconcepto = '<strong style="'.($value->status==0? 'color:red;': '').'">'.
						ucfirst($value->metodo_pago).' No '.($value->no_cheque!=''? $value->no_cheque: 'S/N').
						($value->status==0? ' (Cancelado)': '').'</strong> | ';
				}
			}
			$response['movimientos'][] = array(
				'id_movimiento' => $value->id_movimiento,
				'fecha'         => $value->fecha,
				'concepto'      => $masconcepto.$value->concepto,
				'depositos'     => $depositos,
				'retiros'       => $retiros,
				'saldo'         => String::formatoNumero($saldo),
				'conceptos'     => $this->getConceptosMov($value->id_movimiento),
				);
		}
		$response['depositos'] = String::formatoNumero($total_depositos);
		$response['retiros']   = String::formatoNumero($total_retiros);

		return $response;
	}
	/**
	 * obtiene los conceptos reales de un movimiento
	 * @param  [type] $id_movimiento [description]
	 * @return [type]                [description]
	 */
	public function getConceptosMov($id_movimiento){
		$data = $this->db->query("SELECT id_movimiento, no_concepto, concepto, monto
		                           FROM bancos_movimientos_conceptos
		                           WHERE id_movimiento = ".$id_movimiento." 
		                           ORDER BY no_concepto");
		$respon = array();
		if($data->num_rows()>0){
			foreach ($data->result() as $key => $value) {
				$value->monto = String::formatoNumero($value->monto);
				$respon[] = $value;
			}
		}
		return $respon;
	}

	/**
	 * genera el estado de ceunta de la cuenta bancaria seleccionada
	 * @param  [type] $id_cuenta [description]
	 * @param  [type] $fecha1    [description]
	 * @param  [type] $fecha2    [description]
	 * @return [type]            [description]
	 */
	public function printEstadoCuenta($id_cuenta, $fecha1, $fecha2){
		$data = $this->getDataEstadoCuenta($id_cuenta, $fecha1, $fecha2);

		$this->load->library('mypdf');
    // Creación del objeto de la clase heredada
    $pdf = new MYpdf('P', 'mm', 'Letter');
    $pdf->titulo2 = "Estado de ceunta | ";
    $pdf->titulo2 .= $data['cuenta']->banco.' ('.$data['cuenta']->alias.')';
    $pdf->titulo3 = 'Del: '.$fecha1." Al ".$fecha2."\n";

    $pdf->AliasNbPages();
    //$pdf->AddPage();
    $pdf->SetFont('Arial','', 8);

		$aligns  = array('C', 'L', 'C', 'C', 'C');
		$widths  = array(20, 100, 28, 28, 28);
		$widths2 = array(20, 100, 56, 28);
		$header  = array('Fecha', 'Concepto', 'Retiros', 'Depósitos', 'Saldo');

    foreach ($data['movimientos'] as $key => $value) {
    	if($pdf->GetY() >= $pdf->limiteY || $key==0) //salta de pagina si exede el max
      {
        $pdf->AddPage();

        $pdf->SetFont('Arial','B',8);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFillColor(160,160,160);
        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($header, true);
      }

      $pdf->SetFont('Arial','',8);
      $pdf->SetTextColor(0,0,0);


      $pdf->SetX(6);
      $pdf->SetAligns($aligns);
      $pdf->SetWidths($widths);
      $pdf->Row(array(
					$value['fecha'],
					strip_tags($value['concepto']),
					$value['retiros'],
					$value['depositos'],
					$value['saldo'],
      	));

      if(count($value['conceptos']) > 0){
      	$pdf->SetFillColor(205,255,209);
      	foreach ($value['conceptos'] as $key2 => $cons) {
	      	$pdf->SetX(6);
		      $pdf->SetAligns($aligns);
		      $pdf->SetWidths($widths2);
		      $pdf->Row(array(
							'',
							$cons->concepto,
							$cons->monto,
							'',
		      	), true);
		    }
      }
    }
    
    $pdf->SetFont('Arial','B',8);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFillColor(160,160,160);
    $pdf->SetX(6);
    $pdf->SetAligns($aligns);
    $pdf->SetWidths($widths);
    $pdf->Row(array(
				'',
				'Total:',
				$data['retiros'],
				$data['depositos'],
				'',
    	), true);

    $pdf->Output('estado_ceunta.pdf', 'I');
	}

	public function xlsEstadoCuenta($id_cuenta, $fecha1, $fecha2)
  {
  	$data = $this->getDataEstadoCuenta($id_cuenta, $fecha1, $fecha2);

    $this->load->library('myexcel');
    $xls = new myexcel();

    $worksheet =& $xls->workbook->addWorksheet();

    $xls->titulo2 = 'Estado de ceutna';
    $xls->titulo3 = $data['cuenta']->banco.' ('.$data['cuenta']->alias.')';
    $xls->titulo4 = 'Del: '.$fecha1.' Al '.$fecha2."\n";


    $row=0;
    // //Header
    $xls->excelHead($worksheet, $row, 8, array(
                    array($xls->titulo2, 'format_title2'),
                    array($xls->titulo3, 'format_title3'),
                    array($xls->titulo4, 'format_title3')
    ));

    $row +=3;
		$array_obj = array();

		//header
		$head = array('Fecha', 'Concepto', 'Retiros', 'Depositos', 'Saldo');
		foreach ($head as $key => $value) {
			$worksheet->write($row, $key, $value, $xls->formatsEx['format3']);
		}
		//movimientos
    foreach ($data['movimientos'] as $key => $value) {
    	$row++;

    	$worksheet->write($row, 0, $value['fecha'], $xls->formatsEx['format4']);
    	$worksheet->write($row, 1, strip_tags($value['concepto']), $xls->formatsEx['format4']);
    	$worksheet->write($row, 2, String::float($value['retiros']), $xls->formatsEx['format4']);
    	$worksheet->write($row, 3, String::float($value['depositos']), $xls->formatsEx['format4']);
    	$worksheet->write($row, 4, String::float($value['saldo']), $xls->formatsEx['format4']);

			foreach ($value['conceptos'] as $key2 => $value2) {
				$row++;
				$worksheet->write($row, 0, $value2->concepto, $xls->formatsEx['format6']);
				$worksheet->write($row, 1, '', $xls->formatsEx['format6']);
    		$worksheet->write($row, 2, String::float($value2->monto), $xls->formatsEx['format6']);
    		$worksheet->write($row, 3, '', $xls->formatsEx['format6']);
    		$worksheet->write($row, 4, '', $xls->formatsEx['format6']);
			}
		}

    $row++;
    $worksheet->write($row, 2, String::float($data['retiros']), $xls->formatsEx['format5']);
    $worksheet->write($row, 3, String::float($data['depositos']), $xls->formatsEx['format5']);

    $xls->workbook->send('estado_cuenta.xls');
    $xls->workbook->close();
  }

}