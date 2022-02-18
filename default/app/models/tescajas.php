<?php
class Tescajas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tesdetalleingresos');
		$this->has_many('procajastrama','tesdetallesalidas','tesdetallesalidasinternas','sc_cajasurdido','prodetallepedidos');
    }
	public function generarNumero()
	{
		$maximo = $this->maximum("numerodecaja", "conditions: aclempresas_id =".Session::get('EMPRESAS_ID'));
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
	
	public function TcajasTbobinasTpneto($id)
	{
		#//total de cajas
		$total=$this->count('estado=1 AND tesdetalleingresos_id='.$id);
		#total Bobinas
		$conos=$this->sum('conos', 'conditions: estado=1 AND tesdetalleingresos_id='.$id);
		#total Peso Neto
		$PN=$this->sum('pesoneto', 'conditions: estado=1 AND tesdetalleingresos_id='.$id);
		return $total.'-'.$conos.'-'.number_format($PN,2,'.','');
	}
	/*
	busqueda de cajas para enviar a urdido o trama
	*/
	public function buscarcaja($producto_id,$color_id,$lote=0)
	{
		$cond_lote='';
		if($lote!=0)$cond_lote=" AND I.lote='".$lote."'";
		$campos="tescajas.".join(',tescajas.',$this->fields);
		$join='INNER JOIN tesdetalleingresos as I ON I.id=tescajas.tesdetalleingresos_id';
		$condiciones= "conditions: tescajas.enalmacen=1 AND tescajas.estado=1 AND tescajas.aclempresas_id=".Session::get('EMPRESAS_ID')." AND I.tesproductos_id='".$producto_id."' AND I.tescolores_id='".$color_id."'".$cond_lote;
		/*echo $campos.' '.$join.' '.$condiciones;*/
		return $this->find($condiciones, "join: $join", "columns: $campos", 'order: tescajas.numerodecaja');
	}
	protected function detalleproduccion($producto_id,$color_id,$lote)
	{
		$D=Load::model('tesdetalleingresos');
		$detalles=$D->find('conditions: tesproductos_id='.$producto_id.' AND tescolores_id='.$producto_id.' AND lote='.$lote);
		$roles= array();
		if ($rol->padres) {
            foreach (explode(',', $rol->padres) as $e) {
                $roles[] = "rr.aclroles_id = '$e'";
            }
        }
        return join(' OR ', $roles);
	}
	/*
	#@ $peso= el peso a restar;
	#@ $cono= los conos a restar;
	*/
	public function restarPesoConos($id=0,$peso=0,$cono=0)
	{
		$caja=$this->find_first((int)$id);
		$caja->pesoneto=(int)$caja->pesneto-(int)$peso;
		$caja->conos=(int)$caja->conos-(int)$cono;
		if($caja->save())
		{
			return TRUE;
		}
	}
		
	/*
	#
	# encontrar el peso y los conos en stok por caja
	#
	*/
	public function getStockCC($id=0)
	{
		$caja=$this->find_first((int)$id);
		$p=0;
		$c=0;
		//echo "<br />";echo $caja->pesoneto; echo '=> peso de la caja <br />';
		foreach($caja->getProcajastrama() as $ctp):
		//echo $ctp->peso; echo '=> peso trama <br />';
		//echo $ctp->conos; echo'=> conos trama <br />';
		$p=(float)$p+(float)$ctp->peso;
		$c=(float)$c+(float)$ctp->conos;
		endforeach;
		
		foreach($caja->getTesdetallesalidas() as $cts):
		//echo $cts->cantidad;echo '=> peso salidas <br />';
		//echo $cts->bobinas;echo '=> conos salidas <br />';
		$p=(float)$p+(float)$cts->cantidad;
		$c=(float)$c+(float)$cts->bobinas;
		endforeach;
		
		foreach($caja->getTesdetallesalidasinternas() as $ctsi):
		//echo $ctsi->cantidad; echo '=> peso salidas internas <br />';
		//echo $ctsi->bobinas; echo '=> conos salidas internas <br />';
		$p=(float)$p+(float)$ctsi->cantidad;
		$c=(float)$c+(float)$ctsi->bobinas;
		endforeach;
		/*Para las Auto salidas de guia*/
		$ingresos=Load::model('tesdetalleingresos');
		$auto=$ingresos->find_all_by_sql('SELECT c.* FROM  tesingresos as i,tescajas as c INNER JOIN tesdetalleingresos as d ON c.tesdetalleingresos_id=d.id WHERE i.aclempresas_id='.Session::get('EMPRESAS_ID').' AND i.autosalida="1" AND d.tesingresos_id=i.id AND c.id='.$id);
		foreach($auto as $ct):
		//echo $ct->pesoneto;echo "=> peso auto salidas<br />";
		$p=(float)$p+(float)$ct->pesoneto;
		//echo $ct->conos;echo "=> conos auti salidas<br />";
		$c=(float)$c+(float)$ct->conos;
		endforeach;
		/**echo $p;
		echo "=> p<br />";
		 echo $c;
		echo "=> c<br />";*/
		//$sp=number_format($caja->pesoneto,2,'.','')-number_format($p,2,'.','');
		//echo "<br />peso neto=>".$caja->pesoneto;
		//echo "<br /> conos ".$caja->conos.'<br />';
		$sp=(float)$caja->pesoneto-(float)$p;
		$sc=$caja->conos-$c;
		return (string)$sp.'-'.$sc;
	}
	public function getStokdecajas($id,$pag,$order='c.numerodecaja ASC')/*Tipo de producto*/
	{
		

        $sql="SELECT c.*
FROM tescolores as cl, tesproductos as t,tescajas as c INNER JOIN tesdetalleingresos as d ON d.id=c.tesdetalleingresos_id AND c.aclempresas_id=".Session::get('EMPRESAS_ID')." WHERE cl.id=d.tescolores_id AND c.enalmacen=1 AND c.estado=1 AND t.id=d.tesproductos_id AND t.testipoproductos_id=".$id." 
ORDER BY ".$order." ";
echo $sql;
//exit();
//return $this->find_all_by_sql($sql);
return $this->paginate_by_sql($sql, 'per_page: 100', 'page: '.$pag);
	}
	
/*recibe y el id del detalle a a ingresar las cajas en una nota de pedido 
se tiene que recibir el id del detalle y generar la nota de ingreso al alamacen y recien poner el 1 en la cajas 
retorna un objeto
*/
	function totalcajas($id)
	{
		return $this->find_all_by_aql('SELECT count(tescajas.id), tescajas.tipodecaja FROM `tescajas` where tesdetalleingresos_id='.$id.' AND aclempresas_id='.Session::get('EMPRESAS_ID').' GROUP BY tipodecaja');
	}
	
	public function getListado($conditions)
	{
		$campos='tescajas.'.join(',tescajas.',$this->fields).' , p.nombre as nombre';
//echo 'SELECT '.$campos.' FROM tesproductos as p,tescajas INNER JOIN tesdetalleingresos as d ON d.id=tescajas.tesdetalleingresos_id WHERE p.id=tescajas.tipodecaja AND tescajas.aclempresas_id='.Session::get('EMPRESAS_ID').' AND p.aclempresas_id='.Session::get('EMPRESAS_ID').' AND '.$conditions;
		return $this->find_all_by_sql('SELECT '.$campos.' FROM tesproductos as p,tescajas INNER JOIN tesdetalleingresos as d ON d.id=tescajas.tesdetalleingresos_id WHERE p.id=tescajas.tipodecaja AND tescajas.aclempresas_id='.Session::get('EMPRESAS_ID').' AND p.aclempresas_id='.Session::get('EMPRESAS_ID').' AND '.$conditions);
	}
/* REPORTES */

public function getCajasConhilo()
{
	return $this->find_all_by_sql("SELECT t.*,(t.pesoneto-d.cantidad) as peso FROM tescajas as t, 

tesdetallesalidasinternas as d WHERE d.tescajas_id=t.id AND 

(t.pesoneto-d.cantidad)>0
UNION DISTINCT
SELECT t.*,(t.pesoneto-d.cantidad) as peso FROM tescajas as t, 

tesdetallesalidas as d WHERE d.tescajas_id=t.id AND 

(t.pesoneto-d.cantidad)>0
UNION DISTINCT
SELECT t.*,(t.pesoneto-d.peso) as peso FROM tescajas as t, 

procajastrama as d WHERE d.tescajas_id=t.id AND 

(t.pesoneto-d.peso)>0
UNION DISTINCT
SELECT t.*,(t.pesoneto) as peso FROM tescajas as t ");
}
public function getCajassinHilo()
{
	return $this->find_all_by_sql("");
}
/*santa Carmela parel listado de cajas*/
public function getCajasU($id=0,$tesdatos_id)
{
	if($id!=0){
	/*echo 'SELECT i.id as id_ingreso,i.tesdatos_id as tesdatos_id,t.id as id,d.tesproductos_id as tesproductos_id, d.tescolores_id as tescolores_id,d.lote as lote, t.pesoneto as peso,t.conos as conos,IFNULL(numerodecaja,numeroant)as numerodecaja,t.estado as estado FROM tescajas as t, tesdetalleingresos as d ,tesingresos as i  WHERE i.id=d.tesingresos_id AND i.tesdatos_id='.$tesdatos_id.' AND d.tesproductos_id='.$id.' AND t.tesdetalleingresos_id=d.id AND t.aclempresas_id='.Session::get('EMPRESAS_ID').' AND (enalmacen=1 or enalmacen=8)';*/
	return $this->find_all_by_sql('SELECT i.id as id_ingreso,i.tesdatos_id as tesdatos_id,t.id as id,d.tesproductos_id as tesproductos_id, d.tescolores_id as tescolores_id,d.lote as lote, t.pesoneto as peso,t.conos as conos,IFNULL(numerodecaja,numeroant)as numerodecaja,t.estado as estado FROM tescajas as t, tesdetalleingresos as d ,tesingresos as i  WHERE i.id=d.tesingresos_id AND i.tesdatos_id='.$tesdatos_id.' AND d.tesproductos_id='.$id.' AND t.tesdetalleingresos_id=d.id AND t.aclempresas_id='.Session::get('EMPRESAS_ID').' AND (enalmacen=1 or enalmacen=8)');
	}else
	{
		return false;
	}
}
/*ids array();*/
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
	endforeach;
	return $c;
}

public function ActualizarCajas($tipo)
{
	/*$sql="SELECT IFNULL((c.pesoneto- (IFNULL((SELECT sum(ct.peso) FROM procajastrama as ct WHERE ct.tescajas_id=c.id GROUP BY ct.tescajas_id),0)+ IFNULL((SELECT sum(s.cantidad) FROM tesdetallesalidas as s WHERE s.tescajas_id=c.id GROUP BY s.tescajas_id),0)+ IFNULL((SELECT sum(si.cantidad) FROM tesdetallesalidasinternas as si WHERE si.tescajas_id=c.id GROUP BY si.tescajas_id),0) )),0) as stokpeso, IFNULL((c.conos- (IFNULL((SELECT sum(ct.conos) FROM procajastrama as ct WHERE ct.tescajas_id=c.id GROUP BY ct.tescajas_id),0)+ IFNULL((SELECT sum(s.bobinas) FROM tesdetallesalidas as s WHERE s.tescajas_id=c.id GROUP BY s.tescajas_id),0)+ IFNULL((SELECT sum(si.bobinas) FROM tesdetallesalidasinternas as si WHERE si.tescajas_id=c.id GROUP BY si.tescajas_id),0))),0) as stokbobinas,c.* FROM tesproductos as t,tescajas as c INNER JOIN tesdetalleingresos as d ON d.id=c.tesdetalleingresos_id AND c.aclempresas_id=1 WHERE c.enalmacen=1 AND c.estado=1 AND t.id=d.tesproductos_id AND t.testipoproductos_id=".$tipo." ORDER BY c.numerodecaja ASC";*/
	$sql="SELECT c.* FROM tesproductos as t,tescajas as c INNER JOIN tesdetalleingresos as d ON d.id=c.tesdetalleingresos_id AND c.aclempresas_id=1 WHERE c.estado=1 AND t.id=d.tesproductos_id AND t.testipoproductos_id=".$tipo." ORDER BY c.numerodecaja ASC limit 0,100";
	echo $sql;
	return $this->find_all_by_sql($sql);
}
	

}
/*

SELECT * FROM `tesproductos` WHERE codigo_ant=262 OR codigo_ant=271 OR codigo_ant=419 OR codigo_ant=510 OR codigo_ant=533 OR codigo_ant=535 OR codigo_ant=543 OR codigo_ant=544 OR codigo_ant=545 OR codigo_ant=553 OR codigo_ant=567 OR codigo_ant=578 OR codigo_ant=579 OR codigo_ant=580 OR codigo_ant=584 OR codigo_ant=585 OR codigo_ant=589 OR codigo_ant=590 OR codigo_ant=591 OR codigo_ant=593 OR codigo_ant=594 OR codigo_ant=595 OR codigo_ant=1278 OR codigo_ant=1312 OR codigo_ant=1330 OR codigo_ant=1338 OR codigo_ant=1363 OR codigo_ant=1375 OR codigo_ant=1378 OR codigo_ant=1386 OR codigo_ant=1404 OR codigo_ant=1405 OR codigo_ant=1406 OR codigo_ant=1407 OR codigo_ant=1412 OR codigo_ant=1413 OR codigo_ant=1416 OR codigo_ant=1435 OR codigo_ant=3148 OR codigo_ant=3161 OR codigo_ant=3163 OR codigo_ant=4267 OR codigo_ant=4305 OR codigo_ant=4533 OR codigo_ant=4582 OR codigo_ant=4626 OR codigo_ant=4954 OR codigo_ant=4962 OR codigo_ant=4963 OR codigo_ant=4982 OR codigo_ant=4990
*/
?>