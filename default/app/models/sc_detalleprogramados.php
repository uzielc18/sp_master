<?php
class ScDetalleprogramados extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('sc_detalleprocesos');
		$this->belongs_to('aclusuarios','sc_produccion','sc_crollos','tesproductos','aclusuarios','tesdatos','acldatos');
    }
	/*devuelve los detalles con el total del detalle enviado a a pocesos*/
	public function getDetalles()
	{
		return $this->find_all_by_sql("SELECT dp.*,sum(dpr.cantidad) as n FROM sc_detalleprogramados as dp LEFT JOIN sc_detalleprocesos as dpr ON dp.id=dpr.sc_detalleprogramados_id AND ISNULL(dpr.sc_detalleprocesos_id) WHERE dp.sc_produccion_id=".Session::get('PRODUCCION_ID')." GROUP BY dp.id");
	}
	/*el total del detalle procesos por el detalle programado*/
	public function TotalProcesos($id)
	{
		
		$det=$this->find_by_sql("SELECT dp.*,sum(dpr.cantidad) as n FROM sc_detalleprogramados as dp LEFT JOIN sc_detalleprocesos as dpr ON dp.id=dpr.sc_detalleprogramados_id WHERE ISNULL(dpr.sc_detalleprocesos_id) AND dp.id=".$id.'');
		return $det->n;
	}
/*Validar si ya temrino de enviar todo el detalle de programacion 
	Recibe el id del detalle programado y compara con el total de detalles en proceso si el total es = al cantidad entonces se cambia al estado de TERMINADO	*/
	public function detalleprogramdo($id)
	{
		$cantidad=0;
		$suma=0;
		$detalle=$this->find_first($id);
		$cantidad=$detalle->cantidad;
		$sumas=$this->find_by_sql("SELECT count(id) as n FROM sc_detalleprocesos WHERE sc_detalleprogramados_id=".$id);
		$suma=$sumas->n;
		if($cantidad==$suma)
		{
			$detalles= new ScDetalleprogramados();
			$detalles->find_first($id);
			$detalles->estadodetalle='TERMINADO';
			$detalles->save();
			return TRUE;
		}else
		{
			return FALSE;
		}
	}
}
?>