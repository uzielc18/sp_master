<?php
class Tesdatos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tescolorclientes','tessalidasinternas','tessalidas','tesingresos','tescontactos','tesvauchers','tesordendecompras','tesfacturasadelantos','tesdatosdirecciones');
		$this->belongs_to('testipodatos','tesvendedores','aclempresas');
    }
	/*$id :: se espera recibir el id del provedor o cliente*/
	public function generacodigo($id)
	{
		$maximo = $this->maximum("codigo", "conditions: testipodatos_id=".$id." AND aclempresas_id =".Session::get('EMPRESAS_ID'));
		if(empty($maximo))
		{
			$maximo=1;
		}else{
			$maximo=$maximo+1;
		}
		switch($maximo)
		{
			case $maximo<10: $maximo="000000".$maximo; break;
			case $maximo<100: $maximo="00000".$maximo; break;
			case $maximo<1000: $maximo="0000".$maximo; break;
			case $maximo<10000: $maximo="000".$maximo; break;
			case $maximo<100000: $maximo="00".$maximo; break;
			case $maximo<1000000: $maximo="0".$maximo; break;
			default: $maximo=$maximo;
		}
		return $maximo;
	}
	
	public function TesdatosPag($page=1,$per_page=100,$tipodato=1,$campo='codigo',$direccion='DESC')
	{
		$select='d.'.join(',d.',$this->fields).',c.nombres as contacto, c.id as idcontato';
		$from='tesdatos as d';
		$joins='LEFT JOIN tescontactos as c ON d.id=c.tesdatos_id AND c.activo=1 and c.estado="1"';
		$condiciones='d.testipodatos_id='.$tipodato.' AND d.estado="1" and d.aclempresas_id='.Session::get('EMPRESAS_ID');
		$orden='ORDER BY d.'.$campo.' '.$direccion;
		//echo "SELECT $select FROM $from $joins WHERE $condiciones";
		return $this->paginate_by_sql("SELECT $select FROM $from $joins WHERE $condiciones $orden","page: $page","per_page: $per_page");
	}
	/*
	#id es el id del tesdato
	*/
	public function dato_id($id=0)
	{
		$select='d.'.join(',d.',$this->fields).',c.nombres as contacto, c.id as idcontato';
		$from='tesdatos as d';
		$joins='LEFT JOIN tescontactos as c ON d.id=c.tesdatos_id AND c.activo=1 and c.estado="1"';
		$condiciones='d.id='.$id.' AND d.activo = 1 AND d.estado="1" and d.aclempresas_id='.Session::get('EMPRESAS_ID');
		$orden='';
		return $this->find_by_sql("SELECT $select FROM $from $joins WHERE $condiciones");
	}
	/*devuelme el el di del proveedor = 1 a  y el id del cliente = 2 b */
	public function getDatos_p($q)
	{
		return $this->find_all_by_sql("SELECT concat_ws('-',b.direccion,b.distrito) as direccion_cliente,concat_ws('-',a.direccion,a.distrito) as direccion_proveedor,concat_ws('- ',a.razonsocial,a.ruc,a.codigo_ant) as name, a.id as id_proveedor, a.codigo_ant as codigo_proveedor, b.id as id_cliente, b.codigo_ant as codigo_cliente,a.ruc as ruc_proveedor, b.ruc as ruc_cliente
FROM tesdatos as a LEFT JOIN tesdatos as b ON a.ruc=b.ruc AND b.testipodatos_id=2 AND b.aclempresas_id=".Session::get('EMPRESAS_ID')." WHERE concat_ws(' ',a.razonsocial,a.codigo_ant,a.ruc) like '%".$q."%' AND a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.testipodatos_id=1");		
	}
public function getDatos_c($q)
{
		return $this->find_all_by_sql("SELECT concat_ws('-',a.direccion,a.distrito) as direccion_cliente,concat_ws('-',b.direccion,b.distrito) as direccion_proveedor,concat_ws('- ',a.razonsocial,a.ruc,a.codigo_ant) as name, b.id as id_proveedor, b.codigo_ant as codigo_proveedor, a.id as id_cliente, a.codigo_ant as codigo_cliente,b.ruc as ruc_proveedor, a.ruc as ruc_cliente
FROM tesdatos as a LEFT JOIN tesdatos as b ON a.ruc=b.ruc AND b.testipodatos_id=1 AND b.aclempresas_id=".Session::get('EMPRESAS_ID')." WHERE concat_ws(' ',a.razonsocial,a.codigo_ant,a.ruc) like '%".$q."%' AND a.aclempresas_id=".Session::get('EMPRESAS_ID')." AND a.testipodatos_id=2");		
}

/*Modificar para bucar tb los Proveedores = 1 y los Clientes = 2*/	
public function getDatosConPendientes()
{
	$sql="SELECT * FROM `tesdatos` WHERE aclempresas_id=".Session::get('EMPRESAS_ID')."  AND testipodatos_id=2 
AND ((select count(id)from tessalidas where tessalidas.npedido not like 'MT-%' AND tessalidas.tesdatos_id=tesdatos.id AND (tessalidas.estadosalida='Pendiente' or tessalidas.estadosalida='ADELANTADO') )+(select count(id)from tessalidasinternas where  tessalidasinternas.npedido not like 'MT-%' AND tessalidasinternas.tesdatos_id=tesdatos.id AND (tessalidasinternas.estadosalida='Pendiente' or tessalidasinternas.estadosalida='ADELANTADO')))>0 order by tesdatos.razonsocial ASC";
	return $this->find_all_by_sql($sql);
}
	/*Movimientos en soles*/
public function getMovimientos($dato_id,$fi,$ff,$id_prveedor,$ultimos=0) 
	{
		$fecha=$this->primerpendiente($dato_id);
		if(Auth::get('id')==3){
		echo Ajax::linkAction('ver_movimientos_cliente_reporte/'.$dato_id.'/'.$id_prveedor.'/'.$fecha.'/'.$ff,'Ver desde el ultimo Pendiente','ver','btn btn-info');
		//echo "&nbsp;";
		//echo Ajax::linkAction('ver_movimientos_cliente_reporte/'.$dato_id.'/'.$id_prveedor.'/'.$fecha.'/'.$ff.'/1','Ver 20 ultimos Movimientos','ver','btn btn-info');
		}
		//echo $id_prveedor;  AND s.tesdocumentos_id!=34 
		/*tesdetallesalidas, tessalidasinternas, tesabonosdetalles*/
		$igv=1+Session::get('IGV');
		$or_s="s.tesdatos_id=".$dato_id;
		$order='femision_s,numero_s DESC';
		
		$order='femision_s,numero_s ASC';
		
		$BETWEEN=" AND s.femision BETWEEN '".$fi."' AND '".$ff."'";
		$BETWEEN_fecha=" AND s.fecha BETWEEN '".$fi."' AND '".$ff."'";
		
		if($ultimos!=0){$order='femision_s ASC limit 0,20';$BETWEEN='';}
		/* AND s.tesdocumentos_id!=13*/
		$sql="(SELECT 'tessalidas' as tabla,
(case when s.tesdocumentos_id=7 then if(s.npedido like 'PA%','FA','VT') when s.tesdocumentos_id=34 then 'PT' when s.tesdocumentos_id=13 then 'NC' when s.tesdocumentos_id=14 then 'ND' ELSE 'VT' END )as mov_s, s.tesdocumentos_id as id_doc_s,s.id as sid,s.femision as femision_s,CONCAT_WS('-',s.serie,s.numero) as numero_s, if(s.totalconigv=0 or ISNULL(s.totalconigv),(SELECT sum(importe)*".$igv." as importe FROM tesdetallesalidas WHERE tessalidas_id=s.id),s.totalconigv )as monto_s,s.totalconigv as totalconigv,s.estadosalida as estado_s , s.tesmonedas_id as moneda,'' as numero_doc
FROM tessalidas as s 
WHERE s.estado=1 AND ".$or_s." AND s.tesdocumentos_id!=15 AND  (s.tesdocumentos_id!=34 OR s.movimiento='PT')".$BETWEEN.") 
UNION ALL 
( SELECT 'tessalidasinternas' as tabla,
(case when s.tesdocumentos_id=15 then if(s.npedido like 'PA%','FA','VT') when s.tesdocumentos_id=34 then 'LT' when s.tesdocumentos_id=13 then 'NC' when s.tesdocumentos_id=14 then 'ND' ELSE 'VT' END )as mov_s, s.tesdocumentos_id as id_doc_s,s.id as sid,s.femision as femision_s,CONCAT_WS('-',s.serie,s.numero) as numero_s, if(s.total=0 or ISNULL(s.total),(SELECT sum(IFNULL(importe,0)) as importe FROM tesdetallesalidasinternas WHERE tessalidasinternas_id=s.id),s.total )as monto_s,s.total as totalconigv,s.estadosalida as estado_s, s.tesmonedas_id as moneda,'' as numero_doc 
FROM tessalidasinternas as s WHERE s.estado=1 AND (s.tesdocumentos_id!=34 OR s.npedido LIKE  'LA%')  AND ".$or_s." AND s.npedido NOT like 'HL%'".$BETWEEN.")
 UNION ALL 
 ( SELECT 'tesabonos 14' as tabla,f.abr as mov_s, '0' as id_doc_s,s.id as sid,s.fecha as femision_s,CONCAT_WS('-',s.mes,s.numero) as numero_s, s.total as monto_s,s.total as totalconigv,s.estadov as estado_s , s.tesmonedas_id as moneda,(SELECT CASE WHEN !isnull(ad.tessalidas_id) THEN (SELECT concat_ws('-',serie,numero) from tessalidas where id=ad.tessalidas_id) WHEN !isnull(ad.tessalidasinternas_id) THEN (select concat_ws('-',serie,numero) from tessalidasinternas where id=ad.tessalidasinternas_id) ELSE '-' END) as numero_doc 
 FROM tesabonosdetalles as ad,tesformadepagosabonos as f,tesabonos as s 
 WHERE (ad.abono=0 OR s.tesformadepagosabonos_id=14) AND ad.tesabonos_id=s.id AND s.estado=1 AND f.id=s.tesformadepagosabonos_id AND ".$or_s.$BETWEEN_fecha." GROUP BY s.id)
 UNION
 ( SELECT 'tesabonos todos' as tabla,f.abr as mov_s, '0' as id_doc_s,s.id as sid,s.fecha as femision_s,CONCAT_WS('-',s.mes,s.numero) as numero_s, s.total as monto_s,s.total as totalconigv,s.estadov as estado_s , s.tesmonedas_id as moneda,(SELECT CASE WHEN !isnull(ad.tessalidas_id) THEN (SELECT concat_ws('-',serie,numero) from tessalidas where id=ad.tessalidas_id) WHEN !isnull(ad.tessalidasinternas_id) THEN (select concat_ws('-',serie,numero) from tessalidasinternas where id=ad.tessalidasinternas_id) ELSE '-' END) as numero_doc
 FROM tesabonosdetalles as ad,tesformadepagosabonos as f,tesabonos as s 
 WHERE ad.tessalidas_id=0 AND ad.tessalidasinternas_id=0 AND ad.abono=1 AND ad.tesabonos_id=s.id AND s.estado=1 AND f.id=s.tesformadepagosabonos_id AND (select count(dt.id) from tesabonosdetalles as dt WHERE dt.tesabonos_id=s.id)=1 AND ".$or_s.$BETWEEN_fecha." GROUP BY s.id) ORDER BY ".$order;
 /*echo $sql;
 echo "<br />";*/
		return $this->find_all_by_sql($sql);
	}
/* obtener el numero de l anota de credito para el reporte de movimientos cliente */
public function getNumeroNC($id_abono)
{ 	$numero='';
	$nc_s=$this->find_all_by_sql("SELECT (SELECT concat_ws('-',serie,numero) from tessalidas where id=d.tessalidas_id) as numero from tesabonosdetalles as d WHERE d.tesabonos_id=".$id_abono." AND d.abono=1 AND d.tessalidas_id!=0");
	$r=0;
	foreach($nc_s as $item)
	{
		$rest = substr($item->numero, -3); 
		$numero.= $r==0 ? $item->numero:','.$rest;
		$r++;
	}
	$nc_i=$this->find_all_by_sql("SELECT (SELECT concat_ws('-',serie,numero) from tessalidasinternas where id=d.tessalidasinternas_id) as numero from tesabonosdetalles as d WHERE d.tesabonos_id=".$id_abono." AND d.abono=1 AND d.tessalidasinternas_id!=0");
	$r=0;
	foreach($nc_i as $item)
	{
		$rest = substr($item->numero, -3); 
		$numero.= $r==0 ? $item->numero:','.$rest;
		$r++;
	}
	
	
	return $numero;
	
}

public function primerpendiente($id)
{
	$sql="select (SELECT min(femision) FROM tessalidas WHERE (estadosalida='Pendiente' or estadosalida='ADELANTADO') AND tesdatos_id=".$id." AND npedido not like 'LT-%') as f_s,
	(SELECT min(femision) FROM tessalidasinternas WHERE (estadosalida='Pendiente' or estadosalida='ADELANTADO') AND tesdatos_id=".$id." AND npedido not like 'LT-%') as f_i";
	//echo $sql;
	$val=$this->find_by_sql($sql);
	/*echo $id.'/*f_i: '.$val->f_i.'-';*/
	//echo 'f_s: '.$val->f_s.'*/';
	$fecha=date("Y-m-d");
	if(empty($val->f_s))
	{
		$fecha=$val->f_i;
	}
	if(empty($val->f_i))
	{
		$fecha=$val->f_s;
	}
	if(empty($val->f_i) && empty($val->f_s)){$fecha=date("Y-m-d");}
	if(!empty($val->f_i) && !empty($val->f_s))
	{
		if($val->f_s >= $val->f_i)
		{
			$fecha=$val->f_i;
		}else
		{
			$fecha=$val->f_s;
		}
	}
	
	return $fecha;
}

/* FINAL Funcion para devolver los 20 ultimos movimientos o desde el ultimo pendiente*/		
	
public function getLetrasPendientes($tesdatos_id)
{
	$sql="SELECT 
	DATE_FORMAT(f.fvencimiento,'%Y-%m') as y_m,
	DATE_FORMAT(f.fvencimiento,'%M') as nombre_mes,
	f.fvencimiento as fecha,
	IFNULL((SELECT sum(ls.monto) as monto FROM tesletrassalidasinternas AS ls INNER JOIN tessalidasinternas AS s ON s.id = ls.tessalidasinternas_id AND ls.estadoletras!='PAGADO' WHERE s.tesdatos_id =".$tesdatos_id." AND DATE_FORMAT(s.fvencimiento,'%Y-%m') = DATE_FORMAT(f.fvencimiento,'%Y-%m')),0) as monto_interno,
	IFNULL((SELECT sum(ls.monto) as monto FROM tesletrassalidas AS ls INNER JOIN tessalidas AS s ON s.id = ls.tessalidas_id AND ls.estadoletras!='PAGADO' WHERE s.tesdatos_id =".$tesdatos_id." AND DATE_FORMAT(s.fvencimiento,'%Y-%m')=DATE_FORMAT(f.fvencimiento,'%Y-%m')),0) as monto_ruc,
	(IFNULL((SELECT sum(ls.monto) as monto FROM tesletrassalidasinternas AS ls INNER JOIN tessalidasinternas AS s ON s.id = ls.tessalidasinternas_id AND ls.estadoletras!='PAGADO' WHERE s.tesdatos_id =".$tesdatos_id." AND DATE_FORMAT(s.fvencimiento,'%Y-%m')=DATE_FORMAT(f.fvencimiento,'%Y-%m')),0)+IFNULL((SELECT sum(ls.monto) as monto FROM tesletrassalidas AS ls INNER JOIN tessalidas AS s ON s.id = ls.tessalidas_id AND ls.estadoletras!='PAGADO' WHERE s.tesdatos_id =".$tesdatos_id." AND DATE_FORMAT(s.fvencimiento,'%Y-%m')=DATE_FORMAT(f.fvencimiento,'%Y-%m')),0)) as monto_total,
	f.tesmonedas_id as moneda
	FROM tessalidas as f 
	WHERE f.tesdatos_id=".$tesdatos_id." AND 
	((IFNULL((SELECT sum(ls.monto) as monto FROM tesletrassalidasinternas AS ls INNER JOIN tessalidasinternas AS s ON s.id = ls.tessalidasinternas_id AND ls.estadoletras!='PAGADO' WHERE s.tesdatos_id =".$tesdatos_id." AND DATE_FORMAT(s.fvencimiento,'%Y-%m')=DATE_FORMAT(f.fvencimiento,'%Y-%m')),0)+IFNULL((SELECT sum(ls.monto) as monto FROM tesletrassalidas AS ls
INNER JOIN tessalidas AS s ON s.id = ls.tessalidas_id AND ls.estadoletras!='PAGADO'
WHERE s.tesdatos_id =".$tesdatos_id." AND DATE_FORMAT(s.fvencimiento,'%Y-%m')=DATE_FORMAT(f.fvencimiento,'%Y-%m')),0))>0) GROUP BY DATE_FORMAT(f.fvencimiento,'%Y-%m')

/*# MONEDAS SOLES#*/
 ";
		//echo $sql;
		return $this->find_all_by_sql($sql);
	}
/*#
TOTATLES POR TESDATOS ID ID CARGOS Y ABONOS 
#*/
public function getCargos_s($dato_id,$id_prveedor,$moneda=1)
{
	/*#CARGOS SOLES#  AND (select count(id) from tesaplicacionletrainterna WHERE tesaplicacionletrainterna.tessalidasinternas_id=s.id)=0 */
	$igv=1+Session::get('IGV');
		$or_d="s.tesdatos_id=".$dato_id;
		return $this->find_by_sql("SELECT(
(SELECT IFNULL(sum(s.totalconigv),0) as cargos FROM tessalidas as s WHERE s.estadosalida!='ANULADO' AND s.tesmonedas_id=".$moneda." AND (s.tesdocumentos_id=7 OR s.tesdocumentos_id=14 OR s.movimiento='PT') AND ".$or_d."
)
+
(SELECT IFNULL(sum(s.total),0) as cargos FROM tessalidasinternas as s WHERE s.estadosalida!='ANULADO' AND s.tesmonedas_id=".$moneda." AND (s.tesdocumentos_id=15 OR s.tesdocumentos_id=14 OR s.npedido LIKE 'LA%') AND s.npedido NOT LIKE 'MT%' AND ".$or_d." AND (select count(id) from tesaplicacionletrainterna WHERE tesaplicacionletrainterna.tessalidasinternas_id=s.id)=0 )) as cargos_s");
}

public function getCargos_d($dato_id,$id_prveedor,$moneda=2)
{/*#CARGOS DOLARES#  AND (select count(id) from tesaplicacionletrainterna WHERE tesaplicacionletrainterna.tessalidasinternas_id=d.id)=0 */
	$igv=1+Session::get('IGV');
		$or_d="d.tesdatos_id=".$dato_id;
	return $this->find_by_sql("SELECT(
(SELECT IFNULL(sum(d.totalconigv),0) as cargos FROM tessalidas as d WHERE d.estadosalida!='ANULADO' AND d.tesmonedas_id=".$moneda." AND (d.tesdocumentos_id=7 OR d.tesdocumentos_id=14 OR d.movimiento='PT') AND ".$or_d."
)
+
(SELECT IFNULL(sum(d.total),0) as cargos FROM tessalidasinternas as d WHERE d.estadosalida!='ANULADO' AND d.tesmonedas_id=".$moneda." AND (d.tesdocumentos_id=15 OR d.tesdocumentos_id=14 OR d.npedido LIKE 'LA%') AND d.npedido NOT LIKE 'MT%' AND ".$or_d." AND (select count(id) from tesaplicacionletrainterna WHERE tesaplicacionletrainterna.tessalidasinternas_id=d.id)=0)) as cargos_d");
}

public function getAbonos_s($dato_id,$id_prveedor,$moneda=1)
{/*#ABONOS SOLES#*/
	$igv=1+Session::get('IGV');
		$or_d="s.tesdatos_id=".$dato_id;
		
		/*$sql='SELECT( 
(SELECT IFNULL(sum(if(s.totalconigv=0 or ISNULL(s.totalconigv),(SELECT sum(importe)*'.$igv.' as importe FROM tesdetallesalidas WHERE tessalidas_id=s.id),s.totalconigv )),0) as cargos FROM tessalidas as s WHERE s.tesmonedas_id='.$moneda.' AND s.tesdocumentos_id=13 AND '.$or_d.' 
) 
+ 
(SELECT IFNULL(sum(s.total),0) FROM tesabonos as s WHERE s.tesmonedas_id='.$moneda.' AND '.$or_d.' )
)
+
(SELECT IFNULL(sum(s.total),0) FROM tessalidasinternas as s WHERE s.tesdocumentos_id=13 AND s.tesmonedas_id='.$moneda.' AND '.$or_d.') as abonos_s';*/
$sql="SELECT IFNULL(sum(s.total),0) as abonos_s FROM tesabonos as s WHERE s.tesmonedas_id=".$moneda." AND ".$or_d;
//echo $sql;
	return $this->find_by_sql($sql);
	
}
public function getNC_Anulados_s($dato_id,$id_prveedor,$moneda=1)
{
	$or_d="s.tesdatos_id=".$dato_id;
	$sql="SELECT IFNULL(sum(s.total),0) as resta_abono_s FROM tesabonos as s WHERE s.tesformadepagosabonos_id=16 AND s.tesmonedas_id=".$moneda." AND ".$or_d." AND (select count(id) from tesabonosdetalles as d WHERE d.tesabonos_id=s.id AND d.abono=1 AND d.monto=0)=1 AND (select count(id) from tesabonosdetalles as d WHERE d.tesabonos_id=s.id AND d.cargos=1)=0";
	return $this->find_by_sql($sql);
}
public function getNC_Anulados_d($dato_id,$id_prveedor,$moneda=2)
{
	$or_d="s.tesdatos_id=".$dato_id;
	
	$sql="SELECT IFNULL(sum(s.total),0) as resta_abono_d FROM tesabonos as s WHERE s.tesformadepagosabonos_id=16 AND s.tesmonedas_id=".$moneda." AND ".$or_d." AND (select count(id) from tesabonosdetalles as d WHERE d.tesabonos_id=s.id AND d.abono=1 AND d.monto=0)=1 AND (select count(id) from tesabonosdetalles as d WHERE d.tesabonos_id=s.id AND d.cargos=1)=0";
	return $this->find_by_sql($sql);
}
public function getAbonos_d($dato_id,$id_prveedor,$moneda=2)
{/*#ABONOS DOLARES#*/
	$igv=1+Session::get('IGV');
		$or_d="d.tesdatos_id=".$dato_id;
$sql="SELECT IFNULL(sum(d.total),0) as abonos_d FROM tesabonos as d WHERE d.tesmonedas_id=".$moneda." AND ".$or_d;
	return $this->find_by_sql($sql);/*'SELECT( 
(SELECT IFNULL(sum(if(d.totalconigv=0 or ISNULL(d.totalconigv),(SELECT sum(importe)*'.$igv.' as importe FROM tesdetallesalidas WHERE tessalidas_id=d.id),d.totalconigv )),0) as cargos FROM tessalidas as d WHERE d.tesmonedas_id='.$moneda.' AND d.tesdocumentos_id=13 AND '.$or_d.' 
) 
+ 
(SELECT IFNULL(sum(d.total),0) FROM tesabonos as d WHERE d.tesmonedas_id='.$moneda.' AND '.$or_d.' )
)
+
(SELECT IFNULL(sum(d.total),0) FROM tessalidasinternas as d WHERE d.tesdocumentos_id=13 AND d.tesmonedas_id='.$moneda.' AND '.$or_d.') as abonos_d'*/
}
/* PARA VER LO QUE FALTA PAGAR SI LA FACTURA FUE ADELATADA*/
public function getDetalleAbono($id,$tabla='tessalidas')
{
	$d=$this->find_by_sql('SELECT sum(monto) as monto FROM `tesabonosdetalles` WHERE '.$tabla.'_id='.$id);
	
	return $d->monto; 
}

/*validar proveedor y cliente si tiene movientos*/
public function validar_dato($id)
{
	/*tesingresos,tessalidas,tessalidasinternas,pronotas,tesabonos,tesvaucers*/
	$i=$this->find_by_sql('SELECT count(id) as total FROM tesingresos WHERE tesdatos_id='.$id);
	$s=$this->find_by_sql('SELECT count(id) as total  FROM tessalidas WHERE tesdatos_id='.$id);
	$s_i=$this->find_by_sql('SELECT count(id) as total  FROM tessalidasinternas WHERE tesdatos_id='.$id);
	$n=$this->find_by_sql('SELECT count(id) as total  FROM pronotapedidos WHERE tesdatos_id='.$id);
	$a=$this->find_by_sql('SELECT count(id) as total  FROM tesabonos WHERE tesdatos_id='.$id);
	$v=$this->find_by_sql('SELECT count(id) as total  FROM tesdetallevauchers WHERE tesdatos_id='.$id);
	$total=$i->total+$s->total+$s_i->total+$n->total+$a->total+$v->total;
	return $total;
}

public function validar_ruc($ruc,$id)
{
	if($this->exists('ruc='.$ruc,'conditions: testipodatos_id='.$id))
	{
		return TRUE;
	}else
	{
		return FALSE;
	}
}
public function getDatosConMovimientos($id){
/*,
(SELECT count(id) as total FROM tesingresos WHERE tesdatos_id=d.id)as i,
(SELECT count(id) as total  FROM tessalidas WHERE tesdatos_id=d.id)as s,
(SELECT count(id) as total  FROM tessalidasinternas WHERE tesdatos_id=d.id)as si,
(SELECT count(id) as total  FROM pronotapedidos WHERE tesdatos_id=d.id)as p,
(SELECT count(id) as total  FROM tesabonos WHERE tesdatos_id=d.id)as a,
(SELECT count(id) as total  FROM tesdetallevauchers WHERE tesdatos_id=d.id)as v*/
	$sql="SELECT d.id as dato_id
FROM tesdatos as d WHERE ((SELECT count(id) as total FROM tesingresos WHERE tesdatos_id=d.id)+
(SELECT count(id) as total  FROM tessalidas WHERE tesdatos_id=d.id)+
(SELECT count(id) as total  FROM tessalidasinternas WHERE tesdatos_id=d.id)+
(SELECT count(id) as total  FROM pronotapedidos WHERE tesdatos_id=d.id)+
(SELECT count(id) as total  FROM tesabonos WHERE tesdatos_id=d.id)+
(SELECT count(id) as total  FROM tesdetallevauchers WHERE tesdatos_id=d.id))>1 AND d.aclempresas_id=".Session::get('EMPRESAS_ID')." AND d.testipodatos_id=".$id;
	
	//echo $sql;

	$datos=$this->find_all_by_sql($sql);
	$DD=array();
	$n=0;
	foreach($datos as $d):
	$DD[$n]=$d->dato_id;
	$n++;
	endforeach;
	return $DD;


}


}
?>