<?php
Load::models('tesordendecompras','tesdetalleordenes','tesabonosdetalles');
class Tessalidas extends ActiveRecord {
static function zero_fill ($valor, $long = 0)
{
	    return str_pad($valor, $long, '0', STR_PAD_LEFT);
}
public function initialize()
{
	//relaciones
	$this->has_many('tesdetallesalidas','tesfaturasadelantos','teschequessalidas','prodetalletransportes','tesabonosdetalles');
	$this->belongs_to('aclusuarios','tesmonedas','tesdatos','tesdocumentos','aclempresas','tescuentascorrientes','tescondicionespagos','testipocambios','tesdatosdirecciones','tesexportaciones');
	$this->has_one('proprocesos','tesletrassalidas');
}

public function before_save()
{
	/*$rs = $this->find_first("cedula = $this->cedula");*/
	if($this->tesdocumentos_id==34)
	{
		if($this->fvencimiento<$this->femision)
		{
			Flash::warning("La fecha de vencimiento no puede ser menor a la fecha de emision");
			return 'cancel';
		}
	}
}
public function generarNumero($documento,$serie='001')
{
	if(Auth::get('id')==3)echo "serie--->".$serie;
	$maximo = $this->maximum("numero", "conditions: serie='".$serie."' AND tesdocumentos_id=".$documento." AND estado='1' AND aclempresas_id =".Session::get('EMPRESAS_ID'));
	if(Auth::get('id')==3)echo "conditions: serie='".$serie."' AND tesdocumentos_id=".$documento." AND estado='1' AND aclempresas_id =".Session::get('EMPRESAS_ID');
	if(empty($maximo))
	{
		$maximo=1;
	}else{
		if(Auth::get('id')==3)echo "maximo-->".$maximo;
		$maximo=$maximo+1;
	}
	/*function zero_fill ($valor, $long = 0)
	{
	    return str_pad($valor, $long, '0', STR_PAD_LEFT);
	}*/
	$maximo=$this->zero_fill($maximo,7);
	if(Auth::get('id')==3)echo 'max_nuew->'.$maximo;
	/*switch($maximo)
	{
		case $maximo<10: $maximo="0000000".$maximo; break;
		case $maximo<100: $maximo="000000".$maximo; break;
		case $maximo<1000: $maximo="00000".$maximo; break;
		case $maximo<10000: $maximo="0000".$maximo; break;
		case $maximo<100000: $maximo="000".$maximo; break;
		case $maximo<1000000: $maximo="00".$maximo; break;
		case $maximo<10000000: $maximo="0".$maximo; break;
		default: $maximo=$maximo;
	}*/
	return $maximo;
}

/*GENERAR NUMERO PARA CONFECCIONES Y TINTORERIA*/
public function generarNumero_interno($documento,$serie='999',$prefijo='')
{
	$maximo = $this->maximum("numero", "conditions: SUBSTRING( npedido, 1, 2 )='".$prefijo."' AND serie='".$serie."' AND tesdocumentos_id=".$documento." AND (estado='1' OR estado='9') AND aclempresas_id =".Session::get('EMPRESAS_ID'));
	if(empty($maximo))
	{
		$maximo=1;
	}else{
		$maximo=$maximo+1;
	}
	function zero_fill ($valor, $long = 0)
	{
	    return str_pad($valor, $long, '0', STR_PAD_LEFT);
	}
	$maximo=$this->zero_fill($maximo,7);

	/*switch($maximo)
	{
		case $maximo<10: $maximo="000000".$maximo; break;
		case $maximo<100: $maximo="00000".$maximo; break;
		case $maximo<1000: $maximo="0000".$maximo; break;
		case $maximo<10000: $maximo="000".$maximo; break;
		case $maximo<100000: $maximo="00".$maximo; break;
		case $maximo<1000000: $maximo="0".$maximo; break;
		default: $maximo=$maximo;
	}*/
	return $maximo;
}

public function generarNumeroLetras()
{
	$maximo = $this->maximum("numero", "conditions: tesdocumentos_id=34 AND estado='1' AND aclempresas_id =".Session::get('EMPRESAS_ID'));
	if(empty($maximo))
	{
		$maximo=1;
	}else{
		$maximo=$maximo+1;
	}

	
	$maximo=$this->zero_fill($maximo,3);
	/*switch($maximo)
	{
		case $maximo<10: $maximo="000".$maximo; break;
		case $maximo<100: $maximo="00".$maximo; break;
		case $maximo<1000: $maximo="0".$maximo; break;
		default: $maximo=$maximo;
	}*/
	return $maximo;
}

public function generarNumerocheque($cuentacorriente)
{
	$maximo = $this->maximum("numero", "conditions: tescuentascorrientes_id=".$cuentacorriente." AND tesdocumentos_id=35 AND aclempresas_id =".Session::get('EMPRESAS_ID'));
	if(empty($maximo))
	{
		$maximo=1;
	}else{
		$maximo=$maximo+1;
	}


	$maximo=$this->zero_fill($maximo,9);
	/*switch($maximo)
	{
		case $maximo<10: $maximo="00000000".$maximo; break;
		case $maximo<100: $maximo="0000000".$maximo; break;
		case $maximo<1000: $maximo="000000".$maximo; break;
		case $maximo<10000: $maximo="00000".$maximo; break;
		case $maximo<100000: $maximo="0000".$maximo; break;
		case $maximo<1000000: $maximo="000".$maximo; break;
		case $maximo<10000000: $maximo="00".$maximo; break;
		case $maximo<100000000: $maximo="0".$maximo; break;
		default: $maximo=$maximo;
	}*/
	return $maximo;
}
	
public function getNumeropedido($prefijo='',$serie='002')
{
	$maximos = $this->find_by_sql('SELECT (IFNULL(MAX(SUBSTRING(npedido,4)),0)) as npedido FROM `tessalidas` WHERE SUBSTRING( npedido, 1, 2 )="'.$prefijo.'" AND serie="'.$serie.'" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
/*echo 'pedido '.$maximos->npedido;*/
	$maximo=$maximos->npedido+1;

		$maximo=$this->zero_fill($maximo,5);

	/*switch($maximo)
	{
		case $maximo<10: $maximo="0000".$maximo; break;
		case $maximo<100: $maximo="000".$maximo; break;
		case $maximo<1000: $maximo="00".$maximo; break;
		case $maximo<10000: $maximo="0".$maximo; break;
		default: $maximo=$maximo;
	}*/
	return $prefijo.'-'.$maximo;
}

public function generarNumeroFactura($serie='F002')
{
	$maximos = $this->find_by_sql('SELECT (IFNULL(MAX(SUBSTRING(numerofactura,5)),0)) as numerofactura FROM `tessalidas` WHERE serie="'.$serie.'" AND tesdocumentos_id=15 AND aclempresas_id='.Session::get('EMPRESAS_ID'));
	$numero=$this->maximum('numero','conditions: tesdocumentos_id=7 AND serie="'.$serie.'" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
	$maximo=$maximos->numerofactura;
	echo 'numero '.$numero;
	echo "<br />";
	echo 'numerofactura '.$maximo;
	if($maximo<$numero)$maximo=(int)$numero;

	if(empty($maximo))
	{
		$maximo=1;
	}else{
		$maximo=$maximo+1;
	}
	$maximo=$this->zero_fill($maximo,7);
	/*switch($maximo)
	{
		case $maximo<10: $maximo="000000".$maximo; break;
		case $maximo<100: $maximo="00000".$maximo; break;
		case $maximo<1000: $maximo="0000".$maximo; break;
		case $maximo<10000: $maximo="000".$maximo; break;
		case $maximo<100000: $maximo="00".$maximo; break;
		case $maximo<1000000: $maximo="0".$maximo; break;
		default: $maximo=$maximo;
	}*/
	return $serie.'-'.$maximo;
}
	
public function getCambio($fecha)
{
	$tipocambios= new Testipocambios();
	$cambio=$tipocambios->find_first('conditions: fecha="'.$fecha.'"');
	if($cambio)
	{
		$id=$cambio->id;
	}else
	{
		$id=$tipocambios->maximum('id');
	}
	return $id;
}	

public function getDetallesorden($codigo)
{
	$orden=new Tesordendecompras();
	if($orden->exists('codigo='.$codigo)){
	$or=$orden->find_first('conditions: codigo='.$codigo);
	$detalles=new Tesdetalleordenes();
	return $detalles->find('conditions: tesordendecompras_id='.(int)$or->id);
	}else
	{
		return $detalles=array();
	}
}
/* el total de la factura = a la resta de total-igv con la suma de todas las aplicaciones */

public function getTotal_a($id)
{
	$a=$this->find_by_sql('SELECT ((tessalidas.totalconigv-tessalidas.igv)-(sum(tesaplicacionfacturasadelantos.montototal))) as total FROM `tessalidas` INNER JOIN tesaplicacionfacturasadelantos ON tesaplicacionfacturasadelantos.tessalidas_id=tessalidas.id AND tesaplicacionfacturasadelantos.tessalidas_id='.$id);
	if(empty($a->total)) $t=0;else $t=$a->total;
	return $t;
}

/*Para la aplicacion*/
public function getTotal_aplicacion($id)
{
	$a=$this->find_by_sql('SELECT ((tesdetallesalidas.importe)-IFNULL((sum(tesaplicacionfacturasadelantos.montototal)),0)) as t FROM `tessalidas` INNER JOIN tesaplicacionfacturasadelantos ON tesaplicacionfacturasadelantos.tessalidas_id=tessalidas.id AND tesaplicacionfacturasadelantos.tessalidas_id='.$id.'');
	if(empty($a->t)) $t=0;else $t=(float)$a->t;
	return $t;
}

/*Todos los documentos pendientos de cobro facturas, letras, letrasinternas salidasinternas, salidas */
public function getPendientescobro($id=0,$conditions,$id_p)
{
	$detraccion='';
	$detraccion_i='';
	$estado='';
	if($id_p==14)
	{
		$estado=" or s.estadosalida='TERMINADO '";
	}
	if($id_p==10)
	{
		$detraccion=' AND s.totalconigv>700';
		$detraccion_i=' AND s.total>700';
	}
	$sql="(SELECT 'RUC' as origen, s.id as id,
s.tesmonedas_id as tesmonedas_id,s.tesdatos_id as tesdatos_id,
s.direccion_entrega as direccion_entrega,s.tesdocumentos_id as tesdocumentos_id,
s.testipocambios_id as testipocambios_id,
s.estadosalida as estadosalida,
CONCAT_WS('-',s.serie,s.numero) as numero,
s.femision as femision,s.fvencimiento as fvencimiento,
s.igv as igv,s.totalconigv as totalconigv FROM tessalidas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND (s.tesdocumentos_id!=15 AND s.tesdocumentos_id!=35  AND s.tesdocumentos_id!=34) AND (s.estadosalida='Pendiente' OR s.estadosalida='ADELANTADO'".$estado.") AND s.totalconigv>0 AND tesdatos_id=$id ".$conditions.$detraccion.")
union(
SELECT 'INT' as origen, s.id as id,
s.tesmonedas_id as tesmonedas_id,s.tesdatos_id as tesdatos_id,
s.direccion_entrega as direccion_entrega,s.tesdocumentos_id as tesdocumentos_id,
s.testipocambios_id as testipocambios_id,
s.estadosalida as estadosalida,
CONCAT_WS('-',s.serie,s.numero) as numero,
s.femision as femision,s.fvencimiento as fvencimiento,
'0' as igv,s.total as totalconigv FROM tessalidasinternas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND (s.tesdocumentos_id!=35 AND s.tesdocumentos_id!=34 ) AND (s.estadosalida='Pendiente' OR s.estadosalida='ADELANTADO'".$estado.") AND s.total>0 AND tesdatos_id=$id ".$conditions.$detraccion_i.") order by femision ASC";
	//echo $sql;
	return $this->find_all_by_sql($sql);

}

public function getLetrassinabono($id=0,$conditions,$id_p)
{
	$detraccion='';
	$detraccion_i='';
	if($id_p==10)
	{
		$detraccion=' AND s.totalconigv>700';
		$detraccion_i=' AND s.total>700';
	}
	return $this->find_all_by_sql("Select 'RUC' as origen, s.id as id,
s.tesmonedas_id as tesmonedas_id,s.tesdatos_id as tesdatos_id,
s.direccion_entrega as direccion_entrega,s.tesdocumentos_id as tesdocumentos_id,
s.testipocambios_id as testipocambios_id,
s.estadosalida as estadosalida,
CONCAT_WS('-',s.serie,s.numero) as numero,
s.femision as femision,s.fvencimiento as fvencimiento,
s.igv as igv,s.totalconigv as totalconigv 
from tessalidas as s,tesletrassalidas as l 
where not exists (select a.id from tesabonosdetalles as a WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tessalidas_id = l.tessalidas_id) AND s.id=l.tessalidas_id AND s.totalconigv>0 AND s.tesdatos_id=$id ".$conditions.$detraccion_i."
UNION ALL
SELECT 'INT' as origen, s.id as id,
s.tesmonedas_id as tesmonedas_id,s.tesdatos_id as tesdatos_id,
s.direccion_entrega as direccion_entrega,s.tesdocumentos_id as tesdocumentos_id,
s.testipocambios_id as testipocambios_id,
s.estadosalida as estadosalida,
CONCAT_WS('-',s.serie,s.numero) as numero,
s.femision as femision,s.fvencimiento as fvencimiento,
'0' as igv,s.total as totalconigv from tessalidasinternas as s,tesletrassalidasinternas as l 
where not exists (select a.id from tesabonosdetalles as a WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tessalidasinternas_id = l.tessalidasinternas_id) AND s.id=l.tessalidasinternas_id AND s.total>0 AND s.tesdatos_id=$id ".$conditions.$detraccion_i." order by femision ASC");
	
}

/*CONSULTAS PARA LOS REPORTES*/
public function ventasMes($fecha)
{
	/*
	En este reporte se listaran todas las ventas ya sean intern o externas
	*/
	return $this->find_all_by_sql("(SELECT 'RUC' as origen, s.id as id,
s.tesmonedas_id as tesmonedas_id,s.tesdatos_id as tesdatos_id,
s.direccion_entrega as direccion_entrega,s.tesdocumentos_id as tesdocumentos_id,
s.testipocambios_id as testipocambios_id,
s.estadosalida as estadosalida,
CONCAT_WS('-',s.serie,s.numero) as numero,
s.femision as femision,s.fvencimiento as fvencimiento,
s.igv as igv,s.totalconigv as totalconigv,s.estadosalida as estado 
FROM tessalidas as s 
WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdocumentos_id=7 AND DATE_FORMAT(s.femision,'%Y-%m')='".$fecha."')
union(
SELECT 'INT' as origen, s.id as id,
s.tesmonedas_id as tesmonedas_id,s.tesdatos_id as tesdatos_id,
s.direccion_entrega as direccion_entrega,s.tesdocumentos_id as tesdocumentos_id,
s.testipocambios_id as testipocambios_id,
s.estadosalida as estadosalida,
CONCAT_WS('-',s.serie,s.numero) as numero,
s.femision as femision,s.fvencimiento as fvencimiento,
'0' as igv,s.total as totalconigv,s.estadosalida as estado FROM tessalidasinternas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdocumentos_id=7 OR s.tesdocumentos_id=15 AND npedido like 'VT-%' AND DATE_FORMAT(s.femision,'%Y-%m')='".$fecha."')  order by femision ASC");
}
public function getIgvpotmes($fecha,$igv=0)
{
	$s_soles=0;
	$s_igv=0;
	$i_soles=0;
	$i_igv=0;
	$ss=$this->find_all_by_sql("SELECT sum(totalconigv) as totalconigv, sum(igv) as igv, s.tesmonedas_id as moneda, s.testipocambios_id as testipocambios_id FROM `tessalidas` as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.estadosalida!='ANULADO' AND s.tesdocumentos_id=7 AND DATE_FORMAT(s.femision,'%Y-%m')='".$fecha."' GROUP BY moneda, testipocambios_id");
	foreach($ss as $s)
	{
		$monedas= $s->moneda;
		switch ($monedas)
		{
			case 1: $simbolo='S/. ';
			(float)$monto_soles=(float)$s->totalconigv;
			(float)$monto_dolares=(float)$s->totalconigv/(float)$s->getTestipocambios()->compra;
			(float)$monto_igv=(float)$s->igv;
			; break;
			case 2: $simbolo='$. ';
			(float)$monto_soles=(float)$s->totalconigv*(float)$s->getTestipocambios()->compra;
			(float)$monto_dolares=(float)$s->totalconigv;
			(float)$monto_igv=(float)$s->igv*(float)$s->getTestipocambios()->compra;
			break;
			case 4: $simbolo='S/. ';
			(float)$monto_soles=(float)$s->totalconigv;
			(float)$monto_dolares=(float)$s->totalconigv/(float)$s->getTestipocambios()->compra;
			(float)$monto_igv=(float)$s->igv;
			; break;
			case 5: $simbolo='$. ';
			(float)$monto_soles=(float)$s->totalconigv*(float)$s->getTestipocambios()->compra;
			(float)$monto_dolares=(float)$s->totalconigv;
			(float)$monto_igv=(float)$s->igv*(float)$s->getTestipocambios()->compra;
			  break;
			case 0: $simbolo='S/. ';
			(float)$monto_soles=(float)$s->totalconigv;
			(float)$monto_dolares=(float)$s->totalconigv/(float)$s->getTestipocambios()->compra;
			(float)$monto_igv=(float)$s->igv;
			; break;
		}
			
		(float)$s_soles=(float)$s_soles+(float)$monto_soles;
		(float)$s_igv=(float)$s_igv+(float)$monto_igv;	
	}
	$ii=$this->find_all_by_sql("SELECT sum(i.totalconigv) as totalconigv,sum(i.igv) as igv, i.tesmonedas_id as moneda,i.testipocambios_id as testipocambios_id   FROM `tesingresos` as i WHERE i.aclempresas_id=".Session::get('EMPRESAS_ID')." AND i.tesdocumentos_id=7 AND DATE_FORMAT(i.femision,'%Y-%m')='".$fecha."' GROUP BY moneda, testipocambios_id");
	foreach($ii as $i)
	{
		$monedas= $i->moneda;
		switch ($monedas)
		{
			case 1:
			(float)$monto_soles=(float)$i->totalconigv;
			(float)$monto_dolares=(float)$i->totalconigv/(float)$i->getTestipocambios()->compra;
			(float)$monto_igv=(float)$i->igv;
			; break;
			case 2:
			(float)$monto_soles=(float)$i->totalconigv*(float)$i->getTestipocambios()->compra;
			(float)$monto_dolares=(float)$i->totalconigv;
			(float)$monto_igv=(float)$i->igv*(float)$i->getTestipocambios()->compra;
			  break;
			case 4:
			(float)$monto_soles=(float)$i->totalconigv;
			(float)$monto_dolares=(float)$i->totalconigv/(float)$i->getTestipocambios()->compra;
			(float)$monto_igv=(float)$i->igv;
			; break;
			case 5:
			(float)$monto_soles=(float)$i->totalconigv*(float)$i->getTestipocambios()->compra;
			(float)$monto_dolares=(float)$i->totalconigv;
			(float)$monto_igv=(float)$i->igv*(float)$i->getTestipocambios()->compra;
			  break;
			case 0:
			(float)$monto_soles=(float)$i->totalconigv;
			(float)$monto_dolares=(float)$i->totalconigv/(float)$i->getTestipocambios()->compra;
			(float)$monto_igv=(float)$i->igv;
			; break;
		}
		
		(float)$i_soles=(float)$i_soles+(float)$monto_soles;
		(float)$i_igv=(float)$i_igv+(float)$monto_igv;
	}	
	if($igv==0)
	{
		return array("ST"=>(float)$s_soles,"SI"=>(float)$s_igv,"IT"=>(float)$i_soles,"II"=>(float)$i_igv);
	}else
	{
		return (float)$s_igv-(float)$i_igv;
	}
}

public function getVentasDatos($id=0)
{
	$cond='';
	if($id!=0)$cond=' AND c.id='.$id;
	return $this->find_all_by_sql("SELECT c.id as datos_id, c.razonsocial as razonsocial, c.ruc as ruc, s.* FROM  tesdatos as c, tessalidas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND c.testipodatos_id=2 AND c.id=s.tesdatos_id AND (s.tesdocumentos_id!=15 AND s.tesdocumentos_id!=34 AND s.tesdocumentos_id!=35 ) AND s.estadosalida='Pendiente'".$cond." ORDER BY `c`.`razonsocial` ASC");
}

public function getVentas_anual($anio,$c,$d)
{
if(Session::get('EMPRESAS_ID')=='1')$moneda=1;else $moneda=4;;

$sql="SELECT d.*,
(select ifnull(sum(if(s_i.tesmonedas_id=".$moneda.",ifnull(s_i.total,0),(select d.total*c.compra from tessalidasinternas as d, testipocambios as c WHERE d.testipocambios_id=c.id AND d.id=s_i.id))),0) from tessalidasinternas as s_i WHERE s_i.tesdatos_id=d.id AND date_format(s_i.femision,'%Y')=".$anio." AND s_i.npedido like 'VT-%' AND s_i.tesdocumentos_id=15 AND s_i.estadosalida!='ANULADO') as venta_interna,
(select ifnull(sum(if(s.tesmonedas_id=".$moneda.",ifnull(s.totalconigv,0),(select d.totalconigv*c.compra from tessalidas as d, testipocambios as c WHERE d.testipocambios_id=c.id AND d.id=s.id))),0) from tessalidas as s WHERE s.tesdatos_id=d.id AND date_format(s.femision,'%Y')=".$anio." AND s.tesdocumentos_id=7 AND s.estadosalida!='ANULADO') as venta_ruc,
((select ifnull(sum(if(s_i.tesmonedas_id=".$moneda.",ifnull(s_i.total,0),(select d.total*c.compra from tessalidasinternas as d, testipocambios as c WHERE d.testipocambios_id=c.id AND d.id=s_i.id))),0) from tessalidasinternas as s_i WHERE s_i.tesdatos_id=d.id AND date_format(s_i.femision,'%Y')=".$anio." AND s_i.npedido like 'VT-%' AND s_i.tesdocumentos_id=15 AND s_i.estadosalida!='ANULADO')+(select ifnull(sum(if(s.tesmonedas_id=".$moneda.",ifnull(s.totalconigv,0),(select d.totalconigv*c.compra from tessalidas as d, testipocambios as c WHERE d.testipocambios_id=c.id AND d.id=s.id))),0) from tessalidas as s WHERE s.tesdatos_id=d.id AND date_format(s.femision,'%Y')=".$anio." AND s.tesdocumentos_id=7 AND s.estadosalida!='ANULADO')) as total 
FROM `tessalidas`,tesdatos as d WHERE d.id=tessalidas.tesdatos_id AND date_format(tessalidas.femision,'%Y')=".$anio." AND tessalidas.tesdocumentos_id=7 AND tessalidas.aclempresas_id=".Session::get('EMPRESAS_ID')." GROUP BY tessalidas.tesdatos_id 
UNION 
SELECT d.*,

(select ifnull(sum(if(s_i.tesmonedas_id=".$moneda.",ifnull(s_i.total,0),(select d.total*c.compra from tessalidasinternas as d, testipocambios as c WHERE d.testipocambios_id=c.id AND d.id=s_i.id))),0) from tessalidasinternas as s_i WHERE s_i.tesdatos_id=d.id AND date_format(s_i.femision,'%Y')=".$anio." AND s_i.npedido like 'VT-%' AND s_i.tesdocumentos_id=15 AND s_i.estadosalida!='ANULADO') as venta_interna,
(select ifnull(sum(if(s.tesmonedas_id=".$moneda.",ifnull(s.totalconigv,0),(select d.totalconigv*c.compra from tessalidas as d, testipocambios as c WHERE d.testipocambios_id=c.id AND d.id=s.id))),0) from tessalidas as s WHERE s.tesdatos_id=d.id AND date_format(s.femision,'%Y')=".$anio." AND s.tesdocumentos_id=7 AND s.estadosalida!='ANULADO') as venta_ruc,

((select ifnull(sum(if(s_i.tesmonedas_id=".$moneda.",ifnull(s_i.total,0),(select d.total*c.compra from tessalidasinternas as d, testipocambios as c WHERE d.testipocambios_id=c.id AND d.id=s_i.id))),0) from tessalidasinternas as s_i WHERE s_i.tesdatos_id=d.id AND date_format(s_i.femision,'%Y')=".$anio." AND s_i.npedido like 'VT-%' AND s_i.tesdocumentos_id=15 AND s_i.estadosalida!='ANULADO')+(select ifnull(sum(if(s.tesmonedas_id=".$moneda.",ifnull(s.totalconigv,0),(select d.totalconigv*c.compra from tessalidas as d, testipocambios as c WHERE d.testipocambios_id=c.id AND d.id=s.id))),0) from tessalidas as s WHERE s.tesdatos_id=d.id AND date_format(s.femision,'%Y')=".$anio." AND s.tesdocumentos_id=7 AND s.estadosalida!='ANULADO')) as total 
FROM `tessalidasinternas`, tesdatos as d WHERE d.id=tessalidasinternas.tesdatos_id AND date_format(tessalidasinternas.femision,'%Y')=".$anio." AND tessalidasinternas.npedido like 'VT-%' AND tessalidasinternas.aclempresas_id=".Session::get('EMPRESAS_ID')." GROUP BY tessalidasinternas.tesdatos_id ORDER BY $c $d";
//echo $sql;
//echo $sql_1;
	return $this->find_all_by_sql($sql);
}


/*reporte semanal*/
public function getVentasALLDatos($id=0,$fecha)
{
	$cond='';
	if($id!=0)$cond=' AND c.id='.$id;
	//ADELANTADO Pendiente s.tesdocumentos_id!=14 AND s.estadosalida='TERMINADO' OR 
	$sql="SELECT s.tesdatosdirecciones_id as direccion_entrega, s.tesdocumentos_id as tesdocumentos_id, s.tesdatos_id as tesdatos_id,WEEKOFYEAR(s.femision) as semana,s.estadosalida,estadosalida, 'ruc' as origen, s.femision as fecha,c.sufijo as sufijo,c.codigo_ant AS codigo_ant,c.codigo AS codigo, c.tesvendedores_id AS tesvendedores_id, s.id as id,c.id as datos_id, c.razonsocial as razonsocial, c.ruc as ruc, s.tesmonedas_id as tesmonedas_id, s.totalconigv as total,'0.00' as total_a, concat_ws('-',s.serie,s.numero) as numerofactura, s.numeroguia as numeroguia, (SELECT (f.montototal-IFNULL(sum(a.montototal),0))*(1+".Session::get("IGV").") FROM tesfacturasadelantos as f LEFT JOIN tesaplicacionfacturasadelantos as a ON a.tesfacturasadelantos_id=f.id WHERE f.tessalidas_id=s.id) as pa
FROM tesdatos as c, tessalidas as s 
WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.femision<='".$fecha."' AND c.testipodatos_id=2 AND c.id=s.tesdatos_id AND (s.tesdocumentos_id!=15 AND s.tesdocumentos_id!=34 AND s.tesdocumentos_id!=35 ) AND s.estadosalida!='ANULADO' AND s.npedido NOT LIKE 'MT-%' AND (s.estadosalida='ADELANTADO' OR s.estadosalida='Pendiente' OR (SELECT f.montototal-IFNULL(sum(a.montototal),0) FROM tesfacturasadelantos as f LEFT JOIN tesaplicacionfacturasadelantos as a ON a.tesfacturasadelantos_id=f.id WHERE f.tessalidas_id=s.id)>1 OR (SELECT count(id) as n FROM tesletrassalidas as l WHERE FIND_IN_SET(s.id, factura_id) AND l.estadoletras='Sin Numero')>0
)".$cond." 
union 
SELECT s.tesdatosdirecciones_id as direccion_entrega, s.tesdocumentos_id as tesdocumentos_id, s.tesdatos_id as tesdatos_id,WEEKOFYEAR(s.femision) as semana,s.estadosalida,estadosalida, 'interna' as origen, s.femision as fecha,c.sufijo as sufijo,c.codigo_ant AS codigo_ant,c.codigo AS codigo, c.tesvendedores_id AS tesvendedores_id, s.id as id,c.id as datos_id, c.razonsocial as razonsocial, c.ruc as ruc, s.tesmonedas_id as tesmonedas_id , s.total as total ,(select sum(importe) from tesdetallesalidasinternas WHERE tesdetallesalidasinternas.tessalidasinternas_id=s.id) as total_a,concat_ws('-',s.serie,s.numero) as numerofactura, s.numero as numeroguia, NULL as pa
FROM tesdatos as c, tessalidasinternas as s 
WHERE  s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.femision<='".$fecha."'  AND s.npedido NOT LIKE 'MT-%' AND  c.testipodatos_id=2 AND c.id=s.tesdatos_id AND (s.tesdocumentos_id!=34 AND s.tesdocumentos_id!=35 ) AND s.estadosalida!='ANULADO' AND (s.estadosalida='ADELANTADO' OR s.estadosalida='Pendiente')".$cond."
ORDER BY razonsocial,tesmonedas_id,direccion_entrega,fecha,origen ASC";
	return $this->find_all_by_sql($sql);
}

/*REPROTE DE GUIAS EN TINTORERIA*/
public function getGuiasT($id=0,$serie='')
{
	$con_t='';
	if($serie!='')$con_t=' AND s.serie='.$serie;
	$cond='';
	if($id!=0)$cond=' AND c.id='.$id;
	
	return $this->find_all_by_sql("SELECT pr.id as procesos_id,concat_ws(' ',p.nombre) as descripcion , p.codigo_ant as art, r.estadorollo as estadorollo, c.id as datos_id, c.razonsocial as razonsocial, c.ruc as ruc,sum(d.pesoneto) as pesoneto,sum(d.cantidad) as metros, pr.tescolores_id as color,r.tescolores_id as color_r, s.* FROM proprocesos AS pr,tesproductos as p, tesdatos as c, prorollos as r, tessalidas as s INNER JOIN tesdetallesalidas as d ON s.id=d.tessalidas_id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')."
WHERE pr.tessalidas_id=s.id AND p.id=r.tesproductos_id AND d.prorollos_id=r.id AND c.id=s.tesdatos_id AND s.npedido like 'TI%' AND s.tesdocumentos_id=15 AND (s.estadosalida!='TERMINADO' AND s.estadosalida!='ANULADO')  AND r.estadorollo='Tintoreria'".$con_t." GROUP BY d.tessalidas_id ORDER BY c.id,`s`.`femision` ASC ");
}

public function getGuiasR($id=0,$serie='')
{
	$con_t='';
	if($serie!='')$con_t=' AND s.serie='.$serie;
	$cond='';
	if($id!=0)$cond=' AND c.id='.$id;
	
	return $this->find_all_by_sql("SELECT pr.id as procesos_id,p.nombre as descripcion , p.codigo_ant as art, r.estadorollo as estadorollo, c.id as datos_id, c.razonsocial as razonsocial, c.ruc as ruc,sum(d.pesoneto) as pesoneto,sum(d.cantidad) as metros, pr.tescolores_id as color, concat_ws(' ',r.observacion_control,pr.detalle) as observacion,s.* FROM proprocesos AS pr,tesproductos as p, tesdatos as c, prorollos as r, tessalidas as s INNER JOIN tesdetallesalidas as d ON s.id=d.tessalidas_id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')."
WHERE pr.tessalidas_id=s.id AND p.id=r.tesproductos_id AND d.prorollos_id=r.id AND c.id=s.tesdatos_id AND s.npedido like 'TI%' AND s.tesdocumentos_id=15 AND (s.estadosalida!='TERMINADO' AND s.estadosalida!='ANULADO')  AND r.estadorollo='Tintoreria-2'".$con_t." GROUP BY d.tessalidas_id ORDER BY c.id,`s`.`femision` ASC ");
}
/* obetener los procesos de las guias 
@ id => es el id de la tabla proprocesos_id;
*/

public function getdetalle_procesos()
{
	$acabados=$this->find_all_by_sql("SELECT a.nombre,da.id,a.id FROM prodetalleacabados as da INNER JOIN proacabados as a ON a.id=da.proacabados_id 
WHERE a.tipo_acabado=0 AND da.proprocesos_id=".$this->procesos_id." AND a.id!=3");
	$acabado=array();
	foreach($acabados as $a):
	$acabado[$a->id]=$a->nombre;
	endforeach;
	return implode(",", $acabado);
}

public function anularfactura($id)
{
	$salida=$this->find_first($id);
	$t_guias=explode(',',$salida->numeroguia);
	count($t_guias);
	if(count($t_guias)>1)
	{
		foreach($t_guias as $value=>$item):
		$s_n=explode('-',$item);
		$guia=$this->find_first('conditions: serie="'.$s_n[0].'" AND numero="'.$s_n[1].'"');
		$guia->estadosalida='Pendiente';
		$guia->save();
		endforeach;
		$salida->estadosalida="ANULADO";
		$salida->activo='0';
	$salida->totalconigv=0;
	$salida->igv=0;
		$salida->userid=Auth::get('id');
		if($salida->save())
		{
			/*ANular las applicaciones a facturas si hubiera*/
			$app=Load::model('tesaplicacionfacturasadelantos');
			$apps=$app->find('conditions: tessalidas_id='.$id);
			foreach($apps as $ap):
			$app->find_first((int)$ap->id);
			$app->estado='0';
			$app->save();			
			endforeach;
				
			Acciones::add("Colocó el ".$salida->serie."-".$salida->numero." ({$salida->getTesdocumentos()->nombre}) como ANULADO",'tessalidas');
				
		return TRUE;
		}
	}else
	{
		if(!empty($salida->numeroguia))
		{
			$s_n=explode('-',$salida->numeroguia);
			$guia=$this->find_first('conditions: serie="'.$s_n[0].'" AND numero="'.$s_n[1].'"');
			$guia->estadosalida='Pendiente';
			$guia->save();
		}
		$salida->estadosalida="ANULADO";
		$salida->activo='0';
	$salida->totalconigv=0;
	$salida->igv=0;
		$salida->userid=Auth::get('id');
		if($salida->save())
		{
			/*ANular las applicaciones a facturas si hubiera*/
			$app=Load::model('tesaplicacionfacturasadelantos');
			$apps=$app->find('conditions: tessalidas_id='.$id);
			foreach($apps as $ap):
			$app->find_first((int)$ap->id);
			$app->estado='0';
			$app->save();			
			endforeach;
			
			Acciones::add("Colocó el ".$salida->serie."-".$salida->numero." ({$salida->getTesdocumentos()->nombre}) como ANULADO",'tessalidas');
			return TRUE;
		}else
		{
			return FALSE;
		}
	}
}


public function anularguia($id)
{
	$salida=$this->find_first((int)$id);
	$detalles=Load::model('tesdetallesalidas');
	$rollos=Load::model('prorollos');
	$rollos_sc=Load::model('sc_crollos');
	/*restablecer los rollos*/
	$dett=$detalles->find('conditions: tessalidas_id='.(int)$id);	
	foreach($dett as $det):
	if($det->prorollos_id)
	{
		if($salida->serie=='001')
		{
			$rollo=$rollos->find_first((int)$det->prorollos_id);
			$rollo->estadorollo='VENTA';
			$rollo->estado='1';
			$rollo->enalmacen='1';
			$rollo->save();
		}
		if($salida->serie=='F002')
		{
			$rollo=$rollos->find_first((int)$det->prorollos_id);
			$rollo->estadorollo='VENTA';
			$rollo->estado='1';
			$rollo->enalmacen='1';
			$rollo->save();
		}
		if($salida->serie=='002')
		{
			$rollo=$rollos->find_first((int)$det->prorollos_id);
			$rollo->estadorollo='Sin procesos';
			$rollo->estado='1';
			$rollo->enalmacen='1';
			$rollo->save();
		}
	}
	
	Acciones::add("Colocó el ".$rollo->numero." PARA VENTA (anulacion de venta)",'prorollos');
	if($det->sc_crollos_id)
	{
		$rollo=$rollos_sc->find_first((int)$det->sc_crollos_id);
		$rollo->estadorollo='Pendiente';
		$rollo->estado='1';
		$rollo->enalmacen='1';
		$rollo->save();
		Acciones::add("Colocó el ".$rollo->numero." PARA Pendiente (anulacion de venta)",'sc_crollos');
	}
	endforeach;
	$salida->estadosalida="ANULADO";
	$salida->estado='1';
	$salida->totalconigv=0;
	$salida->igv=0;
	$salida->userid=Auth::get('id');
	if($salida->save())
	{
		Acciones::add("Colocó el ".$salida->serie."-".$salida->numero." ({$salida->getTesdocumentos()->nombre}) como ANULADO",'tessalidas');
		return TRUE;
	}else
	{
		return FALSE;
	}
}

/*Consultas para ver los adelantos revizar */
public function getAdelantos($id,$campo='tessalidas_id')
{
	$total_a=0;
	$detalles_abonos=new Tesabonosdetalles();
	(float)$total_c=$detalles_abonos->sum('monto','conditions: '.$campo.'='.$id.' AND cargos="1"');
	(float)$total_a=$detalles_abonos->sum('monto','conditions: '.$campo.'='.$id.' AND abono="1"');
	/*ABONOS con NC*/
	/*SELECT sum(monto) FROM `tesabonosdetalles` 
WHERE tesabonos_id=1294 AND 
!isnull(tessalidasinternas_id) AND tessalidasinternas_id!=0 and abono=1*/
	$abonos = $detalles_abonos->find('conditions: '.$campo.'='.$id);
	$suma=0;
	$retencion=0;
	foreach($abonos as $ab)
	{
		$sql="SELECT sum(monto) as monto FROM tesabonosdetalles WHERE tesabonos_id=".$ab->tesabonos_id." AND !isnull(".$campo.") AND ".$campo."!=0 and abono=1";
		//echo $sql;
		$a=$this->find_by_sql($sql);
		if($ab->getTesabonos()->tesformadepagosabonos_id!=14){
		$suma=$suma+$a->monto;
		}else{
		$retencion=$ab->getTesabonos()->tesformadepagosabonos_id;
		}
	}
	$r=0;
	//echo $retencion.'**';
	if($retencion==14){
		/*Suma de las letras si estan como pagados*/
		$sql_r="SELECT sum(monto) as monto FROM `tesletrassalidas` WHERE estadoletras!='Sin Numero' AND  factura_id like '%".$id."%'";
		//echo $sql_r;
		$rr=$this->find_by_sql($sql_r);
		$r=$rr->monto;
	}
	return (float)$total_c+(float)$total_a+(float)$suma+$r;
}
	
/*recibe el id de la venta ruc o la venta interna para ver el total de abonos que tiene*/
public function getAbonos_factura($id,$campo='')
{
	//echo 'id='.$id.'---';
	$abonos=$this->find_all_by_sql("SELECT * FROM  `tesabonosdetalles` WHERE ".$campo." =".$id." GROUP BY tesabonos_id");
	$tabla=explode("_",$campo);
	$total=0;
	$total_i=0;
	$n=0;

	$a_l=$this->find_by_sql("SELECT IFNULL( SUM( monto ) , 0 ) as monto FROM  `tesabonosdetalles` WHERE ".$campo." =".$id."");
	//echo $a_l->monto.' ABL ';
	/*sumar las letras de la factura*/
	/*valida si existe la una letra para esta factura*/
	
	$total_tra=0;
	if($campo=='tessalidas_id')
	{
		//echo "//SELECT count(id) as n FROM `tesletrassalidas` WHERE ".$id." in (factura_id)///";
		//$l_s=$this->find_by_sql("SELECT count(id) as n FROM `tesletrassalidas` WHERE ".$id." in (factura_id)");
		$l_s=$this->find_by_sql("SELECT count(id) as n FROM `tesletrassalidas` WHERE FIND_IN_SET(".$id.", factura_id)");
	if($l_s->n>0)
	{
		//echo "SELECT sum(monto) as monto FROM tesletrassalidas WHERE ".$id." in (factura_id) AND (select count(*) from tesabonosdetalles WHERE tesabonosdetalles.tessalidas_id=tesletrassalidas.tessalidas_id)>0";
		//$salida=$this->find_by_sql("SELECT sum(monto) as monto FROM tesletrassalidas WHERE ".$id." in (factura_id) AND (select count(*) from tesabonosdetalles WHERE tesabonosdetalles.tessalidas_id=tesletrassalidas.tessalidas_id)>0");
		$salida=$this->find_by_sql("SELECT sum(monto) as monto FROM tesletrassalidas WHERE FIND_IN_SET(".$id.", factura_id) AND (select count(*) from tesabonosdetalles WHERE tesabonosdetalles.tessalidas_id=tesletrassalidas.tessalidas_id)>0");
		$total_tra=$salida->monto;
		//echo $salida->monto." LE ";
	}
	}
	//echo (float)$a->monto; 
	return (float)$a_l->monto+(float)$total+(float)$total_i+$total_tra;
}

/*Reporte de Adelantos y Aplicaciones*/
public function getAdelantosAp($id=0)
{
	$cond='';
	if($id!=0)$cond=' AND d.id='.$id;
	return $this->find_all_by_sql("
SELECT 'ruc-salidas' as origen,CONCAT_WS('-',s.serie,s.numero) as numero_adelanto,a.tessalidas_id as app_id,f.tessalidas_id as doc_id, s.tesmonedas_id as tesmonedas_id, 'FA' as doc, d.id as datos_id, d.razonsocial as razonsocial,f.montototal as totaladelanto_s,f.montototal as totaladelanto, a.montototal as montoaplicacion, CONCAT_WS('-',a.serie,a.numero) as numero, a.fecha_at as fecha
FROM tesdatos as d, tessalidas as s, tesaplicacionfacturasadelantos as a 
INNER JOIN tesfacturasadelantos as f 
ON f.id=a.tesfacturasadelantos_id
WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND 
f.tessalidas_id=s.id AND a.estado=1 AND s.estadosalida!='ANULADO' AND d.id=s.tesdatos_id".$cond."
union 
SELECT 'ruc-ingresos' as origen, CONCAT_WS('-',i.serie,i.numero) as numero_adelanto,a.tesingresos_id as app_id,l.tesingresos_id as doc_id, i.tesmonedas_id as tesmonedas_id, 'LTR' as doc, d.id as datos_id, d.razonsocial as razonsocial,l.monto_n as totaladelanto_s,l.monto as totaladelanto, a.monto as montoaplicacion, a.numerodefactura as numero, a.fecha_at as fecha
FROM tesdatos as d, tesingresos as i, tesaplicacionletraingresos as a 
INNER JOIN tesletrasingresos as l 
ON l.id=a.tesletrasingresos_id
WHERE i.aclempresas_id=".Session::get('EMPRESAS_ID')." AND 
l.tesingresos_id=i.id AND a.estado=1 AND  i.estadoingreso!='ANULADO' AND d.id=i.tesdatos_id".$cond."
union 
SELECT 'interno' as origen, CONCAT_WS('-',s.serie,s.numero) as numero_adelanto,a.tessalidasinternas_id as app_id,l.tessalidasinternas_id as doc_id, s.tesmonedas_id as tesmonedas_id, 'LTR-I' as doc, d.id as datos_id, d.razonsocial as razonsocial,l.monto as totaladelanto_s,l.monto as totaladelanto, a.monto as montoaplicacion, a.numerodefactura as numero, a.fecha_at as fecha
FROM tesdatos as d, tessalidasinternas as s, tesaplicacionletrainterna as a 
INNER JOIN tesletrassalidasinternas as l 
ON l.id=a.tesletrassalidasinternas_id
WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND 
l.tessalidasinternas_id=s.id AND a.estado=1 AND s.estadosalida!='ANULADO' AND d.id=s.tesdatos_id".$cond."
ORDER BY doc_id,datos_id,fecha ASC");
}

/*//--Consulta para el llamado de adelantos en si los que ya estan aplicados... solo las facturas de adelantos que sean mayores a 0--///*/
public function adelantos()
{
	$sql="(
SELECT f.montototal-(select IFNULL(sum(tesaplicacionfacturasadelantos.montototal),0) from tesaplicacionfacturasadelantos where f.id=tesaplicacionfacturasadelantos.tesfacturasadelantos_id and tesaplicacionfacturasadelantos.estado!=0) as total, d.testipodatos_id as testipodatos_id, 'ruc-salidas' as origen,CONCAT_WS('-',s.serie,s.numero) as numero_adelanto,f.tessalidas_id as doc_id, s.tesmonedas_id as tesmonedas_id, 'FA' as doc, d.id as datos_id, d.razonsocial as razonsocial,
f.montototal as totaladelanto_s,
f.montototal as totaladelanto, 
IFNULL(SUM(a.montototal),0) as montoaplicacion, 
s.femision as fecha FROM tessalidas as s,tesdatos as d, tesfacturasadelantos as f LEFT JOIN tesaplicacionfacturasadelantos as a ON f.id=a.tesfacturasadelantos_id AND a.estado!=0 WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.estadosalida!='ANULADO' AND s.npedido like 'PA%' AND f.tessalidas_id=s.id AND d.id=s.tesdatos_id AND f.montototal-(select IFNULL(sum(tesaplicacionfacturasadelantos.montototal),0) from tesaplicacionfacturasadelantos where f.id=tesaplicacionfacturasadelantos.tesfacturasadelantos_id and tesaplicacionfacturasadelantos.estado!=0)>0.2 GROUP BY f.id 
)
union 
(
SELECT l.monto-(select IFNULL(sum(tesaplicacionletrainterna.monto),0) from tesaplicacionletrainterna where l.id=tesaplicacionletrainterna.tesletrassalidasinternas_id and tesaplicacionletrainterna.estado!=0) as total, d.testipodatos_id as testipodatos_id, 'interno' as origen, CONCAT_WS('-',s.serie,s.numero) as numero_adelanto,l.tessalidasinternas_id as doc_id, s.tesmonedas_id as tesmonedas_id, 'LTR-I' as doc, d.id as datos_id, d.razonsocial as razonsocial,
l.monto as totaladelanto_s,
l.monto as totaladelanto, 
IFNULL(SUM(a.monto),0) as montoaplicacion, 
s.femision as fecha FROM tessalidasinternas as s,tesdatos as d, tesletrassalidasinternas as l LEFT JOIN tesaplicacionletrainterna as a ON l.id=a.tesletrassalidasinternas_id AND a.estado!=0 WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.estadosalida!='ANULADO' AND s.npedido like 'LA%' AND l.tessalidasinternas_id=s.id AND d.id=s.tesdatos_id AND l.monto-(select IFNULL(sum(tesaplicacionletrainterna.monto),0) from tesaplicacionletrainterna where l.id=tesaplicacionletrainterna.tesletrassalidasinternas_id and tesaplicacionletrainterna.estado!=0)>0.2 AND l.estado!=9 GROUP BY l.id
)
union
(
SELECT IFNULL(l.monto_n,l.monto)-(select IFNULL(sum(tesaplicacionletraingresos.monto),0) from tesaplicacionletraingresos where l.id=tesaplicacionletraingresos.tesletrasingresos_id) as total,d.testipodatos_id as testipodatos_id, 'ruc-ingresos' as origen, CONCAT_WS('-',i.serie,i.numero) as numero_adelanto,l.tesingresos_id as doc_id, i.tesmonedas_id as tesmonedas_id, 'LTR' as doc, d.id as datos_id, d.razonsocial as razonsocial,
l.monto_n as totaladelanto_s,
l.monto as totaladelanto, 
IFNULL(SUM(a.monto),0) as montoaplicacion, 
i.femision as fecha FROM tesdatos as d, tesingresos as i, tesletrasingresos as l LEFT JOIN tesaplicacionletraingresos as a ON l.id=a.tesletrasingresos_id WHERE i.aclempresas_id=".Session::get('EMPRESAS_ID')." AND i.movimiento='LA' AND l.tesingresos_id=i.id AND d.id=i.tesdatos_id AND IFNULL(l.monto_n,l.monto)-(select IFNULL(sum(tesaplicacionletraingresos.monto),0) from tesaplicacionletraingresos where l.id=tesaplicacionletraingresos.tesletrasingresos_id)>0.2 GROUP BY l.id
)
ORDER BY testipodatos_id,doc,fecha,datos_id ASC";
//echo $sql;
	return $this->find_all_by_sql($sql);
}

/*Datos_id*/
public function getAdelantosClientes($id)
{
	$sql="(
SELECT 
s.tesdatos_id as datos_id,
s.numero as numero,
f.montototal as montotal,
s.tesmonedas_id as tesmonedas_id,
f.montototal-(select IFNULL(sum(tesaplicacionfacturasadelantos.montototal),0) from tesaplicacionfacturasadelantos where f.id=tesaplicacionfacturasadelantos.tesfacturasadelantos_id and tesaplicacionfacturasadelantos.estado!=0) as total,
IFNULL(SUM(a.montototal),0) as montoaplicacion 
FROM tessalidas as s,tesdatos as d, tesfacturasadelantos as f LEFT JOIN tesaplicacionfacturasadelantos as a ON f.id=a.tesfacturasadelantos_id AND a.estado!=0 
WHERE s.aclempresas_id=1 AND s.estadosalida!='ANULADO' AND s.npedido like 'PA%' AND f.tessalidas_id=s.id AND d.id=s.tesdatos_id AND d.id=".$id." AND 
f.montototal-(select IFNULL(sum(tesaplicacionfacturasadelantos.montototal),0) from tesaplicacionfacturasadelantos where f.id=tesaplicacionfacturasadelantos.tesfacturasadelantos_id and tesaplicacionfacturasadelantos.estado!=0)>0.5 GROUP BY s.id ) 
union 
(SELECT 
s.tesdatos_id as datos_id,
s.numero as numero,
l.monto as montotal,
s.tesmonedas_id as tesmonedas_id,
l.monto-(select IFNULL(sum(tesaplicacionletrainterna.monto),0) from tesaplicacionletrainterna where l.id=tesaplicacionletrainterna.tesletrassalidasinternas_id and tesaplicacionletrainterna.estado!=0) as total,  
IFNULL(SUM(a.monto),0) as montoaplicacion
FROM tessalidasinternas as s,tesdatos as d, tesletrassalidasinternas as l LEFT JOIN tesaplicacionletrainterna as a ON l.id=a.tesletrassalidasinternas_id AND a.estado!=0 WHERE s.aclempresas_id=1 AND s.estadosalida!='ANULADO' AND s.npedido like 'LA%' AND l.tessalidasinternas_id=s.id AND d.id=s.tesdatos_id AND d.id=".$id." AND l.monto-(select IFNULL(sum(tesaplicacionletrainterna.monto),0) from tesaplicacionletrainterna where l.id=tesaplicacionletrainterna.tesletrassalidasinternas_id and tesaplicacionletrainterna.estado!=0)>0.5 AND l.estado!=9 GROUP BY s.id
) 
union 
( 
SELECT 
i.tesdatos_id as datos_id,
i.numero as numero,
l.monto as montotal,
i.tesmonedas_id as tesmonedas_id,
IFNULL(l.monto_n,l.monto)-(select IFNULL(sum(tesaplicacionletraingresos.monto),0) from tesaplicacionletraingresos where l.id=tesaplicacionletraingresos.tesletrasingresos_id) as total,
IFNULL(SUM(a.monto),0) as montoaplicacion
FROM tesdatos as d, tesingresos as i, tesletrasingresos as l LEFT JOIN tesaplicacionletraingresos as a ON l.id=a.tesletrasingresos_id WHERE i.aclempresas_id=1 AND i.movimiento='LA' AND l.tesingresos_id=i.id AND d.id=i.tesdatos_id AND d.id=".$id." AND IFNULL(l.monto_n,l.monto)-(select IFNULL(sum(tesaplicacionletraingresos.monto),0) from tesaplicacionletraingresos where l.id=tesaplicacionletraingresos.tesletrasingresos_id)>0.5 GROUP BY i.id )";
//echo 'id empresa cliente'.$id;
$adelantos=$this->find_all_by_sql($sql);
$array=array();
$total_s=0;
$total_d=0;
$array['S']=0;
$array['D']=0;
  foreach($adelantos as $item)
  {
	if($item->tesmonedas_id==1 || $item->tesmonedas_id==4)
	{
		//echo $item->numero.' -->'.$item->tesdatos_id.'total==>'.$item->total.' aplicacion==>'.$item->montoaplicacion;
		$total_s=$total_s+$item->total;
		$array['S']=$total_s;
	}/*else
	{
		$array['S']=0;
	}*/
	if($item->tesmonedas_id==2 || $item->tesmonedas_id==5)
	{
		$total_d=$total_d+$item->total;
		$array['D']=$total_d;
	}/*else
	{
		$array['D']=0;
	}*/
  }
  print_r($array);
  return $array;
}


/* Muestra los totales de venta por cliente sin santa carmela*/
public function getTotales($valores='')
{
	if($valores=='')$valores="";
	return $this->find_all_by_sql("SELECT sum(IFNULL(d.importe,0))*0.18 as igv,sum(IFNULL(d.importe,0))*0.18+sum(IFNULL(d.importe,0)) as total, DATE_FORMAT(s.femision,'%m/%y') as fecha FROM tesdatos as da, tessalidas as s INNER JOIN tesdetallesalidas as d ON s.id=d.tessalidas_id WHERE s.tesdatos_id!=1569 AND s.tesdatos_id!=2393 AND da.id=s.tesdatos_id AND s.tesdocumentos_id=7 GROUP BY DATE_FORMAT(s.femision,'%Y-%m')");
}
	

/* TOTALES PARA GRAFICOS DE LOS CLIENTES SUMAR LS CAMPOS IGV Y TOTALCONIGV  VALORES PARA QUE PUEDA INGRESAR EL AÑO */
public function getTotalesall($valores='')
{
	
	return $this->find_all_by_sql("SELECT 
sum(if(s.tesmonedas_id=2,s.igv*(select compra from testipocambios WHERE fecha=s.femision),s.igv))+(select IFNULL(sum(nd.igv),0) from tessalidas as nd WHERE nd.aclempresas_id=".Session::get('EMPRESAS_ID')." AND nd.tesdocumentos_id=14 AND DATE_FORMAT(nd.femision,'%Y-%m')=DATE_FORMAT(s.femision,'%Y-%m'))-
(select IFNULL(sum(nc.igv),0) from tessalidas as nc WHERE nc.aclempresas_id=".Session::get('EMPRESAS_ID')." AND nc.tesdocumentos_id=13 AND DATE_FORMAT(nc.femision,'%Y-%m')=DATE_FORMAT(s.femision,'%Y-%m')) as igv, 
sum(if(s.tesmonedas_id=2,s.totalconigv*(select compra from testipocambios WHERE fecha=s.femision),s.totalconigv))+(select IFNULL(sum(nd.totalconigv),0) from tessalidas as nd WHERE nd.aclempresas_id=".Session::get('EMPRESAS_ID')." AND nd.tesdocumentos_id=14 AND DATE_FORMAT(nd.femision,'%Y-%m')=DATE_FORMAT(s.femision,'%Y-%m'))-
(select IFNULL(sum(nc.totalconigv),0) from tessalidas as nc WHERE nc.aclempresas_id=".Session::get('EMPRESAS_ID')." AND nc.tesdocumentos_id=13 AND DATE_FORMAT(nc.femision,'%Y-%m')=DATE_FORMAT(s.femision,'%Y-%m')) as total,
DATE_FORMAT(s.femision,'%m/%y') as fecha FROM tesdatos as da, tessalidas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND da.id=s.tesdatos_id AND s.tesdocumentos_id=7.".$valores." GROUP BY DATE_FORMAT(s.femision,'%Y-%m')");

}
	
/*Reporte para la utilizacion de hilos por tela o donde se encuentra */
public function getLotesdehilos($id=0)
{
	return $this->find_all_by_sql("SELECT i.tesdatos_id as dato_id, p.razonsocial as razonsocial,d.tesproductos_id as id_producto,d.lote as lote,co.nombre as color,count(c.id) as cajas,sum(c.pesoneto) as pesoneto,sum(c.conos) as conos FROM tesdatos as p, tesingresos as i, tescajas as c, tesdetalleingresos as d, tescolores as co WHERE i.aclempresas_id=".Session::get('EMPRESAS_ID')." AND p.id=i.tesdatos_id AND i.id=d.tesingresos_id AND c.tesdetalleingresos_id=d.id AND d.tesproductos_id=".$id." AND co.id=d.tescolores_id GROUP BY d.lote,i.tesdatos_id"); 
}

public function getDetalle_utilizacion($lote=0,$hilo_id=0)
{
	return $this->find_all_by_sql("/*doc,referencia,tela,caja,kilos*/
SELECT n.id as id,d.tesproductos_id as hilo_id,d.lote as lote,n.codigo as doc,m.nombre as referencia, IFNULL( p.nombre,p.detalle ) as tela,'demo' as cajas,d.cantidad as kilos 
FROM promaquinas as m,proproduccion as pr,prodetalleproduccion as pd,protrama as tr,tesproductos as p, pronotapedidos as n INNER JOIN prodetallepedidos as d ON n.id=d.pronotapedidos_id AND d.lote='".$lote."' AND n.aclempresas_id=".Session::get('EMPRESAS_ID')." AND  d.tesproductos_id=".$hilo_id."
WHERE tr.prodetallepedidos_id=d.id AND pd.id=tr.prodetalleproduccion_id AND pr.id=pd.proproduccion_id AND m.id=pr.promaquinas_id AND pd.tesproductos_id=p.id
UNION ALL
SELECT s.id as id,d.tesproductos_id as hilo_id,d.lote as lote,CONCAT_WS('-',s.serie,s.numero)as doc,'Salida para regular los saldos' as referencia, '' as tela,count(d.id) as cajas, sum(IFNULL(d.cantidad,0)) as kilos
FROM tesdetallesalidasinternas as d, tessalidasinternas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND d.lote='".$lote."' AND d.tesproductos_id=".$hilo_id." AND s.id=d.tessalidasinternas_id
GROUP BY s.id,d.tesproductos_id
UNION ALL
SELECT s.id as id,d.tesproductos_id as hilo_id,d.lote as lote, CONCAT_WS('-',s.serie,s.numero) as doc,'Salida para regular los saldos' as referencia, '' as tela,count(d.id) as cajas, sum(IFNULL(d.cantidad,0)) as kilos FROM tesdetallesalidas as d,tessalidas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdocumentos_id=15 AND s.id=d.tessalidas_id AND d.lote='".$lote."' AND d.tesproductos_id=".$hilo_id."
GROUP BY s.id");

}

/* REPORTE DIARIO CONSULTAS*/
/* 
getVEntas devuele las ventas del dia por clientes
cambiado por maritza sin notas de credito y debito 

cambiar a lo que consuelo pide NC y ND tambien se muestranAND s.estadosalida!='ANULADO' 
*/
public function getVentas($fecha,$moneda=1)
{
	$sql="SELECT s.tescondicionespagos_id as cp, 'tessalidas' as origen,s.totalconigv as totalconigv,SUBSTRING_INDEX(s.npedido, '-',2) as numero,s.tesmonedas_id as tesmonedas_id,IFNULL(sum(IF(((s.totalconigv<=0 OR ISNULL(s.totalconigv)) AND s.estadosalida!='ANULADO'),(select sum(importe)*1.18 FROM tesdetallesalidas WHERE tessalidas_id=s.id),s.totalconigv)),0) as total, 'S' as tabla, s.id as id, d.id as dato_id,s.id as id,d.tesvendedores_id,s.numeroguia as numero_s,s.npedido as pedido,d.razonsocial as razonsocial, s.femision as fecha,s.estadosalida as estado FROM tesdatos as d,tessalidas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND (s.tesdocumentos_id=7 or s.tesdocumentos_id=14 or s.tesdocumentos_id=13) AND (s.npedido like 'TE%' OR s.npedido like 'PA%' OR s.npedido LIKE  'PT%' OR s.npedido LIKE 'SV%' OR s.npedido LIKE 'NC%' OR s.npedido LIKE 'ND%') AND s.femision='".$fecha."' GROUP BY s.id 
UNION ALL
SELECT s.tescondicionespagos_id as cp, 'tessalidasinternas' as origen,s.total as totalconigv,s.numero as numero,s.tesmonedas_id as tesmonedas_id,IFNULL(sum(IF(((s.total<=0 OR ISNULL(s.total)) AND s.estadosalida!='ANULADO' ),(select sum(importe) FROM tesdetallesalidasinternas WHERE  tessalidasinternas_id=s.id),s.total)),0) as total, 'I' as tabla, s.id as id, d.id as dato_id,s.id as id,d.tesvendedores_id,s.numeroguia as numero_s,s.npedido as pedido,d.razonsocial as razonsocial, s.femision as fecha,s.estadosalida as estado FROM tesdatos as d,tessalidasinternas as s WHERE s.tesdocumentos_id!=34 AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND (s.npedido like 'VT%' OR s.npedido like 'PA%' OR s.npedido LIKE  'PT%' OR s.npedido LIKE 'SV%' OR s.npedido LIKE 'NC%' OR s.npedido LIKE 'ND%') AND s.femision='".$fecha."' GROUP BY s.id 
UNION ALL
SELECT s.tescondicionespagos_id as cp,'tessalidas' as origen,s.totalconigv as totalconigv,SUBSTRING_INDEX(s.npedido, '-',2) as numero,s.tesmonedas_id as tesmonedas_id,IFNULL(sum(IF(((s.totalconigv<=0 OR ISNULL(s.totalconigv)) AND s.estadosalida!='ANULADO'),(select sum(importe)*1.18 FROM tesdetallesalidas WHERE tessalidas_id=s.id),s.totalconigv)),0) as total, 'S' as tabla, s.id as id, d.id as dato_id,s.id as id,d.tesvendedores_id,s.numeroguia as numero_s,s.npedido as pedido,d.razonsocial as razonsocial, s.femision as fecha,s.estadosalida as estado FROM tesdatos as d,tessalidas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesdocumentos_id=15 and s.tescondicionespagos_id=5 AND s.femision='".$fecha."' GROUP BY s.id
order by pedido ASC";
	/*echo $sql;*/
	return $this->find_all_by_sql($sql);
}
/*devuelve el monto total de venta en dolares tesmonedas_id=>2 recibe le id del cliente y la fecha*/
public function getTotalVentaFecha($id,$fecha,$moneda=1,$tabla='')
{
	if($tabla=='S')
	{
		$v=$this->find_by_sql("SELECT IFNULL(sum(IF((s.totalconigv<=0 OR ISNULL(s.totalconigv)),(select sum(importe)*1.18 FROM tesdetallesalidas WHERE tessalidas_id=s.id),s.totalconigv)),0) as total FROM tesdatos as d,tessalidas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesdocumentos_id=7 AND s.tesmonedas_id=".$moneda."  AND (s.npedido like 'TE%' OR s.npedido like 'PA%' OR s.npedido LIKE  'PT%') AND s.femision='".$fecha."' AND d.id=".$id."
");
	return $v->total;
	}
	if($tabla=='I')
	{
		$i=$this->find_by_sql("SELECT IFNULL(sum(IF((s.total<=0 OR ISNULL(s.total)),(select sum(importe) FROM tesdetallesalidasinternas WHERE  tessalidasinternas_id=s.id),s.total)),0) as total FROM tesdatos as d,tessalidasinternas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id  AND s.tesmonedas_id=".$moneda."  AND (s.npedido like 'TE%' OR s.npedido like 'PA%' OR s.npedido LIKE  'PT%' OR s.npedido LIKE  'VT%') AND s.femision='".$fecha."' AND d.id=".$id);
	return $i->total;
	}

	if($tabla=='')
	{
		$v=$this->find_by_sql("SELECT (SELECT IFNULL(sum(IF((s.totalconigv<=0 OR ISNULL(s.totalconigv)),(select sum(importe)*1.18 FROM tesdetallesalidas WHERE tessalidas_id=s.id),s.totalconigv)),0) as total FROM tesdatos as d,tessalidas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesdocumentos_id=7 AND s.tesmonedas_id=".$moneda."  AND (s.npedido like 'TE%' OR s.npedido like 'PA%' OR s.npedido LIKE  'PT%') AND s.femision='".$fecha."' AND d.id=".$id.")
+
(SELECT IFNULL(sum(IF((s.total<=0 OR ISNULL(s.total)),(select sum(importe) FROM tesdetallesalidasinternas WHERE  tessalidasinternas_id=s.id),s.total)),0) as total FROM tesdatos as d,tessalidasinternas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id  AND s.tesmonedas_id=".$moneda."  AND (s.npedido like 'TE%' OR s.npedido like 'PA%' OR s.npedido LIKE  'PT%' OR s.npedido LIKE  'VT%') AND s.femision='".$fecha."' AND d.id=".$id.") as suma");
	return $v->suma;
	}
	
}
/*SAldos anterior por vendedor*/
public function getSaldos($fecha,$moneda_s=1,$moneda_d=2)
{
$fecha_2=date("Y-m-d", strtotime("$fecha -1 day")); 
$sql="SELECT v.*, 
(SELECT IFNULL(sum(if(s.totalconigv<=0 OR ISNULL(s.totalconigv),0,s.totalconigv)),0) FROM tesdatos as d,tessalidas as s WHERE s.estadosalida!='ANULADO' AND s.npedido NOT LIKE 'MT-%' AND (s.estadosalida='ADELANTADO' OR s.estadosalida='Pendiente') AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesmonedas_id=".$moneda_s." AND (s.tesdocumentos_id=7 or s.tesdocumentos_id=14) AND s.femision<'".$fecha."' AND d.tesvendedores_id=v.id ) as total_ruc_soles,

(SELECT IFNULL(sum(if(s.total<=0 OR ISNULL(s.total),0,s.total)),0) FROM tesdatos as d,tessalidasinternas as s WHERE s.estadosalida!='ANULADO' AND s.npedido NOT LIKE 'MT-%' AND (s.estadosalida='ADELANTADO' OR s.estadosalida='Pendiente') AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesmonedas_id=".$moneda_s." AND (s.tesdocumentos_id=15 or s.tesdocumentos_id=14) AND s.femision<'".$fecha."' AND d.tesvendedores_id=v.id ) as total_interna_soles,

(select IFNULL(sum(a.monto),0) from tesabonosdetalles as a, tessalidas as s,tesdatos as d where a.tessalidas_id=s.id AND s.tesdocumentos_id=13 AND s.tesdatos_id=d.id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND d.tesvendedores_id=v.id AND s.tesmonedas_id=".$moneda_s." AND s.femision<'".$fecha."' AND (select count(b.id) from tesabonosdetalles as b,tessalidas as z where b.tesabonos_id=a.tesabonos_id AND b.cargos=1 AND z.id=b.tessalidas_id AND z.estadosalida='ADELANTADO' AND z.femision<'".$fecha."')=1) as total_nc_soles,

(select IFNULL(sum(a.monto),0) from tesabonosdetalles as a, tessalidas as s,tesdatos as d where a.tessalidas_id=s.id AND (s.tesdocumentos_id=7 or s.tesdocumentos_id=14) AND  s.estadosalida='ADELANTADO' AND s.tesdatos_id=d.id AND d.tesvendedores_id=v.id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesmonedas_id=".$moneda_s." AND s.femision<'".$fecha."') as total_abono_soles,

(select IFNULL(sum(a.monto),0) from tesabonosdetalles as a, tessalidasinternas as s,tesdatos as d where a.tessalidasinternas_id=s.id AND (s.tesdocumentos_id=15 or s.tesdocumentos_id=14) AND  s.estadosalida='ADELANTADO' AND s.tesdatos_id=d.id AND d.tesvendedores_id=v.id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesmonedas_id=".$moneda_s." AND s.femision<'".$fecha."') as total_abono_interna_soles,

(select IFNULL(sum(l.monto),0) from tesletrassalidas as l, tessalidas as s,tesdatos as d WHERE s.id=l.factura_id AND s.tesdatos_id=d.id AND d.tesvendedores_id=v.id AND s.tesmonedas_id=".$moneda_s." AND s.estadosalida='ADELANTADO' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.femision<'".$fecha."') as total_letras_soles,

(SELECT IFNULL(sum(if(s.totalconigv<=0 OR ISNULL(s.totalconigv),0,s.totalconigv)),0) FROM tesdatos as d,tessalidas as s WHERE s.npedido NOT LIKE 'MT-%' AND s.estadosalida='Pendiente' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesmonedas_id=".$moneda_s." AND s.tesdocumentos_id=13 AND s.femision<'".$fecha."' AND d.tesvendedores_id=v.id ) as total_ruc_nc_soles,

(SELECT IFNULL(sum(if(s.total<=0 OR ISNULL(s.total),0,s.total)),0) FROM tesdatos as d,tessalidasinternas as s WHERE s.npedido NOT LIKE 'MT-%' AND s.estadosalida='Pendiente' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesmonedas_id=".$moneda_s." AND s.tesdocumentos_id=13 AND s.femision<'".$fecha."' AND d.tesvendedores_id=v.id ) as total_ruc_nc_interna_soles,

(SELECT IFNULL(sum(if(s.totalconigv<=0 OR ISNULL(s.totalconigv),0,s.totalconigv)),0) FROM tesdatos as d,tessalidas as s WHERE s.estadosalida!='ANULADO' AND s.npedido NOT LIKE 'MT-%' AND (s.estadosalida='ADELANTADO' OR s.estadosalida='Pendiente')  AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesmonedas_id=".$moneda_d." AND (s.tesdocumentos_id=7 or s.tesdocumentos_id=14) AND s.femision<'".$fecha."' AND d.tesvendedores_id=v.id ) as total_ruc_dolar,

(SELECT IFNULL(sum(if(s.total<=0 OR ISNULL(s.total),0,s.total)),0) FROM tesdatos as d,tessalidasinternas as s WHERE s.estadosalida!='ANULADO' AND s.npedido NOT LIKE 'MT-%' AND (s.estadosalida='ADELANTADO' OR s.estadosalida='Pendiente')  AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesmonedas_id=".$moneda_d." AND (s.tesdocumentos_id=15 or s.tesdocumentos_id=14) AND s.femision<'".$fecha."' AND d.tesvendedores_id=v.id ) as total_interna_dolar,

(select IFNULL(sum(a.monto),0) from tesabonosdetalles as a, tessalidas as s,tesdatos as d where a.tessalidas_id=s.id AND s.tesdocumentos_id=13 AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND d.tesvendedores_id=v.id AND s.tesmonedas_id=".$moneda_d." AND (select count(b.id) from tesabonosdetalles as b,tessalidas as z where b.tesabonos_id=a.tesabonos_id AND b.cargos=1 AND z.id=b.tessalidas_id AND z.estadosalida='ADELANTADO' AND z.femision<'".$fecha."')=1) as total_nc_dolar,

(select IFNULL(sum(a.monto),0) from tesabonosdetalles as a, tessalidasinternas as s,tesdatos as d where a.tessalidasinternas_id=s.id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND (s.tesdocumentos_id=15 or s.tesdocumentos_id=14) AND  s.estadosalida='ADELANTADO' AND s.tesdatos_id=d.id AND d.tesvendedores_id=v.id AND s.tesmonedas_id=".$moneda_d." AND s.femision<'".$fecha."') as total_abono_interna_dolar,

(select IFNULL(sum(a.monto),0) from tesabonosdetalles as a, tessalidas as s,tesdatos as d where a.tessalidas_id=s.id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND (s.tesdocumentos_id=7 or s.tesdocumentos_id=14) AND  s.estadosalida='ADELANTADO' AND s.tesdatos_id=d.id AND d.tesvendedores_id=v.id AND s.tesmonedas_id=".$moneda_d." AND s.femision<'".$fecha."') as total_abono_dolar,

(select IFNULL(sum(l.monto),0) from tesletrassalidas as l, tessalidas as s,tesdatos as d WHERE s.id=l.factura_id AND s.tesdatos_id=d.id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND d.tesvendedores_id=v.id AND s.tesmonedas_id=".$moneda_d." AND s.estadosalida='ADELANTADO' AND s.femision<'".$fecha."') as total_letras_dolar,

(SELECT IFNULL(sum(if(s.totalconigv<=0 OR ISNULL(s.totalconigv),0,s.totalconigv)),0) FROM tesdatos as d,tessalidas as s WHERE s.npedido NOT LIKE 'MT-%' AND s.estadosalida='Pendiente' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesmonedas_id=".$moneda_d." AND s.tesdocumentos_id=13 AND s.femision<'".$fecha."' AND d.tesvendedores_id=v.id ) as total_ruc_nc_dolar,

(SELECT IFNULL(sum(if(s.total<=0 OR ISNULL(s.total),0,s.total)),0) FROM tesdatos as d,tessalidasinternas as s WHERE s.npedido NOT LIKE 'MT-%' AND s.estadosalida='Pendiente' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesmonedas_id=".$moneda_d." AND s.tesdocumentos_id=13 AND s.femision<'".$fecha."' AND d.tesvendedores_id=v.id ) as total_ruc_nc_interna_dolar
FROM tesvendedores as v WHERE v.aclempresas_id=".Session::get('EMPRESAS_ID');
	//echo $sql;
	//echo "<br />";
	return $this->find_all_by_sql($sql);
}
public function getSaldos_d($fecha,$moneda,$vendedor)
{
	$a=$this->Saldo_ruc($fecha,$moneda,$vendedor)->total;
	$b=$this->Saldo_internas($fecha,$moneda,$vendedor)->total;
	$c=$this->Saldo_abonos($fecha,$moneda,$vendedor)->total;
	$d=$this->Saldo_ncreditos($fecha,$moneda,$vendedor)->total;
	
	echo "<br />";
	echo number_format($a,2,'.','');
	echo "<br />";
	echo number_format($b,2,'.','');
	echo "<br />";
	echo number_format($c,2,'.','');
	echo "<br />";
	echo number_format($d,2,'.','');
	echo "<br />";
	return ($a+$b)-($c+$d);
	
}

static function Saldo_ruc($fecha,$moneda,$vendedor)
{
	 /*AND s.npedido NOT LIKE 'PA-%'*/
	$salidas= new Tessalidas();
	$sql="SELECT IFNULL(sum(if(s.totalconigv<=0 OR ISNULL(s.totalconigv),0,s.totalconigv)),0) as total
		FROM tesdatos as d,tessalidas as s 
		WHERE s.estadosalida != 'ANULADO' AND s.npedido NOT LIKE 'MT-%' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesmonedas_id=".$moneda." AND (s.tesdocumentos_id=7 or s.tesdocumentos_id=14)  AND s.femision<'".$fecha."' AND d.tesvendedores_id=".$vendedor;
	return $salidas->find_by_sql($sql); 
}
static function Saldo_internas($fecha,$moneda,$vendedor)
{
	/*AND s.npedido NOT LIKE 'PA-%'*/
	$salidas= new Tessalidas();
	$sql="SELECT IFNULL(sum(if(s.total<=0 OR ISNULL(s.total),0,s.total)),0) as total
		FROM tesdatos as d,tessalidasinternas as s 
		WHERE s.estadosalida != 'ANULADO' AND s.npedido NOT LIKE 'MT-%' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesmonedas_id=".$moneda." AND (s.tesdocumentos_id=15 or s.tesdocumentos_id=14) AND s.femision<'".$fecha."' AND d.tesvendedores_id=".$vendedor;
	//echo $sql;
	return $salidas->find_by_sql($sql);
}
static function Saldo_abonos($fecha,$moneda,$vendedor)
{
	$salidas= new Tessalidas();
	$sql="SELECT IFNULL(sum(a.total),0) as total FROM tesabonos as a, tesdatos as d WHERE a.estadov='Terminado' AND a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tesdatos_id=d.id AND a.tesmonedas_id=".$moneda." AND d.tesvendedores_id=".$vendedor." AND a.fecha<'".$fecha."'";
	return $salidas->find_by_sql($sql);
}
static function Saldo_ncreditos($fecha,$moneda,$vendedor)
{
	$salidas= new Tessalidas();
	$sql="SELECT IFNULL(sum(if(s.totalconigv<=0 OR ISNULL(s.totalconigv),0,s.totalconigv)),0) as total
		FROM tesdatos as d,tessalidas as s 
		WHERE s.estadosalida !=  'ANULADO' AND s.npedido NOT LIKE 'MT-%' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesdatos_id=d.id AND s.tesmonedas_id=".$moneda." AND s.tesdocumentos_id=13  AND s.femision<'".$fecha."' AND d.tesvendedores_id=".$vendedor;
	return $salidas->find_by_sql($sql);
}


/*Ventas del dia solo el totoal con igv es decir sin las aplicaciones*/
public function getVenta($fecha,$moneda_s=1,$moneda_d=2)
{
	/*IFNULL(sum(IF((s.total<=0 OR ISNULL(s.total)),(select sum(importe) FROM tesdetallesalidasinternas WHERE  tessalidasinternas_id=s.id),s.total)),0) as total*/
	$sql="SELECT v.*,(
	SELECT IFNULL(sum(if(ISNULL(s.totalconigv),0,s.totalconigv)),0) FROM tesdatos as d,tessalidas as s WHERE s.estadosalida!='ANULADO' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesmonedas_id=".$moneda_s." AND s.tesdatos_id=d.id AND s.tesdocumentos_id=7 AND s.femision='".$fecha."' AND d.tesvendedores_id=v.id)
	+
	(SELECT IFNULL(sum(if(s.total<=0 OR ISNULL(s.total),0,s.total)),0) FROM tesdatos as d,tessalidasinternas as s WHERE (s.npedido LIKE 'VT-%' OR s.npedido LIKE 'PT-%'  OR s.npedido LIKE 'ND-%') AND s.estadosalida!='ANULADO' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesmonedas_id=".$moneda_s." AND s.tesdatos_id=d.id AND s.femision='".$fecha."' AND d.tesvendedores_id=v.id) as suma_soles,
	
	(SELECT IFNULL(sum(if(s.totalconigv<=0 OR ISNULL(s.totalconigv),0,s.totalconigv)),0) FROM tesdatos as d,tessalidas as s WHERE s.estadosalida!='ANULADO' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesmonedas_id=".$moneda_d." AND s.tesdatos_id=d.id AND s.tesdocumentos_id=7 AND s.femision='".$fecha."' AND d.tesvendedores_id=v.id)
	+
	(SELECT IFNULL(sum(if(s.total<=0 OR ISNULL(s.total),0,s.total)),0) FROM tesdatos as d,tessalidasinternas as s WHERE (s.npedido LIKE 'VT-%' OR s.npedido LIKE 'PT-%') AND s.estadosalida!='ANULADO' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesmonedas_id=".$moneda_d." AND s.tesdatos_id=d.id AND s.femision='".$fecha."' AND d.tesvendedores_id=v.id) as suma_dolares 
	FROM tesvendedores as v";
	$sql;
	return $this->find_all_by_sql($sql);
}
/* Cobranzas del dia */
public function getAbonos($fecha,$moneda_s=1,$moneda_d=2)
{
	$sql="SELECT v.*,(SELECT IFNULL(sum(a.total),0) FROM tesabonos as a, tesdatos as d WHERE a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tesdatos_id=d.id AND a.tesmonedas_id=".$moneda_s." AND d.tesvendedores_id=v.id AND a.fecha='".$fecha."') as suma_soles,
	(SELECT IFNULL(sum(a.total),0) FROM tesabonos as a, tesdatos as d WHERE a.tesdatos_id=d.id AND a.tesmonedas_id=".$moneda_d." AND d.tesvendedores_id=v.id AND a.fecha='".$fecha."') as suma_dolares FROM tesvendedores as v WHERE v.aclempresas_id=".Session::get('EMPRESAS_ID');
	//echo $sql;
	return $this->find_all_by_sql($sql);
}

/*Adelantos menos sus aplicaciones*/
public function getAdelanto_f($fecha,$moneda_s=1,$moneda_d=2)
{
	/*$sql="select v.*,
(SELECT IFNULL(sum(f.montototal),0) as total_f FROM tesdatos as d,tessalidas as s INNER JOIN tesfacturasadelantos as f ON s.id=f.tessalidas_id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')."  WHERE s.estadosalida!='ANULADO' AND d.id=s.tesdatos_id AND d.tesvendedores_id=v.id AND f.tesmonedas_id=".$moneda_s."  AND date_format(f.fecha_at,'%Y-%m-%d')<='".$fecha."' AND  s.femision<='".$fecha."')-(SELECT IFNULL(sum(a.montototal),0) as total_a FROM tesdatos as d,tessalidas as s,tesfacturasadelantos as f INNER JOIN tesaplicacionfacturasadelantos as a ON a.tesfacturasadelantos_id=f.id WHERE s.estadosalida!='ANULADO' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND d.id=s.tesdatos_id AND s.id=f.tessalidas_id AND d.tesvendedores_id=v.id AND f.tesmonedas_id=".$moneda_s." AND s.femision<='".$fecha."' AND date_format(a.fecha_at,'%Y-%m-%d')<='".$fecha."') as suma_soles,
(SELECT IFNULL(sum(f.montototal),0) as total_f FROM tesdatos as d,tessalidas as s INNER JOIN tesfacturasadelantos as f ON s.id=f.tessalidas_id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." WHERE s.estadosalida!='ANULADO' AND d.id=s.tesdatos_id AND d.tesvendedores_id=v.id AND f.tesmonedas_id=".$moneda_d." AND date_format(f.fecha_at,'%Y-%m-%d')<='".$fecha."' AND s.femision<='".$fecha."')-(SELECT IFNULL(sum(a.montototal),0) as total_a FROM tesdatos as d,tessalidas as s,tesfacturasadelantos as f INNER JOIN tesaplicacionfacturasadelantos as a ON a.tesfacturasadelantos_id=f.id WHERE s.estadosalida!='ANULADO' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND d.id=s.tesdatos_id AND s.id=f.tessalidas_id AND d.tesvendedores_id=v.id AND f.tesmonedas_id=".$moneda_d." AND s.femision<='".$fecha."' AND date_format(a.fecha_at,'%Y-%m-%d')<='".$fecha."') as suma_dolares
FROM tesvendedores as v  WHERE v.aclempresas_id=".Session::get('EMPRESAS_ID');*/
$sql="select v.*,
((SELECT IFNULL(sum(f.montototal),0) as total_f FROM tesdatos as d,tessalidas as s INNER JOIN tesfacturasadelantos as f ON s.id=f.tessalidas_id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')."  WHERE s.estadosalida!='ANULADO' AND d.id=s.tesdatos_id AND d.tesvendedores_id=v.id AND f.tesmonedas_id=".$moneda_s."  AND date_format(f.fecha_at,'%Y-%m-%d')<='".$fecha."' AND  s.femision<='".$fecha."')+(select IFNULL(sum(tessalidas.totalconigv-tessalidas.igv),0) as total_nc from tessalidas, tesdatos WHERE tesdatos.id=tessalidas.tesdatos_id AND tesdatos.tesvendedores_id=v.id AND tessalidas.tesmonedas_id=".$moneda_s." AND tessalidas.femision<='".$fecha."' AND tessalidas.aclempresas_id=".Session::get('EMPRESAS_ID')." AND tessalidas.tesdocumentos_id=13 AND tessalidas.estadosalida='Pendiente'))-(SELECT IFNULL(sum(a.montototal),0) as total_a FROM tesdatos as d,tessalidas as s,tesfacturasadelantos as f INNER JOIN tesaplicacionfacturasadelantos as a ON a.tesfacturasadelantos_id=f.id WHERE s.estadosalida!='ANULADO' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND d.id=s.tesdatos_id AND s.id=f.tessalidas_id AND d.tesvendedores_id=v.id AND f.tesmonedas_id=".$moneda_s." AND s.femision<='".$fecha."' AND date_format(a.fecha_at,'%Y-%m-%d')<='".$fecha."') as suma_soles,
((SELECT IFNULL(sum(f.montototal),0) as total_f FROM tesdatos as d,tessalidas as s INNER JOIN tesfacturasadelantos as f ON s.id=f.tessalidas_id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." WHERE s.estadosalida!='ANULADO' AND d.id=s.tesdatos_id AND d.tesvendedores_id=v.id AND f.tesmonedas_id=".$moneda_d." AND date_format(f.fecha_at,'%Y-%m-%d')<='".$fecha."' AND s.femision<='".$fecha."')+(select IFNULL(sum(tessalidas.totalconigv-tessalidas.igv),0) as total_nc from tessalidas, tesdatos WHERE tesdatos.id=tessalidas.tesdatos_id AND tesdatos.tesvendedores_id=v.id AND tessalidas.tesmonedas_id=".$moneda_d." AND tessalidas.femision<='".$fecha."' AND tessalidas.aclempresas_id=".Session::get('EMPRESAS_ID')." AND tessalidas.tesdocumentos_id=13 AND tessalidas.estadosalida='Pendiente'))-(SELECT IFNULL(sum(a.montototal),0) as total_a FROM tesdatos as d,tessalidas as s,tesfacturasadelantos as f INNER JOIN tesaplicacionfacturasadelantos as a ON a.tesfacturasadelantos_id=f.id WHERE s.estadosalida!='ANULADO' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND d.id=s.tesdatos_id AND s.id=f.tessalidas_id AND d.tesvendedores_id=v.id AND f.tesmonedas_id=".$moneda_d." AND s.femision<='".$fecha."' AND date_format(a.fecha_at,'%Y-%m-%d')<='".$fecha."') as suma_dolares
FROM tesvendedores as v  WHERE v.aclempresas_id=".Session::get('EMPRESAS_ID');
	//echo $sql;
	return $this->find_all_by_sql($sql);
}

public function getAbonosporTipo($fecha,$moneda_s=1,$moneda_d=2)
{
	/*Crear una consulta agragando between*/
	$vendedores = Load::model('tesvendedores');
	
	$code="SELECT fa.*,";
	$i=0;
	foreach($vendedores->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID')) as $v){
	
	if($i!=0)$code.=" , ";
	$code.="'".$v->id."' as v_id, '".$v->nombre."' as v_nombre, ";
	$code.="(SELECT IFNULL(sum(a.total),0) FROM tesabonos as a, tesdatos as d WHERE a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tesdatos_id=d.id AND a.tesmonedas_id=".$moneda_s." AND d.tesvendedores_id=$v->id AND a.fecha='".$fecha."' AND a.tesformadepagosabonos_id=fa.id) as suma_soles".$v->id.", 
	(SELECT IFNULL(sum(a.total),0) FROM tesabonos as a, tesdatos as d WHERE a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tesdatos_id=d.id AND a.tesmonedas_id=".$moneda_d." AND d.tesvendedores_id=$v->id AND a.fecha='".$fecha."' AND a.tesformadepagosabonos_id=fa.id) as suma_dolares".$v->id."";
	$i++;
	}
	$code.=" FROM tesformadepagosabonos as fa WHERE fa.estado=1";
	//echo $code;
	return $this->find_all_by_sql($code);
}
/*EFECTIVO DEL MES 
### devuelve los abonos del dia y los acumulados
*/ 
public function getAbonosMes($fecha)
{
	/*hasta ayer*/
	$f = new DateTime($fecha);
	$f->modify('first day of this month');
	$f1 = new DateTime($fecha);
	$f1->sub(new DateInterval('P1D'));
	$ff=$f1->format('Y-m-d');
	//$ff=date("Y-m-d", strtotime("$ff -1 day"));    AND a.tesformadepagosabonos_id!=3 AND a.tesformadepagosabonos_id!=10 AND a.tesformadepagosabonos_id!=14 AND a.tesformadepagosabonos_id!=15 AND a.tesformadepagosabonos_id!=16
	//echo $ff;
	$fi=$f->format('Y-m-d');
	$monedas = Load::model('tesmonedas');
	$i=0;
	$code='SELECT v.*';
	foreach($monedas->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID')) as $m):
	$code.=" , ";
	$i++;
	$code.="(SELECT IFNULL(sum(a.total),0) FROM tesabonos as a, tesdatos as d WHERE a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tesdatos_id=d.id AND a.tesmonedas_id=".$m->id." AND d.tesvendedores_id=v.id AND a.fecha BETWEEN '".$fi."' AND '".$ff."' AND a.tesformadepagosabonos_id!=3 AND a.tesformadepagosabonos_id!=10 AND a.tesformadepagosabonos_id!=14 AND a.tesformadepagosabonos_id!=15 AND a.tesformadepagosabonos_id!=16) as total".$m->abr;
	endforeach;
	$code.=' FROM tesvendedores as v WHERE v.aclempresas_id='.Session::get('EMPRESAS_ID');
	
	//echo "<br />";
	//echo $code;
	return $this->find_all_by_sql($code);
}
public function getAbonosDia($fecha)
{
	
	$monedas = Load::model('tesmonedas');
	$i=0;
	$code='SELECT v.*';
	foreach($monedas->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID')) as $m):
	$code.=" , ";
	$i++;
	$code.="(SELECT IFNULL(sum(a.total),0) FROM tesabonos as a, tesdatos as d WHERE a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tesdatos_id=d.id AND a.tesmonedas_id=".$m->id." AND d.tesvendedores_id=v.id AND a.fecha='".$fecha."' AND a.tesformadepagosabonos_id!=3 AND a.tesformadepagosabonos_id!=16 AND a.tesformadepagosabonos_id!=14) as total".$m->abr;
	endforeach;
	$code.=' FROM tesvendedores as v WHERE v.aclempresas_id='.Session::get('EMPRESAS_ID');
	//echo $code;
	return $this->find_all_by_sql($code);
}
/*LETRAS ACUMULADAS*/
public function getLetrasAcumuladas($fecha)
{
	/*hasta ayer*/
	$f = new DateTime($fecha);
	$f->modify('first day of this month');
	/*$f1 = new DateTime($fecha);
	$f1->sub(new DateInterval('P1D'));
	$ff=$f1->format('Y-m-d');*/
	$ff=$fecha;
	$fi=$f->format('Y-m-d');
	$monedas = Load::model('tesmonedas');
	$i=0;
	$code='SELECT v.*';
	foreach($monedas->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID')) as $m):
	$code.=" , ";
	$i++;
	$code.="((SELECT IFNULL(sum(monto),0) FROM tesdatos as d, tesletrassalidasinternas as li INNER JOIN tessalidasinternas as si ON si.id=li.tessalidasinternas_id AND li.fecha_pago BETWEEN '".$fi."' AND '".$ff."' AND si.aclempresas_id=".Session::get('EMPRESAS_ID')." AND si.tesmonedas_id=".$m->id." WHERE d.id=si.tesdatos_id AND li.estadoletras='PAGADO' AND d.tesvendedores_id=v.id)+(SELECT IFNULL(sum(monto),0) FROM tesdatos as d, tesletrassalidas as l INNER JOIN tessalidas as s ON s.id=l.tessalidas_id AND l.fecha_pago BETWEEN '".$fi."' AND '".$ff."' AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.tesmonedas_id=".$m->id." WHERE d.id=s.tesdatos_id AND l.estadoletras='PAGADO' AND d.tesvendedores_id=v.id)) as total".$m->abr;
	endforeach;
	$code.=' FROM tesvendedores as v WHERE v.aclempresas_id='.Session::get('EMPRESAS_ID');
	//echo $code;
	return $this->find_all_by_sql($code);
}
/*ABONOS TIPO LETRA PARA EL REPORTE DIARIO*/
public function getAbonosLetraMes($fecha)
{
	/*hasta ayer*/
	$f = new DateTime($fecha);
	$f->modify('first day of this month');
	$f1 = new DateTime($fecha);
	$f1->sub(new DateInterval('P1D'));
	$ff=$f1->format('Y-m-d');
	$fi=$f->format('Y-m-d');
	$monedas = Load::model('tesmonedas');
	$i=0;
	$code='SELECT v.*';
	foreach($monedas->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID')) as $m):
	$code.=" , ";
	$i++;
	$code.="(SELECT IFNULL(sum(a.total),0) FROM tesabonos as a, tesdatos as d WHERE a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tesdatos_id=d.id AND a.tesmonedas_id=".$m->id." AND d.tesvendedores_id=v.id AND a.fecha BETWEEN '".$fi."' AND '".$ff."' AND a.tesformadepagosabonos_id=3) as total".$m->abr;
	endforeach;
	$code.=' FROM tesvendedores as v WHERE v.aclempresas_id='.Session::get('EMPRESAS_ID');
	//echo $code;
	return $this->find_all_by_sql($code);
}
/*ABONOS TIPO LETRA PARA EL REPORTE DIARIO*/
public function getAbonosRetencionMes($fecha)
{
	/*hasta ayer*/
	$f = new DateTime($fecha);
	$f->modify('first day of this month');
	$f1 = new DateTime($fecha);
	$f1->sub(new DateInterval('P1D'));
	$ff=$f1->format('Y-m-d');
	$fi=$f->format('Y-m-d');
	$monedas = Load::model('tesmonedas');
	$i=0;
	$code='SELECT v.*';
	foreach($monedas->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID')) as $m):
	$code.=" , ";
	$i++;
	$code.="(SELECT IFNULL(sum(a.total),0) FROM tesabonos as a, tesdatos as d WHERE a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tesdatos_id=d.id AND a.tesmonedas_id=".$m->id." AND d.tesvendedores_id=v.id AND a.fecha BETWEEN '".$fi."' AND '".$ff."' AND a.tesformadepagosabonos_id=14) as total".$m->abr;
	endforeach;
	$code.=' FROM tesvendedores as v WHERE v.aclempresas_id='.Session::get('EMPRESAS_ID');
	//echo $code;
	return $this->find_all_by_sql($code);
}

/*ABONOS TIPO LETRA PARA EL REPORTE DIARIO*/
public function getAbonosDetraccionMes($fecha)
{
	/*hasta ayer*/
	$f = new DateTime($fecha);
	$f->modify('first day of this month');
	$f1 = new DateTime($fecha);
	$f1->sub(new DateInterval('P1D'));
	$ff=$f1->format('Y-m-d');
	$fi=$f->format('Y-m-d');
	$monedas = Load::model('tesmonedas');
	$i=0;
	$code='SELECT v.*';
	foreach($monedas->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID')) as $m):
	$code.=" , ";
	$i++;
	$code.="(SELECT IFNULL(sum(a.total),0) FROM tesabonos as a, tesdatos as d WHERE a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tesdatos_id=d.id AND a.tesmonedas_id=".$m->id." AND d.tesvendedores_id=v.id AND a.fecha BETWEEN '".$fi."' AND '".$ff."' AND a.tesformadepagosabonos_id=10) as total".$m->abr;
	endforeach;
	$code.=' FROM tesvendedores as v WHERE v.aclempresas_id='.Session::get('EMPRESAS_ID');
	//echo $code;
	return $this->find_all_by_sql($code);
}


public function getAbonosLetraDia($fecha)
{
	
	$monedas = Load::model('tesmonedas');
	$i=0;
	$code='SELECT v.*';
	foreach($monedas->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID')) as $m):
	$code.=" , ";
	$i++;
	$code.="(SELECT IFNULL(sum(a.total),0) FROM tesabonos as a, tesdatos as d WHERE a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.tesdatos_id=d.id AND a.tesmonedas_id=".$m->id." AND d.tesvendedores_id=v.id AND a.fecha='".$fecha."' AND a.tesformadepagosabonos_id=3) as total".$m->abr;
	endforeach;
	$code.=' FROM tesvendedores as v WHERE v.aclempresas_id='.Session::get('EMPRESAS_ID');
	//echo $code;
	return $this->find_all_by_sql($code);
}
/*SELECT v.* , (SELECT sum(monto) as total FROM tesdatos as d, tesletrassalidasinternas as li INNER JOIN tessalidasinternas as si ON si.id=li.tessalidasinternas_id AND si.fvencimiento BETWEEN '2014-09-30' AND '2014-10-02' WHERE
d.id=si.tesdatos_id AND
li.estadoletras="PAGADO" AND d.tesvendedores_id=v.id) as totalD FROM tesvendedores as v WHERE v.aclempresas_id=1*/


/* PARA VER LO QUE FALTA PAGAR SI LA FACTURA FUE ADELATADA*/
public function getDetalleAbono($id,$tabla='tessalidas')
{
	$d=$this->find_by_sql('SELECT sum(monto) as monto FROM `tesabonosdetalles` WHERE '.$tabla.'_id='.$id);
	
	return $d->monto; 
}
/* PARA EL REPORTE FINAL DE SALDOS DE HILO */
public function getSaldoHilos($id)
{
	$cond='';
	if($id!=0){$cond=' AND t.id='.$id;}
	$sql="SELECT p.hilosistema_id,s.nombre as sistema, p.nombre as titulo,
t.nombre as material, p.hiloacabados_id, a.nombre as tipo_material, p.hilofibras_id, f.nombre as caracteristicas,c.nombre as color, 
t.id as tid, p.id as pid,c.id as cid
FROM tesdetalleingresos as dp, tescolores as c, tesproductos as p 
INNER JOIN testipoproductos as t ON t.id=p.testipoproductos_id AND t.teslineaproductos_id=3
LEFT JOIN hilosistema as s ON s.id=p.hilosistema_id
LEFT JOIN hiloacabados as a ON a.id=p.hiloacabados_id
LEFT JOIN hilofibras as f ON f.id=p.hilofibras_id
WHERE dp.tesproductos_id=p.id and dp.tescolores_id=c.id".$cond."
GROUP BY dp.tescolores_id,dp.tesproductos_id 
ORDER BY p.hilosistema_id,p.nombre,c.nombre ASC";
	
	/*$sql="SELECT t.id as tid,t.nombre as tipo,
	p.id as pid,c.id as cid,p.nombre as nombre ,c.nombre as color,'000.00' as saldo,
	(select nombre from hilofibras WHERE id=p.hilofibras_id)as fibra,p.tipo_fibra,p.hilofibras_id
	FROM tesdetalleingresos as dp, tescolores as c, tesproductos as p INNER JOIN testipoproductos as t ON 
	t.id=p.testipoproductos_id AND t.teslineaproductos_id=3
	WHERE dp.tesproductos_id=p.id and dp.tescolores_id=c.id".$cond."
	GROUP BY dp.tescolores_id,dp.tesproductos_id
	ORDER BY fibra,nombre,c.nombre ASC";*/
echo $sql;
return $this->find_all_by_sql($sql);

}
/*
Recibe el tipo de documento 
Devuelve el total y el igv
Dolares 2 or 5
Soles 1 or 4
*/

public function igv_total($fecha,$documento_id=7)
{
	$soles=1;$dolares=2;
	if(Session::get('EMPRESAS_ID')==2)
	{
		$soles=4;$dolares=5;
	}
	$s=$this->find_by_sql("SELECT sum(igv) as igv,sum(totalconigv)as totalconigv FROM `tessalidas`
WHERE date_format(femision,'%Y-%m')='".$fecha."' AND tesmonedas_id=".$soles." AND tesdocumentos_id=".$documento_id." AND aclempresas_id=".Session::get('EMPRESAS_ID'));
	
	$d=$this->find_by_sql("SELECT sum(m.igv*c.compra) as igv,sum(m.totalconigv*c.compra)as totalconigv FROM tessalidas as m,testipocambios as c
WHERE m.femision=c.fecha AND date_format(m.femision,'%Y-%m')='".$fecha."' AND m.tesmonedas_id=".$dolares." AND m.tesdocumentos_id=".$documento_id." AND m.aclempresas_id=".Session::get('EMPRESAS_ID'));
	//echo $d->igv;
	$V=array();
	$V["igv"]=$s->igv+$d->igv;
	$V["totalconigv"]=$s->totalconigv+$d->totalconigv;
	return $V;
	
	
}


}
?>