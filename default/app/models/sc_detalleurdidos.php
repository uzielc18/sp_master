<?php
class ScDetalleurdidos extends ActiveRecord{

    public function initialize() {
        //relaciones
		//$this->has_many('');
		$this->belongs_to('aclusuarios','aclempresas','sc_urdidos');
    }
	
}
?>