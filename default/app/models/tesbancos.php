<?php
class Tesbancos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tescuentascorrientes');
		$this->belongs_to('aclempresas');
    }
    public function getBancos(){
    	return $this->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID'));
    }	
}
?>