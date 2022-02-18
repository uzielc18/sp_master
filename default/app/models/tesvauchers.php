<?php
Load::models('tesdetallevauchers');
class Tesvauchers extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesdetallevauchers');
		$this->belongs_to('aclusuarios','testipocambios','tesmonedas','voucherformadepagos');
		
		$this->validates_presence_of('voucherformadepagos_id', 'message: Debe Seleccionar una <b>Forma de Pago</b> en el Selector');
		$this->validates_presence_of('tesmonedas_id', 'message: Debe seleccionar una <b>Monedar</b> en el selector');
    }
	
	public function numero()
	{
		$maximo = $this->maximum("numero", "conditions: estadov!='ANULADO' AND aclempresas_id =".Session::get('EMPRESAS_ID'));
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
	
	public function asiento()
	{
		$m=date('m');
		$maximo = $this->maximum("numero", "conditions: mes='".$m."' AND aclempresas_id =".Session::get('EMPRESAS_ID'));
		if(empty($maximo))
		{
			$maximo=1;
		}else{
			$maximo=$maximo+1;
		}
		switch($maximo)
		{
			
			case $maximo<10: $maximo="000".$maximo; break;
			case $maximo<100: $maximo="00".$maximo; break;
			case $maximo<1000: $maximo="0".$maximo; break;
			default: $maximo=$maximo;
		}
		return $m.$maximo;
	}
	
	public function getProveedores()
	{
		$tesdetallevauchers= new Tesdetallevauchers();
		return $tesdetallevauchers->find('conditions: tesvauchers_id='.$this->id.' GROUP BY tesdatos_id');
		
	}
	public function getIngresoDetraccion($id=0)/*recibe le id del vauchers*/
	{
		$tesdetallevauchers= new Tesdetallevauchers();
		$td=$tesdetallevauchers->find_first('conditions: tesvauchers_id='.$id.' AND cargos="1"');
		//echo $td->tesingresos_id;
		return $td->tesingresos_id;
	}
	
	public function getCheques($tesdatos_id)
	{
		return $this->find_all_by_sql("SELECT s.id as tessalidas_id,s.numero as numero, d.* FROM tesdetallevauchers as d, tessalidas as s WHERE d.tessalidas_id=s.id AND d.abono=1 AND s.tesdocumentos_id=35 AND s.estadosalida='Pendiente' AND s.tesdatos_id=".$tesdatos_id." AND (select count(ch.tessalidas_id) from teschequessalidas as ch WHERE ch.tessalidas_id=s.id)=0");
	}
}
?>