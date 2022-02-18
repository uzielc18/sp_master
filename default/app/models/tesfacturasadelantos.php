<?php
class Tesfacturasadelantos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesaplicacionfacturasadelantos');
		$this->belongs_to('tessalidas','tesmonedas','tesdatos');
    }
	
	public function getFacturasAdelantos($id)
	{
		$sql="SELECT a . * FROM tesfacturasadelantos AS a INNER JOIN tessalidas AS s ON s.id = a.tessalidas_id AND s.estadosalida!='ANULADO' AND s.aclempresas_id=".Session::get("EMPRESAS_ID")." WHERE a.estado =1 AND a.tesdatos_id=".$id;
		//echo $sql;
		return $this->find_all_by_sql($sql);
	}
	/*para la aplicacion*/
	public function getTotal_aplicacion($id)
	{
		$a=$this->find_by_sql('SELECT (tesfacturasadelantos.montototal-IFNULL(sum(tesaplicacionfacturasadelantos.montototal),0))as total FROM `tesfacturasadelantos` INNER JOIN tesaplicacionfacturasadelantos ON tesaplicacionfacturasadelantos.tesfacturasadelantos_id=tesfacturasadelantos.id AND tesfacturasadelantos.id='.$id);
		if(empty($a->total))$t=0;else $t=$a->total;
		return $t;
	}
	public function getTotal($id)
	{
		$a=$this->find_by_sql('SELECT (tesfacturasadelantos.montototal-sum(tesaplicacionfacturasadelantos.montototal))as total FROM `tesfacturasadelantos` INNER JOIN tesaplicacionfacturasadelantos ON tesaplicacionfacturasadelantos.tesfacturasadelantos_id=tesfacturasadelantos.id AND tesfacturasadelantos.id='.$id);
		if(empty($a->total))$t=0;else $t=$a->total;
		return $t;
	}
	public function buscar($q)
	{
		return $this->find_all_by_sql('SELECT CONCAT_WS(" ",s.serie,s.numero,f.montototal) as name,f.* FROM tesfacturasadelantos as f INNER JOIN tessalidas as s ON s.id=f.tessalidas_id
WHERE CONCAT_WS(" ",s.serie,s.numero,f.montototal) like "%'.$q.'%" AND f.estado=1');
	}
	public function getFacturasError()
	{
		return $this->find_all_by_sql("Select f.id as id,f.tessalidas_id as tessalidas_id,f.estado as estado,f.serie as serie,f.numero as numero, f.montototal as montototal,a.tesfacturasadelantos_id as tesfacturasadelantos_id,GROUP_CONCAT(concat_ws('-',a.serie,a.numero)) as allapp,sum(a.montototal) as suma FROM tesfacturasadelantos as f INNER JOIN tesaplicacionfacturasadelantos as a ON f.id=a.tesfacturasadelantos_id
WHERE f.estado in(1,2) GROUP BY a.tesfacturasadelantos_id ORDER BY f.fecha_at");
	}
	
	public function actulizar_factura($id){
		$sql="SELECT f.id as factura_id,round(f.montototal-sum(a.montototal),2) as resta FROM tesaplicacionfacturasadelantos as a INNER JOIN tesfacturasadelantos as f ON f.id=a.tesfacturasadelantos_id AND f.id=".$id;
		$factura=$this->find_by_sql($sql);
		if(!empty($factura->resta))
		{
			if($factura->resta<=0.20)
			{
				$f=$this->find_first($factura->factura_id);
				$f->estado=9;
				if($f->save())
				{
				Flash::valid("La factura se termino por que ya no tenia saldo");
				}
			}else
			{
				$f=$this->find_first($factura->factura_id);
				$f->estado=1;
				if($f->save())
				{
				Flash::valid("La factura aun esta con saldo");
				}
				
			}
		}else
		{
			Flash::info("La factura no tiene aplicaciones no tiene niguna aplicacion");
		}
	}/*recibe la factura*/
	
}
?>