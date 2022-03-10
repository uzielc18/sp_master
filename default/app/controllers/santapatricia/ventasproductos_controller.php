<?php 
View::template('spatricia/default');
Load::models('aclempresas','tesproductos','testipoproductos','teslineaproductos','tesdocumentos','tessalidas','prodetalletransportes','tesdetallesalidas','tessalidasinternas','tesdetallesalidasinternas','tesfacturasadelantos','tesaplicacionfacturasadelantos','tesletrassalidasinternas','tesaplicacionletrainterna','testipocambios');
class VentasproductosController extends AppController
{
	public $per_page=30;
	protected function before_filter() {
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
		$salidas= new Tessalidas();
		$this->ventas= $salidas->find('conditions: aclempresas_id='.Session::get("EMPRESAS_ID").' AND (serie="F001" OR serie="F002" OR serie="001") AND npedido like "PT%"  AND estado=1 AND tesdocumentos_id=7 AND DATE_FORMAT(femision, "%Y-%m")="'.$anio.'-'.$mes_activo.'"','order: numero ASC');
		Session::delete("SALIDA_ID");
		Session::delete('DOC_ID');
		Session::delete('DOC_NOMBRE');
		Session::delete('DOC_ABR');
		Session::delete('DOC_CODIGO');
}
	
public function salidas($id=15)
{
		$this->titulo='Guias sin factura';
		$this->url='crearsalidas';
		if($id==7)return Redirect::toAction('f_salidas/'.$id);
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)$id);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		/*
		serie 002
		salida de Hilos
		*/
		$salidas= new Tessalidas();
		$this->salidas= $salidas->find('conditions:  (serie="F001" OR serie="001" OR serie = "F002") AND estadosalida!="Con factura" AND npedido like "PT%" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID')." AND aclempresas_id=".Session::get("EMPRESAS_ID"),'order: numero DESC limit 10');
		Session::delete("SALIDA_ID");		
}
	public function f_salidas($id=7)
	{
		$this->titulo='Facturas del mes '.date("M");
		$this->url='crearsalidas';
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)$id);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		/*
		serie 002
		salida de Hilos
		*/
		$salidas= new Tessalidas();
		$this->salidas= $salidas->find('conditions: aclempresas_id='.Session::get("EMPRESAS_ID").' AND (serie="F001" OR serie="001" OR serie = "F002") AND (npedido like "PT-%") AND tesdocumentos_id='.Session::get('DOC_ID'),'order: femision DESC limit 15');
		Session::delete("SALIDA_ID");
		$this->guias= $salidas->find('conditions:  aclempresas_id='.Session::get("EMPRESAS_ID").' AND (serie="F001" OR serie="001" OR serie = "F002") AND estadosalida="Pendiente" AND npedido like "PT%" AND estado=1 AND tesdocumentos_id=15','order: numero ASC');
	}
	public function crearsalidas()
	{
		$SALD=new Tessalidas();
		if(Session::has('SALIDA_ID'))
		{
			$this->salida=$SALD->find_first((int)Session::get('SALIDA_ID'));
		}else
		{
		$se='001';
		if(Session::get('DOC_ID')==9)$se='999';	
		$this->salida['serie']=$se;
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),$se,'PT');
		$this->salida['npedido']=$SALD->getNumeropedido('PT',$se);
		$this->salida['numerofactura']=$SALD->generarNumeroFactura('F002');	
		}
		
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
				$detalleT->estado='1';
				$detalleT->userid=Auth::get('id');
				$detalleT->save();
				}
				Session::set("SALIDA_ID",$salidas->id);
            	
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
	/*INGRESA CON SESSION SALIDA_ID*/
	public function agregardetalles()
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
				
                return Redirect::toAction('agregardetalles/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('agregardetalles/');
             }
		}
	}
	
	public function grabardetalle()
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
	return Redirect::toAction('agregardetalles/');
}
/*Finalizar y destruir la session creadas para la generacion de la salida de hilo*/
	public function versalida()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
            if ($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida/');
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
	
	public function versalida_i()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		
		$salidas= new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$detalles->find('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
	}
	
	public function cargarsalida($id=0,$url='agregardetalles')
	{
		if($id!=0){
		Session::set("SALIDA_ID",$id);
		}else
		{
			Session::delete("SALIDA_ID");
		}
		return Redirect::toAction($url);
	}
	/*
	recibe el id de la guia para crear la factura
	*/
	public function generarfactura()
	{
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)7);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		if (Input::hasPost('facturas')) 
		{
			$values=Input::post('facturas');
			$VAL=explode(',',$values['ids']);
			
			$g=0;
			$guias='';
			foreach($VAL as $value=>$item)
			{
				$salidas=new Tessalidas();
				$salida=$salidas->find_first((int)$item);
				$salida->estadosalida="Con factura";
				$salida->save();
				if($g==0)$coma='';else $coma=',';
				$guias.=$coma.$salida->serie.'-'.$salida->numero;
				$g++;
			}
			
			$n=0;
			foreach($VAL as $value=>$item)
			{
				if($n==0){
				$salidas=new Tessalidas();
				$salida=$salidas->find_first((int)$item);
				$new_salida= new Tessalidas();
				$new_salida->aclusuarios_id	=$salida->aclusuarios_id;
				$new_salida->tesmonedas_id=$salida->tesmonedas_id;
				$new_salida->tesdatos_id=$salida->tesdatos_id;
				$new_salida->direccion_entrega=$salida->direccion_entrega;
				$new_salida->tesdocumentos_id=7;
				$new_salida->testipocambios_id=$salida->testipocambios_id;
				$new_salida->codigo=$salida->codigo;
				$new_salida->serie='001';
				$V=explode('-',$salida->numerofactura);
				$new_salida->numero=$V[1];
				$new_salida->femision=$salida->femision;
				$new_salida->fvencimiento=$salida->fvencimiento;
				$new_salida->fregistro=$salida->fregistro;
				$new_salida->npedido=$salida->npedido;
				$new_salida->numeroguia=$guias;
				$new_salida->ordendecompra=$salida->ordendecompra;
				$new_salida->glosa=$salida->glosa;
				$new_salida->cuentagastos=$salida->cuentagastos;
				$new_salida->cuentaporpagar=$salida->cuentaporpagar;
				$new_salida->totalconigv=$salida->totalconigv;
				$new_salida->totalenletras=$salida->totalenletras;
				$new_salida->movimiento=$salida->movimiento;
				$new_salida->finiciotraslado=$salida->finiciotraslado;
				$new_salida->estado=$salida->estado;
				$new_salida->estadosalida='Editable';
				$new_salida->userid=$salida->userid;
				$new_salida->aclempresas_id=$salida->aclempresas_id;
				$new_salida->tescuentascorrientes_id=$salida->tescuentascorrientes_id;
				$new_salida->hilodestino_id=$salida->hilodestino_id;
				$new_salida->tescondicionespagos_id=$salida->tescondicionespagos_id;				
				$new_salida->tesdatosdirecciones_id=$salida->tesdatosdirecciones_id;
				$new_salida->save();
				Session::set("SALIDA_ID",$new_salida->id);
				}
				
				$DETALLES=new Tesdetallesalidas();
				$detalle=$DETALLES->find('conditions: tessalidas_id='.(int)$item);
				foreach($detalle as $det)
				{
					$new_detalles=new Tesdetallesalidas();
					
					$new_detalles->tesproductos_id=$det->tesproductos_id;
					$new_detalles->tessalidas_id=$new_salida->id;
					$new_detalles->tesunidadesmedidas_id=$det->tesunidadesmedidas_id;
					$new_detalles->cantidad=$det->cantidad;
					$new_detalles->preciounitario=$det->preciounitario;
					$descuento=(($det->cantidad*$det->preciounitario)*($det->descuento)/100);
					$new_detalles->descuento=$det->descuento;
					$new_detalles->importe=($det->cantidad*$det->preciounitario)-$descuento;
					$new_detalles->lote=$det->lote;
					$new_detalles->empaque=$det->empaque;
					$new_detalles->tescajas_id=$det->tescajas_id;
					$new_detalles->bobinas=$det->bobinas;
					$new_detalles->pesobruto=$det->pesobruto;
					$new_detalles->pesoneto=$det->pesoneto;
					$new_detalles->estado=$det->estado;
					$new_detalles->userid=$det->userid;
					$new_detalles->aclempresas_id=$det->aclempresas_id;
					$new_detalles->tescolores_id=$det->tescolores_id;
					$new_detalles->prorollos_id=$det->prorollos_id;
					$new_detalles->save();
					
				}
			}
		}
		return Redirect::toAction('venta/');
		
	}


	/*Notas de debito y credito*/
	
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
		$SALD=new Tessalidas();
		if($id==0){
		$this->salida['serie']='001';
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'001');
		$this->salida['npedido']=$SALD->getNumeropedido($npedido,'001');
		}else
		{
		$this->salida=$SALD->find_first($id);
		$DETALLES=new Tesdetallesalidas();
		$this->detalle=$DETALLES->find_first('conditions: tessalidas_id='.(int)$id);
		}
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
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
				Session::set("SALIDA_ID",$salidas->id);
            	
				if (Input::hasPost('detalle')) 
				{
					$detalles= new Tesdetallesalidas(Input::post('detalle'));
					$detalles->tessalidas_id=Session::get('SALIDA_ID');
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
	$salidas=new Tessalidas();
		$this->salida=$salidas->find_first(Session::get('SALIDA_ID'));
		$DETALLES=new Tesdetallesalidas();
		$this->detalles=$DETALLES->find('conditions: tessalidas_id='.(int)Session::get('SALIDA_ID'));
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
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
public function venta()
	{
		Flash::valid('Esta es la vista previa de la factura !!!');
		$salidas=new Tessalidas();
		$this->salida=$salidas->find_first(Session::get('SALIDA_ID'));
		$DETALLES=new Tesdetallesalidas();
		$this->detalles=$DETALLES->find('conditions: tessalidas_id='.(int)Session::get('SALIDA_ID'));
		/* mostrar las facturas por adelanto y si monto final */
		$adelantos= new Tesfacturasadelantos();
		$this->adelantos=FALSE;
		if($adelantos->exists('tesdatos_id='.$this->salida->tesdatos_id.' AND estado=1'))
		{
			$this->adelantos=$adelantos->getFacturasAdelantos($this->salida->tesdatos_id);
		}
		/* Busa las aplicaciones a esta factura */
		$aplicaciones= new Tesaplicacionfacturasadelantos();
		$this->aplicaciones=FALSE ;
		if($aplicaciones->exists('tessalidas_id='.$this->salida->id)){
			
		$this->aplicaciones =$aplicaciones->find('conditions: tessalidas_id='.$this->salida->id);
		}
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
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


public function limpiar_factura($id)
{
	View::template(null);
	$aplicaciones= new Tesaplicacionfacturasadelantos();
	if($aplicaciones->delete('tesfacturasadelantos_id='.$id.' AND estado=0'))
	{
	Flash::valid('Se recalcuraron los totales');
	}else
	{
	Flash::warning('Los totales esta bien calculados!!!');
	}
	return Redirect::toAction('venta/');
}

public function nota_creditos()
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	$tessalidas= new Tessalidas();
	$this->salidas=$tessalidas->find('conditions: aclempresas_id='.Session::get("EMPRESAS_ID").' AND tesdocumentos_id='.Session::get("DOC_ID"));
}

public function nota_debitos(){
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	$tessalidas= new Tessalidas();
	$this->salidas=$tessalidas->find('conditions: aclempresas_id='.Session::get("EMPRESAS_ID").' AND tesdocumentos_id='.Session::get("DOC_ID"));
	}

public function notacredito()
{
	$salidas=new Tessalidas();
		$this->salida=$salidas->find_first(Session::get('SALIDA_ID'));
		$DETALLES=new Tesdetallesalidas();
		$this->detalles=$DETALLES->find('conditions: tessalidas_id='.(int)Session::get('SALIDA_ID'));
}

public function notadebito()
{
	$salidas=new Tessalidas();
		$this->salida=$salidas->find_first(Session::get('SALIDA_ID'));
		$DETALLES=new Tesdetallesalidas();
		$this->detalles=$DETALLES->find('conditions: tessalidas_id='.(int)Session::get('SALIDA_ID'));
}
public function listado_servicio()
{	
	$this->titulo='Facturas del mes '.date("M");
	$this->url='crearsalidas';
	/*
	serie 001
	salida de de rollos para venta
	*/
	$salidas= new Tessalidas();
	$this->salidas= $salidas->find('conditions: (serie="F001" OR serie="001" OR serie = "F002") AND estadosalida!="TERMINADO" AND npedido like "SR%" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at ASC');
	Session::delete("SALIDA_ID");
	$this->guias= $salidas->find('conditions: (serie="F001" OR serie="001" OR serie = "F002") AND estadosalida="Pendiente" AND npedido like "SR%" AND estado=1 AND tesdocumentos_id=15','order: fecha_at ASC');
}
public function guia_servicio($id=0)
{
		$SALD=new Tessalidas();
		$this->salida['serie']='001';
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'001');
		$this->salida['npedido']=$SALD->getNumeropedido('SR','001');
		$this->salida['numerofactura']=$SALD->generarNumeroFactura('F002');
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
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
				
                return Redirect::toAction('factura_servicio_detalle/servicio');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('crearsalidas/');
             }
		}
	}
