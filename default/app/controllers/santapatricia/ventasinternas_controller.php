<?Php 
View::template('spatricia/default');
//,'tessalidasinternas','tesletrassalidasinternas'
Load::models('tesproductos','testipoproductos','teslineaproductos','tesdocumentos','tessalidasinternas','prodetalletransportes','tesdetallesalidasinternas','prorollos','aclempresas','tesingresos','tesletrassalidasinternas','tesaplicacionletrainterna','testipocambios');
class VentasinternasController extends AdminController
{
	protected function before_filter() 
	{
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
	}	
	public function index($Y='',$m='')
	{
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		$salidas= new Tessalidasinternas();
		$this->ventas= $salidas->find('conditions: aclempresas_id='.Session::get("EMPRESAS_ID").' AND serie="999" AND estadosalida!="TERMINADO" AND (npedido like "VT%" OR npedido like "PT%") AND estado=1 AND DATE_FORMAT(femision, "%Y-%m")="'.$anio.'-'.$mes_activo.'"','order: fecha_at ASC');
		Session::delete("SALIDA_ID_I");
		Session::delete('DOC_ID');
		Session::delete('DOC_NOMBRE');
		Session::delete('DOC_ABR');
		Session::delete('DOC_CODIGO');
	}
	public function cargarsalida($id=0,$url='agregardetalles')
	{
		if($id!=0){
		Session::set("SALIDA_ID_I",$id);
		}else
		{
			Session::delete("SALIDA_ID_I");
		}
		return Redirect::toAction($url);
	}	
public function guias($id=15,$pedido='VT')
	{
		//if($id==7)return Redirect::toAction('facturas');
		$this->titulo='Guias sin factura';
		$this->url='crearsalidas';
		$this->Ped=$pedido;
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)$id);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		/*
		serie 001
		salida de de rollos para venta
		*/
		$salidas= new Tessalidasinternas();
		$this->salidas= $salidas->find('conditions: aclempresas_id='.Session::get("EMPRESAS_ID").' AND serie="999" AND estadosalida!="Con factura" AND npedido like "'.$pedido.'%" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: id DESC limit 100');
		Session::delete("SALIDA_ID_I");
	}
	
public function crearsalidas()
	{
		$SALD=new Tessalidasinternas();
		$this->salida['serie']='999';
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'999');
		$this->salida['npedido']=$SALD->getNumeropedido('VT','999');
		$this->salida['numerofactura']=$SALD->generarNumeroFactura('999');
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
			$salidas->tesdocumentos_id=Session::get('DOC_ID');
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
				
				Session::set("SALIDA_ID_I",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('agregardetalles/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('crearsalidas/');
             }
		}
	}
/*salida para muestra*/

public function crearsalidas_muestra()
	{
		$SALD=new Tessalidasinternas();
		$this->salida['serie']='999';
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'999','MT');
		$this->salida['npedido']=$SALD->getNumeropedido('MT','999');
		$this->salida['numerofactura']=$SALD->generarNumeroFactura('999');
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
			$salidas->tesdocumentos_id=Session::get('DOC_ID');
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
				
				Session::set("SALIDA_ID_I",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('agregardetalles/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('crearsalidas/');
             }
		}
	}
public function crear($id=0)
	{
		$SALD=new Tessalidasinternas();
		if($id==0){
		$this->salida['serie']='999';
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'999');
		$this->salida['npedido']=$SALD->getNumeropedido('VT','999');
		$this->salida['numerofactura']=$SALD->generarNumeroFactura('999');
		}else
		{
		$this->salida=$SALD->find_first($id);
		$DETALLES=new Tesdetallesalidasinternas();
		$this->detalle=$DETALLES->find_first('conditions: tessalidasinternas_id='.(int)$id);
		}
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
			$salidas->tesdocumentos_id=Session::get('DOC_ID');
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
				Session::set("SALIDA_ID_I",$salidas->id);
				
				if (Input::hasPost('detalle')) 
				{
					$detalles= new Tesdetallesalidasinternas(Input::post('detalle'));
					$detalles->tessalidasinternas_id=Session::get('SALIDA_ID_I');
					$detalles->cantidad=1;
					$detalles->tesunidadesmedidas_id='15';
					$detalles->userid=Auth::get('id');
					$detalles->estado='1';
					$detalles->tescolores_id='1';
					$detalles->aclempresas_id=Session::get("EMPRESAS_ID");
					$detalles->save();
				}
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('crear_detalle/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('crearsalidas/');
             }
		}
	}

