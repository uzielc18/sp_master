<?php
class Tescondicionespagos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tessalidas');
    }
	
}
?>