<?php
Load::models('tessalidas');
class Tesletrassalidas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tessalidas','tessalidasinternas');
	}
	public function reporteFechas($c=0)
	{
		$cond='';$cond_i='';
		/*Pendientes 1 */
		if($c==1){
			$cond=" AND tesletrassalidas.estadoletras!='PAGADO'";
			$cond_i=" AND tesletrassalidasinternas.estadoletras!='PAGADO'";
		}
		
return $this->find_all_by_sql("(
SELECT 
tesletrassalidas.tessalidas_id as id,
tesletrassalidas.numerodeletra as numerodeletra,
tesletrassalidas.estadoletras as estadoletras,
tesletrassalidas.tessalidas_id as tessalidas_id,
tesletrassalidas.numerounico as numerounico,
tesletrassalidas.banco as banco,
'1' as origen,
tessalidas.totalconigv as monto, 
WEEKOFYEAR(tessalidas.fvencimiento) as semana,
tesletrassalidas.banco as ubicacion, 
DATEDIFF('".date("Y-m-d")."',tessalidas.fvencimiento) AS dd,
tessalidas.fvencimiento as fvencimiento,
tessalidas.femision as femision,
tesdatos.razonsocial as razonsocial
FROM tesletrassalidas, tessalidas,tesdatos  WHERE tessalidas.aclempresas_id=".Session::get('EMPRESAS_ID')." AND  tesletrassalidas.tessalidas_id=tessalidas.id AND tessalidas.tesdatos_id=tesdatos.id".$cond." )
UNION
(SELECT 
tesletrassalidasinternas.tessalidasinternas_id as id,
tesletrassalidasinternas.numerodeletra as numerodeletra,
tesletrassalidasinternas.estadoletras as estadoletras,
tesletrassalidasinternas.tessalidasinternas_id as tessalidasinternas_id 
,tesletrassalidasinternas.numerounico as numerounico 
,tesletrassalidasinternas.banco as banco,
'0' as origen ,
tesletrassalidasinternas.monto as monto ,
WEEKOFYEAR(tessalidasinternas.fvencimiento) as semana,
tesletrassalidasinternas.banco as ubicacion,
DATEDIFF('".date("Y-m-d")."',tessalidasinternas.fvencimiento) AS dd,
tessalidasinternas.fvencimiento as fvencimiento,
tessalidasinternas.femision as femision,
tesdatos.razonsocial as razonsocial
FROM tesletrassalidasinternas, tessalidasinternas,tesdatos WHERE tessalidasinternas.aclempresas_id=".Session::get('EMPRESAS_ID')." AND tesletrassalidasinternas.tessalidasinternas_id=tessalidasinternas.id AND tessalidasinternas.tesdatos_id=tesdatos.id".$cond_i."
)
ORDER BY fvencimiento, semana ASC");
		
	}
	