public function factura_servicio($id=0)
	{
		$SALD=new Tessalidas();
		if($id==0){
		$this->salida['serie']='001';
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'001');
		$this->salida['npedido']=$SALD->getNumeropedido('SR','001');
		}else
		{
		$this->salida=$SALD->find_first($id);
		$DETALLES=new Tesdetallesalidas();
		$this->detalle=$DETALLES->find_first('conditions: tessalidas_id='.(int)$id);
		}
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
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
				Session::set("SALIDA_ID",$salidas->id);
            	$detalles= new Tesdetallesalidas(Input::post('detalle'));
					$detalles->tessalidas_id=Session::get('SALIDA_ID');
					$detalles->cantidad=1;
					$detalles->tescolores_id='773';
					$detalles->tesunidadesmedidas_id='14';
					$detalles->userid=Auth::get('id');
					$detalles->estado='1';
					$detalles->tescolores_id='1';
					$detalles->aclempresas_id=Session::get("EMPRESAS_ID");
					$detalles->save();
				
				
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('factura_servicio_detalle/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 $this->detalle=Input::post('detalle');
				 return Redirect::toAction('factura_servicio/');
             }
		}
	}
public function factura_servicio_detalle($s='servicio')
{
	$this->s=$s;
	$salidas=new Tessalidas();
		$this->salida=$salidas->find_first(Session::get('SALIDA_ID'));
		$DETALLES=new Tesdetallesalidas();
		$this->detalles=$DETALLES->find('conditions: tessalidas_id='.(int)Session::get('SALIDA_ID'));
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
            if($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida_servicio/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				return Redirect::toAction('factura_servicio_detalle/');
			}
		}
		if (Input::hasPost('detalle')) 
			{
					$detalles= new Tesdetallesalidas(Input::post('detalle'));
					$detalles->tessalidas_id=Session::get('SALIDA_ID');
					$detalles->tescolores_id='773';
					$detalles->tesunidadesmedidas_id='14';
					$detalles->cantidad=Input::post('cantidad');
					$detalles->preciounitario=Input::post('preciounitario');
					$detalles->importe=Input::post('preciounitario')*Input::post('cantidad');
					$detalles->userid=Auth::get('id');
					$detalles->estado='1';
					$detalles->tescolores_id='1';
					$detalles->aclempresas_id=Session::get("EMPRESAS_ID");
					$detalles->save();
					return Redirect::toAction('factura_servicio_detalle/');
			}
}
public function versalida_servicio()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
            if ($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida_servicio/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$salidas= new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$detalles->find('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		$letras = new Tesletrassalidas();
		$this->letras=$letras->count('estadoletras="Editable" AND factura_id='.(int)Session::get("SALIDA_ID"));
		$this->l_f=$letras->count('estadoletras="Sin Numero" AND factura_id='.(int)Session::get("SALIDA_ID"));
		$letrassalida=new Tesletrassalidas();
		$this->lets=$letrassalida->find('conditions: factura_id='.Session::get('SALIDA_ID'));
	}
	
public function ordencompra()
{
	$this->data='';
		$q=$_GET['q'];
		$obj = new Tesordendecompras();
		$results = $obj->find('conditions: estado=1 AND origenorden="externo" and codigo like "%'.$q.'%" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
		foreach ($results as $value)
		{
			$id=(string)$value->codigo;
			$name=$value->getTesdatos()->razonsocial.' Nº'.$value->codigo;
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
		}
 
}


/*Anular factura*/
public function anularfacturas($id)
{
  $salidas= new Tessalidas();	
  $salidas->anularfactura($id);
  
  return Redirect::toAction('facturas/7');

}
/*Anular Guia*/
public function anularguias($id)
{
  $salidas= new Tessalidas();	
  $salidas->anularguia($id);
  
  return Redirect::toAction('facturas/7');
}
public function editar_detalle($id=0,$url='')
{
	if (Input::hasPost('detalle')) 
	{
		$detalles= new Tesdetallesalidas(Input::post('detalle'));
		$detalles->save();
		return Redirect::toAction('venta/');
	}
	$detalles= new Tesdetallesalidas();
	$this->detalle=$detalles->find_first($id);
}
/*
#######################################################################################################################
venta INTERNA 
*/
	
public function salidas_internas($id=15)
{
		$this->titulo='Guias sin factura';
		$this->url='crearsalidas_internas';
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)$id);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		/*
		serie 002
		salida de Hilos
		*/
		$salidas= new Tessalidasinternas();
		$this->salidas= $salidas->find('conditions: serie="999" AND estadosalida!="Con factura" AND (npedido like "PT%" OR npedido like "MT%") AND estado=1 AND aclempresas_id='.Session::get("EMPRESAS_ID").' AND tesdocumentos_id='.Session::get('DOC_ID'),'order: numero DESC limit 10');
		Session::delete("SALIDA_ID_I");		
}