public function crear_detalle($s='servicio')
{
	$this->s=$s;
	$salidas=new Tessalidasinternas();
		$this->salida=$salidas->find_first(Session::get('SALIDA_ID_I'));
		$DETALLES=new Tesdetallesalidasinternas();
		$this->detalles=$DETALLES->find('conditions: tessalidasinternas_id='.(int)Session::get('SALIDA_ID_I'));
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
            if($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				return Redirect::toAction('crear_detalle/');
			}
		}
}	
	public function agregardetalles()
	{
		$salidas= new Tessalidasinternas();
		$detalles = new Tesdetallesalidasinternas();
		$rollos=new Prorollos();
		$this->rollos=$rollos->find('conditions: (estadorollo="VENTA") AND enalmacen="1"');
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID_I'));
		$this->detalles=$detalles->find('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
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
				
				Session::set("SALIDA_I_ID",$salidas->id);
            	//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Edito la Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('agregardetalles/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('agregardetalles/');
             }
		}
	}
	
	public function venta()
	{
		$salidas=new Tessalidasinternas();
		$this->salida=$salidas->find_first(Session::get('SALIDA_ID_I'));
		$DETALLES=new Tesdetallesalidasinternas();
		$this->detalles=$DETALLES->find('conditions: tessalidasinternas_id='.(int)Session::get('SALIDA_ID_I'));
		$adelantos= new Tesletrassalidasinternas();
		/*valida la existenca de letras por adelanto*/
		$this->adelantos=FALSE;
		if($salidas->exists('tesdatos_id='.$this->salida->tesdatos_id.' AND npedido like "LA%"')){
		$this->adelantos=$salidas->LetraAdelanto($this->salida->tesdatos_id);
		}
		/* Busa las aplicaciones a esta factura */
		$aplicaciones= new Tesaplicacionletrainterna();
		$this->aplicaciones=FALSE ;
		if($aplicaciones->exists('tessalidasinternas_id='.$this->salida->id)){
			
		$this->aplicaciones =$aplicaciones->find('conditions: tessalidasinternas_id='.$this->salida->id);
		}
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
            if ($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				return Redirect::toAction('venta/');
			}
		}
	}

	
	public function versalida()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
            if ($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$salidas= new Tessalidasinternas();
		$detalles = new Tesdetallesalidasinternas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID_I'));
		$this->detalles=$detalles->find('conditions: tessalidasinternas_id='.(int)Session::get("SALIDA_ID_I"));
		$letras = new Tesletrassalidasinternas();
		$this->letras=$letras->count('estadoletras="Editable" AND factura_id='.(int)Session::get("SALIDA_ID_I"));
		$this->l_f=$letras->count('estadoletras="Sin Numero" AND factura_id='.(int)Session::get("SALIDA_ID_I"));
		$letrassalida=new Tesletrassalidasinternas();
		$this->lets=$letrassalida->find('conditions: factura_id='.Session::get('SALIDA_ID_I'));
		/* Busa las aplicaciones a esta factura */
		$aplicaciones= new Tesaplicacionletrainterna();
		$this->aplicaciones=FALSE ;
		if($aplicaciones->exists('tessalidasinternas_id='.$this->salida->id)){
			
		$this->aplicaciones =$aplicaciones->find('conditions: tessalidasinternas_id='.$this->salida->id);
		}
		/*valida la existenca de letras por adelanto*/
		//$letras_adelanto=$salidas->LetraAdelanto();
		
		
		
	}
	
	public function versalida_i()
	{
		
		if(Session::get('DOC_ID')==7){View::select('versalidafactventa_i');}
		if(Session::get('DOC_ID')==15){View::select('versalidaguiaventa');}
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas= new Tessalidasinternas();
		$detalles = new Tesdetallesalidasinternas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID_I'));
		$this->detalles=$detalles->find('conditions: tessalidasinternas_id='.(int)Session::get("SALIDA_ID_I"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
		$letrassalida=new Tesletrassalidasinternas();
		$this->lets=$letrassalida->find('conditions: factura_id='.Session::get('SALIDA_ID_I'));
		
	}



public function versalidafactventa_w(){
	View::template(null);
	}
public function versalidafactventa_i(){
	}
public function versalidafactventa(){}
public function versalidaguiaventa(){}
/*
# $id: el id de la salida interna 
# $l_id: el id de la letra por adelanto
# $total: el total de la guia interna 
*/
public function aplicaradelanto_interno($id,$l_id,$total=0)
{
	$adelantos= new Tesletrassalidasinternas();
	$tessalidas= new Tessalidasinternas();
	/*el $t_factura_a es la resta de total de factura adleanto menos la suma de todas las plicaciones */
	$t_factura_a=$tessalidas->totalAplicacion($l_id);
	//echo 'TOTAL DE LA APLICACION =>'.$t_factura_a;
	/*el $t_factura_a es la resta de total de factura menos la suma de todas las plicaciones */
	$t_salida_a=$total;//$tessalidas->getTotal_aplicacion($id);
	//echo 'TOTAL DE LA SALIDA =>'.$t_salida_a;
	/*
	###### validar si la diferencia es mayor que sero y menor 0.5
	*/
	//echo 'la diferencia es '.
	$diferencia=$t_salida_a-$t_factura_a;
	if($diferencia>0 && $diferencia<0.1)
	{
		//echo "se suma la diferencia";
		$t_factura_a=$t_factura_a+$diferencia;
		//echo "el nuevo monto es ".$t_factura_a;
	}
	$adelanto=$adelantos->find_first($l_id);/*la letra*/
	
	$this->salida=$salida=$tessalidas->find_first($id);/*la salida */
	 
	 $aplicacion= new Tesaplicacionletrainterna();
	 $aplicacion->tesletrassalidasinternas_id=$adelanto->id;
	 $aplicacion->tesmonedas_id=$salida->tesmonedas_id;
	 $aplicacion->numerodefactura=$salida->serie.'-'.$salida->numero;
	 if($t_salida_a>=$t_factura_a)$aplicacion->monto=$t_factura_a;else $aplicacion->monto=$t_salida_a;
	 $aplicacion->estado='1';
	 $aplicacion->userid=Auth::get('id');
	 $aplicacion->tessalidasinternas_id=$salida->id;
	 $aplicacion->aclempresas_id=Session::get("EMPRESAS_ID");
	 $aplicacion->aclusuarios_id=Auth::get('id');
	 $aplicacion->save();
	if($t_factura_a>$t_salida_a){	
	$salida->estadosalida='ADELANTADO';
	$salida->save(); 
	$adelantos->find_first((int)$adelanto->id);
	$adelantos->estado='1';
	$adelantos->estadoletra='Pendiente';
	$adelantos->save();
	Flash::valid('El total del adelanto es mayor que el total de la factura '.$t_factura_a.' > '.$t_salida_a.'');
	}else{
	$salida->estadosalida='PAGADO';
	$salida->save(); 
	$adelantos->find_first((int)$adelanto->id);
	$adelantos->estado='9';
	$adelantos->estadoletra='Pendiente';
	$adelantos->save();
	Flash::valid('El total del adelanto es menor o igual a de la factura '.$t_factura_a.' =< '.$t_salida_a.'');
	}
	 
		
		$DETALLES=new Tesdetallesalidasinternas();
		$this->detalles=$DETALLES->find('conditions: tessalidasinternas_id='.(int)Session::get('SALIDA_ID_I'));
		$adelantos= new Tesletrassalidasinternas();
		/*valida la existenca de letras por adelanto*/
		$this->adelantos=FALSE;
		if($tessalidas->exists('tesdatos_id='.$this->salida->tesdatos_id.' AND npedido like "LA%" AND estadosalida!="PAGADO"')){
			
		$this->adelantos=$tessalidas->LetraAdelanto($this->salida->tesdatos_id);
		}
		/* Busa las aplicaciones a esta factura */
		$aplicaciones= new Tesaplicacionletrainterna();
		$this->aplicaciones=FALSE ;
		if($aplicaciones->exists('tessalidasinternas_id='.$this->salida->id)){
			
		$this->aplicaciones =$aplicaciones->find('conditions: tessalidasinternas_id='.$this->salida->id);
		}
	$monedas= $salida->tesmonedas_id;
	switch ($monedas)
	{
		case 1: $this->simbolo='S/. ';$this->moneda_letras='NUEVOS SOLES'; break;
		case 2: $this->simbolo='$. ';$this->moneda_letras='DOLARES AMERICANOS';  break;
		case 4: $this->simbolo='S/. ';$this->moneda_letras='NUEVOS SOLES';  break;
		case 5: $this->simbolo='$. ';$this->moneda_letras='DOLARES AMERICANOS';  break;
		case 0: $this->simbolo='S/. ';$this->moneda_letras='NUEVOS SOLES';  break;
	}	
}
/*recalcular monto de letrainterna*/
public function limpiar_letra($id)
{
	$aplicaciones= new Tesaplicacionletrainterna();
	$aplicaciones->delete('estado=0');
	$LETRAS=new Tesletrassalidasinternas();
	$LETRAS->getLetraAdelanto($id);
  	return Redirect::toAction('venta/');
}

/*ELiminar las facturas
recibe le id de la app
*/
public function eliminar_app($id)
{
	$aplicaciones= new Tesaplicacionletrainterna();
	$app=$aplicaciones->find_first($id);
	$aplicaciones->delete($id);
	$LETRAS=new Tesletrassalidasinternas();
	$LETRAS->getLetraAdelanto($app->tesletrassalidasinternas_id);
	return Redirect::toAction('venta/');
}
/*Anular Guia*/
public function anularguias($id)
{
  $salidas= new Tessalidasinternas();	
  $salidas->anularguia($id);
  
  return Redirect::toAction('guias/15');
}	

	
public function cargar_doc($id=0,$url='nota_creditos')
{
	$documentos= new Tesdocumentos();
	$doc= $documentos->find_first((int)$id);
	Session::set('DOC_ID',$doc->id);
	Session::set('DOC_NOMBRE',$doc->nombre);
	Session::set('DOC_ABR',$doc->abr);
	Session::set('DOC_CODIGO',$doc->codigo);
	return Redirect::toAction($url);
}
public function crear_nota($id=0)
	{
		$npedido='ND';
		$this->url='nota_debitos';
		if(Session::get('DOC_ID')==13)$npedido='NC';$this->url='nota_creditos';
		$SALD=new Tessalidasinternas();
		if($id==0){
		$this->salida['serie']='999';
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'999',$npedido);
		$this->salida['npedido']=$SALD->getNumeropedido($npedido,'999');
		}else
		{
		$this->salida=$SALD->find_first($id);
		$DETALLES=new Tesdetallesalidasinternas();
		$this->detalle=$DETALLES->find_first('conditions: tessalidasinternas_id='.(int)$id);
		}
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
			$salidas->tesdocumentos_id=Session::get('DOC_ID');
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
				Session::set("SALIDA_ID_I",$salidas->id);
            	
				if (Input::hasPost('detalle')) 
				{
					$detalles= new Tesdetallesalidasinternas(Input::post('detalle'));
					$detalles->tessalidasinternas_id=Session::get('SALIDA_ID_I');
					$detalles->cantidad=1;
					$detalles->tesunidadesmedidas_id='15';
					$detalles->userid=Auth::get('id');
					$detalles->estado='1';
					$detalles->tescolores_id='1';
					$detalles->aclempresas_id=Session::get("EMPRESAS_ID");
					$detalles->save();
				}
				
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('detalle_nota/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 $this->detalle=Input::post('detalle');
				 return Redirect::toAction('crear_nota/');
             }
		}
	}

