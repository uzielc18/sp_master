<?php
class Placargos extends ActiveRecord {


    public function initialize() {
        //validaciones
		$this->has_many('acldatos');
		$this->belongs_to('aclempresas');
		
	}
}
?>