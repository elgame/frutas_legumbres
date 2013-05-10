<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuentas_pagar_model extends CI_Model {

  function __construct(){
    parent::__construct();
  }

  /**
   * Obtiene listado del total que se le debe por productor tomando en cuenta
   * los abonos y las cajas entregadas
   *
   * @param  string $per_pag
   * @return array
   */
  public function get_cuentas_pagar($per_pag = '9999', $order='nombre_fiscal ASC')
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

    $fecha = $_GET['ffecha1'] > $_GET['ffecha2'] ? $_GET['ffecha1'] : $_GET['ffecha2'];

    $sql = " AND DATE(cr.fecha)>='".$_GET['ffecha1']."' AND DATE(cr.fecha)<='".$_GET['ffecha2']."'";

    $query = BDUtil::pagination("
            SELECT id_productor,
                   nombre_fiscal AS nombre,
                   COALESCE(SUM(total_entradas), 0) AS total_entradas,
                   COALESCE(SUM(total_abonos),0) AS total_abonos,
                   COALESCE(COALESCE(SUM(total_entradas), 0) - COALESCE(SUM(total_abonos),0)) AS total_pagar
            FROM
              (

                SELECT p.id_productor,
                       p.nombre_fiscal,
                       SUM(cr.total_pagar_kc * cr.precio) AS total_entradas,
                       cat.abonos as total_abonos
                FROM productores AS p
                INNER JOIN cajas_recibidas AS cr ON cr.id_productor = p.id_productor
                LEFT JOIN
                (

                    SELECT cr.id_productor,
                          SUM(cra.monto) as abonos
                    FROM cajas_recibidas AS cr
                    INNER JOIN cajas_recibidas_abonos AS cra ON cra.id_caja = cr.id_caja
                    WHERE DATE(cra.fecha) <= '".$fecha."' AND
                          DATE(cr.fecha) <= '".$fecha."'
                    GROUP BY cr.id_productor

                ) AS cat ON cat.id_productor = p.id_productor
                WHERE p.status = 'ac' AND
                      p.tipo = 'r' AND
                      DATE(cr.fecha) <= '".$fecha."'
                GROUP BY id_productor, nombre_fiscal, cat.abonos

             ) AS subquery

            GROUP BY id_productor, nombre_fiscal
            ORDER BY ".$order." ", $params, true);

    $res = $this->db->query($query['query']);

    $response = array(
              'cuenta_pagar'      => array(),
              'total_rows'        => $query['total_rows'],
              'items_per_page'    => $params['result_items_per_page'],
              'result_page'       => $params['result_page'],
              'total_pagar_todos' => 0
    );

    if($res->num_rows() > 0)
      $response['cuenta_pagar'] = $res->result();

    foreach ($query['resultset']->result() as $productor) {
      $response['total_pagar_todos'] += $productor->total_pagar;
    }

    return $response;
  }

  /**
   * Obtiene el listado de cuentas por pagar 2 nivel
   * @param  string $per_pag
   * @return array
   */
  public function get_cuentas_pagar_productor()
  {
    $sql = '';

    //Filtros para buscar
    $_GET['ffecha1'] = $this->input->get('ffecha1')==''? date("Y-m-").'01': $this->input->get('ffecha1');
    $_GET['ffecha2'] = $this->input->get('ffecha2')==''? date("Y-m-d"): $this->input->get('ffecha2');

    $fecha = $_GET['ffecha1'] > $_GET['ffecha2'] ? $_GET['ffecha1'] : $_GET['ffecha2'];
    $sql = " AND DATE(cr.fecha)>='".$_GET['ffecha1']."' AND DATE(cr.fecha)<='".$_GET['ffecha2']."'";

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

    // Obtiene los saldos anteriores
    $query = $this->db->query(
               "SELECT id_productor,
                   nombre_fiscal AS nombre,
                   COALESCE(SUM(total_entradas), 0) AS total_entradas,
                   COALESCE(SUM(total_abonos),0) AS total_abonos,
                   COALESCE(COALESCE(SUM(total_entradas), 0) - COALESCE(SUM(total_abonos),0)) AS total_pagar
                FROM
                  (

                    SELECT p.id_productor,
                           p.nombre_fiscal,
                           SUM(cr.total_pagar_kc * cr.precio) AS total_entradas,
                           SUM(cat.abonos) AS total_abonos

                    FROM productores AS p
                    INNER JOIN cajas_recibidas AS cr ON cr.id_productor = p.id_productor
                    LEFT JOIN
                    (

                        SELECT cr.id_productor,
                               cr.id_caja,
                               SUM(cra.monto) as abonos
                        FROM cajas_recibidas AS cr
                        INNER JOIN cajas_recibidas_abonos AS cra ON cra.id_caja = cr.id_caja
                        WHERE DATE(cra.fecha) <= '{$fecha2}' AND
                              DATE(cr.fecha) <= '{$fecha2}' AND
                              cr.id_productor = {$_GET['id']}
                        GROUP BY cr.id_productor, cr.id_caja

                    ) AS cat ON cat.id_productor = p.id_productor AND cr.id_caja = cat.id_caja
                    WHERE p.status = 'ac' AND
                          p.tipo = 'r' AND
                          p.id_productor = {$_GET['id']} AND
                          DATE(cr.fecha) < '{$fecha1}'
                    GROUP BY id_productor, nombre_fiscal, cat.abonos

                 ) AS subquery

                GROUP BY id_productor
             ");

    $response['anterior'] = array();
    if($query->num_rows() > 0)
      $response['anterior'] = $query->result();


    // Cajas y sus abonos en el rango de fechas seleccionado
    $query = $this->db->query("SELECT cr.id_caja,
                                      DATE(cr.fecha) AS fecha,
                                      cr.no_ticket,
                                      cr.cajas,
                                      cr.cajas_rezaga,
                                      cr.kilos,
                                      cr.kilos_rezaga,
                                      cr.total_pagar_kc,
                                      cr.precio,
                                      (cr.total_pagar_kc * cr.precio) AS importe,
                                      tab.abonos,
                                      ((cr.total_pagar_kc * cr.precio) - COALESCE(tab.abonos, 0)) AS saldo,
                                      v.nombre as variedad,
                                      cr.observaciones
                               FROM cajas_recibidas AS cr
                               INNER JOIN variedades AS v ON v.id_variedad = cr.id_variedad

                               LEFT JOIN (

                                  SELECT cra.id_caja,
                                         SUM(cra.monto) as abonos
                                  FROM cajas_recibidas_abonos AS cra
                                  WHERE DATE(cra.fecha) <= '{$fecha2}'
                                  GROUP BY cra.id_caja

                               ) AS tab ON tab.id_caja = cr.id_caja

                               WHERE cr.id_productor = {$_GET['id']} AND
                                     (Date(cr.fecha) >= '{$fecha1}' AND
                                      Date(cr.fecha) <= '{$fecha2}')

                               ORDER BY fecha ASC");

    $response['cajas'] = array();
    if($query->num_rows() > 0)
      $response['cajas'] = $query->result();

    return $response;
  }

  /**
   * Muestra el detalle de una entrada o caja
   *
   * @return array
   */
  public function detalle()
  {
    $sql = '';

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

    $sql = $sql2 = '';
    if($this->input->get('ftipo')=='pv'){
            $sql = " AND (Date('".$fecha2."'::timestamp with time zone)-Date(c.fecha)) > c.plazo_credito";
            $sql2 = 'WHERE saldo > 0';
    }

    //Obtenemos los abonos
    $query = $this->db->query("SELECT id_abono,
                                      Date(fecha) AS fecha,
                                      monto AS abono
                              FROM cajas_recibidas_abonos
                              WHERE id_caja = {$_GET['idc']} AND
                                    Date(fecha) <= '{$fecha2}'
                              ORDER BY fecha ASC");

    $response = array(
      'abonos' => array(),
      'fecha1'  => $fecha1
    );

    if($query->num_rows() > 0)
      $response['abonos'] = $query->result();

    return $response;
  }

  /*************** FUNCIONES PARA GENERAR PDF'S Y XLS'S  *****************/

  /**
   * Visualiza/Descarga el PDF de la relacion de entregas
   *
   * @return void
   */
  public function cpp_pdf()
  {
    $this->load->model('productores_model');

    $cpp  = $this->get_cuentas_pagar_productor();
    $info = $this->productores_model->getInfoProductor($_GET['id']);

    $this->load->library('mypdf');
    // Creación del objeto de la clase heredada
    $pdf = new MYpdf('L', 'mm', 'Letter');
    $pdf->titulo2 = "Relación de Cajas Entregadas ";
    $pdf->titulo2 .= 'Productor '.$info['info']->nombre_fiscal;
    $pdf->titulo3 = 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2')."\n";

    $pdf->AliasNbPages();
    //$pdf->AddPage();
    $pdf->SetFont('Arial','', 8);

    $aligns = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
    $widths = array(20, 15, 15, 15, 17, 18, 20, 25, 25, 25, 25, 25, 23);
    $header = array('Fecha', '# Ticket', '# Cajas', 'Cajas Rezaga', 'Kilos Recibidos',
                    'Promedio', 'Kilos Rezaga', 'Total Kilos/Cajas Pagar', 'Precio x Kilo/Caja',
                    'Importe', 'Abonos', 'Saldo', 'Tipo');

    $ttotal_importe = 0;
    $ttotal_abonos  = 0;
    $ttotal_saldo   = 0;

    $bad_saldo_ante = true;
    if(isset($cpp['anterior'][0]->total_pagar)) //se suma a los totales de las cajas anteriores a la fecha
    {
      $ttotal_importe += $cpp['anterior'][0]->total_entradas;
      $ttotal_abonos  += $cpp['anterior'][0]->total_abonos;
      $ttotal_saldo   += $cpp['anterior'][0]->total_pagar;
    }
    else
    {
      $cpp['anterior'][] = new stdClass();
      $cpp['anterior'][0]->total_entradas = 0;
      $cpp['anterior'][0]->total_abonos = 0;
      $cpp['anterior'][0]->total_pagar = 0;
    }

    $cpp['anterior'][0]->concepto = 'ANTERIORES A '.$_GET['ffecha1'];

    if (count($cpp['cajas']) === 0)
      $cpp['cajas'][] = new stdClass();

    $ttotal_cajas           = 0;
    $ttotal_cajas_rezaga    = 0;
    $ttotal_kilos_recibidos = 0;
    $ttotal_kilos_rezaga    = 0;
    $ttotal_kilos_pagar     = 0;

    foreach($cpp['cajas'] as $key => $item)
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
        $pdf->Row(array('', '', '', '', '', '', '', '', $cpp['anterior'][0]->concepto,
                        String::formatoNumero($cpp['anterior'][0]->total_entradas),
                        String::formatoNumero($cpp['anterior'][0]->total_abonos),
                        String::formatoNumero($cpp['anterior'][0]->total_pagar),
                        ''), false);

        $bad_saldo_ante = false;
      }

      if (isset($item->fecha))
      {

        $ttotal_cajas           += $item->cajas;
        $ttotal_cajas_rezaga    += $item->cajas_rezaga;
        $ttotal_kilos_recibidos += $item->kilos;
        $ttotal_kilos_rezaga    += $item->kilos_rezaga;
        $ttotal_kilos_pagar     += $item->total_pagar_kc;

        $ttotal_importe         += $item->importe;
        $ttotal_abonos          += $item->abonos;
        $ttotal_saldo           += $item->saldo;

        $promedio = ($item->variedad !== 'ATAULFO') ? round(floatval($item->kilos) / floatval($item->cajas), 2) : 0;

        $datos = array($item->fecha,
                       $item->no_ticket,
                       $item->cajas,
                       $item->cajas_rezaga,
                       $item->kilos,
                       $promedio,
                       $item->kilos_rezaga,
                       $item->total_pagar_kc,
                       String::formatoNumero($item->precio),
                       String::formatoNumero($item->importe),
                       String::formatoNumero($item->abonos),
                       String::formatoNumero($item->saldo),
                       $item->variedad);

        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($datos, false);
      }

    }

    $pdf->SetX(6);
    $pdf->SetFont('Arial','B',8);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetWidths(array(20, 15, 15, 15, 17, 18, 20, 25, 25, 25, 25, 25, 23));
    $pdf->Row(array('TOTALES',
                    '',
                    $ttotal_cajas,
                    $ttotal_cajas_rezaga,
                    $ttotal_kilos_recibidos,
                    '',
                    $ttotal_kilos_rezaga,
                    $ttotal_kilos_pagar,
                    '',
                    String::formatoNumero($ttotal_importe),
                    String::formatoNumero($ttotal_abonos),
                    String::formatoNumero($ttotal_saldo),
                    ''), true);

    $pdf->Output('PRODUCTOR_'.strtoupper(str_replace(' ', '_', $info['info']->nombre_fiscal)).'.pdf', 'I');
  }

  /**
   * Descarga un archivo EXCEL con la relacion de cajas entredas
   *
   * @return void
   */
  public function cpp_xls()
  {
    $this->load->model('productores_model');

    $cpp  = $this->get_cuentas_pagar_productor();
    $info = $this->productores_model->getInfoProductor($_GET['id']);

    $this->load->library('myexcel');
    $xls = new myexcel();

    $worksheet =& $xls->workbook->addWorksheet();

    $xls->titulo2 = 'Relacion de Cajas Entradas';
    $xls->titulo3 = 'Productor '.$info['info']->nombre_fiscal;
    $xls->titulo4 = 'Del: '.$this->input->get('ffecha1').' Al '.$this->input->get('ffecha2')."\n";

    foreach ($cpp['cajas'] as $key => $caja)
      $caja->promedio = ($caja->variedad !== 'ATAULFO') ? round(floatval($caja->kilos) / floatval($caja->cajas), 2) : 0;

    if(!isset($cpp['anterior'][0]->total_pagar))
    {
      $cpp['anterior'][] = new stdClass();
      $cpp['anterior'][0]->importe = 0;
      $cpp['anterior'][0]->abonos  = 0;
      $cpp['anterior'][0]->saldo   = 0;
    }
    else
    {
      $cpp['anterior'][0]->importe = $cpp['anterior'][0]->total_entradas;
      $cpp['anterior'][0]->abonos  = $cpp['anterior'][0]->total_abonos;
      $cpp['anterior'][0]->saldo   = $cpp['anterior'][0]->total_pagar;
    }

    $cpp['anterior'][0]->fecha        = $cpp['anterior'][0]->no_ticket = '';
    $cpp['anterior'][0]->cajas        = $cpp['anterior'][0]->cajas_rezaga = 0;
    $cpp['anterior'][0]->kilos        = $cpp['anterior'][0]->promedio = 0;
    $cpp['anterior'][0]->kilos_rezaga = $cpp['anterior'][0]->total_pagar_kc = 0;

    $cpp['anterior'][0]->precio = 'SALDO ANTERIOR A ' . $_GET['ffecha1'];

    $cpp['anterior'][0]->variedad     = '';


    array_unshift($cpp['cajas'], $cpp['anterior'][0]);

    $row=0;
    // //Header
    $xls->excelHead($worksheet, $row, 8, array(
                    array($xls->titulo2, 'format_title2'),
                    array($xls->titulo3, 'format_title3'),
                    array($xls->titulo4, 'format_title3')
    ));

    $data = $cpp['cajas'];

    $row +=3;

    $xls->excelContent($worksheet, $row, $data, array(
                    'head' => array('Fecha', '# Ticket', '# Cajas', 'Cajas Rezaga', 'Kilos Recibidos', 'Promedio', 'Kilos Rezaga', 'Total Kilos/Cajas Pagar', 'Precio x Kilo/Caja', 'Importe', 'Abonos', 'Saldo', 'Tipo'),
                    'conte' => array(
                                    array('name' => 'fecha', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'no_ticket', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'cajas', 'format' => 'format4', 'sum' => 0),
                                    array('name' => 'cajas_rezaga', 'format' => 'format4', 'sum' => 0),
                                    array('name' => 'kilos', 'format' => 'format4', 'sum' => 0),
                                    array('name' => 'promedio', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'kilos_rezaga', 'format' => 'format4', 'sum' => 0),
                                    array('name' => 'total_pagar_kc', 'format' => 'format4', 'sum' => 0),
                                    array('name' => 'precio', 'format' => 'format4', 'sum' => -1),
                                    array('name' => 'importe', 'format' => 'format4', 'sum' => 0),
                                    array('name' => 'abonos', 'format' => 'format4', 'sum' => 0),
                                    array('name' => 'saldo', 'format' => 'format4', 'sum' => 0),
                                    array('name' => 'variedad', 'format' => 'format4', 'sum' => -1))
    ));

    $xls->workbook->send('relacion_productor.xls');
    $xls->workbook->close();
  }

  /**
   * Visualiza/Descarga el PDF de la relacion de
   *
   * @return void
   */
  public function detalle_pdf()
  {
    $this->load->model('productores_model');
    $this->load->model('cajas_model');

    $entrega = $this->cajas_model->get_info_entrada($_GET['idc']);
    $productor = $this->productores_model->getInfoProductor($_GET['id']);
    $abonos = $this->detalle();

    $this->load->library('mypdf');
    // Creación del objeto de la clase heredada
    $pdf = new MYpdf('P', 'mm', 'Letter');
    $pdf->titulo2 = 'Detalle Entrega ' . $abonos['fecha1'];
    $pdf->titulo3 = $productor['info']->nombre_fiscal . "\n";
    $pdf->titulo3 .= 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2');
    $pdf->AliasNbPages();
    //$pdf->AddPage();
    $pdf->SetFont('Arial','',8);

    $aligns = array('C', 'C', 'C');
    $widths = array(55, 73, 73);
    $header = array('Fecha', 'Abono', 'Saldo');

    $total_abono = 0;
    $total_entrega = floatval($entrega['info']->total_pagar_kc) * floatval($entrega['info']->precio);
    $total_saldo = $total_entrega;

    $bad_cargot = true;

    foreach($abonos['abonos'] as $key => $item)
    {
      $total_abono += floatval($item->abono);
      $total_saldo -= floatval($item->abono);

      if($pdf->GetY() >= $pdf->limiteY || $key==0) //salta de pagina si exede el max
      {
        $pdf->AddPage();

        $pdf->SetFont('Arial','B',8);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFillColor(160,160,160);
        $pdf->SetX(6);

        if($bad_cargot)
        {
          $pdf->SetX(6);
          $pdf->SetAligns(array('R'));
          $pdf->SetWidths(array(201));
          $pdf->Row(array('Total: '.String::formatoNumero($total_entrega)), true);
          $bad_cargot = false;
        }

        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($header, true);
      }

      $pdf->SetFont('Arial','',8);
      $pdf->SetTextColor(0,0,0);
      $datos = array($item->fecha,
                     String::formatoNumero($item->abono),
                     String::formatoNumero($total_saldo));

      $pdf->SetX(6);
      $pdf->SetAligns($aligns);
      $pdf->SetWidths($widths);
      $pdf->Row($datos, false);
    }

    $pdf->SetX(6);
    $pdf->SetFont('Arial','B',8);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetWidths(array(55, 73, 73));
    $pdf->Row(array('Totales:',
                    String::formatoNumero($total_abono),
                    String::formatoNumero($total_saldo)), true);

    $pdf->Output('DETALLE_FACTURA.pdf', 'I');
  }

}