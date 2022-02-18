<?php
class Tesdetalleordenes extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tesordendecompras','tesproductos','tescolores','tesunidadesmedidas');
    }	
}
?>