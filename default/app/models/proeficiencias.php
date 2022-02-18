<?php
class Proeficiencias extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('promaquinas','acldatos','aclusuarios','proturnos');
    }
	//recibe el id del dato para ve su eficiencia
	public function getEfi($id,$columns='p.*',$group='',$order='',$anio='',$mes='')
	{
		/*echo "SELECT ".$columns.", s.valor as resta_valor,IF(p.valor < s.valor,(p.valor-0),(p.valor-IFNULL(s.valor,0)))as resta,(p.valor-IFNULL(s.valor,0)) as resta_2,s.id as id_s FROM  proeficiencias AS p LEFT JOIN proeficiencias AS s ON s.acldatos_id = p.acldatos_id AND s.promaquinas_id=p.promaquinas_id AND s.id < p.id WHERE p.acldatos_id=".$id.$fecha.$group.$order;*/
		$betwen='';
		if($anio!='')
		{
			$m_a=$mes-1;
			if($m_a<10)$m_a='0'.$m_a;
			$fecha_a=$maxf=$this->maximum("fecha", "conditions: DATE_FORMAT(fecha,'%Y-%m') = '".$anio."-".$m_a."'");
			if(empty($fecha_a))$fecha_a=$anio.'-01-01';
			//$fecha_a=$maxf->fecha;
			/*busco el maxima fecha para es mes */
			
			$m_b=$mes;
			/*$m_b=$mes+1;
			if($m_b<10)$m_b='0'.$m_b;*/
			$fecha_b=$maxf=$this->maximum("fecha", "conditions: DATE_FORMAT(fecha,'%Y-%m') = '".$anio."-".$m_b."'");
			//$fecha_b=$anio.'-'.$m_b.'-01';
			$betwen=' AND s.fecha BETWEEN  "'.$fecha_a.'" AND  "'.$fecha_b.'" AND p.fecha<="'.$fecha_b.'"';
			/*obentgo el primer dia del siguente mes */
		 }
		 
		 $sql="SELECT ".$columns.", s.valor as resta_valor,IF(p.valor<s.valor,(p.valor-0),(p.valor-IFNULL(s.valor,0)))as resta,(p.valor-IFNULL(s.valor,0)) as resta_2,s.id as id_s FROM  proeficiencias AS p LEFT JOIN proeficiencias AS s ON s.acldatos_id = p.acldatos_id AND s.promaquinas_id=p.promaquinas_id AND s.id<p.id WHERE p.acldatos_id=".$id.$betwen.$group.$order;
		 echo $sql;
		return $this->find_all_by_sql($sql);
	}
	
	public function getTurno($id=0,$fecha='',$maq=0)
	{
		/*SELECT t.nombre
FROM proeficiencias AS p, proturnos AS t
WHERE p.proturnos_id = t.id
AND p.fecha =  '2013-07-10'
AND p.acldatos_id =16
GROUP BY t.id*/
		 $mas=" AND p.promaquinas_id=".$maq;
		if($maq==0){$mas=" GROUP BY t.id";}
		$turno=$this->find_by_sql("Select t.nombre as nombre,p.observaciones as observaciones From proeficiencias as p, proturnos as t WHERE t.id=p.proturnos_id AND p.acldatos_id=".$id." AND p.fecha='".$fecha."'".$mas);
		return $turno;
	}
	
	
}
?>