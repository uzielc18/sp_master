<?php 
View::template('spatricia/default');
Load::models('pronotapedidos','prodetallepedidos','tesproductos','aclempresas','acldatos','protrama','tescajas','procajastrama','tesdetalleingresos','tesdocumentos','tesingresos','promaquinas');

class NotadepedidoController extends AdminController
{
protected function before_filter()
{
	if ( Input::isAjax() ){
		View::template(null);
		//View::select(NULL, NULL);
	}
}
public function index($value)
{
	$this->value=$value;
	/*salida*//*ingreso*/
	Session::set('tipo_nota',''.$value.'');
	
	//return Redirect::toAction('listado');
}
public function listado($origen='Otros',$estado='Sin enviar')
{
	if($origen=="Produccion")return Redirect::toAction('produccion/'.$origen.'/'.$estado);
	Session::delete('DOC_ID');
	Session::delete('DOC_NOMBRE');
	Session::delete('DOC_ABR');
	Session::delete('DOC_CODIGO');
	Session::delete("INGRESO_ID");
	$this->origen=$origen;
	Session::delete('NOTA_ID');
	$obj= new Pronotapedidos();
	/*
	validar de acuerdo al rol el listado de la nota
	*/
	if($origen=='Chenille' && Session::get('tipo_nota')=='ingreso'){
	
	$ingresos= new Tesingresos();
	$campos = 'tesingresos.' . join(',tesingresos.', $ingresos->fields)."";
	 $this->notas= $ingresos->find('conditions: pr="NN" AND tesdatos_id=2600 AND estado=1 AND tesdocumentos_id=15 AND aclempresas_id='.Session::get('EMPRESAS_ID'), "columns: $campos",'order: numero DESC');
	}else
	{
		/* pronotapedidos.estadonota!="Entregado" AND */
	$condiciones='conditions: pronotapedidos.aclempresas_id='.Session::get('EMPRESAS_ID').' AND pronotapedidos.tipo="'.Session::get('tipo_nota').'" AND pronotapedidos.origen="'.$origen.'"';
	$campos = 'pronotapedidos.' . join(',pronotapedidos.', $obj->fields) . ",(IFNULL(sum(r.cantidad),0)) as total";
    $join = 'LEFT JOIN prodetallepedidos as r on r.pronotapedidos_id=pronotapedidos.id';
    $agrupar_por = 'pronotapedidos.id';
	$this->notas=$obj->find($condiciones, "join: $join", "columns: $campos", "group: $agrupar_por",'order: codigo DESC');
	}
}
public function produccion($origen,$estado='pendiente',$Y='',$m='')
{
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	$this->estado=$estado;
	$this->origen=$origen;
	$obj= new Pronotapedidos();
	$this->estados=$obj->find('columns: estadonota','group: estadonota','order: estadonota');
	$c_fecha='';
	$c_limit='';
	$c_fecha=' DATE_FORMAT(fecha,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND ';
	/* pronotapedidos.estadonota!="Entregado" AND */
	$condiciones='conditions: '.$c_fecha.'pronotapedidos.aclempresas_id='.Session::get('EMPRESAS_ID').' AND pronotapedidos.tipo="'.Session::get('tipo_nota').'" AND pronotapedidos.origen="Produccion" AND pronotapedidos.estadonota="'.$estado.'"';
	$campos = 'pronotapedidos.' . join(',pronotapedidos.', $obj->fields) . ",(IFNULL(sum(r.cantidad),0)) as total";
    $join = 'LEFT JOIN prodetallepedidos as r on r.pronotapedidos_id=pronotapedidos.id';
    $agrupar_por = 'pronotapedidos.id';
	$this->notas=$obj->find($condiciones, "join: $join", "columns: $campos", "group: $agrupar_por",'order: codigo ASC');

}

public function grabar()
{
	$this->v=0;
	if($_POST['id']==0){
	$obj= new Pronotapedidos();
	$obj->aclusuarios_id=Auth::get('id');
	}else
	{
	$OBJ= new Pronotapedidos();
	$obj=$OBJ->find_first((int)$_POST['id']);
	}
	if(isset($_POST['tesdatos_id']))$obj->tesdatos_id=$_POST['tesdatos_id'];
	if(isset($_POST['codigo']))$obj->codigo=$_POST['codigo'];
	if(isset($_POST['descripcion']))$obj->descripcion=$_POST['descripcion'];
	if(isset($_POST['observacions']))$obj->observacion=$_POST['observacions'];
	if(isset($_POST['estadonota']))$obj->estadonota=$_POST['estadonota'];
	$obj->fecha=date("Y-m-d");
	$obj->estado=1;
	$obj->activo=1;
	$obj->aclempresas_id=Session::get('EMPRESAS_ID');
	$obj->tipo=Session::get('tipo_nota');
	if(isset($_POST['origen']))$obj->origen=$_POST['origen'];
	if($obj->save()){
	$this->v=$obj->id;
	Session::set('NOTA_ID',$obj->id);
	}
}	
public function grabardetalle()
{
	View::select('grabar');
	
	$this->v=0;
	if(Session::get('NOTA_ID')!=0){
	if($_POST['id_detalle']==0){
	$obj=$OBJ= new Prodetallepedidos();
	}else
	{
	$OBJ= new Prodetallepedidos();
	$obj=$OBJ->find_first((int)$_POST['id_detalle']);
	}
	$obj->pronotapedidos_id=Session::get('NOTA_ID');
	if(isset($_POST['tesproductos_id']))$obj->tesproductos_id=$_POST['tesproductos_id'];
	if(isset($_POST['tescolores_id']))$obj->tescolores_id=$_POST['tescolores_id'];
	if(isset($_POST['descripcion']))$obj->descripcion=$_POST['descripcion'];
	if(isset($_POST['cantidad']))$obj->cantidad=$_POST['cantidad'];
	if(isset($_POST['cantidad_p']))$obj->cantidad_p=$_POST['cantidad_p']; else $obj->cantidad_p=$_POST['cantidad'];
	if(isset($_POST['tesunidadesmedidas_id']))$obj->tesunidadesmedidas_id=$_POST['tesunidadesmedidas_id'];
	if(isset($_POST['tescajas_id']))$obj->tescajas_id=$_POST['tescajas_id'];
	if(isset($_POST['lote']))$obj->lote=$_POST['lote'];
	if(isset($_POST['precio'])){if($_POST['precio']=='1')$obj->precio=$OBJ->getPrecio_ingreso($obj->tesproductos_id,$obj->tescolores_id,$obj->lote);}
	$obj->activo=1;
	$obj->estado=1;
	if($obj->save())
	{
		$this->v=$obj->id;
		$trama= new Protrama();
		if($trama->exists('prodetallepedidos_id='.$obj->id))
		{
			$trama->find_first('prodetallepedidos_id='.$obj->id);
			$trama->cantidadprogramada=$obj->cantidad;
			$trama->tescolores_id=$obj->tescolores_id;
			$trama->save();
		}
	}
	}
}

public function eliminarDetalle($val=0)
	{
		View::select('grabar');
		$this->v=0;
		if($val!=0)
		{
			$DET=new Prodetallepedidos();
			$DET->delete($val);
		}
	}
public function eliminar($val=0,$origen='Produccion',$estado='')
	{
		View::select('grabar');
		$this->v=0;
		if($val!=0)
		{
			$NO=new Pronotapedidos();
			$DET=new Prodetallepedidos();
			if($DET->exists('pronotapedidos_id='.$val)){
				$DET->delete('pronotapedidos_id='.$val);
			}
			$NO->delete($val);
		}
		return Redirect::toAction('listado/'.$origen.'/'.$estado);
	}
public function cargarnota($id=0,$val='crear',$origen='Otros')
{
	if($id!=0){
	$Npedidos= new Pronotapedidos();
	$nota=$Npedidos->find_first((int)$id);
	Session::set('NOTA_ID',$nota->id);
	}else
	{
	Session::set('NOTA_ID',$id);
	}
	return Redirect::toAction($val.'/'.$origen);
}

public function crearm($origen='Otros')
{
	$this->origen=$origen;
	$this->titulo='NOTA DE PEDIDO PARA MUESTRA';
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	
	$datos= new Acldatos();
	$this->dato=$datos->find_first((int)Auth::get('id'));
	if(Session::get('NOTA_ID')!=0)
	{
	$id=Session::get('NOTA_ID');
	$Npedidos= new Pronotapedidos();
	$Ndetalles= new Prodetallepedidos();
	$pedido=$Npedidos->find_first((int)$id);
	$this->pedido=$pedido;
	$this->detalles=$Ndetalles->find('conditions: pronotapedidos_id='.(int)$id);
	$this->id=$id;
	$this->codigo=$pedido->codigo;
	$this->descripcion=$pedido->descripcion;
	$this->observacions=$pedido->observacion;
	$this->estadonota=$pedido->estadonota;
	$this->tipo=$pedido->tipo;
	$this->origen=$pedido->origen;
	if(!empty($pedido->tesdatos_id)){
	$this->tesdatos_id=$pedido->tesdatos_id;
	$this->cliente=$pedido->getTesdatos()->razonsocial.' ruc: '.$pedido->getTesdatos()->ruc.' ('.$pedido->getTesdatos()->departamento.' '.$pedido->getTesdatos()->provincia.' '.$pedido->getTesdatos()->distrito.' '.$pedido->getTesdatos()->direccion.')';
	}else
	{
		$this->tesdatos_id=NULL;
		$this->cliente=NULL;
	}
	$this->DETALLE=TRUE;
	}else
	{
	$Npedidos= new Pronotapedidos();
	$this->DETALLE=FALSE;
	$this->id=0;
	$this->codigo=$Npedidos->codigo(Session::get('tipo_nota'));
	$this->descripcion='';
	$this->observacions='';
	$this->estadonota='';
	$this->tipo='';
	$this->origen='Muestra';
	}
}

public function crear($origen='')
{
	$this->origen=$origen;
	$this->titulo='NOTA DE PEDIDO';
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	
	$datos= new Acldatos();
	$this->dato=$datos->find_first((int)Auth::get('id'));
	if(Session::get('NOTA_ID')!=0)
	{
	$id=Session::get('NOTA_ID');
	$Npedidos= new Pronotapedidos();
	$Ndetalles= new Prodetallepedidos();
	$pedido=$Npedidos->find_first((int)$id);
	$this->pedido=$pedido;
	$this->detalles=$Ndetalles->find('conditions: pronotapedidos_id='.(int)$id);
	$this->id=$id;
	$this->codigo=$pedido->codigo;
	$this->descripcion=$pedido->descripcion;
	$this->observacions=$pedido->observacion;
	$this->estadonota=$pedido->estadonota;
	$this->tipo=$pedido->tipo;
	$this->origen=$pedido->origen;
	$this->DETALLE=TRUE;
	}else
	{
	$Npedidos= new Pronotapedidos();
	$this->DETALLE=FALSE;
	$this->id=0;
	$this->codigo=$Npedidos->codigo(Session::get('tipo_nota'));
	$this->descripcion='';
	$this->observacions='';
	$this->estadonota='';
	}
}
/*
CREAR SALIDA A CHENILLE DE dos tipos de hilo solo dos 
*/
public function crear_s_ch($origen='')
{
	$this->origen=$origen;
	$this->titulo='NOTA DE PEDIDO';
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	
	$datos= new Acldatos();
	$this->dato=$datos->find_first('conditions: aclusuarios_id='.(int)Auth::get('id'));
	if(Session::get('NOTA_ID')!=0)
	{
	$id=Session::get('NOTA_ID');
	$Npedidos= new Pronotapedidos();
	$Ndetalles= new Prodetallepedidos();
	$pedido=$Npedidos->find_first((int)$id);
	$this->pedido=$pedido;
	$this->detalles=$Ndetalles->find('conditions: pronotapedidos_id='.(int)$id);
	$this->id=$id;
	$this->codigo=$pedido->codigo;
	$this->descripcion=$pedido->descripcion;
	$this->observacions=$pedido->observacion;
	$this->estadonota=$pedido->estadonota;
	$this->tipo=$pedido->tipo;
	$this->origen=$pedido->origen;
	$this->DETALLE=TRUE;
	}else
	{
	$Npedidos= new Pronotapedidos();
	$this->DETALLE=FALSE;
	$this->id=0;
	$this->codigo=$Npedidos->codigo(Session::get('tipo_nota'));
	$this->descripcion='';
	$this->observacions='';
	$this->estadonota='';
	}
}
public function ver($origen='')
{
	$this->origen=$origen;
	$id=Session::get('NOTA_ID');
	$this->titulo='NOTA DE PEDIDO';
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	$Npedidos= new Pronotapedidos();
	$Ndetalles= new Prodetallepedidos();
	$this->pedido=$Npedidos->find_first((int)$id);
	$this->detalles=$Ndetalles->find('conditions: pronotapedidos_id='.(int)$id);
	$this->entregados=$Ndetalles->getEntrega($id);
}
public function imprimir($origen='Produccion')
{
	$this->origen=$origen;
	$id=Session::get('NOTA_ID');
	$this->titulo='NOTA DE PEDIDO';
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	$Npedidos= new Pronotapedidos();
	$Ndetalles= new Prodetallepedidos();
	$ids=Input::post('ids');
	//$this->pedido=$Npedidos->find_first((int)$id);
	$this->detalles=$Ndetalles->getPedidos($origen,$ids);
	//$this->entregados=$Ndetalles->getEntrega($id);
}
public function pdf($id)
{
	View::template(null);
	$id=$id;
	$this->titulo='NOTA DE PEDIDO';
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	$Npedidos= new Pronotapedidos();
	$Ndetalles= new Prodetallepedidos();
	$this->pedido=$Npedidos->find_first((int)$id);
	$this->detalles=$Ndetalles->find('conditions: pronotapedidos_id='.(int)$id);
}

public function revisar($origen='',$id_t_p=0)
{
	$this->origen=$origen;
	$id=Session::get('NOTA_ID');
	$this->titulo='NOTA DE PEDIDO';
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	$Npedidos= new Pronotapedidos();
	$Ndetalles= new Prodetallepedidos();
	$this->pedido=$Npedidos->find_first((int)$id);
	$tipos=new Testipoproductos();
	$this->tipos=$tipos->find('conditions: teslineaproductos_id=3');
	$cajas=new Tescajas();
	
	$this->cajas=$cajas->getStokdecajas($id_t_p);
	$this->id_t_p=$id_t_p;
	$maquinas=new Promaquinas();
	$this->maquinas=$maquinas->find("conditions: prefijo='M'");
	
	//$campos = 'prodetallepedidos.' . join(',prodetallepedidos.', $Ndetalles->fields) . ',sum(prodetallepedidos.cantidad) as totalkilos';
	//$this->detalles=$Ndetalles->find('columns: '.$campos,'conditions: pronotapedidos_id='.(int)$id);
	$this->detalles=$Ndetalles->find('conditions: pronotapedidos_id='.(int)$id);
}
/*Entregar por cajas*/
public function entregar($origen='')
{
	$this->origen=$origen;
	$id=Session::get('NOTA_ID');
	$this->titulo='NOTA DE PEDIDO';
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	$Npedidos= new Pronotapedidos();
	$Ndetalles= new Prodetallepedidos();
	$this->pedido=$Npedidos->find_first((int)$id);
	
	//$campos = 'prodetallepedidos.' . join(',prodetallepedidos.', $Ndetalles->fields) . ',sum(prodetallepedidos.cantidad) as totalkilos';
	//$this->detalles=$Ndetalles->find('columns: '.$campos,'conditions: pronotapedidos_id='.(int)$id);
	$this->detalles=$Ndetalles->find('conditions: pronotapedidos_id='.(int)$id);
}
public function procajastrama_entregar($id,$p_id,$color_id)
{
	$this->id=$id;
	$this->p_id=$p_id;
	$this->color_id=$color_id;
	if(Input::post('procajastrama'))
	{
		$procajastrama=new Procajastrama(Input::post('procajastrama'));
		$procajastrama->aclusuarios_id=Auth::get('id');
		$procajastrama->estado=1;
		if($procajastrama->save())
		{
			$cajas_nuevas='';
			$DET=new Prodetallepedidos();
			$this->detalle_pedido=$det=$DET->find_first((int)$id);
			//$cajas_anterior=$det->tescajas_id;
			//if(empty($cajas_anterior))$cajas_nuevas=$procajastrama->tescajas_id;
			//if(!empty($cajas_anterior))$cajas_nuevas=$cajas_anterior.','.$procajastrama->tescajas_id;
			/*Camibar de estado la caja enviada a cajastrama de 1 a 9*/
			$UPDATE_CAJA=new Tescajas();
			$UPDATE_CAJA->find_first($procajastrama->tescajas_id);
			$UPDATE_CAJA->estado=9;
			$UPDATE_CAJA->save();
			/*Fin de Actualizar caja*/
			//$det->tescajas_id=$cajas_nuevas;
			$det->save();
			Input::delete('procajastrama');
			return Redirect::toAction('procajastrama_entregar/'.$id.'/'.$p_id.'/'.$color_id);
		}
	}
	if(Input::hasPost('detalle_pedido'))
	{
		$detalles=new Prodetallepedidos(Input::post('detalle_pedido'));
		$detalles->save();
		return Redirect::toAction('entregar/'.$detalles->getPronotapedidos()->origen);
	}
	$procajast=new Procajastrama();
	$this->cajas=$procajast->find('conditions: prodetallepedidos_id='.$id);
	$detalles=new Prodetallepedidos();
	$de=$this->detalle_pedido=$detalles->find_first((int)$id);
	
	$deta=$detalles->find('conditions: pronotapedidos_id='.Session::get('NOTA_ID').' AND tesproductos_id='.$p_id.' AND tescolores_id='.$color_id.' AND lote="'.$de->lote.'"');
	$t=0;
	foreach($deta as $dt)
	{
		$t=$t+$dt->cantidad;
	}
	$this->total=$t;
	$obj= new Tescajas();
	$this->CAJAS = $obj->buscarcaja($de->tesproductos_id,$de->tescolores_id,0);
}
public function eliminar_cajastrama_entregar($val,$id,$p_id,$color_id)
{
	$procajast=new Procajastrama();
	$cajatrama=$procajast->find_first($val);
	/*Camibar de estado la caja enviada a cajastrama de 9 a 1*/
			$UPDATE_CAJA=new Tescajas();
			$UPDATE_CAJA->find_first($cajatrama->tescajas_id);
			$UPDATE_CAJA->estado=1;
			$UPDATE_CAJA->save();
	/*Fin de Actualizar caja*/
	/*actualizar el detalle de pedidos*/
	$detalles=new Prodetallepedidos();
	$detalles->find_first($id);
	$detalles->tescajas_id='';
	$detalles->save();
	/*Fin de actualizar el detalle de pedidos*/
	$procajast->delete($val);
	return Redirect::toAction('procajastrama_entregar/'.$id.'/'.$p_id.'/'.$color_id);
}
public function invalidar($id)
{
	$Npedidos= new Pronotapedidos();
	$Npedidos->find_first($id);
	$Npedidos->estadonota="INVALIDO PARA INVENTARIO";
	$Npedidos->save();
	return Redirect::toAction('produccion/Produccion/Pendiente');
}
/*fin de entregar por cajas*/
public function cajas($p,$c,$l)
	{
		$this->data='';
		$q=$_GET['q'];
		$obj= new Tescajas();
		$results = $obj->buscarcaja($q,$p,$c,$l);
		foreach ($results as $value)
		{
			$S=explode("-",$value->getStockCC($value->id));
			
			$json = array();
			$json['id'] =$value->id;
			$json['name'] ='#:'.$value->numerodecaja.', Peso: '.$S[0].'Kg, Conos:'.$S[1].', f:'.$S[0]/$S[1];
			$json['peso'] =$S[0];
			$json['conos'] =$S[1];
			$json['numerodecaja'] =$value->numerodecaja;
			$this->data[] = $json;
		}
	}
/*
Busqueda de lote y su stok
*/
public function lotes($id,$color)
	{
		View::select('cajas');
		$this->data='';
		$q=$_GET['q'];
		$obj= new Tesdetalleingresos();
		$results = $obj->find('conditions: tescolores_id='.$color.' AND tesproductos_id='.$id.' AND lote like "'.$q.'%"');
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =$value->lote;
			$json['name'] = $value->lote.' (stock: '.$this->stokLote($value,$value->tesproductos_id,$value->tescolores_id,$value->lote).')';
			$this->data[] = $json;
		}
	}	
