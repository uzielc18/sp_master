<?php
Load::models('tesingresos','tesletrasingresos','tesvauchers','tesdetallevauchers');
class Tesaplicacionletraingresos extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('tesletrasingresos');
    }

	public function guardarApllicacion($id)
	{
		$DETALLES= new Tesdetallevauchers();
		$detalles=$DETALLES->find('conditions: tesvauchers_id='.(int)Session::get('V_ID'));
		foreach($detalles as $detalle):
		if(!$this->exists('tesletrasingresos_id='.$id.' AND tesingresos_id='.$detalle->tesingresos_id)){
		 $aplicacion=new Tesaplicacionletraingresos();
		 $aplicacion->tesletrasingresos_id=$id;
		 $aplicacion->tesingresos_id=$detalle->tesingresos_id;
		 $aplicacion->numerodefactura=$detalle->getTesingresos()->serie.'-'.$detalle->getTesingresos()->numero;
		 $aplicacion->monto=$detalle->monto;
		 $aplicacion->estado='1';
		 $aplicacion->userid=Auth::get('id');
		 $aplicacion->aclempresas_id=Session::get('EMPRESAS_ID');
		 $aplicacion->tesvauchers_id=Session::get('VI_ID');
		 $aplicacion->save();
		}
		endforeach;
		
	}
	
public function guardarApllicacion_letra($ids,$montos,$dets)
{
$ID=explode(",",$ids);
$MON=explode(",",$montos);
$z=count(explode(",",$ids));
$arrayletras=array();
for($i=0;$i<$z;$i++)
{
	$arrayletras[$ID[$i]]=$MON[$i];
	
}
asort($arrayletras,1);
$array_detalles=array();
foreach($dets as $det)
{
	$array_detalles[$det->id]=array('id'=>$det->id,'tesingresos_id'=>$det->tesingresos_id,'monto'=>$det->monto,'numerodefactura'=>$det->getTesingresos()->serie.'-'.$det->getTesingresos()->numero,"tesvauchers_id"=>$det->tesvauchers_id);
}
echo "1";
self::pasarletras($arrayletras,$array_detalles);

}
function pasarletras($arrayl,$array_d)
{
	$contador=0;
	foreach($arrayl as $item=>$value)
	{
		$contador++;
		$d=0;
		foreach($array_d as $a_det):
		if($value>=$a_det['monto'])
		{
			/*graba el detalla de la apliacion letra ingreso*/
			$aplicacion=new Tesaplicacionletraingresos();
			$aplicacion->tesletrasingresos_id=$item;
			$aplicacion->tesingresos_id=$a_det['tesingresos_id'];
			$aplicacion->numerodefactura=$a_det['numerodefactura'];
			$aplicacion->monto=$a_det['monto'];
			$aplicacion->estado='1';
			$aplicacion->userid=Auth::get('id');
			$aplicacion->aclempresas_id=Session::get('EMPRESAS_ID');
			$aplicacion->tesvauchers_id=Session::get('V_ID');;
			$aplicacion->tesdetallevauchers_id=$a_det['id'];
			$aplicacion->save();
			$arrayl[$item]=($value-$a_det['monto']);
			unset($array_d[$a_det['id']]);
			return $this->pasarletras($arrayl,$array_d);
		}
		if($value<$a_det['monto'])
		{
			/*graba el detalla de la apliacion letra ingreso*/
			$aplicacion=new Tesaplicacionletraingresos();
			$aplicacion->tesletrasingresos_id=$item;
			$aplicacion->tesingresos_id=$a_det['tesingresos_id'];
			$aplicacion->numerodefactura=$a_det['numerodefactura'];
			$aplicacion->monto=$value;
			$aplicacion->estado='1';
			$aplicacion->userid=Auth::get('id');
			$aplicacion->aclempresas_id=Session::get('EMPRESAS_ID');
			$aplicacion->tesvauchers_id=$a_det['tesvauchers_id'];
			$aplicacion->tesdetallevauchers_id=$a_det['id'];
			$aplicacion->save();
			
			$LI=new Tesletrasingresos();
			$letra=$LI->find_first($item);
			$letra->estado='9';
			$letra->save();
			
			unset($arrayl[$item]);
			$a_det['monto']=($a_det['monto']-$value);
			$array_d[$a_det['id']]=array('id'=>$a_det['id'],'tesingresos_id'=>$a_det['tesingresos_id'],'monto'=>$a_det['monto'],'numerodefactura'=>$a_det['numerodefactura']);
			
			break;
		}
		$d++;
		endforeach;
		/*graba el detalle del */
		$LI=new Tesletrasingresos();
		 $letra=$LI->find_first($item);
		 $ingresos= new Tesingresos();
		 $ingreso=$ingresos->find_first((int)$letra->tesingresos_id);
		 $detalle=new Tesdetallevauchers();
		 $detalle->tesvauchers_id=Session::get('V_ID');
		 $detalle->tesingresos_id=$ingreso->id;
		 $detalle->tessalidas_id='0';
		 $detalle->abono='1';
		 $detalle->cargos='0';
		 $detalle->tesdatos_id=$ingreso->tesdatos_id;
		 //if(!empty($letra->monto_n)){$detalle->monto=$letra->monto_n-$value;}else{$detalle->monto=$letra->monto-$value;}
		 $detalle->monto=$this->get_total_app($letra->id,Session::get('V_ID'));
		 $detalle->save();
		/**/
		if(count($array_d)==0){$arrayl="";$array_d="";};
	}
}
	
