<?php
class Teschequessalidas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tessalidas','tesbancos');
    }	
}
?>