public function crearsalidas_internas()
{
		$SALD=new Tessalidasinternas();
		if(Session::has('SALIDA_ID_I'))
		{
			$this->salida=$SALD->find_first((int)Session::get('SALIDA_ID_I'));
		}else
		{
		$se='999';
		$this->salida['serie']=$se;
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'999');
		$this->salida['npedido']=$SALD->getNumeropedido('PT',$se);
		}
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
				if(Session::get('DOC_ID')==15){
				$detalleT= new Prodetalletransportes(Input::post('prodetalletransportes'));
				$detalleT->tessalidasinternas_id=$salidas->id;
				$detalleT->numero=$salidas->numero;
				$detalleT->serie=$salidas->serie;
				$detalleT->fechatraslado=$salidas->finiciotraslado;
				$detalleT->estado='1';
				$detalleT->userid=Auth::get('id');
				$detalleT->save();
				}
				Session::set("SALIDA_ID_I",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('agregardetalles_internas/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('crearsalidas_internas/');
             }
		}
		
	}
public function crearsalidas_muestra()
	{
		View::select('crearsalidas_internas');		
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
				
                return Redirect::toAction('agregardetalles_internas/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('crearsalidas/');
             }
		}
	}
/*INGRESA CON SESSION SALIDA_ID*/
public function agregardetalles_internas()
	{
		$salidas= new Tessalidasinternas();
		$detalles = new Tesdetallesalidasinternas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID_I'));
		$this->detalles=$detalles->find('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
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
				if(Session::get('DOC_ID')==15){
				$detalleT= new Prodetalletransportes(Input::post('prodetalletransportes'));
				$detalleT->tessalidasinternas_id=$salidas->id;
				$detalleT->numero=$salidas->numero;
				$detalleT->serie=$salidas->serie;
				$detalleT->fechatraslado=$salidas->finiciotraslado;
				$detalleT->save();
				}
				Session::set("SALIDA_ID_I",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('agregardetalles_internas/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('agregardetalles_internas/');
             }
		}
	}
	
public function grabardetalle_internas()
	{
		$val=Input::post('detalles');
		if (Input::hasPost('detalles')) 
		{
			$productos=new Tesproductos();
			$producto= $productos->find_first((int)$val['tesproductos_id']);
			$detalles= new Tesdetallesalidasinternas(Input::post('detalles'));
			$detalles->tessalidasinternas_id=Session::get('SALIDA_ID_I');
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
			$this->detalles=$detalles->find('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
			}
		}
		
	}
	
	/*Finalizar y destruir la session creadas para la generacion de la salida de hilo*/
public function versalida_internas()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
            if ($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida_internas/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$salidas= new Tessalidasinternas();
		//$this->N=new numeroLetras();
		$detalles = new Tesdetallesalidasinternas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID_I'));
		$this->detalles=$detalles->find('conditions: tessalidasinternas_id='.(int)Session::get("SALIDA_ID_I"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
	}
	
public function versalida_i_internas()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		
		$salidas= new Tessalidasinternas();
		$detalles = new Tesdetallesalidasinternas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID_I'));
		$this->detalles=$detalles->find('conditions: tessalidasinternas_id='.(int)Session::get("SALIDA_ID_I"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
}

/*recibe el id del detalle de la salida_interna*/
public function eliminar_internas($id=0)
{
	$detalles= new Tesdetallesalidasinternas();
	$detalles->delete((int)$id);
	return Redirect::toAction('agregardetalles_internas/');
}

public function cargarsalida_internas($id=0,$url='agregardetalles_internas')
	{
		if($id!=0){
		Session::set("SALIDA_ID_I",$id);
		}else
		{
			Session::delete("SALIDA_ID_I");
		}
		return Redirect::toAction($url);
	}
/*
APPLICACION DE LETRAS INTERNAS A VENTA PRODUCTOS INTERNAS
*/
public function venta_interna()
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
				return Redirect::toAction('versalida_internas/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				return Redirect::toAction('venta_interna/');
			}
		}
	}


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
  	return Redirect::toAction('venta_interna/');
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
	return Redirect::toAction('venta_interna/');
}

}
?>