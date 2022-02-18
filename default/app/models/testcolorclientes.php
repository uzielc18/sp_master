<?php
class Testcolorclientes extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tescolores','tesdatos');
    }	
}
?>