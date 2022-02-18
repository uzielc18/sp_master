<?php
class Tesdatosdirecciones extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('tesdatos');
		$this->has_many('tessalidas','tessalidasinternas');
    }
	/*public function before_validation_on_create() {
        $this->validates_uniqueness_of('direccion', array('conditions: tesdatos_id=$this->tesdatos_id','message: Esta direccion ya existe'));
    }*/
	/*public function getCajasTrama()
	{
		$this->find_all_by_sql();

	}*/
	public function getDireccion($id)
	{
		if(!empty($id)){
		$d=$this->find_by_sql("select * from tesdatosdirecciones WHERE id=".$id);
		return "$d->direccion $d->distrito - $d->provincia - $d->departamento";
		}else
		{
			return "";
		}
		
	}
	public function getDirecciones($id){
		$sql="SELECT d.*,r.tesdatos_id as tesdatos_id, d.razonsocial as razonsocial,r.direccion as direccion,r.id as id_direccion FROM tesdatosdirecciones as r INNER JOIN tesdatos as d ON d.id=r.tesdatos_id AND d.aclempresas_id=".Session::get('EMPRESAS_ID')." AND d.testipodatos_id=".$id;
		return $this->find_all_by_sql($sql);
		}
}
?>