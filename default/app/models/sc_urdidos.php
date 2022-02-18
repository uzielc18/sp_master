<?php
class ScUrdidos extends ActiveRecord{

    public function initialize() {
        //relaciones
		$this->has_many('sc_detalleurdidos');
		$this->belongs_to('aclusuarios','aclempresas','sc_totalcajas','tesdatos','tesproductos');
    }
	
	
	
}
?>