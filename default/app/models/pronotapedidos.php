<?php
Load::models('prodetallepedidos');
class Pronotapedidos extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('aclusuarios','tesdatos','plareas');
		$this->has_many('prodetallepedidos');
    }
	public function before_validation_on_create() {
        /*$this->validates_uniqueness_of('codigo', 'message: El <b>Codigo</b> ya está siendo utilizado');*/
    }
	public function codigo_r()
	{
		$maximos = $this->find_by_sql('SELECT (IFNULL(MAX(codigo),0)) as codigo FROM `pronotapedidos` WHERE tipo="salida" AND !ISNULL(plareas_id) AND origen =  "Repuestos" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
		$maximo=$maximos->codigo+1;
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
	
	public function codigo($tipo)
	{
		$maximos = $this->find_by_sql('SELECT (IFNULL(MAX(SUBSTRING(codigo,4)),0)) as codigo FROM `pronotapedidos` WHERE tipo="'.$tipo.'" AND origen !=  "Repuestos" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
		$maximo=$maximos->codigo+1;
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
		$prefijo='NTP';
		return $prefijo.$maximo;
	}

	/* Recibe el id de la nota de pedido para ver la referencia que tiene*/
	public function getReferencia($refrencia)
	{
		$valor='No hay referencia';
		$R=explode("_",$refrencia);
		$RID=explode("-",$R[1]);
		$id=$RID[1];
		if($R[0]=="proproduccion")
		{
			$pro=Load::model($R[0]);
			$producion=$pro->find_first($id);
			$valor=Html::link('santapatricia/produccion/cargarmaquina/'.$producion->getPromaquinas()->id,$producion->getPromaquinas()->prefijo.'-'.$producion->getPromaquinas()->numero,'class="btn btn-secondary"');
			
		}
		return $valor;
	}
	/*
	Recibe el id de produccion y busca su pedido y su detalle de trama por su detalle de pedido y lo valida con su detalle de pedido
	@ id = id de produccion
	@ id_d = id del detalle de produccion
	*/
	public function getValidarDetallePedido($id,$id_d)
	{		
		/*echo "id detalle produccion ".$id_d;
		echo "<br />";
		echo "id p produccion ".$id;
		echo "<br />";*/
		
		$val=FALSE;
		$tramas=Load::model('protrama');
		$detalles_t=$tramas->find('conditions: estado=1 AND prodetalleproduccion_id='.$id_d);
		/*detalle del pedido*/
		$detalles=new Prodetallepedidos();
		$pedido=$this->find('conditions: referencia="proproduccion_id-'.$id.'"');
		
		$i_p=0;
		$i_t=0;
		if($pedido)
		{
			foreach($detalles_t as $t)
			{
				 $i_t++;
				if(Auth::get('id')==3)
				{
					echo "trama_id=>".$t->id.'***detallepedido_id='.$t->prodetallepedidos_id.'';echo "<br />";
				}
				if($detalles->exists("id=".$t->prodetallepedidos_id)){if($i_t==1)echo Html::link("santapatricia/notadepedido/cargarnota/".$t->getProdetallepedidos()->pronotapedidos_id.'/ver/Produccion','Ver Pedido');}else {echo "Sin/Pedido";}
		        echo "<br />";
				
				foreach($pedido as $ped)
				{
					//echo 'id del PEdido =>'.$ped->id;
					//echo "<br />";
					foreach($ped->getProdetallepedidos() as $det):
					/*validar quie sea el mismo producto el mismo color y y que el cajas_id en det sea diferente de NULL y 0 y tb el lote*/	
						if(Auth::get('id')==100)
						{
							echo "id del detalle pedido=>".$det->id;
							echo "<br />";
							echo "$det->id==$t->prodetallepedidos_id && $det->tesproductos_id==$t->tesproductos_id && $det->tescolores_id==$t->tescolores_id && !empty($det->tescajas_id) && !empty($det->lote)";
							echo "<br />";
						}
						if($det->id==$t->prodetallepedidos_id && $det->tesproductos_id==$t->tesproductos_id && $det->tescolores_id==$t->tescolores_id && !empty($det->tescajas_id) && !empty($det->lote))
						{
							/*echo "****";*/
							$i_p++;
							
							/*echo "<br />";*/
						}
						
					endforeach;
				}	
			}
			/*Fin Foreach*/
			
			if($tramas->exists('prodetalleproduccion_id='.$id_d.' AND estado=0'))
			{
						echo "Entregado sin pedido <br />";
						$val=TRUE;
			}
		}else
		{
			/*echo 'No hay Ni un pedido';*/
			if($pedidos->exists('referencia="proproduccion_id-'.$id.'" AND estadonota="ENTREGADO"'))
			{
				echo "Entregado";
				$val=TRUE;
			}
		}
		if(Auth::get('id')==3){
		echo $i_t;
		echo " total de trama<br />";
		echo $i_p;
		echo " total detalle pedido entregado <br />";}
		if($i_t!=0){
			if($i_p>=$i_t)
			{
			echo "Entregado";
			$val=TRUE;
			}
		}
		return $val;
	}
}
?>