<?php
Load::models('tesdetalleingresos','tesdetallesalidas');
class Promovimientos extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('proplegadores','tesingresos','tessalidas');
    }
	/*
	#
	# detalle del ingreso en el que esta el plegador
	# @productos_id: el id del producto;
	# @movimientos_id: es el movimiento ya se ingreso o salida
	#
	*/
	public function getDetalle($productos_id=0,$movimientos_id=0,$campo='')
	{
		if($campo=='tesingresos_id')
		{
			$detalles= new Tesdetalleingresos();
			return $detalles->find_first('conditions: tesproductos_id='.$productos_id.' AND '.$campo.'='.$movimientos_id);			
		}else{
			$detalles= new Tesdetallesalidas();
			return $detalles->find_first('conditions: tesproductos_id='.$productos_id.' AND '.$campo.'='.$movimientos_id);
		}
	}
}
?>