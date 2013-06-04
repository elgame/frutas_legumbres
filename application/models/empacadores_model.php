<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Empacadores_model extends CI_Model {

  function __construct(){
    parent::__construct();
  }

  /**
   * Obtiene el listado de Marcas
   */
  public function getEmpacadores()
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
        lower(nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' )";

    $fstatus = $this->input->get('fstatus')===false? 'ac': $this->input->get('fstatus');
    if($fstatus != '' && $fstatus != 'todos')
      $sql .= ($sql==''? ' WHERE ': ' AND ')." status = '".$fstatus."'";

    $query = BDUtil::pagination("
        SELECT id_empacador, nombre, status
        FROM empacadores
        ".$sql."
        ORDER BY nombre ASC
        ", $params, true);
    $res = $this->db->query($query['query']);

    $response = array(
      'empacadores'    => array(),
      'total_rows'     => $query['total_rows'],
      'items_per_page' => $params['result_items_per_page'],
      'result_page'    => $params['result_page']
    );
    $response['empacadores'] = $res->result();
    return $response;
  }

  /**
   * Obtiene las marcas
   * @return array
   */
  public function getEmpacadoresAll()
  {
    $query = $this->db->query("SELECT *
                               FROM empacadores
                               WHERE status = 'ac'");

    return $query->result();
  }

  /**
   * Obtiene la informacion de una marca
   */
  public function getInfoEmpacador($id, $info_basic=false){
    $res = $this->db
      ->select('*')
      ->from('empacadores AS e')
      ->where("e.id_empacador = '".$id."'")
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
   * Agrega la info de una marca a la bd
   */
  public function addEmpacador($data=null)
  {
    if ($data == null) {
      $data = array(
        'nombre'    => $this->input->post('dnombre'),
      );
    }
    $this->db->insert('empacadores', $data);

    $msg = 3;
    return array(true, '', $msg);
  }

  /**
   * Modifica la informacion de un empacador.
   */
  public function updateEmpacador($id, $data=null)
  {
    $msg = 4;
    if ($data == null) {
      $data = array(
        'nombre'    => $this->input->post('dnombre'),
      );
    }
    $this->db->update('empacadores', $data, "id_empacador = '".$id."'");

    return array(true, '', $msg);
  }

  /************************ AJAX  *****************************/

  /**
   * Obtiene el listado de productores
   */
  public function get_empacadores_ajax()
  {
    $sql = '';
    $res = $this->db->query("
        SELECT id_empacador, nombre, status
        FROM empacadores
        WHERE status = 'ac' AND lower(nombre) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'
        ORDER BY nombre ASC
        LIMIT 20");

    $response = array();
    if($res->num_rows() > 0)
    {
      foreach($res->result() as $itm)
      {
        $response[] = array(
            'id'    => $itm->id_empacador,
            'label' => $itm->nombre,
            'value' => $itm->nombre,
            'item'  => $itm,
        );
      }
    }

    return $response;
  }

}

/* End of file empacadores.php */
/* Location: ./application/models/empacadores.php */