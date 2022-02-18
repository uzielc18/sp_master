<?php class Testipoalmacenes extends ActiveRecord
{
	public function initialize() {
        //relaciones
		$this->has_many('teslineaproductos');
    }
}
?>