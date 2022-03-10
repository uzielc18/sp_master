<?php 
View::template('spatricia/default');
Load::models('tesproductos','prorollos','tesdetalleingresos','aclempresas','pronotapedidos','prodetallepedidos','tesdetallesalidas','tesdetalleingresos','proprocesos','prodetalleprocesos','testipocambios','tessalidasinternas','tesletrassalidasinternas','tesdetallesalidasinternas');
class RollosController extends AppController
{
protected function before_filter() 
{
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
}
	 public function index($Y='',$m='') {
        
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		$this->titulo="INGRESO DE ROLLOS";
		$this->tipo_id=1;
		if (Input::hasPost('rollos')) {

            $obj = new Prorollos(Input::post('rollos'));
            $obj->prodetalleproduccion_id='0';
			$obj->estadorollo='Sin Revisar';
	 		$obj->estado='1';
	 		$obj->enalmacen='1';
			$obj->lote=date('time');
	 		$obj->userid=Auth::get('id');
            if ($obj->save()) {
                /* GRABAR EN EL INVENTARIO el QUE Este ACTIVO*/
				
				$detalle_inventario= new Tesdetalleingresos();
				$detalle_inventario->tesunidadesmedidas_id='2';
				$detalle_inventario->tesproductos_id=$obj->tesproductos_id;
				$detalle_inventario->tesingresos_id=Session::get('INVENTARIO_ID');
				$detalle_inventario->cantidad=$obj->metros;
				$detalle_inventario->preciounitario='1';
				$detalle_inventario->importe=$obj->metros;
				$detalle_inventario->lote='13576660753';
				$detalle_inventario->estado='1';
				$detalle_inventario->userid=Auth::get('id');
				$detalle_inventario->aclempresas_id=Session::get('EMPRESAS_ID');
				$detalle_inventario->tescolores_id=$obj->tescolores_id;
				$detalle_inventario->prorollos_id=$obj->id;
				if($detalle_inventario->save())Flash::valid('Ingreso al Inventario');
                return Redirect::redirect();
            }else{

                Flash::error('Falló Operación');
            return Redirect::redirect();
			}
        }else{
        // Solo es necesario para el autoForm
		
		 Flash::info('Esta Acción, estara activa solo para el ingreso de rollos a almacen por inventario!!!! ');
		$obj = new Prorollos();
		$this->prorollos=$obj->find('conditions: ISNULL(prorollos_id) AND prodetalleproduccion_id=0 AND DATE_FORMAT(fechacorte, "%Y-%m")="'.$anio.'-'.$mes_activo.'"');
		}
    }

    /**
     * Edita un Registro
     */
    public function editar($id) {
        View::select('index');
		$this->titulo="INGRESO DE ROLLO A TELA CRUDA";
		$this->tipo_id=1;
        //se verifica si se ha enviado via POST los datos
        if (Input::hasPost('rollos')) {
            $obj = new Prorollos();
            if ($obj->update(Input::post('rollos'))) {
                $detalle_inventario= new Tesdetalleingresos();
				$detalle=$detalle_inventario->find_first('conditions: prorollos_id='.$obj->id);
				$detalle->tesunidadesmedidas_id='2';
				$detalle->tesproductos_id=$obj->tesproductos_id;
				$detalle->tesingresos_id=Session::get('INVENTARIO_ID');
				$detalle->cantidad=$obj->metros;
				$detalle->preciounitario='1';
				$detalle->importe=$obj->metros;
				$detalle->lote='13576660753';
				$detalle->estado='1';
				$detalle->userid=Auth::get('id');
				$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
				$detalle->tescolores_id=$obj->tescolores_id;
				$detalle->prorollos_id=$obj->id;
				if($detalle->save())Flash::valid('Ingreso al Inventario');
				return Redirect::toAction('');
            }else{
               
            }
        }

        //Aplicando la autocarga de objeto, para comenzar la edición
        $obj = new Prorollos();
		$this->prorollos=$obj->find('conditions: prodetalleproduccion_id=0');
		
		$this->rollos=$obj->find_first((int)$id);
    }

