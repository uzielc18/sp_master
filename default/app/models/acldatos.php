<?php
class Acldatos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('plhaberes','prodetallepedidos','proeficiencias');
        $this->belongs_to('aclusuarios','plareas','aclempresas','placargos');
		$this->has_one('protransportistas');
		$this->validates_presence_of('nombre', 'message: Debe escribir un <b>Nombre</b> para el Trabajador');
        $this->validates_presence_of('apaterno', 'message: Debe escribir el <b>Primir Apellido</b>');
        $this->validates_presence_of('amaterno', 'message: Debe escribir el <b>Segundo Apellido</b>');
        /*$this->validates_presence_of('dni', 'message: Debe ingresar su <b> DNI </b>');*/
		$this->validates_presence_of('aclempresas_id', 'message: Debe seleccionar una <b>Empresa</b>');
        $this->validates_presence_of('plareas_id', 'message: Debe seleccionar una <b>Area</b>');
		$this->validates_presence_of('placargos_id', 'message: Debe seleccionar un <b>Cargo</b>');
    }


    public function before_validation_on_create() {
        $this->validates_uniqueness_of('dni', 'message: El <b>DNI</b> ya está siendo utilizado');
    }
	/*public function before_validation_on_update() {
        $this->validates_uniqueness_of('dni', 'message: El <b>DNI</b> ya está siendo utilizado');
    }*/
	public function personal_paginado($page=1,$per_page=15,$empresa)
	{
		return $this->paginate('page: '.$page,'per_page: '.$per_page,'conditions: estado=1 and aclempresas_id='.$empresa,'order: nombre asc');
	}
}
?>