<?php
Load::models('tesdetallevauchers');
class Sc_detalleprogramado extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesdetallevauchers');
		$this->belongs_to('aclusuarios','sc_produccion','sc_crollos');
    }
}
?>