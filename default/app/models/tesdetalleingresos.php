<?php
Load::models('testipoproductos');
class Tesdetalleingresos extends ActiveRecord
{

public function initialize()
{
	//relaciones
	$this->has_many('tescajas');
	$this->belongs_to('tesunidadesmedidas','tesproductos','tesingresos','tescolores','prorollos','sc_calidades');
}
	
public function getDetalles($id_ingreso=0,$tipoproducto=0)
{
	return $this->find_all_by_sql('SELECT td.* FROM tesdetalleingresos as td, tesproductos as p, testipoproductos as tp WHERE td.tesingresos_id='.$id_ingreso.' AND td.aclempresas_id='.Session::get('EMPRESAS_ID').' td.tesproductos_id=p.id AND p.testipoproductos_id=tp.id AND tp.id='.$tipoproducto);
}

/* detalle de un inventario*/
public function getDetallesI($id_ingreso=0)
{
	//echo $id_ingreso;DATE_FORMAT(fecha_at,"%Y")="2013" AND 
	return $this->find('conditions: tesingresos_id='.$id_ingreso.' AND !ISNULL(cantidad) AND cantidad>0 AND aclempresas_id='.Session::get('EMPRESAS_ID'));
}

/*Todos los detalles no importa colo o lote*/
public function getDetallesProductos($tipoproducto=0,$tipo='tesproductos_id',$id=0,$adicional='')
{
	$uno=' ';
	if($id!=0)$uno=' td.tesproductos_id='.$id.' AND '.$adicional;
	$producto='';
	if($tipo=='tesproductos_id'){$producto='td.tesproductos_id';}
	if($tipo=='tescolores_id'){$producto='td.tesproductos_id,td.tescolores_id';}
	if($tipo=='lote'){$producto='td.tesproductos_id,td.tescolores_id,td.lote';}
	$sql='
(SELECT p.id as id,td.tesproductos_id as tesproductos_id,td.tescolores_id as tescolores_id,td.tesunidadesmedidas_id as tesunidadesmedidas_id, td.lote as lote,td.sc_calidades_id as sc_calidades_id FROM tesproductos as p, tesdetalleingresos as td, testipoproductos as tp,tesingresos as i WHERE'.$uno.'i.id=td.tesingresos_id AND i.aclempresas_id='.Session::get('EMPRESAS_ID').' AND td.tesproductos_id=p.id AND p.testipoproductos_id=tp.id AND td.tescolores_id!=0 AND tp.id='.$tipoproducto.' group by '.$producto.'
)
UNION DISTINCT
(
SELECT p.id as id,td.tesproductos_id as tesproductos_id,td.tescolores_id as tescolores_id,td.tesunidadesmedidas_id as tesunidadesmedidas_id, td.lote as lote,td.sc_calidades_id as sc_calidades_id FROM tesproductos as p, prodetallepedidos as td, pronotapedidos as pn, testipoproductos as tp WHERE'.$uno.'td.tesproductos_id=p.id AND pn.aclempresas_id='.Session::get('EMPRESAS_ID').' AND p.testipoproductos_id=tp.id AND td.tescolores_id!=0 AND pn.id=td.pronotapedidos_id AND pn.tipo="ingreso" AND pn.origen!="Cajas" AND tp.id='.$tipoproducto.' group by '.$producto.'
)
ORDER BY tesproductos_id, tescolores_id ASC';
	echo $sql;
	return $this->find_all_by_sql($sql);		
}
	
/*Todos Los detalles por tipode ingreso (Chenille,urdido,trama,otro,devolucion) solo para Hilos*/
public function getDetallesProductos_ubicacion($linea_id=3,$tipo='tesproductos_id',$id=0,$ubicacion)
{
	$uno='i.almacen='.$ubicacion.' AND ';
	if($id!=0)$uno='td.tesproductos_id='.$id.' AND '.$adicional;
	$producto='';
	if($tipo=='tescolores_id' || $tipo=='lote'){$producto='td.tesproductos_id,';}
	/*echo 'SELECT td.* FROM tesdetalleingresos as td, tesproductos as p, testipoproductos as tp,tesingresos as i WHERE '.$uno.'i.id=td.tesingresos_id AND td.tesproductos_id=p.id AND p.testipoproductos_id=tp.id AND td.tescolores_id!=0 AND ('.$this->obtener_tipo_productos($linea_id).')  group by '.$producto.'td.'.$tipo.' ORDER BY  td.tesproductos_id,td.tescolores_id ASC';*/
	return $this->find_all_by_sql('SELECT td.* FROM tesdetalleingresos as td, tesproductos as p, testipoproductos as tp,tesingresos as i WHERE '.$uno.'i.id=td.tesingresos_id AND i.aclempresas_id='.Session::get('EMPRESAS_ID').' AND td.tesproductos_id=p.id AND p.testipoproductos_id=tp.id AND td.tescolores_id!=0 AND ('.$this->obtener_tipo_productos($linea_id).')  group by '.$producto.'td.'.$tipo.' ORDER BY  td.tesproductos_id,td.tescolores_id ASC');
}
	
/*Todos los detalles Para el resumen de mes*/
public function getDetallesProductosResumen($linea_id=0,$tipo='tesproductos_id',$fecha_i)
{
	$fecha = new DateTime($fecha_i);
	$fecha->modify('first day of this month');
	$fecha_inicio=$fecha->format('Y-m-d');
	/*last day of this month*/
	$fecha = new DateTime($fecha_i);
	$fecha->modify('last day of this month');
	//echo $fecha_fin=$fecha->format('Y-m-d');
	$uno='';
	$producto='';
	if($tipo=='tescolores_id' || $tipo=='lote'){$producto='td.tesproductos_id,';}
	$sql='(SELECT td.prorollos_id as prorollos_id,td.tesproductos_id as tesproductos_id,td.tescolores_id as tescolores_id,td.tesunidadesmedidas_id as tesunidadesmedidas_id FROM tesdetalleingresos as td, tesproductos as p, testipoproductos as tp,tesingresos as i WHERE i.id=td.tesingresos_id AND i.aclempresas_id='.Session::get('EMPRESAS_ID').' AND td.tesproductos_id=p.id AND p.testipoproductos_id=tp.id AND td.tescolores_id!=0 AND ('.$this->obtener_tipo_productos($linea_id).') group by '.$producto.'td.'.$tipo.' 
)
UNION DISTINCT
(
SELECT td.prorollos_id as prorollos_id,td.tesproductos_id as tesproductos_id,td.tescolores_id as tescolores_id,td.tesunidadesmedidas_id as tesunidadesmedidas_id FROM prodetallepedidos as td, pronotapedidos as pn, tesproductos as p, testipoproductos as tp WHERE td.tesproductos_id=p.id  AND pn.aclempresas_id='.Session::get('EMPRESAS_ID').' AND p.testipoproductos_id=tp.id AND td.tescolores_id!=0 AND pn.id=td.pronotapedidos_id AND pn.tipo="ingreso" AND pn.origen="Telas" AND ('.$this->obtener_tipo_productos($linea_id).') group by '.$producto.'td.'.$tipo.' 
)';
//echo $sql;
	return $this->find_all_by_sql($sql);
}
	
/*recibe el id de la familia de productos*/
protected function obtener_tipo_productos($id)
{
	$obj = new Testipoproductos();
	$tipos=$obj->find('conditions: estado=1 AND activo=1 AND teslineaproductos_id='.$id);
    $tipos_pro = array();
    foreach ($tipos as $e)
	{
		$tipos_pro[] = "tp.id = '$e->id'";
    }
    return join(' OR ', $tipos_pro);
}
	
