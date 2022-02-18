<?php
class Promerma extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('prodetalleproduccion');
    }
}
?>