    /**
     * Borra un Registro
     */
    public function borrar($id) {
		
		 $obj = new Prorollos();
		 $detalle_inventario= new Tesdetalleingresos();
		 if($detalle_inventario->exists('prorollos_id='.$id))
		 {
			 $detalle_inventario->delete('prorollos_id='.$id);
		 }
		if (!$obj->delete((int) $id)) 
		{
		Flash::error('Falló Operación');
		}
       return  Redirect::redirect();
    }

/*Rollos de Produccion*/

public function listado($estado='Sin procesos')
{
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$rollos= new Prorollos();
		$this->rollos=$rollos->find('conditions:  enalmacen="0" AND estadorollo!="Sin Revisar" AND estadorollo!="Control de Calidad"','order: numero ASC');
		$this->rollos_almacen=$rollos->find('conditions: estadorollo!="TERMINADO" AND enalmacen="1"','order: numero ASC');
}

/*
Ingreso de Tela al almacen mediante pedido tela cruda sin almacen
*/
public function ingresoalmacen()
{
	$RO=new Prorollos();
	$ingresar_rollos=$RO->count('id','conditions: enalmacen=0');
	if($ingresar_rollos>0){
		$Npedidos= new Pronotapedidos();
		
		if(!$Npedidos->exists("tipo='ingreso' AND origen='Telas' AND DATE_FORMAT( fecha_at,'%Y-%m-%d' ) =  '".date("Y-m-d")."'")){
		/*graba el pedido*/
		$nota= new Pronotapedidos();
		$nota->aclusuarios_id=Auth::get('id');
		$nota->tesdatos_id=$tesdatos_id=0;
		$nota->codigo=$Npedidos->codigo('ingreso');
		$nota->descripcion='Ingreso de Telas al almacen';
		$nota->observacion='Ingreso de Telas al almacen';
		$nota->estadonota='Entregado';
		$nota->estado=1;
		$nota->activo=1;
		$nota->aclempresas_id=Session::get('EMPRESAS_ID');
		$nota->tipo='ingreso';
		$nota->origen='Telas';
		
		if($nota->save()){		
		$ROLLOS= new Prorollos();
		$rollos=$ROLLOS->find('conditions: enalmacen=0 AND estadorollo!="VENDIDO"');
		foreach($rollos as $r): 		
		$obj=$OBJ= new Prodetallepedidos();
		$obj->pronotapedidos_id=$nota->id;
		$obj->tesproductos_id=$r->tesproductos_id;
		$obj->tescolores_id=$r->tescolores_id;
		$obj->descripcion='Ingreso Automatico de Telas a almacen';
		$obj->cantidad=$r->metros;
		if($r->prodetalleproduccion_id!=0)$obj->lote=$r->getProdetalleproduccion()->getProproduccion()->codigo;else $obj->lote=$r->lote;
		//$obj->lote=$r->lote;
		$obj->tesunidadesmedidas_id='2';/* METROS */
		$obj->precio=$r->getTesproductos()->precio;
		$obj->activo=1;
		$obj->estado=1;
		$obj->prorollos_id=$r->id;
		$obj->save();
		$mod_rollo=$ROLLOS->find_first((int)$r->id);
		$mod_rollo->enalmacen='1';
		$mod_rollo->save();
		endforeach;
		
		//$RO->update_all("enalmacen =  '1'","enalmacen=0");
		}
		
		}else
		{
		
		$nota=$Npedidos->find_first("conditions: tipo='ingreso' AND origen='Telas' AND DATE_FORMAT( fecha_at,  '%Y-%m-%d' ) =  '".date("Y-m-d")."'");	
		$ROLLOS= new Prorollos();
		$rollos=$ROLLOS->find('conditions: enalmacen="0" AND estadorollo!="VENDIDO" AND estadorollo!="Sin Revisar" AND estadorollo!="Control de Calidad"','order: numero ASC');
		foreach($rollos as $r): 		
		$obj=$OBJ= new Prodetallepedidos();
		$obj->pronotapedidos_id=$nota->id;
		$obj->tesproductos_id=$r->tesproductos_id;
		$obj->tescolores_id=$r->tescolores_id;
		$obj->descripcion='Ingreso Automatico de Telas a almacen';
		$obj->cantidad=$r->metros;
		$obj->lote=$r->Lote($r->id);
		$obj->tesunidadesmedidas_id='2';/*METROS*/
		$obj->precio=$r->getTesproductos()->precio;
		$obj->activo=1;
		$obj->estado=1;
		$obj->prorollos_id=$r->id;
		$obj->save();
		$mod_rollo=$ROLLOS->find_first((int)$r->id);
		$mod_rollo->enalmacen='1';
		$mod_rollo->save();
		endforeach;
		
		//$RO->update_all("enalmacen =  '1'","enalmacen=0");
		}
		Flash::valid("Telas enviadas a almacen Gracias");
	}else
	{
		Flash::warning("No existe ninguna Tela para mandar a Almcen");
	}
	
}
/*SALIDA DE ROLLOS*/
public function grabarventatelas($id=0,$id_detalle=0)
{
	if($id!=0)
	{
			$rollos= new Prorollos();
			$rollo=$rollos->find_first((int)$id);
			
			$detalle=new Tesdetallesalidas();
			$detalle->tessalidas_id=Session::get('SALIDA_ID');
			$detalle->tesunidadesmedidas_id='2';
			$detalle->tescolores_id=$rollo->tescolores_id;
			$detalle->tesproductos_id=$rollo->tesproductos_id;
			$detalle->cantidad=$rollo->metros;
			$detalle->pesoneto=$rollo->peso;
			$detalle->pesobruto=$rollo->peso;
			$detalle->lote=$rollo->lote;
			$detalle->prorollos_id=$rollo->id;
			$detalle->userid=Auth::get('id');
			$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
			$detalle->estado=1;
			if($detalle->save()){
				Flash::valid("modifique el precio del rollo si es necesario");
			$rollo->enalmacen='9';
			$rollo->estadorollo='VENDIDO';
			$rollo->save();
			$this->detalle=$detalle->find_first((int)$detalle->id);
				}
	}elseif($id_detalle!=0)
	{
		$detalle= new Tesdetallesalidas();
		$this->detalle=$detalle->find_first((int)$id_detalle);
		
	}else
	{
		Flash::warning("No existe ningun Rollo");
		return Redirect::toAction('index/');
	}
}


public function listadodetelasventa()
{
	$detalle= new Tesdetallesalidas();
	if (Input::hasPost('detalle')) 
		{
			$detalles= new Tesdetallesalidas(Input::post('detalle'));
			
            if ($detalles->save())
			{
			$monedas='0';
			$monedas=$detalles->getTessalidas()->tesmonedas_id;
			$cambios=new Testipocambios();
			$cam=$cambios->find_first($detalles->getTessalidas()->getCambio($detalles->getTessalidas()->femision));/*Cambiar por la session*/
					switch ($monedas)
					{
						case '1': //moneda es SOLES
								$precioS=$detalles->preciounitario;
								$precioD=($detalles->preciounitario/$cam->compra);
						 		break;
						case '2':  //moneda es Dolares
								$precioD=$detalles->preciounitario;
								$precioS=($detalles->preciounitario*$cam->compra);
						 		break;
						case '4':  //moneda es SOLES
								$precioS=$detalles->preciounitario;
								$precioD=($detalles->preciounitario/$cam->compra);
						 		break;
						case '5':  //moneda es Dolares
								$precioD=$detalles->preciounitario;
								$precioS=($detalles->preciounitario/$cam->compra);
						 		break;
						case '0':  //moneda es SOLES
								$precioS=$detalles->preciounitario;
								$precioD=($detalles->preciounitario/$cam->compra);
						 		break;
					}
					
					
				$producto = new Tesproductos();
				if($detalles->tesproductos_id!=0){
				$pr=$producto->find_first((int)$detalles->tesproductos_id);
				$pr->precio=$precioS;
				$pr->preciod=$precioD;
				$pr->save();
				}
				Flash::valid('EL precio del producto ahora es '.$pr->precio);
				$this->detalles=$detalle->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				$this->detalles=$detalle->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
			}
		}
	$this->detalles=$detalle->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
}

public function grabarsalidatelas($id=0)
{
	if($id!=0)
	{
			$rollos= new Prorollos();
			$rollo=$rollos->find_first((int)$id);
			
			$detalle=new Tesdetallesalidas();
			$detalle->tessalidas_id=Session::get('SALIDA_ID');
			$detalle->tesunidadesmedidas_id='2';
			$detalle->tescolores_id=$rollo->tescolores_id;
			$detalle->tesproductos_id=$rollo->tesproductos_id;
			$detalle->cantidad=$rollo->metros;
			$detalle->lote=$rollo->lote;
			$detalle->pesoneto=$rollo->peso;
			$detalle->pesobruto=$rollo->peso;
			$detalle->prorollos_id=$rollo->id;
			$detalle->userid=Auth::get('id');
			$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
			$detalle->estado=1;
			if($detalle->save()){
				Flash::valid("Agregado a guia");
			$rollo->enalmacen='2';
			if($rollo->estadorollo=='Re-Proceso'){$rollo->estadorollo='Tintoreria-2';}else{$rollo->estadorollo='Tintoreria';}
			$rollo->save();
			$this->detalles=$detalle->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
				}
			$this->detalles=$detalle->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
			
	}else
	{
		Flash::warning("No existe ningun Rollo");
		return Redirect::toAction('index/');
	}
}

public function grabarsalidatransformaciones($id=0)
{
	View::select('grabarsalidatelas');
	if($id!=0)
	{
			$rollos= new Prorollos();
			$rollo=$rollos->find_first((int)$id);
			
			$detalle=new Tesdetallesalidas();
			$detalle->tessalidas_id=Session::get('SALIDA_ID');
			$detalle->tesunidadesmedidas_id='2';
			$detalle->tescolores_id=$rollo->tescolores_id;
			$detalle->tesproductos_id=$rollo->tesproductos_id;
			$detalle->cantidad=$rollo->metros;
			$detalle->lote=$rollo->lote;
			$detalle->pesoneto=$rollo->peso;
			$detalle->pesobruto=$rollo->peso;
			$detalle->prorollos_id=$rollo->id;
			$detalle->userid=Auth::get('id');
			$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
			$detalle->estado=1;
			if($detalle->save()){
				Flash::valid("Agregado a guia");
			$rollo->enalmacen='8';
			$rollo->estadorollo='TR';
			$rollo->save();
			$this->detalles=$detalle->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
				}
			$this->detalles=$detalle->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
			
	}else
	{
		Flash::warning("No existe ningun Rollo");
		return Redirect::toAction('index/');
	}
}	
public function borrarsalidatransformaciones($detalle_id,$rollos_id)
{
	//View::select('index');
	$rollos= new Prorollos();
	$rollo = $rollos->find_first((int)$rollos_id);
	if($rollo){
	$rollo->enalmacen='1';/**/
	$rollo->estadorollo='VENTA';
	$rollo->save();
	}else{
		Flash::warning("No existe ninguna rollo");
	}
	$detalle=new Tesdetallesalidas();
	$detalle->delete($detalle_id);
	return Redirect::redirect('/santapatricia/confecciones/agregardetalles');
}
public function borrarsalidarollos($detalle_id,$rollos_id)
{
	//View::select('index');
	$rollos= new Prorollos();
	$rollo = $rollos->find_first((int)$rollos_id);
	if($rollo){
	$rollo->enalmacen='1';
	if($rollo->estadorollo=='Tintoreria-2')$rollo->estadorollo='Re-Proceso'; else $rollo->estadorollo='Sin procesos';
	$rollo->save();
	}else{
		Flash::warning("No existe ninguna rollo");
	}
	$detalle=new Tesdetallesalidas();
	$detalle->delete($detalle_id);
	return Redirect::redirect('/santapatricia/tintoreria/agregardetalles');
}

public function borrarventarollos($detalle_id,$rollos_id)
{
	//View::select('index');
	$rollos= new Prorollos();
	$rollo = $rollos->find_first((int)$rollos_id);
	if($rollo){
	$rollo->enalmacen='1';
	$rollo->estadorollo='VENTA';
	$rollo->save();
	}else{
		Flash::warning("No existe ninguna rollo");
	}
	$detalle=new Tesdetallesalidas();
	$detalle->delete($detalle_id);
	return Redirect::redirect('/santapatricia/ventas/agregardetalles');
}
public function borrarventarollos_i($detalle_id,$rollos_id)
{
	View::select('borrarventarollos');
	$rollos= new Prorollos();
	$rollo = $rollos->find_first((int)$rollos_id);
	if($rollo){
	$rollo->enalmacen='1';
	$rollo->estadorollo='VENTA';
	$rollo->save();
	}else{
		Flash::warning("No existe ninguna rollo");
	}
	$detalle=new Tesdetallesalidasinternas();
	$detalle->delete($detalle_id);
	return Redirect::redirect('/santapatricia/ventasinternas/agregardetalles');
}
/*SALIDA INTERNA DE ROLLOS*/
public function grabarventatelas_i($id=0,$id_detalle=0)
{
	if($id!=0)
	{
			$rollos= new Prorollos();
			$rollo=$rollos->find_first((int)$id);
			
			$detalle=new Tesdetallesalidasinternas();
			$detalle->tessalidasinternas_id=Session::get('SALIDA_ID_I');
			$detalle->tesunidadesmedidas_id='2';
			$detalle->tescolores_id=$rollo->tescolores_id;
			$detalle->tesproductos_id=$rollo->tesproductos_id;
			$detalle->cantidad=$rollo->metros;
			$detalle->pesoneto=$rollo->peso;
			$detalle->pesobruto=$rollo->peso;
			$detalle->lote=$rollo->lote;
			$detalle->prorollos_id=$rollo->id;
			$detalle->userid=Auth::get('id');
			$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
			$detalle->estado=1;
			if($detalle->save()){
			$rollo->enalmacen='9';
			$rollo->estadorollo='VENDIDO';
			$rollo->save();
			Flash::valid("modifique el precio del rollo si es necesario (".$rollo->estadorollo.')');
			$this->detalle=$detalle->find_first((int)$detalle->id);
				}
	}elseif($id_detalle!=0)
	{
		$detalle= new Tesdetallesalidasinternas();
		$this->detalle=$detalle->find_first((int)$id_detalle);
		
	}else
	{
		Flash::warning("No existe ningun Rollo");
		return Redirect::toAction('index/');
	}
}


public function listadodetelasventa_i()
{
	View::template(null);
	$detalle= new Tesdetallesalidasinternas();
	if (Input::hasPost('detalle')) 
		{
			$DET=Input::post('detalle');
			$detalles= new Tesdetallesalidasinternas(Input::post('detalle'));
			if(empty($DET['descuento']) || ($DET['descuento']==''))$d=0; else $d=$DET['descuento'];
			$desc=$d;
			$descuento=($DET['preciounitario']*$DET['cantidad'])*($desc/100);
            $detalles->importe=($DET['preciounitario']*$DET['cantidad'])-$descuento;
			if ($detalles->save())
			{
			$monedas='0';
			$monedas=$detalles->getTessalidasinternas()->tesmonedas_id;
			$cambios=new Testipocambios();
			$cam=$cambios->find_first($detalles->getTessalidasinternas()->getCambio($detalles->getTessalidasinternas()->femision));/*Cambiar por la session*/
					switch ($monedas)
					{
						case '1': //moneda es SOLES
								$precioS=$detalles->preciounitario;
								$precioD=($detalles->preciounitario/$cam->compra);
						 		break;
						case '2':  //moneda es Dolares
								$precioD=$detalles->preciounitario;
								$precioS=($detalles->preciounitario*$cam->compra);
						 		break;
						case '4':  //moneda es SOLES
								$precioS=$detalles->preciounitario;
								$precioD=($detalles->preciounitario/$cam->compra);
						 		break;
						case '5':  //moneda es Dolares
								$precioD=$detalles->preciounitario;
								$precioS=($detalles->preciounitario/$cam->compra);
						 		break;
						case '0':  //moneda es SOLES
								$precioS=$detalles->preciounitario;
								$precioD=($detalles->preciounitario/$cam->compra);
						 		break;
					}
					
					
				$producto = new Tesproductos();
				if($detalles->tesproductos_id!=0){
				$pr=$producto->find_first((int)$detalles->tesproductos_id);
				$pr->precio=$precioS;
				$pr->preciod=$precioD;
				$pr->save();
				}
				Flash::valid('EL precio del producto ahora es '.$pr->precio);
				$this->detalles=$detalle->find('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				$this->detalles=$detalle->find('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
			}
		}
	$this->detalles=$detalle->find('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
}


/*INGRESO DE ROLLOS
id recibe el id del detalle de salida de rollos

*/
public function grabaringresotelas($id=0,$rollos_id=0)
{
	if($id!=0)
	{
		if (Input::hasPost('detalleingreso')) 
		{
			$detalle_i= new Tesdetalleingresos(Input::post('detalleingreso'));
            if ($detalle_i->save())
			{
				Flash::valid('EL rollo fue ingresado!!!');
				Redirect::redirect('/santapatricia/tintoreria/ingresorollos');
				$rollos= new Prorollos();
				$rollo=$rollos->find_first((int)$detalle_i->prorollos_id);
				$rollo->metros=$detalle_i->cantidad;
				$rollo->peso=$detalle_i->pesoneto;
				$rollo->enalmacen='1';
				$rollo->estadorollo='Control de Calidad Venta';
				$rollo->save();
				//Flash::valid('La Salida se realizo con exito!!!');
				//Redirect::redirect('/santapatricia/tintoreria/agregardetalles_ingreso');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}else{
			
			if($rollos_id==0)
			{
				$detalle=new Tesdetallesalidas();
				$detalle_s=$detalle->find_first((int)$id);
				$detalle_i=new Tesdetalleingresos();
				$detalle_i->tesingresos_id=Session::get('INGRESO_ID');
				$detalle_i->tesunidadesmedidas_id='2';
				$detalle_i->tescolores_id=$detalle_s->tescolores_id;
				$detalle_i->tesproductos_id=$detalle_s->tesproductos_id;
				$detalle_i->cantidad=$detalle_s->cantidad;
				$detalle_i->lote=$detalle_s->lote;
				$detalle_i->pesoneto=$detalle_s->pesoneto;
				$detalle_i->pesobruto=$detalle_s->pesoneto;
				$detalle_i->prorollos_id=$detalle_s->prorollos_id;
				$detalle_i->userid=Auth::get('id');
				$detalle_i->aclempresas_id=Session::get('EMPRESAS_ID');
				$detalle_i->estado=1;
				if($detalle_i->save()){
				//Flash::valid("Agregado a guia");
				
				$rollos= new Prorollos();
				$rollo=$rollos->find_first((int)$detalle_i->prorollos_id);
				$rollo->metros=$detalle_i->cantidad;
				$rollo->peso=$detalle_i->pesoneto;
				$rollo->enalmacen='1';
				$rollo->estadorollo='Control de Calidad Venta';
				$rollo->save();
				/*agregar la observacion al procesodetalle*/
				$PR= new Proprocesos();
				$proproceso=$PR->find_first('conditions: tessalidas_id='.$detalle_s->getTessalidas()->id);
				$detalle_p= new Prodetalleprocesos();
				$Prodetalleprocesos=$detalle_p->find_first('conditions: prorollos_id='.$detalle_i->prorollos_id.' AND proprocesos_id='.$proproceso->id);
				$Prodetalleprocesos->estado='2';
				if($Prodetalleprocesos->save())//Flash::valid("Se cambio el estado");
				$this->detalleingreso=$detalle_i;
				Flash::valid('EL rollo fue ingresado!!!');
				Redirect::redirect('/santapatricia/tintoreria/ingresorollos');
				}
			}else
			{
				$detalle_i=new Tesdetalleingresos();
				$this->detalleingreso=$detalle_i->find_first((int)$id);
				$detalle_p= new Prodetalleprocesos();
				$this->Prodetalleprocesos=$detalle_p->find_first('conditions: prorollos_id='.$rollos_id.' AND estado=1');
			}
	}
			
	}else
	{
		Flash::warning("No existe ningun Rollo");
	}
	//Redirect::redirect('/santapatricia/tintoreria/agregardetalles');
}	
public function borraringresorollos($detalle_id,$rollos_id)
{
	//View::select('index');
	$rollos= new Prorollos();
	$rollo = $rollos->find_first((int)$rollos_id);
	if($rollo){
	$rollo->enalmacen='2';
	$rollo->estadorollo='Tintoreria';
	$rollo->save();
	$detalle_p= new Prodetalleprocesos();
	$Prodetalleprocesos=$detalle_p->find_first('conditions: estado=2 AND prorollos_id='.$rollos_id);
	$Prodetalleprocesos->estado=1;
	$Prodetalleprocesos->save();
	}else{
		Flash::warning("No existe ningun rollo");
	}
	$detalle=new Tesdetalleingresos();
	$detalle->delete($detalle_id);
	Redirect::redirect('/santapatricia/tintoreria/agregardetalles_ingreso');
}

public function modificar($page=1)
{
	if(Auth::get('id')==3 || Auth::get('id')==2)
	{
	
	Flash::info('Ingrese los datos de los rollos');
	$this->R=$prorollos=new Prorollos();
	$this->prorollos=$prorollos->paginate('conditions: estadorollo="Sin procesos" AND prodetalleproduccion_id=0','order: numero DESC','perpage: 15','page: '.$page);
	$this->productos=$prorollos->getProductoslinatelas();
	
	}else
	{
		Flash::warning("Esta accion esta restringida para los Administrador, por favor acercarse al area de sistemas...!");
		Redirect::redirect('/santapatricia/tintoreria/salidas');
	}
}
public function grabarrollo_modificar($page)
{
	
	$a=Input::post('a');
	if(Input::hasPost('prorollos'.$a))
	{
		$prorollos=new Prorollos();
		$post=Input::post('prorollos'.$a);
		if($prorollos->save(Input::post('prorollos'.$a)))
		{
			Flash::valid("Rollo Modificado");
			$this->rollo=$prorollos->find_first($prorollos->id);
			$this->productos=$prorollos->getProductoslinatelas();
		}
		$this->page=$page;
	}
	
	
	
}

public function repara_info_de_sub_rollos()
{
	/*Modificar los campos =>prodetalleproduccion_id, tesproductos_id, numeroordencompra, tesordendecompras_id*/
	$prorollos=new Prorollos();
	$rollos=$prorollos->find('conditions: !ISNULL(prorollos_id)','group: prorollos_id');
	$ids='';
	foreach($rollos as $item)
	{
		$rollo=$prorollos->find_first($item->prorollos_id);
		
		if($rollo)
		{
		$ids.=$rollo->id.'(';
		if($prorollos->exists('prorollos_id='.$rollo->id))
		{
			$subrollos=$prorollos->find('conditions: (ISNULL(ordencompra) OR ISNULL(tesordendecompras_id)) AND prorollos_id='.$rollo->id);
			foreach($subrollos as $sub)
			{
				//$ids.=$sub->id.',';
				$sub_rollo=$prorollos->find_first($sub->id);
				$sub_rollo->prodetalleproduccion_id=$rollo->prodetalleproduccion_id;
				$sub_rollo->tesproductos_id=$rollo->tesproductos_id;
				$sub_rollo->numero=$rollo->numero;
				$sub_rollo->ordencompra=$rollo->ordencompra;
				$sub_rollo->tesordendecompras_id=$rollo->tesordendecompras_id;
				if($sub_rollo->save())
				{
					$ids.=$sub->id.',';
				}
			}
		}
		$ids.='), ';
		}else
		{
			$ids.=$item->prorollos_id.' Este id no tiene Rollo';
		}
	}
	Flash::valid('Ides modificados '.$ids);
	return Redirect::toAction('ingresoalmacen/');
}

public function unir($id=0)
{
	$productos = new Tesproductos();
	$this->producto=NULL;
	if($id!=0){
	$this->producto=$productos->find_first($id);
	$this->rollos=$prorollos->find('conditions: tesproductos_id='.$id.' AND estadorollo="VENTA"');
	}
}


public function unirollos()
{
	$prorollos=new Prorollos();
	$this->rollo=$prorollos->find_first((int)$id_rollo_principal);
	$this->rollos=$prorollos->find('conditions: prorollos_id='.$id_rollo_principal.' AND estadorollo="VENTA"');
}


}

?>