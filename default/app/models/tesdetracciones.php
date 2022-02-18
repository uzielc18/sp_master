<?php
class Tesdetracciones extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tesingresos');
    }
	public function getDetracciones($anio,$mes_activo)
	{
		
		return $this->find_all_by_sql("SELECT d.razonsocial, doc.abr, i.femision, i.totalconigv, dr . * 
FROM tesdocumentos AS doc, tesdatos AS d, tesdetracciones AS dr
INNER JOIN tesingresos AS i ON dr.tesingresos_id = i.id
WHERE i.tesdatos_id = d.id AND dr.aclempresas_id=".Session::get('EMPRESAS_ID')."
AND doc.id = i.tesdocumentos_id AND DATE_FORMAT(i.femision,'%Y-%m')='".$anio.'-'.$mes_activo."' ORDER BY d.razonsocial,i.femision ASC");
	}
	
	
}
?>