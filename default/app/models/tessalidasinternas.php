<?php
class Tessalidasinternas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('aclusuarios','tesmonedas','tesdatos','tesdocumentos','aclempresas','tescuentascorrientes','tescondicionespagos','tesdatosdirecciones');
		$this->has_many('tesdetallesalidasinternas','tesletrassalidasinternas');
    }
	public function generarNumero($documento,$serie='999',$prefijo='VT')
	{
		//echo "numero", "conditions: SUBSTRING(npedido,1,2)='".$prefijo."' AND serie='".$serie."' AND tesdocumentos_id=".$documento." AND (estado='1' OR estado='9') AND aclempresas_id =".Session::get('EMPRESAS_ID');
		$maximo = $this->maximum("numero", "conditions: SUBSTRING(npedido,1,2)='".$prefijo."' AND serie='".$serie."' AND tesdocumentos_id=".$documento." AND (estado='1' OR estado='9') AND aclempresas_id =".Session::get('EMPRESAS_ID'));
		
		//echo $maximo;
		
		if(empty($maximo))
		{
			$maximo=1;
		}else{
			$maximo=$maximo+1;
		}
		return $maximo;
	}
	/*SALIDA DE HILO A TRAMA*/
	public function generarNumeroH($documento,$serie='999',$prefijo='')
	{
		echo $maximo = $this->maximum("numero", "conditions: SUBSTRING(npedido, 1, 2 )='".$prefijo."' AND serie='".$serie."' AND tesdocumentos_id=".$documento." AND estado='1' AND aclempresas_id =".Session::get('EMPRESAS_ID'));
		if(empty($maximo))
		{
			echo $maximo=1;
		}else{
			echo $maximo=$maximo+1;
		}
		return $maximo;
	}
	
	public function generarNumeroLetras()
	{
		$maximo = $this->maximum("numero", "conditions: tesdocumentos_id=34 AND (estado='1' OR estado='9') AND aclempresas_id =".Session::get('EMPRESAS_ID'));
		if(empty($maximo))
		{
			$maximo=1;
		}else{
			$maximo=$maximo+1;
		}
		return $maximo;
	}
	
	public function generarNumerocheque($cuentacorriente)
	{
		$maximo = $this->maximum("numero", "conditions: tescuentascorrientes_id=".$cuentacorriente." AND tesdocumentos_id=35 AND aclempresas_id =".Session::get('EMPRESAS_ID'));
		if(empty($maximo))
		{
			$maximo=1;
		}else{
			$maximo=$maximo+1;
		}
		return $maximo;
	}
	
	public function getNumeropedido($prefijo='',$serie=002)
	{
		$maximos=$this->find_by_sql('SELECT (IFNULL(MAX(SUBSTRING(npedido,4)),0)) as npedido FROM `tessalidasinternas` WHERE SUBSTRING( npedido, 1, 2 )="'.$prefijo.'" AND serie="'.$serie.'" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
		$maximo=$maximos->npedido+1;
		switch($maximo)
		{
			case $maximo<10: $maximo="0000".$maximo; break;
			case $maximo<100: $maximo="000".$maximo; break;
			case $maximo<1000: $maximo="00".$maximo; break;
			case $maximo<10000: $maximo="0".$maximo; break;
			default: $maximo=$maximo;
		}
		return $prefijo.'-'.$maximo;
	}

	public function generarNumeroFactura($serie='001')
	{
		$maximos = $this->find_by_sql('SELECT (IFNULL(MAX(SUBSTRING(numerofactura,5)),0)) as numerofactura FROM `tessalidasinternas` WHERE serie='.$serie.' AND tesdocumentos_id=15 AND aclempresas_id='.Session::get('EMPRESAS_ID'));
		$maximo=$maximos->numerofactura;
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
		return $serie.'-'.$maximo;
	}
	public function getCambio($fecha)
	{
	$tipocambios= new Testipocambios();
	$cambio=$tipocambios->find_first('conditions: fecha="'.$fecha.'"');
	if($cambio)
	{
		$id=$cambio->id;
	}else
	{
		$id=$tipocambios->maximum('id');
	}
	}
	
	public function getFacturas($facturas)
	{
		$ids=explode(',',$facturas);
		$fac='';
		$n=0;
		foreach($ids as $item=>$value)
		{
			$f=$this->find_first((int)$value);
			if($n==0)
			{
				$fac.='F/'.$f->numero;
			}else
			{
				$fac.=', '.$f->numero;
			}
			$n++;
		}
		return $fac;
	}
	
	/*PARA LOS ADELANTOS DE LOS ABONOS*/
	
public function getAdelantos($id)
{
	$detalles_abonos=new Tesabonosdetalles();
	return $total=$detalles_abonos->sum('monto','conditions: tessalidasinternas_id='.$id.' AND cargos="1"');
}

public function LetraAdelanto($id_dato)
{
	$sql='SELECT l.*, (l.monto-IFNULL(sum(ap.monto),0)) as total FROM tessalidasinternas as s,tesletrassalidasinternas as l LEFT JOIN tesaplicacionletrainterna as ap ON ap.tesletrassalidasinternas_id=l.id WHERE s.id=l.tessalidasinternas_id AND l.estado="1" AND s.aclempresas_id='.Session::get('EMPRESAS_ID').' AND total>0 AND s.npedido LIKE "LA%" AND s.tesdatos_id='.$id_dato.' GROUP BY l.id';
	//echo $sql;
	return $this->find_all_by_sql($sql);
}

public function existsAdelanto($id_dato)
{
	return $this->find_all_by_sql('SELECT (l.monto-IFNULL(sum(ap.monto),0)) as total FROM tessalidasinternas as s,tesletrassalidasinternas as l  LEFT JOIN tesaplicacionletrainterna as ap ON ap.tesletrassalidasinternas_id=l.id WHERE s.id=l.tessalidasinternas_id AND s.aclempresas_id='.Session::get('EMPRESAS_ID').' AND total>0.50 AND s.npedido LIKE "LA%" AND s.tesdatos_id='.$id_dato.' GROUP BY l.id');
}

public function totalAplicacion($id)/*el id de la letra adelanto*/
{
	$s=$this->find_by_sql('SELECT  l.monto - IFNULL( SUM( ap.monto ) , 0 ) AS t FROM tessalidasinternas AS s, tesletrassalidasinternas AS l LEFT JOIN tesaplicacionletrainterna AS ap ON ap.tesletrassalidasinternas_id = l.id WHERE  s.id=l.tessalidasinternas_id AND s.aclempresas_id='.Session::get('EMPRESAS_ID').' AND l.id ='.$id);
	return $s->t;
}

public function anularguia($id)
{
	$salida=$this->find_first((int)$id);
	$detalles=Load::model('tesdetallesalidasinternas');
	$rollos=Load::model('prorollos');
	/*restablecer los rollos*/
	$dett=$detalles->find('conditions: tessalidasinternas_id='.(int)$id);	
	foreach($dett as $det):
	if($rollos->exists('id='.(int)$det->prorollos_id)){
	$rollo=$rollos->find_first((int)$det->prorollos_id);
	$rollo->estadorollo='VENTA';
	$rollo->estado='1';
	$rollo->enalmacen='1';
	$rollo->save();
	Acciones::add("Colocó el ".$rollo->numero." PARA VENTA (anulacion de venta)",'prorollos');
	}
	endforeach;
	$salida->estadosalida="ANULADO";
	$salida->estado='1';
	$salida->userid=Auth::get('id');
	$salida->total=0;
	if($salida->save())
	{
	/*ANular las applicaciones a facturas si hubiera*/
		$app=Load::model('tesaplicacionletrainterna');
		$apps=$app->find('conditions: tessalidasinternas_id='.$id);
		foreach($apps as $ap):
			$app->find_first((int)$ap->id);
			$app->estado='0';
			$app->save();			
		endforeach;
	Acciones::add("Colocó el ".$salida->serie."-".$salida->numero." ({$salida->getTesdocumentos()->nombre}) como ANULADO",'tessalidas');
	return TRUE;
	}else
	{
	return FALSE;
	}
}

/*CONSULTAS PARA GRAFICOS */
public function getVentas()
{
	return $this->find_all_by_sql("SELECT sum(s.total) as total,DATE_FORMAT(s.femision,'%m/%y') as fecha FROM tessalidasinternas as s WHERE s.aclempresas_id=".Session::get('EMPRESAS_ID')." AND s.npedido like 'VT%' 
GROUP BY DATE_FORMAT(s.femision,'%Y-%m')");
}

public function getMuestras($anio,$mes_activo=0)
{
	if($mes_activo==0)
	{
		$cond=" AND DATE_FORMAT(s.femision, '%Y')='".$anio."'";}else{$cond=" AND DATE_FORMAT(s.femision, '%Y-%m')='".$anio."-".$mes_activo."'";
	}
	return $this->find_all_by_sql("SELECT d.tesproductos_id as id, s.femision as fecha,CONCAT_WS('-',s.serie,s.numero)as nu_guia, CONCAT_WS(' ',IFNULL(c.codigo_ant,c.codigo),c.razonsocial) as cliente,IFNULL(p.codigo_ant,p.codigo) as articulo, d.cantidad as mts, d.preciounitario as preunit, d.importe
FROM tessalidasinternas as s, tesdetallesalidasinternas as d, tesdatos as c,tesproductos as p 
WHERE s.npedido like 'MT%' AND s.id=d.tessalidasinternas_id AND c.id=s.tesdatos_id AND d.tesproductos_id=p.id AND s.aclempresas_id=".Session::get('EMPRESAS_ID').$cond."
ORDER BY p.id,s.femision ASC");

}

public function getNumeroruc($q)
{
	return $this->find_all_by_sql('SELECT s.id as id,concat_ws("-",s.serie,s.numero) as numero FROM tessalidas as s, tesdatos as d WHERE d.id=s.tesdatos_id AND concat_ws(" ",s.serie,s.numero) like "%'.$q.'%"');
}


}
?>