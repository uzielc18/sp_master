<?php
class Teschequesingresos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tespagosacuentas');
		$this->belongs_to('tesingresos','tesbancos');
    }	
}
?>