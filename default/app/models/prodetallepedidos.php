<?php
Load::models('protrama','prodetalleproduccion','pronotapedidos');
class Prodetallepedidos extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('pronotapedidos','tesproductos','tesunidadesmedidas','tescajas','tescolores','acldatos');
		$this->has_many('procajastrama');
		$this->has_one('prorepuestouso','protrama');
    }
	/*
	recibel id del detalle de id de pedidos
	*/
	public function getRollotrama($id=0)
	{
		$tramas=new Protrama();
		if($tramas->exists('prodetallepedidos_id='.$id)){
	 //echo $id;
		$trama=$tramas->find_first('conditions: prodetallepedidos_id='.$id);
		$detalles= new Prodetalleproduccion();
		return $detalle=$detalles->find_first((int)$trama->prodetalleproduccion_id);
		}else
		{
		 return FALSE;	
		}
	}
	
	public function getPrecio_ingreso($producto_id,$colores_id,$lote)
	{
		$precio=Load::model('tesdetalleingresos')->find_first('columns: preciounitario','conditions: tesproductos_id='.$producto_id.' AND tescolores_id='.$colores_id.' AND lote='.$lote,'join: INNER JOIN tesingresos as i ON i.id=tesdetalleingresos.tesingresos_id AND i.tesdocumentos_id=7');
		if(!empty($precio->preciounitario)){
			return $precio->preciounitario;
		}else
		{
			$prod=Load::model('tesproductos')->find_first('columns: precio','conditions: id='.(int)$producto_id);
			return $prod->precio;
		}		
	}
	/*MODIFICAR LA FORMA EN LA QUE SE ENTREGA PEDIDOS*/
	public function getLotes($id,$color)
	{
		$obj= Load::model('tesdetalleingresos');
		$campos='tesdetalleingresos.'.join(',tesdetalleingresos.',$obj->fields);
		return $results = $obj->find('columns: '.$campos,'conditions: tesdetalleingresos.tescolores_id='.$color.' AND tesdetalleingresos.tesproductos_id='.$id.'','join: INNER JOIN tesingresos ON tesingresos.id=tesdetalleingresos.tesingresos_id AND (tesingresos.tesdocumentos_id=15 or tesingresos.tesdocumentos_id=27)','group: tesdetalleingresos.lote');
	}
	
	public function getEntrega($id)
	{
		return $this->find_all_by_sql("SELECT c.numerodecaja as numerodecaja, c.numeroant as numeroant,t.conos as conos, t.peso as peso,t.lote as lote, d.* FROM procajastrama as t , tescajas as c, prodetallepedidos as d WHERE d.id=t.prodetallepedidos_id AND c.id=t.tescajas_id AND d.pronotapedidos_id=".$id);
	}
	
	public function getPedidos($origen='Produccion',$ids='')
	{
		$seleccionados='';
		if($ids!='')
		{
			$items=explode(',',$ids);
			$seleccionados=' AND (n.id='.join(' OR n.id=',$items).')';
			//$this->find_by_sql("UPDATE  pronotapedidos SET  imprimir='1' WHERE id IN (".$ids.")");
			$pedidos=new Pronotapedidos();
			$pedidos->update_all("imprimir='1'", " id IN (".$ids.")");
			//echo $ids;
			/*foreach($items as $item=>$value):
			
			endforeach;*/
			
		}
		$sql="SELECT d.*,m.numero,pt.nombre as tela, n.codigo as codigo FROM prodetallepedidos as d, pronotapedidos as n , promaquinas as m, tesproductos as pt WHERE d.pronotapedidos_id=n.id AND n.estadonota='Pendiente' AND n.origen='".$origen."' AND m.id=d.promaquinas_id AND d.tesproductos_id=pt.id".$seleccionados;
		//echo $sql;
		return $this->find_all_by_sql($sql);
	}
	
	public function getDetallePendientes()
	{
		$sql="SELECT n.fecha_at as fecha_creacion,n.fecha as fecha_entrega,n.referencia as referencia,n.estadonota,n.id as nid,n.origen,n.tipo, d.* FROM prodetallepedidos as d INNER JOIN pronotapedidos as n ON n.id=d.pronotapedidos_id AND n.estadonota='Pendiente' AND n.origen='Produccion' AND n.tipo='salida'
ORDER BY d.tesproductos_id,n.referencia,d.tescolores_id ASC";
		return $this->find_all_by_sql($sql);
	}
	
}
?>