/*Guardar la aplicacion para la letra por adelanto*/
/*public function guardarApllicacion_letra($ids,$montos,$cargos,$idv)
{
	count(explode(",",$ids));
	//*compara el mayor con el menor
	foreach($cargos as $detalle):
	if(!$this->exists('tesletrasingresos_id='.$id.' AND tesingresos_id='.$detalle->tesingresos_id))
	{
		$aplicacion=new Tesaplicacionletraingresos();
		$aplicacion->tesletrasingresos_id=$id;
		$aplicacion->tesingresos_id=$detalle->tesingresos_id;
		$aplicacion->numerodefactura=$detalle->getTesingresos()->serie.'-'.$detalle->getTesingresos()->numero;
		if($detalle->cargos==1)
		{
			if($abono>$detalle->monto)$aplicacion->monto=$detalle->monto;else $aplicacion->monto=$abono;
			$aplicacion->estado='1';
		}
		if($detalle->abono==1)
		{
			if(!empty($validar))
			{
				$aplicacion->monto=$validar;
			}else
			{
				$aplicacion->monto=$detalle->monto;	
			}
			$aplicacion->estado='9';
		}
		$aplicacion->userid=Auth::get('id');
		$aplicacion->aclempresas_id=Session::get('EMPRESAS_ID');
		$aplicacion->tesvauchers_id=$v_id;
		$aplicacion->tesdetallevauchers_id=$id_detalle_vaucher;
		$aplicacion->save();
	}
	endforeach;
	//echo 'se aplico este vuahcers '.$v_id;
}
	
	public function getTotalaplicaciones($id)
	{
		return $this->sum('monto','conditions: tesletrasingresos_id='.$id);
	}
	public function getTotalApp()
	{
		return $this->sum('monto','conditions: tesletrasingresos_id='.$this->id);
	}*/
/*Recibe el id del ingreso para obtener el id de la letra ingreso*/

public function getLetra_ingresos($id){
	$l=$this->find_by_sql("SELECT * FROM `tesletrasingresos` WHERE tesingresos_id=".$id);
	return $l;
	}
/*recibe el id de la leta y el id del vauchers para poder opbneter el total de la aplicaciona  esa letra*/
private function get_total_app($l_id,$v_id)
{
	$a=$this->find_by_sql("SELECT ifnull(sum(monto),0) as total FROM tesaplicacionletraingresos WHERE tesletrasingresos_id=".$l_id." AND tesvauchers_id=".$v_id);
	return $a->total; 
}
	
}
?>