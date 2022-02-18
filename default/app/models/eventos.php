<?php
class Eventos extends ActiveRecord {

    public function initialize() {
        //relaciones
		//$this->has_many('tesvauchers');
		//$this->has_many('tesletrasingresos');
		$this->belongs_to('aclusuarios','eventostipo');
    }
	
	/*$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;*/
	public function getAllEventos($fecha_inicio,$fecha_fin)
	{
		$fecha=explode('-',$fecha_inicio);		
		return $this->find_all_by_sql("SELECT v.id as id,CONCAT_WS(' ',v.titulo,v.subtitulo)as title,CONCAT_WS(' ',v.fecha_inicio,v.hora_inicio)as fecha_inicio,CONCAT_WS(' ',v.fecha_fin,v.hora_fin)as fecha_fin,CONCAT('calendarios/eventos/ver/',id)as url FROM eventos as v WHERE v.fecha_inicio BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."'
UNION ALL
SELECT v.id AS id, CONCAT_WS(' ',td.razonsocial,CONCAT_WS('-',CONCAT_WS(' ',d.nombre ,v.serie), v.numero )) AS title, v.fvencimiento AS fecha_inicio,'' AS fecha_fin,'' AS url FROM tessalidas as v, tesdocumentos as d, tesdatos as td WHERE v.tesdocumentos_id=d.id AND td.id=v.tesdatos_id AND v.fvencimiento BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."' AND (v.tesdocumentos_id=34 OR v.tesdocumentos_id=37)
UNION ALL
SELECT v.id as id,CONCAT_WS(' ',v.nombre,v.appaterno)as title,CONCAT_WS('-','".$fecha[0]."',DATE_FORMAT(v.fnacimiento,'%m-%d')) as fecha_inicio,'' as fecha_fin,'' as url FROM acldatos as v WHERE  DATE_FORMAT(v.fnacimiento,'%m') ='".$fecha[1]."' ");
	}
	public function getCumples($fecha_inicio,$fecha_fin)
	{
		$fecha=explode('-',$fecha_inicio);
		$fecha2=explode('-',$fecha_fin);
		/*Los cumpleaños de la tabla acldatos*/
		$cumple="SELECT 'datos' as tabla, v.id as id,CONCAT_WS(' ',v.nombre,v.appaterno)as title,CONCAT_WS('-','".$fecha[0]."',DATE_FORMAT(v.fnacimiento,'%m-%d')) as fecha_inicio,'' as fecha_fin,'' as url FROM acldatos as v WHERE (DATE_FORMAT(v.fnacimiento,'%m-%d')>='".$fecha[1]."-".$fecha[2]."' AND DATE_FORMAT(v.fnacimiento,'%m-%d')<='".$fecha2[1]."-".$fecha2[2]."')";
		//$cumple="SELECT 'datos' as tabla, v.id as id,CONCAT_WS(' ',v.nombre,v.appaterno)as title,CONCAT_WS('-','".$fecha[0]."',DATE_FORMAT(v.fnacimiento,'%m-%d')) as fecha_inicio,'' as fecha_fin,'' as url FROM acldatos as v WHERE (DATE_FORMAT(v.fnacimiento,'%m-%d')>='".$fecha[1]."-".$fecha[2]."')";
		$cumple_evento="SELECT 'eventos' as tabla,v.id as id,CONCAT_WS(' ',v.titulo,v.subtitulo)as title,CONCAT_WS('-','".$fecha[0]."',DATE_FORMAT(v.fecha_inicio,'%m-%d')) as fecha_inicio,CONCAT_WS('',v.fecha_fin,v.hora_fin)as fecha_fin,CONCAT('calendarios/eventos/ver/',id)as url FROM eventos as v WHERE v.eventostipo_id=1 AND (DATE_FORMAT(v.fecha_inicio,'%m-%d')>='".$fecha[1]."-".$fecha[2]."' AND DATE_FORMAT(v.fecha_inicio,'%m-%d')<='".$fecha2[1]."-".$fecha2[2]."')";
		//$cumple_evento="SELECT 'eventos' as tabla,v.id as id,CONCAT_WS(' ',v.titulo,v.subtitulo)as title,CONCAT_WS('-','".$fecha[0]."',DATE_FORMAT(v.fecha_inicio,'%m-%d')) as fecha_inicio,CONCAT_WS('',v.fecha_fin,v.hora_fin)as fecha_fin,CONCAT('calendarios/eventos/ver/',id)as url FROM eventos as v WHERE v.eventostipo_id=1 AND (DATE_FORMAT(v.fecha_inicio,'%m-%d')>='".$fecha[1]."-".$fecha[2]."')";
		$cumple_contacto="SELECT 'contacto' as tabla,v.id as id,CONCAT_WS(' ',v.nombres)as title,CONCAT_WS('-','".$fecha[0]."',DATE_FORMAT(v.fnacimiento,'%m-%d')) as fecha_inicio,'' as fecha_fin,'' as url FROM tescontactos as v WHERE estado ='1' AND v.aclempresas_id=".Session::get('EMPRESAS_ID')." AND !ISNULL(v.nombres) AND (DATE_FORMAT(v.fnacimiento,'%m-%d')>='".$fecha[1]."-".$fecha[2]."' AND DATE_FORMAT(v.fnacimiento,'%m-%d')<='".$fecha2[1]."-".$fecha2[2]."')";
		$cumple_contacto="SELECT 'contacto' as tabla,v.id as id,CONCAT_WS(' ',v.nombres)as title,CONCAT_WS('-','".$fecha[0]."',DATE_FORMAT(v.fnacimiento,'%m-%d')) as fecha_inicio,'' as fecha_fin,'' as url FROM tescontactos as v WHERE estado ='1' AND v.aclempresas_id=".Session::get('EMPRESAS_ID')." AND !ISNULL(v.nombres) AND (DATE_FORMAT(v.fnacimiento,'%m-%d')>='".$fecha[1]."-".$fecha[2]."')";
		//echo "(".$cumple.") UNION ALL (".$cumple_evento.") UNION ALL (".$cumple_contacto.")";
		return $this->find_all_by_sql("(".$cumple.") UNION ALL (".$cumple_evento.")");/* UNION ALL (".$cumple_contacto.")*/
	}
	public function getDoc($fecha_inicio,$fecha_fin)
	{
		$fecha=explode('-',$fecha_inicio);
		return $this->find_all_by_sql("SELECT v.id AS id, CONCAT_WS(' ',td.razonsocial,CONCAT_WS('-',CONCAT_WS(' ',d.nombre ,v.serie), v.numero )) AS title,  CONCAT_WS(' ',v.fvencimiento,'06:00:00') AS fecha_inicio,'' AS fecha_fin,'' AS url FROM tessalidas as v, tesdocumentos as d, tesdatos as td WHERE v.tesdocumentos_id=d.id AND td.id=v.tesdatos_id AND v.fvencimiento BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."' AND (v.tesdocumentos_id=34 OR v.tesdocumentos_id=37)");
	}
	/*Repeticiones*/
	public function getEventos_diarios($fecha_inicio,$fecha_fin)
	{
		/*Todos los eventos -1*/
		$fecha=explode('-',$fecha_inicio);
		return $this->find_all_by_sql("SELECT v.id as id,CONCAT_WS(' ',v.titulo,v.subtitulo)as title,CONCAT_WS(' ',v.fecha_inicio,v.hora_inicio)as fecha_inicio,CONCAT_WS(' ',v.fecha_fin,v.hora_fin)as fecha_fin,CONCAT('calendarios/eventos/ver/',id) as url FROM eventos as v WHERE v.fecha_inicio BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."' AND v.eventostipo_id!=1 AND v.repetirdiario=1");
	}
	
	public function getEventos_semanal($fecha_inicio,$fecha_fin)
	{
		/*Todos los eventos -1 v.fecha_inicio BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."' AND*/
		$fecha=explode('-',$fecha_inicio);
		return $this->find_all_by_sql("SELECT v.id as id,CONCAT_WS(' ',v.titulo,v.subtitulo)as title,CONCAT_WS(' ',v.fecha_inicio,v.hora_inicio)as fecha_inicio,CONCAT_WS(' ',v.fecha_fin,v.hora_fin)as fecha_fin,CONCAT('calendarios/eventos/ver/',id) as url FROM eventos as v WHERE v.eventostipo_id!=1 AND v.repetirsemanal=1");
	}
	public function getEventos_mensual($fecha_inicio,$fecha_fin)
	{
		/*Todos los eventos -1 v.fecha_inicio BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."' AND */
		$fecha=explode('-',$fecha_inicio);
		return $this->find_all_by_sql("SELECT v.id as id,CONCAT_WS(' ',v.titulo,v.subtitulo)as title,CONCAT_WS(' ',v.fecha_inicio,v.hora_inicio)as fecha_inicio,CONCAT_WS(' ',v.fecha_fin,v.hora_fin)as fecha_fin,CONCAT('calendarios/eventos/ver/',id) as url FROM eventos as v WHERE v.eventostipo_id!=1 AND v.repetirmensual=1");
	}
	public function getEventos_anual($fecha_inicio,$fecha_fin)
	{
		/*Todos los eventos -1*/
		$fecha=explode('-',$fecha_inicio);
		$fecha2=explode('-',$fecha_fin);
		return $this->find_all_by_sql("SELECT 'eventos' as tabla,v.id as id,CONCAT_WS(' ',v.titulo,v.subtitulo)as title,CONCAT_WS('-','".$fecha[0]."',DATE_FORMAT(v.fecha_inicio,'%m-%d')) as fecha_inicio,CONCAT_WS(' ',v.fecha_fin,v.hora_fin)as fecha_fin,CONCAT('calendarios/eventos/ver/',id)as url FROM eventos as v WHERE v.eventostipo_id!=1 AND v.repetiranual=1 AND (DATE_FORMAT(v.fecha_inicio,'%m-%d')>='".$fecha[1]."-".$fecha[2]."')");
	}
	/*Eventos sin repeticiones*/
	public function getEventos($fecha_inicio,$fecha_fin)
	{
		/*Todos los eventos -1*/
		$fecha=explode('-',$fecha_inicio);
		return $this->find_all_by_sql("SELECT v.id as id,CONCAT_WS(' ',v.titulo,v.subtitulo)as title,CONCAT_WS(' ',v.fecha_inicio,v.hora_inicio)as fecha_inicio,CONCAT_WS(' ',v.fecha_fin,v.hora_fin)as fecha_fin,CONCAT('calendarios/eventos/ver/',id) as url FROM eventos as v WHERE v.fecha_inicio BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."' AND v.eventostipo_id!=1 AND ISNULL(v.repetirdiario) AND ISNULL(v.repetirsemanal) AND ISNULL(v.repetirmensual) AND ISNULL(v.repetiranual) ");
	}
	public function getDias($fecha_inicio,$fecha_fin){
		$b=$this->find_by_sql("SELECT DATEDIFF('".$fecha_fin."','".$fecha_inicio."') as n;");
		return $b->n; 
		} 
	public function getNuevoDia($fecha_inicio,$n)
	{
		//SELECT '1997-12-31 23:59:59' + INTERVAL 3 day
		$b=$this->find_by_sql("SELECT '".$fecha_inicio."' + INTERVAL ".$n." day as dia_new");
		return $b->dia_new; 
	}
}
?>
