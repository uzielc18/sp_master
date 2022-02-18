<?php
class Teslineaproductos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('testipoproductos');
		$this->belongs_to('aclempresas','testipoalmacenes');
    }	
}
?>