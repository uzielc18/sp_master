<?php
Load::models('testipocambios','tesdetallevauchers');
class Tesingresos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesdetalleingresos','tesabonosdetalles','tesdetallevauchers');
		$this->belongs_to('aclusuarios','tesmonedas','tesdatos','tesdocumentos','testipocambios');
		$this->has_one('tesdetracciones','tesletrasingresos','teschequesingresos');
		if(Session::get('DOC_CODIGO')=='01' or Session::get('DOC_CODIGO')=='08'){
			if(!Session::has('V_ID'))
			{
			$this->validates_presence_of('serie', 'message: Debe ingresar la <b>SERIE</b> en el documento');
			$this->validates_presence_of('numero', 'message: Debe ingresar el <b>NUMERO</b> en el documento');
			}
		}
    }
	
	public function generarNumero($documento)
	{
		$maximo = $this->maximum("numero", "conditions: tesdocumentos_id=".$documento." AND aclempresas_id =".Session::get('EMPRESAS_ID'));
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
	public function generarNumeroInterno($documento)
	{
		$maximo = $this->maximum("numero", "conditions: tesdocumentos_id=".$documento." AND tesdatos_id=2600 AND aclempresas_id =".Session::get('EMPRESAS_ID'));
		if(empty($maximo))
		{
			$maximo=1;
		}else{
			$maximo=$maximo+1;
		}
		switch($maximo)
		{
			case $maximo<10: $maximo="0000".$maximo; break;
			case $maximo<100: $maximo="000".$maximo; break;
			case $maximo<1000: $maximo="00".$maximo; break;
			case $maximo<10000: $maximo="0".$maximo; break;
			default: $maximo=$maximo;
		}
		return $maximo;
	}
	public function getInventario($tipo)
	{
		//echo 'SELECT ti.numero as numero, ti.id as id, sum(td.importe) as importe, (sum(td.importe)*0.18) as igv, (sum(td.importe)+(SUM( td.importe )*0.18)) as totalconigv, "inventario" as glosa  FROM tesingresos as ti, tesdetalleingresos as td, testipoproductos as tp, tesproductos as p WHERE ti.tesdocumentos_id=27 AND ti.id=td.tesingresos_id AND p.id=td.tesproductos_id AND p.testipoproductos_id=tp.id AND tp.id='.$tipo.'';
		return $this->find_by_sql('SELECT ti.numero as numero, ti.id as id, sum(td.importe) as importe, (sum(td.importe)*0.18) as igv, (sum(td.importe)+(SUM( td.importe )*0.18)) as totalconigv, "inventario" as glosa  FROM tesingresos as ti, tesdetalleingresos as td, testipoproductos as tp, tesproductos as p WHERE ti.aclempresas_id='.Session::get('EMPRESAS_ID').' AND ti.tesdocumentos_id=27 AND ti.id=td.tesingresos_id AND p.id=td.tesproductos_id AND p.testipoproductos_id=tp.id AND tp.id='.$tipo.'');
	}
	
	public function getFacturasDetraccion($conditions)
	{$det=Session::get('DETRACCION')*100;
	echo "SELECT tesingresos.*, IF( 
								  CASE 
								  WHEN tesmonedas_id=2 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=1
								  THEN totalconigv
								  WHEN tesmonedas_id=5 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=4 
								  THEN totalconigv  
								  ELSE '0.00' 
								  END 
								  >700,(CASE 
								  WHEN tesmonedas_id=2 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=1
								  THEN totalconigv
								  WHEN tesmonedas_id=5 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=4 
								  THEN totalconigv  
								  ELSE '0.00' 
								  END *".$det.")/100,0) AS detraccion
								  FROM `tesingresos` WHERE aclempresas_id=".Session::get('EMPRESAS_ID')." AND".$conditions;
		return $this->find_all_by_sql("SELECT tesingresos.*, IF( 
								  CASE 
								  WHEN tesmonedas_id=2 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=1
								  THEN totalconigv
								  WHEN tesmonedas_id=5 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=4 
								  THEN totalconigv  
								  ELSE '0.00' 
								  END 
								  >700,(CASE 
								  WHEN tesmonedas_id=2 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=1
								  THEN totalconigv
								  WHEN tesmonedas_id=5 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=4 
								  THEN totalconigv  
								  ELSE '0.00' 
								  END *".$det.")/100,0) AS detraccion
								  FROM `tesingresos` WHERE aclempresas_id=".Session::get('EMPRESAS_ID')." AND".$conditions);
	}

