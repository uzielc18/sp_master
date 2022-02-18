<?php
class Tescolores extends ActiveRecord {

   public function initialize() {
        //validaciones
		$this->belongs_to('aclempresas');
		$this->has_many('tesdetalleingresos');
	}
	public function codigo()
	{
		$maximo = $this->maximum("codigo");
		if(empty($maximo))
		{
			$maximo=1;
		}else{
			$maximo=$maximo+1;
		}
		switch($maximo)
		{
			case $maximo<10: $maximo="0".$maximo; break;
			case $maximo<100: $maximo="00".$maximo; break;
			case $maximo<1000: $maximo="000".$maximo; break;
			default: $maximo=$maximo;
		}
		return $maximo;
	}
}
?>