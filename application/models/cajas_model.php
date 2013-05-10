<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cajas_model extends CI_Model {

  function __construct(){
    parent::__construct();
  }

  /**
   * Obtiene listado del total de cajas prestada, entregadas y que debe por
   * productor
   *
   * @param  string $per_pag
   * @return array
   */
  public function get_inventario($per_pag = '9999')
  {
    $sql = '';
    //paginacion
    $params = array(
                    'result_items_per_page' => $per_pag,
                    'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
    );

    if($params['result_page'] % $params['result_items_per_page'] == 0)
            $params['result_page'] = ($params['result_page']/$params['result_items_per_page']);

    //Filtros para buscar
    $_GET['ffecha1'] = $this->input->get('ffecha1')==''? date("Y-m-").'01': $this->input->get('ffecha1');
    $_GET['ffecha2'] = $this->input->get('ffecha2')==''? date("Y-m-d"): $this->input->get('ffecha2');


    $fecha1 = $fecha2 = '';
    if($_GET['ffecha1'] > $_GET['ffecha2'])
    {
      $fecha2 = $_GET['ffecha1'];
      $fecha1 = $_GET['ffecha2'];
    }
    else
    {
      $fecha2 = $_GET['ffecha2'];
      $fecha1 = $_GET['ffecha1'];
    }

    // $sql = " AND DATE(ci.fecha)>='".$_GET['ffecha1']."' AND DATE(ci.fecha)<='".$_GET['ffecha2']."'";
    $sql = " AND DATE(ci.fecha)<='".$fecha2."'";

    $query = BDUtil::pagination("
            SELECT id_productor,
                   productor as nombre,
                   SUM(salidas) AS salidas,
                   SUM(entradas) AS entradas,
                   SUM(salidas) - SUM(entradas) AS total_debe

            FROM
            (
                    SELECT pr.id_productor,
                           SUM(ci.cantidad) AS salidas,
                           0 as entradas,
                           pr.nombre_fiscal AS productor
                    FROM cajas_inventario AS ci
                    INNER JOIN productores AS pr ON pr.id_productor = ci.id_productor
                    WHERE ci.tipo = 's' AND
                          pr.tipo = 'r'
                          {$sql}
                    GROUP BY pr.id_productor

                    UNION ALL

                    SELECT pr.id_productor,
                           0 as salidas,
                           SUM(ci.cantidad) AS entradas,
                           pr.nombre_fiscal AS productor
                    FROM cajas_inventario AS ci
                    INNER JOIN productores AS pr ON pr.id_productor = ci.id_productor
                    WHERE ci.tipo = 'en' AND
                          pr.tipo = 'r'
                          {$sql}
                    GROUP BY pr.id_productor

            ) AS inv
            GROUP BY id_productor, productor
            ORDER BY productor ASC", $params, true);

    $res = $this->db->query($query['query']);

    $response = array(
                    'inventario'     => array(),
                    'total_rows'     => $query['total_rows'],
                    'items_per_page' => $params['result_items_per_page'],
                    'result_page'    => $params['result_page'],
                    'ttotal'         => 0
    );

    if($res->num_rows() > 0)
            $response['inventario'] = $res->result();

    foreach ($query['resultset']->result() as $productor) {
            $response['ttotal'] += $productor->total_debe;
    }

    return $response;
  }

  /**
   * Obtiene listado del total de cajas prestada, entregadas y que debe por
   * productor
   *
   * @param  string $per_pag
   * @return array
   */
  public function get_productor_inventario()
  {
   $sql = '';

    //Filtros para buscar
    $_GET['ffecha1'] = $this->input->get('ffecha1')==''? date("Y-m-").'01': $this->input->get('ffecha1');
    $_GET['ffecha2'] = $this->input->get('ffecha2')==''? date("Y-m-d"): $this->input->get('ffecha2');

    $sql = "AND DATE(ci.fecha)>='".$_GET['ffecha1']."' AND DATE(ci.fecha)<='".$_GET['ffecha2']."'";

    //  Obtiene las entradas, salidas anteriores a la fecha
    $query = $this->db->query("
                SELECT COALESCE(SUM(salidas), 0) AS salidas,
                       COALESCE(SUM(entradas), 0) AS entradas,
                       COALESCE(SUM(salidas), 0) - COALESCE(SUM(entradas), 0) AS total_anterior
                FROM

                  (
                    SELECT id_productor, SUM(cantidad) AS salidas, 0 AS entradas
                    FROM cajas_inventario
                    WHERE id_productor = ".$_GET['id']." AND
                          tipo = 's' AND
                          DATE(fecha) < '".$_GET['ffecha1']."'
                    GROUP BY id_productor

                    UNION ALL

                    SELECT id_productor, 0 AS salidas, SUM(cantidad) AS entradas
                    FROM cajas_inventario
                    WHERE id_productor = ".$_GET['id']." AND
                          tipo = 'en' AND
                          DATE(fecha) < '".$_GET['ffecha1']."'
                    GROUP BY id_productor

                  ) AS ant

                  ");

    $response['anteriores'] = array();
    if ($query->num_rows() > 0) $response['anteriores'] = $query->result();

    $query->free_result();

    // Obtiene las entradas, salidas en el rango de fechas
    $query = $this->db->query("
                SELECT ci.id_inventario,
                       DATE(ci.fecha) AS fecha,
                       ci.concepto,
                       ci.cantidad,
                       ci.chofer,
                       ci.tipo,
                       v.nombre as variedad
                FROM cajas_inventario AS ci
                INNER JOIN variedades AS v ON v.id_variedad = ci.id_variedad
                WHERE id_productor = ".$_GET['id']." {$sql}
                ORDER BY ci.id_inventario, ci.fecha ASC");

    $response['inventario'] = array();
    if($query->num_rows() > 0) $response['inventario'] = $query->result();

    return $response;
  }

  /**
   * Inserta una entrada/salida segun se especifique en el formulario
   * @param  $data array
   * @return array
   */
  public function insert_cajas($data = null)
  {
    if ($data === null)
    {
      $data = array(
        'id_productor' => $this->input->post('did_productor'),
        'id_variedad'  => $this->input->post('dvariedad'),
        'fecha'        => $this->input->post('dfecha'),
        'concepto'     => $this->input->post('dconcepto'),
        'cantidad'     => $this->input->post('dcantidad'),
        'chofer'       => $this->input->post('dchofer'),
        'tipo'         => $this->input->post('dmovimiento')
      );
    }

    $this->db->insert('cajas_inventario', $data);

    return array(true, '', 3);
  }

  /************************ CAJAS ENTRADAS  *****************************/

  /**
   * Obtiene el listado de las entradas
   */
  public function get_cajas_entrada()
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
    if($this->input->get('fnombre') != '')
      $sql = " WHERE (lower(pr.nombre_fiscal) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%')";

    $query = BDUtil::pagination("
        SELECT cr.id_caja, pr.nombre_fiscal AS productor, v.nombre as variedad, cr.fecha, cr.cajas
        FROM cajas_recibidas AS cr
        INNER JOIN productores AS pr ON pr.id_productor = cr.id_productor
        INNER JOIN variedades AS v ON v.id_variedad = cr.id_variedad
        ".$sql."
        ORDER BY cr.id_caja DESC
        ", $params, true);
    $res = $this->db->query($query['query']);

    $response = array(
      'cajas'          => array(),
      'total_rows'     => $query['total_rows'],
      'items_per_page' => $params['result_items_per_page'],
      'result_page'    => $params['result_page']
    );
    $response['cajas'] = $res->result();
    return $response;
  }

  /**
   * Obtiene la informacion de una entrada
   * @param  $id
   * @return array
   */
  public function get_info_entrada($id)
  {
    $res = $this->db
                ->select('cr.*, pr.nombre_fiscal as productor, dh.nombre as dueno')
                ->from('cajas_recibidas as cr')
                ->join('productores as pr', 'pr.id_productor = cr.id_productor', 'inner')
                ->join('duenios_huertas as dh', 'dh.id_dueno = cr.id_dueno', 'inner')
                ->where("cr.id_caja = '".$id."'")
                ->get();

    $res2 = $this->db->select('ct.id_tratamiento as id, tr.nombre, ct.cantidad')
                     ->from('cajas_tratamiento as ct')
                     ->join('tratamientos as tr', 'tr.id_tratamiento = ct.id_tratamiento', 'inner')
                     ->where('id_caja = ' . $id)
                     ->get();

    $res3 = $this->db->select('*')
                     ->from('cajas_recibidas_abonos')
                     ->where('id_caja = ' . $id)
                     ->get();

    if($res->num_rows() > 0)
    {
      $response['info'] = $res->row();
      $response['info']->tratamientos = $res2->result();

      $response['info']->abonos = array();
      if ($res3->num_rows() > 0) $response['info']->abonos = $res3->result();

      $res->free_result();
      $res2->free_result();
      $res3->free_result();

      return $response;
    }
    else
      return false;
  }

  /**
   * Inserta una entrada en la bdd
   * @param  $data
   * @return array
   */
  public function insert_entrada($data = null)
  {
    if ($data === null)
      $data = $this->calcula_kilos_pagar($this->build_array_data_entrada());

    $this->db->insert('cajas_recibidas', $data);

    return array(true, '', 4);
  }

  /**
   * Actualiza una entrada
   * @param  array $data       [Datos de la entrada]
   * @param  array $data_trata [Datos del tratamieno]
   * @return array
   */
  public function update_entrada($id = null, $data = null, $data_trata = null)
  {
    $id = ($id) ? $id : $this->input->get('id');

    if ($data === null)
    {
      $data = $this->calcula_kilos_pagar($this->build_array_data_entrada());

      if ($data_trata === null)
      {

        // $_POST['did_tratamiento'] es un array de ids de tratamientos
        $data_trata = array();
        if (isset($_POST['did_tratamiento']))
        {
          foreach ($_POST['did_tratamiento'] as $key => $tratamiento) {
            if ($tratamiento !== '' && $_POST['dcantidad_trata'][$key] !== '')
            {
              $data_trata[] = array(
                'id_caja'        => $this->input->get('id'),
                'id_tratamiento' => $tratamiento,
                'cantidad'       => $_POST['dcantidad_trata'][$key],
              );
            }
          }
        }

      }
    }

    $this->db->update('cajas_recibidas', $data, array('id_caja' => $id)); // Actualiza los datos de la entrada

    if ($data_trata !== null)
    {
      $this->db->delete('cajas_tratamiento', array('id_caja' => $id)); // Elimina el tratamiento de la caja a actualizar

      if (count($data_trata) > 0) $this->db->insert_batch('cajas_tratamiento', $data_trata); // Inserta el nuevo tratamiento
    }
    return array(true, '', 5);
  }

  /**
   * Funcion para construir el array que contendra los datos que se insertaran
   * o actualizaran en la bdd, este funcion es util para las funciones
   * insert_entrada y update_entrada.
   * @return array
   */
  private function build_array_data_entrada()
  {
    return array(
        'id_dueno'            => $this->input->post('did_dueno'),
        'id_productor'        => $this->input->post('did_productor'),
        'id_variedad'         => $this->input->post('dvariedad'),
        'fecha'               => $this->input->post('dfecha'),
        'certificado_tarjeta' => $this->input->post('dcertificado_tarjeta'),
        'codigo_huerta'       => $this->input->post('dcodigo_huerta'),
        'no_lote'             => $this->input->post('dno_lote'),
        'cajas'               => $this->input->post('dcajas'),
        'cajas_rezaga'        => $this->input->post('dcajas_rezaga'),
        'no_ticket'           => $this->input->post('dno_ticket'),
        'kilos'               => $this->input->post('dkilos'),
        'precio'              => $this->input->post('dprecio'),
        'es_organico'         => $this->input->post('des_organico'),
        'unidad_transporte'   => $this->input->post('dunidad_transporte'),
        'dueno_carga'         => $this->input->post('ddueno_carga'),
        'observaciones'       => $this->input->post('dobservaciones')
                  );
  }

  /**
   * Elimina entradas de la bdd
   * @param string $id
   *
   * @return  array()
   */
  public function delete_entrada ($id = null)
  {
    $id = $id ? $id : $this->input->get('id');

    $this->db->delete('cajas_recibidas', array('id_caja'=>$id));

    return array(true, '', 6);
  }


  /**************************** UTIL ************************************/

  /**
   * Esta funcion realiza los calcula para obtener el total de kilos rezaga
   * y el total de kilos/cajas a pagar
   * @param  string $tipo_pago [Tipo de pago k=kilos, c=cajas]
   * @param  array  $data      [array con la datos de la entrada]
   * @return array
   */
  private function calcula_kilos_pagar($data = array(), $tipo_pago = null)
  {
    // Si el tipo de pago no fue especificado en el parametro de la funcion
    // entonces asume que el array $data contiene el id de la variedad
    if ($tipo_pago === null)
    {
      $query = $this->db->query("SELECT tipo_pago
                                   FROM variedades
                                   WHERE id_variedad = ". $data['id_variedad']);
      $variedad = $query->result();
      $tipo_pago = $variedad[0]->tipo_pago;
    }

    if ($tipo_pago === 'c') // si el tipo de pago es por cajas
    {
      // Valida que estos campos necesarios no esten vacios para poder hacer los calculos
      if ($data['cajas'] !== '' && $data['cajas_rezaga'] !== '' && $data['precio'] !== '')
      {
        $data['total_pagar_kc'] = floatval($data['cajas']) - floatval($data['cajas_rezaga']);
      }
    }
    else // si el tipo de pago es por kilos
    {
      // Valida que estos campos necesarios no esten vacios para poder hacer los calculos
      if ($data['cajas'] !== '' && $data['cajas_rezaga'] !== '' && $data['kilos'] !== '' && $data['precio'] !== '')
      {
        $data['kilos_rezaga']   = round((floatval($data['cajas_rezaga']) * floatval($data['kilos'])) / floatval($data['cajas']));
        $data['total_pagar_kc'] = floatval($data['kilos']) -  floatval($data['kilos_rezaga']);
      }
    }

    return $data;
  }

  /**
   * Obtiene las variedades existentes de la base de datos
   * @return array
   */
  public function get_variedades()
  {
    $query = $this->db->query("SELECT *
                               FROM variedades
                               WHERE status = 'ac'");

    return $query->result();
  }

  /************************ AJAX  *****************************/

  /**
   * Obtiene el listado de productores
   */
  public function get_productores_ajax()
  {
    $sql = '';
    $res = $this->db->query("
        SELECT id_productor, nombre_fiscal, rfc, calle, no_exterior, no_interior, colonia, municipio, estado, cp, telefono
        FROM productores
        WHERE status = 1 AND tipo = 'r' AND lower(nombre_fiscal) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'
        ORDER BY nombre_fiscal ASC
        LIMIT 20");

    $response = array();
    if($res->num_rows() > 0)
    {
      foreach($res->result() as $itm)
      {
        $response[] = array(
            'id'    => $itm->id_productor,
            'label' => $itm->nombre_fiscal,
            'value' => $itm->nombre_fiscal,
            'item'  => $itm,
        );
      }
    }

    return $response;
  }

  /*************** FUNCIONES PARA GENERAR PDF'S Y XLS'S  *****************/

  /**
   * Visualiza/Descarga el PDF de la relacion de cajas entregasa y salidas de
   * un productor en un rango de fechas
   *
   * @return void
   */
  public function productor_inventario_pdf()
  {
    $this->load->model('productores_model');

    $inv = $this->get_productor_inventario();
    $info = $this->productores_model->getInfoProductor($_GET['id']);

    $this->load->library('mypdf');
    // Creación del objeto de la clase heredada
    $pdf = new MYpdf('L', 'mm', 'Letter');
    $pdf->titulo2 = "Relación de Cajas Entradas y Salidas ";
    $pdf->titulo2 .= 'Productor '.$info['info']->nombre_fiscal;
    $pdf->titulo3 = 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2')."\n";

    $pdf->AliasNbPages();
    //$pdf->AddPage();
    $pdf->SetFont('Arial','', 8);

    $aligns = array('C', 'C', 'C', 'C', 'C', 'C', 'C');
    $widths = array(20, 25, 90, 20, 20, 30, 60);
    $header = array('Fecha', 'No. Movimiento', 'Concepto', 'Salidas', 'Entradas', 'Variedad', 'Chofer');

    $total_salidas  = 0;
    $total_entradas = 0;
    $total_debe     = 0;

    $bad_saldo_ante = true;
    if(isset($inv['anteriores'][0]->total_anterior)) //se suma a los totales de las cajas anteriores a la fecha
    {
      $total_salidas  += $inv['anteriores'][0]->salidas;
      $total_entradas += $inv['anteriores'][0]->entradas;
      $total_debe     += $inv['anteriores'][0]->total_anterior;
    }
    else
    {
      $inv['anteriores'][] = new stdClass();
      $inv['anteriores'][0]->salidas        = 0;
      $inv['anteriores'][0]->entradas       = 0;
      $inv['anteriores'][0]->total_anterior = 0;
    }

    $inv['anteriores'][0]->concepto = 'Total anterior a '.$_GET['ffecha1'];

    if (count($inv['inventario']) === 0)
    {
      $inv['inventario'][] = new stdClass();

      $inv['inventario'][0]->fecha         = '';
      $inv['inventario'][0]->id_inventario = '';
      $inv['inventario'][0]->concepto      = '';
      $inv['inventario'][0]->salida        = 0;
      $inv['inventario'][0]->entrada       = 0;
      $inv['inventario'][0]->variedad      = '';
      $inv['inventario'][0]->chofer        = '';
      $inv['inventario'][0]->tipo          = '';
      $inv['inventario'][0]->cantidad      = 0;
    }

    foreach($inv['inventario'] as $key => $item)
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

      if($bad_saldo_ante)
      {
        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row(array('', '', $inv['anteriores'][0]->concepto,
                        $inv['anteriores'][0]->salidas,
                        $inv['anteriores'][0]->entradas,
                        $inv['anteriores'][0]->total_anterior,
                        ''), false);

        $bad_saldo_ante = false;
      }

      $salida = $entrada = 0;

      if ($item->tipo === 's') $total_salidas  += $salida = $item->cantidad;
      else $total_entradas += $entrada = $item->cantidad;

      if ($item->id_inventario !== '')
      {
        $datos = array($item->fecha,
                       $item->id_inventario,
                       $item->concepto,
                       $salida,
                       $entrada,
                       $item->variedad,
                       $item->chofer);

        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($datos, false);
      }
    }

    $pdf->SetX(6);
    $pdf->SetFont('Arial','B',8);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetWidths(array(135, 20, 20, 30));
    $pdf->Row(array('Totales:',
                      $total_salidas,
                      $total_entradas,
                      floatval($total_salidas) - floatval($total_entradas)), true);

    $pdf->Output('relacion_productor.pdf', 'I');
  }

  /**
   * Descarga un archivo EXCEL con la relacion de cajas de salidas y entregadas
   * de un productor en un rango de fechas
   *
   * @return void
   */
  public function productor_inventario_xls()
  {
    $this->load->model('productores_model');

    $inv = $this->get_productor_inventario();
    $info = $this->productores_model->getInfoProductor($_GET['id']);

    $this->load->library('myexcel');
    $xls = new myexcel();

    $worksheet =& $xls->workbook->addWorksheet();

    $xls->titulo2 = 'Relacion de Cajas Entradas y Salidas';
    $xls->titulo3 = 'Productor '.$info['info']->nombre_fiscal;
    $xls->titulo4 = 'Del: '.$this->input->get('ffecha1').' Al '.$this->input->get('ffecha2')."\n";

    foreach ($inv['inventario'] as $key => $mov)
    {
      $mov->salidas = 0;
      $mov->entradas = 0;
      if ($mov->tipo === 's') $mov->salidas = floatval($mov->cantidad);
      if ($mov->tipo === 'en') $mov->entradas = floatval($mov->cantidad);
    }

    if(!isset($inv['anteriores'][0]->total_anterior)){
            $inv['anterior'] = new stdClass();
            $inv['anterior']->salidas = 0;
            $inv['anterior']->entradas = 0;
            $inv['anterior']->total_debe = 0;
    }else{
            $inv['anterior'] = new stdClass();
            $inv['anterior']->salidas = $inv['anteriores'][0]->salidas;
            $inv['anterior']->entradas = $inv['anteriores'][0]->entradas;
            $inv['anterior']->variedad = $inv['anteriores'][0]->total_anterior;
    }

    $inv['anterior']->concepto = 'SALDE ANTERIOR A ' . $_GET['ffecha1'];
    $inv['anterior']->fecha = $inv['anterior']->id_inventario = '';
    $inv['anterior']->chofer = '';

    array_unshift($inv['inventario'], $inv['anterior']);

    $row=0;
    // //Header
    $xls->excelHead($worksheet, $row, 8, array(
                    array($xls->titulo2, 'format_title2'),
                    array($xls->titulo3, 'format_title3'),
                    array($xls->titulo4, 'format_title3')
    ));

    $data = $inv['inventario'];

    $row +=3;
    $xls->excelContent($worksheet, $row, $data, array(
                    'head' => array('Fecha', 'No. Movimiento', 'Concepto', 'Salidas', 'Entradas', 'Variedad', 'Chofer'),
                    'conte' => array(
                                    array('name' => 'fecha', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'id_inventario', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'concepto', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'salidas', 'format' => 'format4', 'sum' => 0),
                                    array('name' => 'entradas', 'format' => 'format4', 'sum' => 0),
                                    array('name' => 'variedad', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'chofer', 'format' => 'format4', 'sum' => -1))
    ));

    $xls->workbook->send('relacion_productor.xls');
    $xls->workbook->close();
  }

}

/* End of file cajas_model.php */
/* Location: ./application/models/cajas_model.php */