<?php
class Tesordendecompras extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesdetalleordenes','prodetalleproduccion','prorollos');
		$this->belongs_to('aclusuarios','aclempresas','tesdatos','tesmonedas');
    }
	public function before_validation_on_create(){
        //$this->validates_uniqueness_of('codigo','message: El <b>Numero</b> ya está siendo utilizado');
    }
	public function generarNumero()
	{
		$maximo = $this->maximum("codigo", "conditions: origenorden='interno' AND aclempresas_id =".Session::get('EMPRESAS_ID'));
		if(empty($maximo))
		{
			$maximo=1;
		}else{
			$maximo=$maximo+1;
		}
		switch($maximo)
		{
			case $maximo<10: $maximo="000000".$maximo; break;
			case $maximo<100: $maximo="00000".$maximo; break;
			case $maximo<1000: $maximo="0000".$maximo; break;
			case $maximo<10000: $maximo="000".$maximo; break;
			case $maximo<100000: $maximo="00".$maximo; break;
			case $maximo<1000000: $maximo="0".$maximo; break;
			default: $maximo=$maximo;
		}
		return $maximo;
	}
	public function generarNumero_interno()
	{
		$maximo = $this->maximum("numero_interno", "conditions: origenorden='externo' AND aclempresas_id =".Session::get('EMPRESAS_ID'));
		if(empty($maximo))
		{
			$maximo=1;
		}else{
			$maximo=$maximo+1;
		}
		return $maximo;
	}
	
 public function detalles_entregados($cond='',$group='') 
	{
/*echo "SELECT s.id as id,CONCAT_WS('-',s.serie,s.numero) as factura,DATE_FORMAT(s.femision,'%d/%m/%y') as fecha ,SUM(d.cantidad) as cantidad,SUM( d.importe ) as importe,d.tesproductos_id as tesproductos_id ,d.tescolores_id as tescolores_id FROM tesdetallesalidas AS d, tessalidas as s, tesdetalleordenes as od,tesordendecompras as o WHERE o.fecha <= s.femision AND s.id=d.tessalidas_id AND s.tesdocumentos_id=7 AND od.tesordendecompras_id=o.id AND o.tesdatos_id=s.tesdatos_id AND od.tesproductos_id = d.tesproductos_id AND od.tescolores_id = d.tescolores_id".$cond.$group;*/
		return $this->find_all_by_sql("SELECT s.id as id,s.totalconigv as totalconigv,s.igv as igv,CONCAT_WS('-',s.serie,s.numero) as factura,DATE_FORMAT(s.femision,'%d/%m/%y') as fecha ,SUM(d.cantidad) as cantidad,SUM( d.importe ) as importe,d.tesproductos_id as tesproductos_id ,d.tescolores_id as tescolores_id FROM tesdetallesalidas AS d, tessalidas as s, tesdetalleordenes as od,tesordendecompras as o WHERE s.estadosalida!='ANULADO' AND o.fecha <= s.femision AND s.id=d.tessalidas_id AND s.tesdocumentos_id=7 AND od.tesordendecompras_id=o.id AND o.tesdatos_id=s.tesdatos_id AND od.tesproductos_id = d.tesproductos_id AND od.tescolores_id = d.tescolores_id".$cond.$group);
//return $this->find_all_by_sql("SELECT s.id as id,CONCAT_WS('-',s.serie,s.numero) as factura,DATE_FORMAT(s.femision,'%d/%m/%y') as fecha ,SUM(d.cantidad) as cantidad,SUM( d.importe ) as importe,d.tesproductos_id as tesproductos_id ,d.tescolores_id as tescolores_id FROM tesdetallesalidas AS d, tessalidas as s, tesdetalleordenes as od,tesordendecompras as o,prorollos as r WHERE o.id=r.tesordendecompras_id AND r.id=d.prorollos_id AND o.fecha <= s.femision AND s.id=d.tessalidas_id AND s.tesdocumentos_id=7 AND od.tesordendecompras_id=o.id AND o.tesdatos_id=s.tesdatos_id AND od.tesproductos_id = d.tesproductos_id AND od.tescolores_id = r.tescolores_id".$cond.$group);
	}
	
	public function getOrdenes($q)
	{
		return $this->find_all_by_sql("SELECT o.id AS id, o.numero_interno AS numero_interno, o.codigo AS codigo, d.razonsocial AS razonsocial, d.codigo_ant AS codigo, d.ruc AS ruc FROM tesordendecompras AS o, tesdatos AS d WHERE d.id = o.tesdatos_id AND o.origenorden ='externo' AND o.aclempresas_id=".Session::get('EMPRESAS_ID')." AND CONCAT_WS(' ', o.numero_interno, d.razonsocial, d.codigo_ant, d.ruc,o.codigo ) LIKE  '%".$q."%'");
	}
}
?>