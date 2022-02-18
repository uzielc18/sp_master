<?php
class Protransportistas extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('prodetalletransportes');
		$this->belongs_to('acldatos');
    }
}
?>