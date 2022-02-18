<?php
class Memos extends ActiveRecord {


    public function initialize() {
        //validaciones
		$this->belongs_to('aclempresas','acldatos','aclusuarios');
	}
}
?>