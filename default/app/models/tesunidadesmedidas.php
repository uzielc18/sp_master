<?php
class Tesunidadesmedidas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesdetallesalidasinternas','tesdetallesalidas','tesdetalleingresos','tesdetalleproformas','prodetallepedidos');
		$this->belongs_to('aclempresas');
    }	
}
?>