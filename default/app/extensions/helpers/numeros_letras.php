<?php
//------    CONVERTIR NUMEROS A LETRAS         ---------------
//------    Máxima cifra soportada: 18 dígitos con 2 decimales
//------    999,999,999,999,999,999.99
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE BILLONES
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE MILLONES
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE PESOS 99/100 M.N.
//------    Creada por:                        ---------------
//------             ULTIMINIO RAMOS GALÁN     ---------------
//------            uramos@gmail.com           ---------------
//------    10 de junio de 2009. México, D.F.  ---------------
//------    PHP Version 4.3.1 o mayores (aunque podría funcionar en versiones anteriores, tendrías que probar)
class NumerosLetras
{
	
public static function NL($xcifra,$moneda)
{
    $xcifra=number_format($xcifra,2,'.','');
	$xarray = array(0 => "Cero",
        1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
        "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
        "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
        100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
    );
//
    $xcifra = trim($xcifra);
    $xlength = strlen($xcifra);
    $xpos_punto = strpos($xcifra, ".");
    $xaux_int = $xcifra;
    $xdecimales = "00";
    if (!($xpos_punto === false)) {
        if ($xpos_punto == 0) {
            $xcifra = "0" . $xcifra;
            $xpos_punto = strpos($xcifra, ".");
        }
        $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
        $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
    }
 
    $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
    $xcadena = "";
    for ($xz = 0; $xz < 3; $xz++) {
        $xaux = substr($XAUX, $xz * 6, 6);
        $xi = 0;
        $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
        $xexit = true; // bandera para controlar el ciclo del While
        while ($xexit) {
            if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                break; // termina el ciclo
            }
 
            $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
            $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
            for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                switch ($xy) {
                    case 1: // checa las centenas
                        if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                             
                        } else {
                            $key = (int) substr($xaux, 0, 3);
                            if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                $xseek = $xarray[$key];
                                $xsub = self::subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                if (substr($xaux, 0, 3) == 100)
                                    $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                            }
                            else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                $key = (int) substr($xaux, 0, 1) * 100;
                                $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                $xcadena = " " . $xcadena . " " . $xseek;
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 0, 3) < 100)
                        break;
                    case 2: // checa las decenas (con la misma lógica que las centenas)
                        if (substr($xaux, 1, 2) < 10) {
                             
                        } else {
                            $key = (int) substr($xaux, 1, 2);
                            if (TRUE === array_key_exists($key, $xarray)) {
                                $xseek = $xarray[$key];
                                $xsub = self::subfijo($xaux);
                                if (substr($xaux, 1, 2) == 20)
                                    $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3;
                            }
                            else {
                                $key = (int) substr($xaux, 1, 1) * 10;
                                $xseek = $xarray[$key];
                                if (20 == substr($xaux, 1, 1) * 10)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 1, 2) < 10)
                        break;
                    case 3: // checa las unidades
                        if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
                             
                        } else {
                            $key = (int) substr($xaux, 2, 1);
                            $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                            $xsub = self::subfijo($xaux);
                            $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                        } // ENDIF (substr($xaux, 2, 1) < 1)
                        break;
                } // END SWITCH
            } // END FOR
            $xi = $xi + 3;
        } // ENDDO
 
        if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
            $xcadena.= " DE";
 
        if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
            $xcadena.= " DE";
 
        // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
        if (trim($xaux) != "") {
            switch ($xz) {
                case 0:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena.= "UN BILLON ";
                    else
                        $xcadena.= " BILLONES ";
                    break;
                case 1:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena.= "UN MILLON ";
                    else
                        $xcadena.= " MILLONES ";
                    break;
                case 2:
                    if ($xcifra < 1) {
                        $xcadena = "CERO CON $xdecimales/100 $moneda";
                    }
                    if ($xcifra >= 1 && $xcifra < 2) {
                        $xcadena = "UN CON $xdecimales/100 $moneda ";
                    }
                    if ($xcifra >= 2) {
                        $xcadena.= " CON $xdecimales/100 $moneda "; //
                    }
                    break;
            } // endswitch ($xz)
        } // ENDIF (trim($xaux) != "")
        // ------------------      en este caso, para México se usa esta leyenda     ----------------
        $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
    } // ENDFOR ($xz)
    return trim($xcadena);
}
 
