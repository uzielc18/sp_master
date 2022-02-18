<?php
Load::models('testipoproductos');
class Tesdetallesalidas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tesproductos','tessalidas','tesunidadesmedidas','tescolores','tescajas','prorollos','sc_crollos');
    }
	
	public function getTotales($id)
	{
		$c=$this->sum('cantidad','conditions: tessalidas_id='.$id);
		$e=$this->sum('empaque','conditions: tessalidas_id='.$id);
		$b=$this->sum('bobinas','conditions: tessalidas_id='.$id);
		echo $c."-".$e."-".$b.'***';
		return $c."-".$e."-".$b;
	}
public function getPrecios($conditions='',$order='')
	{
		$group =" GROUP BY numero,d.tescolores_id";
		//$group=" GROUP BY d.tesproductos_id,s.tesdatos_id,d.preciounitario";
		$sql="SELECT s.id as id,CONCAT_WS('-',s.serie,s.numero) as numero, s.tesdatos_id as datos_id,d.tesproductos_id as productos_id,p.nombre as nombre,IFNULL(p.codigo,p.codigo_ant) as codigo,c.razonsocial as razonsocial, s.femision as fecha,s.tesmonedas_id as tesmonedas_id,d.preciounitario as preciounitario,d.tescolores_id as tescolores_id,sum(d.cantidad) as cantidad FROM tesproductos as p,tesdatos as c, tessalidas as s INNER JOIN tesdetallesalidas as d ON s.id=d.tessalidas_id AND s.tesdocumentos_id=7
WHERE c.id=s.tesdatos_id AND p.id=d.tesproductos_id".$conditions.$group."
UNION ALL
SELECT s.id as id,CONCAT_WS('-',s.serie,s.numero) as numero, s.tesdatos_id as datos_id,d.tesproductos_id as productos_id,p.nombre as nombre,p.codigo_ant as codigo,c.razonsocial as razonsocial, s.fecha_at as fecha,s.tesmonedas_id as tesmonedas_id,d.preciounitario as preciounitario,d.tescolores_id as tescolores_id,sum(d.cantidad) as cantidad  FROM tesproductos as p,tesdatos as c, tessalidasinternas as s INNER JOIN tesdetallesalidasinternas as d ON s.id=d.tessalidasinternas_id
WHERE c.id=s.tesdatos_id AND p.id=d.tesproductos_id".$conditions.$group."
".$order;
		/*echo $sql;*/
		return $this->find_all_by_sql($sql);
	}
public function getCantidades($conditions='',$f_i,$f_f)
	{
		$group ="";
		//$group=" GROUP BY d.tesproductos_id,s.tesdatos_id,d.preciounitario";
		$sql="
		SELECT '".$f_i."' as a,'".$f_f."' as b,'R',sum(d.cantidad) as metros,sum(d.pesoneto) as peso,group_concat(CONCAT_WS('-',s.serie,s.numero)) as numero, s.tesdatos_id as datos_id,d.tesproductos_id as productos_id,p.nombre as nombre,IFNULL(p.codigo,p.codigo_ant) as codigo,c.razonsocial as razonsocial,d.tescolores_id as tescolores_id 
FROM tesproductos as p,tesdatos as c, tessalidas as s 
INNER JOIN tesdetallesalidas as d ON s.id=d.tessalidas_id AND s.tesdocumentos_id=15 AND s.estadosalida!='ANULADO'
WHERE 
c.id=s.tesdatos_id AND p.id=d.tesproductos_id".$conditions." AND s.femision BETWEEN '".$f_i."' AND '".$f_f."'  GROUP BY d.tesproductos_id 
UNION ALL 
SELECT '".$f_i."' as a,'".$f_f."' as b,'I',sum(d.cantidad) as metros,0 as peso,group_concat(CONCAT_WS('-',s.serie,s.numero)) as numero, s.tesdatos_id as datos_id,d.tesproductos_id as productos_id,p.nombre as nombre,p.codigo_ant as codigo,c.razonsocial as razonsocial,d.tescolores_id as tescolores_id 
FROM tesproductos as p,tesdatos as c, tessalidasinternas as s 
INNER JOIN tesdetallesalidasinternas as d ON s.id=d.tessalidasinternas_id AND s.estadosalida!='ANULADO'
WHERE c.id=s.tesdatos_id AND p.id=d.tesproductos_id".$conditions." AND s.femision BETWEEN '".$f_i."' AND '".$f_f."' GROUP BY d.tesproductos_id";
		//echo $sql;
		return $this->find_all_by_sql($sql);
	}
	
/*Liusta de Hilo para urdir*/	
	public function getUrdidos($linea_id=3)
	{
		return $this->find_all_by_sql("SELECT td.* FROM tesdetallesalidas as td, tesproductos as p, testipoproductos as tp,tessalidas as i WHERE i.hilodestino_id=1 AND i.id=td.tessalidas_id AND td.tesproductos_id=p.id AND p.testipoproductos_id=tp.id AND td.tescolores_id!=0 AND (".$this->obtener_tipo_productos($linea_id).") group by td.tesproductos_id,td.lote ORDER BY tesproductos_id,tescolores_id ASC");
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
	/* Santa Carmela */
public function getDetalles($id){
		$sql="SELECT r.codigo as codigo,r.numero as numero,d.* FROM tesdetallesalidas as d, sc_crollos as r 
		WHERE d.sc_crollos_id=r.id AND d.tessalidas_id=".$id."
		union distinct
		SELECT '' as codigo,'' as numero, d.* FROM tesdetallesalidas as d WHERE d.tessalidas_id=".$id;
		//echo $sql;
		return $this->find_all_by_sql($sql);
		}
}
?>