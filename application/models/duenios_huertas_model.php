<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class duenios_huertas_model extends CI_Model{

  function __construct(){
    parent::__construct();
  }

  /**
   * Obtiene el listado de Dueños de Huertas
   */
  public function getDueniosHuertas()
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
      $sql = " WHERE (
        lower(nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR
        lower(calle) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR
        lower(colonia) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR
        lower(municipio) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR
        lower(estado) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%'
        )";

    $fstatus = $this->input->get('fstatus')===false? 'ac': $this->input->get('fstatus');
    if($fstatus != '' && $fstatus != 'todos')
      $sql .= ($sql==''? ' WHERE ': ' AND ')." status = '".$fstatus."'";

    $query = BDUtil::pagination("
        SELECT id_dueno, nombre, telefono, celular,
          CONCAT(calle, ' #', no_exterior, ', ', colonia, ', ', municipio, ', ', estado) AS direccion, status
        FROM duenios_huertas
        ".$sql."
        ORDER BY nombre ASC
        ", $params, true);
    $res = $this->db->query($query['query']);

    $response = array(
      'duenios'        => array(),
      'total_rows'     => $query['total_rows'],
      'items_per_page' => $params['result_items_per_page'],
      'result_page'    => $params['result_page']
    );
    $response['duenios'] = $res->result();
    return $response;
  }

  /**
   * Obtiene la informacion de un productor
   */
  public function getInfoDuenioHuerta($id, $info_basic=false)
  {
    $res = $this->db
                ->select('*')
                ->from('duenios_huertas')
                ->where("id_dueno = '".$id."'")
                ->get();
    if($res->num_rows() > 0)
    {
      $response['info'] = $res->row();
      $res->free_result();
      if($info_basic)
        return $response;

      return $response;
    }
    else
      return false;
  }

  /**
   * Crea un Dueño Huerta
   */
  public function addDuenioHuerta($data = null){

    if ($data === null) {

      $data = array(
        'nombre'      => $this->input->post('dnombre'),
        'calle'       => $this->input->post('dcalle'),
        'no_exterior' => $this->input->post('dno_exterior'),
        'no_interior' => $this->input->post('dno_interior'),
        'colonia'     => $this->input->post('dcolonia'),
        'municipio'   => $this->input->post('dmunicipio'),
        'estado'      => $this->input->post('destado'),
        'cp'          => $this->input->post('dcp'),
        'telefono'    => $this->input->post('dtelefono'),
        'celular'     => $this->input->post('dcelular'),
      );
    }

    $this->db->insert('duenios_huertas', $data);

    $msg = 3;
    return array(true, '', $msg);
  }

  /**
   * Modifica la informacion de un productor
   */
  public function updateDuenioHuerta($id, $data=null)
  {
    $msg = 4;
    if ($data == null)
    {
      $data = array(
        'nombre'  => $this->input->post('dnombre'),
        'calle'          => $this->input->post('dcalle'),
        'no_exterior'    => $this->input->post('dno_exterior'),
        'no_interior'    => $this->input->post('dno_interior'),
        'colonia'        => $this->input->post('dcolonia'),
        'municipio'      => $this->input->post('dmunicipio'),
        'estado'         => $this->input->post('destado'),
        'cp'             => $this->input->post('dcp'),
        'telefono'       => $this->input->post('dtelefono'),
        'celular'        => $this->input->post('dcelular')
      );

    }

    $this->db->update('duenios_huertas', $data, "id_dueno = '".$id."'");

    return array(true, '', $msg);
  }

  /**
   * Obtiene el listado de los dueños de las huertas para el metodo por ajax
   */
  public function get_duenos_huertas_ajax()
  {
    $sql = '';
    $res = $this->db->query("
        SELECT id_dueno, nombre, calle, no_exterior, no_interior, colonia, municipio,
                estado, cp, telefono, celular
        FROM duenios_huertas
        WHERE status = 'ac' AND lower(nombre) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'
        ORDER BY nombre ASC
        LIMIT 20");

    $response = array();
    if($res->num_rows() > 0)
    {
      foreach($res->result() as $itm)
      {
        $response[] = array(
            'id'    => $itm->id_dueno,
            'label' => $itm->nombre,
            'value' => $itm->nombre,
            'item'  => $itm,
        );
      }
    }

    return $response;
  }

}

/* End of file duenios_huertas_model.php */
/* Location: ./application/models/duenios_huertas_model.php */