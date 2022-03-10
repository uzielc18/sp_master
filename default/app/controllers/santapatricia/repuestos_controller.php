<?php
View::template('spatricia/default');
Load::models('acldatos','plareas','prorepuestos','testipoproductos','tesproductos','aclempresas','tesdocumentos','tesingresos','tessalidas','tesdetalleingresos','pronotapedidos','prodetallepedidos','prorepuestosuso','tesdetallesalidas','prodetalletransportes');
class RepuestosController extends AdminController {
	public $per_page=20;
	public $proveedor=2;
	//public $empresa=Session::get('EMPRESAS_ID');
    protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	public function listado($page=1)
	{
		 try {
		/*
		Se debe saber el id de la empresa para poder ver sus trabajadores Santa Patricia es =1 y Santa Carmela = 2.
		*/
		$this->page=$page;
		$prorepuestos = new Prorepuestos();
		$this->repuestos=$prorepuestos->paginate('page: '.$page,'per_page: '.$this->per_page,'conditions: estado=1 AND aclempresas_id='.Session::get('EMPRESAS_ID'),'order: id DESC');
	 	} catch (KumbiaException $e) {
          View::excepcion($e);
        }
    }
	public function crear($page=1)
	{
	//try {
		$productos= new Tesproductos();
		$this->page=$page;
		$this->empresa=Session::get('EMPRESAS_ID');
		$repuestos= new Prorepuestos();
		$emp= new Aclempresas();
		$this->titulo='Registrar nuevo Repuesto';
		$this->boton="Guardar";
		$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
		$this->codigo=$productos->generacodigo();
             if(Input::hasPost('tesproductos'))
			 {
				 $PRO_DAT=Input::post('tesproductos');
				 if($PRO_DAT['id']==''){
				 $pro=new Tesproductos(Input::post('tesproductos'));
				 }else
				 {
				$pro= $productos->find_first((int)$PRO_DAT['id']);
				 }
				 $pro->activo='1';
				 $pro->estado=1;
				 $pro->aclusuarios_id=Auth::get('id');
				 $pro->aclempresas_id=Session::get('EMPRESAS_ID');
				 if($pro->update(Input::post('tesproductos')))
				 {
					if (Input::hasPost('prorepuestos'))
					{
						$dat = new Prorepuestos(Input::post('prorepuestos'));
						$dat->tesproductos_id=$pro->id;
						$dat->codigo=$pro->prefijo.$pro->codigo;
						$dat->activo='1';
						$dat->estado=1;
						$dat->aclempresas_id=Session::get('EMPRESAS_ID');
						if ($dat->save()) {
							Flash::valid('El Repuesto Ha Sido Agregado Exitosamente...!!!');
							Aclauditorias::add("Agregó al Repuesto {$dat->numero} al sistema", 'prorepuestos');
							return Redirect::toAction('listado/'.$page);
						} else {
							Flash::warning('No se Pudieron Guardar los datos de la repuesto...!!!');
						}
					}
				 }else
				 {
					 Flash::warning('No se Pudo Guardar el producto Guardar los Datos...!!!');
					 return Redirect::toAction('listado/'.$page);
				}
			}
        /*} catch (KumbiaException $e) {
          View::excepcion($e);
        }*/
	}
	