public function reporteFechas_mes($mes)
{/*PARA ver la semana que ingresa de la fecha*/

	$sql="(
SELECT tessalidas.tesmonedas_id as moneda,tesletrassalidas.id as id,
tesletrassalidas.estadoletras as estadoletras,
' ' as tessalidasinternas_id,
tesletrassalidas.tessalidas_id as tessalidas_id,
tesletrassalidas.numerounico as numerounico,
tesletrassalidas.banco as banco,
'1' as origen,
tessalidas.totalconigv as monto, 
tessalidas.numero as numero, 
WEEKOFYEAR(tessalidas.fvencimiento) as semana,
tesletrassalidas.banco as ubicacion, 
DATEDIFF('".date("Y-m-d")."',tessalidas.fvencimiento) AS dd,
tessalidas.fvencimiento as fvencimiento,
tesdatos.razonsocial as razonsocial
FROM tesletrassalidas, tessalidas,tesdatos  WHERE tesletrassalidas.estadoletras!='PAGADO' AND tessalidas.aclempresas_id=".Session::get('EMPRESAS_ID')." AND DATE_FORMAT(tessalidas.fvencimiento,'%Y-%m')='".$mes."' AND tesletrassalidas.tessalidas_id=tessalidas.id AND tessalidas.tesdatos_id=tesdatos.id )
UNION
(SELECT tessalidasinternas.tesmonedas_id as moneda,tesletrassalidasinternas.id as id,
tesletrassalidasinternas.estadoletras as estadoletras,
tesletrassalidasinternas.tessalidasinternas_id as tessalidasinternas_id,
' ' as tessalidas_id,
tesletrassalidasinternas.numerounico as numerounico,
tesletrassalidasinternas.banco as banco,
'0' as origen ,
tesletrassalidasinternas.monto as monto , 
tessalidasinternas.numero as numero,
WEEKOFYEAR(tessalidasinternas.fvencimiento) as semana,
tesletrassalidasinternas.banco as ubicacion,
DATEDIFF('".date("Y-m-d")."',tessalidasinternas.fvencimiento) AS dd,
tessalidasinternas.fvencimiento as fvencimiento,
tesdatos.razonsocial as razonsocial
FROM tesletrassalidasinternas, tessalidasinternas,tesdatos WHERE tesletrassalidasinternas.estadoletras!='PAGADO' AND tessalidasinternas.aclempresas_id=".Session::get('EMPRESAS_ID')." AND DATE_FORMAT(tessalidasinternas.fvencimiento,'%Y-%m')='".$mes."' AND tesletrassalidasinternas.tessalidasinternas_id=tessalidasinternas.id AND tessalidasinternas.tesdatos_id=tesdatos.id
)
ORDER BY fvencimiento, semana ASC";
	//echo $sql;
	return $this->find_all_by_sql($sql);
}