public function getDetraccion_id($conditions)
	{
		$det=Session::get('DETRACCION')*100;
		return $this->find_by_sql("SELECT tesingresos.*, IF( 
								  CASE 
								  WHEN tesmonedas_id=2 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=1
								  THEN totalconigv
								  WHEN tesmonedas_id=5 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=4 
								  THEN totalconigv  
								  ELSE '0.00' 
								  END 
								  >700,(CASE 
								  WHEN tesmonedas_id=2 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=1
								  THEN totalconigv
								  WHEN tesmonedas_id=5 
								  THEN (Select totalconigv*compra From testipocambios WHERE id=testipocambios_id) 
								  WHEN tesmonedas_id=4 
								  THEN totalconigv  
								  ELSE '0.00' 
								  END *".$det.")/100,0) AS detraccion
								  FROM `tesingresos` WHERE aclempresas_id=".Session::get('EMPRESAS_ID')." AND id=".$conditions);
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
		$v=$tipocambios->find_by_sql("SELECT max(id) as id FROM `testipocambios`");
		$id=$v->id;
	}
	return $id;
}	

public function getAdelantos()
{
	/*buscar todos los adelantos de un vauchers_id en base al ingreso especifico*/
	
	$Tesdetallevauchers= new Tesdetallevauchers();
	$adelantos=0;
	if($Tesdetallevauchers->exists('tesingresos_id='.$this->id)){
	$cargos=$Tesdetallevauchers->find('conditions: tesingresos_id='.$this->id.' AND cargos="1"');

	$adelantos=0;
	foreach($cargos as $c):
	$adelantos=$adelantos+$Tesdetallevauchers->sum('tesdetallevauchers.monto','conditions: tesvauchers_id='.$c->tesvauchers_id.' AND abono="1"','join: INNER JOIN tesvauchers ON tesvauchers.id=tesdetallevauchers.tesvauchers_id AND estadov!="ANULADO" AND tesvauchers.voucherformadepagos_id!=10 AND tesvauchers.aclempresas_id='.Session::get('EMPRESAS_ID'));
	endforeach;
	}
	if($Tesdetallevauchers->exists('tesingresos_ingreso='.$this->id)){
	$abonos=$Tesdetallevauchers->find('conditions: tesingresos_ingreso='.$this->id.' AND abono="1"');

	foreach($abonos as $a):
	$adelantos=$adelantos+$Tesdetallevauchers->sum('tesdetallevauchers.monto','conditions: tesingresos_ingreso='.$a->tesvauchers_id.' AND abono="1"','join: INNER JOIN tesvauchers ON tesvauchers.id=tesdetallevauchers.tesvauchers_id AND estadov!="ANULADO" AND tesvauchers.voucherformadepagos_id!=10 AND tesvauchers.aclempresas_id='.Session::get('EMPRESAS_ID'));
	endforeach;
	}
	
	return $adelantos;
}

/* Reprote POR fechas*/
public function reporteFechas($todo=0)
	{
		$fecha = date('Y-m').'-01';
		$nuevafecha = strtotime ( '+2 month' , strtotime ( $fecha ) ) ;
		$nuevafecha = date ( 'Y-m-d' , $nuevafecha );
		//$cond= "AND DATE_FORMAT(fvencimiento,'%Y')='".date("Y")."'";
		$cond= "AND fvencimiento<='".$nuevafecha."'";
		
		if($todo!=0)$cond="";
		$sql="SELECT tesingresos.*, WEEK(tesingresos.fvencimiento) as semana, DATEDIFF('".date("Y-m-d")."', tesingresos.fvencimiento) AS dd  FROM tesingresos WHERE tesingresos.aclempresas_id=".Session::get('EMPRESAS_ID')." AND (estadoingreso!='PAGADO' AND estadoingreso!='ANULADO' AND (select count(tesletrasingresos.id) from tesletrasingresos WHERE tesletrasingresos.tesingresos_id=tesingresos.id AND tesletrasingresos.estadodeletra='PAGADO')!=1) AND (tesingresos.tesdocumentos_id=34 or tesingresos.tesdocumentos_id=37  or tesingresos.tesdocumentos_id=38  or tesingresos.tesdocumentos_id=39  or tesingresos.tesdocumentos_id=40) ".$cond." ORDER BY tesingresos.fvencimiento, `semana` ASC ";
		echo $sql;
		return $this->find_all_by_sql($sql);
	}

public function getValidar($id)
{
	$sql="SELECT count(id) as f FROM `tesdetallevauchers` WHERE cargos=1 AND tesingresos_id=$id";
	$v=$this->find_by_sql($sql);
	//echo '**'.$v->f.'**';
	
	if($v->f==0){$val="";}else{$val=Html::linkAction("modificar_estado/$id","<i class='icon-exclamation-sign'></i>");}
	return $val;

}