	public function editar($id,$page=1) {
        try {
			View::select('crear');
			$productos= new Tesproductos();
			$this->page=$page;
			$this->empresa=Session::get('EMPRESAS_ID');
			$repuestos= new Prorepuestos();
			$emp= new Aclempresas();
			$this->titulo='Editar Repuesto';
			$this->boton="Editar";
			$this->repuestos = $repuestos->find();
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
			$this->prorepuestos= $repuestos->find_first((int)$id);
			$this->tesproductos= $productos->find_first((int)$this->prorepuestos->tesproductos_id);
			$this->codigo=$this->tesproductos->codigo;
			if(Input::hasPost('tesproductos'))
			{
				$productos->update(Input::post('tesproductos'));
				if (Input::hasPost('prorepuestos')) {
					
						$repuestos->tesproductos_id=$productos->id;
						$repuestos->codigo=$productos->prefijo.$productos->codigo;
						$repuestos->aclempresas_id=Session::get('EMPRESAS_ID');
					if ($repuestos->update(Input::post('prorepuestos'))) {
						Flash::valid('La Repuesto Ha Sido Actualizado Exitosamente...!!!');
						Aclauditorias::add("Editó al personal {$repuestos->numero}", 'prorepuestos');
						
						return Redirect::toAction('listado/'.$page);
					} else {
						Flash::warning('No se Pudieron Guardar los Datos...!!!');
						unset($this->prorepuestos); //para que cargue el $_POST en el form
					}
				} else if (!$this->prorepuestos) {
					Flash::warning("No existe ningun Maquinas con id '{$id}'");
					return Redirect::redirect('santapatricia/personal/listado');
				}
			}
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	
	public function activar($id,$page=1) {
        try {
            $id = Filter::get($id, 'digits');
            $dat = new Prorepuestos();
            if (!$dat->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Repuesto con id '{$id}'");
            }else if ($dat->activar()) {
				$tesproductos=new Tesproductos();
				$tesproductos->find_first($dat->tesproductos_id);
				$tesproductos->activar();
                Flash::valid("El Repuesto<b>{$dat->modelo}</b> Esta ahora <b>Activo</b>...!!!");
                Aclauditorias::add("Colocó el Repuesto {$dat->modelo} como activo", 'prorepuestos');
            } else {
                Flash::warning('No se pudo Activar el Repuesto!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$page);
    }
     public function desactivar($id,$page=1) {
        try {
            $id = Filter::get($id, 'digits');
            $dat = new Prorepuestos();
            if (!$dat->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Repuesto");
            }else if ($dat->desactivar()) {
				
				$tesproductos=new Tesproductos();
				$tesproductos->find_first($dat->tesproductos_id);
				$tesproductos->desactivar();
				
                Flash::valid("El Repuesto <b>{$dat->modelo}</b> Esta ahora <b>Inactivo</b>...!!!");
                Aclauditorias::add("Colocó al Repuesto {$dat->modelo} como inactivo", 'prorepuestos');
            } else {
                Flash::warning('No se Pudo Desactivar el Repuesto...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$page);
    }
	
	public function borrar($id=0,$page=0) {
        try {
            $id = Filter::get($id, 'digits');

            $rep = new Prorepuestos();

            if (!$rep->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Repuesto");
            } else if ($rep->borrar()) {
                Flash::valid("Repuesto <b>{$rep->modelo}</b> fué Eliminado...!!!");
                Aclauditorias::add("Repuesto {$rep->modelo} del sistema", 'prorepuestos');
            } else {
                Flash::warning("No se Pudo Eliminar el Repuesto <b>{$rep->modelo}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado');
    }
	public function resultados() 
	{
		$q=$_GET['q'];
		$obj = new Tesproductos();
		$results = $obj->find('conditions: CONCAT_WS(" ",codigo,detalle,abr) like "%'.$q.'%" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
		foreach ($results as $value)
		{
			/*  codigo	varchar(10)	Sí 	NULL 	 
				prefijo	varchar(10)	Sí 	NULL 	 
				codigobarras	int(10)	Sí 	NULL 	 
				nombre	varchar(250)	Sí 	NULL 	 
				abr	varchar(10)	Sí 	NULL 	 
				nombrecorto	varchar(50)	Sí 	NULL 	 
				detalle	text	Sí 	NULL 	 
				precio*/
			$ID=array("id"=>$value->id,"testipoproductos_id"=>$value->testipoproductos_id,"codigo"=>$value->codigo,"prefijo"=>$value->prefijo,"codigobarras"=>$value->codigobarras,"nombre"=>$value->nombre,"abr"=>$value->abr,"nombrecorto"=>$value->nombrecorto,"detalle"=>$value->detalle,"precio"=>$value->precio,"preciod"=>$value->preciod);
			$id=$ID;
			//$name=$value->nombre;
			$name=$value->nombre.' : '.$value->codigo;
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
		}
    }
	
	public function ver($id=0,$page=1)
	{
        try {
			$this->page=$page;
			$this->estados;
			$this->empresa=Session::get('EMPRESAS_ID');
			$emp= new Aclempresas();
            $id = Filter::get($id, 'digits');
			$dat = new Prorepuestos();
			$pro= new Tesproductos();
			$this->boton="Editar";
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.$this->empresa);
            $this->prorepuestos = $dat->find_first($id);
			/*
			Valida la existencia del distrito creado en todo caso le permite editar
			*/
			$this->tesproductos=$pro->find_first($this->prorepuestos->id);
			$this->titulo='Detalle al Repuesto ('.$this->prorepuestos->numero.')';
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
	}
public function actualizar()
{	
	$repuestos= new Prorepuestos();
	$res=$repuestos->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID'));
	foreach($res as $r):
	$obj = new Tesproductos();
	$obj->find_first($r->tesproductos_id);
	$obj->ubicacion_almacen=$r->ubicacion;
	$obj->save();
	endforeach;
	return Redirect::toAction('ingresos');
}	
/*Repuestos movimientos de repuestos salida e ingresos a maquinas*/
public function getrepuestos()
{
	View::select("resultados");
	$q=$_GET['q'];
	$obj = new Tesproductos();
	$results = $obj->getProductos_tipo($q,6);
 	foreach ($results as $value)
		{
			$name=$value->nombre.' ('.$value->codigo_ant.')';
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $name;
			$json['oculto'] = $value->ubicacion_almacen;
			$json['precio'] = $value->preciod;
			$this->data[] = $json;
		}	
}

public function index() {

    }
	
public function ingresos($Y='',$m='')
	{
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		Session::delete("INGRESO_ID");
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)15);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		/*
		serie 002
		salida de Hilos
		*/
		$ingresos= new Tesingresos();
		$this->ingresos= $ingresos->find('conditions: (DATE_FORMAT(femision,"%Y-%m")="'.$anio.'-'.$mes_activo.'" OR ISNULL(femision)) AND (ISNULL(movimiento) OR movimiento!="INTE") AND serie!="999" AND pr like "RR%" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID').' AND aclempresas_id='.Session::get('EMPRESAS_ID'),'order: fecha_at DESC');
		Session::delete("INGRESO_ID");
	}
public function cargaringreso($id=0,$url='agregardetalles_ingreso/')
{
		if($id!=0){
		Session::set("INGRESO_ID",$id);
		}else
		{
			Session::delete("INGRESO_ID");
		}
		return Redirect::toAction($url);
}	
public function crearingresos()
	{
		if (Input::hasPost('ingreso')) 
		{
			$ingresos= new Tesingresos(Input::post('ingreso'));
			$ingresos->tesdocumentos_id=Session::get('DOC_ID');
			$ingresos->fvencimiento=date("Y-m-d",strtotime($ingresos->femision."+ 30 days"));
			$ingresos->estadoingreso='Editable';
			$ingresos->estado=1;
			$ingresos->userid=Auth::get('id');
			$ingresos->activo='1';
			$ingresos->pr='RR';
			$ingresos->testipocambios_id=Session::get('CAMBIO_ID');
			$ingresos->userid=Auth::get('id');
			$ingresos->aclusuarios_id=Auth::get('id');
			$ingresos->aclempresas_id=Session::get("EMPRESAS_ID");
            if($ingresos->save())
			{
				Session::set("INGRESO_ID",$ingresos->id);
            	
				Flash::valid('fue agregada Exitosamente...!!!'.$ingresos->id);
                //Aclauditorias::add("Creo un Nuevo Ingreso tipo Repuesto ".Session::get('DOC_NOMBRE')."-{$ingresos->serie}-{$ingresos->numero} al sistema");
				
                return Redirect::toAction('agregardetalles_ingreso/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 //$this->ingresos=Input::post('ingreso');
				 return Redirect::toAction('crearingresos/');
             }
		}
	}
public function agregardetalles_ingreso()
	{
		if (Input::hasPost('ingreso')) 
		{
			$ingreso= new Tesingresos(Input::post('ingreso'));
			$ingreso->aclusuarios_id=Auth::get("id");
            if ($ingreso->save())
			{
				Session::set("INGRESO_ID",$ingreso->id);
				Flash::valid('EL ingreso se Modifico con exito!!!');
				return Redirect::toAction('agregardetalles_ingreso/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$ingresos= new Tesingresos();
		$detalles= new tesdetalleingresos();
		
		$this->ingreso=$ingresos->find_first((int)Session::get('INGRESO_ID'));
		$this->detalles=$detalles->find('conditions: tesingresos_id='.Session::get('INGRESO_ID'));
		
		
	}
public function grabardetalle_ingresos($id)
{
	if (Input::hasPost('detalles')) 
		{
			$detalles= new tesdetalleingresos(Input::post('detalles'));
			$D=Input::post('detalles');
			$detalles->tesingresos_id=$id;
			$detalles->estado='1';
			$detalles->userid=Auth::get('id');
			$detalles->aclempresas_id=Session::get("EMPRESAS_ID");
            if ($detalles->save())
			{
			$DET= new tesdetalleingresos();
			$detalle=$DET->find_first($detalles->id);
			$monedas='0';
			$monedas=$detalle->getTesingresos()->getTesmonedas()->id;
			$cambios=new Testipocambios();
			$cam=$cambios->find_first($detalle->getTesingresos()->getCambio($detalle->getTesingresos()->femision));/*Cambiar por la session*/
					switch ($monedas)
					{
						case '1': //moneda es SOLES
								$precioS=$detalle->preciounitario;
								$precioD=($detalle->preciounitario/$cam->compra);
						 		break;
						case '2':  //moneda es Dolares
								$precioD=$detalle->preciounitario;
								$precioS=($detalle->preciounitario*$cam->compra);
						 		break;
						case '4':  //moneda es SOLES
								$precioS=$detalle->preciounitario;
								$precioD=($detalle->preciounitario/$cam->compra);
						 		break;
						case '5':  //moneda es Dolares
								$precioD=$detalle->preciounitario;
								$precioS=($detalle->preciounitario*$cam->compra);
						 		break;
						case '0':  //moneda es SOLES
								$precioS=$detalle->preciounitario;
								$precioD=($detalle->preciounitario/$cam->compra);
						 		break;
					}
					
					
				$producto = new Tesproductos();
				if($detalle->tesproductos_id!=0){
				$pr=$producto->find_first((int)$detalle->tesproductos_id);
				$pr->precio=$precioS;
				$pr->preciod=$precioD;
				$pr->ubicacion_almacen=$D['ubicacion'];
				$pr->save();
				}
				$repuestos= new Prorepuestos();
				$repuestos->find_first('conditions: tesproductos_id='.$detalle->tesproductos_id);
				$repuestos->ubicacion=$D['ubicacion'];
				$repuestos->save();
				Flash::valid('Repuesto ingrezado!!!');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$detalles= new tesdetalleingresos();
		$this->id=$id;
		$this->detalles=$detalles->find('conditions: tesingresos_id='.$id);
}

public function eliminardetalleingreso($id)
{
	$detalles= new tesdetalleingresos();
	$det=$detalles->find_first($id);
	$id_ingreso=$det->tesingresos_id;
	$detalles->delete($id);
	return Redirect::toAction('grabardetalle_ingresos/'.$id_ingreso);
}
public function veringreso($id=0)
{
		$ingresos= new Tesingresos();
		$detalles= new tesdetalleingresos();
		$this->ingreso=$ingresos->find_first((int)Session::get('INGRESO_ID'));
		$this->detalles=$detalles->find('conditions: tesingresos_id='.Session::get('INGRESO_ID'));
		if($id!=0)
		{
			$ingresos= new Tesingresos();
			$ingreso= $ingresos->find_first($id);
			$ingreso->estadoingreso='Pendiente';
			$ingreso->save();
			Flash::valid('La guia '.$this->ingreso->serie.'-'.$this->ingreso->numero.' Finalizao su ingreso!!!');
		}
}
/*salida de repuestos*/

public function getdatos(){
	View::select("resultados");
	$q=$_GET['q'];
	$obj = new Acldatos();
	$results = $obj->find('conditions: CONCAT_WS(" ",nombre,appaterno,apmaterno,dni) like "%'.$q.'%" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
 	foreach ($results as $value)
		{
			$name=$value->nombre.' ('.$value->dni.')';
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $name;
			$this->data[] = $json;
		}	
	}
public function getareas(){
	View::select("resultados");
	$q=$_GET['q'];
	$obj = new Plareas();
	$results = $obj->find('conditions: CONCAT_WS(" ",nombre,descripcion) like "%'.$q.'%" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
 	foreach ($results as $value)
		{
			$name=$value->nombre.' ('.$value->descripcion.')';
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $name;
			$this->data[] = $json;
		}	
	}
public function cargarnota($id=0,$val='agregardetalles_salida')
{
	if($id!=0){
	$Npedidos= new Pronotapedidos();
	$nota=$Npedidos->find_first((int)$id);
	Session::set('NOTA_ID',$nota->id);
	}else
	{
	Session::set('NOTA_ID',$id);
	}
	return Redirect::toAction($val.'/');
}
public function salidas($Y='',$m='')
	{
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
	Session::delete('DOC_ID');
	Session::delete('DOC_NOMBRE');
	Session::delete('DOC_ABR');
	Session::delete('DOC_CODIGO');
	Session::delete("INGRESO_ID");
	$this->origen=$origen='Repuestos';
	Session::delete('NOTA_ID');
	$obj= new Pronotapedidos();
	$condiciones='conditions: (DATE_FORMAT(fecha,"%Y-%m")="'.$anio.'-'.$mes_activo.'" OR ISNULL(fecha)) AND !ISNULL(pronotapedidos.plareas_id) AND pronotapedidos.aclempresas_id='.Session::get('EMPRESAS_ID').' AND pronotapedidos.tipo="salida" AND pronotapedidos.origen="'.$origen.'"';
	$campos = 'pronotapedidos.' . join(',pronotapedidos.', $obj->fields) . ",(IFNULL(sum(r.cantidad),0)) as total";
    $join = 'LEFT JOIN prodetallepedidos as r on r.pronotapedidos_id=pronotapedidos.id';
    $agrupar_por = 'pronotapedidos.id';
	$this->notas=$obj->find($condiciones, "join: $join", "columns: $campos", "group: $agrupar_por",'order: codigo DESC');
}

public function crearsalidas()
	{
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));		
		$Npedidos= new Pronotapedidos();
		$this->DETALLE=FALSE;
		$this->id=0;
		$this->pedido['codigo']=$Npedidos->codigo_r();
		if (Input::hasPost('pedido')) 
		{
			$pedidos= new Pronotapedidos(Input::post('pedido'));
			$pedidos->aclusuarios_id=Auth::get('id');
			$pedidos->estadonota='Editable';
			$pedidos->estado=1;
			$pedidos->origen='Repuestos';
			$pedidos->tipo='salida';
			$pedidos->userid=Auth::get('id');
			$pedidos->activo='1';
			$pedidos->userid=Auth::get('id');
			$pedidos->aclempresas_id=Session::get("EMPRESAS_ID");
            if($pedidos->save())
			{
				Session::set("NOTA_ID",$pedidos->id);
            	
				Flash::valid('fue agregada Exitosamente...!!!'.$pedidos->id);
                //Aclauditorias::add("Creo un Nuevo Ingreso tipo Repuesto ".Session::get('DOC_NOMBRE')."-{$ingresos->serie}-{$ingresos->numero} al sistema");
				
                return Redirect::toAction('agregardetalles_salida/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 //$this->ingresos=Input::post('ingreso');
				 return Redirect::toAction('crearingresos/');
             }
		}
		
	}
public function agregardetalles_salida()
	{
		if (Input::hasPost('pedido')) 
		{
			$pedidos= new Pronotapedidos(Input::post('pedido'));
			$pedidos->aclusuarios_id=Auth::get("id");
            if ($pedidos->update())
			{
				Session::set("NOTA_ID",$pedidos->id);
				Flash::valid('EL ingreso se Modifico con exito!!!');
				return Redirect::toAction('agregardetalles_salida/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$pedidos= new Pronotapedidos();
		$detalles= new prodetallepedidos();
		
		$this->pedido=$pedidos->find_first((int)Session::get('NOTA_ID'));
		$this->detalles=$detalles->find('conditions: pronotapedidos_id='.Session::get('NOTA_ID'));
		
		
	}
public function grabardetalle_salidas($id)
{
	if (Input::hasPost('detalles')) 
		{
			$D=Input::post('detalles');
			if($D['tesproductos_id']){
			$detalles= new prodetallepedidos(Input::post('detalles'));			
			$detalles->pronotapedidos_id=$id;
			$detalles->tescajas_id='1';
			$detalles->estado='1';
			$detalles->userid=Auth::get('id');
			$detalles->aclempresas_id=Session::get("EMPRESAS_ID");
            if ($detalles->save())
			{
			$DET= new prodetallepedidos();
			$detalle=$DET->find_first($detalles->id);
			/*agregar repuestouso*/
			if (Input::hasPost('prorepuestosuso')) 
				$uso=new Prorepuestosuso(Input::post('prorepuestosuso'));
				$uso->prorepuestos_id=$detalle->getTesproductos()->getProrepuestos()->id;
				$uso->prodetallepedidos_id=$detalle->id;
				$uso->acldatos_id=$detalle->acldatos_id;
				$uso->fechaingreso=$detalle->fecha_r;
				$uso->estado='1';
				$uso->userid=Auth::get('id');
				$uso->save();
				Flash::valid('Repuesto en maquina!!!');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		  }
		}
		$detalles= new prodetallepedidos();
		$this->id=$id;
		$this->detalles=$detalles->find('conditions: pronotapedidos_id='.$id);
}
/*id del detalle*/
public function eliminar_detalle_salida($id) 
{
	$DET= new prodetallepedidos();
	$detalle=$DET->find_first($id);
	$uso=new Prorepuestosuso();
	$uso->delete('prorepuestos_id='.$detalle->getTesproductos()->getProrepuestos()->id.' AND prodetallepedidos_id='.$detalle->id);
	$DET->delete($id);
	Flash::valid('EL detalle fue eliminado con exito!!!');
	return Redirect::toAction('agregardetalles_salida/');
}
/*id del detalle*/
public function eliminar_nota_salida($id) 
{
	$pedidos= new Pronotapedidos();
	$DET= new prodetallepedidos();
	$detalles=$DET->find('pronotapedidos_id='.$id);
	foreach($detalles as $d){
	$detalle=$DET->find_first($d->id);
	$uso=new Prorepuestosuso();
	$uso->delete('prorepuestos_id='.$detalle->getTesproductos()->getProrepuestos()->id.' AND prodetallepedidos_id='.$detalle->id);
	$DET->delete($d->id);
	}
	$pedidos->delete($id);
	Flash::valid('La nota de salida fue eliminada!!!');
	return Redirect::toAction('salidas/');
}
public function versalida()
{
	if (Input::hasPost('pedido')) 
		{
			$pedidos= new Pronotapedidos(Input::post('pedido'));
			$pedidos->estadonota='Entregado';
			$pedidos->aclusuarios_id=Auth::get("id");
            if ($pedidos->save())
			{
				Flash::valid('EL ingreso se Modifico con exito!!!');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$pedidos= new Pronotapedidos();
		$detalles= new prodetallepedidos();
		$this->pedido=$pedidos->find_first((int)Session::get('NOTA_ID'));
		$this->detalles=$detalles->find('conditions: pronotapedidos_id='.Session::get('NOTA_ID'));
}
public function versalida_i()
{
		$pedidos= new Pronotapedidos();
		$detalles= new prodetallepedidos();
		$this->pedido=$pedidos->find_first((int)Session::get('NOTA_ID'));
		$this->detalles=$detalles->find('conditions: pronotapedidos_id='.Session::get('NOTA_ID'));
}
public function getubicacionmaquina($id)
{
	$this->id=$id;
}
/*$id = EL id del producto para buscar el id del repuesto*/
public function resumen_repuesto($id=0, $maquina=0)
{
	$this->id=$id;
	if($id!=0)
	{
		$productos = new Tesproductos();
		$this->producto=$productos->find_first((int)$id);
		$usos=new Prorepuestosuso();
		$this->usos=$usos->getResumen((int)$id,$maquina);
	}
	
}

public function reportesotk($c='nombre',$o='ASC')
{
	/*Stock actual de repuestos*/
	$prorepuestos = new Prorepuestos();
	$this->repuestos=$prorepuestos->getSotck($c,$o);
	
}
public function reporterepocicion()
{
	/*Stock actual de repuestos*/
	$prorepuestos = new Prorepuestos();
	$this->repuestos=$prorepuestos->stokRepocicion();
	
}
public function reporteingresos($Y='',$m='')
{
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	
	$prorepuestos = new Prorepuestos();
	$this->repuestos=$prorepuestos->getIngresos($anio,$mes_activo);
}
public function reporteutilizacion($Y='',$m='')
{
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	
	$prorepuestos = new Prorepuestos();
	$this->repuestos=$prorepuestos->getUtilizacion($anio,$mes_activo);
}
public function reporteutilizacion_maquinas($Y='',$m=0)
{
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	
	$prorepuestos = new Prorepuestos();
	$this->repuestos=$prorepuestos->getUtilizacion_m($anio,$mes_activo);
}
public function repuestoprecios()
	{
		$productos = new Tesproductos();
		$this->PP=$productos->getLineap_json(6);
		$this->CC=$productos->getColores_json();
	}
	public function ver_repuesto()
	{
		$a=Input::post('local_producto');
		$b=Input::post('local_color');
		$detalles_ingresos=new Tesdetalleingresos();
		$this->precios=$detalles_ingresos->getPrecios($a,$b);
		
	}

/* Guias para Devolucion de salidas de respuestos*/

public function cargarsalida($id=0,$url='agregardetalles_guia')
{
	if($id!=0)
	{
		Session::set("SALIDA_ID",$id);
	}else
	{
		Session::delete("SALIDA_ID");
	}
	return Redirect::toAction($url);
}

public function listado_guias()
{
	$this->titulo='Guias sin factura';
	$this->url='crearsalidas';
	$documentos= new Tesdocumentos();
	$doc= $documentos->find_first((int)15);
	Session::set('DOC_ID',$doc->id);
	Session::set('DOC_NOMBRE',$doc->nombre);
	Session::set('DOC_ABR',$doc->abr);
	Session::set('DOC_CODIGO',$doc->codigo);
	/*
	serie 001
	salida de de rollos para venta
	*/
	$salidas= new Tessalidas();
	$this->salidas= $salidas->find('conditions: aclempresas_id='.Session::get("EMPRESAS_ID").' AND serie="002" AND estadosalida!="Con factura" AND estadosalida!="Anulado" AND npedido like "DV%" AND estado=1 AND tesdocumentos_id=15','order: numero DESC limit 15');
	Session::delete("SALIDA_ID");
}
public function crearsalidas_guias()
	{
		$SALD=new Tessalidas();
		$this->salida['serie']='002';
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'002');
		$this->salida['npedido']=$SALD->getNumeropedido('DV','002');
		//$this->salida['numerofactura']=$SALD->generarNumeroFactura('002');
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id=Session::get('DOC_ID');
			$salidas->fvencimiento=date("Y-m-d",strtotime($salidas->femision."+ 30 days"));
			$salidas->estadosalida='Editable';
			$salidas->estado=1;
			/*Cuenta cuentagastos cuentaporpagar*/
			$salidas->cuentaporcobrar='12121';
			$salidas->userid=Auth::get('id');
			$salidas->activo='1';
			$salidas->testipocambios_id=Session::get('CAMBIO_ID');
			$salidas->userid=Auth::get('id');
			$salidas->aclusuarios_id=Auth::get('id');
			$salidas->aclempresas_id=Session::get("EMPRESAS_ID");
            if ($salidas->save())
			{
				$detalleT= new Prodetalletransportes(Input::post('prodetalletransportes'));
				$detalleT->tessalidas_id=$salidas->id;
				$detalleT->numero=$salidas->numero;
				$detalleT->serie=$salidas->serie;
				$detalleT->fechatraslado=$salidas->finiciotraslado;
				$detalleT->estado='1';
				$detalleT->userid=Auth::get('id');
				$detalleT->save();
				
				Session::set("SALIDA_ID",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('agregardetalles_guia/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('crearsalidas_guia/');
             }
		}
	}

	public function agregardetalles_guia()
	{
		$salidas= new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$detalles->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id=Session::get('DOC_ID');
			$salidas->femision=date('Y-m-d');
			$salidas->fvencimiento=date("Y-m-d",strtotime($salidas->femision."+ 30 days"));
			$salidas->estadosalida='Editable';
			$salidas->estado=1;
			$salidas->userid=Auth::get('id');
			$salidas->activo='1';
			$salidas->testipocambios_id=Session::get('CAMBIO_ID');
			$salidas->userid=Auth::get('id');
			$salidas->aclusuarios_id=Auth::get('id');
			$salidas->aclempresas_id=Session::get("EMPRESAS_ID");
            if ($salidas->save())
			{
				if(Session::get('DOC_ID')==15){
				$detalleT= new Prodetalletransportes(Input::post('prodetalletransportes'));
				$detalleT->tessalidas_id=$salidas->id;
				$detalleT->numero=$salidas->numero;
				$detalleT->serie=$salidas->serie;
				$detalleT->fechatraslado=$salidas->finiciotraslado;
				$detalleT->save();
				}
				Session::set("SALIDA_ID",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('agregardetalles_guia/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('agregardetalles_guia/');
             }
		}
	}
	
	public function grabardetalle_guia()
	{
		$val=Input::post('detalles');
		if (Input::hasPost('detalles')) 
		{
			$productos=new Tesproductos();
			$producto= $productos->find_first((int)$val['tesproductos_id']);
			$detalles= new Tesdetallesalidas(Input::post('detalles'));
			$detalles->tessalidas_id=Session::get('SALIDA_ID');
			$detalles->estado=1;
			$detalles->importe=$val['cantidad']*$val['preciounitario'];
			$detalles->pesoneto=$val['cantidad'];
			$detalles->userid=Auth::get('id');
			$detalles->activo='1';
			$detalles->userid=Auth::get('id');
			$detalles->aclusuarios_id=Auth::get('id');
			$detalles->aclempresas_id=Session::get("EMPRESAS_ID");
            if($detalles->save())
			{
			$this->detalles=$detalles->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
			}
		}
		
	}
public function eliminar($id=0)
{
	$detalles= new Tesdetallesalidas();
	$detalles->delete((int)$id);
	return Redirect::toAction('salidas/');
}
/*Finalizar y destruir la session creadas para la generacion de la salida*/
	public function versalida_guia()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
            if ($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida_guia/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$salidas= new Tessalidas();
		//$this->N=new numeroLetras();
		$detalles = new Tesdetallesalidas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$detalles->find('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
	}	
	
}
?>