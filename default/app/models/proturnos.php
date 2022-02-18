<?php
class Proturnos extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('proeficiencias');
		$this->belongs_to('aclusuarios','aclempresas');
    }
}
?>