protected function stokLote($value,$id,$color,$lote)
{
	return $value->getTesproductos()->getStockLote($id,$color,$lote);
}


public function terminar($id=0,$estado='Sin enviar',$origen='Otros',$e='')
{
	if($id!=0)
	{
		$ms='Nota de pedido enviada';
		if($estado=='Pendiente'){
			$detalle=new Prodetallepedidos(); 
			if(!$detalle->exists('ISNULL(cantidad) AND pronotapedidos_id='.$id))
			{
				if($detalle->sum('cantidad','conditions: pronotapedidos_id='.$id)!=0){
				$notas=new Pronotapedidos();
				$nota=$notas->find_first((int)$id);
				$nota->estadonota=$estado;
				$nota->save();
				$ms='LA nota de pedido fue enviada a ser revisada';Flash::valid($ms);
				return Redirect::toAction('listado/'.$origen);
				}else
				{
				$notas=new Pronotapedidos();
					$nota=$notas->find_first((int)$id);
					Flash::error('Debe ingresar un detalle');
					if($nota->origen=='Muestra')
					{return Redirect::toAction('crearm/'.$nota->origen);
					}else{
					return Redirect::toAction('crear/'.$nota->origen);
					}
				}
			}else
			{
				Flash::error('Ingrese todas las cantidades');
				return Redirect::toAction('crear/');
			}
		}
		if($estado=='Revisado' || $estado=='Entregado' || $estado=='Ingresado')
		{
			$detalle=new Prodetallepedidos(); 
			if(!$detalle->exists('ISNULL(tescajas_id) AND pronotapedidos_id='.$id))
			{
				$notas=new Pronotapedidos();
				$nota=$notas->find_first((int)$id);
				$nota->estadonota=$estado;
				$nota->save();
				$ms='Nota de pedido fue '.$estado;Flash::valid($ms);
				return Redirect::toAction('listado/'.$origen.'/'.$e);
			}else
			{	if($estado=='Ingresado')
				{
					$notas=new Pronotapedidos();
					$nota=$notas->find_first((int)$id);
					$nota->estadonota=$estado;
					$nota->save();
					$ms='Nota de pedido fue '.$estado;Flash::valid($ms);
					return Redirect::toAction('listado/'.$origen.'/'.$e);
				}else{
				Flash::error('Para poder terminar una nota de pedido tendra que entregar todos los pedidos');
				return Redirect::toAction('revisar');
				}
			}
		}
		Flash::valid($ms);
		return Redirect::toAction('listado');
	}else
	{
		Flash::error('Revise Todas su pedido antes de enviar a almacen');
		return Redirect::toAction('crear');
	}
}
/*
# @id = el id del detalle de la nota de pedido y obterner el color 
*/
public function procajastrama($id,$p_id,$color_id)
{
	$this->id=$id;
	$this->p_id=$p_id;
	$this->color_id=$color_id;
	if(Input::post('procajastrama'))
	{
		$DET=new Prodetallepedidos();
		$this->detalle_pedido=$det=$DET->find_first((int)$id);
		$det->tescajas_id='1';
		$det->save();
		$procajastrama=new Procajastrama(Input::post('procajastrama'));
		$procajastrama->aclusuarios_id=Auth::get('id');
		$procajastrama->estado=1;
		if($procajastrama->save())
		{
			Input::delete('procajastrama');
			return Redirect::toAction('procajastrama/'.$id.'/'.$p_id.'/'.$color_id);
		}
	}
	if(Input::hasPost('detalle_pedido'))
	{
		$detalles=new Prodetallepedidos(Input::post('detalle_pedido'));
		$detalles->save();
		return Redirect::toAction('revisar/'.$detalles->getPronotapedidos()->origen);
	}
	$procajast=new Procajastrama();
	$this->cajas=$procajast->find('conditions: prodetallepedidos_id='.$id);
	$detalles=new Prodetallepedidos();
	$de=$this->detalle_pedido=$detalles->find_first((int)$id);
	
	$deta=$detalles->find('conditions: pronotapedidos_id='.Session::get('NOTA_ID').' AND tesproductos_id='.$p_id.' AND tescolores_id='.$color_id.' AND lote="'.$de->lote.'"');
	$t=0;
	foreach($deta as $dt)
	{
		$t=$t+$dt->cantidad;
	}
	$this->total=$t;
	$obj= new Tescajas();
	$this->CAJAS = $obj->buscarcaja($de->tesproductos_id,$de->tescolores_id,$de->lote);
}
public function eliminar_cajastrama($val,$id,$p_id,$color_id)
{
	$procajast=new Procajastrama();
	$procajast->delete($val);
	return Redirect::toAction('procajastrama/'.$id.'/'.$p_id.'/'.$color_id);
}