public function reporteFechas_mes_pagado($mes)
{/*PARA ver la semana que ingresa de la fecha*/

	$sql="(
SELECT tessalidas.tesmonedas_id as moneda,tesletrassalidas.id as id,
tesletrassalidas.estadoletras as estadoletras,
' ' as tessalidasinternas_id,
tesletrassalidas.tessalidas_id as tessalidas_id,
tesletrassalidas.numerounico as numerounico,
tesletrassalidas.banco as banco,
'1' as origen,
tessalidas.totalconigv as monto, 
tessalidas.numero as numero, tesletrassalidas.fecha_pago as fecha_pago,
tesletrassalidas.banco as ubicacion, 
DATEDIFF('".date("Y-m-d")."',tessalidas.fvencimiento) AS dd,
tessalidas.fvencimiento as fvencimiento,
tesdatos.razonsocial as razonsocial
FROM tesletrassalidas, tessalidas,tesdatos  WHERE tesletrassalidas.estadoletras='PAGADO' AND tessalidas.aclempresas_id=".Session::get('EMPRESAS_ID')." AND DATE_FORMAT(tesletrassalidas.fecha_pago,'%Y-%m')='".$mes."' AND tesletrassalidas.tessalidas_id=tessalidas.id AND tessalidas.tesdatos_id=tesdatos.id )
UNION
(SELECT tessalidasinternas.tesmonedas_id as moneda,tesletrassalidasinternas.id as id,
tesletrassalidasinternas.estadoletras as estadoletras,
tesletrassalidasinternas.tessalidasinternas_id as tessalidasinternas_id,
' ' as tessalidas_id,
tesletrassalidasinternas.numerounico as numerounico,
tesletrassalidasinternas.banco as banco,
'0' as origen ,
tesletrassalidasinternas.monto as monto , 
tessalidasinternas.numero as numero, tesletrassalidasinternas.fecha_pago as fecha_pago,
tesletrassalidasinternas.banco as ubicacion,
DATEDIFF('".date("Y-m-d")."',tessalidasinternas.fvencimiento) AS dd,
tessalidasinternas.fvencimiento as fvencimiento,
tesdatos.razonsocial as razonsocial
FROM tesletrassalidasinternas, tessalidasinternas,tesdatos WHERE tesletrassalidasinternas.estadoletras!='PAGADO' AND tessalidasinternas.aclempresas_id=".Session::get('EMPRESAS_ID')." AND DATE_FORMAT(tesletrassalidasinternas.fecha_pago,'%Y-%m')='".$mes."' AND tesletrassalidasinternas.tessalidasinternas_id=tessalidasinternas.id AND tessalidasinternas.tesdatos_id=tesdatos.id
)
ORDER BY fecha_pago,fvencimiento ASC";
	//echo $sql;
	return $this->find_all_by_sql($sql);
}
public function getResumenFechasMes($mes,$ms=1,$md=2)
{
	$sql="(SELECT 
(select IFNULL(sum(s.totalconigv),0) as total from tesletrassalidas as l, tessalidas as s where l.estadoletras!='PAGADO' AND s.aclempresas_id=tessalidas.aclempresas_id AND 
DATE_FORMAT(s.fvencimiento,'%Y-%m')=DATE_FORMAT(tessalidas.fvencimiento,'%Y-%m') AND 
l.tessalidas_id=s.id AND s.tesdatos_id=tesdatos.id AND s.tesmonedas_id=".$ms."
) as total_soles,
(select IFNULL(sum(s.totalconigv),0) as total from tesletrassalidas as l, tessalidas as s where l.estadoletras!='PAGADO' AND s.aclempresas_id=tessalidas.aclempresas_id AND 
DATE_FORMAT(s.fvencimiento,'%Y-%m')=DATE_FORMAT(tessalidas.fvencimiento,'%Y-%m') AND 
l.tessalidas_id=s.id AND s.tesdatos_id=tesdatos.id AND s.tesmonedas_id=".$md."
) as total_dolares,
tesletrassalidas.id as id, 
tesdatos.razonsocial as razonsocial 
FROM tesletrassalidas, tessalidas,tesdatos WHERE 
tesletrassalidas.estadoletras!='PAGADO' AND tessalidas.aclempresas_id=".Session::get('EMPRESAS_ID')." AND 
DATE_FORMAT(tessalidas.fvencimiento,'%Y-%m')='".$mes."' AND 
tesletrassalidas.tessalidas_id=tessalidas.id AND tessalidas.tesdatos_id=tesdatos.id 
GROUP BY tessalidas.tesdatos_id )
UNION
(SELECT 
(select IFNULL(sum(s.total),0) as total from tesletrassalidasinternas as l, tessalidasinternas as s where l.estadoletras!='PAGADO' AND s.aclempresas_id=tessalidasinternas.aclempresas_id AND 
DATE_FORMAT(s.fvencimiento,'%Y-%m')=DATE_FORMAT(tessalidasinternas.fvencimiento,'%Y-%m') AND 
l.tessalidasinternas_id=s.id AND s.tesdatos_id=tesdatos.id AND s.tesmonedas_id=".$ms."
) as total_soles,
(select IFNULL(sum(s.total),0) as total from tesletrassalidasinternas as l, tessalidasinternas as s where l.estadoletras!='PAGADO' AND s.aclempresas_id=tessalidasinternas.aclempresas_id AND 
DATE_FORMAT(s.fvencimiento,'%Y-%m')=DATE_FORMAT(tessalidasinternas.fvencimiento,'%Y-%m') AND 
l.tessalidasinternas_id=s.id AND s.tesdatos_id=tesdatos.id AND s.tesmonedas_id=".$md."
) as total_dolares,
tesletrassalidasinternas.id as id, 
tesdatos.razonsocial as razonsocial 
FROM tesletrassalidasinternas, tessalidasinternas,tesdatos 
WHERE 
tesletrassalidasinternas.estadoletras!='PAGADO' AND 
tessalidasinternas.aclempresas_id=".Session::get('EMPRESAS_ID')." AND 
DATE_FORMAT(tessalidasinternas.fvencimiento,'%Y-%m')='".$mes."' AND 
tesletrassalidasinternas.tessalidasinternas_id=tessalidasinternas.id AND 
tessalidasinternas.tesdatos_id=tesdatos.id GROUP BY tessalidasinternas.tesdatos_id ) ORDER BY razonsocial asc";
	//echo $sql;
	return $this->find_all_by_sql($sql);

}

