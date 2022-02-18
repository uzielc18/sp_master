<?php
class Tesexportaciones extends ActiveRecord {

public function initialize()
{
	//relaciones
		$this->belongs_to('tessalidas');
}

}
?>