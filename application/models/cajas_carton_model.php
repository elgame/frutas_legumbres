<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cajas_carton_model extends CI_Model {

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Obtiene listado del total de cajas por marca.
   *
   * @param  string $per_pag
   * @return array
   */
  public function get_inventario($per_pag = '9999', $order='marca ASC')
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
    $sql = " AND DATE(cic.fecha)<='".$fecha2."'";

    if ( ! empty($_GET['ide']))
      $sql .= " AND cic.id_empacador = {$_GET['ide']}";

    $query = BDUtil::pagination("
            SELECT id_marca,
                   marca,
                   SUM(salidas) AS salidas,
                   SUM(entradas) AS entradas,
                   SUM(salidas) - SUM(entradas) AS total_debe

            FROM
            (
                    SELECT m.id_marca,
                           SUM(cic.cantidad) AS salidas,
                           0 as entradas,
                           m.nombre AS marca
                    FROM cajas_inventario_carton AS cic
                    INNER JOIN marcas AS m ON m.id_marca = cic.id_marca
                    WHERE cic.tipo = 's'
                          {$sql}
                    GROUP BY m.id_marca

                    UNION ALL

                    SELECT m.id_marca,
                           0 as salidas,
                           SUM(cic.cantidad) AS entradas,
                           m.nombre AS marca
                    FROM cajas_inventario_carton AS cic
                    INNER JOIN marcas AS m ON m.id_marca = cic.id_marca
                    WHERE cic.tipo = 'en'
                          {$sql}
                    GROUP BY m.id_marca

            ) AS inv
            GROUP BY id_marca, marca
            ORDER BY ".$order, $params, true);

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

    foreach ($query['resultset']->result() as $marca) {
            $response['ttotal'] += $marca->total_debe;
    }

    return $response;
  }

  /**
   * Obtiene listado del total de cajas salidas, entradas por marca.
   *
   * @param  string $per_pag
   * @return array
   */
  public function get_marca_inventario()
  {
   $sql = '';

    //Filtros para buscar
    $_GET['ffecha1'] = $this->input->get('ffecha1')==''? date("Y-m-").'01': $this->input->get('ffecha1');
    $_GET['ffecha2'] = $this->input->get('ffecha2')==''? date("Y-m-d"): $this->input->get('ffecha2');

    $sql = "AND DATE(cic.fecha)>='".$_GET['ffecha1']."' AND DATE(cic.fecha)<='".$_GET['ffecha2']."'";

    if ( ! empty($_GET['ide']))
      $sql .= " AND cic.id_empacador = {$_GET['ide']}";

    //  Obtiene las entradas, salidas anteriores a la fecha
    $query = $this->db->query("
                SELECT COALESCE(SUM(salidas), 0) AS salidas,
                       COALESCE(SUM(entradas), 0) AS entradas,
                       COALESCE(SUM(salidas), 0) - COALESCE(SUM(entradas), 0) AS total_anterior
                FROM

                  (
                    SELECT id_marca, SUM(cantidad) AS salidas, 0 AS entradas
                    FROM cajas_inventario_carton
                    WHERE id_marca = ".$_GET['id']." AND
                          tipo = 's' AND
                          DATE(fecha) < '".$_GET['ffecha1']."'
                    GROUP BY id_marca

                    UNION ALL

                    SELECT id_marca, 0 AS salidas, SUM(cantidad) AS entradas
                    FROM cajas_inventario_carton
                    WHERE id_marca = ".$_GET['id']." AND
                          tipo = 'en' AND
                          DATE(fecha) < '".$_GET['ffecha1']."'
                    GROUP BY id_marca
                  ) AS ant

                  ");

    $response['anteriores'] = array();
    if ($query->num_rows() > 0) $response['anteriores'] = $query->result();

    $query->free_result();

    // Obtiene las entradas, salidas en el rango de fechas
    $query = $this->db->query("
                SELECT cic.id_inventario_carton,
                       DATE(cic.fecha) AS fecha,
                       cic.concepto,
                       cic.cantidad,
                       cic.es_desecho,
                       cic.tipo,
                       e.nombre AS empacador
                FROM cajas_inventario_carton AS cic
                LEFT JOIN empacadores AS e ON e.id_empacador = cic.id_empacador
                WHERE cic.id_marca = ".$_GET['id']." {$sql}
                ORDER BY cic.id_inventario_carton, cic.fecha ASC");

    $response['inventario'] = array();
    if($query->num_rows() > 0) $response['inventario'] = $query->result();

    return $response;
  }

  public function addCaja($data = null)
  {
    if ($data === null)
    {

      $data = array(
        'id_marca' => $this->input->post('dmarca'),
        'fecha'    => $this->input->post('dfecha'),
        'concepto' => $this->input->post('dconcepto'),
        'cantidad' => $this->input->post('dcantidad'),
        'tipo'     => $this->input->post('dmovimiento'),
      );

      if ($_POST['dmovimiento'] === 's' && ! isset($_POST['ddesecho']))
        $data['id_empacador'] = $this->input->post('did_empacador');

    }

    $this->db->insert('cajas_inventario_carton', $data);
    $id = $this->db->insert_id();
    return array(true, '', 3,$id);
  }

  /*************** FUNCIONES PARA GENERAR PDF'S Y XLS'S  *****************/

  /**
   * Visualiza/Descarga el PDF de la relacion de cajas entregasa y salidas de
   * un productor en un rango de fechas
   *
   * @return void
   */
  public function marca_inventario_pdf()
  {
    $this->load->model('marcas_model');
    $this->load->model('empacadores_model');

    $inv = $this->get_marca_inventario();

    $info = $this->marcas_model->getInfoMarca($_GET['id']);

    if (isset($_GET['ide']))
      $empacador = $this->empacadores_model->getInfoEmpacador($_GET['ide']);

    $this->load->library('mypdf');
    // Creación del objeto de la clase heredada
    $pdf = new MYpdf('L', 'mm', 'Letter');
    $pdf->titulo2 = "Relación de Cajas Entradas y Salidas ";
    $pdf->titulo2 .= 'Marca '.$info['info']->nombre;

    if (isset($_GET['ide']))
      if ( !empty($_GET['ide']))
        $pdf->titulo2 .= 'Empacador '.$empacador['info']->nombre;

    $pdf->titulo3 = 'Del: '.$this->input->get('ffecha1')." Al ".$this->input->get('ffecha2')."\n";

    $pdf->AliasNbPages();
    //$pdf->AddPage();
    $pdf->SetFont('Arial','', 8);

    $aligns = array('C', 'C', 'C', 'C', 'C', 'C', 'C');
    $widths = array(20, 90, 78, 20, 20, 20, 20);
    $header = array('Fecha', 'Concepto', 'Empacador', 'Salidas', 'Entradas', 'Total', 'Desecho');

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


      $inv['inventario'][0]->fecha     = '';
      $inv['inventario'][0]->id_inventario_carton = '';
      $inv['inventario'][0]->concepto  = '';
      $inv['inventario'][0]->empacador = '';
      $inv['inventario'][0]->salida    = 0;
      $inv['inventario'][0]->entrada   = 0;
      $inv['inventario'][0]->desecho   = '';
      $inv['inventario'][0]->tipo   = '';
      $inv['inventario'][0]->cantidad   = 0;
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
                        $inv['anteriores'][0]->total_anterior, ''), false);

        $bad_saldo_ante = false;
      }

      $salida = $entrada = 0;

      if ($item->tipo === 's') $total_salidas  += $salida = $item->cantidad;
      else $total_entradas += $entrada = $item->cantidad;

      if ($item->id_inventario_carton !== '')
      {
        $datos = array($item->fecha,
                       $item->concepto,
                       $item->empacador,
                       $salida,
                       $entrada,
                       '',
                       ($item->es_desecho == 1 ? 'Si' : 'No' ));

        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($datos, false);
      }
    }

    $pdf->SetX(6);
    $pdf->SetFont('Arial','B',8);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetWidths(array(188, 20, 20, 20, 20));
    $pdf->Row(array('Totales:',
                      $total_salidas,
                      $total_entradas,
                      floatval($total_salidas) - floatval($total_entradas)), true);

    $pdf->Output('relacion_marca.pdf', 'I');
  }
}

/* End of file cajas_carton_model.php */
/* Location: ./application/models/cajas_carton_model.php */