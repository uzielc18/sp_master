<?php 
View::template('spatricia/default');
Load::models('tesproductos','testipoproductos','teslineaproductos','tesdocumentos','tessalidas','prodetalletransportes','tesdetallesalidas','prorollos','aclempresas','tesingresos','tesdetalleingresos','proprocesos','proacabados','prodetalleprocesos','prodetalleacabados','tescolores');
class TintoreriaController extends AppController
{
protected function before_filter() 
{
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
}	
public function salidas($Y='',$m='')
	{
		$this->titulo='Guias para salida a tintoreria con la serie 002';
		$this->url='crearsalidas';
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)15);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		/*
		serie 002
		salida de Hilos
		*/
		$salidas= new Tessalidas();
		//$this->salidas= $salidas->find('conditions: serie="002" AND npedido like "TI%" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at DESC');
		$this->salidas= $salidas->find('conditions: DATE_FORMAT(femision,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND aclempresas_id='.Session::get("EMPRESAS_ID").' AND serie="002" AND estado=1 AND (npedido like "TI%") AND tesdocumentos_id='.Session::get('DOC_ID'),'order: numero DESC');
		Session::delete("SALIDA_ID");
	}

	public function salidas_i($Y='',$m='')
	{
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		$this->titulo='Guias con la serie 999';
		$this->url='crearsalida_interna';
		View::select('salidas');
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
		$salidas= new Tessalidas();
		$this->salidas= $salidas->find('conditions: DATE_FORMAT(femision,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND estadosalida!="TERMINADO" AND npedido like "TI%" AND serie="999" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at DESC');
		Session::delete("SALIDA_ID");
	}
	public function crearsalidas()
	{
		$this->ver=Input::post('salida');
		$this->salida=Input::post('salida');
		if(Input::hasPost('salida')) 
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
			//$salidas->save();
            if($salidas->save())
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
            	
				Flash::valid('fue agregada Exitosamente...!!!');
                //Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('agregardetalles/');
				//unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('crearsalidas/');
             }
		}
		$this->val=002;
		$SALD=new Tessalidas();
		$this->salida['serie']='002';
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'002');
		$this->salida['npedido']=$SALD->getNumeropedido('TI','002');
	}
	
	public function crearsalida_interna()
	{
		$this->val=999;
		View::select('crearsalidas');
		$SALD=new Tessalidas();
		$this->salida['serie']='999';
		$this->salida['numero']=$SALD->generarNumero_interno(Session::get('DOC_ID'),'999','TI');
		$this->salida['npedido']=$SALD->getNumeropedido('TI','999');
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id=Session::get('DOC_ID');
			/*$salidas->femision=date('Y-m-d');*/
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
            	
				Flash::valid('fue agregada Exitosamente...!!!');
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
	public function agregardetalles($ver='')
	{
		$salidas= new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$rollos=new Prorollos();
		$this->rollos=$rollos->find('conditions: (estadorollo="Sin procesos" OR estadorollo="Re-Proceso") AND enalmacen="1"');
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$detalles->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id=Session::get('DOC_ID');
			/*$salidas->femision=date('Y-m-d');*/
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
				$detalleT->save();
				
				Session::set("SALIDA_ID",$salidas->id);
            	
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
		$PR= new Proprocesos();
		$detalle_A=new Prodetalleacabados();
		$detalle_p= new Prodetalleprocesos();
		$detalles = new Tesdetallesalidas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$detalles->find('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		
		$this->proceso=$PR->find_first('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
		$this->detalle_A=$detalle_A->find('conditions: proprocesos_id='.$this->proceso->id);
		
	}
	
	public function versalida_i()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas= new Tessalidas();
		$PR= new Proprocesos();
		$detalle_A=new Prodetalleacabados();
		$detalle_p= new Prodetalleprocesos();
		$detalles = new Tesdetallesalidas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$detalles->find('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		
		$this->proceso=$PR->find_first('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
		$this->detalle_A=$detalle_A->find('conditions: proprocesos_id='.$this->proceso->id);
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
	/*,'proprocesos','prodetalleacabados','prodetalleprocesos'*/
	public function crearprocesos()
	{
		$PR= new Proprocesos();
		$ACAB=new Proacabados();
		$DETPRO=new Prodetalleprocesos();
		$this->proceso=FALSE;
		if (Input::hasPost('proprocesos')) 
		{
			$procesos= new Proprocesos(Input::post('proprocesos'));
			$procesos->tessalidas_id=Session::get('SALIDA_ID');
			$procesos->aclusuarios_id=Auth::get('id');
			$procesos->userid=Auth::get('id');
            if ($procesos->save())
			{
				Flash::valid('Se realizo con exito!!!');
				return Redirect::toAction('crearprocesos/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		if($PR->exists('tessalidas_id='.Session::get('SALIDA_ID')))
		{
			$this->proceso=TRUE;
			/*$procs = el procesos*/
			$procs=$this->proprocesos=$PR->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
			if (Input::hasPost('prodetalleacabados')) 
			{
				$values=Input::post('prodetalleacabados');
				$VAL=explode(',',$values['ids']);
				$id_procesos=$values['proprocesos_id'];
				foreach($VAL as $value=>$item){
				$detalle_A=new Prodetalleacabados();
				$detalle_A->proprocesos_id=$id_procesos;
				$detalle_A->proacabados_id=$item;
				$detalle_A->estado='1';
				$detalle_A->descripcion='';
	 			$detalle_A->save();
				}
				
				/*AGregando los rollos al detalle proceso*/
				$detalles = new Tesdetallesalidas();
				$dets=$detalles->find('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
				foreach($dets as $det){
				$detalle_p= new Prodetalleprocesos();
				$detalle_p->proprocesos_id=$id_procesos;
				$detalle_p->prorollos_id=$det->prorollos_id;
				$detalle_p->estado='1';
				$detalle_p->userid=Auth::get('id');
	 			$detalle_p->save();
				/*Cambiar el color de rollo el campo de tescolores_id=*/
				$rollos= new Prorollos();
				$rollos->find_first($det->prorollos_id);
				$rollos->tescolores_id=$procs->tescolores_id;
				$rollos->color='';
				if(!empty($procs->tescolores_id))$rollos->color=$procs->getTescolores()->nombre;
				$rollos->save();
				}
								
				Flash::valid('Acabdos agregados con exito!!!');
				return Redirect::toAction('crearprocesos/');
			}
			$this->acabados=$ACAB->find('order: tipo_acabado ASC');
			$detalle_p=new Prodetalleacabados();
			$detalle_A=$this->detalle_acabados=$detalle_p->find('conditions: proprocesos_id='.$this->proprocesos->id);
			$ACABADOS=array();
			foreach($detalle_A as $a)
			{
				$ACABADOS[]=$a->proacabados_id;
			}
			$this->ACABADOS=$ACABADOS;
		}
		/*muetsra la salida*/
		$salidas= new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$detalles->find('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
	}

/* Borrar toda la seleccion*/
public function borrar_seleccion($id=0)
{
	$detalle_A=new Prodetalleacabados();
	$detalle_A->delete('proprocesos_id='.$id);
	$detalle_P= new Prodetalleprocesos();
	$detalle_P->delete('proprocesos_id='.$id);
	Flash::valid('Los acabados Fueron Eliminados con exito!!!');
	return Redirect::toAction('crearprocesos/');
}

/*INGRESOS*/

public function ingresos($Y='',$m='')
	{
		
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		
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
		$this->ingresos= $ingresos->find('conditions: DATE_FORMAT(femision,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND (ISNULL(movimiento) OR movimiento!="INTE") AND serie!="999" AND npedido like "TI%" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at DESC limit 0,10');
		Session::delete("INGRESO_ID");
		$salidas= new Tessalidas();
		$this->rollos_tintoreria=$salidas->getGuiasT(0,'002');
		$this->rollos_reprocesos=$salidas->getGuiasR(0,'002');
		/*$this->salidas= $salidas->find('conditions: serie!="999" AND (estadosalida="Enviado" OR estadosalida="Proceso") AND npedido like "TI%" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at DESC limit 0,100');*/
		$this->COLOR= new Tescolores();
	}
	
	public function ingresos_i($Y='',$m='')
	{
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		View::select('ingresos');
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
		$this->ingresos= $ingresos->find('conditions: DATE_FORMAT(femision,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND movimiento="INTE" AND npedido like "TI%" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at DESC');
		Session::delete("INGRESO_ID");
		$salidas= new Tessalidas();
		$this->rollos_tintoreria=$salidas->getGuiasT(0,'999');
		$this->rollos_reprocesos=$salidas->getGuiasR(0,'999');
		/*$this->salidas= $salidas->find('conditions: serie!="999" AND (estadosalida="Enviado" OR estadosalida="Proceso") AND npedido like "TI%" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at DESC limit 0,100');*/
		$this->COLOR= new Tescolores();
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
/* recibe el id de la salida*/
public function generaringreso_tintoreria_mas()
{
	if (Input::hasPost('guias')) 
	{
			$values=Input::post('guias');
			$VAL=explode(',',$values['ids']);
		$g=0;
		$guias='';
		foreach($VAL as $value=>$item)
		{
		  $id=$item;
		  $salidas=new Tessalidas();
		  $salida=$salidas->find_first((int)$item);
		  $salida->estadosalida="Proceso";
		  $salida->save();
		  if($g==0)$coma='';else $coma=',';
		  $guias.=$coma.$salida->serie.'-'.$salida->numero;
		  $g++;
		}
		
		$salidas= new Tessalidas();
		$salida=$salidas->find_first((int)$id);
		$new_ingreso= new Tesingresos();
		$new_ingreso->serie=$salida->serie;
		$new_ingreso->tesdocumentos_id=15;
		$new_ingreso->tesdatos_id=$salida->tesdatos_id;
		$new_ingreso->tesmonedas_id=$salida->tesmonedas_id;
		$new_ingreso->femision=$salida->femision;
		$new_ingreso->fvencimiento=$salida->fvencimiento;
		$new_ingreso->fregistro=date('Y-m-d');
		$new_ingreso->npedido=$salida->npedido;
		$new_ingreso->numeroguia=$guias;
		$new_ingreso->ordendecompra=$salida->ordendecompra;
		$new_ingreso->finiciotraslado=$salida->finiciotraslado;
		if($salida->serie=='999')$new_ingreso->movimiento='INTE';else $new_ingreso->movimiento=$salida->movimiento;
		$new_ingreso->glosa=$salida->glosa;
		$new_ingreso->totalconigv=0;
		$new_ingreso->cuantagastos=$salida->cuantagastos;
		$new_ingreso->cuentaporpagar=$salida->cuentaporpagar;
		$new_ingreso->igv=0;
		$new_ingreso->totalenletras=$salida->totalenletras;
		$new_ingreso->pr='TI';
		$new_ingreso->estado=1;
		$new_ingreso->userid=Auth::get('id');
		$new_ingreso->activo='1';
		$new_ingreso->testipocambios_id=Session::get('CAMBIO_ID');
		$new_ingreso->userid=Auth::get('id');
		$new_ingreso->aclusuarios_id=Auth::get('id');
		$new_ingreso->estadoingreso='Editable';
		$new_ingreso->aclempresas_id=Session::get("EMPRESAS_ID");
		$new_ingreso->aclusuarios_id=Auth::get("id");
		$new_ingreso->save();
		$salida->numeroguia=$new_ingreso->serie.'-'.$new_ingreso->numero;
		$salida->estadosalida="Proceso";
		if($salida->save()){
		return Redirect::toAction('cargaringreso/'.$new_ingreso->id);
		}
	}else
	{
	}
}


public function generaringreso_tintoreria($id)
{
		$salidas= new Tessalidas();
		$salida=$salidas->find_first((int)$id);
		$new_ingreso= new Tesingresos();
		$new_ingreso->serie=$salida->serie;
		$new_ingreso->tesdocumentos_id=15;
		$new_ingreso->tesdatos_id=$salida->tesdatos_id;
		$new_ingreso->tesmonedas_id=$salida->tesmonedas_id;
		$new_ingreso->femision=$salida->femision;
		$new_ingreso->fvencimiento=$salida->fvencimiento;
		$new_ingreso->fregistro=date('Y-m-d');
		$new_ingreso->npedido=$salida->npedido;
		$new_ingreso->numeroguia=$salida->serie.'-'.$salida->numero;
		$new_ingreso->ordendecompra=$salida->ordendecompra;
		$new_ingreso->finiciotraslado=$salida->finiciotraslado;
		if($salida->serie=='999')$new_ingreso->movimiento='INTE';else $new_ingreso->movimiento=$salida->movimiento;
		$new_ingreso->glosa=$salida->glosa;
		$new_ingreso->totalconigv=0;
		$new_ingreso->cuantagastos=$salida->cuantagastos;
		$new_ingreso->cuentaporpagar=$salida->cuentaporpagar;
		$new_ingreso->igv=0;
		$new_ingreso->totalenletras=$salida->totalenletras;
		$new_ingreso->pr='TI';
		$new_ingreso->estado=1;
		$new_ingreso->userid=Auth::get('id');
		$new_ingreso->activo='1';
		$new_ingreso->testipocambios_id=Session::get('CAMBIO_ID');
		$new_ingreso->userid=Auth::get('id');
		$new_ingreso->aclusuarios_id=Auth::get('id');
		$new_ingreso->estadoingreso='Editable';
		$new_ingreso->aclempresas_id=Session::get("EMPRESAS_ID");
		$new_ingreso->aclusuarios_id=Auth::get("id");
		$new_ingreso->save();
		$salida->numeroguia=$new_ingreso->serie.'-'.$new_ingreso->numero;
		$salida->estadosalida="Proceso";
		if($salida->save()){
		return Redirect::toAction('cargaringreso/'.$new_ingreso->id);
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
				Flash::valid('EL ingreso se Modifico con exito!!!');
				return Redirect::toAction('agregardetalles_ingreso/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$ingresos= new Tesingresos();
		$detalles= new tesdetalleingresos();
		$rollos=new Prorollos();
		$this->ingreso=$ingresos->find_first((int)Session::get('INGRESO_ID'));
		$this->detalles=$detalles->find('conditions: tesingresos_id='.Session::get('INGRESO_ID'));
		/*$SN=explode('-',$this->ingreso->numeroguia);*/
		$this->rollos=$rollos->getRollosSalidas($this->ingreso->numeroguia);
		
	}
/* 
Muestra los rollos ingresados a una guia
*/
public function ingresorollos()
{
		$detalles= new tesdetalleingresos();
		$this->detalles=$detalles->find('conditions: tesingresos_id='.Session::get('INGRESO_ID'));
}

public function veringreso($id=0)
{
		$ingresos= new Tesingresos();
		$detalles= new tesdetalleingresos();
		$this->ingreso=$ingresos->find_first((int)Session::get('INGRESO_ID'));
		$this->detalles=$detalles->find('conditions: tesingresos_id='.Session::get('INGRESO_ID'));
		$SN=explode('-',$this->ingreso->numeroguia);
		if($id!=0)
		{
			$rollos=new Prorollos();
			$r=$rollos->getRollosSalidas_count($this->ingreso->numeroguia);
			$ingresos= new Tesingresos();
			$ingreso= $ingresos->find_first($id);
			$ingreso->estadoingreso='Pendiente';
			$ingreso->save();
			Flash::valid('La guia '.$SN[0].'-'.$SN[1].' Finalizao su ingreso!!!');
			
			
		}
}
/*
recibe le id del ingreso para borrar y elimanr todos los procesos 
*/
public function borraringreso($id=0)
{
	$detalles= new tesdetalleingresos();
	if(!$detalles->exists('tesingresos_id='.$id)){
	$ingresos= new Tesingresos();
	$ingreso=$ingresos->find_first((int)$id);
	$SG=explode(',',$ingreso->numeroguia);
		foreach($SG as $value=>$item)
		{
			$SN=explode('-',$item);
			$salidas= new Tessalidas();
			$salida=$salidas->find_first('conditions: serie='.$SN[0].' AND numero='.$SN[1]);
			$salida->estadosalida="Enviado";
			$salida->save();
		}
	$ingresos->delete((int)$id);
		Flash::valid('EL ingreso se Elimino de la base de dato!!!');
	}else
	{
		Flash::warning('El ingreso no puede ser Borrado Comuniquese con Sistemas!!!');
	}
		
		return Redirect::toAction('ingresos/');
}

public function control()
{
	/*CONTROL DE CALIDAD VENTA*/
	Session::delete('PRODUCCION_ID');
	$rollos= new Prorollos();
	$this->rollos=$rollos->find('conditions: estadorollo="Control de Calidad Venta"');
	
}
/*
recibe el id del rollo principal;
$idsecundario => recibe el id del rollo que no es principal y que se esta cortando
*/
public function generarollos($id,$numero,$idsecundario=0)
{
	$prorollos=new Prorollos();
	$rollo=$prorollos->find_first((int)$id);
	if($idsecundario!=0){$prorollos=new Prorollos();
	$rollo_secundario=$prorollos->find_first((int)$idsecundario);
	$rollo_secundario->estadorollo='TERMINADO';
	$rollo_secundario->save();
	}
	if($numero!=0)
	{
		for($i=1;$i<=$numero;$i++)
		 {
		 $prorollos=new Prorollos();
		 $prorollos->prorollos_id=$rollo->id;
		 $prorollos->prodetalleproduccion_id=$rollo->prodetalleproduccion_id;
		 $prorollos->tesproductos_id=$rollo->tesproductos_id;
		 if(empty($rollo->getTesproductos()->codigo_ant))$COD=$rollo->getTesproductos()->codigo; else $COD=$rollo->getTesproductos()->codigo_ant;
		 $prorollos->codigo=$COD;
		 $prorollos->numero=$rollo->numero;
		 $prorollos->rendimiento=$rollo->rendimiento;
		 $prorollos->numeroventa=$prorollos->numero_venta($rollo->tesproductos_id);
		 $prorollos->maquina_numero=$rollo->maquina_numero;
		 $prorollos->fechacorte=$rollo->fechacorte;
		 $prorollos->ancho=$rollo->ancho;
		 $prorollos->color=$rollo->color;
		 $prorollos->tescolores_id=$rollo->tescolores_id;
		 $prorollos->tesordendecompras_id=$rollo->tesordendecompras_id;
//		 tesordendecompras_id
		 $prorollos->estado=1;
		 $prorollos->userid=Auth::get('id');
		 $prorollos->lote=$rollo->lote;
		 $prorollos->enalmacen='0';
		 $prorollos->save();
		 }
		///Flash::info('Ingrese los datos de los rollos');
	 	///return Redirect::toAction('rollos_listados/'.$id.'');
	$rollo->estadorollo='TERMINADO';
	$rollo->save();
		$this->ok='Rollos generado ';
	}else{
		///Flash::error('No se puede crear los rollos!!!! '."<br /> Por favor intente nuevamente, verificando la informacion enviada");
		//return Redirect::toAction('corte');
		$this->ok='Rollo no generado';
	}
}
public function rollos_listados($id)
{
	Flash::info('Ingrese los datos de los rollos');
	$this->R=$prorollos=new Prorollos();
	$this->rollos=$prorollos->find_first($id);
	$this->t=$prorollos->count('conditions: ISNULL(estadorollo) AND prorollos_id='.$id);
	if($this->t!=0){
	$this->prorollos=$prorollos->find('conditions: ISNULL(estadorollo) AND prorollos_id='.$id);
	}else
	{
	$this->prorollos=$prorollos->find('conditions: id='.$id);
	}
	$this->id=$id;
}
/*
Ingresa el rollo sin generar ningun corte
*/
public function ingresarsincortar($n=0,$id)
{
	View::select('editar_rollo');
	$prorollos=new Prorollos();
	$this->prorollos=$rollo=$prorollos->find_first((int)$id);
	/*$rollo->estadorollo='VENTA';
	$rollo->save();*/
	$this->id=$id;
	$this->n=$rollo->numeroventa;
	/*return Redirect::toAction('rollos_venta/');*/
}

/*/
$n es el numero de rollo
$id es el id del rollo a editar
*/
public function editar_rollo($n,$id)
{
	$prorollos=new Prorollos();
	$this->prorollos=$prorollos->find_first($id);
	$this->id=$id;
	$this->n=$n;
}

public function grabarrollo($id=0)
{
	$this->ok='No grabo';
	$a=Input::post('a');
	if(Input::hasPost('prorollos'.$a))
	{
		$prorollos=new Prorollos();
		$this->ok='';
		$post=Input::post('prorollos'.$a);
		if($prorollos->save(Input::post('prorollos'.$a)))
		{
			$this->ok.='Desa editar la informacion enviada?';
			$rollo=$prorollos->find_first($prorollos->id);
			$this->id=$rollo->id;
			$this->n=$a;
		}
	}
	
	
	
}
public function rollos_tintoreria()
{
	$prorollos=new Prorollos();
	$this->rollos=$prorollos->getRollostintoreria();
	$this->rollos_r=$prorollos->getRollosreprocesos();
}

public function rollos_reprocesos()
{
	$this->titulo='Rollos para reprocesos';
	$prorollos=new Prorollos();
	$this->rollos=$prorollos->getRollosreprocesos();
	//$this->rollos=$prorollos->find('conditions: estadorollo="Re-Proceso"');
}
public function rollos_venta($ancho='')
{
	$this->titulo='Rollos para Venta';
	$this->ancho=$ancho;
	$prorollos=new Prorollos();
	//$this->rollos=$prorollos->getRollosporestado("VENTA");
	//$this->rollos=$rollos->getRollosporestado($e);
	//$this->rollos=$prorollos->getVentas();
	$this->rollos=$prorollos->ControlVenta($ancho);
}
public function rollos_sin_datos()
{
	$this->titulo='Rollos para Venta';
	$prorollos=new Prorollos();
	//$this->rollos=$prorollos->getRollosporestado("VENTA");
	//$this->rollos=$rollos->getRollosporestado($e);
	//$this->rollos=$prorollos->getVentas();
	$this->rollos=$prorollos->SinDatos();
}
public function rollos_venta_db()
{
	$this->titulo='Rollos para Venta';
	$prorollos=new Prorollos();
	//$this->rollos=$prorollos->getRollosporestado("VENTA");
	//$this->rollos=$prorollos->getVentas();
	$this->rollos=$prorollos->find('conditions: estadorollo="VENTA" limit 0,5');
}

/*
$id='';
se divie el rollo en el numero que se desa manteniendo el rollo original como tal es decir si un rollo lo divides en 3 crear do rollos y el rollo original lo mantienes pero con nuevos datos tales como (metro y peso)
*/

public function divir_venta($id,$numero=1)
{
	/*Obtener los datos del rollo*/
	if($numero>=1){
	$prorollos=new Prorollos();
	$p_rollo=$prorollos->find_first((int)$id);
	
	for($i=1;$i<$numero;$i++)
	{
		 $prorollos=new Prorollos();
		 $prorollos->prorollos_id=$p_rollo->prorollos_id;
		 $prorollos->prodetalleproduccion_id=$p_rollo->prodetalleproduccion_id;
		 $prorollos->tesproductos_id=$p_rollo->tesproductos_id;
		 if(empty($p_rollo->getTesproductos()->codigo_ant))$COD=$p_rollo->getTesproductos()->codigo; else $COD=$p_rollo->getTesproductos()->codigo_ant;
		 $prorollos->codigo=$COD;
		 $prorollos->numero=$p_rollo->numero;
		 $prorollos->rendimiento=$p_rollo->rendimiento;
		 $prorollos->numeroventa=$p_rollo->numero_venta($p_rollo->tesproductos_id);
		 $prorollos->maquina_numero=$p_rollo->maquina_numero;
		 $prorollos->fechacorte=$p_rollo->fechacorte;
		 $prorollos->metros=$p_rollo->metros;
		 $prorollos->ancho=$p_rollo->ancho;
		 $prorollos->color=$p_rollo->color;
		 $prorollos->tescolores_id=$p_rollo->tescolores_id;
		 $prorollos->estadorollo=$p_rollo->estadorollo;
		 $prorollos->tesordendecompras_id=$p_rollo->tesordendecompras_id;
		 $prorollos->estado=1;
		 $prorollos->userid=Auth::get('id');
		 $prorollos->lote=$p_rollo->lote;
		 $prorollos->enalmacen='1';
		 $prorollos->save();
		 }
		$this->M=$p_rollo->metros;
		//return Redirect::toAction('ver_divir_venta/'.$id.'/'.$p_rollo->tesproductos_id.'/'.$p_rollo->metros);
	}else
	{
		return Redirect::toAction('rollos_venta/');
	}
}

public function ver_divir_venta($id,$tesproductos_id,$metros)
{
	$prorollos=new Prorollos();
	$this->rollos=$prorollos->find_first((int)$id);
	$this->prorollos=$prorollos->find('conditions: tesproductos_id='.$tesproductos_id.' AND estadorollo="VENTA" AND metros="'.$metros.'"');
	$this->t=1;
}
/*Envia de venta a reprocesos*/
public function enviar_reprocesos($id)
{
	$rollo=new Prorollos();
	$rollo->find_first((int)$id);
	$rollo->estadorollo='Re-Proceso';
	$rollo->save();
	Flash::valid('El rollo fue enviado a reproceso!');
	return Redirect::toAction('rollos_venta/');
}

/*recibe el id del rollo*/
public function modificar_rollos($id)
{
	$rollo=new Prorollos();
	$this->rollo=$rollo->find_first((int)$id);
	if(Input::hasPost('rollo'))
	{
		$prorollos=new Prorollos();
		$post=Input::post('rollo');
		if($prorollos->save(Input::post('rollo')))
		{
			Flash::valid('EL rollo Fue modificado!');
			return Redirect::toAction('modificar_rollos/'.$id);	
		}else
		{
			Flash::valid('El Rollo no se puede modificar!');
		}
	}
}
public function anularguias($id)
{
  $salidas= new Tessalidas();	
  $salidas->anularguia($id);
  
  return Redirect::toAction('salidas/');
  
}

/*Los rollos que no tienen Procesos Verificar*/
 public function rollos_sinp($id=0)
 {
	 $n=0;
	 $this->titulo='Rollos para Venta';
	 $prorollos=new Prorollos();
	 $this->rollos=$roll=$prorollos->find('conditions: estadorollo="VENTA"');
	 if($id==43408841)
	 {
		 $n=0;
		 foreach($roll as $r)
		 {
		 	$detalle= new Prodetalleprocesos();
			if(!$detalle->exists("prorollos_id=".$r->id)){
			$detalle->prorollos_id=$r->id;
			$detalle->proprocesos_id=22;
			$detalle->estado=1;
			$detalle->observacion='inventario';
			$detalle->save();
			$n++;
			}
		 }
		 Flash::valid('Seactualizo la informacion ('.$n.')!');
	 return Redirect::toAction('rollos_venta/');
	 }
	
	 
 }

public function actualizar_procesos($id=0,$estado='ANULADO')
{
	$i=0;
	$h=0;
	if($id==43408841){
	$salidas= new Tessalidas();
	$detalleprocesos= new Prodetalleprocesos();
	$procesos=new Proprocesos();
	$detallesalidas=new Tesdetallesalidas();
	
	$sal=$salidas->find("conditions: npedido like 'TI-%' AND estadosalida='".$estado."'");
	
	
	foreach($sal as $s)
	{
		/*captura en un array los id de los rollos*/
		$detalles=$detallesalidas->find("conditions: tessalidas_id=".$s->id,'order: prorollos_id ASC');
		$n=0;
		$idrollos=array();
		foreach($detalles as $a)
		{
			$idrollos[$n]=$a->prorollos_id;
			$n++;
		}
		/*busca el processo*/
		if($procesos->exists("tessalidas_id=".$s->id)){
		 $pr=$procesos->find_first("conditions: tessalidas_id=".$s->id);
		// echo $pr->id.'--'.$s->id;
		// echo "<br />";
		 $a=0;
		 foreach($detalleprocesos->find_all_by_sql("SELECT d.* FROM prodetalleprocesos as d ,proprocesos as p WHERE p.id=d.proprocesos_id AND d.proprocesos_id=".$pr->id." AND p.tessalidas_id=".$s->id." ORDER BY prorollos_id ASC") as $det)
		 {
			if(array_key_exists($a, $idrollos))
			{
				$dep=$detalleprocesos->find_first($det->id);
				$dep->prorollos_id=$idrollos[$a];
				if($estado=="Enviado")$dep->estado=1;
				if($estado=="TERMINADO")$dep->estado=2;
				if($estado=="ANULADO")$dep->estado=1;
				if($dep->save())
				{
					$i++;	
				}
			}else
			{
				if($estado!='Proceso')
				{
				$detalleprocesos->delete($det->id);
				$h++;
				}
			}
			$a++;
		 }
		}
	}
	}
	 Flash::valid('Los rollos modificados fueron '.$i.' y los detalleprocesos eliminados fueron '.$h);
	 return Redirect::toAction('ingresos/');
} 

/*encontrar el color segun el nombre para colocarlo en tescolores_id*/

public function color_tintoreria()
{
	$colores= new Tescolores();
	$prorollos=new Prorollos();
	$rollos=$prorollos->find('conditions: tescolores_id=0 AND estadorollo="Tintoreria"');
	$a=0;
	$b=count($rollos);
	foreach($rollos as $r):
		$rollo=$prorollos->find_first($r->id);
		$c=$colores->find_first('conditions: aclempresas_id='.Session::get("EMPRESAS_ID").' AND nombre="'.$r->color.'"');
		$rollo->tescolores_id=$c->id;
		if($rollo->save())$a++;
		
	endforeach;
	Flash::valid('Los rollos encontrados fueron '.$b.' y los modificados fueron '.$a);
	return Redirect::toAction('rollos_tintoreria/');
}

}



?>