// END FUNCTION
 
public static function subfijo($xx)
{ // esta función regresa un subfijo para la cifra
    $xx = trim($xx);
    $xstrlen = strlen($xx);
    if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
        $xsub = "";
    //
    if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
        $xsub = "MIL";
    //
    return $xsub;
}
 

public static function NL___borrrar($tt,$moneda)
{
	//favor de teclear a mano la cantidad numerica a convertir y asignarla a $tt
	$tt; 
	
	$tt = $tt+0.009;
	$Numero = intval($tt);
	$Decimales = $tt - intval($tt);
	$Decimales= $Decimales*100;
	$Decimales= intval($Decimales);
	$x=self::NumerosALetras($Numero);
	$code='';
	$code.=strtoupper($x);
	if ($Decimales > 0){
	$code.=$Decimales.'/100';
	$code.=' '.strtoupper($moneda);
	}
	else {
	$code.=$Decimales.'/100';
	$code.=' '.strtoupper($moneda);
	}
	return $code;
}
public static function Centenas($VCentena)
	{
		$Numeros[0] = "cero";
	$Numeros[1] = "uno";
	$Numeros[2] = "dos";
	$Numeros[3] = "tres";
	$Numeros[4] = "cuatro";
	$Numeros[5] = "cinco";
	$Numeros[6] = "seis";
	$Numeros[7] = "siete";
	$Numeros[8] = "ocho";
	$Numeros[9] = "nueve";
	$Numeros[10] = "diez";
	$Numeros[11] = "once";
	$Numeros[12] = "doce";
	$Numeros[13] = "trece";
	$Numeros[14] = "catorce";
	$Numeros[15] = "quince";
	$Numeros[20] = "veinte";
	$Numeros[30] = "treinta";
	$Numeros[40] = "cuarenta";
	$Numeros[50] = "cincuenta";
	$Numeros[60] = "sesenta";
	$Numeros[70] = "setenta";
	$Numeros[80] = "ochenta";
	$Numeros[90] = "noventa";
	$Numeros[100] = "ciento";
	$Numeros[101] = "quinientos";
	$Numeros[102] = "setecientos";
	$Numeros[103] = "novecientos";
	if ($VCentena == 1) { return $Numeros[100]; }
	else if ($VCentena == 5) { return $Numeros[101];}
	else if ($VCentena == 7 ) {return ( $Numeros[102]); }
	else if ($VCentena == 9) {return ($Numeros[103]);}
	else {return $Numeros[$VCentena];}
	}
	
public static function Unidades($VUnidad)
	{
		
	$Numeros[0] = "cero";
	$Numeros[1] = "un";
	$Numeros[2] = "dos";
	$Numeros[3] = "tres";
	$Numeros[4] = "cuatro";
	$Numeros[5] = "cinco";
	$Numeros[6] = "seis";
	$Numeros[7] = "siete";
	$Numeros[8] = "ocho";
	$Numeros[9] = "nueve";
	$Numeros[10] = "diez";
	$Numeros[11] = "once";
	$Numeros[12] = "doce";
	$Numeros[13] = "trece";
	$Numeros[14] = "catorce";
	$Numeros[15] = "quince";
	$Numeros[20] = "veinte";
	$Numeros[30] = "treinta";
	$Numeros[40] = "cuarenta";
	$Numeros[50] = "cincuenta";
	$Numeros[60] = "sesenta";
	$Numeros[70] = "setenta";
	$Numeros[80] = "ochenta";
	$Numeros[90] = "noventa";
	$Numeros[100] = "ciento";
	$Numeros[101] = "quinientos";
	$Numeros[102] = "setecientos";
	$Numeros[103] = "novecientos";
	
	$tempo=$Numeros[$VUnidad]; 
	return $tempo;
	}
	