public function detalle_nota()
{
	$salidas=new Tessalidasinternas();
		$this->salida=$salidas->find_first(Session::get('SALIDA_ID_I'));
		$DETALLES=new Tesdetallesalidasinternas();
		$this->detalles=$DETALLES->find('conditions: tessalidasinternas_id='.(int)Session::get('SALIDA_ID_I'));
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
            if($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				$url='notadebito';
				if(Session::get('DOC_ID')==13)$url='notacredito';
				return Redirect::toAction($url.'/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				return Redirect::toAction('detalle_nota/');
			}
		}
}

public function nota_creditos()
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	$tessalidas= new Tessalidasinternas();
	$this->salidas=$tessalidas->find('conditions: aclempresas_id='.Session::get("EMPRESAS_ID").' AND tesdocumentos_id='.Session::get("DOC_ID"));
}

public function nota_debitos(){
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	$tessalidas= new Tessalidasinternas();
	$this->salidas=$tessalidas->find('conditions: aclempresas_id='.Session::get("EMPRESAS_ID").' AND tesdocumentos_id='.Session::get("DOC_ID"));
	}

public function notacredito()
{
	$salidas=new Tessalidasinternas();
		$this->salida=$salidas->find_first(Session::get('SALIDA_ID_I'));
		$DETALLES=new Tesdetallesalidasinternas();
		$this->detalles=$DETALLES->find('conditions: tessalidasinternas_id='.(int)Session::get('SALIDA_ID_I'));
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
            if($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				$url='notadebito';
				if(Session::get('DOC_ID')==13)$url='notacredito';
				return Redirect::toAction($url.'/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				return Redirect::toAction('detalle_nota/');
			}
		}
}

