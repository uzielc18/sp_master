<?php
class Tespagosacuentas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('teschequesingresos');
    }	
}
?>