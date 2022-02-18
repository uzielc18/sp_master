<?php
Load::models('prodetalleproduccion','aclusuarios');
class Prorollos extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('prodetalleprocesos','prorollos');
		$this->belongs_to('prodetalleproduccion','tesproductos','tescolores','tesordendecompras','prorollos');
    }
	public function before_validation_on_create() {
        if(Session::has('PRODUCCION_ID')){
		/*$this->validates_uniqueness_of('codigo', 'message: El <b>Codigo</b> ya está siendo utilizado');*/
		$this->validates_uniqueness_of('numero', 'message: El <b>Numero</b> ya está siendo utilizado');
		}
    }
	/*public function before_update() {            
           if(!empty($this->numero)){
		   $rs = $this->exists("numero=$this->numero AND id!=$this->id");
           if($rs){
                   Flash::warning("Ya existe un Rollo registrado con este número");
                   return 'cancel';
           }
		   }
       }*/
	public function getUsuarios()
	{
		$usu= new Aclusuarios();
		return $usu->find_first($this->userid);
	}
	public function codigo($id=0)
	{
		if($id!=0)
		{
		$Detalle= new Prodetalleproduccion();
		$detalle=$Detalle->find_first((int)$id);
		
		if(empty($detalle->getTesproductos()->codigo_ant))
		{
			$maximo=$detalle->getTesproductos()->codigo;
		}else
		{
			$maximo=$detalle->getTesproductos()->codigo_ant;
		}
		
		}
		return $maximo;
	}
	public function numero()
	{
		$maximos = $this->find_by_sql('SELECT (IFNULL(MAX(numero),0)) as numero FROM `prorollos` WHERE ISNULL(prorollos_id)');
		$maximo=$maximos->numero+1;
		
		return $maximo;
	}
	
	public function numero_venta($id)
	{
		//echo 'SELECT (IFNULL(MAX(numeroventa),0)) as numero FROM `prorollos` WHERE tesproductos_id='.$id;
		$maximos = $this->find_by_sql('SELECT (IFNULL(MAX(numeroventa),0)) as numero FROM `prorollos` WHERE tesproductos_id='.$id);
		$maximo=$maximos->numero+1;
		return $maximo;
	}
	public function getRollosSalidas($numeroguia)
	{
		//echo $numeroguia;
		$SG=explode(',',$numeroguia);
		$code='';
		$union='';
		$n=0;
		foreach($SG as $value=>$item)
		{
			if($n!=0)$union=' UNION ';
			$SN=explode('-',$item);
			$code.=$union.'SELECT r.*, sd.id as detallessalidas_id FROM prorollos as r, prodetalleprocesos as pr, tessalidas as s, tesdetallesalidas as sd WHERE sd.tessalidas_id=s.id AND r.id=sd.prorollos_id AND r.id=pr.prorollos_id AND pr.estado=1 AND s.serie='.$SN[0].' AND s.numero='.$SN[1].' group by r.id';
		$n++;
		}
		//echo $code;
		return $this->find_all_by_sql($code);
		/*return $this->find_all_by_sql('SELECT r.*, sd.id as detallessalidas_id FROM prorollos as r, prodetalleprocesos as pr, tessalidas as s, tesdetallesalidas as sd WHERE sd.tessalidas_id=s.id AND r.id=sd.prorollos_id AND r.id=pr.prorollos_id AND pr.estado=1 AND s.serie='.$serie.' AND s.numero='.$numero);*/
	}
	public function getRollosSalidas_count($numeroguia)
	{
		//echo 'SELECT count(r.id) as c FROM prorollos as r, prodetalleprocesos as pr, tessalidas as s, tesdetallesalidas as sd WHERE sd.tessalidas_id=s.id AND r.id=sd.prorollos_id AND r.id=pr.prorollos_id AND pr.estado=1 AND s.serie='.$serie.' AND s.numero='.$numero;
		$SG=explode(',',$numeroguia);
		$VALIDAR=0;
		foreach($SG as $value=>$item)
		{
			$SN=explode('-',$item);
			$rr=$this->find_by_sql('SELECT count(r.id) as c FROM prorollos as r, prodetalleprocesos as pr, tessalidas as s, tesdetallesalidas as sd WHERE sd.tessalidas_id=s.id AND r.id=sd.prorollos_id AND r.id=pr.prorollos_id AND pr.estado=1 AND s.serie='.$SN[0].' AND s.numero='.$SN[1]);
			if($rr->c==0){
			$salidas= new Tessalidas();
			$salida=$salidas->find_first('conditions: serie='.$SN[0].' AND numero='.$SN[1]);
			$s=$salidas->find_first((int)$salida->id);
			$s->estadosalida="TERMINADO";
			$s->save();
			$VALIDAR++;
			}else
			{
			Flash::info('La guia Nº '.$SN[0].'-'.$SN[1].' aun no se termino de ingresar');
			}
		}
		//echo $rr->c;
		return $rr->c; 
	}
	/**/
	public function getRollostintoreria()
	{
		return $this->find_all_by_sql("SELECT r.*, CONCAT_WS('-',s.serie,s.numero) as guia, d.razonsocial as proveedor  FROM prorollos as r, tesdetallesalidas as sd, tessalidas as s , tesdatos as d WHERE s.id=sd.tessalidas_id AND sd.prorollos_id=r.id AND s.tesdatos_id=d.id AND r.estadorollo='Tintoreria'");
	}
	public function getRollosreprocesos()
	{
		return $this->find_all_by_sql("SELECT r.*, CONCAT_WS('-',s.serie,s.numero) as guia, d.razonsocial as proveedor, pr.detalle as observacion  FROM prorollos as r, tesdetallesalidas as sd, tessalidas as s , tesdatos as d, proprocesos as pr WHERE s.id=sd.tessalidas_id AND sd.prorollos_id=r.id AND s.tesdatos_id=d.id AND (r.estadorollo='Tintoreria-2' OR  estadorollo='Re-Proceso') AND  s.id=pr.tessalidas_id");
	}
	/*
	Contar los numeros de reprocesos
	*/
	public function getNprocesos($id)
	{
	
		$val=$this->find_by_sql('SELECT COUNT(id) AS n FROM prodetalleprocesos WHERE prorollos_id='.$id);
		//echo 'el valor de n es : '.$val->n.' *';
		switch($val->n)
		{
			case '0': $c='A';break;
			case '1': $c='A';break;
			case '2': $c='C';break;
			case '3': $c='D';break;
			case '4': $c='E';break;
			default: $c='X';
		}
		return $c;
	}
	public function Lote($id)/*recibe el id del rollo*/
	{
		$val=$this->find_by_sql("SELECT p.codigo AS codigo FROM proproduccion AS p, prodetalleproduccion AS d, prorollos AS r WHERE p.id = d.proproduccion_id AND d.id = r.prodetalleproduccion_id AND r.id =10 LIMIT 0 , 1");
		return $val->codigo;
	}
	
	/*especiales*/
	
	public function getProductoslinatelas()
	{
		$productos= Load::model('tesproductos');
		$pp=$productos->find_all_by_sql('SELECT p.* FROM tesproductos as p, testipoproductos as tp WHERE p.testipoproductos_id=tp.id AND ('.$this->obtener_tipo_productos().')');
		
		$r=array();
		foreach($pp as $p)
		{
			$r[$p->id]=$p->nombre.$p->codigo_ant;
		}
		return $r;

	}
	protected function obtener_tipo_productos()
	{
        $obj = Load::model('testipoproductos');
		$tipos=$obj->find('conditions: estado=1 AND activo=1 AND teslineaproductos_id=1');
        $tipos_pro = array();
            foreach ($tipos as $e) {
                $tipos_pro[] = "tp.id = '$e->id'";
            }
        return join(' OR ', $tipos_pro);
    }
	
	public function getRollosporestado($e,$ancho)
	{//TR
		$cond=' AND r.estadorollo="'.$e.'"';
		$cond2=' AND r.ancho="'.$ancho.'" ';
		if($e=='')$cond='';
		if($ancho=='')$cond2='';
	if($e=='Sin procesos' || $e=='TERMINADO' || $e=='TR'){
		
		$sql='SELECT r.tescolores_id as tescolores_id,NULL as color_venta,p.id as id, p.codigo as codigo, p.codigo_ant as codigo_ant, CONCAT_WS(" ",p.nombre,p.detalle) as descripcion, CONCAT_WS("-",r.codigo) as piezza,r.numeroventa as nventa,r.calidad as calidad, r.fechacorte as fecha, r.metros as metros , r.peso as kilos, r.estadorollo as estadorollo, r.ordencompra as odencompra,r.ancho as ancho,r.anchoutil as anchoutil FROM tesproductos as p INNER JOIN prorollos as r ON p.id = r.tesproductos_id'.$cond.$cond2.' order by p.nombre,r.color,r.numeroventa ASC';
	}else{
		
		$sql='SELECT r.tescolores_id as tescolores_id,r.color as color_venta,p.id as id, p.codigo as codigo, p.codigo_ant as codigo_ant, CONCAT_WS(" ",p.nombre,p.detalle) as descripcion, CONCAT_WS("-",r.codigo) as piezza,r.numeroventa as nventa,r.calidad as calidad, r.fechacorte as fecha, r.metros as metros , r.peso as kilos, r.estadorollo as estadorollo, r.ordencompra as rodencompra,r.ancho as ancho,r.anchoutil as anchoutil  FROM tescolores as c, tesproductos as p INNER JOIN prorollos as r ON p.id = r.tesproductos_id'.$cond.$cond2.' WHERE (c.id=r.tescolores_id) order by p.nombre,r.color,r.numeroventa ASC';
	
	}
	//echo $sql;
	return $this->find_all_by_sql($sql);
	
	}
	
	/* el $id es para el reporte de impresion de rollos*/
	public function getVentas($id=0)
	{
		$uno='';
		if($id!=0)$uno=' AND r.tesproductos_id='.$id;
		
		return $this->find_all_by_sql("SELECT c.id as color_id,c.nombre as color_venta, r.* FROM prorollos as r,prodetalleprocesos as dp, proprocesos as p, tescolores as c
WHERE c.id=p.tescolores_id AND dp.id=(select max(id) from prodetalleprocesos WHERE prorollos_id=r.id) AND p.id=dp.proprocesos_id AND dp.prorollos_id=r.id AND r.estadorollo='VENTA'".$uno."
GROUP BY r.id ORDER BY r.tescolores_id,r.codigo,r.numeroventa ASC");
	}
	/* Es para el listado de sin procesos de reporte de rollos*/
	
	public function getVentas_rollos($id)
	{
		$sql='SELECT NULL as color_venta,p.id as id, p.codigo as codigo, p.codigo_ant as codigo_ant, CONCAT_WS(" ",p.nombre,p.detalle) as descripcion, CONCAT_WS("-",r.codigo) as piezza,r.numeroventa as nventa,r.calidad as calidad, r.fechacorte as fecha, r.metros as metros , r.peso as kilos, r.estadorollo as estadorollo, r.ordencompra as rodencompra, r.* FROM tesproductos as p INNER JOIN prorollos as r ON p.id = r.tesproductos_id AND r.estadorollo="VENTA" AND r.tesproductos_id='.$id.' order by r.tescolores_id,r.numeroventa,p.nombre ASC';
		//echo $sql;
		return $this->find_all_by_sql($sql);
	}
	
	public function getSinProcesos($id)
	{
		return $this->find_all_by_sql('SELECT r.tescolores_id as tescolores_id,NULL as color_venta,p.id as id, p.codigo as codigo, p.codigo_ant as codigo_ant, CONCAT_WS(" ",p.nombre,p.detalle) as descripcion, CONCAT_WS("-",r.codigo) as piezza,r.numeroventa as nventa,r.calidad as calidad, r.fechacorte as fecha, r.metros as metros, r.peso as kilos, r.estadorollo as estadorollo, r.ordencompra as rodencompra, r.* FROM tesproductos as p INNER JOIN prorollos as r ON p.id = r.tesproductos_id AND r.estadorollo="Sin procesos" AND r.tesproductos_id='.$id.' order by p.nombre,r.color,r.numeroventa ASC');
	}
	public function getRollos_tintoreria($id)
	{
		$sql="SELECT r.color as color_venta, r.id as id, r.numero as codigo_t, p.codigo_ant as codigo_ant, p.nombre as descripcion, CONCAT_WS('-',r.codigo) as piezza,r.numeroventa as nventa,r.calidad as calidad, r.fechacorte as fecha, r.metros as metros , r.peso as kilos, r.estadorollo as estadorollo, r.ordencompra as rodencompra, (SELECT CONCAT_WS(' ',CONCAT_WS('-',tessalidas.serie,tessalidas.numero),concat_ws('',tesdatos.razonsocial,' Fecha: ',tessalidas.femision,' Pedido:',tessalidas.npedido)) as detalle FROM tesdetallesalidas INNER JOIN tessalidas ON tessalidas.id=tesdetallesalidas.tessalidas_id AND tessalidas.estadosalida='Enviado' INNER JOIN tesdatos ON tesdatos.id=tessalidas.tesdatos_id WHERE tesdetallesalidas.prorollos_id=r.id) as resumen ,r.* FROM tesproductos as p INNER JOIN prorollos as r ON p.id = r.tesproductos_id AND (r.estadorollo='Tintoreria' OR r.estadorollo='Tintoreria-2' OR r.estadorollo='Re-Proceso') AND r.tesproductos_id=".$id." order by r.tescolores_id,r.numeroventa,p.nombre ASC
";
		//echo $sql;
		return $this->find_all_by_sql($sql);
	}
	
	public function getRollos_listado($id)
	{
		/*r.enalmacen=1 AND r.estadorollo='VENTA'*/
		return $this->find_all_by_sql("SELECT r.color as color, r.id as id,r.fechacorte as fecha,r.estadorollo as estadorollo,IFNULL(p.codigo_ant,p.codigo) as articulo,IFNULL(p.nombre,p.detalle) as descripcion,r.numero,r.codigo,concat_ws('-',r.numeroventa,r.maquina_numero) as pieza,r.metros as metros,r.peso as kilos, r.ordencompra as oc,r.ancho as ancho,r.anchoutil as anchoutil,r.ancho as ancho,r.anchoutil as anchoutil FROM prorollos as r,tesproductos as p WHERE (r.estadorollo!='VENDIDO' AND r.estadorollo!='TERMINADO') AND  r.tesproductos_id=p.id AND p.id=".$id." ORDER BY r.estadorollo ASC");
	}
	public function getRollos_producion($id)
	{
		return $this->find_all_by_sql("SELECT pr.guianumero as guia,pr.id as produccion_id,d.id as id,DATE_FORMAT(d.fecha_at,'%Y-%m-%d') as fecha,IFNULL(p.nombre,p.detalle) as descripcion,'' as numeroderollo,m.nombre as maquina,'Sin corte' as estadorollo,d.metroprogramados as metros, '0.00' as kilos FROM prodetalleproduccion as d, tesproductos as p,proproduccion as pr,promaquinas as m
WHERE d.proproduccion_id=pr.id AND pr.promaquinas_id=m.id AND isnull(d.fechadecorte) AND p.id = d.tesproductos_id AND d.tesproductos_id=".$id);
	}
	
	public function ControlVenta($ancho='')
	{
		$cond2=' AND r.ancho="'.$ancho.'" ';
		if($ancho=='')$cond2='';
		return $this->find_all_by_sql('SELECT r.* FROM prorollos as r INNER JOIN tesproductos as p ON p.id=r.tesproductos_id AND r.estadorollo="VENTA"'.$cond2.' ORDER BY p.nombre ASC');
	}
	
	
	public function SinDatos()
	{
		return $this->find_all_by_sql('SELECT r.* FROM prorollos as r INNER JOIN tesproductos as p ON p.id=r.tesproductos_id AND isnull(r.estadorollo) ORDER BY p.nombre ASC');
	}
	public function getMetros($estado)
	{
		return $this->find_all_by_sql('SELECT distinct ancho as ancho, ancho as id  FROM `prorollos` WHERE estadorollo="'.$estado.'" group by ancho ORDER BY `prorollos`.`ancho`  DESC');
	}
	/*SELECT DISTINCT estadorollo FROM `prorollos`*/
	public function getEstados()
	{
		return $this->find_all_by_sql('SELECT DISTINCT estadorollo as estadorollo, estadorollo as id FROM `prorollos` ORDER BY `prorollos`.`estadorollo`  DESC');
	}
}
?>