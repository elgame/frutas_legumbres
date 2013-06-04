<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Marcas_model extends CI_Model {

  function __construct(){
    parent::__construct();
  }

  /**
   * Obtiene el listado de Marcas
   */
  public function getMarcas()
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
        SELECT id_marca, nombre, status
        FROM marcas
        ".$sql."
        ORDER BY nombre ASC
        ", $params, true);
    $res = $this->db->query($query['query']);

    $response = array(
      'marcas'    => array(),
      'total_rows'     => $query['total_rows'],
      'items_per_page' => $params['result_items_per_page'],
      'result_page'    => $params['result_page']
    );
    $response['marcas'] = $res->result();
    return $response;
  }

  /**
   * Obtiene las marcas
   * @return array
   */
  public function getMarcasAll()
  {
    $query = $this->db->query("SELECT *
                               FROM marcas
                               WHERE status = 'ac'");

    return $query->result();
  }

  /**
   * Obtiene la informacion de una marca
   */
  public function getInfoMarca($id, $info_basic=false){
    $res = $this->db
      ->select('*')
      ->from('marcas AS m')
      ->where("m.id_marca = '".$id."'")
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
  public function addMarca($data=null)
  {
    if ($data == null) {
      $data = array(
        'nombre'    => $this->input->post('dnombre'),
      );
    }
    $this->db->insert('marcas', $data);

    $msg = 3;
    return array(true, '', $msg);
  }

  /**
   * Modifica la informacion de una marca
   */
  public function updateMarca($id, $data=null)
  {
    $msg = 4;
    if ($data == null) {
      $data = array(
        'nombre'    => $this->input->post('dnombre'),
      );
    }
    $this->db->update('marcas', $data, "id_marca = '".$id."'");

    return array(true, '', $msg);
  }


  /**
   * Obtiene el listado de marcas para usar ajax
   */
  public function getMarcaAjax()
  {
    $sql = '';
    $res = $this->db->query("
        SELECT id_marca, nombre
        FROM marcas
        WHERE status = 'ac' AND lower(nombre) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%'
        ORDER BY nombre ASC
        LIMIT 20");

    $response = array();
    if($res->num_rows() > 0){
      foreach($res->result() as $itm){
        $response[] = array(
            'id'    => $itm->id,
            'label' => $itm->nombre,
            'value' => $itm->nombre,
            'item'  => $itm,
        );
      }
    }

    return $response;
  }

}

/* End of file marcas_model.php */
/* Location: ./application/models/marcas_model.php */