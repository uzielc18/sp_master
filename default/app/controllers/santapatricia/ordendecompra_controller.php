<?php
View::template('spatricia/default');
Load::models('tesordendecompras','tesdetalleordenes','tesproductos','aclempresas','tesdatos','tesunidadesmedidas');
class OrdendecompraController extends AdminController
{
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	
	public function index()
	{
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	}
	
	public function listado($tipo='interno',$Y='',$m='')
	{
		$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
		$this->origen=$tipo;
		if($tipo=='externo') return Redirect::toAction('lis_'.$tipo."/ing/$anio/$mes_activo"); 
		Session::delete("ORDEN_ID");
		$this->tipo=$tipo;
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		Session::set('ordenorigen',$tipo);
		$this->titulo='Ordenes de Pedido Clientes';
		if($tipo=='interno'){
		$this->titulo='Ordenes de compra Interno';
		}
		$ordenes= new Tesordendecompras();
		$this->ordenes=$ordenes->find('conditions: DATE_FORMAT(fecha,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND origenorden="'.$tipo.'" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
	}
	/*pedidos de cliente*/
	public function lis_externo($estado='ing',$Y='',$m='')
	{
		$this->origen='externo';
		$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
		Session::delete("ORDEN_ID");
		$tipo='externo';
		$this->tipo=$tipo;
		$this->estado=$estado;
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		Session::set('ordenorigen',$tipo);
		$this->titulo='Ordenes de Pedido Clientes';
		if($tipo=='interno'){
		$this->titulo='Ordenes de compra Interno';
		}
		$ordenes= new Tesordendecompras();
		$this->ordenes=$ordenes->find('conditions: DATE_FORMAT(fecha,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND numero_interno!="0001" AND ubicacion="'.$estado.'" AND origenorden="'.$tipo.'" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
	}
	
	public function crear($v)
	{
	//try {
		$this->v=$v;
		$tipo=Session::get('ordenorigen');
		$this->titulo='Pedido de Clientes';
		if($tipo=='interno'){
		$this->titulo='Ordenes de compra';
		}else{}
		$this->DETALLE=FALSE;
		$this->monedas=0;
		$ORD=new Tesordendecompras();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		if($tipo=='interno'){
		$this->codigo=$ORD->generarNumero();
		$this->sufijo=date('y');
		}else
		{
		$this->codigo='';
		$this->numero_interno=$ORD->generarNumero_interno();
		$this->sufijo=date('y');
		}
		$this->monedas_nombre='SOLES';
		$this->cliente_id='';
		$this->observacion='Observacion: ';
		$this->origenorden=$tipo;
		$this->pre_descripcion='Observacion: ';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->igv=0.00;
		$this->totalconigv=0.00;
		if(Session::has('ORDEN_ID')){
		if($ORD->exists("id=".(int)Session::get('ORDEN_ID'))){
		$this->DETALLE=TRUE;
		/*
		Cargar los detalles
		*/

		$detalles=new Tesdetalleordenes();
		$orden=$ORD->find_first((int)Session::get('ORDEN_ID'));
		$this->detalles=$detalles->find('conditions: tesordendecompras_id='.$orden->id);
		$this->id=$orden->id;
		
		$this->numero_interno=$orden->numero_interno;
		$this->origenorden=$orden->origenorden;
		$this->sufijo=$orden->sufijo;
		$this->codigo=$orden->codigo;
		$this->formapago=$orden->formapago;
		$this->direccionentrega=$orden->direccionentrega;
		$this->fecha=$orden->fecha;
		$this->observacion=$orden->observacion;
		$this->pre_descripcion=$orden->observacion;
		$this->paid=$orden->descuento;
		$this->descuento=$orden->descuento;
		$this->totalconigv=$orden->totalconigv;
		$this->totalenletras=$orden->totalenletras;
		$this->igv=$orden->igv;
		$this->monedas=$orden->tesmonedas_id;
		$this->monedas_nombre=$orden->getTesmonedas()->nombre;
		$this->ubicacion=$orden->ubicacion;
		$this->cliente_id=$orden->tesdatos_id;
		$this->nombre_tesdatos=$orden->getTesdatos()->razonsocial.' ruc: '.$orden->getTesdatos()->ruc;
		
		}
		}
	
	}
	/*
	#
	# Ver proforma para editar
	#
	*/
	public function cargar($id=0,$url='crear',$v='ing')
	{
		if($id!=0){
		Session::set("ORDEN_ID",$id);
		}else
		{
			Session::delete("ORDEN_ID");
		}
		if($url=='editar')$url='crear';
		if($url=='ver'){
		return Redirect::toAction($url);
			}else{
		return Redirect::toAction($url.'/'.$v);
		}
	}
	
	public function enviar($id,$destino)
	{
		$ORD=new Tesordendecompras();
		$o=$ORD->find_first((int)$id);
		$url=$o->ubicacion;
		$o->ubicacion=$destino;
		$o->save();
		return Redirect::toAction('lis_externo/'.$url);
	}
	
	/*
	GRABAR LA PROFORMA
	###
	*/
	public function grabar($val=0)
	{
		if ($val==1)
		{
			$ORD=new Tesordendecompras();
			if($_POST['id']==''){
        	$ordens = new Tesordendecompras();
			$ordens->id=$_POST['id'];
			}else
			{
			$ordens=$ORD->find_first((int)$_POST['id']);
			}
			$ordens->tesdatos_id=$_POST['cliente_id'];
			$ordens->codigo=$_POST['numero'];
			$ordens->numero_interno=$_POST['numero_interno'];/*numero_interno*/
			$ordens->sufijo=$_POST['sufijo'];
			$ordens->tesmonedas_id=$_POST['monedas'];
			$ordens->fecha=date("Y-m-d");
			$ordens->direccionentrega=$_POST['direccionentrega'];
			$ordens->formapago=$_POST['formapago'];
			$ordens->fechaentrega=$_POST['fechaentrega'];
			$ordens->observacion=$_POST['observacion'];
			$ordens->origenorden=$_POST['origenorden'];
			$ordens->totalconigv=$_POST['totalconigv'];
			$ordens->totalenletras=$_POST['totalenletras'];
			$ordens->ubicacion=$_POST['ubicacion'];
			$ordens->igv=$_POST['igv'];
			$ordens->estado=1;
			$ordens->userid=Auth::get('id');
			$ordens->activo='1';
			$ordens->aclusuarios_id=Auth::get('id');
			$ordens->aclempresas_id=Session::get("EMPRESAS_ID");
            if ($ordens->save())
			{
				Session::set("ORDEN_ID",$ordens->id);
            	Flash::valid('La proforma fué agregada Exitosamente...!!!');
                Aclauditorias::add("Agregó Proforma {$ordens->descripcion} al sistema");
                return Redirect::toAction('respuesta/'.$ordens->id);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
             }
         }
	}
	
	/*
	GRABAR DETALLE DE LA PROFORMA
	###
	*/
	public function grabarDetalle($val=0)
	{
		View::select('respuesta');
		$this->id=0;
		if($val!=0)
		{
			$DET=new Tesdetalleordenes();
			if($_POST['id_detalle']=='0')
			{
			$detalle= new Tesdetalleordenes();
			}else{
			$detalle= $DET->find_first((int)$_POST['id_detalle']);
			}
			$detalle->tesordendecompras_id=Session::get('ORDEN_ID');
			$detalle->tescolores_id=$_POST['tescolores_id'];
			$detalle->tesunidadesmedidas_id=$_POST['tesunidadesmedidas_id'];
			$detalle->tesproductos_id=$_POST['productos_id'];
			$detalle->observaciones=$_POST['pro_descripcion'];
			$detalle->cantidad=$_POST['cantidad'];
			$detalle->precio=$_POST['precio'];
			$detalle->total=$_POST['total'];
			$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
			$detalle->estado=1;
			if($detalle->save()){
			$this->id=$detalle->id;
			}
		}
		
	}
	
	public function eliminarDetalle($val=0)
	{
		View::select('respuesta');
		$this->id=0;
		if($val!=0)
		{
			$DET=new Tesdetalleordenes();
			$DET->delete($val);
		}
	}
		
	public function respuesta($id=0)
	{
		View::template(null);
		$this->id=$id;
	}
 	
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $obj = new Tesordendecompras();
            if (!$obj ->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Area con id '{$id}'");
            }else if ($obj ->activar()) {
                Flash::valid("El Producto<b>{$obj->descripcion}</b> Esta ahora <b>Activo</b>...!!!");
                Aclauditorias::add("Colocó al Producto {$obj->descripcion} como activo", 'testproductos');
            } else {
                Flash::warning('No se pudo Activar el Producto!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado');
    }
    
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $obj  = new Tesordendecompras();
            if (!$obj ->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Producto");
            }else if ($obj ->desactivar()) {
                Flash::valid("El Producto <b>{$obj->descripcion}</b> Esta ahora <b>Inactivo</b>...!!!");
                Aclauditorias::add("Colocó el Producto {$obj->descripcion} como inactivo", 'testproductos');
            } else {
                Flash::warning('No se Pudo Desactivar el Producto...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado');
    }
	
	public function borrar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $obj  = new Tesordendecompras();

            if (!$obj ->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Producto");
            } else if ($obj ->borrar()) {
                Flash::valid("Producto <b>{$obj->nombre}</b> fué Eliminado...!!!");
                Aclauditorias::add("Producto {$obj->nombre} del sistema", 'tesproductos');
            } else {
                Flash::warning("No se Pudo Eliminar el Prodcuto <b>{$tesproductos->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado');
    }
	
	public function producto()
	{
		$orden_id=0;
		if(Session::has('ORDEN_ID'))
		{
			$orden_id=Session::get('ORDEN_ID');
		}
		$q=$_GET['q'];
		$orden= new Tesdetalleordenes();
		$obj = new Tesproductos();
		$results = $obj->find('conditions: estado=1 and aclempresas_id='.Session::get('EMPRESAS_ID').' and CONCAT_WS(" ",nombre,detalle,codigo) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			
			$ID=array();
			foreach($value->fields as $field)
			{
				$ID[$field]=$value->$field;
			}
			$id=$ID;
			//$name=$value->nombre;
			$n=(string)$value->nombrecorto." (".$value->codigo.') '.$value->detalle;
			$name=$this->clear($this->htmlcode($n));
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
			}
	}
	public function medidas()
	{
		View::select('producto');
		$q=$_GET['q'];
		$obj= new Tesunidadesmedidas();
		$results = $obj->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' and CONCAT_WS(" ",nombre,detalle) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $value->nombre;
			$this->data[] = $json;
		}
	}
	public function buscarcliente() 
	{$this->data='';
		$q=$_GET['q'];
		$obj = new Tesdatos();
		$results = $obj->find('columns:id,codigo,razonsocial,ruc,departamento,provincia,distrito,pais,direccion','conditions: testipodatos_id="2" and CONCAT_WS(" ",codigo,razonsocial,ruc,pais) like "%'.$q.'%" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
		foreach ($results as $value)
		{
			$id=$value->id;
			$name=$value->razonsocial."\n ruc: ".$value->ruc;
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$json['direccion'] = $value->direccion;
			$this->data[] = $json;
		}
    }
	public function buscarproveedor() 
	{$this->data='';
		View::select('buscarcliente');
		$q=$_GET['q'];
		$obj = new Tesdatos();
		$results = $obj->find('columns:id,codigo,razonsocial,ruc,departamento,provincia,distrito,pais,direccion','conditions: testipodatos_id="1" and CONCAT_WS(" ",codigo,razonsocial,ruc,pais) like "%'.$q.'%" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
		foreach ($results as $value)
		{
			$id=$value->id;
			$name=$value->razonsocial."\n ruc: ".$value->ruc;
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$json['direccion'] = $value->direccion;
			$this->data[] = $json;
		}
    }
	
	private function htmlcode($text)
    {
        $stvarno = array ("<", ">");
        $zamjenjeno = array ("&lt;","&gt;");
        $final = str_replace($stvarno, $zamjenjeno, $text);
        return $final;
    }
    private function clear($text)
    {
        $final = stripslashes(stripslashes( $text));
        return $final;
    }


public function entregado($v='ing')
{
	$this->estado=$v;
	$ORD=new Tesordendecompras();
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	
	/*
	Cargar los detalles
	*/
	$detalles=new Tesdetalleordenes();
	$this->orden=$ORD->find_first((int)Session::get('ORDEN_ID'));
	
	$this->detalles=$detalles->find('conditions: tesordendecompras_id='.(int)Session::get('ORDEN_ID'));
	/* AND s.tesdatos_id=2090 AND o.id=2*/
	$this->entregados_fecha=$ORD->detalles_entregados(' AND s.tesdatos_id='.$this->orden->tesdatos_id.' AND o.id='.$this->orden->id,' GROUP BY s.id');
	$this->entregados=$ORD->detalles_entregados(' AND s.tesdatos_id='.$this->orden->tesdatos_id.' AND o.id='.$this->orden->id,' GROUP BY d.tesproductos_id, s.id, d.tescolores_id');
}
public function finalizar($id)
{
	$ORD=new Tesordendecompras();
		$o=$ORD->find_first((int)$id);
		$url=$o->ubicacion;
		$o->ubicacion='TERMINADO';
		$o->save();
		return Redirect::toAction('lis_externo/'.$url);
}
	
public function ver()
{
		$ORD=new Tesordendecompras();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		
		$detalles=new Tesdetalleordenes();
		$orden=$ORD->find_first((int)Session::get('ORDEN_ID'));
		
		return Redirect::toAction($orden->origenorden);
}
public function interno()
{		$this->monedas=0;
		$ORD=new Tesordendecompras();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		
		$detalles=new Tesdetalleordenes();
		$orden=$ORD->find_first((int)Session::get('ORDEN_ID'));
		
		$this->detalles=$detalles->find('conditions: tesordendecompras_id='.$orden->id);
		$this->id=$orden->id;
		$this->orden=$orden;
		$this->codigo=$orden->codigo;
		$this->numero_interno=$orden->numero_interno;
		$this->formapago=$orden->formapago;
		$this->direccionentrega=$orden->direccionentrega;
		$this->fechaentrega=$orden->fechaentrega;
		$this->fecha=$orden->fecha;
		$this->observacion=$orden->observacion;
		$this->pre_descripcion=$orden->observacion;
		$this->paid=$orden->descuento;
		$this->subtotal=$orden->totalconigv-$orden->igv-$orden->descuento;
		$this->descuento=$orden->descuento;
		$this->totalconigv=$orden->totalconigv;
		$this->totalenletras=$orden->totalenletras;
		$this->igv=$orden->igv;
		$this->monedas=$orden->tesmonedas_id;
		$this->monedas_nombre=$orden->getTesmonedas()->nombre;
		$this->origenorden=$orden->origenorden;
}
public function externo()
{$this->monedas=0;
		$ORD=new Tesordendecompras();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		
		$detalles=new Tesdetalleordenes();
		$orden=$ORD->find_first((int)Session::get('ORDEN_ID'));
		
		$this->detalles=$detalles->find('conditions: tesordendecompras_id='.$orden->id);
		$this->id=$orden->id;
		$this->orden=$orden;
		$this->codigo=$orden->codigo;
		$this->numero_interno=$orden->numero_interno;
		$this->formapago=$orden->formapago;
		$this->direccionentrega=$orden->direccionentrega;
		$this->fechaentrega=$orden->fechaentrega;
		$this->fecha=$orden->fecha;
		$this->observacion=$orden->observacion;
		$this->pre_descripcion=$orden->observacion;
		$this->paid=$orden->descuento;
		$this->subtotal=$orden->totalconigv-$orden->igv-$orden->descuento;
		$this->descuento=$orden->descuento;
		$this->totalconigv=$orden->totalconigv;
		$this->totalenletras=$orden->totalenletras;
		$this->igv=$orden->igv;
		$this->monedas=$orden->tesmonedas_id;
		$this->monedas_nombre=$orden->getTesmonedas()->nombre;
		$this->origenorden=$orden->origenorden;
}
public function reportes()
{
	$this->estado='ing';
}
public function reporte($id)
{
	$this->estado=$v;
	$ORD=new Tesordendecompras();
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	
	/*
	Cargar los detalles
	*/
	$detalles=new Tesdetalleordenes();
	$this->orden=$ORD->find_first((int)$id);
	
	$this->detalles=$detalles->find('conditions: tesordendecompras_id='.(int)$id);
	/* AND s.tesdatos_id=2090 AND o.id=2*/
	$this->entregados_fecha=$ORD->detalles_entregados(' AND s.tesdatos_id='.$this->orden->tesdatos_id.' AND o.id='.$this->orden->id,' GROUP BY s.id');
	$this->entregados=$ORD->detalles_entregados(' AND s.tesdatos_id='.$this->orden->tesdatos_id.' AND o.id='.$this->orden->id,' GROUP BY d.tesproductos_id, s.id, d.tescolores_id');
	
	
}
public function lista()
{
	$this->data='';
	View::select('producto');
	$q=$_GET['q'];
	$obj = new Tesordendecompras();
	$results = $obj->getOrdenes($q);
		foreach ($results as $value)
		{
			$id=$value->id;
			$name=$value->razonsocial." ruc: ".$value->ruc." (".$value->codigo.') ORDEN DE COMPRA Nº '.$value->numero_interno;
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
		}
    }




}

?>