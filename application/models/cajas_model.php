<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cajas_model extends CI_Model {

  function __construct(){
    parent::__construct();
  }

  /**
   * Obtiene el listado de los movimiento de cajas
   */
  public function get_cajas()
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
      $sql = " WHERE (lower(pr.nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%')";

    // $fstatus = $this->input->get('fstatus')===false? 'ac': $this->input->get('fstatus');
    // if($fstatus != '' && $fstatus != 'todos')
    //   $sql .= ($sql==''? ' WHERE ': ' AND ')." status = '".$fstatus."'";

    $query = BDUtil::pagination("
        SELECT ci.id_inventario, pr.nombre_fiscal AS productor, v.nombre as variedad, ci.fecha, ci.tipo
        FROM cajas_inventario AS ci
        INNER JOIN productores AS pr ON pr.id_productor = ci.id_productor
        INNER JOIN variedades AS v ON v.id_variedad = ci.id_variedad
        ".$sql."
        ORDER BY ci.id_inventario DESC
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
                ->select('cr.*, pr.nombre_fiscal as productor, dh.nombre as dueno, ct.id_tratamiento, ct.cantidad as cantidad_trata')
                ->from('cajas_recibidas as cr')
                ->join('productores as pr', 'pr.id_productor = cr.id_productor', 'inner')
                ->join('duenios_huertas as dh', 'dh.id_dueno = cr.id_dueno', 'inner')
                ->join('cajas_tratamiento as ct', 'ct.id_caja = cr.id_caja', 'left')
                ->where("cr.id_caja = '".$id."'")
                ->get();

    if($res->num_rows() > 0)
    {
      $response['info'] = $res->row();
      $res->free_result();

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
        if ($_POST['did_tratamiento'] !== '' && $_POST['dcantidad_trata'] !== '')
        {
          $data_trata = array(
            'id_caja'        => $this->input->get('id'),
            'id_tratamiento' => $this->input->post('did_tratamiento'),
            'cantidad'       => $this->input->post('dcantidad_trata'),
          );
        }
      }
    }

    $this->db->update('cajas_recibidas', $data, array('id_caja' => $id)); // Actualiza los datos de la entrada

    if ($data_trata !== null)
    {
      $this->db->delete('cajas_tratamiento', array('id_caja' => $id)); // Elimina el tratamiento de la caja a actualizar
      $this->db->insert('cajas_tratamiento', $data_trata); // Inserta el nuevo tratamiento
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
   * Obtiene las variedad existentes de la base de datos
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

}

/* End of file cajas_model.php */
/* Location: ./application/models/cajas_model.php */