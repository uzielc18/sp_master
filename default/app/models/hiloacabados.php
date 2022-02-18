<?php
class Hiloacabados extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesproductos');
		$this->belongs_to('testipoproductos');
    }	
}
?>
