<?php

class Prorepuestos extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('prorepuestosuso');
		$this->belongs_to('tesproductos');
    }
	public function before_validation_on_create() {
        $this->validates_uniqueness_of('tesproductos_id', 'message: El <b>Producto ya fue creado');
    }
public function getSotck($c,$o,$l_id=6)
{
	$sql="SELECT p.id as id_producto, p.codigo_ant as codigo, p.nombre as nombre, p.ubicacion_almacen as ubicacion,r.paglibro as paglibro ,r.ubicacion as r_ubicacion, IFNULL(p.stokminimo,0) as stokminimo, sum(d.cantidad)-(select IFNULL(sum(dp.cantidad),0) from prodetallepedidos as dp where dp.tesproductos_id=p.id)-(select IFNULL(sum(sd.cantidad),0) from tesdetallesalidas as sd where sd.tesproductos_id=p.id)as stokactual,sum(d.cantidad) as ingreso, (select IFNULL(sum(dp.cantidad),0) FROM prodetallepedidos as dp WHERE dp.tesproductos_id=p.id) as salida,p.precio as preciosoles,p.preciod as preciodolares
FROM testipoproductos as t,teslineaproductos as l,tesproductos as p,prorepuestos as r ,tesingresos as i INNER JOIN tesdetalleingresos as d ON  d.tesingresos_id=i.id AND ((select sum(cantidad) from tesdetalleingresos WHERE p.id=tesdetalleingresos.tesproductos_id)-(select IFNULL(sum(dp.cantidad),0) from prodetallepedidos as dp where dp.tesproductos_id=p.id))!=0 AND i.tesdocumentos_id!=7
WHERE d.tesproductos_id=p.id AND p.activo=1 AND p.id=r.tesproductos_id AND p.testipoproductos_id=t.id AND t.teslineaproductos_id=l.id AND l.id=".$l_id."
GROUP BY d.tesproductos_id 
order by ".$c." ".$o;
	//echo $sql;
	return $this->find_all_by_sql($sql);
}

public function getIngresos($Y,$m,$moneda_d=2)
{
	$sql="SELECT i.tesmonedas_id,p.id as id_producto, p.codigo_ant as codigo, p.nombre as nombre, DATE_FORMAT(i.femision,'%d/%m/%Y') as fecha,if(i.tesmonedas_id=".$moneda_d.",d.preciounitario,d.preciounitario/(select compra from testipocambios where fecha=i.femision)) as preciounitario ,d.cantidad as cantidad,if(i.tesmonedas_id=".$moneda_d.",d.preciounitario,d.preciounitario/(select compra from testipocambios where fecha=i.femision))*d.cantidad as total
FROM tesproductos as p,tesingresos as i INNER JOIN tesdetalleingresos as d ON d.tesingresos_id=i.id AND i.aclempresas_id=".Session::get('EMPRESAS_ID')." WHERE i.pr='RR' AND d.tesproductos_id=p.id AND DATE_FORMAT(i.femision,'%Y-%m')='".$Y."-".$m."'";
	echo $sql;
	return $this->find_all_by_sql($sql);
}

