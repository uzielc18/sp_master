<?php
class Tesproformas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesdetalleproformas');
		$this->belongs_to('aclusuarios','aclempresas','tesproductos','tesmonedas');
    }
	public function before_validation_on_create() {
        //$this->validates_uniqueness_of('numero','message: El <b>Numero</b> ya está siendo utilizado');
    }
	public function generarNumero()
	{
		$maximo = $this->maximum("numero", "conditions: aclempresas_id =".Session::get('EMPRESAS_ID'));
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
	
	public function obtenerProformas()
	{
		$columnas='tesproformas.' . join(',tesproformas.', $this->fields).', (SUM(d.total) - IFNULL( tesproformas.descuento,\'0\')) AS TOTAL';
		$joins="INNER JOIN tesdetalleproformas AS d ON tesproformas.id = d.tesproformas_id";
		$condiciones="tesproformas.estado=1 GROUP BY tesproformas.id";
		$orden="fecha_at ASC";
		//echo "columns: $columnas"."conditions: $condiciones"."join: $joins"."order: $orden";
		//$val=$this->find("columns: ".$columnas,"conditions: $condiciones","join: $joins","order: $orden");
		$val=$this->find_all_by_sql("SELECT tesproformas.*, (sum(d.total)-IFNULL(tesproformas.descuento,'0'))as TOTAL FROM tesproformas INNER JOIN tesdetalleproformas as d ON tesproformas.id=d.tesproformas_id WHERE 1=1 AND tesproformas.aclempresas_id=".Session::get('EMPRESAS_ID')." GROUP BY tesproformas.id");
		return $val;
	}
}
?>