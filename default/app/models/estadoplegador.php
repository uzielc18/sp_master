<?php
class Estadoplegador extends ActiveRecord {
	
	public function initialize() {
        //validaciones
		$this->has_many('proplegadores');
    }
}
?>