/*detalle para ver kardex y movimeintos*/
public function getKardex($producto=0,$color=0,$lote='',$fechainicio,$fechafin)
{
	//echo $lote."<br />";
	if($lote==0 && $producto==0 && $color==0){return FALSE;}
	$productos=Load::model('tesproductos');
	$prod=$productos->find_first((int)$producto);
	$linea=$prod->getTestipoproductos()->teslineaproductos_id;
	
	$cond_lote='';
	$cond_producto='';
	$cond_color='';
	if($lote!='')
	{
	  	$cond_lote=' AND d.lote="'.$lote.'"';
	}
	if($producto!=0)
	{
	  	$cond_producto=' AND d.tesproductos_id='.$producto;
	}
	if($color!=0)
	{
	  	$cond_color=' AND d.tescolores_id='.$color;
	}
	/*para la busqueda de telas*/
	//echo "LINEA ES ".$linea;
	if($linea==1)
	{
	//AND rollo.estadorollo!='TERMINADO'
	$detalles=$this->find_all_by_sql("
(SELECT rollo.id as prorollos_id, CONCAT_WS(' ',doc.abr, i.serie,i.numero) as documento,i.femision as fecha, i.fecha_at as  registro, d.lote as lote, 'ingreso' as tipo, d.cantidad as cantidad, d.preciounitario as precio,i.glosa as descripcion, d.tescolores_id as tescolores_id FROM tesdetalleingresos as d, tesingresos as i, tesdocumentos as doc, prorollos as rollo WHERE d.prorollos_id=rollo.id AND i.aclempresas_id=".Session::get('EMPRESAS_ID')." AND (i.estadoingreso='PAGADO' OR i.estadoingreso='Pendiente' OR i.estadoingreso='INGRESO-CH') AND !ISNULL( d.prorollos_id ) AND i.id=d.tesingresos_id AND doc.id=i.tesdocumentos_id".$cond_producto.$cond_color.$cond_lote." AND i.femision  BETWEEN '".$fechainicio."' AND '".$fechafin."'$cond_lote )
union all
(SELECT rollo.id as prorollos_id, CONCAT_WS(' ',doc.abr, s.serie,s.numero) as documento, s.femision as fecha, s.fecha_at as  registro, d.lote as lote, 'salida' as tipo, d.cantidad as cantidad, d.preciounitario as precio, s.glosa as descripcion, d.tescolores_id as tescolores_id FROM tesdetallesalidas d, tessalidas as s, tesdocumentos as doc, prorollos as rollo WHERE d.prorollos_id=rollo.id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.id=d.tessalidas_id AND s.estadosalida!='Editable' AND s.estadosalida!='Con factura' AND !ISNULL( d.prorollos_id ) AND doc.id=s.tesdocumentos_id".$cond_producto.$cond_color.$cond_lote."  AND s.femision  BETWEEN '".$fechainicio."' AND '".$fechafin."'$cond_lote)
union all
(SELECT rollo.id as prorollos_id, CONCAT_WS(' ',doc.abr, s.serie,s.numero) as documento, s.femision as fecha, s.fecha_at as  registro, d.lote as lote, 'salida' as tipo, d.cantidad as cantidad, d.preciounitario as precio, s.glosa as descripcion, d.tescolores_id as tescolores_id FROM tesdetallesalidasinternas d, tessalidasinternas as s, tesdocumentos as doc, prorollos as rollo WHERE d.prorollos_id=rollo.id  AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.id=d.tessalidasinternas_id AND s.estadosalida!='Editable' AND s.estadosalida!='Con factura' AND !ISNULL( d.prorollos_id ) AND doc.id=s.tesdocumentos_id".$cond_producto.$cond_color.$cond_lote."  AND s.femision  BETWEEN '".$fechainicio."' AND '".$fechafin."'$cond_lote)
union all
(SELECT rollo.id as prorollos_id, CONCAT_WS(' ','PD',pd.codigo) as documento, DATE_FORMAT(pd.fecha_at,'%Y-%m-%d') as fecha, pd.fecha_at as  registro, d.lote as lote, pd.tipo as tipo, d.cantidad as cantidad, d.precio as precio,pd.descripcion as descripcion, d.tescolores_id as tescolores_id FROM prodetallepedidos d, pronotapedidos as pd, tesproductos as tp, prorollos as rollo WHERE d.prorollos_id=rollo.id AND pd.aclempresas_id=".Session::get('EMPRESAS_ID')."  AND (pd.estadonota='Ingresado' or pd.estadonota='Entregado' or pd.estadonota='Revisado') AND pd.tipo='ingreso' AND !ISNULL( d.prorollos_id ) AND pd.id=d.pronotapedidos_id AND tp.id=d.tesproductos_id".$cond_producto.$cond_color.$cond_lote." AND pd.fecha_at  BETWEEN '".$fechainicio."' AND '".$fechafin."'$cond_lote )
union all
(SELECT rollo.id as prorollos_id, CONCAT_WS(' ','PD',pd.codigo) as documento, DATE_FORMAT(pd.fecha_at,'%Y-%m-%d') as fecha, pd.fecha_at as  registro, d.lote as lote, pd.tipo as tipo, d.cantidad as cantidad, d.precio as precio ,pd.descripcion as descripcion, d.tescolores_id as tescolores_id FROM prodetallepedidos d, pronotapedidos as pd, tesproductos as tp, prorollos as rollo WHERE d.prorollos_id=rollo.id AND pd.aclempresas_id=".Session::get('EMPRESAS_ID')." AND (pd.estadonota='Ingresado' or pd.estadonota='Entregado' or pd.estadonota='Revisado') AND pd.tipo='salida' AND pd.id=d.pronotapedidos_id AND !ISNULL( d.prorollos_id ) AND tp.id=d.tesproductos_id".$cond_producto.$cond_color.$cond_lote." AND pd.fecha_at  BETWEEN '".$fechainicio."' AND '".$fechafin."'$cond_lote )
ORDER BY registro,fecha ASC");
	}else
	{
	/*para la busqueda de productos normales*/
	$detalles=$this->find_all_by_sql("
(SELECT i.id as id,CONCAT_WS(' ',doc.abr, i.serie,i.numero) as documento,i.femision as fecha,  d.lote as lote, 'ingreso' as tipo, d.cantidad as cantidad, d.preciounitario as precio,i.glosa as descripcion, d.tescolores_id as tescolores_id FROM tesdetalleingresos as d, tesingresos as i, tesdocumentos as doc WHERE (i.estadoingreso='PAGADO' OR i.estadoingreso='Pendiente' OR i.estadoingreso='INGRESO-CH') AND i.aclempresas_id=".Session::get('EMPRESAS_ID')."  AND ISNULL( d.prorollos_id ) AND i.id=d.tesingresos_id AND doc.id=i.tesdocumentos_id".$cond_producto.$cond_color.$cond_lote." AND i.femision  BETWEEN '".$fechainicio."' AND '".$fechafin."'$cond_lote )
union all
(SELECT s.id as id,CONCAT_WS(' ',doc.abr, s.serie,s.numero) as documento, s.femision as fecha,  d.lote as lote, 'salida' as tipo, d.cantidad as cantidad, d.preciounitario as precio, 'descripcion' as descripcion, d.tescolores_id as tescolores_id FROM tesdetallesalidasinternas d, tessalidasinternas as s, tesdocumentos as doc WHERE  s.id=d.tessalidasinternas_id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND ISNULL( d.prorollos_id ) AND doc.id=s.tesdocumentos_id".$cond_producto.$cond_color.$cond_lote."  AND s.femision  BETWEEN '".$fechainicio."' AND '".$fechafin."'$cond_lote)
union all
(SELECT s.id as id,CONCAT_WS(' ',doc.abr, s.serie,s.numero) as documento, s.femision as fecha,  d.lote as lote, 'salida' as tipo, d.cantidad as cantidad, d.preciounitario as precio, s.glosa as descripcion, d.tescolores_id as tescolores_id FROM tesdetallesalidas d, tessalidas as s, tesdocumentos as doc WHERE s.id=d.tessalidas_id AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND ISNULL( d.prorollos_id ) AND doc.id=s.tesdocumentos_id".$cond_producto.$cond_color.$cond_lote."  AND s.femision  BETWEEN '".$fechainicio."' AND '".$fechafin."'$cond_lote)
union all
(SELECT i.id as id,CONCAT_WS(' ',doc.abr, i.serie,i.numero) as documento, i.femision as fecha, d.lote as lote, 'salida' as tipo, d.cantidad as cantidad, d.preciounitario as precio,i.glosa as descripcion, d.tescolores_id as tescolores_id FROM tesdetalleingresos as d, tesingresos as i, tesdocumentos as doc WHERE i.autosalida='1' AND i.aclempresas_id=".Session::get('EMPRESAS_ID')." AND i.id=d.tesingresos_id AND ISNULL( d.prorollos_id ) AND doc.id=i.tesdocumentos_id".$cond_producto.$cond_color.$cond_lote."  AND i.femision  BETWEEN '".$fechainicio."' AND '".$fechafin."'$cond_lote )
union all
(SELECT pd.id as id,CONCAT_WS(' ','PD',pd.codigo) as documento, pd.fecha_at as fecha, d.lote as lote, pd.tipo as tipo, d.cantidad as cantidad, d.precio as precio,pd.descripcion as descripcion, d.tescolores_id as tescolores_id FROM prodetallepedidos d, pronotapedidos as pd, tesproductos as tp WHERE pd.tipo='ingreso' AND pd.aclempresas_id=".Session::get('EMPRESAS_ID')."  AND ISNULL( d.prorollos_id ) AND pd.id=d.pronotapedidos_id AND tp.id=d.tesproductos_id".$cond_producto.$cond_color.$cond_lote." AND pd.fecha_at  BETWEEN '".$fechainicio."' AND '".$fechafin."'$cond_lote )
union all
(SELECT pd.id as id,CONCAT_WS(' ','PD',pd.codigo) as documento, pd.fecha_at as fecha, d.lote as lote, pd.tipo as tipo, d.cantidad as cantidad, d.precio as precio ,pd.descripcion as descripcion, d.tescolores_id as tescolores_id FROM prodetallepedidos d, pronotapedidos as pd, tesproductos as tp WHERE pd.tipo='salida' AND pd.aclempresas_id=".Session::get('EMPRESAS_ID')."  AND pd.id=d.pronotapedidos_id AND ISNULL( d.prorollos_id ) AND tp.id=d.tesproductos_id".$cond_producto.$cond_color.$cond_lote." AND pd.fecha_at  BETWEEN '".$fechainicio."' AND '".$fechafin."'$cond_lote )
ORDER BY fecha ASC");
	}
	return $detalles;
}

/*saldo de los productos para resumenes*/
public function getSaldo($producto,$color=0,$lote=0,$fecha)
{
	$c=0;
	$hh='';
	if($lote!=0)
	{
		$ingresos=Load::model('tesdetalleingresos');
		if($ingresos->exists('tesproductos_id='.$producto.' AND tescolores_id='.$color.' AND lote='.$lote)){
		$ingresos->find_first('columns: preciounitario','conditions: tesproductos_id='.$producto.' AND tescolores_id='.$color.' AND lote='.$lote);
		 $a=$this->getTotalingresos($producto,$color,$lote,$fecha);
		 $b=$this->getTotalsalidas($producto,$color,$lote,$fecha);
		$c=(float)$a-(float)$b;
		$hh=$c.'-'.$ingresos->preciounitario;
		}else
		{
		$hh=$c.'-0';
		}
		return $hh;
	}else
	{
		$a=$this->getTotalingresos($producto,$color,'',$fecha);
		$b=$this->getTotalsalidas($producto,$color,'',$fecha);
		if($producto==69)echo $a."-".$b;
		(float)$c=(float)$a-(float)$b;
		return (float)$c;
	}
}	
public function getTotalingresos($id=0,$color_id=0,$lote='',$fecha='',$calidad='')
{
	$a=$this->getIngresos_f($id,$color_id,$lote,$fecha,$calidad);
	$b=$this->getIngresos_g($id,$color_id,$lote,$fecha,$calidad);
	$c=$this->getIngresos_p($id,$color_id,$lote,$fecha,$calidad);
	/*if($id==69){
	echo " F: ".$a;
	echo " G: ".$b;
	echo " P: ".$c;
	echo "<br />";
	}*/
	return $a+$c+$b;
}
public function getIngresos_g($id=0,$color_id=0,$lote='',$fecha='',$calidad='')
{
	$t=0;
	if($id!=0)
	{
	$ingresos=Load::model('tesdetalleingresos');
	if($color_id==0){$cond='tesdetalleingresos.tesproductos_id='.$id;}
	if($color_id!=0)
	{
		$cond='tesdetalleingresos.tescolores_id='.$color_id;
			if($id!=0)$cond.=' AND tesdetalleingresos.tesproductos_id='.$id;
	}
	if($lote!='')
	{
		$cond='tesdetalleingresos.lote="'.$lote.'"';
		if($color_id!=0)$cond.=' AND tesdetalleingresos.tescolores_id='.$color_id;
		if($id!=0)$cond.=' AND tesdetalleingresos.tesproductos_id='.$id;
	}
	if($calidad!='')
	{
		$cond='tesdetalleingresos.sc_calidades_id="'.$calidad.'"';
			if($color_id!=0)$cond.=' AND tesdetalleingresos.tescolores_id='.$color_id;
			if($id!=0)$cond.=' AND tesdetalleingresos.tesproductos_id='.$id;
	}
		
		/*echo 'aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond;*/
    $join = 'INNER JOIN tesingresos as i ON i.id = tesdetalleingresos.tesingresos_id AND (i.estadoingreso="PAGADO" OR i.estadoingreso="Pendiente" OR i.estadoingreso="INGRESO-CH") AND i.aclempresas_id='.Session::get("EMPRESAS_ID");
			
	$di_g=$ingresos->sum('tesdetalleingresos.cantidad','conditions: !ISNULL(tesdetalleingresos.cantidad) 
AND tesdetalleingresos.cantidad >0 AND (ISNULL(tesdetalleingresos.prorollos_id) OR tesdetalleingresos.prorollos_id=0) AND '.$cond,'join: INNER JOIN tesingresos as i ON i.id = tesdetalleingresos.tesingresos_id AND (i.estadoingreso="PAGADO" OR i.estadoingreso="Pendiente" OR i.estadoingreso="INGRESO-CH" or i.estadoingreso="Con factura") AND i.aclempresas_id='.Session::get("EMPRESAS_ID").' AND (i.tesdocumentos_id=15 OR i.tesdocumentos_id=27) AND i.femision<="'.$fecha.'"');
/* PARA LAS TELAS SIN CORTE  AND r.estadorollo!="TERMINADO"*/
	$isc=$ingresos->find_by_sql('SELECT SUM( tesdetalleingresos.cantidad ) AS tsc FROM prorollos AS r, tesdetalleingresos INNER JOIN tesingresos AS i ON i.id = tesdetalleingresos.tesingresos_id AND (i.estadoingreso="Pendiente" OR i.estadoingreso="TERMINADO" OR i.estadoingreso="PAGADO") AND (i.tesdocumentos_id=15 OR i.tesdocumentos_id=27) AND i.femision<="'.$fecha.'" AND i.aclempresas_id='.Session::get('EMPRESAS_ID').' WHERE (!ISNULL(tesdetalleingresos.prorollos_id)) AND r.id = tesdetalleingresos.prorollos_id AND ISNULL(r.prorollos_id) AND '.$cond);
	//echo $isc->total.'inventario telas sin corte<br />';
	/* PARA LAS TELAS CON CORTE*/
	$icc=$ingresos->find_by_sql('SELECT SUM( tesdetalleingresos.cantidad ) AS tcc FROM prorollos AS r, tesdetalleingresos INNER JOIN tesingresos AS i ON i.id = tesdetalleingresos.tesingresos_id AND(i.estadoingreso =  "Pendiente" OR i.estadoingreso =  "TERMINADO" OR i.estadoingreso =  "PAGADO") AND (i.tesdocumentos_id=15 OR i.tesdocumentos_id=27) AND i.femision<="'.$fecha.'" AND i.aclempresas_id='.Session::get('EMPRESAS_ID').' WHERE (!ISNULL(tesdetalleingresos.prorollos_id)) AND r.id = tesdetalleingresos.prorollos_id AND !ISNULL(r.prorollos_id) AND '.$cond);
	
	(float)$t=(float)$di_g+$isc->tsc+(float)$icc->tcc;
	}
	//echo ''.$t.' Ingresos <br />';
	return $total=number_format((float)$t,2,'.','');
}

public function getIngresos_f($id=0,$color_id=0,$lote='',$fecha='',$calidad='')
{
	$t=0;
	if($id!=0)
	{
	$ingresos=Load::model('tesdetalleingresos');
	if($color_id==0){
		$cond='tesdetalleingresos.tesproductos_id='.$id;
		}
	if($color_id!=0){
		$cond='tesdetalleingresos.tescolores_id='.$color_id;
			if($id!=0)$cond.=' AND tesdetalleingresos.tesproductos_id='.$id;
		}
	if($lote!=''){
		$cond='tesdetalleingresos.lote="'.$lote.'"';
			if($color_id!=0)$cond.=' AND tesdetalleingresos.tescolores_id='.$color_id;
			if($id!=0)$cond.=' AND tesdetalleingresos.tesproductos_id='.$id;
		}
		if($calidad!=''){
		$cond='tesdetalleingresos.sc_calidades_id="'.$calidad.'"';
			if($color_id!=0)$cond.=' AND tesdetalleingresos.tescolores_id='.$color_id;
			if($id!=0)$cond.=' AND tesdetalleingresos.tesproductos_id='.$id;
		}
		
		/*echo 'aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond;*/
    $join = 'INNER JOIN tesingresos as i ON i.id = tesdetalleingresos.tesingresos_id AND (i.estadoingreso="PAGADO" OR i.estadoingreso="Pendiente" OR i.estadoingreso="INGRESO-CH") AND i.aclempresas_id='.Session::get("EMPRESAS_ID");
	$di_f=0;
	$di_f=$ingresos->sum('tesdetalleingresos.cantidad','conditions: !ISNULL(tesdetalleingresos.cantidad) 
AND tesdetalleingresos.cantidad >0 AND (ISNULL(tesdetalleingresos.prorollos_id) OR tesdetalleingresos.prorollos_id=0) AND tesdetalleingresos.aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond,'join: '.$join.' AND tesdocumentos_id=7 AND isnull(numeroguia) AND i.femision<="'.$fecha.'"');

	(float)$t=(float)$di_f;
	}
	//echo ''.$t.' Ingresos <br />';
	return $total=number_format((float)$t,2,'.','');
}
public function getIngresos_p($id=0,$color_id=0,$lote='',$fecha='',$calidad='')
{
	$t=0;
	if($id!=0)
	{
	$pedidos=Load::model('pronotapedidos');
	if($color_id==0){
		$condp='prodetallepedidos.tesproductos_id='.$id;
		}
	if($color_id!=0){
		$condp='prodetallepedidos.tescolores_id='.$color_id;
			if($id!=0)$condp.=' AND prodetallepedidos.tesproductos_id='.$id;
		}
	if($lote!=''){
		$condp='prodetallepedidos.lote="'.$lote.'"';
			if($color_id!=0)$condp.=' AND prodetallepedidos.tescolores_id='.$color_id;
			if($id!=0)$condp.=' AND prodetallepedidos.tesproductos_id='.$id;
		}
		if($calidad!=''){
		$condp='prodetallepedidos.sc_calidades_id="'.$calidad.'"';
			if($color_id!=0)$condp.=' AND prodetallepedidos.tescolores_id='.$color_id;
			if($id!=0)$condp.=' AND prodetallepedidos.tesproductos_id='.$id;
		}
		/*peidos sin rollos*/
	$pi=$pedidos->find_by_sql(
	'SELECT SUM(prodetallepedidos.cantidad) as t FROM pronotapedidos INNER JOIN prodetallepedidos ON pronotapedidos.id=prodetallepedidos.pronotapedidos_id AND pronotapedidos.aclempresas_id='.Session::get("EMPRESAS_ID").' WHERE (pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado") AND pronotapedidos.aclempresas_id='.Session::get('EMPRESAS_ID').' AND (ISNULL(prodetallepedidos.prorollos_id) OR prodetallepedidos.prorollos_id="0") AND pronotapedidos.fecha<="'.$fecha.'" AND pronotapedidos.tipo="ingreso" AND '.$condp.' GROUP BY prodetallepedidos.tesproductos_id');
	/*PEDIDO SIN CORTE*/
	$pisc=$pedidos->find_by_sql(
	'SELECT SUM(prodetallepedidos.cantidad) as ts FROM prorollos as r, pronotapedidos INNER JOIN prodetallepedidos ON pronotapedidos.id=prodetallepedidos.pronotapedidos_id WHERE (pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado") AND pronotapedidos.fecha<="'.$fecha.'" AND !ISNULL(prodetallepedidos.prorollos_id) AND pronotapedidos.aclempresas_id='.Session::get('EMPRESAS_ID').' AND r.id=prodetallepedidos.prorollos_id AND ISNULL(r.prorollos_id) AND pronotapedidos.tipo="ingreso" AND '.$condp.' GROUP BY prodetallepedidos.tesproductos_id');
	/*PEDIDO CON CORTE*/
	$picc=$pedidos->find_by_sql('SELECT SUM(prodetallepedidos.cantidad) as tc FROM prorollos as r, pronotapedidos INNER JOIN prodetallepedidos ON pronotapedidos.id=prodetallepedidos.pronotapedidos_id WHERE (pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado") AND pronotapedidos.fecha<="'.$fecha.'" AND !ISNULL(prodetallepedidos.prorollos_id) AND pronotapedidos.aclempresas_id='.Session::get('EMPRESAS_ID').' AND r.id=prodetallepedidos.prorollos_id AND !ISNULL(r.prorollos_id) AND pronotapedidos.tipo="ingreso" AND '.$condp.' GROUP BY prodetallepedidos.tesproductos_id');
	
	(float)$t=$pi->t+(float)$pisc->ts+(float)$picc->tc;
	}
	//echo ''.$t.' Ingresos <br />';
	return $total=number_format((float)$t,2,'.','');
}

public function getTotalsalidas($id=0,$color_id=0,$lote='',$fecha='',$calidad='')
{
	$a=$this->getSalidas_g($id,$color_id,$lote,$fecha,$calidad);
	$b=$this->getSalidas_p($id,$color_id,$lote,$fecha,$calidad);
	$c=$this->getAutosalidas($id,$color_id,$lote,$fecha,$calidad);
	$d=$this->getSalidas_internas($id,$color_id,$lote,$fecha,$calidad);
	$t=$a+$b+$c+$d;
	/*if($id==69){
	echo " G: ".$a;
	echo " P: ".$b;
	echo " A: ".$c;
	echo " I: ".$d;
	echo "<br />";
	}*/
	return $total=number_format((float)$t,2,'.','');
}

public function getSalidas_g($id=0,$color_id=0,$lote='',$fecha='',$calidad='')
{
	$t=0;
	if($id!=0)
	{	
	$salidas=Load::model('tesdetallesalidas');
	if($color_id==0){$cond='tesdetallesalidas.tesproductos_id='.$id;}
	if($color_id!=0)
	{
		$cond='tesdetallesalidas.tescolores_id='.$color_id;
		if($id!=0)$cond.=' AND tesdetallesalidas.tesproductos_id='.$id;
	}
	if($lote!='')
	{
		$cond='tesdetallesalidas.lote="'.$lote.'"';
		if($id!=0)$cond.=' AND tesdetallesalidas.tesproductos_id='.$id;
		if($color_id!=0)$cond.=' AND tesdetallesalidas.tescolores_id='.$color_id;
	}
	if($calidad!='')
	{
		$cond='tesdetallesalidas.sc_calidades_id="'.$calidad.'"';
		if($id!=0)$cond.=' AND tesdetallesalidas.tesproductos_id='.$id;
		if($color_id!=0)$cond.=' AND tesdetallesalidas.tescolores_id='.$color_id;
		
	}
	$join_S='INNER JOIN tessalidas as s ON s.id=tesdetallesalidas.tessalidas_id AND (s.estadosalida="Pendiente" or s.estadosalida="TERMINADO" or s.estadosalida="PAGADO" or s.estadosalida="Pagado" or s.estadosalida="Enviado") AND s.tesdocumentos_id=15 AND s.aclempresas_id='.Session::get('EMPRESAS_ID');
	/*salidas de todo menos de telas*/
	$di=$salidas->sum('tesdetallesalidas.cantidad','conditions: ISNULL(tesdetallesalidas.prorollos_id) AND tesdetallesalidas.aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond,'join: '.$join_S.' AND s.femision<="'.$fecha.'"');
	/*PARA LAS TELAS SIN CORTE  AND r.estadorollo!="TERMINADO"*/
	$tsc=$salidas->find_by_sql('SELECT SUM( tesdetallesalidas.cantidad ) AS tsc FROM prorollos AS r, tesdetallesalidas INNER JOIN tessalidas AS s ON s.id = tesdetallesalidas.tessalidas_id AND s.estadosalida!="Editable" AND s.estadosalida!="Con factura" AND s.tesdocumentos_id=15 AND s.aclempresas_id='.Session::get('EMPRESAS_ID').' AND s.femision<="'.$fecha.'" WHERE !ISNULL(tesdetallesalidas.prorollos_id) AND r.id = tesdetallesalidas.prorollos_id AND ISNULL(r.prorollos_id) AND '.$cond);
	//echo "telasincorte ".$tsc->tsc.'<br />';
	/* PARA LAS TELAS CON CORTE*/
	$tcc=$salidas->find_by_sql('SELECT SUM(tesdetallesalidas.cantidad) AS tcc FROM prorollos AS r, tesdetallesalidas INNER JOIN tessalidas AS s ON s.id = tesdetallesalidas.tessalidas_id AND s.estadosalida!="Editable" AND s.estadosalida!="Con factura"  AND s.tesdocumentos_id=15 AND s.aclempresas_id='.Session::get('EMPRESAS_ID').' AND s.femision<="'.$fecha.'" WHERE !ISNULL(tesdetallesalidas.prorollos_id) AND r.id = tesdetallesalidas.prorollos_id AND !ISNULL(r.prorollos_id) AND '.$cond);
	/*FIN PARA TELAS*/
	$t=(float)$di+(float)$tsc->tsc+(float)$tcc->tcc;
	}
return $total=number_format((float)$t,2,'.','');
}
public function getSalidas_p($id=0,$color_id=0,$lote='',$fecha='',$calidad='')
{
	$t=0;
	if($id!=0)
	{
		$pedidos=Load::model('pronotapedidos');
	if($color_id==0){$condp='prodetallepedidos.tesproductos_id='.$id;}
	if($color_id!=0){
		$condp='prodetallepedidos.tescolores_id='.$color_id;
		if($id!=0)$condp.=' AND prodetallepedidos.tesproductos_id='.$id;
		}
	if($lote!='')
	{
		$condp='prodetallepedidos.lote="'.$lote.'"';
		if($id!=0)$condp.=' AND prodetallepedidos.tesproductos_id='.$id;
		if($color_id!=0)$condp.=' AND prodetallepedidos.tescolores_id='.$color_id;
	}
	if($calidad!='')
	{
		$condp='prodetallepedidos.sc_calidades_id="'.$calidad.'"';
		if($id!=0)$condp.=' AND prodetallepedidos.tesproductos_id='.$id;
		if($color_id!=0)$condp.=' AND prodetallepedidos.tescolores_id='.$color_id;
	}
	$pi=$pedidos->find_by_sql('SELECT ifnull(sum(c.peso),0) as total FROM pronotapedidos , procajastrama as c INNER JOIN prodetallepedidos ON prodetallepedidos.id=c.prodetallepedidos_id
WHERE pronotapedidos.id=prodetallepedidos.pronotapedidos_id AND (pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado" or pronotapedidos.estadonota="Pendiente")AND pronotapedidos.aclempresas_id='.Session::get('EMPRESAS_ID').' AND pronotapedidos.fecha<="'.$fecha.'"  AND prodetallepedidos.tescajas_id>="1" AND ISNULL(prodetallepedidos.prorollos_id) AND pronotapedidos.tipo="salida" AND '.$condp.' GROUP BY prodetallepedidos.tesproductos_id');
	
	$t=(float)$pi->total;
	
	}
	return $total=number_format((float)$t,2,'.','');
}
public function getAutosalidas($id=0,$color_id=0,$lote='',$fecha='',$calidad='')
{
	$t=0;
	if($id!=0)
	{
		$autosalida=Load::model('tesdetalleingresos');
	if($color_id==0){$cond_auto='tesdetalleingresos.tesproductos_id='.$id;}
	if($color_id!=0){
		$cond_auto='tesdetalleingresos.tescolores_id='.$color_id;
		if($id!=0)$cond_auto.=' AND tesdetalleingresos.tesproductos_id='.$id;/* Esto para las Auto Salidas solo el stock;*/
	}
	if($lote!='')
	{
		$cond_auto='tesdetalleingresos.lote="'.$lote.'"';
			if($id!=0)$cond_auto.=' AND tesdetalleingresos.tesproductos_id='.$id;
			if($color_id!=0)$cond_auto.=' AND tesdetalleingresos.tescolores_id='.$color_id;
	}
	if($calidad!='')
	{
		/* Esto para las Auto Salidas solo el stock;*/
		$cond_auto='tesdetalleingresos.sc_calidades_id="'.$calidad.'"';
		if($id!=0)$cond_auto.=' AND tesdetalleingresos.tesproductos_id='.$id;
		if($color_id!=0)$cond_auto.=' AND tesdetalleingresos.tescolores_id='.$color_id;
		
	}
	$join = 'INNER JOIN tesingresos as i ON i.id = tesdetalleingresos.tesingresos_id AND  i.autosalida="1" ';
	$di_auto=$autosalida->sum('tesdetalleingresos.cantidad','conditions: tesdetalleingresos.aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond_auto,'join: '.$join);
	$t=(float)$di_auto;
	
	}
	/*echo 'SALIDAS  ->'.$di.'<br />';
	echo 'SALIDAS INTERNAS ->'.$sdi.'<br />';
	echo 'cajasTrama='.$pi->total;*/

	return $total=number_format((float)$t,2,'.','');
}
public function getSalidas_internas($id=0,$color_id=0,$lote='',$fecha='',$calidad='')
{
	$t=0;
	if($id!=0)
	{
	$salidasinternas=Load::model('tesdetallesalidasinternas');
	if($color_id==0){$cond_i='tesdetallesalidasinternas.tesproductos_id='.$id;}
	if($color_id!=0){
		$cond_i='tesdetallesalidasinternas.tescolores_id='.$color_id;
			if($id!=0)$cond_i.=' AND tesdetallesalidasinternas.tesproductos_id='.$id;
		}
	if($lote!='')
	{
		$cond_i='tesdetallesalidasinternas.lote="'.$lote.'"';
			if($id!=0)$cond_i.=' AND tesdetallesalidasinternas.tesproductos_id='.$id;
			if($color_id!=0)$cond_i.=' AND tesdetallesalidasinternas.tescolores_id='.$color_id;
	}
	if($calidad!='')
	{
		$cond_i='tesdetallesalidasinternas.sc_calidades_id="'.$calidad.'"';
			if($id!=0)$cond_i.=' AND tesdetallesalidasinternas.tesproductos_id='.$id;
			if($color_id!=0)$cond_i.=' AND tesdetallesalidasinternas.tescolores_id='.$color_id;
	}
	$join_S_I='INNER JOIN tessalidasinternas as s_i ON s_i.id=tesdetallesalidasinternas.tessalidasinternas_id AND (s_i.estadosalida="TERMINADO" OR s_i.estadosalida="Pendiente" or s_i.estadosalida="Enviado" or s_i.estadosalida="PAGADO") AND s_i.aclempresas_id='.Session::get('EMPRESAS_ID');
	$sdi=$salidasinternas->sum('tesdetallesalidasinternas.cantidad','conditions: ISNULL(tesdetallesalidasinternas.prorollos_id) AND '.$cond_i,'join: '.$join_S_I.' AND s_i.femision<="'.$fecha.'"');
	
	$t=(float)$sdi;
	
	}
	return $total=number_format((float)$t,2,'.','');
}

public function getSaldo_entrefechas($producto,$color=0,$lote=0,$fechainicio,$fechafin)
{
	if($lote!=0)
	{
		$ingresos=Load::model('tesdetalleingresos')->find_first('columns: preciounitario','conditions: tesproductos_id='.$producto.' AND tescolores_id='.$color.' AND lote='.$lote);
		
		$a=$this->getTotalingresos_entrefechas($id,$color_id,$lote,$fechainicio,$fechafin);
		$b=$this->getTotalsalidas_entrefechas($id,$color_id,$lote,$fechainicio,$fechafin);
		(float)$c=(float)$a-(float)$b;
		return $c.'-'.$ingresos->preciounitario;
	}else
	{
		$a=$this->getTotalingresos_entrefechas($id,$color_id,0,0,$fechafin);
		$b=$this->getTotalsalidas_entrefechas($id,$color_id,0,0,$fechafin);
		(float)$c=(float)$a-(float)$b;
		return $c;
	}
}	
public function getTotalingresos_entrefechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin)
{
	$a=$this->getFacturas_ingresos_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin);
	$b=$this->getGuias_ingresos_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin);
	$c=$this->getPedidos_ingresos_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin);
	$t=$a+$b+$c;
	//echo "i=>".$b.' ';
	return $total=(float)$t;
}

public function getFacturas_ingresos_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin)
{
	$t=0;
	if($id!=0)
	{
	$ingresos=Load::model('tesdetalleingresos');
	if($color_id==0){
		$cond='tesdetalleingresos.tesproductos_id='.$id;
		}
	if($color_id!=0){
		$cond='tesdetalleingresos.tescolores_id='.$color_id;
			if($id!=0)$cond.=' AND tesdetalleingresos.tesproductos_id='.$id;
		}
	if($lote!=''){
		$cond='tesdetalleingresos.lote="'.$lote.'"';
			if($color_id!=0)$cond.=' AND tesdetalleingresos.tescolores_id='.$color_id;
			if($id!=0)$cond.=' AND tesdetalleingresos.tesproductos_id='.$id;
		}
		
		/*echo 'aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond;*/
    $join = 'INNER JOIN tesingresos as i ON i.id = tesdetalleingresos.tesingresos_id AND (i.estadoingreso="PAGADO" OR i.estadoingreso="Pendiente" OR i.estadoingreso="INGRESO-CH") AND i.aclempresas_id='.Session::get("EMPRESAS_ID");
	$di_f=0;
	$di_f=$ingresos->sum('tesdetalleingresos.cantidad','conditions: !ISNULL(tesdetalleingresos.cantidad) 
AND tesdetalleingresos.cantidad >0 AND (ISNULL(tesdetalleingresos.prorollos_id) OR tesdetalleingresos.prorollos_id=0) AND  i.femision BETWEEN "'.$fecha_inicio.'" AND "'.$fecha_fin.'"  AND tesdetalleingresos.aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond,'join: '.$join.' AND tesdocumentos_id=7 AND isnull(numeroguia)');

	(float)$t=(float)$di_f;
	}
	//echo ''.$t.' Ingresos <br />';
	return $total=number_format((float)$t,2,'.','');
}
public function getGuias_ingresos_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin)
{
	$t=0;
	if($id!=0)
	{
	$ingresos=Load::model('tesdetalleingresos');
	if($color_id==0){
		$cond='tesdetalleingresos.tesproductos_id='.$id;
		}
	if($color_id!=0){
		$cond='tesdetalleingresos.tescolores_id='.$color_id;
			if($id!=0)$cond.=' AND tesdetalleingresos.tesproductos_id='.$id;
		}
	if($lote!=''){
		$cond='tesdetalleingresos.lote="'.$lote.'"';
			if($color_id!=0)$cond.=' AND tesdetalleingresos.tescolores_id='.$color_id;
			if($id!=0)$cond.=' AND tesdetalleingresos.tesproductos_id='.$id;
		}
		/*echo 'aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond;*/
    $join = 'INNER JOIN tesingresos as i ON i.id = tesdetalleingresos.tesingresos_id AND (i.estadoingreso="PAGADO" OR i.estadoingreso="Pendiente" OR i.estadoingreso="INGRESO-CH") AND tesdocumentos_id=15 AND ISNULL(i.produccion)';
	$di=$ingresos->sum('tesdetalleingresos.cantidad','conditions: ISNULL(tesdetalleingresos.prorollos_id) AND  i.femision BETWEEN "'.$fecha_inicio.'" AND "'.$fecha_fin.'" AND  !ISNULL(tesdetalleingresos.cantidad) 
AND tesdetalleingresos.cantidad >0 AND tesdetalleingresos.aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond,'join: '.$join);
	/*PARA la suma de los stcok de los rollos y las telas*/
	$di_rollos=$ingresos->sum('tesdetalleingresos.cantidad','conditions: !ISNULL(tesdetalleingresos.prorollos_id) AND  i.femision BETWEEN "'.$fecha_inicio.'" AND "'.$fecha_fin.'" AND  !ISNULL(tesdetalleingresos.cantidad) 
AND tesdetalleingresos.cantidad >0 AND tesdetalleingresos.aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond,'join: '.$join);
	
	$t=(float)$di+(float)$di_rollos;
	}
	/*echo 'Ingresos->'.$t.'<br />';*/
	return $total=(float)$t;
}
public function getPedidos_ingresos_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin)
{
	$t=0;
	
	if($id!=0)
	{
	$pedidos=Load::model('pronotapedidos');
	if($color_id==0){
		$condp='prodetallepedidos.tesproductos_id='.$id;
		}
	if($color_id!=0){
		$condp='prodetallepedidos.tescolores_id='.$color_id;
			if($id!=0)$condp.=' AND prodetallepedidos.tesproductos_id='.$id;
		}
	
	$pi=$pedidos->find_by_sql('SELECT SUM(prodetallepedidos.cantidad) as t FROM pronotapedidos INNER JOIN prodetallepedidos ON pronotapedidos.id=prodetallepedidos.pronotapedidos_id WHERE ISNULL(prodetallepedidos.prorollos_id) AND (pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado") AND pronotapedidos.fecha_at BETWEEN "'.$fecha_inicio.'" AND "'.$fecha_fin.'" AND pronotapedidos.aclempresas_id='.Session::get('EMPRESAS_ID').' AND pronotapedidos.tipo="ingreso" AND '.$condp.' GROUP BY prodetallepedidos.tesproductos_id');
	$pi_rollos=$pedidos->find_by_sql('SELECT SUM(prodetallepedidos.cantidad) as total FROM pronotapedidos INNER JOIN prodetallepedidos ON pronotapedidos.id=prodetallepedidos.pronotapedidos_id WHERE !ISNULL(prodetallepedidos.prorollos_id) AND (pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado") AND pronotapedidos.fecha_at BETWEEN "'.$fecha_inicio.'" AND "'.$fecha_fin.'" AND pronotapedidos.aclempresas_id='.Session::get('EMPRESAS_ID').' AND pronotapedidos.tipo="ingreso" AND '.$condp.' GROUP BY prodetallepedidos.tesproductos_id');
	
	/*echo $di."<br />".$di_rollos;
	echo $pi->total.' pe<br />'.$pi_rollos->total;*/
	
	(float)$t=(float)$pi->t+(float)$pi_rollos->total;
	}
	/*echo 'Ingresos->'.$t.'<br />';*/
	return $total=(float)$t;
}

