<?php
class Protransportes extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('prodetalletransportes');
		$this->belongs_to('tesproductos');
    }
}
?>