public static function Decenas($VDecena)
	{
		
	$Numeros[0] = "cero";
	$Numeros[1] = "uno";
	$Numeros[2] = "dos";
	$Numeros[3] = "tres";
	$Numeros[4] = "cuatro";
	$Numeros[5] = "cinco";
	$Numeros[6] = "seis";
	$Numeros[7] = "siete";
	$Numeros[8] = "ocho";
	$Numeros[9] = "nueve";
	$Numeros[10] = "diez";
	$Numeros[11] = "once";
	$Numeros[12] = "doce";
	$Numeros[13] = "trece";
	$Numeros[14] = "catorce";
	$Numeros[15] = "quince";
	$Numeros[20] = "veinte";
	$Numeros[30] = "treinta";
	$Numeros[40] = "cuarenta";
	$Numeros[50] = "cincuenta";
	$Numeros[60] = "sesenta";
	$Numeros[70] = "setenta";
	$Numeros[80] = "ochenta";
	$Numeros[90] = "noventa";
	$Numeros[100] = "ciento";
	$Numeros[101] = "quinientos";
	$Numeros[102] = "setecientos";
	$Numeros[103] = "novecientos";
	$tempo =	($Numeros[$VDecena]);
	return $tempo;
	}
	
public static function NumerosALetras($Numero)
	{ 
		$Decimales = 0;
	//$Numero = intval($Numero);
	$letras = "";
	
	while ($Numero != 0)
	{
		// '*---> Validación si se pasa de 100 millones
		if ($Numero >= 1000000000)
		{
			$letras = "Error en Conversión a Letras";
			$Numero = 0;
			$Decimales = 0;
		}
		// '*---> Centenas de Millón
		if (($Numero < 1000000000) and ($Numero >= 100000000))
		{
			if ((intval($Numero / 100000000) == 1) and (($Numero - (intval($Numero / 100000000) * 100000000)) < 1000000))
			{
				$letras .= (string) "cien millones ";
			}else
			{
				$letras = $letras & self::Centenas(intval($Numero / 100000000));
				if ((intval($Numero / 100000000) <> 1) and (intval($Numero / 100000000) <> 5) and (intval($Numero / 100000000) <> 7) and (intval($Numero / 100000000) <> 9))
				{
					$letras .= (string) "cientos ";
				}else
				{
					$letras .= (string) " ";
				}
			}
		$Numero = $Numero - (intval($Numero / 100000000) * 100000000);
		}
	
	// '*---> Decenas de Millón
	if (($Numero < 100000000) and ($Numero >= 10000000)) {
	if (intval($Numero / 1000000) < 16) {
	$tempo = self::Decenas(intval($Numero / 1000000));
	$letras .= (string) $tempo; 
	$letras .= (string) " millones ";
	$Numero = $Numero - (intval($Numero / 1000000) * 1000000);
	}
	else {	
	$letras = $letras & self::Decenas(intval($Numero / 10000000) * 10);
	$Numero = $Numero - (intval($Numero / 10000000) * 10000000);
	if ($Numero > 1000000) {
	$letras .= $letras & " y ";
	}
	}
	}
	
	// '*---> Unidades de Millón
	if (($Numero < 10000000) and ($Numero >= 1000000)) {
	$tempo=(intval($Numero / 1000000));
	if ($tempo == 1) {	
	$letras .= (string) " un millón ";
	}	
	else {
	$tempo= self::Unidades(intval($Numero / 1000000));
	$letras .= (string) $tempo; 
	$letras .= (string) " millones ";
	}
	$Numero = $Numero - (intval($Numero / 1000000) * 1000000);
	}
	
	// '*---> Centenas de Millar
	if (($Numero < 1000000) and ($Numero >= 100000)) {
	$tempo=(intval($Numero / 100000));
	$tempo2=($Numero - ($tempo * 100000));
	if (($tempo == 1) and ($tempo2 < 1000)) {
	$letras .= (string) "cien mil ";
	}	
	else {
	$tempo=self::Centenas(intval($Numero / 100000));
	$letras .= (string) $tempo;
	$tempo=(intval($Numero / 100000));
	if (($tempo <> 1) and ($tempo <> 5) and ($tempo <> 7) and ($tempo <> 9)) {
	$letras .= (string) "cientos ";
	}	
	else { 
	$letras .= (string) " ";
	}
	}
	$Numero = $Numero - (intval($Numero / 100000) * 100000);
	}
	
	// '*---> Decenas de Millar
	if (($Numero < 100000) and ($Numero >= 10000)) {
	$tempo= (intval($Numero / 1000));
	if ($tempo < 16) {
	$tempo = self::Decenas(intval($Numero / 1000)); 
	$letras .= (string) $tempo;
	$letras .= (string) " mil ";
	$Numero = $Numero - (intval($Numero / 1000) * 1000);
	}
	else {
	$tempo = self::Decenas(intval($Numero / 10000) * 10);
	$letras .= (string) $tempo;
	$Numero = $Numero - (intval(($Numero / 10000)) * 10000);
	if ($Numero > 1000) {
	$letras .= (string) " y ";	
	}
	else {
	$letras .= (string) " mil ";
	
	}
	}
	}
	
	if (($Numero < 10000) and ($Numero >= 1000)) {
	$tempo=(intval($Numero / 1000));
	if ($tempo == 1) {
	$letras .= (string) "";/*"un"*/
	}	
	else {
	$tempo = self::Unidades(intval($Numero / 1000));
	$letras .= (string) $tempo;
	}
	$letras .= (string) " mil ";
	$Numero = $Numero - (intval($Numero / 1000) * 1000);
	}
	
	// '*---> Centenas
	if (($Numero < 1000) and ($Numero > 99)) {	
	if ((intval($Numero / 100) == 1) and (($Numero - (intval($Numero / 100) * 100)) < 1)) {	
	$letras = $letras & "cien ";
	}	
	else {	
	$temp=(intval($Numero / 100));	
	$l2=self::Centenas($temp);	
	$letras .= (string) $l2;	
	if ((intval($Numero / 100) <> 1) and (intval($Numero / 100) <> 5) and (intval($Numero / 100) <> 7) and (intval($Numero / 100) <> 9)) {
	$letras .= "cientos ";
	}	
	else {
	$letras .= (string) " ";
	}
	}
	
	$Numero = $Numero - (intval($Numero / 100) * 100);
	
	}
	if (($Numero < 100) and ($Numero > 9) ) {
	if ($Numero < 16 ) {
	$tempo = self::Decenas(intval($Numero));
	$letras .= $tempo;
	$Numero = $Numero - intval($Numero);
	}	
	else {	
	$tempo= self::Decenas(intval(($Numero / 10)) * 10);
	$letras .= (string) $tempo;	
	$Numero = $Numero - (intval(($Numero / 10)) * 10);
	if ($Numero > 0.99) {
	$letras .=(string) " y ";
	
	}
	}
	}
	if (($Numero < 10) and ($Numero > 0.99)) {
	$tempo=self::Unidades(intval($Numero));
	$letras .= (string) $tempo;
	
	$Numero = $Numero - intval($Numero);
	}
	
	if ($Decimales > 0) {
	
	}	
	else {
	if (($letras <> "Error en Conversión a Letras") and (strlen(trim($letras)) > 0)) {
	$letras .= (string) " ";
	
	}
	}
	return $letras;
	}
	}
}
?>