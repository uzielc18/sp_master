<?php
class Prodetalleprocesos extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('proprocesos','prorollos');
    }
}
?>