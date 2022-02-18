<?php
class Proplegadoresenuso extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('aclusuarios','proplegadores','proproduccion');
    }
	/*
	# recibe el tipo de accion. 
	# I = inicio.
	# F = fin.
	# id = id de la produccion
	*/
	public function getIinicioFin($tipo='I',$id)
	{
		$pus=$this->find('conditions: proproduccion_id='.$id);
		foreach($pus as $p):
		$pu=$this->find_first((int)$p->id);
		if($tipo=='I' && empty($p->fechaingreso))$pu->fechaingreso=date('Y-m-d');
		if($tipo=='F' && empty($p->fechasalida))$pu->fechasalida=date('Y-m-d');
		$pu->save();
		endforeach;
		return true;
	}
	
	/*
	# 
	# id = id de la produccion.
	*/
	public function getPlegadorenpiso($id)
	{
		$plegadores=Load::model('proplegadores');
		$pus=$this->find('conditions: proproduccion_id='.$id);
		foreach($pus as $p):
		$pu=$this->find_first((int)$p->id);
		$pl=$plegadores->find_first((int)$p->proplegadores_id);
		$pl->estadoplegador_id=1;
		$pl->save();
		endforeach;
		return true;
	}
	/*$id es el id de la produccion */
	public function getMerma($id)
	{
		$m=$this->find_by_sql("SELECT (p.metros-sum(d.metrosrevisados))as merma FROM prodetalleproduccion as d INNER JOIN proproduccion as p ON p.id=d.proproduccion_id AND p.id=".$id."
WHERE 1=1 GRoUP BY d.proproduccion_id"); 
		return $m->merma;
	}
}
?>