<?php
class Plhaberes extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('plabonos');
		$this->belong_to('acldatos');
    }
}
?>