<?php
class Tescuentascorrientes extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tesbancos','aclempresas');
		$this->has_many('tessalidas');
    }	
}
?>