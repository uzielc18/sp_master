<?php
class Tesvendedores extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesdatos');
		$this->belongs_to('acldatos');
    }
	public function before_validation_on_create() {
        $this->validates_uniqueness_of('codigo', 'message: Ya existe un vendedor con el <b>mismo Codigo</b>');
    }
	public function codigo()
	{
		$maximo = $this->maximum("codigo", "conditions: aclempresas_id =".Session::get('EMPRESAS_ID'));
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

	public function getAV()
	{
		$sql="SELECT v.*,(select ifnull(sum(a.total),0)
from tesabonos as a 
INNER JOIN tesdatos as d ON a.tesdatos_id=d.id AND (select count(id) from tesabonosdetalles where tesabonos_id=a.id)=1 AND a.tesmonedas_id=1
where d.tesvendedores_id=v.id AND isnull(tipodeabono) AND isnull(adelanto_no_mostrar)) as total_sp,
(select ifnull(sum(a.total),0)
from tesabonos as a 
INNER JOIN tesdatos as d ON a.tesdatos_id=d.id AND (select count(id) from tesabonosdetalles where tesabonos_id=a.id)=1 AND a.tesmonedas_id=2
where d.tesvendedores_id=v.id AND isnull(tipodeabono) AND isnull(adelanto_no_mostrar)) as total_dp,
(select ifnull(sum(a.total),0)
from tesabonos as a 
INNER JOIN tesdatos as d ON a.tesdatos_id=d.id AND (select count(id) from tesabonosdetalles where tesabonos_id=a.id)=1 AND a.tesmonedas_id=4
where d.tesvendedores_id=v.id AND isnull(tipodeabono) AND isnull(adelanto_no_mostrar)) as total_sc,
(select ifnull(sum(a.total),0)
from tesabonos as a 
INNER JOIN tesdatos as d ON a.tesdatos_id=d.id AND (select count(id) from tesabonosdetalles where tesabonos_id=a.id)=1 AND a.tesmonedas_id=5
where d.tesvendedores_id=v.id AND isnull(tipodeabono) AND isnull(adelanto_no_mostrar)) as total_dc FROM tesvendedores as v 
WHERE v.aclempresas_id=".Session::get('EMPRESAS_ID');
	//echo $sql;
		return $this->find_all_by_sql($sql);
	}
	
}
?>