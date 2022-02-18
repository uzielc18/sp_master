<?php
class Proubicacionmaquina extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('prorepuestosuso');
		$this->belongs_to('promaquinas');
    }
}
?>