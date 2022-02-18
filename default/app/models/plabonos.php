<?php
class Plabonos extends ActiveRecord {


    public function initialize() {
        //validaciones
		$this->belongs_to('plhaberes');
    }
}
?>