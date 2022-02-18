<?php
class Protrama extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('aclusuarios','prodetalleproduccion','tesproductos','tescolores','prodetallepedidos');
    }
}
?>