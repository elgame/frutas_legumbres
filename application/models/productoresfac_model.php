<?php
class productoresfac_model extends privilegios_model{

	function __construct(){
		parent::__construct();
	}



	/**
	 * 					FACTURACION
	 * **********************************************
	 * Obtiene el limite para facturar
	 */
	public function getLimiteProductor($id_productor, $anio, $fecha=''){
		$data_salario = $this->db->query("SELECT zona_c AS salario FROM nomina_salarios_minimos WHERE id_salario = 1")->row();
		$response['limite'] = $data_salario->salario * 40 * 365;
		
		$sql_fecha = $fecha!=''? " AND Date(fecha) <= '".$fecha."'": '';
		$data_saldo = $this->db->query("SELECT Sum(total) AS total 
			FROM productores_facturas 
			WHERE status <> 'ca' AND id_productor = '".$id_productor."' AND YEAR(fecha) = ".$anio.$sql_fecha)->row();

		$response['saldo'] = $data_saldo->total;
		return $response;
	}

	/**
	 * Obtiene el listado de Productores y sus limites
	 */
	public function getProductores($fecha){
		$fechae = explode('-', $fecha);

		$this->load->model('productores_model');
		$data = $this->productores_model->getProductores('f');
		foreach ($data['productores'] as $key => $value) {
			$limite          = $this->getLimiteProductor($value->id_productor, $fechae[0], $fecha);
			$value->saldo    = String::formatoNumero($limite['saldo']);
			$value->limite   = String::formatoNumero($limite['limite']);
			$value->restante = String::formatoNumero( ($limite['limite']-$limite['saldo']) );

			$data['productores'][$key] = $value;
		}
		return $data;
	}

	public function getFacturas($perpage = '40'){
		$sql = '';
		//paginacion
		$params = array(
				'result_items_per_page' => $perpage,
				'result_page' 			=> (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);

		$fecha = explode('-', $_GET['fecha']);

		$query = BDUtil::pagination("
				SELECT f.id_factura, Date(f.fecha) AS fecha, f.serie, f.folio, f.nombre,
                p.nombre_fiscal as productor, f.condicion_pago, f.forma_pago,  f.status, f.total
				FROM productores_facturas AS f
        INNER JOIN productores AS p ON p.id_productor = f.id_productor
				WHERE p.id_productor = ".$_GET['id']." AND YEAR(f.fecha) = '".$fecha[0]."'
				ORDER BY f.serie DESC, f.folio DESC, Date(f.fecha) DESC
				", $params, true);
		$res = $this->db->query($query['query']);

		$response = array(
				'fact' => array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);
		if($res->num_rows() > 0)
			$response['fact'] = $res->result();

		return $response;
	}

	/**
	 * Obtiene la informacion de una factura
	 */
	public function getInfoFactura($id, $info_basic=false)
  {
		$res = $this->db->select("*")->
                      from('productores_facturas')->
                      where("id_factura = '".$id."'")->get();

    if($res->num_rows() > 0)
    {
			$response['info'] = $res->row();
			$response['info']->fecha = substr($response['info']->fecha, 0, 10);
			$res->free_result();
			if($info_basic)
				return $response;

			$this->load->model('productores_model');
			$prov = $this->productores_model->getInfoProductor($response['info']->id_productor, true);
			$response['info']->productor = $prov['info'];

      $res = $this->db->select('fp.id_fac_prod, fp.id_factura, fp.descripcion, fp.taza_iva, fp.cantidad, fp.precio_unitario,
                                fp.importe, fp.importe_iva, fp.total, fp.descuento, fp.retencion, fp.unidad AS unidad')->
                        from('productores_facturas_productos as fp')->
                        where('fp.id_factura = '.$id)->get();

      $response['productos'] = $res->result();

			return $response;
		}
    else
			return false;
	}

	/**
	 * Obtiene el folio de acuerdo a la serie seleccionada
	 */
	public function getFolioSerie($serie, $empresa){
		$res = $this->db->select('folio')->
                      from('productores_facturas')->
                      where("serie = '".$serie."' AND id_productor = ".$empresa)->
                      order_by('folio', 'DESC')->
                      limit(1)->get()->row();

		$folio = (isset($res->folio)? $res->folio: 0)+1;

		$res = $this->db->select('*')->
                      from('productores_series_folios')->
                      where("serie = '".$serie."' AND id_productor = ".$empresa)->
                      limit(1)->get()->row();

		if(is_object($res)){
			if($folio < $res->folio_inicio)
				$folio = $res->folio_inicio;

			$res->folio = $folio;
			$msg = 'ok';

			if($folio > $res->folio_fin || $folio < $res->folio_inicio)
				$msg = "El folio ".$folio." está fuera del rango de folios para la serie ".$serie.". <br>
					Verifique las configuraciones para asignar un nuevo rango de folios";
		}else
			$msg = 'La serie no existe.';

		return array($res, $msg);
	}

  /**
   * Obtiene el folio de acuerdo a la serie seleccionada
   */
  public function get_series_productores($ide){
    $query = $this->db->select('id_serie_folio, id_productor, serie, leyenda')->
                      from('productores_series_folios')->
                      where("id_productor = ".$ide."")->
                      order_by('serie', 'ASC')->get();

    if($query->num_rows() > 0)
    {
      $res = $query->result();
      $msg = 'ok';
    } else
      $msg = 'El productor seleccionada no cuenta con Series y Folios.';

    return array($res, $msg);
  }

	/**
	 * Agrega una factura a la bd
	 */
	public function addFactura(){
		$status = 'pa';

		$this->load->model('productores_model');
		$productor = $this->productores_model->getInfoProductor($this->input->post('did_productor'));

		$prod_dom1 = $productor['info']->calle.' '.$productor['info']->no_exterior.
			($productor['info']->no_interior!=''? '('.$productor['info']->no_interior.')': '').', Col. '.$productor['info']->colonia;
		$prod_dom2 = $productor['info']->municipio.', '.$productor['info']->estado.', C.P. '.$productor['info']->cp;

		$data = array(
			'id_productor'        => $this->input->post('did_productor'),
			// 'id_usuario'       => $this->session->userdata('id_usuario'),
			'serie'               => $this->input->post('dserie'),
			'folio'               => $this->input->post('dfolio'),
			'no_aprobacion'       => $this->input->post('dno_aprobacion'),
			'ano_aprobacion'      => $this->input->post('dano_aprobacion'),
			'fecha'               => $this->input->post('dfecha'),
			
			'subtotal'            => $this->input->post('total_importe'),
			'importe_iva'         => $this->input->post('total_iva'),
			'retencion_iva'       => $this->input->post('total_retiva'),
			'descuento'           => $this->input->post('total_descuento'),
			'total'               => $this->input->post('total_totfac'),
			'total_letra'         => $this->input->post('dttotal_letra'),
			'img_cbb'             => $this->input->post('dimg_cbb'),
			'forma_pago'          => $this->input->post('dforma_pago'),
			'metodo_pago'         => $this->input->post('dmetodo_pago'),
			'metodo_pago_digitos' => $this->input->post('dmetodo_pago_digitos'),
			// 'condicion_pago'   => $this->input->post('dcondicion_pago'),
			// 'plazo_credito'    => $this->input->post('dplazo_credito'),
			'status'              => $status,
			
			'productor_domicilio' => $prod_dom1,
			'productor_ciudad'    => $prod_dom2,
			
			'nombre'              => $this->input->post('dcliente'),
			'rfc'                 => $this->input->post('dcliente_rfc'),
			'domicilio'           => $this->input->post('dcliente_domici'),
			'domicilio2'          => $this->input->post('dcliente_ciudad'),
		);
		$this->db->insert('productores_facturas', $data);
    $id_factura = $this->db->insert_id();

    $data_productos = array();
    foreach ($_POST['prod_did_prod'] as $k => $v)
    {
      if ($_POST['prod_dreten_iva_porcent'][$k] === '0.04')
        $porc_reten = 4;
      elseif ($_POST['prod_dreten_iva_porcent'][$k] === '0.6666')
        $porc_reten = 66.66;
      elseif ($_POST['prod_dreten_iva_porcent'][$k] === '1')
        $porc_reten = 100;
      else
        $porc_reten = 0;

      $importe_iva = (floatval($_POST['prod_dpreciou'][$k]) * floatval($_POST['prod_diva_porcent'])) / 100;
      $descuento   = (floatval($_POST['prod_importe'][$k]) * floatval($_POST['prod_ddescuento_porcent'][$k])) / 100;

      $data_productos[] = array(
                                'id_factura'      => $id_factura,
                                'descripcion'     => $_POST['prod_ddescripcion'][$k],
                                'taza_iva'        => $_POST['prod_diva_porcent'][$k],
                                'cantidad'        => $_POST['prod_dcantidad'][$k],
                                'precio_unitario' => floatval($_POST['prod_dpreciou'][$k]),
                                'importe'         => floatval($_POST['prod_importe'][$k]),
                                'importe_iva'     => floatval($_POST['prod_diva_total'][$k]),
                                'total'           => (floatval($_POST['prod_importe'][$k]) - $descuento) + (floatval($_POST['prod_diva_total'][$k]) - floatval($_POST['prod_dreten_iva_total'][$k])),
                                'descuento'       => $_POST['prod_ddescuento_porcent'][$k],
                                'retencion'       => $porc_reten,
                                'unidad'          => $_POST['prod_dmedida'][$k],
                              );
    }
    $this->db->insert_batch('productores_facturas_productos', $data_productos);
		return array(true, $status, $id_factura);
	}

	/**
	 * Cancela una factura, la elimina
	 */
	public function cancelaFactura($id_factura){
		$this->db->update('productores_facturas', array('status' => 'ca'), "id_factura = '".$id_factura."'");

		return array(true, '');
	}

	/**
	 * obtiene los datos para imprimir una factura
	 * @param  [type] $id_factura [description]
	 * @return [type]             [description]
	 */
	public function printFactura($id_factura){
		$data = $this->productoresfac_model->getInfoFactura($id_factura);

    $res = $this->db->select('*')->
                      from('productores_series_folios')->
                      where("serie = '".$data['info']->serie."' AND id_productor = ".$data['info']->id_productor)->get();
    $data_serie = $res->row();
    $res->free_result();

    $data_productor = $data['info']->productor; //$res->row();

    $this->load->library('mypdf');
    // Creación del objeto de la clase heredada
    $pdf = new MYpdf('P', 'mm', 'Letter');
    $pdf->show_head = false;
    $pdf->AddPage();

    $pdf->Image(APPPATH.'images/factura.jpg', .5, 0, 215, 279);
    if ($data_productor->logo != '')
      $pdf->Image($data_productor->logo, 5, 5, 60, 0); // Logo de la Empresa

    $y = 40;

    $pdf->SetXY(51, 9);
    $pdf->SetFont('Arial','B', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(118, 4, $data_productor->nombre_fiscal, 0, 0, 'C');

    $pdf->SetXY(51, 13);
    $pdf->SetFont('Arial','B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(118, 4, 'R.F.C. '.$data_productor->rfc, 0, 0, 'C');


    $calle = '';
    if ($data_productor->calle !== '')
      $calle = $data_productor->calle;
    if ($data_productor->no_interior !== '')
      $calle .= ' No. '.$data_productor->no_interior;
    if ($data_productor->no_exterior !== '')
      $calle .= ' No. '.$data_productor->no_exterior;

    $colonia = '';
    if ($data_productor->colonia !== '')
      $colonia = ' COL. '.$data_productor->colonia;

    $colmuni = '';
    if($data_productor->cp !== '')
      $colmuni = ' C.P. '.$data_productor->cp;
    if($data_productor->municipio !== '')
      $colmuni .= ' '.$data_productor->municipio;
    if($data_productor->estado !== '')
      $colmuni .= ' '.$data_productor->estado;

    $pdf->SetXY(51, 17);
    $pdf->SetFont('Arial','', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(118, 4, $calle, 0, 0, 'C');

    $pdf->SetXY(51, 21);
    $pdf->SetFont('Arial','', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(118, 4, $colonia, 0, 0, 'C');

    $pdf->SetXY(51, 25);
    $pdf->SetFont('Arial','', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(118, 4, $colmuni, 0, 0, 'C');

    $pdf->SetXY(51, 29);
    $pdf->SetFont('Arial','B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(118, 4, 'TEL/FAX: '.$data_productor->telefono.(($data_productor->celular !== '')?'   CEL:'.$data_productor->celular:''), 0, 0, 'C');

    $www = '';
    if ($data_productor->email !== '')
      $www .= '      Email: '.$data_productor->email;

    $pdf->SetXY(51, 33);
    $pdf->SetFont('Arial','B', 8);
    $pdf->SetTextColor(204, 0, 0);
    $pdf->Cell(118, 4, $www, 0, 0, 'C');

    $pdf->SetXY(170, 15);
    $pdf->SetFont('Arial','', 12);
    $pdf->SetTextColor(204, 0, 0);
    $pdf->Cell(37, 6, ($data['info']->serie!=''? $data['info']->serie: '').$data['info']->folio, 0, 0, 'C');

    $pdf->SetFont('Arial','B', 7);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(170, 26);
    $pdf->SetAligns(array('L'));
    $pdf->SetWidths(array(37));
    $pdf->Row(array('EXPEDIDA EN '.$data_productor->municipio.', '.$data_productor->estado), false, false);

    $pdf->SetXY(158, 40);
    $pdf->SetFont('Arial','', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(48, 6, $data['info']->fecha, 0, 0, 'C');

    $pdf->SetXY(158, 50);
    $pdf->SetFont('Arial','', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(48, 6, $data['info']->forma_pago, 0, 0, 'C');
    /*$pdf->SetXY(182, 50);
    $pdf->Cell(25, 6, $data['info']->serie, 0, 0, 'C');*/
    $pdf->SetXY(158, 58);
    $pdf->Cell(48, 6, ($data['info']->condicion_pago=='cr'? 'CREDITO': 'CONTADO'), 0, 0, 'C');

    $pdf->SetAligns(array('L'));
    $pdf->SetWidths(array(128));

    $pdf->SetXY(28, 36);
    // $pdf->Cell(128, 6, $data['info']->cliente->nombre_fiscal, 0, 0, 'L');
    $pdf->Row(array($data['info']->nombre), false, false);
    $pdf->SetFont('Arial','', 9);
    $pdf->SetXY(28, 43);
    // $pdf->Cell(128, 6, $data['info']->domicilio, 0, 0, 'L');
    $pdf->Row(array($data['info']->domicilio), false, false);

    $pdf->SetXY(28, 52);
    $pdf->Cell(128, 6, $data['info']->domicilio2, 0, 1, 'L');
    $pdf->SetXY(28, 58);
    $pdf->Cell(128, 6, strtoupper($data['info']->rfc), 0, 1, 'L');

    $pdf->SetY(70);
    $aligns = array('C', 'C', 'L', 'C', 'C');
    $widths = array(14, 18, 113, 24, 27);
    $header = array('', '', '', '', '');

    foreach($data['productos'] as $key => $item)
    {
      $band_head = false;
      if($pdf->GetY() >= 200 || $key==0){ //salta de pagina si exede el max
        if($key > 0)
          $pdf->AddPage();
      }

      $pdf->SetFont('Arial','',8);
      $pdf->SetTextColor(0,0,0);

      $datos = array($item->cantidad, ($item->unidad !== NULL ? $item->unidad : $item->unidad2), $item->descripcion,
                    String::formatoNumero($item->precio_unitario),
                    String::formatoNumero($item->importe));

      $pdf->SetX(11);
      $pdf->SetAligns($aligns);
      $pdf->SetWidths($widths);
      $pdf->Row($datos, false, false);
    }

    $y = 214;

    $pdf->SetFont('Arial','', 8.5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(232,232,232);
    $pdf->SetXY(156, $y);
    $pdf->Cell(24, 4, 'SUB-TOTAL', 1, 0, 'L', 1);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetXY(180, $y);
    $pdf->Cell(27, 4, string::formatoNumero($data['info']->subtotal), 1, 0, 'L');


    if (floatval($data['info']->descuento) > 0)
    {
      $y += 4;
      $pdf->SetFont('Arial','', 8.5);
      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetFillColor(232,232,232);
      $pdf->SetXY(156, 218);
      $pdf->Cell(24, 4, 'DESC.', 1, 0, 'L', 1);
      $pdf->SetTextColor(0,0,0);
      $pdf->SetFillColor(255,255,255);
      $pdf->SetXY(180, 218);
      $pdf->Cell(27, 4, string::formatoNumero($data['info']->descuento), 1, 0, 'L', 1);

      $y += 4;
      $pdf->SetFont('Arial','', 8.5);
      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetFillColor(232,232,232);
      $pdf->SetXY(156, 222);
      $pdf->Cell(24, 4, 'SUB-TOTAL', 1, 0, 'L', 1);
      $pdf->SetTextColor(0,0,0);
      $pdf->SetFillColor(255,255,255);
      $pdf->SetXY(180, 222);
      $pdf->Cell(27, 4, string::formatoNumero(floatval($data['info']->subtotal) - floatval($data['info']->descuento)), 1, 0, 'L', 1);
    }

    $y += 4;

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(232,232,232);
    $pdf->SetXY(156, $y);
    $pdf->Cell(24, 4, 'I.V.A.', 1, 0, 'L', 1);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetXY(180, $y);
    if(mb_strtoupper($data['info']->rfc) == 'XAXX010101000')
      $pdf->Cell(27, 4, string::formatoNumero(0), 1, 0, 'L', 1);
    else
      $pdf->Cell(27, 4, string::formatoNumero($data['info']->importe_iva), 1, 0, 'L', 1);

    if (floatval($data['info']->retencion_iva) > 0)
    {
      $y += 4;
      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetFillColor(232,232,232);
      $pdf->SetXY(156, $y);
      $pdf->Cell(24, 4, 'Ret. I.V.A.', 1, 0, 'L', 1);
      $pdf->SetTextColor(0,0,0);
      $pdf->SetFillColor(255,255,255);
      $pdf->SetXY(180, $y);
      $pdf->Cell(27, 4, string::formatoNumero($data['info']->retencion_iva), 1, 0, 'L', 1);
    }


    $y += 4;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(232,232,232);
    $pdf->SetXY(156, $y);
    $pdf->Cell(24, 4, 'TOTAL', 1, 0, 'L', 1);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetXY(180, $y);
    $pdf->Cell(27, 4, string::formatoNumero($data['info']->total), 1, 0, 'L', 1);

    $pdf->SetXY(51, 214);
    $pdf->Cell(105, 24, '', 1, 0, 'L');

    $pdf->SetXY(51, 217);
    $pdf->SetAligns(array('L'));
    $pdf->SetWidths(array(105));
    $pdf->Row(array(strtoupper(string::num2letras($data['info']->total))), false, false);

    $pdf->Image(APPPATH.'images/series_folios/'.$data['info']->img_cbb, 11, 217, 34, 34); // 185

    $pdf->SetFont('Arial','', 8);
    $pdf->SetXY(50, 238);
    $pdf->Cell(155, 6, $data_serie->leyenda1, 0, 0, 'L');
    $pdf->SetXY(50, 241);
    $pdf->SetAligns(array('L'));
    $pdf->SetWidths(array(155));
    $pdf->Row(array($data_serie->leyenda2.' '.$data['info']->ano_aprobacion), false, false); // .' '.$data['info']->ano_aprobacion

    // $pdf->SetXY(50, 229);
    // $pdf->Cell(106, 6, $data['info']->forma_pago, 0, 0, 'L');
    $pdf->SetXY(50, 233);
    $pdf->Cell(106, 6, 'Metodo de pago: '.$data['info']->metodo_pago.', '.$data['info']->metodo_pago_digitos, 0, 0, 'L');

    $pdf->SetFont('Arial','', 10);
    $pdf->SetXY(50, 249);
    $pdf->Cell(155, 6, $data_productor->regimen_fiscal, 0, 0, 'L');

    $pdf->SetXY(10, 252);
    $pdf->Cell(155, 6, "SICOFI ".$data['info']->no_aprobacion, 0, 0, 'L');

    // $pdf->SetXY(170, 258);
    // $pdf->SetFont('Arial','B', 12);
    // $pdf->SetTextColor(204, 0, 0);
    // $pdf->Cell(35, 8, ($data['info']->serie!=''? $data['info']->serie.'-': '').$data['info']->folio, 0, 0, 'C');

    if($data['info']->status == 'ca')
      $pdf->Image(APPPATH.'images/cancelado.png', 3, 9, 215, 270);

    $pdf->Output('Factura.pdf', 'I');
	}





	/**
	 * 					SERIES Y FOLIOS
	 * ***************************************************
	 * Obtiene el listado de series y folios para administrarlos
	 */
	public function getSeriesFolios($per_pag='30'){

		//paginacion
		$params = array(
				'result_items_per_page' => $per_pag,
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);

		$sql = '';
		if($this->input->get('fnombre')!='')
			$sql = " WHERE lower(psf.serie) LIKE '".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."' OR 
				lower(p.nombre_fiscal) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' ";

		$query = BDUtil::pagination("SELECT psf.id_serie_folio, psf.id_productor, psf.serie, psf.no_aprobacion, psf.folio_inicio,
					psf.folio_fin, psf.imagen, psf.leyenda, psf.leyenda1, psf.leyenda2, psf.ano_aprobacion, p.nombre_fiscal AS productor
				FROM productores_series_folios AS psf
					INNER JOIN productores AS p ON p.id_productor = psf.id_productor
				".$sql."
				ORDER BY psf.serie", $params, true);
		$res = $this->db->query($query['query']);

		$data = array(
				'series' 			=> array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);

		if($res->num_rows() > 0)
			$data['series'] = $res->result();

		return $data;
	}

	/**
	 * Obtiene la informacion de una serie/folio
	 * @param unknown_type $id_serie_folio
	 */
	public function getInfoSerieFolio($id_serie_folio = ''){
		$id_serie_folio = ($id_serie_folio != '') ? $id_serie_folio : $this->input->get('id');

		$res = $this->db->select('psf.id_serie_folio, p.id_productor, psf.serie, psf.no_aprobacion, psf.folio_inicio,
				psf.folio_fin, psf.imagen, psf.leyenda, psf.leyenda1, psf.leyenda2, psf.ano_aprobacion, p.nombre_fiscal AS productor')
			->from('productores_series_folios AS psf')
				->join('productores AS p', 'p.id_productor = psf.id_productor', 'inner')
			->where('psf.id_serie_folio', $id_serie_folio)->get()->result();
		return $res;
	}

	/**
	 * Agrega una serie/folio a la base de datos
	 */
	public function addSerieFolio(){
		$path_img = '';
		//valida la imagen
		$upload_res = UploadFiles::uploadImgSerieFolio();

		if(is_array($upload_res)){
			if($upload_res[0] == false)
				return array(false, $upload_res[1]);
			$path_img = $upload_res[1]['file_name']; //APPPATH.'images/series_folios/'.$upload_res[1]['file_name'];
		}

		$id_serie_folio	= BDUtil::getId();
		$data	= array(
				'id_productor'   => $this->input->post('did_productor'),
				'serie'          => strtoupper($this->input->post('fserie')),
				'no_aprobacion'  => $this->input->post('fno_aprobacion'),
				'folio_inicio'   => $this->input->post('ffolio_inicio'),
				'folio_fin'      => $this->input->post('ffolio_fin'),
				'ano_aprobacion' => $this->input->post('fano_aprobacion'),
				'imagen'         => $path_img,
		);

		if($this->input->post('fleyenda')!='')
			$data['leyenda'] = $this->input->post('fleyenda');

		if($this->input->post('fleyenda1')!='')
			$data['leyenda1'] = $this->input->post('fleyenda1');

		if($this->input->post('fleyenda2')!='')
			$data['leyenda2'] = $this->input->post('fleyenda2');

		$this->db->insert('productores_series_folios',$data);
		return array(true);
	}

	/**
	 * Modifica la informacion de un serie/folio
	 * @param unknown_type $id_serie_folio
	 */
	public function editSerieFolio($id_serie_folio=''){
		$id_serie_folio = ($id_serie_folio != '') ? $id_serie_folio : $this->input->get('id');

		$path_img = '';
		//valida la imagen
		$upload_res = UploadFiles::uploadImgSerieFolio();

		if(is_array($upload_res)){
			if($upload_res[0] == false)
				return array(false, $upload_res[1]);
			$path_img = $upload_res[1]['file_name']; //APPPATH.'images/series_folios/'.$upload_res[1]['file_name'];

			/*$old_img = $this->db->select('imagen')->from('productores_series_folios')->where('id_serie_folio',$id_serie_folio)->get()->row()->imagen;

			UploadFiles::deleteFile($old_img);*/
		}

		$data	= array(
				'id_productor'   => $this->input->post('did_productor'),
				'serie'          => strtoupper($this->input->post('fserie')),
				'no_aprobacion'  => $this->input->post('fno_aprobacion'),
				'folio_inicio'   => $this->input->post('ffolio_inicio'),
				'folio_fin'      => $this->input->post('ffolio_fin'),
				'ano_aprobacion' => $this->input->post('fano_aprobacion')
		);

		if($path_img!='')
			$data['imagen'] = $path_img;

		if($this->input->post('fleyenda')!='')
			$data['leyenda'] = $this->input->post('fleyenda');

		if($this->input->post('fleyenda1')!='')
			$data['leyenda1'] = $this->input->post('fleyenda1');

		if($this->input->post('fleyenda2')!='')
			$data['leyenda2'] = $this->input->post('fleyenda2');

		$this->db->update('productores_series_folios',$data, array('id_serie_folio'=>$id_serie_folio));

		return array(true);
	}

	public function exist($table, $sql, $return_res=false){
		$res = $this->db->get_where($table, $sql);
		if($res->num_rows() > 0){
			if($return_res)
				return $res->row();
			return TRUE;
		}
		return FALSE;
	}

  public function getRVP()
  {
    $sql = '';
    //Filtros para buscar
    if($this->input->get('ffecha1') != '' && $this->input->get('ffecha2') != '')
      $sql = " AND Date(f.fecha) BETWEEN '".$this->input->get('ffecha1')."' AND '".$this->input->get('ffecha2')."'";
    elseif($this->input->get('ffecha1') != '')
      $sql = " AND Date(f.fecha) = '".$this->input->get('ffecha1')."'";
    elseif($this->input->get('ffecha2') != '')
      $sql = " AND Date(f.fecha) = '".$this->input->get('ffecha2')."'";

    if ($this->input->get('dfamilia') != '')
      $sql .= " AND p.id_familia = " . $this->input->get('dfamilia');

    // var_dump($sql);exit;

    $query = $this->db->query("SELECT fp.id_producto, SUM(fp.cantidad) AS total_cantidad, SUM(fp.importe) AS total_importe, p.codigo, p.nombre as producto
                                FROM facturas_productos AS fp
                                INNER JOIN facturas AS f ON f.id_factura = fp.id_factura
                                INNER JOIN productos AS p ON p.id_producto = fp.id_producto
                                WHERE f.status != 'ca' $sql
                                GROUP BY fp.id_producto");

    return $query->result();

  }


   /****************************************
   *           REPORTES                   *
   ****************************************/


   public function rvc_pdf()
   {
      $_GET['ffecha1'] = date("Y-m").'-01';
      $_GET['ffecha2'] = date("Y-m-d");

      $data = $this->getFacturas('10000');

      $this->load->library('mypdf');
      // Creación del objeto de la clase heredada
      $pdf = new MYpdf('P', 'mm', 'Letter');
      $pdf->show_head = true;
      $pdf->titulo2 = 'Reporte Ventas Cliente';


      if (!empty($_GET['ffecha1']) && !empty($_GET['ffecha2']))
        $pdf->titulo3 = "Del ".$_GET['ffecha1']." al ".$_GET['ffecha2']."";
      elseif (!empty($_GET['ffecha1']))
        $pdf->titulo3 = "Del ".$_GET['ffecha1'];
      elseif (!empty($_GET['ffecha2']))
        $pdf->titulo3 = "Del ".$_GET['ffecha2'];

      $pdf->AliasNbPages();
      // $links = array('', '', '', '');
      $pdf->SetY(30);
      $aligns = array('C', 'C', 'C', 'C','C', 'C', 'C', 'C');
      $widths = array(20, 25, 13, 51, 30, 25, 18, 22);
      $header = array('Fecha', 'Serie', 'Folio', 'Cliente', 'Empresa', 'Forma de pago', 'Estado', 'Total');
      $total = 0;

      foreach($data['fact'] as $key => $item)
      {
        $band_head = false;
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

        $estado = ($item->status === 'p') ? 'Pendiente' : (($item->status === 'pa') ? 'Pagada' : 'Cancelada');
        $condicion_pago = ($item->condicion_pago === 'co') ? 'Contado' : 'Credito';
        $datos = array($item->fecha, $item->serie, $item->folio, $item->nombre_fiscal, $item->empresa, $condicion_pago, $estado, String::formatoNumero($item->total));
        $total += floatval($item->total);

        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($datos, false);
      }

      $pdf->SetX(6);
      $pdf->SetFont('Arial','B',8);
      $pdf->SetTextColor(255,255,255);
      $pdf->Row(array('', '', '', '', '', '', 'Total:', String::formatoNumero($total)), true);

      $pdf->Output('Reporte_Ventas_Cliente.pdf', 'I');
  }

  public function rvp_pdf()
   {
      $data = $this->getRVP();

      $this->load->library('mypdf');
      // Creación del objeto de la clase heredada
      $pdf = new MYpdf('P', 'mm', 'Letter');
      $pdf->show_head = true;
      $pdf->titulo2 = 'Reporte Ventas Productos';

      if (!empty($_GET['ffecha1']) && !empty($_GET['ffecha2']))
        $pdf->titulo3 = "Del ".$_GET['ffecha1']." al ".$_GET['ffecha2']."";
      elseif (!empty($_GET['ffecha1']))
        $pdf->titulo3 = "Del ".$_GET['ffecha1'];
      elseif (!empty($_GET['ffecha2']))
        $pdf->titulo3 = "Del ".$_GET['ffecha2'];

      $pdf->AliasNbPages();
      // $links = array('', '', '', '');
      $pdf->SetY(30);
      $aligns = array('C', 'C', 'C', 'C','C', 'C', 'C', 'C', 'C');
      $widths = array(20, 120, 20, 44);
      $header = array('Codigo', 'Producto', 'Cantidad', 'Importe');
      $total = 0;

      foreach($data as $key => $item)
      {
        $band_head = false;
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

        $datos = array($item->codigo, $item->producto, $item->total_cantidad, String::formatoNumero($item->total_importe));

        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($datos, false);
      }

      $pdf->Output('Reporte_Ventas_Productos.pdf', 'I');
  }


}