public function Totalporsemana($n,$fecha)/*$n=Numero de semana,$fecha=numero de semana*/
{
		$anio=date("Y", strtotime($fecha));
		
		$sql="SELECT count(tesingresos.id) as total FROM tesingresos WHERE tesingresos.estadoingreso!='ANULADO' AND  tesingresos.aclempresas_id=".Session::get('EMPRESAS_ID')." AND estadoingreso!='PAGADO' AND (tesingresos.tesdocumentos_id=34 or tesingresos.tesdocumentos_id=37  or tesingresos.tesdocumentos_id=38  or tesingresos.tesdocumentos_id=39  or tesingresos.tesdocumentos_id=40) AND WEEK(tesingresos.fvencimiento)=".$n." AND YEAR(tesingresos.fvencimiento)='".$anio."' GROUP BY WEEK( tesingresos.fvencimiento )";
		//echo $sql;
		$M=$this->find_by_sql($sql);
		return $M->total;
	}

/*lista de guias de plegador sin imprimir etiquetas*/
public function getLista_guias($id=0,$fecha='')
{
	$cond=' AND i.id='.$id;
	if($id==0){$cond=' AND DATE_FORMAT(i.fecha_at,"%Y-%m")="'.$fecha.'"';}
	$sql='SELECT i.id as id, pp.numero as numero,
 (SELECT pl.numero FROM proplegadores as pl, tesdetalleingresos as di, tesingresos as ig WHERE di.tesingresos_id=ig.id AND pl.tesproductos_id=di.tesproductos_id AND pl.numero!=pp.numero AND ig.id=i.id ) as par,
da.razonsocial as urdidopor, i.aclusuarios_id as aclusuarios_id, DATE_FORMAT(i.fecha_at,"%d/%m/%Y") as fecha, CONCAT_WS("-",i.serie,i.numero) as guia, i.glosa as glosa
 FROM tesdetalleingresos as d, tesingresos as i,tesproductos as p, proplegadores as pp, tesdatos as da WHERE i.aclempresas_id='.Session::get('EMPRESAS_ID').' AND d.tesingresos_id=i.id'.$cond.' AND d.tesproductos_id=p.id AND i.tesdocumentos_id=15 AND i.pr="PL" AND i.estado=1 AND pp.tesproductos_id=p.id AND i.tesdatos_id=da.id
ORDER BY `d`.`id` ASC';
//echo $sql;
	return $this->find_all_by_sql($sql);
}