public function Totalletrasporsemana($n,$fecha)/*$n=Numero de semana,$fecha=numero de semana*/
{
		$anio=date("Y", strtotime($fecha));
		/*echo "SELECT count(tesletrassalidas.id) as total FROM tesletrassalidas INNER JOIN tessalidas ON tesletrassalidas.tessalidas_id=tessalidas.id AND tessalidas.aclempresas_id=".Session::get('EMPRESAS_ID')." WHERE (estadosalida!='PAGADO' AND estadosalida!='ANULADO') AND  WEEKOFYEAR(tessalidas.fvencimiento)=".$n." AND YEAR(tessalidas.fvencimiento)='".$anio."' GROUP BY YEAR(tessalidas.fvencimiento)";
		echo "<br />";*/
		$M=$this->find_by_sql("SELECT count(tesletrassalidas.id) as total FROM tesletrassalidas INNER JOIN tessalidas ON tesletrassalidas.tessalidas_id=tessalidas.id AND tessalidas.aclempresas_id=".Session::get('EMPRESAS_ID')." WHERE (estadosalida!='PAGADO' OR estadosalida!='ANULADO') AND  WEEKOFYEAR(tessalidas.fvencimiento)=".$n." AND YEAR(tessalidas.fvencimiento)='".$anio."' GROUP BY YEAR(tessalidas.fvencimiento)");
		/*echo "SELECT count(tesletrassalidasinternas.id) as total_i FROM tesletrassalidasinternas INNER JOIN tessalidasinternas ON tesletrassalidasinternas.tessalidasinternas_id=tessalidasinternas.id AND  tessalidasinternas.aclempresas_id=".Session::get('EMPRESAS_ID')." WHERE (estadosalida!='PAGADO' AND estadosalida!='ANULADO') AND  WEEKOFYEAR(tessalidasinternas.fvencimiento)=".$n." AND YEAR(tessalidasinternas.fvencimiento)='".$anio."' GROUP BY YEAR(tessalidasinternas.fvencimiento)";*/
		$M_i=$this->find_by_sql("SELECT count(tesletrassalidasinternas.id) as total_i FROM tesletrassalidasinternas INNER JOIN tessalidasinternas ON tesletrassalidasinternas.tessalidasinternas_id=tessalidasinternas.id AND  tessalidasinternas.aclempresas_id=".Session::get('EMPRESAS_ID')." WHERE (estadosalida!='PAGADO' OR estadosalida!='ANULADO') AND  WEEKOFYEAR(tessalidasinternas.fvencimiento)=".$n." AND YEAR(tessalidasinternas.fvencimiento)='".$anio."' GROUP BY YEAR(tessalidasinternas.fvencimiento)");
		
		/*echo $M->total.'+'.$M_i->total_i."=";
		echo $M->total+$M_i->total_i;
		echo "<br />";*/
		return $M->total+$M_i->total_i;
	}
public function getTessalidasinternas()
	{
		return Load::model('tessalidasinternas')->find_first($this->id);
	}

/*NUMEROS DE FACTURAS DE LA TABLA SALIDAS PARA LETRAS POR COBRAR*/	
	public function getFacturas($facturas)
	{
		if($facturas!=0 && !empty($facturas)){
		$ids=explode(',',$facturas);
		$fac='';
		$n=0;
		foreach($ids as $item=>$value)
		{
			$salidas= new Tessalidas();
			$f=$salidas->find_first((int)$value);
			if($n==0)
			{
				$fac.='F/'.(int)$f->numero;
			}else
			{
				$fac.=','.(int)substr($f->numero,-3);
			}
			/*if($n==0)
			{
				$fac.='F/'.(int)substr($f->numero);
			}else
			{
				$fac.=','.(int)substr($f->numero);
			}*/
			$n++;
		}
		return $fac;}else{return 'F/0';}
	}