public function stokRepocicion($l_id=6)
{
	return $this->find_all_by_sql('SELECT p.id as id_producto, p.codigo_ant as codigo, p.nombre as nombre, IFNULL(p.stokminimo,0) as stokminimo, sum(d.cantidad)-(select IFNULL(sum(dp.cantidad),0) from prodetallepedidos as dp where dp.tesproductos_id=p.id)-(select IFNULL(sum(sd.cantidad),0) from tesdetallesalidas as sd where sd.tesproductos_id=p.id)as stokactual,AVG(CONVERT(p.precio,DECIMAL)) as preciosoles, AVG(CONVERT(p.preciod,DECIMAL)) as preciodolares
FROM testipoproductos as t,teslineaproductos as l,tesproductos as p,tesingresos as i 
INNER JOIN tesdetalleingresos as d ON d.tesingresos_id=i.id AND i.aclempresas_id='.Session::get('EMPRESAS_ID').'  
WHERE d.tesproductos_id=p.id AND p.aclempresas_id='.Session::get('EMPRESAS_ID').' AND p.activo=1 AND p.testipoproductos_id=t.id AND t.teslineaproductos_id=l.id AND l.id='.$l_id.' AND (
(select sum(cantidad) from tesdetalleingresos WHERE p.id=tesdetalleingresos.tesproductos_id)
-
(select IFNULL(sum(dp.cantidad),0) from prodetallepedidos as dp where dp.tesproductos_id=p.id)
)<p.stokminimo
GROUP BY d.tesproductos_id ORDER BY p.nombre ASC');
}

/*Modificar para recibir el mes y el año y solo es el año mostrar todo el año*/

public function getUtilizacion($Y,$m)
{
	$cond=" AND DATE_FORMAT(u.fechaingreso,'%Y-%m')='".$Y."-".$m."'";
	if($m==0){$cond=" AND DATE_FORMAT(u.fechaingreso,'%Y')='".$Y."'";}
$sql="SELECT pn.codigo as numero,p.id as id,p.codigo_ant as condigo_ant,p.nombre as nombre,u.fechaingreso as fecha ,CONCAT_WS('-',m.prefijo,m.numero) as maquina,ub.nombre as ubicacion,d.cantidad as cantidad, 
IFNULL( p.preciod, 0 ) as precio,
( IFNULL( p.preciod, 0 )*d.cantidad) as total,
(SELECT sum(dd.cantidad)-(select IFNULL(sum(ddp.cantidad),0) from prodetallepedidos as ddp where ddp.tesproductos_id=pp.id)-(select IFNULL(sum(dsd.cantidad),0) from tesdetallesalidas as dsd where dsd.tesproductos_id=pp.id)as stokactual
FROM testipoproductos as dt,teslineaproductos as dl,tesproductos as pp,prorepuestos as dr ,tesingresos as di 
INNER JOIN tesdetalleingresos as dd ON dd.tesingresos_id=di.id AND di.tesdocumentos_id!=7 
WHERE dd.tesproductos_id=pp.id AND pp.activo=1 AND pp.id=dr.tesproductos_id AND pp.testipoproductos_id=dt.id AND dt.teslineaproductos_id=dl.id AND dl.id=6 AND pp.id=p.id GROUP BY dd.tesproductos_id)as stokactual

FROM prorepuestosuso as u, proubicacionmaquina as ub, promaquinas as m,pronotapedidos as pn, prodetallepedidos as d, prorepuestos as r 
INNER JOIN tesproductos as p ON p.id=r.tesproductos_id AND p.aclempresas_id=1 
WHERE pn.id=d.pronotapedidos_id AND u.proubicacionmaquina_id=ub.id AND ub.promaquinas_id=m.id AND r.id=u.prorepuestos_id AND u.prodetallepedidos_id=d.id".$cond." order by p.nombre,u.fechaingreso";
//echo $sql;
	return $this->find_all_by_sql($sql);
	
}
public function getUtilizacion_m($Y,$m)
{
	$cond=" AND DATE_FORMAT(u.fechaingreso,'%Y-%m')='".$Y."-".$m."'";
	if($m==0){$cond=" AND DATE_FORMAT(u.fechaingreso,'%Y')='".$Y."'";}
$sql="SELECT m.numero as n_maquina,pn.codigo as numero,p.id as id,p.codigo_ant as condigo_ant,p.nombre as nombre,u.fechaingreso as fecha ,CONCAT_WS('-',m.prefijo,m.numero) as maquina,ub.nombre as ubicacion,d.cantidad as cantidad, 
IFNULL( p.preciod, 0 ) as precio,
( IFNULL( p.preciod, 0 )*d.cantidad) as total,
(SELECT sum(dd.cantidad)-(select IFNULL(sum(ddp.cantidad),0) from prodetallepedidos as ddp where ddp.tesproductos_id=pp.id)-(select IFNULL(sum(dsd.cantidad),0) from tesdetallesalidas as dsd where dsd.tesproductos_id=pp.id)as stokactual
FROM testipoproductos as dt,teslineaproductos as dl,tesproductos as pp,prorepuestos as dr ,tesingresos as di 
INNER JOIN tesdetalleingresos as dd ON dd.tesingresos_id=di.id AND di.tesdocumentos_id!=7 
WHERE dd.tesproductos_id=pp.id AND pp.activo=1 AND pp.id=dr.tesproductos_id AND pp.testipoproductos_id=dt.id AND dt.teslineaproductos_id=dl.id AND dl.id=6 AND pp.id=p.id GROUP BY dd.tesproductos_id)as stokactual

FROM prorepuestosuso as u, proubicacionmaquina as ub, promaquinas as m,pronotapedidos as pn, prodetallepedidos as d, prorepuestos as r 
INNER JOIN tesproductos as p ON p.id=r.tesproductos_id AND p.aclempresas_id=1 
WHERE pn.id=d.pronotapedidos_id AND u.proubicacionmaquina_id=ub.id AND ub.promaquinas_id=m.id AND r.id=u.prorepuestos_id AND u.prodetallepedidos_id=d.id".$cond." order by maquina,p.nombre";
//echo $sql;
	return $this->find_all_by_sql($sql);
	
}
public function getNGuia(){}

}
?>