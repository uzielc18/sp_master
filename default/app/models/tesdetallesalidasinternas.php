<?php
Load::models('testipoproductos');
class Tesdetallesalidasinternas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tesproductos','tessalidasinternas','tesunidadesmedidas','tescajas','tescolores','prorollos','sc_crollos');
    }	
	public function getTotales($id)
	{
		$c=$this->sum('cantidad','conditions: tessalidasinternas_id='.$id);
		$e=$this->sum('empaque','conditions: tessalidasinternas_id='.$id);
		$b=$this->sum('bobinas','conditions: tessalidasinternas_id='.$id);
		return $c."-".$e."-".$b;
	}
/*Liusta de Hilo para urdir*/
public function getUrdidos($linea_id=3)
	{
		return $this->find_all_by_sql("SELECT td.* FROM tesdetallesalidasinternas as td, tesproductos as p, testipoproductos as tp,tessalidasinternas as i WHERE i.hilodestino_id=1 AND i.id=td.tessalidasinternas_id AND td.tesproductos_id=p.id AND p.testipoproductos_id=tp.id AND td.tescolores_id!=0  AND (".$this->obtener_tipo_productos($linea_id).") group by td.tesproductos_id,td.lote ORDER BY tesproductos_id,tescolores_id ASC");
	}
	protected function obtener_tipo_productos($id)
	{
        $obj = new Testipoproductos();
		$tipos=$obj->find('conditions: estado=1 AND activo=1 AND teslineaproductos_id='.$id);
        $tipos_pro = array();
            foreach ($tipos as $e) {
                $tipos_pro[] = "tp.id = '$e->id'";
            }
        return join(' OR ', $tipos_pro);
    }
}
?>