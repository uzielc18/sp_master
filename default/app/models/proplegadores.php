<?php
class Proplegadores extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('proplegadoresenuso','promovimientos');
		$this->belongs_to('tesproductos','estadoplegador','protipoplegadores');
    }
	public function before_validation_on_create() {
        $this->validates_uniqueness_of('numero', 'message: Ya hay un Plegador con el <b>mismo Número</b>');
    }
	public function plconmovimientos()
	{
		return $this->find_all_by_sql('SELECT proplegadores . * , COUNT( promovimientos.id ) AS n
										FROM proplegadores
										INNER JOIN promovimientos ON promovimientos.proplegadores_id = proplegadores.id
										WHERE proplegadores.estado =  "1"
										AND proplegadores.activo =  "1"
										GROUP BY proplegadores.id
										ORDER BY promovimientos.id, n DESC ');
	}
	public function plreporte()
	{
		return $this->find_all_by_sql('SELECT (select glosa from tesingresos WHERE id=max(d.tesingresos_id)) as glosa,p.*,da.razonsocial as razonsocial FROM proplegadores as p, tesdetalleingresos as d,tesingresos as i,estadoplegador as e,tesdatos as da WHERE i.tesdatos_id=da.id AND p.tesproductos_id=d.tesproductos_id AND i.id=d.tesingresos_id AND i.tesdocumentos_id=15 AND e.id=p.estadoplegador_id group by p.id order by p.numero');
	}
	public function getMaquinas()
	{
		return $this->find_all_by_sql("SELECT pm.* FROM proproduccion as p, promaquinas as pm Where pm.id=p.promaquinas_id group by p.promaquinas_id");
	}
	public function getSeguimientos()
	{
		return $this->find_all_by_sql("SELECT (select glosa from tesingresos WHERE id=max(d.tesingresos_id)) as glosa,p.id as id,e.nombre as estado,p.estadoplegador_id as estadoplegador_id,max(d.tesingresos_id) as guia_id,p.numero as numero,p.protipoplegadores_id as protipoplegadores_id,(select ifnull(promaquinas_id,0) from proproduccion WHERE guia_id=max(d.tesingresos_id) AND estadoproduccion='PRODUCCION' limit 1) as maquina_id
,(select concat_ws('-',id,titulo,metros) from proproduccion WHERE guia_id=max(d.tesingresos_id) AND estadoproduccion='PRODUCCION' limit 1) as detalle,
(select ifnull(SUM(prodetalleproduccion.metrosrevisados),0) FROM proproduccion, prodetalleproduccion
WHERE prodetalleproduccion.proproduccion_id = proproduccion.id
AND proproduccion.estadoproduccion =  'PRODUCCION'
AND proproduccion.guia_id=max(d.tesingresos_id) AND estadoproduccion='PRODUCCION' limit 1) as total
FROM proplegadores as p, tesdetalleingresos as d,tesingresos as i,estadoplegador as e,tesdatos as da WHERE i.tesdatos_id=da.id AND p.tesproductos_id=d.tesproductos_id AND i.id=d.tesingresos_id AND i.tesdocumentos_id=15 AND e.id=p.estadoplegador_id group by p.id order by p.numero");
	}
}
?>