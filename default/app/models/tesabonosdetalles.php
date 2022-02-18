<?php
class Tesabonosdetalles extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tessalidas','tessalidasinternas','tesdatos','tesingresos','tesabonos');
    }
	public function getMenor_Mayor($id,$monto)
	{
		 $abono_monto=0;
		 $cargo_monto=0;
		/* Monto del Cargo de la factura*/
		 //echo $id.'   /*/*/*/*/'.$monto;
		 /*el monto ($monto) que ingresa sumarle los abonos que ya esten el documento que sean notas de creditos*/
		  $abono_monto=$this->sum('monto','conditions: (tessalidas_id!=0 or tessalidasinternas_id!=0 or tesingresos_id!=0) AND abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID'));
		  $cargo_monto=$this->sum('monto','conditions: tesabonos_id='.$id.' AND cargos="1" AND abono="0"');
		  $new_monto=$monto+$abono_monto;
		  if($new_monto>$cargo_monto)
		  {
			  $m=$monto-$cargo_monto;
			  Flash::valid('EL cliente tiene a su favor '.$m);
		  }else
		  {
			 /*el total de cargo == al total de abonos */
			 if($new_monto<$cargo_monto)
			 {				 
				 /*id del max cargo*/
				 $max_id=$this->maximum('id','conditions: tesabonos_id='.$id.' AND cargos="1"');
				 $cargo_monto=$this->sum('monto','conditions: tesabonos_id='.$id.' AND cargos="1"');
				 $m=$cargo_monto-$new_monto;
				 $ultimo_cargo=$this->find_first((int)$max_id);
				 $ultimo_cargo->monto=$ultimo_cargo->monto-$m;
				 $ultimo_cargo->save();
				 /*encontrat el monto a restar a al ultima factura*/
				 if(!empty($ultimo_cargo->tessalidas_id) || $ultimo_cargo->tessalidas_id!=0)
				 {
					 $salidas= new Tessalidas();
					 $salida=$salidas->find_first($ultimo_cargo->tessalidas_id);
					 $salida->estadosalida='ADELANTADO';
					 $salida->save();
					 Flash::valid('EL Documento '.$salida->serie.'-'.$salida->numero.' aun no se termino de Cancelar');
				 }
				 if(!empty($ultimo_cargo->tessalidasinternas_id) || $ultimo_cargo->tessalidasinternas_id!=0)
				 {
					 $salidas= new Tessalidasinternas();
					 $salida=$salidas->find_first($ultimo_cargo->tessalidasinternas_id);
					 $salida->estadosalida='ADELANTADO';
					 $salida->save();
					 Flash::valid('EL Documento '.$salida->serie.'-'.$salida->numero.' aun no se termino de Cancelar');
				 }
			 }
			 if($new_monto==$cargo_monto)
			 {
				 $max_id=$this->maximum('id','conditions: tesabonos_id='.$id.' AND cargos="1"');
				 $ultimo_cargo=$this->find_first((int)$max_id);
				 if(!empty($ultimo_cargo->tessalidas_id) || $ultimo_cargo->tessalidas_id!=0){
				 $salidas= new Tessalidas();
				 $salida=$salidas->find_first($ultimo_cargo->tessalidas_id);
				 $salida->estadosalida='PAGADO';
				 $salida->save();
				 Flash::valid('EL Documento '.$salida->serie.'-'.$salida->numero.' esta PAGADO');
				 }
				 if(!empty($ultimo_cargo->tessalidasinternas_id) || $ultimo_cargo->tessalidasinternas_id!=0){
				 $salidas= new Tessalidasinternas();
				 $salida=$salidas->find_first($ultimo_cargo->tessalidasinternas_id);
				 $salida->estadosalida='PAGADO';
				 $salida->save();
				 Flash::valid('EL Documento '.$salida->serie.'-'.$salida->numero.' esta PAGADO');
				 }
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
		$maximo = $this->maximum("correlativo", "conditions: tessalidas_id='0' AND tessalidasinternas_id='0' AND cargos='1'");
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
	
	public function existsLetra($id_v=0)
	{
	  $c=$this->find_by_sql("SELECT count(d.id) as c FROM `tesabonosdetalles`as d, tessalidas as s WHERE s.id=d.tessalidas_id AND s.tesdocumentos_id=34 AND d.abono=1 AND d.tesabonos_id=".$id_v);
	  return $c->c;
	}
	public function findLetras($id_v=0)
	{
	  return $this->find_all_by_sql("SELECT d.* FROM `tesabonosdetalles`as d, tessalidas as s WHERE s.id=d.tessalidas_id AND s.tesdocumentos_id=34 AND d.abono=1 AND d.tesabonos_id=".$id_v);
	}
	
	public function existsLetra_i($id_v=0)
	{
	  echo $id_v;
	  $c=$this->find_by_sql("SELECT count(d.id) as c FROM `tesabonosdetalles`as d, tessalidasinternas as s WHERE s.id=d.tessalidasinternas_id AND s.tesdocumentos_id=34 AND d.abono=1 AND d.tesabonos_id=".$id_v);
	  return $c->c;
	}
	public function findLetras_i($id_v=0)
	{
	  return $this->find_all_by_sql("SELECT d.* FROM `tesabonosdetalles`as d, tessalidasinternas as s WHERE s.id=d.tessalidasinternas_id AND s.tesdocumentos_id=34 AND d.abono=1 AND d.tesabonos_id=".$id_v);
	}
	
}
?>