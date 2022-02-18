<?php
class Tesreportes extends ActiveRecord {

    public function initialize() {
        //relaciones
		//$this->has_many('tesdetalleproformas');
		$this->belongs_to('aclusuarios');
    }
	public function before_validation_on_create() {
        //$this->validates_uniqueness_of('numero','message: El <b>Numero</b> ya está siendo utilizado');
    }

}
?>