<?php
class ScCrollos extends ActiveRecord{

    public function initialize() {
        //relaciones
		$this->has_many('sc_detalleprogramados','sc_detalleprocesos','tesdetallesalidas','tesdetallesalidasinternas');
		$this->belongs_to('tesproductos','tesdetalleingresos','tesdatos','tescolores');
    }
	public function generarCodigo()
	{
		//echo $documento;
		$maximo = $this->maximum("codigo");
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
	
	public function getTelas($q)
	{
		return $this->find("conditions: estadorollo!='TERMINADO' AND CONCAT_WS(' ',nombre,codigo,metros) like '%".$q."%'");
	}
}
?>