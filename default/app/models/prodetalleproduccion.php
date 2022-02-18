<?php
class Prodetalleproduccion extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('protrama','prorollos','proplegadoresenuso','promerma');
		$this->belongs_to('tesproductos','proproduccion','tesordendecompras','tescolores');
    }
	public function getValidarGuia($id=0)/*id de la guia*/
	{
		/*/echo "SELECT count(r.id) as c FROM `prorollos` as r, prodetalleproduccion as pp, proproduccion as p WHERE r.prodetalleproduccion_id=pp.id AND p.id=pp.proproduccion_id AND p.guia_id=".$id." AND ISNULL(r.metros) AND ISNULL( r.prorollos_id ) ";*/
		$c=$this->find_by_sql("SELECT count(r.id) as c FROM `prorollos` as r, prodetalleproduccion as pp, proproduccion as p WHERE r.prodetalleproduccion_id=pp.id AND p.id=pp.proproduccion_id AND p.guia_id=".$id." AND ISNULL(r.metros) AND ISNULL( r.prorollos_id )");
		return $c->c;
	}
	/*
	Id de la produccion
	*/
	public function getOrden($id)
	{
		$a=$this->find_all_by_sql("SELECT IFNULL(max(orden),0)+1 as orden FROM `prodetalleproduccion` WHERE proproduccion_id=".$id);
		return $a->orden;
	}
	public function getRollos()
	{
		return $this->find_all_by_sql("SELECT * FROM prorollos WHERE prodetalleproduccion_id=".$this->id.' AND ISNULL(prorollos_id)');
	}
}
?>
