<?php

class Cheque extends FPDF {
	var $titulo1 = 'Red Fire de Colima';
	var $rg;
	
	var $hheader = '';
	
	var $limiteY = 0;

	var $orientation;
	var $unit;
	
	/**
	 * P:Carta Vertical, L:Carta Horizontal, lP:Legal vertical, lL:Legal Horizontal
	 * @param unknown_type $orientation
	 * @param unknown_type $unit
	 * @param unknown_type $size
	 */
	function __construct($orientation='P', $unit='mm', $size=array(70, 165)){
		$this->orientation = $orientation;
		$this->unit = $unit;
	}
	
	
	/**
	 * Descarga el estado de una cuenta seleccionada en formato pdf
	 */
	public function generaCheque($id_movimiento){
		$this->rg = Registry::singleton();
		$data = $this->rg->getObject("dao")->selectRows('id_movimiento, id_banco, fecha, monto, nombre_comercial',
			'banco_movimientos AS bm INNER JOIN proveedores AS p ON p.id_proveedor = bm.id_proveedor',
			"WHERE bm.id_movimiento = ".$id_movimiento, true);
		if(count($data) > 0)
			$this->{'generaCheque_'.$data[0]['id_banco']}($data[0]['nombre_comercial'], $data[0]['monto'], $data[0]['fecha']);
		else
			echo "No se obtubo la informacion del cheque";
	}

	/**
	 * Banorte 13
	 */
	public function generaCheque_13($nombre, $monto, $fecha=null, $opc='I'){
		parent::__construct($this->orientation, $this->unit, array(70, 165));

		$fecha = $fecha==null? date("Y-m-d"): $fecha;
		$this->AddPage('P', array(70, 165));
		$this->SetFont('Arial','', 10);
		
		$this->SetDrawColor(0, 0, 0);
		$this->SetLineWidth(0.1);
		$this->Rect(0, 0, 70, 165, 'D');
		
		$this->RotatedText(44, 64, $this->rg->getObject('string')->fechaATexto($fecha), -90);
		
		$this->RotatedText(44, 124, $this->rg->getObject('string')->monedaToString($monto), -90);
		
		$this->RotatedText(38, 35, $nombre, -90);
		
		$this->RotatedText(28, 8, $this->rg->getObject('string')->num2letras($monto), -90);
		
		$this->Output('cheque.pdf', $opc);
	}

	/**
	 * Banbajio 12
	 */
	public function generaCheque_12($nombre, $monto, $fecha=null, $opc='I'){
		parent::__construct($this->orientation, $this->unit, array(70, 165));

		$fecha = $fecha==null? date("Y-m-d"): $fecha;
		$this->AddPage('P', array(70, 165));
		$this->SetFont('Arial','', 10);
		
		$this->SetDrawColor(0, 0, 0);
		$this->SetLineWidth(0.1);
		$this->Rect(0, 0, 70, 165, 'D');
		
		$this->RotatedText(50, 64, $this->rg->getObject('string')->fechaATexto($fecha), -90);
		
		$this->RotatedText(40, 124, $this->rg->getObject('string')->monedaToString($monto), -90);
		
		$this->RotatedText(42, 8, $nombre, -90);
		
		$this->RotatedText(32, 8, $this->rg->getObject('string')->num2letras($monto), -90);
		
		$this->Output('cheque.pdf', $opc);
	}
	
	/**
	 * Afirme 11
	 */
	public function generaCheque_11($nombre, $monto, $fecha=null, $opc='I'){
		parent::__construct($this->orientation, $this->unit, array(70, 165));
		
		$fecha = $fecha==null? date("Y-m-d"): $fecha;
		$this->AddPage('P', array(70, 165));
		$this->SetFont('Arial','', 10);
		
		$this->SetDrawColor(0, 0, 0);
		$this->SetLineWidth(0.1);
		$this->Rect(0, 0, 70, 165, 'D');
		
		$this->RotatedText(50, 64, $this->rg->getObject('string')->fechaATexto($fecha), -90);
		
		$this->RotatedText(40, 124, $this->rg->getObject('string')->monedaToString($monto), -90);
		
		$this->RotatedText(42, 8, $nombre, -90);
		
		$this->RotatedText(32, 8, $this->rg->getObject('string')->num2letras($monto), -90);
		
		$this->Output('cheque.pdf', $opc);
	}
	
	
	
	
	function RotatedText($x, $y, $txt, $angle)
	{
		//Text rotated around its origin
		$this->Rotate($angle, $x, $y);
		$this->Text($x, $y, $txt);
		$this->Rotate(0);
	}
	
	
	var $angle=0;
	
	function Rotate($angle, $x=-1, $y=-1)
	{
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)
		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
		}
	}
	
	function _endpage()
	{
		if($this->angle!=0)
		{
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}
}


?>