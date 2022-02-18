<?php
class Tescontactos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tesdatos');
    }	
}
?>