public function getComprasDatos($id=0)
{
	$cond='';
	if($id!=0)$cond=' AND p.id='.$id;
	return $this->find_all_by_sql("SELECT p.id as datos_id, p.razonsocial as razonsocial, p.ruc as ruc, i.* FROM  tesdatos as p, tesingresos as i WHERE i.aclempresas_id=".Session::get('EMPRESAS_ID')." AND p.testipodatos_id=1 AND p.id=i.tesdatos_id AND (i.tesdocumentos_id!=15 AND i.tesdocumentos_id!=34 AND i.tesdocumentos_id!=35 ) AND i.estadoingreso='Pendiente'".$cond." ORDER BY `p`.`razonsocial` ASC");
}
/*recibe el id del ingreso para ver si hay detaccion o adelantos*/
public function getRestas($id=0)
{
	$item=$this->find_first((int)$id);
	$TOTAL=$item->totalconigv;
	$detraccion=load::model('tesdetracciones');
	$dtr='';
	if($detraccion->exists('tesingresos_id='.$id))
	{
		switch ($item->tesmonedas_id)
		{
		case 1: 
		
		$dtr=" *Detraccion = S/. ".$detraccion=number_format($item->getTesdetracciones()->monto,2,'.',' ');
		$TOTAL=$TOTAL-$detraccion; break;
		case 2: 
		$dtr=" *Detraccion = $/. ".$detraccion=number_format($item->getTesdetracciones()->monto/$item->getTestipocambios()->compra,2,'.',' ');
		$TOTAL=$TOTAL-$detraccion; break;
		case 4: 
		$dtr=" *Detraccion = S/. ".$detraccion=number_format($item->getTesdetracciones()->monto,2,'.',' ');
		$TOTAL=$TOTAL-$detraccion;break;
		case 5: 
		$dtr=" *Detraccion = $/. ".$detraccion=number_format($item->getTesdetracciones()->monto/$item->getTestipocambios()->compra,2,'.',' ');
		$TOTAL=$TOTAL-$detraccion;
		break;
		case 0: 
		$dtr='';
		$TOTAL=$TOTAL-0;
		break;
		}
	}

if($item->getAdelantos()!=0){
	
	$dtr.=" *Adelanto = S/. ".number_format($item->getAdelantos(),2,'.',' ');
	$TOTAL=$TOTAL-$item->getAdelantos();
	}
	return $dtr.'-'.$TOTAL;
}
/* TOTALES PARA GRAFICOS DE LOS CLIENTES SUMAR LS CAMPOS IGV Y TOTALCONIGV  VALORES PARA QUE PUEDA INGRESAR EL AÑO */
public function getTotalesall($valores='')
{
	return $this->find_all_by_sql("SELECT s.femision,
IFNULL(sum(if(s.tesmonedas_id=2,s.igv*(select compra from testipocambios WHERE fecha=s.femision group by fecha),s.igv))+(select IFNULL(sum(nd.igv),0) from tesingresos as nd WHERE nd.aclempresas_id=".Session::get('EMPRESAS_ID')." AND nd.tesdocumentos_id=14 AND DATE_FORMAT(nd.femision,'%Y-%m')=DATE_FORMAT(s.femision,'%Y-%m'))-
(select IFNULL(sum(nc.igv),0) from tesingresos as nc WHERE nc.aclempresas_id=".Session::get('EMPRESAS_ID')." AND nc.tesdocumentos_id=13 AND DATE_FORMAT(nc.femision,'%Y-%m')=DATE_FORMAT(s.femision,'%Y-%m')),0) as igv, 
sum(if(s.tesmonedas_id=2,s.totalconigv*(select compra from testipocambios WHERE fecha=s.femision group by fecha),s.totalconigv))+(select IFNULL(sum(nd.totalconigv),0) from tesingresos as nd WHERE nd.aclempresas_id=".Session::get('EMPRESAS_ID')." AND nd.tesdocumentos_id=14 AND DATE_FORMAT(nd.femision,'%Y-%m')=DATE_FORMAT(s.femision,'%Y-%m'))-
(select IFNULL(sum(nc.totalconigv),0) from tesingresos as nc WHERE nc.aclempresas_id=".Session::get('EMPRESAS_ID')." AND nc.tesdocumentos_id=13 AND DATE_FORMAT(nc.femision,'%Y-%m')=DATE_FORMAT(s.femision,'%Y-%m')) as total,
DATE_FORMAT(s.femision,'%m/%y') as fecha FROM tesdatos as da, tesingresos as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND da.id=s.tesdatos_id AND s.tesdocumentos_id=7.".$valores." GROUP BY DATE_FORMAT(s.femision,'%Y-%m') ");
}

public function getPendientesLetrasIngresos($conditions)
{
	/*
UNION DISTINCT
SELECT i.*, i.numero as numerounico,'Coutas' as bancos FROM tesingresos as i 
WHERE ".$conditions."*/
	$sql="SELECT i.*, l.numerounico as numerounico,l.bancos as bancos FROM tesingresos as i LEFT JOIN tesletrasingresos as l ON l.tesingresos_id=i.id WHERE ".$conditions."
order by fvencimiento ASC";
//echo $sql;
	return $this->find_all_by_sql($sql);
	
	
}
/*Para el reporte de hilos por mes y anio*/

public function getHilosAnual($tipo=0,$datos_id=0,$anio='2015',$producto_id=0,$color_id=0)
{
	$group='group by d.tesproductos_id,d.tesingresos_id';
	$order='ORDER BY `p`.`nombrecorto` DESC';
	$cond='';
	$cond_d='';
	$cond_p='';
	$cond_c='';
	$agrupar='group by mes,d.tescolores_id,d.tesproductos_id ';
	if($producto_id==0 && $color_id==0)$agrupar='group by d.tescolores_id,d.tesproductos_id ';
	if($tipo!=0){$cond=' AND t.id='.$tipo;}
	if($datos_id!=0){$cond_d=' AND i.tesdatos_id='.$datos_id;}
	if($producto_id!=0){$cond_p=' AND d.tesproductos_id='.$producto_id;}
	if($color_id!=0){$cond_c=' AND d.tescolores_id='.$color_id;}
	$sql='
	SELECT SUM( IFNULL( d.pesoneto, 0 ) ) AS cantidad, DATE_FORMAT( femision,  "%Y" ) AS anio, t.id as tid,t.nombre as tipo,p.id as pid,c.id as cid,c.nombre as color,p.nombre as nombre,date_format(i.femision,"%m") as mes,i.* 
	FROM tesproductos as p, testipoproductos as t,tescolores as c, tesingresos as i 
	INNER JOIN tesdetalleingresos as d ON i.id=d.tesingresos_id'.$cond_d.''.$cond_p.''.$cond_c.'
	WHERE c.id=d.tescolores_id AND t.teslineaproductos_id=3 AND p.id=d.tesproductos_id'.$cond.' AND t.id=p.testipoproductos_id AND date_format(femision,"%Y")="'.$anio.'" AND i.aclempresas_id=1 AND i.pr!="PL" AND i.tesdocumentos_id=15 '.$agrupar.' ORDER BY tipo,nombre ASC';
	//if($producto_id==0 && $color_id==0)echo $sql;
return $this->find_all_by_sql($sql);
}

public function getDetalles_hilo_semanal($tipo=0)/*esta funcion solo es para santa patrcia*/
{
	$cond=' ';
	if($tipo!=0)$cond=' AND p.testipoproductos_id='.$tipo.' ';
	return $this->find_all_by_sql("SELECT d.id,d.tesproductos_id as pid, p.nombrecorto as nombrecorto, p.nombre as nombre, d.tescolores_id as cid, c.nombre as color,tp.nombre as tipo FROM tesdetalleingresos as d,tesproductos as p, testipoproductos as tp, tescolores as c WHERE p.testipoproductos_id=tp.id".$cond."AND tp.teslineaproductos_id=3 AND d.tesproductos_id=p.id AND p.aclempresas_id=1 AND c.id=d.tescolores_id
GROUP BY d.tesproductos_id,d.tescolores_id
ORDER BY tipo,nombre,color ASC");	
}
/*
Recibe el tipo de documento 
Devuelve el total y el igv
*/
public function igv_total($fecha,$documento_id=7)
{$soles=1;$dolares=2;
	if(Session::get('EMPRESAS_ID')==2)
	{
		$soles=4;$dolares=5;
	}
	$s=$this->find_by_sql("SELECT sum(igv) as igv,sum(totalconigv)as totalconigv FROM `tesingresos`
WHERE date_format(femision,'%Y-%m')='".$fecha."' AND tesmonedas_id=".$soles." AND tesdocumentos_id=".$documento_id." AND aclempresas_id=".Session::get('EMPRESAS_ID'));
	
	$d=$this->find_by_sql("SELECT sum(m.igv*c.compra) as igv,sum(m.totalconigv*c.compra)as totalconigv FROM tesingresos as m,testipocambios as c
WHERE m.testipocambios_id=c.id AND date_format(m.femision,'%Y-%m')='".$fecha."' AND m.tesmonedas_id=".$dolares." AND m.tesdocumentos_id=".$documento_id." AND m.aclempresas_id=".Session::get('EMPRESAS_ID'));
	
	$V=array();
	$V["igv"]=$s->igv+$d->igv;
	$V["totalconigv"]=$s->totalconigv+$d->totalconigv;
	return $V;
	/*echo "SELECT ifnull(sum(igv),0) as igv,ifnull(sum(totalconigv),0)as totalconigv FROM `tesingresos`
WHERE date_format(femision,'%Y-%m')='".$fecha."' AND tesdocumentos_id=".$documento_id." AND aclempresas_id=".Session::get('EMPRESAS_ID');
	$s=$this->find_by_sql("SELECT ifnull(sum(igv),0) as igv,ifnull(sum(totalconigv),0)as totalconigv FROM `tesingresos`
WHERE date_format(femision,'%Y-%m')='".$fecha."' AND tesdocumentos_id=".$documento_id." AND aclempresas_id=".Session::get('EMPRESAS_ID'));
	
	
	
	$V=array();
	$V["igv"]=$s->igv;
	$V["totalconigv"]=$s->totalconigv;
	return $V;*/
}

public function get_obligaciones($ids,$anio,$estado)
{
	//$sql="SELECT  l.*,'RUC' AS tipo, l.numeroletra, i.femision, i.fvencimiento, l.bancos, l.monto, i.estadoingreso, l.estadodeletra, l.numerounico FROM tesletrasingresos as l INNER JOIN tesingresos as i ON i.id=l.tesingresos_id AND i.aclempresas_id=".Session::get("EMPRESAS_ID")." AND i.tesdatos_id = ".$ids." order by i.femision DESC";
	$sql="SELECT  i.* FROM tesingresos as i WHERE i.aclempresas_id=".Session::get("EMPRESAS_ID")." AND i.estadoingreso='".$estado."' AND i.tesdatos_id = ".$ids." AND DATE_FORMAT(i.femision,'%Y')=".$anio." AND i.tesdocumentos_id!=15 AND i.tesdocumentos_id!=7 order by i.fvencimiento DESC";
	//echo $sql;
	return $this->find_all_by_sql($sql);
}

}
?>