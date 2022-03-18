<?php
class Proproduccion extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('prodetalleproduccion','proplegadoresenuso');
		$this->belongs_to('aclusuarios','promaquinas');
    }
	
	/*$id es el id de la maquina hojas con detalle de produccion GROUP BY pd.proproduccion_id*/
	public function findHojas($id,$anio,$mes_activo){
		
		return $this->find_all_by_sql("SELECT p.*,IFNULL(SUM(pd.metroprogramados),0) AS metroprogramados,IFNULL(SUM(pd.metrosrevisados),0) AS metrosrevisados,
IFNULL((Select m.metros FROM promerma as m WHERE p.id=m.proproduccion_id),0)as merma FROM proproduccion as p LEFT JOIN prodetalleproduccion AS pd ON p.id = pd.proproduccion_id WHERE (DATE_FORMAT(fecha,'%Y-%m')='".$anio."-".$mes_activo."' OR ISNULL(fecha) OR p.estadoproduccion='PRODUCCION') AND p.promaquinas_id=".$id." GROUP BY pd.proproduccion_id");
		
		}
	/*
	#
	#@ id el id del 
	#valida la existencia de de trama nueva para pedido en trama por producion
	
	*/
	public function existsNewtrama($id)
	{
		$t=$this->find_by_sql('SELECT count(t.id) as c FROM protrama as t, prodetalleproduccion as pd WHERE ISNULL(t.prodetallepedidos_id) AND pd.proproduccion_id='.$id.' AND t.prodetalleproduccion_id=pd.id');
		if($t->c==0)
		{
			return FALSE;
		}else
		{
			return TRUE;
		}
	}
	
	public function getProduccion($id=0)/*el id de la maquina*/
	{
		return $this->find_first('conditions: promaquinas_id='.$id.' AND (estadoproduccion="PRODUCCION" OR estadoproduccion="PROGRAMACION")');
	}
	public function getProgramacion($id=0)/*el id de la maquina*/
	{
		if($this->exists('promaquinas_id='.$id.' AND estadoproduccion="PROGRAMACION"')){
		return $this->find_first('conditions: promaquinas_id='.$id.' AND estadoproduccion="PROGRAMACION"');
		}
		return 0;
	}
	
	public function totalUsados($id=0)/*recibe el id de la guia*/
	{
		$p=$this->find_by_sql("SELECT IFNULL(sum(prodetalleproduccion.metrosrevisados),0) as c  FROM `prodetalleproduccion`, proproduccion 
		WHERE proproduccion.id=prodetalleproduccion.proproduccion_id AND proproduccion.guia_id=".$id);
		return $p->c;
	}
	/*Consulta para la pagina de bienvenida */
	public function panel_produccion()
	{
		return $this->find_all_by_sql("SELECT m.id,pr.id,CONCAT_WS('-',m.prefijo,m.numero) as maquina,pr.nombre as tela,
		sum(d.metroprogramados) as metros, c.nombre as color,CONCAT_WS('-',d.corte,d.observaciones) as obs 
		FROM promaquinas as m 
		INNER JOIN proproduccion as p ON m.id=p.promaquinas_id AND p.estadoproduccion='PRODUCCION' 
		INNER JOIN prodetalleproduccion as d ON p.id=d.proproduccion_id 
		INNER JOIN tesproductos as pr ON pr.id=d.tesproductos_id 
		INNER JOIN tescolores as c ON c.id=d.tescolores_id 
		WHERE ISNULL(d.fechadecorte) 
		GROUP BY m.id,pr.id,m.prefijo,m.numero,pr.nombre ,d.metroprogramados, c.nombre ,d.corte,d.observaciones,d.fecha_at 
		order by d.fecha_at DESC limit 0,10");
	}
	public function panel_ventas()
	{
		return $this->find_all_by_sql("SELECT s.id, d.razonsocial as empresa, s.numero as numero,s.npedido as pedido,s.totalconigv as total, s.estadosalida as estado,s.femision as fecha
FROM tessalidas as s INNER JOIN tesdatos as d ON d.id=s.tesdatos_id 
WHERE s.estadosalida!='ANULADO' AND s.tesdocumentos_id=7 AND s.aclempresas_id=".Session::get('EMPRESAS_ID')."
ORDER BY s.femision DESC
limit 0,20");
	}
	public function panel_semanal($fecha,$documento_id)
	{
		return $this->find_all_by_sql("SELECT s.id, d.razonsocial as empresa, s.numero as numero,s.npedido as pedido,s.totalconigv as total, s.estadosalida as estado,s.femision as fecha
FROM tessalidas as s INNER JOIN tesdatos as d ON d.id=s.tesdatos_id 
WHERE s.estadosalida!='ANULADO' AND s.tesdocumentos_id=".$documento_id." AND s.aclempresas_id=".Session::get('EMPRESAS_ID')." ORDER BY s.femision DESC limit 0,10");
	}
	public function panel_rollos()
	{
		return $this->find_all_by_sql("SELECT p.nombre AS tela, c.nombre AS color, SUM( r.metros ) AS metros
		FROM  prorollos AS r
		INNER JOIN tesproductos AS p ON p.id = r.tesproductos_id
		INNER JOIN tescolores AS c ON r.tescolores_id = c.id
		WHERE r.estadorollo =  'VENTA'
		GROUP BY p.id, c.id, p.nombre, c.nombre,r.metros, r.fecha_in
		ORDER BY r.fecha_in DESC 
		LIMIT 0 , 10");
	}
	public function panel_orden()
	{
		return $this->find_all_by_sql("SELECT d.razonsocial as nombre, o.totalconigv as total ,o.observacion as obs, o.ubicacion as ubicacion, o.fecha as fecha FROM tesordendecompras as o INNER JOIN tesdatos as d ON d.id=o.tesdatos_id
WHERE o.origenorden='externo' AND o.ubicacion!='TERMINADO' AND o.aclempresas_id=".Session::get('EMPRESAS_ID')."
order by o.fecha DESC limit 0,10");
	}
	public function panel_ingresos()
	{
		return $this->find_all_by_sql("SELECT d.razonsocial AS nombre, i.numero AS numero, c.abr AS doc, i.totalconigv as total, i.estadoingreso AS estado
FROM tesdocumentos AS c, tesingresos AS i
INNER JOIN tesdatos AS d ON d.id = i.tesdatos_id
WHERE i.aclempresas_id =".Session::get('EMPRESAS_ID')."
AND i.estadoingreso !=  'ANULADO'
AND i.estadoingreso !=  'Con Factura'
AND c.id = i.tesdocumentos_id
ORDER BY i.id DESC 
LIMIT 0 , 10");
	}
	public function panel_calendario()
	{
		return $this->find_all_by_sql("SELECT CONCAT_WS('-',m.prefijo,m.numero) as maquina,pr.nombre as tela,d.metroprogramados as metros, c.nombre as color,CONCAT_WS('-',d.corte,d.observaciones) as obs
FROM promaquinas as m, tescolores as c, tesproductos as pr, prodetalleproduccion as d INNER JOIN proproduccion as p ON p.id=d.proproduccion_id AND p.estadoproduccion='PRODUCCION'
WHERE pr.id=d.tesproductos_id AND c.id=d.tescolores_id AND m.id=p.promaquinas_id AND ISNULL(d.fechadecorte)
order by d.fecha_at DESC");
	}
}
