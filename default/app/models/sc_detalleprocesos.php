<?php
class ScDetalleprocesos extends ActiveRecord {

    public function initialize() {
        //relaciones
		//$this->has_many('sc_detalleprocesos');
		$this->belongs_to('aclusuarios','acldatos','sc_detalleprogramados','sc_procesos','tesproductos','sc_produccion','sc_crollos','tesdatos','promaquinas','sc_calidades');
    }
	/*devuele el total detalle depeendiente de una detalle*/
	public function getTotal($id)
	{
		$sumas=$this->find_by_sql("SELECT sum(cantidad) as n FROM sc_detalleprocesos WHERE sc_detalleprocesos_id=".$id);
		return $sumas->n;
	}
	/*Validar si ya temrino de enviar todo el detalle de procesos*/
	public function detalleprocesos($id){
		$cantidad=0;
		$suma=0;
		$detalle=$this->find_first($id);
		$cantidad=$detalle->cantidad;
		$sumas=$this->find_by_sql("SELECT sum(cantidad) as n FROM sc_detalleprocesos WHERE sc_detalleprocesos_id=".$id);
		$suma=$sumas->n;
		if($cantidad==$suma)
		{
			$detalles=new ScDetalleprocesos();
			$detalles->find_first($id);
			$detalles->estadodetalle='TERMINADO';
			$detalles->fechafin=date("Y-m-d");
			$detalles->save();
			return TRUE;
		}else
		{
			return FALSE;
		}
		}
}
?>