/* tesproductos_id=64 AND tescolores_id=1 AND lote=201211272020*/
public function getTotalsalidas_entrefechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin)
{
	$a=$this->getGuias_salidas_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin);
	$b=$this->getInternas_salidas_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin);
	$c=$this->getPedidos_salidas_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin);
	$d=$this->getAutosalidas_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin);
	$t=$a+$b+$c+$d;
	return $total=(float)$t;
}
public function getGuias_salidas_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin)
{
	$t=0;
	if($id!=0)
	{	
	$salidas=Load::model('tesdetallesalidas');
	if($color_id==0){$cond='tesdetallesalidas.tesproductos_id='.$id;}
	if($color_id!=0)
	{
		$cond='tesdetallesalidas.tescolores_id='.$color_id;
		if($id!=0)$cond.=' AND tesdetallesalidas.tesproductos_id='.$id;
	}
	if($lote!='')
	{
		$cond='tesdetallesalidas.lote="'.$lote.'"';
		if($id!=0)$cond.=' AND tesdetallesalidas.tesproductos_id='.$id;
		if($color_id!=0)$cond.=' AND tesdetallesalidas.tescolores_id='.$color_id;
	}
	
	$join_S='INNER JOIN tessalidas as s ON s.id=tesdetallesalidas.tessalidas_id AND   s.estadosalida!="Editable" AND s.estadosalida!="Con factura" AND s.femision BETWEEN "'.$fecha_inicio.'" AND "'.$fecha_fin.'"';
	$di=$salidas->sum('tesdetallesalidas.cantidad','conditions: ISNULL(tesdetallesalidas.prorollos_id) AND  tesdetallesalidas.aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond,'join: '.$join_S);
	/*salida de telas*/
	$di_rollos=$salidas->sum('tesdetallesalidas.cantidad','conditions: !ISNULL(tesdetallesalidas.prorollos_id) AND  tesdetallesalidas.aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond,'join: '.$join_S);
	$t=(float)$di+(float)$di_rollos;
	}
	//echo "Salidas->".$t.'<br />';
	return $total=(float)$t;
}
public function getInternas_salidas_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin)
{
	$t=0;
	if($id!=0)
	{
	$salidasinternas=Load::model('tesdetallesalidasinternas');
	if($color_id==0){$cond_i='tesdetallesalidasinternas.tesproductos_id='.$id;}
	if($color_id!=0){
		$cond_i='tesdetallesalidasinternas.tescolores_id='.$color_id;
			if($id!=0)$cond_i.=' AND tesdetallesalidasinternas.tesproductos_id='.$id;
		}
	if($lote!='')
	{
		$cond_i='tesdetallesalidasinternas.lote="'.$lote.'"';
			if($id!=0)$cond_i.=' AND tesdetallesalidasinternas.tesproductos_id='.$id;
			if($color_id!=0)$cond_i.=' AND tesdetallesalidasinternas.tescolores_id='.$color_id;
	}
	
	$join_S_I='INNER JOIN tessalidasinternas as s_i ON s_i.id=tesdetallesalidasinternas.tessalidasinternas_id  AND   s_i.estadosalida!="Editable" AND s_i.estadosalida!="Con factura"  AND s_i.femision BETWEEN "'.$fecha_inicio.'" AND "'.$fecha_fin.'"';
	$sdi=$salidasinternas->sum('tesdetallesalidasinternas.cantidad','conditions: ISNULL(tesdetallesalidasinternas.prorollos_id) AND '.$cond_i,'join: '.$join_S_I);
	$t=(float)$sdi;
	}
	//echo "Salidas->".$t.'<br />';
	return $total=(float)$t;
}

public function getPedidos_salidas_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin)
{
	$t=0;
	if($id!=0)
	{
	$pedidos=Load::model('pronotapedidos');
	if($color_id==0){$condp='prodetallepedidos.tesproductos_id='.$id;}
	if($color_id!=0){
		$condp='prodetallepedidos.tescolores_id='.$color_id;
		if($id!=0)$condp.=' AND prodetallepedidos.tesproductos_id='.$id;
		}
	if($lote!='')
	{
		$condp='prodetallepedidos.lote="'.$lote.'"';
		if($id!=0)$condp.=' AND prodetallepedidos.tesproductos_id='.$id;
		if($color_id!=0)$condp.=' AND prodetallepedidos.tescolores_id='.$color_id;
	}
	
	$pi=$pedidos->find_by_sql('SELECT SUM(prodetallepedidos.cantidad) as t FROM pronotapedidos INNER JOIN prodetallepedidos ON pronotapedidos.id=prodetallepedidos.pronotapedidos_id WHERE ISNULL(prodetallepedidos.prorollos_id) AND (pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado" or pronotapedidos.estadonota="Pendiente") AND pronotapedidos.fecha_at BETWEEN "'.$fecha_inicio.'" AND "'.$fecha_fin.'" AND pronotapedidos.aclempresas_id='.Session::get('EMPRESAS_ID').' AND prodetallepedidos.tescajas_id="1" AND pronotapedidos.tipo="salida" AND '.$condp.' GROUP BY prodetallepedidos.tesproductos_id');

	$pi_rollos=$pedidos->find_by_sql('SELECT SUM(prodetallepedidos.cantidad) as total FROM pronotapedidos INNER JOIN prodetallepedidos ON pronotapedidos.id=prodetallepedidos.pronotapedidos_id WHERE !ISNULL(prodetallepedidos.prorollos_id) AND (pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado" or pronotapedidos.estadonota="Pendiente") AND pronotapedidos.fecha_at BETWEEN "'.$fecha_inicio.'" AND "'.$fecha_fin.'" AND pronotapedidos.aclempresas_id='.Session::get('EMPRESAS_ID').' AND prodetallepedidos.tescajas_id="1" AND pronotapedidos.tipo="salida" AND '.$condp.' GROUP BY prodetallepedidos.tesproductos_id');
	
	$t=$pi->t+(float)$pi_rollos->total;
	}
	//echo "Salidas->".$t.'<br />';
	return $total=(float)$t;
}

