<?php
Load::models('sc_cajasurdido');
class ScTotalcajas extends ActiveRecord{

    public function initialize() {
        //relaciones
		$this->has_many('sc_urdidos');
		$this->belongs_to('aclempresas','tesproductos');
    }
	
	public function total_urdido($id)
	{
		//echo $id;
		$val=$this->find_by_sql('select IFNULL( SUM( u.totalkilos ) , 0 )  as total,u.tesproductos_id as tesproductos_id,u.colores_id as colores_id,c.lotes as lotes FROM sc_urdidos as u,sc_totalcajas as c WHERE c.id=u.sc_totalcajas_id AND u.sc_totalcajas_id='.$id);
		/*echo 'SELECT sum(d.cantidad) FROM tessalidas AS s, tesdetallesalidas AS d WHERE s.id = d.tessalidas_id
AND s.aclempresas_id =2 AND s.npedido LIKE  "DV%" AND tesproductos_id ='.$val->tesproductos_id.' AND LOCATE( d.lote, "'.$val->lotes.'" )';*/
		$dev=$this->find_by_sql('SELECT IFNULL( SUM( d.cantidad ) , 0 ) as cantidad FROM tessalidas AS s, tesdetallesalidas AS d WHERE s.id = d.tessalidas_id
AND s.aclempresas_id =2 AND s.npedido LIKE  "DV%" AND tesproductos_id ='.$val->tesproductos_id.' AND LOCATE( d.lote, "'.$val->lotes.'" )');
		
		//echo $dev->cantidad.'/* DV*/';
		//echo $val->total.'/*UR*/';
		$t=$val->total+$dev->cantidad;
		
		return $t;
	}

public function getColores($ids)
{
	$colores=explode(',',$ids);
	$coma='';
	$n=0;
	$c='';
	foreach($colores as $item=>$value):
	$color=$this->find_by_sql('SELECT nombre from tescolores WHERE id='.$value);
	if($n!=0){$coma=', ';}
	$c.=$coma.$color->nombre;
	$n++;
	endforeach;
	return $c;
}

public function getCantidad($cajas_id=0)
{
	$cajas= new ScCajasurdido();
	$c=$cajas->find_by_sql("SELECT sum(peso) as total FROM `sc_cajasurdido` WHERE id in(".$cajas_id.")");
	return $c->total; 
	
}


}
?>