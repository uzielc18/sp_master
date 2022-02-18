<?php
class Peraccidentes extends ActiveRecord {

public function initialize() {
        //validaciones
		$this->belongs_to('aclusuarios');
    }
	
	public function accidentesMes($anio=0,$mes=0)
	{
		$meses='';
		if($mes!=0){
		$meses=" and DATE_FORMAT(fecha,'%Y-%m')='".$anio."-".$mes."'";
		}
		return $this->find("conditions: estado=1 ".$meses." and aclempresas_id=".Session::get("EMPRESAS_ID"),'order: fecha,hora asc');
		
		}	
	public function accidentesDia($anio=2012,$mes=0,$dia=0)
	{
		$meses=" and DATE_FORMAT(fecha,'%Y-%m-%d')='".$anio."-".$mes."-".$dia."'";
		return $this->find("conditions: estado=1 ".$meses." and aclempresas_id=".Session::get("EMPRESAS_ID"),'order: fecha,hora asc');
		
	}
}
?>