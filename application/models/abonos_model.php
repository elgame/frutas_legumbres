<?php

class abonos_model extends CI_Model{

  function __construct(){
    parent::__construct();
  }

  /**
   *  Realiza un abono.
   *
   *  - Si el param $id_caja se le pasa entonces no toma en cuenta el
   *     $_POST['id_caja'].
   *  - Si el param $abono se le pasa entonces no toma en cuenta los $_POST.
   *  - Si el param $liquidar se le pasa entonces el monto a abonar lo toma
   *    del saldo restante de caja.
   *  - Si el param $banco es true entonces realiza un movimiento de banco.
   *  - Si el param $bancoData se le pasa entonces no toma en cuenta los
   *    $_POST de los datos del banco.
   *  - Si el param $masivo es true entonces el monto que se registrara en
   *    los movimientos del banco sera el que se le pase desde $_POST['monto']
   *    o en su caso desde $bancoData['monto']
   *
   * @return  array
   */
  public function addAbono($id_caja=null, $abono=null, $liquidar=false,
                          $banco=false, $bancoData=null, $masivo=false)
  {
    $this->load->model('cajas_model');

    $id_caja = ($id_caja) ? $id_caja : $this->input->post('id_caja');

    $caja_data = $this->cajas_model
                      ->get_info_entrada($id_caja); // Obtiene la info de una caja

    $saldo = $this->getSaldoCaja($caja_data); // Obtiene el saldo actual de la caja

    $monto = ($abono) ? floatval($abono['monto']) : floatval($_POST['monto']);

    if ($monto == 0)
    {
      if ($masivo) $msg = 'La cantidad especificada no fue suficiente para cubrir todos los abonos.';
      else  $msg = 'El monto/cantidad a abonar no puede ser cero';

      return array('passes'=>false,
        'msg'=>$msg,
        'ico'=>'error');
    }

    if ($saldo > 0)
    {
      $dataResponse = array();

      // En caso que se quiera realizar un abono mediante la cuenta de un banco
      // entra a esta condicion. Si el saldo de la cuenta no es suficiente para
      // cubrir el abono entonces retorna un error.
      // if ($banco)
      // {
      //   $this->load->model('banco_cuentas_model');
      //   $cuenta = ($bancoData) ? $bancoData['id_cuenta'] : $_POST['id_cuenta'];

      //   $cuenta_info = $this->banco_cuentas_model->getCuentas(0, $cuenta);

      //   if ($monto > $cuenta_info['cuentas'][0]->saldo)
      //     return array('passes'=>false,
      //       'msg'=>'El monto especificado es mayor al saldo de la cuenta',
      //       'ico'=>'error');


      //   // if ($saldo > $cuenta_info['cuentas'][0]->saldo)
      //   //   return array('passes'=>false,
      //   //     'msg'=>'El saldo de la cuenta especificada es insuficiente',
      //   //     'ico'=>'error');
      // }

      if ($abono === null)
      {
        $abono = array('id_caja'     => $id_caja,
                      'id_productor' => $this->input->post('productor'),
                      'fecha'        => str_replace('T', ' ', $_POST['fecha']),
                      'id_productor' => $this->input->post('id_productor'),
                      'concepto'     => $this->input->post('concepto'),
                      'monto'        => $this->input->post('monto'));

        // if ($banco && $bancoData === null)
        // {
        //   $bancoData = array('id_banco'   => $this->input->post('id_banco'),
        //                     'id_cuenta'   => $this->input->post('id_cuenta'),
        //                     'fecha'       => str_replace('T', ' ', $_POST['fecha']),
        //                     'concepto'    => $this->input->post('concepto'),
        //                     'monto'       => $this->input->post('monto'),
        //                     'tipo'        => $this->input->post('tipo'),
        //                     'metodo_pago' => $this->input->post('metodo'));

        //   if ($this->input->post('metodo') === 'cheque')
        //   {
        //     $bancoData['anombre_de'] = $this->input->post('anombrede');
        //     $bancoData['moneda']     = $this->input->post('moneda');
        //   }
        // }
      }

      // Si se va a liquidar el saldo de la caja o el monto es mayor al saldo
      if ($liquidar || (floatval($abono['monto']) > $saldo))
      {
        if (floatval($abono['monto']) > $saldo)
          $abono['monto'] = $saldo;

        // if ($banco && !$masivo) $bancoData['monto'] = $abono['monto'];
      }

      if ($masivo)
        $_POST['monto'] = floatval($_POST['monto']) - floatval($abono['monto']);

      $this->db->insert('cajas_recibidas_abonos', $abono);

      $dataResponse['abonoInfo'] = $abono;
      $dataResponse['abonoInfo']['insert_id'] = $this->db->insert_id();

      // if ($banco)
      // {
      //   $resp = $this->banco_cuentas_model->addOperacion($bancoData);

      //   $dataResponse['bancoInfo'] = $bancoData;
      //   $dataResponse['bancoInfo']['id_mov'] = $resp[3];
      // }

      return array('passes' => true,
                   'msg'    => 'Abono realizado satisfactoriamente!',
                   'ico'    => 'success',
                   'info'   => $dataResponse);
    }
    else return array('passes'=>false,
                      'msg'=>'No se pudo realizar el abono porque el saldo de la caja es 0',
                      'ico'=>'error');
  }

  private function getSaldoCaja($data)
  {
    $total_caja = floatval($data['info']->total_pagar_kc) *
                  floatval($data['info']->precio);

    $total_abonado = 0;
    if (count($data['info']->abonos) > 0)
      foreach ($data['info']->abonos as $abono)
        $total_abonado += $abono->monto;

    $saldo = $total_caja - $total_abonado;

    return floatval($saldo);
  }

  public function eliminar()
  {
    $this->db
      ->delete('cajas_recibidas_abonos', array('id_abono'=>$_GET['ida']));

    return true;
  }

}