<?php
class ScCalidades extends ActiveRecord{

    public function initialize() {
        //relaciones
		$this->has_many('prodetallepedidos','tesdetallesalidas','tesdetallesalidasinternas','sc_detalleprocesos');
		$this->belongs_to('aclusuarios','aclempresas');
    }
	
}
?>