public function cargaringreso($id=0,$url='ingreso_hilo',$origen='Chenille')
	{
		
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)15);/*GUIA de REmision*/
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		if($id!=0){
		Session::set("INGRESO_ID",$id);
		}else
		{
			Session::delete("INGRESO_ID");
		}
		return Redirect::toAction($url.'/'.$origen);
	}

public function ingreso_hilo($origen='')
{
		$this->origen=$origen;
		$this->movimiento='CP';
		$this->monedas_nombre='';
		$this->totalenletras='';
		$this->DETALLE=FALSE;
		$this->monedas=0;
		$ING=new Tesingresos();
		$this->numero=$ING->generarNumeroInterno(Session::get('DOC_ID'));
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->titulo = Session::get('DOC_NOMBRE');
		//$this->numero=$ING->generarNumero(Session::get('DOC_ID'));
		$this->observacion='';
		$this->pre_descripcion='';
		$this->subt=0.00;
		$this->detalles='';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->totalconigv=00.00;
		$this->igv=00.00;
		$this->cliente_id="0";
		$this->cliente="";
		$this->cuantagastos=NULL;
		$this->cuentaporpagar=NULL;
		$this->cuantagastos_name=NULL;
		$this->cuentaporpagar_name=NULL;
		$this->nombre=NULL;
		if(Session::has('INGRESO_ID')){
		if($ING->exists("id=".(int)Session::get('INGRESO_ID'))){
		$this->DETALLE=TRUE;
		/*
		Cargar los detalles
		*/
		$detalles=new Tesdetalleingresos();
		$ingreso=$ING->find_first((int)Session::get('INGRESO_ID'));
		
		$this->detalles=$detalles->find('conditions: tesingresos_id='.$ingreso->id);
		$this->id=$ingreso->id;
		$this->numero=$ingreso->numero;
		$this->glosa=$ingreso->glosa;
		$this->serie=$ingreso->serie;
		$this->codigo=$ingreso->codigo;//fvencimiento
		$this->femision=$ingreso->femision;
		$this->fvencimiento=$ingreso->fvencimiento;
		$this->monedas=$ingreso->tesmonedas_id;
		$this->monedas_nombre=$ingreso->getTesmonedas()->nombre;
		$this->cliente_id=$ingreso->tesdatos_id;
		/*nombre del cliente*/
		$this->nombre=$ingreso->getTesdatos()->razonsocial." ruc: ".$ingreso->getTesdatos()->ruc." (".$ingreso->getTesdatos()->departamento.' - '.$ingreso->getTesdatos()->provincia.' - '.$ingreso->getTesdatos()->distrito.' - '.$ingreso->getTesdatos()->direccion.')';
		/**/
		$this->totalconigv=$ingreso->totalconigv;
		$this->igv=$ingreso->igv;
		$this->paid=$ingreso->igv;
		$this->fregistro=$ingreso->fregistro;
		$this->npedido=$ingreso->npedido;
		$this->numeroguia=$ingreso->numeroguia;
		$this->numerofactura=$ingreso->numerofactura;
		$this->ordendecompra=$ingreso->ordendecompra;
		$this->finiciotraslado=$ingreso->finiciotraslado;
		$this->movimiento=$ingreso->movimiento;
		$this->cuantagastos=$ingreso->cuantagastos;
		$this->cuentaporpagar=$ingreso->cuentaporpagar;
		$this->totalenletras=$ingreso->totalenletras;
		
		$this->subt=$this->totalconigv-$this->igv;
		if(!empty($this->cuentaporpagar)){
		/* detalle de las cuentas*/
		$cc=new Subcuentas();
		if(!empty($this->cuantagastos)){
		$cg=$cc->find_first('codigo='.$this->cuantagastos);
		$this->cuantagastos_name=$cg->codigo.'<span style=\"font-size:9px;\">('.$cg->descripcion.')</span>';
		}
		if(!empty($this->cuentaporpagar)){
		$cp=$cc->find_first('codigo='.$this->cuentaporpagar);
		$this->cuentaporpagar_name=$cp->codigo.'<span style=\"font-size:9px;\">('.$cp->descripcion.')</span>';
		}
		/**/
		}
		$this->codigo=$ingreso->codigo;
		
		}else{
		$this->DETALLE=FALSE;
		$this->numero=$ING->generarNumeroInterno(Session::get('DOC_ID'));
		$this->observacion='';
		$this->pre_descripcion='';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->monedas=0;
		$this->cliente_id="";
		$this->cliente="";
		$this->totalenletras='';
		}
		}
}
/*Funciones explusivas para Chenille*/
public function eliminar_i($id) {
        try {
            $id = Filter::get($id, 'digits');

            $obj  = new Tesingresos();

            if (!$o=$obj ->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Producto");
            } else if ($obj ->delete($id)) {
                Flash::valid("EL Documento Fue Borrado fue Borrado <b>{$o->numero}</b>!!!");
               Aclauditorias::add("EL Documento <b>{$o->numero}</b> fue Borrado del sistema", 'tesproductos');
            } else {
                Flash::warning("No se Pudo Eliminar el Documento <b>{$tesproductos->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/Chenille');
    }
public function ver_i($origen='')
	{
		$this->origen=$origen;
		if (Input::hasPost('ingreso')) {
                $ingresos = new Tesingresos(Input::post('ingreso'));
                
                if($ingresos->save()) {
                    Flash::valid('EL documento fue ingresado');
                    Aclauditorias::add("La factura Fue terminada de ingresar", 'tesingresos');
                    return Redirect::toAction('listado/'.$origen);
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
          }
		$this->DETALLE=FALSE;
		$ING=new Tesingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		if($ING->exists("id=".(int)Session::get('INGRESO_ID'))){
		$this->DETALLE=TRUE;
		$detalles=new Tesdetalleingresos();
		$ingreso=$ING->find_first((int)Session::get('INGRESO_ID'));
		$this->ingreso=$ingreso;
		$this->detalles=$detalles->find('conditions: tesingresos_id='.$ingreso->id);
		if($ingreso->estadoingreso=='Editable')Flash::valid('Por favor verifique La factura antes de terminar!!!!!');
		}else{
		$this->DETALLE=FALSE;
		$this->numero=$ING->generarNumero(Session::get('DOC_ID'));
		$this->observacion='';
		$this->pre_descripcion='';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->monedas=0;
		$this->cliente_id="";
		$this->cliente="";
		$this->totalenletras='';
		}
		
	}
	
public function entregar_todo($origen='Produccion')
{
	$this->origen=$origen;
	$detalles=new Prodetallepedidos(); 	
	$this->detalles=$detalles->getDetallePendientes();
}

public function eliminar_pendients($id=0)
{
	if($id==43408841)
	{
		
	}
}
public function actualizarprocajastrama()
{
	$CAJA=new Tescajas();
	$procajast=new Procajastrama();
	$cajas=$procajast->find('conditions: !isnull(tescajas_id) AND (isnull(lote) OR isnull(numerodecaja))');
	$flash="";
	foreach($cajas as $c):
	$cj=$CAJA->find_first($c->tescajas_id);
	$new_c=$procajast->find_first($c->id);
	$new_c->numerodecaja=$cj->numerodecaja;
	$new_c->lote=$cj->getTesdetalleingresos()->lote;
	
	$new_c->save();
	$flash.=' '.$new_c->id.' ,';
	endforeach;
	Flash::valid('Actualizado '.$flash);
	return Redirect::toAction('produccion/Produccion/Pendiente');	
}
public function unir_pedido($id,$numero)
{
	$this->val=1;
	$pedidos=new Pronotapedidos();
	$pedido=$pedidos->find_first($id);
	$pedido->pedido_referencia=$numero; 
	$pedido->cantidad=0; 
	$pedido->cantidad_p=0;
	$pedido->tescajas_id=0;	
	$pedido->activo=0;
	$pedido->estadonota='Entregrado'; 
	if($pedido->save())
	{
		$this->val=1;
	}
}
/*
recibe el 
$id_p :: id del pedido
$id_c :: id de la caja
*/
public function enviar_caja_trama($id_p,$id_c,$id_maq)
{
	$CAJA=new Tescajas();
	$caja=$CAJA->find_first($id_c);
	
	$pedidos=new Pronotapedidos();
	$this->pedido=$pedidos->find_first($id_p);
	
	$detalles=new Prodetallepedidos();
	$detalles->pronotapedidos_id=$id_p;
	$detalles->tescolores_id=$caja->getTesdetalleingresos()->tescolores_id;
	$detalles->tesproductos_id=$caja->getTesdetalleingresos()->tesproductos_id;
	$detalles->cantidad=$caja->pesoneto;
	$detalles->cantidad_p=$caja->pesoneto;
	$detalles->tescajas_id=$caja->id;
	$detalles->lote=$caja->getTesdetalleingresos()->lote;
	$detalles->tesunidadesmedidas_id=$caja->getTesdetalleingresos()->tesunidadesmedidas_id;
	$detalles->promaquinas_id=$id_maq;
	$detalles->activo=1;
	$detalles->estado=1;
	$detalles->save();
	
	$TRAMA=new Procajastrama();
	$TRAMA->prodetallepedidos_id=$detalles->id;
	$TRAMA->tescajas_id=$caja->id;
	$TRAMA->tescolores_id=$caja->tescolores_id;
	$TRAMA->numerodecaja=$caja->numerodecaja;
	$TRAMA->lote=$caja->lote;
	$TRAMA->conos=$caja->conos;
	$TRAMA->peso=$caja->pesoneto;
	$TRAMA->estado=1;
	$TRAMA->aclusuarios_id=Auth::get('id');
	$TRAMA->save();
	
	$Ndetalles= new Prodetallepedidos();
	$caja->enalmacen='0';
	$caja->save();
	$this->detalles=$Ndetalles->find('conditions: pronotapedidos_id='.(int)$id_p);
}

public function eliminar_caja_detalle($d_id,$c_id)
{
	$detalles=new Prodetallepedidos();
	$detalles->delete("id=".$d_id);
	$TRAMA=new Procajastrama();
	$TRAMA->delete('prodetallepedidos_id='.$d_id.' AND tescajas_id='.$c_id);
	$CAJA=new Tescajas();
	$caja=$CAJA->find_first($c_id);
	$caja->enalmacen='1';
	$caja->save();
	return Redirect::toAction('revisar/Produccion');	
}


}
?>