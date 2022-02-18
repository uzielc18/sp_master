<?php
class Voucherformadepagos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesvauchers');
		$this->has_many('tesletrasingresos');
		//$this->belongs_to('aclusuarios','testipocambios','tesmonedas');
    }	
}
?>
