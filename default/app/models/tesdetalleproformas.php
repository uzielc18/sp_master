<?php
class Tesdetalleproformas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$debug=true;
		$this->belongs_to('tesproductos','tesproformas','tesunidadesmedidas');
    }	
}
?>