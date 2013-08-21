<?php
/**
 * Clase que implementa un coversor de números 
 * a letras. 
 * 
 * Soporte para PHP >= 5.4
 * Para soportar PHP 5.3, declare los arreglos
 * con la función array. 
 * 
 * @author AxiaCore S.A.S
 *
 */

class numToLetra
{
    private static $UNIDADES = array(
        '',
        'UN ',
        'DOS ',
        'TRES ',
        'CUATRO ',
        'CINCO ',
        'SEIS ',
        'SIETE ',
        'OCHO ',
        'NUEVE ',
        'DIEZ ',
        'ONCE ',
        'DOCE ',
        'TRECE ',
        'CATORCE ',
        'QUINCE ',
        'DIECISEIS ',
        'DIECISIETE ',
        'DIECIOCHO ',
        'DIECINUEVE ',
        'VEINTE '
    );

    private static $DECENAS = array(
        'VENTI',
        'TREINTA ',
        'CUARENTA ',
        'CINCUENTA ',
        'SESENTA ',
        'SETENTA ',
        'OCHENTA ',
        'NOVENTA ',
        'CIEN '
    );

    private static $CENTENAS = array(
        'CIENTO ',
        'DOSCIENTOS ',
        'TRESCIENTOS ',
        'CUATROCIENTOS ',
        'QUINIENTOS ',
        'SEISCIENTOS ',
        'SETECIENTOS ',
        'OCHOCIENTOS ',
        'NOVECIENTOS '
    );

    private static $MONEDAS = array(
        array('country' => 'Colombia', 'currency' => 'COP', 'singular' => 'PESO COLOMBIANO', 'plural' => 'PESOS COLOMBIANOS', 'symbol', '$'),
        array('country' => 'Estados Unidos', 'currency' => 'USD', 'singular' => 'DÓLAR', 'plural' => 'DÓLARES', 'symbol', 'US$'),
        array('country' => 'Europa', 'currency' => 'EUR', 'singular' => 'EURO', 'plural' => 'EUROS', 'symbol', '€'),
        array('country' => 'México', 'currency' => 'M.N.', 'singular' => 'PESO', 'plural' => 'PESOS', 'symbol', '$'),
        array('country' => 'Perú', 'currency' => 'PEN', 'singular' => 'NUEVO SOL', 'plural' => 'NUEVOS SOLES', 'symbol', 'S/'),
        array('country' => 'Reino Unido', 'currency' => 'GBP', 'singular' => 'LIBRA', 'plural' => 'LIBRAS', 'symbol', '£')
    );

    public static function to_word($number, $miMoneda = 'M.N.')
    {   
        if ($miMoneda !== null) {
            try {
                
                $moneda = array_filter(self::$MONEDAS, function($m) use ($miMoneda) {
                    return ($m['currency'] == $miMoneda);
                });

                $moneda = array_values($moneda);

                if (count($moneda) <= 0) {
                    throw new Exception("Tipo de moneda inválido");
                    return;
                }

                if ($number < 2) {
                    $moneda = $moneda[0]['singular'];
                } else {
                    $moneda = $moneda[0]['plural'];
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                return;
            }
        } else {
            $moneda = " ";
        }

        $converted = '';

        if (($number < 0) || ($number > 999999999)) {
            return 'No es posible convertir el numero a letras';
        }

        $numberStr = (string) $number;
        $numberStr = explode('.', $numberStr);
        $numberStrFill = str_pad($numberStr[0], 9, '0', STR_PAD_LEFT);
        $millones = substr($numberStrFill, 0, 3);
        $miles = substr($numberStrFill, 3, 3);
        $cientos = substr($numberStrFill, 6);

        if (intval($millones) > 0) {
            if ($millones == '001') {
                $converted .= 'UN MILLON ';
            } else if (intval($millones) > 0) {
                $converted .= sprintf('%sMILLONES ', self::convertGroup($millones));
            }
        }
        
        if (intval($miles) > 0) {
            if ($miles == '001') {
                $converted .= 'MIL ';
            } else if (intval($miles) > 0) {
                $converted .= sprintf('%sMIL ', self::convertGroup($miles));
            }
        }

        if (intval($cientos) > 0) {
            if ($cientos == '001') {
                $converted .= 'UN ';
            } else if (intval($cientos) > 0) {
                $converted .= sprintf('%s ', self::convertGroup($cientos));
            }
        }

        $converted .= $moneda;

        $numberStr[1] = (isset($numberStr[1])? $numberStr[1]: '00');
        $numberStr[1] .= strlen($numberStr[1])==1? '0': '';
        $converted .= ' '.$numberStr[1].'/100';

        $converted .= ' '.$miMoneda;

        return $converted;
    }

    private static function convertGroup($n)
    {
        $output = '';

        if ($n == '100') {
            $output = "CIEN ";
        } else if ($n[0] !== '0') {
            $output = self::$CENTENAS[$n[0] - 1];   
        }

        $k = intval(substr($n,1));

        if ($k <= 20) {
            $output .= self::$UNIDADES[$k];
        } else {
            if(($k > 30) && ($n[2] !== '0')) {
                $output .= sprintf('%sY %s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            } else {
                $output .= sprintf('%s%s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            }
        }
      
        return $output;
    }
}