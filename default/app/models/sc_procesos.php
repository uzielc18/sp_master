<?php
class ScProcesos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('sc_detalleprocesos');
		$this->belongs_to('aclusuarios','acldatos','promaquinas');
    }
}
?>