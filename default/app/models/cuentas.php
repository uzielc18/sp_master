<?php
class Cuentas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('subcuentas');
		$this->validates_presence_of('codigo', 'message: Debe escribir un <b>Codigo</b> para la Cuenta');
	}
	public function before_validation_on_create() {
        $this->validates_uniqueness_of('codigo', 'message: El <b>Codigo</b> ya está siendo utilizado');
    }
}
?>