<?php
class Testipodatos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesdatos');
		$this->belongs_to('aclempresas');
    }	
}
?>