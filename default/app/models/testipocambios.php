<?php
Load::models('tessalidas','tessalidasinternas','tesingresos');
class Testipocambios extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('aclempresas');
    }
	
	public function before_validation_on_create() {
        $this->validates_uniqueness_of('fecha', 'message: Ya existe un recurso con esa fecha');
    }
public function getCambioFecha($fecha='')
{
	
	if($this->exists('fecha="'.$fecha.'"'))
	{
		$val=$this->find_first('conditions: fecha="'.$fecha.'"');
		return $val->id;
	}else
	{
		return 0;
	}	
}
public function getSalidas($id,$fecha)
{
	$salidas= new Tessalidas();
	$por_id=$salidas->count('testipocambios_id='.$id);
	$por_fecha=$salidas->count('femision="'.$fecha.'"');
	return "ids: ".$por_id."<==> fecha:".$por_fecha;
}
public function cambiar($id,$fecha)
{
	$salidas= new Tessalidas();
	$salidas->update_all("testipocambios_id=".$id,"femision='".$fecha."'");
	$internas= new Tessalidasinternas();
	$internas->update_all("testipocambios_id=".$id,"femision='".$fecha."'");
	$ingresos= new tesingresos();
	$ingresos->update_all("testipocambios_id=".$id,"femision='".$fecha."'");
}
public function getInternas($id,$fecha)
{
	$internas= new Tessalidasinternas();
	$por_id=$internas->count('testipocambios_id='.$id);
	$por_fecha=$internas->count('femision="'.$fecha.'"');
	return "ids: ".$por_id."<==> fecha:".$por_fecha;
}
public function getIngresos($id,$fecha)
{
	$ingresos= new tesingresos();
	$por_id=$ingresos->count('testipocambios_id='.$id);
	$por_fecha=$ingresos->count('femision="'.$fecha.'"');
	return "ids: ".$por_id."<==> fecha:".$por_fecha;
}

}
?>