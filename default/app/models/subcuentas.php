<?php
class Subcuentas extends ActiveRecord {
	public function initialize() {
        //validaciones
		$this->belongs_to('cuentas');
		$this->validates_presence_of('codigon', 'message: Debe escribir un <b>Codigo</b> para la Sub Cuenta');$this->validates_presence_of('descripcion', 'message: Debe escribir una <b>descripcion</b> para la Sub Cuenta');
	}
	public function before_validation_on_create() {
        $this->validates_uniqueness_of('codigo', 'message: El <b>Codigo</b> ya está siendo utilizado');
    }
	/*
	#cuenta_id Es el id de la cuenta 
	#$subcuenta_id Es el id de la sub cuenta a ver
	*/
	public function obtenerSubcuentas($cuenta_id=0, $subcuenta_id=0)
	{
		return $this->find('conditions: cuentas_id='.$cuenta_id.' and subcuentas_id="'.$subcuenta_id.'"');
	}
}
?>