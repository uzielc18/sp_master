<?php
class Tesmonedas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tessalidasinternas','tessalidas','tesingresos','tesvauchers','tesfacturasadelantos');
		$this->belongs_to('aclempresas');
    }

    public function getMonedas(){
    	return $this->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID'));
    }
}
?>