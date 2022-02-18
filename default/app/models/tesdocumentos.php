<?php
class Tesdocumentos extends ActiveRecord {

    public function initialize() {
        //relaciones
		//$this->has_many('tessalidasinternas','tessalidas','tesingresos');
		$this->belongs_to('aclempresas');
    }	
}
?>