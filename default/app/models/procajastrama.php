<?php
class Procajastrama extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('prodetallepedidos','tescajas');
    }
	
	/*public function getCajasTrama()
	{
		$this->find_all_by_sql();

	}*/
}
?>