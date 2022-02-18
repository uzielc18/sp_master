<?php
class ScProduccion extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('sc_detalleprogramados','sc_detalleprocesos');
		$this->belongs_to('aclusuarios','tesdatos','promaquinas');
    }
	public function generarNumero($prefijo='SC')
	{
		//$maximo = $this->maximum("numero", "conditions: SUBSTRING( numero, 1, 2 )='".$prefijo."' AND (estado='1' OR estado='9')");
		$maximos = $this->find_by_sql('SELECT (IFNULL(MAX(SUBSTRING(numero,4)),0)) as numero FROM `sc_produccion` WHERE SUBSTRING( numero, 1, 2 )="'.$prefijo.'" AND (estado="1" OR estado="9")');
		$maximo=$maximos->numero;
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
		return $prefijo.'-'.$maximo;
	}
	public function getDetallesproduccion()
	{
		return $this->find_all_by_sql("SELECT p.numero as numero,p.fecha as fecha,d.razonsocial as razonsocial,r.nombre as tela,c.nombre as color,r.metros as metros,t.nombre as producto,dp.cantidad as cantidad,a.nombre as encargado FROM sc_produccion as p, tesdatos as d,sc_detalleprogramados as dp,sc_crollos as r,tescolores as c,tesproductos as t,acldatos as a WHERE d.id=p.tesdatos_id AND p.id=dp.sc_produccion_id AND r.id=dp.sc_crollos_id AND r.tescolores_id=c.id AND t.id=dp.tesproductos_id AND dp.acldatos_id=a.id");
	}
}
?>