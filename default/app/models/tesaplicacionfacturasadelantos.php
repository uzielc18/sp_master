<?php
class Tesaplicacionfacturasadelantos extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('tesfacturasadelantos');
    }
	
	
}
?>