public function nrenovacion($numerodeletra)
{
	
	$maximos = $this->find_by_sql('SELECT (IFNULL(MAX(nrenovacion),0)) as renovacion FROM `Tesletrassalidas` WHERE numerodeletra='.$numerodeletra.' AND aclempresas_id='.Session::get('EMPRESAS_ID'));
	
	   $maximo = $maximos->renovacion+1;  
		switch($maximo) 
		{
			case $maximo<10: $maximo="0".$maximo; break;
			default: $maximo=$maximo;
		}
		return $maximo;
}	
/*CONSULTAS PARA REPORTES*/
public function getEmitidas($Y,$m)
{
	$sql="SELECT s.tesmonedas_id as tesmonedas_id,d.razonsocial as razonsocial,l.factura_id as factura_id,l.numerodeletra as numerodeletra, l.nrenovacion as nrenovacion, s.femision as femision,s.fvencimiento as fvencimiento,l.monto as monto,l.numerounico as numerounico, l.estadoletras as estado FROM tesdatos as d, tesletrassalidas as l 
LEFT JOIN tessalidas as s ON s.id=l.tessalidas_id
WHERE s.tesdatos_id=d.id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND DATE_FORMAT(s.femision,'%Y-%m')='".$Y."-".$m."' ORDER BY d.razonsocial,s.femision";
/*echo $sql;*/
	return $this->find_all_by_sql($sql);
}

public function getEstados()
{
	
}
public function getPorEstado($e='')
{
	
}
/*Para imprimir letras de una factura
 $factura_id => recibe el campo facturas_id
*/
public function getLetras_rtf($factura_id,$letra_id)
{
	$sql='SELECT CONCAT_WS("",l.numerodeletra,IFNULL(l.nrenovacion,"")) as numero,l.factura_id as factura,
"" as giro,
DATE_FORMAT(s.femision,"%Y") as a,
DATE_FORMAT(s.femision,"%m") as b,
DATE_FORMAT(s.femision,"%d") as c,
DATE_FORMAT(s.fvencimiento,"%Y") as d,
DATE_FORMAT(s.fvencimiento,"%m") as e,
DATE_FORMAT(s.fvencimiento,"%d") as f,
CONCAT_WS(" ",m.simbolo,s.totalconigv) as importe,
"" as moned_en_letras,
d.razonsocial as razonsocial,
d.ruc as ruc,
d.telefono as telefonos,
d.direccion as direccion,
m.simbolo as simbolo,
m.nombre as moneda,
s.totalconigv as total,
l.aval as aval,
l.avalruc as avalruc,
l.avaltelefono as avaltelefono,
l.avaldireccion as avaldireccion
FROM tesletrassalidas as l,tessalidas as s,tesdatos as d,tesmonedas as m WHERE l.tessalidas_id=s.id AND d.id=s.tesdatos_id AND s.tesmonedas_id=m.id AND l.id='.$letra_id;
	//echo $sql;
	return $this->find_all_by_sql($sql);
}
/*letras emitidas*/
public function get_letras($id,$anio,$estado)
{
	$sql="SELECT  'RUC' AS tipo, l.numerodeletra, s.femision, s.fvencimiento, l.banco, l.monto, s.estadosalida, l.estadoletras, l.numerounico, l.factura_id
FROM tessalidas AS s
INNER JOIN tesletrassalidas AS l ON s.id = l.tessalidas_id AND s.aclempresas_id=".Session::get("EMPRESAS_ID")."
INNER JOIN tesdatos AS d ON d.id = s.tesdatos_id
AND d.id = ".$id."
WHERE s.tesdatos_id = ".$id." AND DATE_FORMAT(s.femision,'%Y')=".$anio." AND l.estadoletras='".$estado."'
UNION 
SELECT 'INTERNO' AS tipo, l.numerodeletra, s.femision, s.fvencimiento, l.banco, l.monto, s.estadosalida, l.estadoletras, l.numerounico, l.factura_id
FROM tessalidasinternas AS s
INNER JOIN tesletrassalidasinternas AS l ON s.id = l.tessalidasinternas_id AND s.aclempresas_id=".Session::get("EMPRESAS_ID")."
INNER JOIN tesdatos AS d ON d.id = s.tesdatos_id
AND d.id = ".$id."
WHERE s.tesdatos_id = ".$id."  AND DATE_FORMAT(s.femision,'%Y')=".$anio." AND l.estadoletras='".$estado."'
ORDER BY femision,numerodeletra";
	//echo $sql;
	return $this->find_all_by_sql($sql);
}


}
?>