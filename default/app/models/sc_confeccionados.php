<?php
Load::models('tesdetallevauchers');
class Sc_confeccionados extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesdetallevauchers');
		$this->belongs_to('aclusuarios','cs_detalleprogramado','tesproductos','sc_produccion','sc_crollos');
			
	}
}
?>