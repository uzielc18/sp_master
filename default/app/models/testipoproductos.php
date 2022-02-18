<?php
class Testipoproductos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesproductos','hilofibras','hilosistema','hiloacabados');
		$this->belongs_to('teslineaproductos');
    }	
}
?>