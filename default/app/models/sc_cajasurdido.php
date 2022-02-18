<?php
class ScCajasurdido extends ActiveRecord{

    public function initialize() {
        //relaciones
		//$this->has_many('prodetallepedidos','tesdetallesalidas','tesdetallesalidasinternas','sc_detalleprocesos');
		$this->belongs_to('aclusuarios','aclempresas','tescolores','tescajas','tesproductos');
    }
	/*
	devuelve el total de conos 
	*/
	public function getUr($c_id)
	{
		$val=$this->find_by_sql('SELECT sum(conos)as conos,sum(peso)as peso FROM sc_cajasurdido WHERE tescajas_id='.$c_id);
		return $val->conos.'-'.$val->peso;
	}
	public function estadoCajas($array)
	{
		$val=explode(',',$array);
		foreach($val as $item=>$value)
		{
			$cajasUrdido=$this->find_first((int)$value);
			$cajasUrdido->estado='9';
			$cajasUrdido->save();
		}
	}
}
?>