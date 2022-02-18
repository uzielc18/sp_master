<?php
class Tesletrasingresos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesaplicacionletraingresos');
		$this->belongs_to('tesingresos');
    }
	
	public function reporteFechas($cond='')
	{
		//echo "SELECT tesletrasingresos.*, WEEKOFYEAR(tesingresos.fvencimiento) as semana, tesletrasingresos.bancos as ubicacion, DATEDIFF('".date("Y-m-d")."', tesingresos.fvencimiento) AS dd FROM tesletrasingresos, tesingresos WHERE (estadoingreso!='PAGADO' AND estadoingreso!='ANULADO') AND tesletrasingresos.tesingresos_id=tesingresos.id ORDER BY tesingresos.fvencimiento, `semana` ASC";
		if($cond=='')$cond="tesingresos.estadoingreso!='PAGADO'";
		return $this->find_all_by_sql("SELECT tesletrasingresos.*, WEEKOFYEAR(tesingresos.fvencimiento) as semana, tesletrasingresos.bancos as ubicacion, DATEDIFF('".date("Y-m-d")."', tesingresos.fvencimiento) AS dd FROM tesletrasingresos, tesingresos WHERE (estadoingreso!='PAGADO' AND estadoingreso!='ANULADO') AND tesletrasingresos.tesingresos_id=tesingresos.id AND tesingresos.aclempresas_id=".Session::get('EMPRESAS_ID')." ORDER BY tesingresos.fvencimiento, `semana` ASC");
		
	}
	public function Totalletrasporsemana($n,$fecha)/*$n=Numero de semana,$fecha=numero de semana*/
	{
		$anio=date("Y", strtotime($fecha));
		
		$M=$this->find_by_sql("SELECT count(tesletrasingresos.id) as total FROM tesletrasingresos INNER JOIN tesingresos ON tesletrasingresos.tesingresos_id=tesingresos.id AND tesingresos.aclempresas_id=".Session::get('EMPRESAS_ID')." WHERE (estadoingreso!='PAGADO' AND estadoingreso!='ANULADO') AND  WEEKOFYEAR(tesingresos.fvencimiento)=".$n." AND YEAR(tesingresos.fvencimiento)='".$fecha."' GROUP BY YEAR(tesingresos.fvencimiento)");
		return $M->total;
	}
	
	
	public function getTotalAplicacion($id)/*Total de las aplicaciones recibe el id del letraingresos*/
	{
		/*SUMA LAS APLICACIONES QUE SON TIPO CARGO*/
		$d=$this->find_by_sql("SELECT IFNULL( SUM( a.monto ) , 0 ) AS descuento FROM tesletrasingresos AS l INNER JOIN tesaplicacionletraingresos AS a ON l.id = a.tesletrasingresos_id AND a.estado =1 INNER JOIN tesvauchers AS v ON v.id = a.tesvauchers_id AND v.estadov !=  'ANULADO' WHERE l.id=".$id);
		/*SUMA LAS APLICACIONES QUE SON TIPO ABONO*/
		$b=$this->find_by_sql('SELECT IFNULL(sum(a.monto),0) as sumar FROM tesletrasingresos as l INNER JOIN tesaplicacionletraingresos as a ON l.id=a.tesletrasingresos_id AND a.estado=9 INNER JOIN tesvauchers AS v ON v.id = a.tesvauchers_id
AND v.estadov !=  "ANULADO" WHERE l.id='.$id); /*esto es la suma de la aplicaciones totales*/
		/*suma de la notas de creidto aplicadas a vauchers de esta letra*/
		return $d->descuento-$b->sumar;
	}
	
	public function getLetraAdelanto($id)/*Recibe el id del vouhcers*/
	{
		$sql="SELECT li.* FROM `tesdetallevauchers` as dv, tesletrasingresos as li WHERE dv.tesvauchers_id=".$id." AND dv.abono='1' AND dv.tesingresos_id!=0 AND li.tesingresos_id=dv.tesingresos_id";
		//echo $sql;
		return $this->find_all_by_sql($sql);
		
	}
/*Recibe el id del vouhcers y el id de la letra de adelanto*/
public function getAplicacionesAnteriores($id,$letra_adelanto_id)
	{
		$sql_1="SELECT IFNULL(sum(l.monto),0) as aplicaciones FROM tesvauchers as v,tesdetallevauchers as d INNER JOIN tesaplicacionletraingresos as l ON d.tesingresos_id=l.tesingresos_id AND l.estado=1 WHERE v.estadov!='ANULADO' AND v.id=l.tesvauchers_id AND v.voucherformadepagos_id=11 AND  d.tesingresos_id=l.tesingresos_id AND v.id=d.tesvauchers_id AND l.tesletrasingresos_id=$letra_adelanto_id AND d.tesvauchers_id<".$id;
		//echo $sql_1;
		//echo '<br />';
		$d=$this->find_by_sql($sql_1);
		$sql_2="SELECT IFNULL(sum(l.monto),0) as abono FROM tesvauchers as v,tesdetallevauchers as d INNER JOIN tesaplicacionletraingresos as l ON d.tesingresos_id=l.tesingresos_id AND l.estado=9 WHERE v.estadov!='ANULADO' AND v.id=l.tesvauchers_id AND v.voucherformadepagos_id=11 AND  d.tesingresos_id=l.tesingresos_id AND v.id=d.tesvauchers_id AND l.tesletrasingresos_id=$letra_adelanto_id AND d.tesvauchers_id<".$id;
		//echo $sql_2;
		$b=$this->find_by_sql($sql_2);
		
		return $d->aplicaciones-$b->abono;
	}
	public function getTotalporsemana()
	{
		$sql="SELECT COUNT(tesingresos_id) as nletras,SUBDATE(tesingresos.fvencimiento,WEEKDAY(tesingresos.fvencimiento)) as primer_dia,ADDDATE(tesingresos.fvencimiento,6-WEEKDAY(tesingresos.fvencimiento)) as ultimo_dia,tesingresos.tesmonedas_id as tesmonedas_id,WEEK(tesingresos.fvencimiento) as semana, sum(tesletrasingresos.monto_n) as total_n , sum(tesletrasingresos.monto) as total, DATEDIFF('".date("Y-m-d")."', tesingresos.fvencimiento) AS dd, tesletrasingresos.* FROM tesletrasingresos, tesingresos WHERE tesingresos.aclempresas_id=".Session::get('EMPRESAS_ID')." AND (estadoingreso!='PAGADO' AND estadoingreso!='ANULADO') AND tesletrasingresos.tesingresos_id=tesingresos.id GROUP BY tesingresos.tesmonedas_id,semana ORDER BY tesingresos.tesmonedas_id,tesingresos.fvencimiento ASC";
		echo $sql;
		return $this->find_all_by_sql($sql);
	}
	
/*CONSULTAS PARA REPORTES*/
public function getEmitidas($Y,$m)
{
	return $this->find_all_by_sql("SELECT i.tesmonedas_id as tesmonedas_id,d.razonsocial as razonsocial,l.numerofactura as factura_id,l.numeroletra as numerodeletra, '' as nrenovacion, i.femision as femision,i.fvencimiento as fvencimiento,l.monto as monto, l.estadodeletra as estado FROM tesdatos as d, tesletrasingresos as l 
LEFT JOIN tesingresos as i ON i.id=l.tesingresos_id AND i.aclempresas_id=".Session::get('EMPRESAS_ID')."
WHERE i.tesdatos_id=d.id AND DATE_FORMAT(i.femision,'%Y-%m')='".$Y."-".$m."' ORDER BY d.razonsocial,i.femision");
}

/*NUMEROS DE FACTURAS DE LA TABLA INGRESOS PARA LETRAS POR PAGAR*/
public function getNumerofactura($id=0)
{
	$v=explode(',',$id);
	
	$fac='';
		$n=0;
		foreach($v as $item=>$value)
		{
			$f=$this->find_by_sql("select CONCAT_WS('-',serie,numero) as numero FROM tesingresos WHERE id=".$value);
			if($n==0)
			{
				$fac.='F/'.$f->numero;
			}else
			{
				$fac.=', '.$f->numero;
			}
			$n++;
		}
		return $fac;
}
/*letras ingresos*/
public function get_letras($ids,$anio,$estado)
{
	//$sql="SELECT  l.*,'RUC' AS tipo, l.numeroletra, i.femision, i.fvencimiento, l.bancos, l.monto, i.estadoingreso, l.estadodeletra, l.numerounico FROM tesletrasingresos as l INNER JOIN tesingresos as i ON i.id=l.tesingresos_id AND i.aclempresas_id=".Session::get("EMPRESAS_ID")." AND i.tesdatos_id = ".$ids." order by i.femision DESC";
	$sql="SELECT  i.*,'RUC' AS tipo, l.numeroletra, i.femision, i.fvencimiento, l.bancos, l.monto, i.estadoingreso, l.estadodeletra, l.numerounico FROM tesletrasingresos as l INNER JOIN tesingresos as i ON i.id=l.tesingresos_id AND i.aclempresas_id=".Session::get("EMPRESAS_ID")." AND l.estadodeletra='".$estado."' AND i.tesdatos_id = ".$ids." AND DATE_FORMAT(i.femision,'%Y')=".$anio." order by i.femision DESC";
	//echo $sql;
	return $this->find_all_by_sql($sql);
}

}
?>