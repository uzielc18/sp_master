<?Php 
Load::models('tesingresos','tessalidas','tessalidasinternas','pronotapedidos','tescajas','procajastrama');
class Inventario
{
	public function inicial($producto,$gr='d.lote')
	{
		$sql = new Tesingresos();
		return $sql->find_all_by_sql("SELECT i.*,d.tescolores_id as color, d.lote as lote,ifnull(sum(d.cantidad),0) as total FROM tesdetalleingresos as d INNER JOIN tesingresos as i ON i.id=d.tesingresos_id AND i.tesdocumentos_id=27 AND i.aclempresas_id=".Session::get("EMPRESAS_ID")."
WHERE d.tesproductos_id=".$producto." GROUP BY ".$gr.' order by '.$gr.' ASC');
	}
	
	public function i_guias($producto,$gr='d.lote')
	{
		$sql = new Tesingresos();
		return $sql->find_all_by_sql("SELECT i.*,d.tescolores_id as color, d.lote as lote,ifnull(sum(d.cantidad),0) as total FROM tesdetalleingresos as d INNER JOIN tesingresos as i ON i.id=d.tesingresos_id AND i.tesdocumentos_id=15 AND i.aclempresas_id=".Session::get("EMPRESAS_ID")."
WHERE d.tesproductos_id=".$producto." GROUP BY ".$gr.' order by '.$gr.' ASC');
	}
	public function i_facturas($producto,$gr='d.lote')
	{
		$sql = new Tesingresos();
		return $sql->find_all_by_sql("SELECT i.numero,d.tescolores_id as color, d.lote as lote,ifnull(sum(d.cantidad),0) as total
FROM tesdetalleingresos as d INNER JOIN tesingresos as i ON i.id=d.tesingresos_id AND i.tesdocumentos_id=7 AND i.aclempresas_id=".Session::get("EMPRESAS_ID")."  AND isnull(i.numeroguia)  WHERE d.tesproductos_id=".$producto." GROUP BY ".$gr.' order by '.$gr.' ASC');
	}
	public function i_notas($producto,$gr='d.lote')
	{
		if($gr=='d.tesingresos_id')$gr='d.pronotapedidos_id';
		$sql = new Pronotapedidos();
		return $sql->find_all_by_sql("SELECT pronotapedidos.codigo as numero, d.tescolores_id as color,d.lote as lote, ifnull(SUM(d.cantidad),0) as total 
FROM pronotapedidos 
INNER JOIN prodetallepedidos as d ON pronotapedidos.id=d.pronotapedidos_id 
WHERE d.tesproductos_id=".$producto." AND (pronotapedidos.estadonota='Ingresado' or pronotapedidos.estadonota='Entregado' or pronotapedidos.estadonota='Revisado') AND ISNULL(d.prorollos_id) AND pronotapedidos.aclempresas_id=".Session::get("EMPRESAS_ID")." AND pronotapedidos.tipo='ingreso' GROUP BY ".$gr.' order by '.$gr.' ASC');
	}
	public function i_autosalidas($producto,$gr='d.lote')
	{
		$sql = new Tesingresos();
		return $sql->find_all_by_sql("SELECT d.tescolores_id as color, i.numero, d.lote as lote,ifnull(sum(d.cantidad),0) as total
FROM tesdetalleingresos as d INNER JOIN tesingresos as i ON i.id=d.tesingresos_id AND i.tesdocumentos_id=15 AND i.aclempresas_id=".Session::get("EMPRESAS_ID")." AND !ISNULL(i.autosalida)
WHERE d.tesproductos_id=".$producto." GROUP BY ".$gr.' order by '.$gr.' ASC');
	}
	
	public function s_facturas($producto,$gr='d.lote')
	{
		if($gr=='d.tesingresos_id')$gr='d.tessalidas_id';
		$sql = new Tessalidas();
		return $sql->find_all_by_sql("SELECT s.numero,d.tescolores_id as color, d.lote as lote, ifnull(sum(d.cantidad),0) as total 
FROM tesdetallesalidas as d INNER JOIN tessalidas as s ON s.id=d.tessalidas_id AND s.aclempresas_id=".Session::get("EMPRESAS_ID")." AND s.tesdocumentos_id=7 AND isnull(s.numeroguia)
WHERE d.tesproductos_id=".$producto." GROUP BY ".$gr." order by ".$gr." ASC");
	}
	public function s_guias($producto,$gr='d.lote')
	{
		if($gr=='d.tesingresos_id')$gr='d.tessalidas_id';
		$sql = new Tessalidas();
		return $sql->find_all_by_sql("SELECT s.numero, d.tescolores_id as color,  d.lote as lote, ifnull(sum(d.cantidad),0) as total 
FROM tesdetallesalidas as d INNER JOIN tessalidas as s ON s.id=d.tessalidas_id AND s.aclempresas_id=".Session::get("EMPRESAS_ID")." AND s.tesdocumentos_id=15
WHERE d.tesproductos_id=".$producto." GROUP BY ".$gr.' order by '.$gr.' ASC');
	}
	
	public function s_guias_internas($producto,$gr='d.lote')
	{
		if($gr=='d.tesingresos_id')$gr='d.tessalidasinternas_id';
		$sql = new Tessalidasinternas();
		return $sql->find_all_by_sql("SELECT s.numero, d.tescolores_id as color, d.lote as lote, ifnull(sum(d.cantidad),0) as total 
FROM tesdetallesalidasinternas as d INNER JOIN tessalidasinternas as s ON s.id=d.tessalidasinternas_id AND s.aclempresas_id=".Session::get("EMPRESAS_ID")." AND s.tesdocumentos_id=15
WHERE d.tesproductos_id=".$producto." GROUP BY ".$gr.' order by '.$gr.' ASC');
	}
	public function s_notas($producto,$gr='d.lote')
	{
		if($gr=='d.tesingresos_id')$gr='d.pronotapedidos_id';
		$sql = new Pronotapedidos();
		return $sql->find_all_by_sql("SELECT pronotapedidos.codigo as numero, d.tescolores_id as color, d.lote, ifnull(SUM(d.cantidad),0) as total 
FROM pronotapedidos 
INNER JOIN prodetallepedidos as d ON pronotapedidos.id=d.pronotapedidos_id 
WHERE d.tesproductos_id=".$producto." AND (pronotapedidos.estadonota='Ingresado' or pronotapedidos.estadonota='Entregado' or pronotapedidos.estadonota='Revisado') AND ISNULL(d.prorollos_id) AND pronotapedidos.aclempresas_id=".Session::get("EMPRESAS_ID")." AND pronotapedidos.tipo='salida' GROUP BY ".$gr.''.' order by '.$gr.' ASC');
	}
	public function i_cajas($producto,$gr='d.lote')
	{
		if($gr=='d.id')$gr='c.id';
		$sql = new Tescajas();
		return $sql->find_all_by_sql("SELECT c.id,d.lote,ifnull(sum(c.pesoneto),0) as total,sum(c.conos) as conos FROM tescajas as c INNER JOIN tesdetalleingresos as d ON d.id=c.tesdetalleingresos_id AND d.tesproductos_id=".$producto." AND c.aclempresas_id=".Session::get("EMPRESAS_ID")." GROUP BY ".$gr.' order by '.$gr.' ASC');
	}
	public function s_cajas($producto,$gr='d.lote')
	{
		if($gr=='d.id')$gr='c.id';
		if($gr=='d.tesingresos_id')$gr='d.pronotapedidos_id';
		$sql = new Procajastrama();
		return $sql->find_all_by_sql("SELECT c.tescajas_id,c.id,c.lote as lote, ifnull(sum(c.peso),0) as total, sum(c.conos) as conos FROM procajastrama as c 
INNER JOIN prodetallepedidos as d ON d.id=c.prodetallepedidos_id AND d.tesproductos_id=".$producto." GROUP BY ".$gr.' order by '.$gr.' ASC');
	}
	
}
?>