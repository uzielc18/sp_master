<?php
class Eventostipo extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('eventos');
		//$this->has_many('tesletrasingresos');
		//$this->belongs_to('aclusuarios');
    }	
}
?>
