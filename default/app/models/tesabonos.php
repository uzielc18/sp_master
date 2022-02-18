<?php
class Tesabonos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesabonosdetalles');
		$this->belongs_to('tesformadepagosabonos','tesmonedas','tesdatos','aclusuarios','testipocambios');
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
	
	public function getClientes()
	{
		$tesabonosdetalles= new Tesabonosdetalles();
		return $tesabonosdetalles->find('conditions: tesabonos_id='.$this->id.' GROUP BY tesdatos_id');
		
	}
	

/*CONSULTA PARA GRAFIOCS ABONOS ISNULL( d.tessalidasinternas_id )*/
public function getAbonos()
{
	return $this->find_all_by_sql("SELECT SUM( d.monto ) AS total, DATE_FORMAT( a.fecha,  '%m/%y' ) AS fecha
FROM tesabonos AS a, tesabonosdetalles AS d
WHERE d.tesabonos_id = a.id
AND ISNULL( d.tessalidasinternas_id ) 
GROUP BY DATE_FORMAT( a.fecha,  '%Y-%m' ) ");
}
/*CONSULTA PARA GRAFIOCS ABONOS ISNULL( d.tessalidas_id )*/
public function getAbonos_i(){
	return $this->find_all_by_sql("SELECT SUM( d.monto ) AS total, DATE_FORMAT( a.fecha,  '%m/%y' ) AS fecha
FROM tesabonos AS a, tesabonosdetalles AS d
WHERE d.tesabonos_id = a.id
AND ISNULL( d.tessalidas_id ) 
GROUP BY DATE_FORMAT( a.fecha,  '%Y-%m' ) ");
	}
	
/*ABONOS ADELANTO*/
public function getAbonos_ad(){
	/*tesformadepagosabonos_id=1 AND */
	return $this->find_all_by_sql("SELECT * FROM tesabonos as a WHERE isnull(adelanto_no_mostrar) AND (tesformadepagosabonos_id!=14 AND tesformadepagosabonos_id!=10) AND (select count(id) from tesabonosdetalles where tesabonos_id=a.id)=1 AND a.activo=1 AND a.aclempresas_id=".Session::get('EMPRESAS_ID').' order by a.fecha asc ');
	}	

}
?>