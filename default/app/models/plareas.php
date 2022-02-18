<?php
class Plareas extends ActiveRecord {


    public function initialize() {
        //validaciones
		$this->belongs_to('aclempresas');
		$this->has_many('pronotapedidos');
	}
}
?>