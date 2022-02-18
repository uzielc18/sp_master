<?php
Load::models('tesingresos');
class Tesdetallevauchers extends ActiveRecord {

    public function initialize(){
        //relaciones
		//$this->has_many('tessalidasinternas','tessalidas','tesingresos');
		$this->belongs_to('tesvauchers','tessalidas','tesingresos','tesdatos','tescuentascorrientes');
    }
	public function getMenor_Mayor($id,$monto){ 
	      /* Monto del Cargo de la factura*/
		  $cargo_monto=$this->sum('monto','conditions: tesvauchers_id='.$id.' AND cargos="1" AND abono="0"');
		  if($monto>$cargo_monto)
		  {
			  $n=$this->count('id','conditions: tesvauchers_id='.$id);
			  /*crea con la diferencia un nuevo registro de tesdetallevauchers */
			  $monto_nuevo=$monto-$cargo_monto;
			  $detalle= new Tesdetallevauchers();
			  $detalle->tesvauchers_id=$id;
			  $detalle->tesingresos_id='0';
			  $detalle->tessalidas_id='0';
			  $detalle->abono='0';
			  $detalle->cargos='1';
			  if($n==1)
			  {
			  	$detalle->tesdatos_id=Session::get('proveedor_id');
			  }else
			  {
			  	$detalle->tesdatos_id=0;
			  }
			  $detalle->monto=$monto_nuevo;
			  $detalle->estado='1';
			  if($n==1)
			  {
				$detalle->plancontable='42201';  
			  }else
			  {
			  	$detalle->plancontable='67311';
			  }
			  $detalle->correlativo=Session::get('proveedor_id');
	 		  $detalle->noperacion='0000';
			  $detalle->save();
			  Flash::valid('Se agreago un nuevo cargo por la diferencia ');
			  
		  }else
		  {
			 /*el total de cargo == al total de abonos */
			 $cargo_monto=$this->sum('monto','conditions: tesvauchers_id='.$id.' AND cargos="1" AND abono="0"');
			 $abono_monto=$this->sum('monto','conditions: tesvauchers_id='.$id.' AND cargos="0" AND abono="1"');
			 
			  /* valida que sea solo uno y busca el ingreso y lo coloca en estadoingreso = "ADELANTADO"*/
			  $total=$this->count('conditions: tesingresos_id!="0" AND tesvauchers_id='.$id.' AND cargos="1"');
			  if($total==1)
			  {
				 $detalle=$this->find_first('conditions: tesingresos_id!="0" AND tesvauchers_id='.$id.' AND cargos="1"');
				 $ingresos= new Tesingresos();
				 $ingreso=$ingresos->find_first($detalle->tesingresos_id);
				 $cargo_monto=number_format($this->sum('monto','conditions: tesvauchers_id='.$id.' AND cargos="1" AND abono="0" AND tesingresos_id!="0"'),2,'.','');
				 $abono_monto=number_format($this->sum('monto','conditions: tesvauchers_id='.$id.' AND cargos="0" AND abono="1"'),2,'.','');
				 if($cargo_monto!=$abono_monto)
				 {
					 Flash::valid('EL Documento '.$ingreso->serie.'-'.$ingreso->numero.' aun no se termino de pagar');
					 $ingreso->estadoingreso='ADELANTADO';
				 }else{
					 $ingreso->estadoingreso='PAGADO';
				 }
				 $ingreso->save();
				 
			  }
		  }
		  
		  //Flash::valid('este id grabo'.$this->id);
    }
	public function ndocumento()
	{	
		$maximo = $this->maximum("correlativo", "conditions: abono='1'");
		if(empty($maximo))
		{
			$maximo=1;
		}else{
			$maximo=$maximo+1;
		}
		switch($maximo)
		{
			
			case $maximo<10: $maximo="0000".$maximo; break;
			case $maximo<100: $maximo="000".$maximo; break;
			case $maximo<1000: $maximo="00".$maximo; break;
			case $maximo<10000: $maximo="0".$maximo; break;
			default: $maximo=$maximo;
		}
		return $maximo;
	}
	public function getCorrelativo()
	{	
		$max=$this->find_by_sql('SELECT max(correlativo) as maximo FROM tesdetallevauchers as d,tesvauchers as v WHERE v.id=d.tesvauchers_id AND v.aclempresas_id='.Session::get('EMPRESAS_ID'));
		//$maximo = $this->maximum("correlativo", "conditions: tesingresos_id='0' AND tessalidas_id='0' AND cargos='1'");
		$maximo=$max->maximo;
		if(empty($maximo))
		{
			$maximo=1;
		}else{
			$maximo=$maximo+1;
		}
		switch($maximo)
		{
			
			case $maximo<10: $maximo="0000".$maximo; break;
			case $maximo<100: $maximo="000".$maximo; break;
			case $maximo<1000: $maximo="00".$maximo; break;
			case $maximo<10000: $maximo="0".$maximo; break;
			default: $maximo=$maximo;
		}
		return $maximo;
	}
	/*exixste nota de credito para pagar con letra*/
	
	public function existsLetra($id_v=0)
	{
	  $c=$this->find_by_sql("SELECT count(d.id) as c FROM `tesdetallevauchers`as d, tesingresos as i WHERE i.id=d.tesingresos_id AND i.tesdocumentos_id=34 AND d.abono=1 AND d.tesvauchers_id=".$id_v);
	  return $c->c;
	}
	public function findLetras($id_v=0)
	{
	  return $this->find_all_by_sql("SELECT d.* FROM `tesdetallevauchers`as d, tesingresos as i WHERE i.id=d.tesingresos_id AND i.tesdocumentos_id=34 AND d.abono=1 AND d.tesvauchers_id=".$id_v);
	}
	/*recibe el id del vaouchers*/
	public function getAbonoA($id)
	{
		$a=$this->find_by_sql("SELECT count(*) as n FROM tesdetallevauchers as d, tesingresos as i WHERE d.tesingresos_id=i.id AND i.tesdocumentos_id!=13 AND d.abono=1 AND d.tesvauchers_id=".$id);
		if($a->n>0)
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}
	public function findAbonoA($id)
	{
		return $this->find_by_sql("SELECT d.* FROM tesdetallevauchers as d, tesingresos as i WHERE d.tesingresos_id=i.id AND i.tesdocumentos_id!=13 AND d.abono=1 AND d.tesvauchers_id=".$id.' limit 0,1');
		
	}
	/*Busca no de credito para restar al totoal del importe del vouchers*/
	public function getAbonoNC($id)
	{
		$a=$this->find_all_by_sql("SELECT count(*) as n FROM tesdetallevauchers as d, tesingresos as i WHERE d.tesingresos_id=i.id AND i.tesdocumentos_id=13 AND d.abono=1 AND d.tesvauchers_id=".$id);
		if($a>0)
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}
	public function findAbonoNC($id)
	{
		$a=$this->find_by_sql("SELECT sum(d.monto)as monto FROM tesdetallevauchers as d, tesingresos as i WHERE d.tesingresos_id=i.id AND i.tesdocumentos_id=13 AND d.abono=1 AND d.tesvauchers_id=".$id);
		return $a->monto;
	}
	
	
}
?>