<?php 
class Tesproformaenviadas extends ActiveRecord
{
    public function initialize() {
        //relaciones
		$this->belongs_to('tesdatos','tesproformas');
    }		
}

?>