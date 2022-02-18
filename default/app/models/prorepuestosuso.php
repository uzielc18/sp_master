<?php
class Prorepuestosuso extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('proubicacionmaquina','prorepuestos','prodetallepedidos');
    }
	public function getResumen($id,$maquina=0)
	{
		
		$maq='';
		if($maquina!=0)$maq=' AND m.id ='.$maquina;
		$sql="SELECT CONCAT_WS('-',m.prefijo,m.numero)as maquina,d.precio as precio,d.cantidad as cantidad,p.detalle as descripcion, pp.codigo as codigo, p.codigo_ant as codigo_ant,r.ubicacion as ubicacion,p.piso_alamacen as piso,p.fila_almacen as fila,p.columna_almacen as columna,p.ubicacion_almacen as a_ubicacion,ru.* 
FROM prorepuestosuso as ru,tesproductos as p,prorepuestos as r, prodetallepedidos as d, pronotapedidos as pp, proubicacionmaquina as u, promaquinas as m
WHERE u.id=ru.proubicacionmaquina_id AND u.promaquinas_id = m.id AND ru.prorepuestos_id=r.id AND r.tesproductos_id=p.id AND d.id=ru.prodetallepedidos_id AND d.pronotapedidos_id = pp.id AND r.aclempresas_id=".Session::get('EMPRESAS_ID')." AND p.aclempresas_id=".Session::get('EMPRESAS_ID')." AND p.id=".$id.$maq.' order by ru.fechaingreso DESC limit 0,20 ';
		//echo $sql;
		return $this->find_all_by_sql($sql);
	}
}
?>