public function getAutosalidas_fechas($id,$color_id,$lote,$fecha_inicio,$fecha_fin)
{
	$t=0;
	if($id!=0)
	{
	$autosalida=Load::model('tesdetalleingresos');
	if($color_id==0){$cond_auto='tesdetalleingresos.tesproductos_id='.$id;}
	if($color_id!=0){
		$cond_auto='tesdetalleingresos.tescolores_id='.$color_id;
		if($id!=0)$cond_auto.=' AND tesdetalleingresos.tesproductos_id='.$id;
	}
	if($lote!='')
	{
		$cond_auto='tesdetalleingresos.lote="'.$lote.'"';
			if($id!=0)$cond_auto.=' AND tesdetalleingresos.tesproductos_id='.$id;
			if($color_id!=0)$cond_auto.=' AND tesdetalleingresos.tescolores_id='.$color_id;
	}	
	
	$campos = 'tesdetalleingresos.' . join(',tesdetalleingresos.', $autosalida->fields);
    $join = 'INNER JOIN tesingresos as i ON i.id = tesdetalleingresos.tesingresos_id AND  i.autosalida="1" AND i.femision BETWEEN "'.$fecha_inicio.'" AND "'.$fecha_fin.'"';
	$di_auto=$autosalida->sum('tesdetalleingresos.cantidad','conditions: tesdetalleingresos.aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond_auto,'join: '.$join);
	$t=(float)$di_auto;
	}
	return $total=(float)$t;
}

public function getUrdido($id,$color_id,$lote)
{
	//echo $id;
	$T=$this->find_by_sql("SELECT IFNULL(sum(cantidad),0) as t FROM `prodetallemovimientos` WHERE tesproductos_id=".$id." AND tescolores_id=".$color_id." AND lote='".$lote."';");

	return $T->t;
}

/*REORTE DE PRECIOS DE UN PRODUCTO YA SEA HILO O REPUESTO*/
public function getPrecios($id_producto=58,$id_color=0)
{
	$color='';
	if($id_color!=0)$color=' AND c.id='.$id_color;
	$sql="SELECT i.tesmonedas_id as tesmonedas_id,p.id as id,p.razonsocial as razonsocial,t.nombrecorto as nombre,d.preciounitario as precio, i.femision as fecha, c.nombre as color,c.id as color_id FROM tesproductos as t, tescolores as c,tesdatos as p, tesingresos as i INNER JOIN tesdetalleingresos as d ON d.tesproductos_id=".$id_producto." AND i.id=d.tesingresos_id 
WHERE d.tesproductos_id = t.id AND p.id=i.tesdatos_id AND (i.tesdocumentos_id=7) AND c.id=d.tescolores_id".$color." ORDER BY p.id,i.femision ASC";
	//echo $sql;
	return $this->find_all_by_sql($sql);
}

/*MODELOS PARA SANTA CARMELA */
public function telas()
{
	$sql='SELECT d.* FROM tesdetalleingresos as d, tesproductos as p, testipoproductos as t, teslineaproductos as l, tesingresos as i WHERE i.tesdocumentos_id!=7 AND d.tesingresos_id=i.id AND ISNULL(i.autosalida) AND l.id=14 AND l.id=t.teslineaproductos_id AND t.id=p.testipoproductos_id AND p.id=d.tesproductos_id AND i.aclempresas_id='.Session::get("EMPRESAS_ID").' AND d.aclempresas_id='.Session::get("EMPRESAS_ID").' AND ISNULL((select ro.tesdetalleingresos_id from sc_crollos  as ro WHERE ro.tesdetalleingresos_id=d.id AND ISNULL(ro.sc_crollos_id))) GROUP BY d.id';
	//echo $sql;
	return $this->find_all_by_sql($sql);
}

/*saldos de hilo en produccion de urdidos*/
public function getSaldos_urdidos()
{/* AND d.lote=sc_totalcajas.lotes GROUP BY sc_totalcajas.lotes*/
	$sql="SELECT 
(select id from sc_totalcajas where sc_totalcajas.tesingresos_id=d.tesingresos_id and sc_totalcajas.tesproductos_id=d.tesproductos_id and sc_totalcajas.estado=1) as total_id,
d.*, d.cantidad as piso,
(select IFNULL(sum(tesdetalleingresos.cantidad),0)from sc_totalcajas,tesdetalleingresos where sc_totalcajas.tesingresos_id=tesdetalleingresos.tesingresos_id AND tesdetalleingresos.tesproductos_id=sc_totalcajas.tesproductos_id AND tesdetalleingresos.tescolores_id=d.tescolores_id AND sc_totalcajas.tesproductos_id=d.tesproductos_id and sc_totalcajas.tesingresos_id=d.tesingresos_id AND d.lote=sc_totalcajas.lotes GROUP BY d.lote) as coneras,
(d.cantidad-(select IFNULL(sum(tesdetalleingresos.cantidad),0)from sc_totalcajas,tesdetalleingresos where sc_totalcajas.tesingresos_id=tesdetalleingresos.tesingresos_id AND tesdetalleingresos.tesproductos_id=sc_totalcajas.tesproductos_id AND tesdetalleingresos.tescolores_id=d.tescolores_id AND sc_totalcajas.tesproductos_id=d.tesproductos_id and sc_totalcajas.tesingresos_id=d.tesingresos_id)) as saldo_piso, 
(select IFNULL(sum(totalkilos)/IF((LENGTH(colores_id )-LENGTH(REPLACE(colores_id,',','' )))=0,1,(LENGTH(colores_id )-LENGTH(REPLACE(colores_id,',','' )))+1),0) from sc_urdidos,sc_totalcajas where sc_urdidos.sc_totalcajas_id=sc_totalcajas.id AND sc_urdidos.tesproductos_id=d.tesproductos_id AND locate(d.tescolores_id,sc_urdidos.colores_id) AND sc_totalcajas.tesingresos_id=d.tesingresos_id) as urdido, 
((select IFNULL(sum(tesdetalleingresos.cantidad),0)from sc_totalcajas,tesdetalleingresos where sc_totalcajas.tesingresos_id=tesdetalleingresos.tesingresos_id AND tesdetalleingresos.tesproductos_id=sc_totalcajas.tesproductos_id AND tesdetalleingresos.tescolores_id=d.tescolores_id AND sc_totalcajas.tesproductos_id=d.tesproductos_id and sc_totalcajas.tesingresos_id=d.tesingresos_id) - ((select IFNULL(sum(totalkilos)/IF((LENGTH(colores_id )-LENGTH(REPLACE(colores_id,',','' )))=0,1,(LENGTH(colores_id )-LENGTH(REPLACE(colores_id,',','' )))+1),0) from sc_urdidos,sc_totalcajas where sc_urdidos.sc_totalcajas_id=sc_totalcajas.id AND sc_urdidos.tesproductos_id=d.tesproductos_id AND locate(d.tescolores_id,sc_urdidos.colores_id) AND sc_totalcajas.tesingresos_id=d.tesingresos_id) + (SELECT IFNULL(SUM(sd.cantidad),0) FROM tesdetallesalidas as sd, tessalidas as s WHERE s.id=sd.tessalidas_id AND sd.tesproductos_id=d.tesproductos_id AND sd.tescolores_id=d.tescolores_id AND sd.lote=d.lote ) ) ) as saldo_coneras 
FROM tesdetalleingresos as d, tesproductos as p, testipoproductos as t, tesingresos as i 
WHERE i.id=d.tesingresos_id AND d.tesproductos_id=p.id AND p.testipoproductos_id=t.id AND t.teslineaproductos_id=24 
AND 
((d.cantidad-(select IFNULL(sum(tesdetalleingresos.cantidad),0)from sc_totalcajas,tesdetalleingresos where sc_totalcajas.tesingresos_id=tesdetalleingresos.tesingresos_id AND tesdetalleingresos.tesproductos_id=sc_totalcajas.tesproductos_id AND tesdetalleingresos.tescolores_id=d.tescolores_id AND sc_totalcajas.tesproductos_id=d.tesproductos_id and sc_totalcajas.tesingresos_id=d.tesingresos_id))>0 
OR ((select IFNULL(sum(tesdetalleingresos.cantidad),0)from sc_totalcajas,tesdetalleingresos where sc_totalcajas.tesingresos_id=tesdetalleingresos.tesingresos_id AND tesdetalleingresos.tesproductos_id=sc_totalcajas.tesproductos_id AND tesdetalleingresos.tescolores_id=d.tescolores_id AND sc_totalcajas.tesproductos_id=d.tesproductos_id and sc_totalcajas.tesingresos_id=d.tesingresos_id) - ((select IFNULL(sum(totalkilos)/IF((LENGTH(colores_id )-LENGTH(REPLACE(colores_id,',','' )))=0,1,(LENGTH(colores_id )-LENGTH(REPLACE(colores_id,',','' )))+1),0) from sc_urdidos,sc_totalcajas where sc_urdidos.sc_totalcajas_id=sc_totalcajas.id AND sc_urdidos.tesproductos_id=d.tesproductos_id AND locate(d.tescolores_id,sc_urdidos.colores_id) AND sc_totalcajas.tesingresos_id=d.tesingresos_id) + (SELECT IFNULL(SUM(sd.cantidad),0) FROM tesdetallesalidas as sd, tessalidas as s WHERE s.id=sd.tessalidas_id AND sd.tesproductos_id=d.tesproductos_id AND sd.tescolores_id=d.tescolores_id AND sd.lote=d.lote ) ) )>0)



";
	//echo $sql;
	return $this->find_all_by_sql($sql);

}
	
/*PARA SALOD SDE CONERAS PARA DEVOLUCION*/
public function getSaldoConeras()
	{
		return $this->find_all_by_sql("SELECT d.*,
(
(select IFNULL(sum(tesdetalleingresos.cantidad),0)from sc_totalcajas,tesdetalleingresos where sc_totalcajas.tesingresos_id=tesdetalleingresos.tesingresos_id AND tesdetalleingresos.tesproductos_id=sc_totalcajas.tesproductos_id AND tesdetalleingresos.tescolores_id=d.tescolores_id AND sc_totalcajas.tesproductos_id=d.tesproductos_id and sc_totalcajas.tesingresos_id=d.tesingresos_id)
- 
((select IFNULL(sum(totalkilos)/IF((LENGTH(colores_id )-LENGTH(REPLACE(colores_id,',','' )))=0,1,(LENGTH(colores_id )-LENGTH(REPLACE(colores_id,',','' )))+1),0) from sc_urdidos,sc_totalcajas where sc_urdidos.sc_totalcajas_id=sc_totalcajas.id AND sc_urdidos.tesproductos_id=d.tesproductos_id AND locate(d.tescolores_id,sc_urdidos.colores_id) AND sc_totalcajas.tesingresos_id=d.tesingresos_id)
+
(SELECT IFNULL(SUM(sd.cantidad),0) FROM tesdetallesalidas as sd, tessalidas as s WHERE s.id=sd.tessalidas_id AND sd.tesproductos_id=d.tesproductos_id AND sd.tescolores_id=d.tescolores_id AND sd.lote=d.lote AND sd.aclempresas_id=".Session::get('EMPRESAS_ID')."
)
)
) as saldo_coneras

FROM tesdetalleingresos as d, tesproductos as p, testipoproductos as t, tesingresos as i WHERE i.id=d.tesingresos_id AND d.tesproductos_id=p.id AND p.testipoproductos_id=t.id AND t.teslineaproductos_id=24 AND ((

(select IFNULL(sum(tesdetalleingresos.cantidad),0)from sc_totalcajas,tesdetalleingresos where sc_totalcajas.tesingresos_id=tesdetalleingresos.tesingresos_id AND tesdetalleingresos.tesproductos_id=sc_totalcajas.tesproductos_id AND tesdetalleingresos.tescolores_id=d.tescolores_id AND sc_totalcajas.tesproductos_id=d.tesproductos_id and sc_totalcajas.tesingresos_id=d.tesingresos_id)
- 
(select IFNULL(sum(totalkilos)/IF((LENGTH(colores_id )-LENGTH(REPLACE(colores_id,',','' )))=0,1,(LENGTH(colores_id )-LENGTH(REPLACE(colores_id,',','' )))+1),0) from sc_urdidos,sc_totalcajas where sc_urdidos.sc_totalcajas_id=sc_totalcajas.id AND sc_urdidos.tesproductos_id=d.tesproductos_id AND locate(d.tescolores_id,sc_urdidos.colores_id) AND sc_totalcajas.tesingresos_id=d.tesingresos_id)
+
(SELECT IFNULL(SUM(sd.cantidad),0) FROM tesdetallesalidas as sd, tessalidas as s WHERE s.id=sd.tessalidas_id AND sd.tesproductos_id=d.tesproductos_id AND sd.tescolores_id=d.tescolores_id AND sd.lote=d.lote
)
)>0 
OR 
(d.cantidad-(select IFNULL(sum(tesdetalleingresos.cantidad),0)from sc_totalcajas,tesdetalleingresos where sc_totalcajas.tesingresos_id=tesdetalleingresos.tesingresos_id AND tesdetalleingresos.tesproductos_id=sc_totalcajas.tesproductos_id AND tesdetalleingresos.tescolores_id=d.tescolores_id AND sc_totalcajas.tesproductos_id=d.tesproductos_id and sc_totalcajas.tesingresos_id=d.tesingresos_id))>0)");
	}


}

 ?>