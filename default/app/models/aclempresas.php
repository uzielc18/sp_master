<?php
class Aclempresas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('plareas','aclusuarios','acldatos','tesunidadesmedidas','testipodatos','testipocambios','tessalidasinternas','tessalidas','tesproformas','tespropductos','tesordendecompras','teslineaproductos','tesmonedas','tesbancos','plareas','plcargos','proturnos');
		$this->validates_presence_of('nombre', 'message: Debe escribir un <b>Nombre</b>');
		$this->validates_presence_of('razonsocial', 'message: Debe escribir la <b>Razon Social de la Empresa</b>');
		$this->validates_numericality_of('ruc', 'message:Este campo acepta solo numeros');
		//$this->validates_presence_of('ruc', 'message: Debe escribir <b> el Ruc de la Empresa</b>');
		$this->validates_presence_of('telefonos', 'message: Debe escribir el Telefono de la Empresa</b>');
    }
	public function before_validation_on_create() {
        $this->validates_uniqueness_of('ruc', 'message: El <b>Ruc</b> ya está siendo utilizado');
    }
}
?>