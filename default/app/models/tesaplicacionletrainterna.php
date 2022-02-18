<?php
class Tesaplicacionletrainterna extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tesletrassalidasinternas');
		$this->has_many('tessalidasinternas');
    }	
}
?>