public function notadebito()
{
	$salidas=new Tessalidasinternas();
		$this->salida=$salidas->find_first(Session::get('SALIDA_ID_I'));
		$DETALLES=new Tesdetallesalidasinternas();
		$this->detalles=$DETALLES->find('conditions: tessalidasinternas_id='.(int)Session::get('SALIDA_ID_I'));
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
            if($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				$url='notadebito';
				if(Session::get('DOC_ID')==13)$url='notacredito';
				return Redirect::toAction($url.'/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				return Redirect::toAction('detalle_nota/');
			}
		}
}
public function numero_ruc()
{
	$this->data='';
	$q=$_GET['q'];
	$salidas=new Tessalidasinternas();
	$ss=$salidas->getNumeroruc($q);
	foreach ($ss as $value)
		{
			$json['id'] =$value->numero;
			$json['name'] = $value->numero;
			$this->data[] = $json;
		}
}
public function actualizar_monto_incadina()
{
	$salidas=new Tessalidasinternas();
	$salidas_mod=$salidas->find_all_by_sql('SELECT s.id,s.total,sum(d.importe) as importe FROM tessalidasinternas as s, tesdetallesalidasinternas as d WHERE s.id=d.tessalidasinternas_id AND s.tesdatos_id=2122 AND s.tesdocumentos_id=15
GROUP BY s.id');
foreach($salidas_mod as $s)
{
	$SA=new Tessalidasinternas();
	$SA=$salidas->find_first($s->id);
	$SA->total=$s->importe;
	$SA->save();
}
 return Redirect::toAction('');
}



}
?>