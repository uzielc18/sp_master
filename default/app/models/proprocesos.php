<?php
class Proprocesos extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('proobservaciones','prodetalleprocesos','prodetalleacabados');
		$this->belongs_to('aclusuarios','proacabados','tescolores','tessalidas');
    }
}
?>