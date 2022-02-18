<?php
class Prodetallemovimientos extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('promovimientos','tesproductos','tesunidadesmedidas','tescolores');
    }
	
}
?>