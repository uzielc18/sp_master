<?php 

class AbonosController extends AdminController
{
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	
public function index($id=1,$Y='',$m='')
{
	$cond='';
	if($id!=0)$cond=' AND tesformadepagosabonos_id='.$id;
	$this->id=$id;
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	Session::delete('A_ID');
	Session::delete('A_ID_O');
	Session::delete('cliente_id');
	$abonos=new Tesabonos();
	$this->vouchers=$abonos->find('conditions: DATE_FORMAT(fecha,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND aclempresas_id='.Session::get('EMPRESAS_ID').$cond,'order: numero DESC');
	$tesformadepagosabonos=new Tesformadepagosabonos();
	$this->pagos=$tesformadepagosabonos->find('conditions: activo=1');
}
public function cargar($id,$url='editar')
{
	$abs = new Tesabonos();
	$ab=$abs->find_first((int)$id);
	Session::set('A_ID',$ab->id);
	Session::set('cliente_id',$ab->tesdatos_id);
	return Redirect::toAction($url);
}
	
/*
genera el vaucher;
escoge la moneda, escoje el proceso a realizar si va a aplicar auna letra o factura 
*/
public function crear()
{
	$this->p='0';
	$this->titulo='Crear';
	if(Input::hasPost('abonos'))
	{
    	$ab = new Tesabonos(Input::post('abonos'));
		$ab->aclusuarios_id=Auth::get('id');
		$ab->testipocambios_id=Session::get('CAMBIO_ID');
		$ab->estado='0';
		$ab->userid=Auth::get('id');
		$ab->activo='0';
		$ab->estadov='Pendiente';
		$ab->aclempresas_id=Session::get('EMPRESAS_ID');
        if($ab->save())
		{
			Session::set('A_ID',$ab->id);
			Session::set('cliente_id',$ab->tesdatos_id);
            Flash::valid('Vaoucher fué agregado Exitosamente...!!!');
            Aclauditorias::add("Genero un Abono con numero {$ab->numero}",'tesabonos');
			if($ab->tesformadepagosabonos_id==11){
				if(Session::get('cliente_id')!=0)
				{
					return Redirect::toAction('creardetalle');
				}else
				{
					Flash::warning('Para poder Continuar deve seleccionar un Cliente!!');
					return Redirect::toAction('editar');
				}
			}else
			{
				return Redirect::toAction('creardetalle');
			}
         } else {
              Flash::warning('No se Pudieron Guardar los Datos...!!!');
         }
     }
}
	
public function editar()
{
	$this->p=Session::get('cliente_id');
	View::select('crear');
	$this->titulo='Editar';
   	$abs = new Tesabonos();
	$ab=$abs->find_first((int)Session::get('A_ID'));
	if(Input::hasPost('abonos'))
	{
		if($ab->update(Input::post('abonos')))
		{
			$ab=$abs->find_first((int)Session::get('A_ID'));
			Session::set('A_ID',$ab->id);
			Session::set('cliente_id',$ab->testados_id);
			/*Auditorias*/                    
            Aclauditorias::add("Edito un Abono con numero {$ab->numero}",'tesabonos');
			/*fin Aurditorias*/
            if($ab->tesformadepagosabonos_id==11)
			{
				if(Session::get('cliente_id')!=0)
				{
					Flash::valid('Vaoucher fué agregado Exitosamente...!!!');
					return Redirect::toAction('creardetalle');
				}else
				{
					Flash::warning('Para poder Continuar debera seleccionar un Cliente!!'.$ab->testados_id);
					return Redirect::toAction('editar');
				}
			}else
			{
				Flash::valid('Vaoucher fué agregado Exitosamente...!!!'.$ab->testados_id);
				return Redirect::toAction('creardetalle');
			}
          }else
		  {
			  Flash::warning('No se Pudieron Guardar los Datos...!!!');
           }
         }
	$this->abonos=$ab;
	}
	
	
	public function creardetalle()
	{
		if(Session::has('A_ID'))
		{
			$abs = new Tesabonos();
			$ab=$abs->find_first((int)Session::get('A_ID'));
			/*Solo facturas*/
			$this->ab=$ab;
			
		 	if(!empty($ab->tesdatos_id))
			{
			  Session::set('cliente_id',$ab->tesdatos_id);
			  $tesdatos= new Tesdatos();
			  $this->tesdatos=$tesdatos->find_first((int)Session::get('cliente_id'));
			  $conditions=' AND s.tesmonedas_id='.$ab->tesmonedas_id;
			  $tesformadepagosabonos_id=$ab->tesformadepagosabonos_id;
			  $salidas= new Tessalidas();
			
			if($tesformadepagosabonos_id==3)
			{
				//$conditions.=' AND !ISNULL()';
			$this->salidas=$salidas->getLetrassinabono($ab->tesdatos_id,$conditions,$ab->tesformadepagosabonos_id);
			}else{
			$this->salidas=$salidas->getPendientescobro($ab->tesdatos_id,$conditions,$ab->tesformadepagosabonos_id);
			}
			}else{
				Flash::warning('Para poder Continuar debera seleccionar un Cliente!!'.Session::get('cliente_id'));
				return Redirect::toAction('editar');
			}
			
		}
	}
	
public function grabardetalle()
	{
		
		$this->salida_id=0;
		
		if(Input::post('s')){
		
		$abs = new Tesabonos();
		$ab=$abs->find_first((int)Session::get('A_ID'));
		$code='Es una Ingreso Normal';
		$ids=$this->ids=Input::post('s');
		$salidas= new Tessalidas(); 
		$salidasinternas= new Tessalidasinternas();
		$t=0;
		foreach(explode(',',$ids) as $item=>$value):
		$monto_A=0;
		$VAL=explode('-',$value);
		if($VAL[1]=='RUC')$salida=$salidas->find_first((int)$VAL[0]);	
		if($VAL[1]=='INT')$salida=$salidasinternas->find_first((int)$VAL[0]);	
		 $detalle=new Tesabonosdetalles();
		 $detalle->tesabonos_id=Session::get('A_ID');
		 if($VAL[1]=='RUC')
		 {
			 $detalle->tessalidas_id=(int)$salida->id;
			 $monto_A=$salidas->getAbonos_factura($salida->id,'tessalidas_id');
		 }
		 if($VAL[1]=='INT')
		 {
			 $detalle->tessalidasinternas_id=$salida->id;
			 $monto_A=$salidas->getAbonos_factura($salida->id,'tessalidasinternas_id');
		 }
		 if($salida->tesdocumentos_id==13){
		    $detalle->abono='1';
		    $detalle->cargos='0';
		 }elseif($ab->tesformadepagosabonos_id==10 || $ab->tesformadepagosabonos_id==14)
		 {
			$detalle->abono='1';
		    $detalle->cargos='0';
		 }else
		 {
		    $detalle->abono='0';
		    $detalle->cargos='1';
		 }
		 $detalle->tesdatos_id=$salida->tesdatos_id;
		 if($VAL[1]=='RUC')
		 {
			 if($ab->tesformadepagosabonos_id==10)/*detraccion*/
			 {
			  $detalle->monto=($salida->totalconigv)*Session::get('DETRACCION');
			  
			  }elseif($ab->tesformadepagosabonos_id==14)/*retencion*/
			 {
			  $detalle->monto=$salida->totalconigv*Session::get('RETENCION');/**Session::get('RETENCION');*/
			 }else{
			  $detalle->monto=$salida->totalconigv-$monto_A;
			 }
		 }
		 if($VAL[1]=='INT') $detalle->monto=$salida->total-$monto_A;
		 $code.=' EL monto de la factura es '.$detalle->monto;
		 $detalle->tescuentascorrientes_id=0;
		 $detalle->estado=1;
		 $detalle->plancontable=0;
		 $detalle->save();
		 /*Auditorias*/
		 Aclauditorias::add("Agrego un detalle {$detalle->monto} $detalle->tessalidasinternas_id=>{$detalle->tessalidasinternas_id},$detalle->tessalidas_id=>{$detalle->tessalidas_id}",'tesabonos');
		 /*fin Aurditorias*/
		if($ab->tesformadepagosabonos_id==10 || $ab->tesformadepagosabonos_id==14)$salida->estadosalida='Pendiente'; else $salida->estadosalida='PAGADO';
		/*VAlIDAR SE ES UNA LETRA*/
		if($salida->tesdocumentos_id==34)
		{
			$LT= new Tesletrassalidas();
			$LTI= new Tesletrassalidasinternas();
			if($VAL[1]=='RUC')$letra=$LT->find_first('conditions: tessalidas_id='.(int)$VAL[0]);
			if($VAL[1]=='INT')$letra=$LTI->find_first('conditions: tessalidasinternas_id='.(int)$VAL[0]);
			$letra->estadoletras='ACEPTADO';
			$letra->save();
		}
		$salida->save();
		endforeach;
		$_POST[]=array();
		$abs = new Tesabonos();
		$ab=$abs->find_first((int)Session::get('A_ID'));
		$ab->importe=$t.'';
		$ab->estado='1';
		$ab->activo='1';
		$ab->save();
		Flash::valid($code);
		if($ab->tesformadepagosabonos_id==10 || $ab->tesformadepagosabonos_id==14)
		{Redirect::toAction('detalle_abono/');}else{Redirect::toAction('grabardetalle');}
		}
		/*Para Grabar el detalle de una tranferencia */
		$abs = new Tesabonos();
		$ab=$abs->find_first((int)Session::get('A_ID'));
		if($ab->tesformadepagosabonos_id==10 || $ab->tesformadepagosabonos_id==14)
		{Redirect::toAction('detalle_abono/');}
		$this->ab=$ab;
		if(empty($ab->numero))$this->numero=$abs->numero(); else $this->numero=$ab->numero;
		if(empty($ab->asiento))$this->asiento=$abs->asiento(); else $this->asiento=$ab->asiento;
		$detalle=new Tesabonosdetalles();
		$this->detalles=$detalle->find('conditions: tesabonos_id='.Session::get('A_ID'));
		$empresas = new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		
		/*Chueqes*/
		if($detalle->exists('cargos="0" AND abono="1" AND tessalidas_id!=0 AND tesabonos_id='.Session::get('A_ID'))){
		$s_d=$detalle->find_first('conditions: cargos="0" AND abono="1" AND tessalidas_id!=0 AND tesabonos_id='.Session::get('A_ID'));
		$this->salida_id=$s_d->tessalidas_id;
		}
		
		/*Letras*/
		if($detalle->exists('cargos="0" AND abono="1" AND tessalidas_id=0 AND tesingresos_id!=0 AND tesabonos_id='.Session::get('A_ID'))){
		 $this->salida_id=1;
		}
		
	}
/*Detraccion y retencion*/
public function detalle_abono($c=0)
{
	$empresas = new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$abs = new Tesabonos();
	$ab=$abs->find_first((int)Session::get('A_ID'));
	$this->ab=$ab;
		if(empty($ab->numero))$this->numero=$abs->numero(); else $this->numero=$ab->numero;
		if(empty($ab->asiento))$this->asiento=$abs->asiento(); else $this->asiento=$ab->asiento;
	$detalle=new Tesabonosdetalles();
	$this->tesdetalles=$detalle->find('conditions: tesabonos_id='.Session::get('A_ID'));
}


/*
EFECTIVO
@fpago = id de la forma de pago 
*/
public function efectivo($fpago=1)
{
	$this->total_abono=0;
	if (Input::hasPost('tesabonosdetalles'))
	{
		$detalle=new Tesabonosdetalles(Input::post('tesabonosdetalles'));
		if ($detalle->save()) 
		{
			/*validar si es el monto de abono es mayor o menor al monto de total de cargos*/
			$D=new Tesabonosdetalles();
			$D->getMenor_Mayor(Session::get('A_ID'),$detalle->monto);
						
		 /*Auditorias*/
		 Aclauditorias::add("Agrego un detalle en efectivo {$detalle->monto}, $detalle->tessalidasinternas_id=>{$detalle->tessalidasinternas_id},$detalle->tessalidas_id=>{$detalle->tessalidas_id}",'tesabonos');
		 /*fin Aurditorias*/ 
			Flash::valid('Datos Guardados Correctamente');
			return Redirect::toAction('terminar');
        } else {
            Flash::warning('No se Pudieron Guardar los Datos...!!!');
        }
    }
	
	$detalle=new Tesabonosdetalles();
	$this->detalles=$detalle->find('conditions: tesabonos_id='.Session::get('A_ID'));
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesabonos_id='.Session::get('A_ID'));
	/*Sumar los abonos por documento tipo Nota de Credito*/
	$this->total_abono=$detalle->sum('monto','conditions: (tessalidas_id!=0 or tessalidasinternas_id!=0 or tesingresos_id!=0) AND abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID'));
	$this->a='';
	if($detalle->exists('tesingresos_id=0 AND abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID')))
	{
		$this->tesabonosdetalles=$detalle->find_first('conditions: tesingresos_id=0 AND abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID'));
		$this->total=$this->sumar_abonos($detalle->find('conditions: abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID')));
		$this->a='';
	}
	$formadepagos= new Tesformadepagosabonos();
	$this->pago=$formadepagos->find_first((int)$fpago);
	$this->numero=$detalle->ndocumento();
	

}
/*
NOTA DE ABONO
@fpago = id de la forma de pago 
*/
public function notaabono($fpago=1)
{
	if (Input::hasPost('tesabonosdetalles'))
	{
		$detalle=new Tesabonosdetalles(Input::post('tesabonosdetalles'));
		if ($detalle->save()) 
		{
			/*validar si es el monto de abono es mayor o menor al monto de total de cargos*/
			$D=new Tesabonosdetalles();
			$D->getMenor_Mayor(Session::get('A_ID'),$detalle->monto);
			
		 /*Auditorias*/
		 Aclauditorias::add("Agrego un detalle en efectivo {$detalle->monto}, $detalle->tessalidasinternas_id=>{$detalle->tessalidasinternas_id},$detalle->tessalidas_id=>{$detalle->tessalidas_id}",'tesabonos');
		 /*fin Aurditorias*/ 
			Flash::valid('Datos Guardados Correctamente');
			return Redirect::toAction('terminar');
        } else {
            Flash::warning('No se Pudieron Guardar los Datos...!!!');
        }
    }
	
	$detalle=new Tesabonosdetalles();
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesabonos_id='.Session::get('A_ID'));
	$this->a='';
	if($detalle->exists('tesingresos_id=0 AND abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID')))
	{
		$this->tesabonosdetalles=$detalle->find_first('conditions: tesingresos_id=0 AND abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID'));
		$this->total=$this->sumar_abonos($detalle->find('conditions: abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID')));
		$this->a='';
	}
	$formadepagos= new Tesformadepagosabonos();
	$this->pago=$formadepagos->find_first((int)$fpago);
	$this->numero=$detalle->ndocumento();
	

}
/*
Cheques
@fpago = id de la forma de pago
*/
public function cheques($fpago=1,$ingreso_id=0)
{
	$this->tesingresos_id=$ingreso_id;
	
	if (Input::hasPost('ingresos'))
	{
		$ingresos=new Tesingresos(Input::post('ingresos'));
		$ingresos->testipocambios_id=Session::get('CAMBIO_ID');
		$ingresos->estadoingreso='Pendiente';
		$ingresos->pr='NN';
		if ($ingresos->save()) 
		{
			/*Auditorias*/
		   Aclauditorias::add("Agrego un Cheque {$ingresos->numero}, tesingresos->id=>{$ingresos->id}",'tesingresos');
		  /*fin Aurditorias*/ 
			  $CHEQUES= new Teschequesingresos();
			   if(!$CHEQUES->exists('tesingresos_id='.$ingresos->id)){
			   $CHEQUES->tesingresos_id=$ingresos->id;
			   $CHEQUES->monto=$ingresos->totalconigv;
			   $CHEQUES->estadocheque='';
			   $CHEQUES->fecha_cobro=$ingresos->fvencimiento;			   
			   $CHEQUES->tesbancos_id=Input::post('tesbancos_id');
			   $CHEQUES->numerocheque=$ingresos->numero;
			   $CHEQUES->estado=1;
			   $CHEQUES->userid=Auth::get('id');
			   $CHEQUES->aclempresas_id=Session::get('EMPRESAS_ID');
			   $CHEQUES->save();
			 	/*Auditorias*/
		   	 	Aclauditorias::add("Agrego un Cheque Numero = {$CHEQUES->numerocheque}, Teschequesingresos->id=>{$CHEQUES->id}",'Teschequesingresos');
		   		/*fin Aurditorias*/ 
			   }
			$detalle=new Tesabonosdetalles();
			$detalle->tesabonos_id=Session::get('A_ID');
			$detalle->tessalidas_id='0';
			$detalle->tessalidasinternas_id='0';
			$detalle->tesingresos_id=$ingresos->id;
			$detalle->monto=$ingresos->totalconigv;
			$detalle->abono='1';
			$detalle->cargos='0';
			$detalle->tesdatos_id=$ingresos->tesdatos_id;
			$detalle->plancontable='10301';//$ingresos->getTescuentascorrientes()->cuentaplan;*/
			$detalle->save();
			Flash::valid('Datos Guardados Correctamente');
			/*Auditorias*/
		   	 	Aclauditorias::add("Agrego un abono Monto = {$detalle->monto} Tesabonosdetalles->id=>{$detalle->id}",'Tesabonosdetalles');
		   		/*fin Aurditorias*/
			return Redirect::toAction('cheques/'.$fpago.'/'.$ingresos->id);
        } else {
            Flash::warning('No se Pudieron Guardar los Datos...!!!');
        }
    }
	
	if (Input::hasPost('tesdetalles'))
	{
		$detalle=new Tesabonosdetalles(Input::post('tesdetalles'));
		
		if ($detalle->save()) 
		{
		/*Auditorias*/
		   	 	Aclauditorias::add("Agrego un abono Monto = {$detalle->monto} Tesabonosdetalles->id=>{$detalle->id}",'Tesabonosdetalles');
		   		/*fin Aurditorias*/
		/*validar si es el monto de abono es mayor o menor al monto de total de cargos*/
		$D=new Tesabonosdetalles();
		$D->getMenor_Mayor($detalle->tesabonos_id,$detalle->monto);
			/*Flash::valid('Datos Guardados Correctamente');*/
			return Redirect::toAction('terminar');
        } else {
            Flash::warning('No se Pudieron Guardar los Datos...!!!');
        }
    }

	$ingresos=new Tesingresos();
	$this->S=$ingresos;
	$abs = new Tesabonos();
	$ab=$abs->find_first((int)Session::get('A_ID'));
	$this->tesmonedas_id=$ab->tesmonedas_id;
	$detalle=new Tesabonosdetalles();
	/* Proveedor y bancos */
	$d=$detalle->find_first('conditions: tesabonos_id='.Session::get('A_ID'));
	$this->proveedor=$d->getTesdatos()->razonsocial;
	$this->tesdatos_id=$d->tesdatos_id;
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesabonos_id='.Session::get('A_ID'))-$this->sumar_abonos($detalle->find('conditions: abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID')));
	if($detalle->exists('tesingresos_id!=0 AND abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID')))
	{
		$this->tesdetalles=$detalle->find_first('conditions: abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID'));
		$this->ingreso=$ingresos->find_first($this->tesdetalles->tesingresos_id);
		$this->tesingresos_id=$this->tesdetalles->tesingresos_id;
		$this->total=$this->tesdetalles->monto;
	}
	

}	

/*
#
# Telecrediro, giro, abono CTA corriente
#
*/
public function bancos($fpago=1,$salidas_id=0)
{
	$this->tessalidas_id=$salidas_id;
	
	if (Input::hasPost('tesdetallevauchers'))
	{
		$detalle=new Tesabonosdetalles(Input::post('tesdetallevauchers'));
		
		if ($detalle->save()) 
		{
			/*Auditorias*/
		   	 	Aclauditorias::add("Agrego un abono tipo (Telecrediro-giro-abono-CTA corriente) Monto = {$detalle->monto} Tesabonosdetalles->id=>{$detalle->id}",'Tesabonosdetalles');
		   		/*fin Aurditorias*/
		/*validar si es el monto de abono es mayor o menor al monto de total de cargos*/
		$D=new Tesabonosdetalles();
		$D->getMenor_Mayor($detalle->tesabonos_id,$detalle->monto);
			Flash::valid('Datos Guardados Correctamente');
			return Redirect::toAction('terminar');
        } else {
            Flash::warning('No se Pudieron Guardar los Datos...!!!');
        }
    }

	$salidas=new Tessalidas();
	$this->S=$salidas;
	$abs = new Tesabonos();
	$ab=$abs->find_first((int)Session::get('A_ID'));
	$this->tesmonedas_id=$ab->tesmonedas_id;
	$detalle=new Tesabonosdetalles();
	/* Proveedor y bancos */
	$d=$detalle->find_first('conditions: tesabonos_id='.Session::get('A_ID'));
	if(!empty($d))
	{
		$this->proveedor=$d->getTesdatos()->razonsocial;
		$this->tesdatos_id=$d->tesdatos_id;
	}else
	{
		$this->proveedor=$ab->getTesdatos()->razonsocial;
		$this->tesdatos_id=$ab->tesdatos_id;
	}
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesabonos_id='.Session::get('A_ID'))-$this->sumar_abonos($detalle->find('conditions: abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID')));
	if($detalle->exists('tesingresos_id=0 AND abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID')))
	{
		$this->tesdetallevauchers=$detalle->find_first('conditions: tesingresos_id=0 AND abono=1 AND cargos=0 AND Tesabonos_id='.Session::get('A_ID'));
		$this->total=$this->tesdetallevauchers->monto;
	}
	$formadepagos= new Tesformadepagosabonos();
	$this->pago=$formadepagos->find_first((int)$fpago);
	$this->numero=$detalle->ndocumento();
}	
/*
#
# Letras de pago
# crea ingresos de letras para pagar una factura o mas 
*/
public function letras($fpago=3,$salidas_id=0,$doc=NULL,$ids=0,$NL=0)
{
	$this->nletras=$NL;
	$this->numerofactura=$doc;
	$this->SALD=new Tessalidas();
	$this->ids=$ids;
	$this->tessalidas_id=$salidas_id;
	$abs = new Tesabonos();
	$ab=$abs->find_first((int)Session::get('A_ID'));
	$this->tesmonedas_id=$ab->tesmonedas_id;
	$detalle=new Tesabonosdetalles();
	/* Proveedor y bancos */
	$d=$detalle->find_first('conditions: tesabonos_id='.Session::get('A_ID'));
	$this->proveedor=$d->getTesdatos()->razonsocial;
	$this->tesdatos_id=$d->tesdatos_id;
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesabonos_id='.Session::get('A_ID'))-$this->sumar_abonos($detalle->find('conditions: abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID')));
	if($detalle->existsLetra(Session::get('A_ID')))
	{
		/*VAlidar que solo se tome en cuenta las letras mas no las nc*/
		$this->tesdetallevauchers=$detalle->findLetras(Session::get('A_ID'));
		$this->tessalidas_id=1;
		$this->nletras=$detalle->existsLetra(Session::get('A_ID'));
	}
	$formadepagos= new Tesformadepagosabonos();
	$this->pago=$formadepagos->find_first((int)$fpago);
	
}

public function ingresoletras($c)
{
	$this->vale='hola'.$c;
	$detalle=new Tesabonosdetalles();
	$SALD=new Tessalidas();
	/* Proveedor y bancos */
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesabonos_id='.Session::get('A_ID'));
	if (Input::hasPost('salidas-'.$c))
	{
		$value=Input::post('salidas-'.$c);
		$this->vale.='salidas';
		$salidas=new Tessalidas(Input::post('salidas-'.$c));
		$salidas->testipocambios_id=Session::get('CAMBIO_ID');
		$salidas->serie='LTR';
		$salidas->numero=$SALD->generarNumeroLetras();
		$salidas->femision=$value['femision'];
		$salidas->cuentaporpagar='12321';
		$salidas->pr='NN';
		$salidas->igv='0';
		$salidas->fregistro=date('Y-m-d');
		$salidas->estadosalida='Pendiente';
		$salidas->aclempresas_id=Session::get('EMPRESAS_ID');
		if ($salidas->save()) 
		{
			/*Auditorias*/
		   	 	Aclauditorias::add("Agrego una salida tipo (letra) Número = {$salidas->numero}, Total = {$salidas->totalconigv}, Tessalidas->id=>{$salidas->id}",'Tessalidas');
		   		/*fin Aurditorias*/
			$letrassalida=new Tesletrassalidas();
			 if(!$letrassalida->exists('tessalidas_id='.$salidas->id)){
			$letrassalida->tessalidas_id=$salidas->id;
			$letrassalida->factura_id=Input::post('ids-'.$c);
			$letrassalida->numerodeletra=$salidas->numero;
			$letrassalida->estadoletras='Sin Numero';
			$letrassalida->monto=$salidas->totalconigv;
			$letrassalida->estado='1';
			$letrassalida->userid=Auth::get('id');
			$letrassalida->aclempresas_id=$salidas->aclempresas_id;
			$letrassalida->save();
			/*Auditorias*/
		   	 	Aclauditorias::add("Agrego una letra ({$letrassalida->estadoletras}) Número = {$letrassalida->numerodeletra}, Total = {$letrassalida->monto}, letrassalida->id=>{$letrassalida->id}",'Tesletrassalidas');
		   		/*fin Aurditorias*/
			}
			$detalle=new Tesabonosdetalles();
			$detalle->tesabonos_id=Session::get('A_ID');
			$detalle->tesingresos_id=0;
			$detalle->tessalidasinternas_id=0;
			$detalle->tessalidas_id=$salidas->id;
			$detalle->monto=$salidas->totalconigv;
			$detalle->abono='1';
			$detalle->cargos='0';
			$detalle->tesdatos_id=$salidas->tesdatos_id;
			$detalle->plancontable='42301';
			$detalle->save();
			/*Auditorias*/
		   	 	Aclauditorias::add("Agrego un Abono (Letra) ,Total = {$detalle->monto}, Tesabonosdetalles->id=>{$detalle->id}",'Tesabonosdetalles');
		   		/*fin Aurditorias*/
			$salidas=new Tessalidas();
			$this->salida=$salidas->find_first($detalle->tessalidas_id);
			$this->tesdetalles=$detalle->find_first($detalle->id);
			$this->vale.=' grabo';
        } 
  	 }
	/*Para grabar todo lo que respcta  adetalle de detraccion letras y retencion*/ 
	if (Input::hasPost('tesdetalles-'.$c))
	{
		$this->vale.=' detalle ';
		$detalle=new Tesabonosdetalles(Input::post('tesdetalles-'.$c));
		
		if ($detalle->save()) 
		{
			/*Auditorias*/
		   	 	Aclauditorias::add("Editando un detalle de un abono ,Total = {$detalle->monto}, Tesabonosdetalles->id=>{$detalle->id}",'Tesabonosdetalles');
		   		/*fin Aurditorias*/
			$this->tesdetalles=$detalle->find_first($detalle->id);
			$salidas=new Tessalidas();
			$this->salida=$salidas->find_first($detalle->tessalidas_id);
			$totalconigv=$this->salida->totalconigv;
			/*validar si la suma de los abonos es es igual el total de la factura*/
			if($detalle->getTesabonos()->tesformadepagosabonos_id==14)
			{
				$monto=$salidas->getAdelantos($detalle->tessalidas_id,'tessalidas_id');
				if(number_format($totalconigv,2,'.','')==number_format($monto,2,'.',''))
				{
					$salidas->estadosalida='PAGADO';
					$salidas->save();
					Flash::valid('Datos Guardados Correctamente'.number_format($totalconigv,2,'.','').'=='.number_format($monto,2,'.',''));
				}
			}
			$this->vale.='grabo';
        } 
    }
	if (Input::hasPost('terminar'))
	{
		Flash::valid('Datos Guardados Correctamente');
		return Redirect::toAction('terminar');
       
    }
}
/*
#
# Letras internas de pago
# crea ingresos de letras para pagar una factura o mas 
*/
public function letras_i($fpago=3,$salidas_id=0,$doc=NULL,$ids=0,$NL=0)
{
	$this->nletras=$NL;
	$this->numerofactura=$doc;
	$this->SALD=new Tessalidasinternas();
	$this->ids=$ids;
	$this->tessalidas_id=$salidas_id;
	$abs = new Tesabonos();
	$ab=$abs->find_first((int)Session::get('A_ID'));
	$this->tesmonedas_id=$ab->tesmonedas_id;
	$detalle=new Tesabonosdetalles();
	/* Proveedor y bancos */
	$d=$detalle->find_first('conditions: tesabonos_id='.Session::get('A_ID'));
	$this->proveedor=$d->getTesdatos()->razonsocial;
	$this->tesdatos_id=$d->tesdatos_id;
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesabonos_id='.Session::get('A_ID'))-$this->sumar_abonos($detalle->find('conditions: abono=1 AND cargos=0 AND tesabonos_id='.Session::get('A_ID')));
	if($detalle->existsLetra_i(Session::get('A_ID')))
	{
		/*VAlidar que solo se tome en cuenta las letras mas no las nc*/
		$this->tesdetallevauchers=$detalle->findLetras_i(Session::get('A_ID'));
		$this->tessalidas_id=1;
		$this->nletras=$detalle->existsLetra_i(Session::get('A_ID'));
	}
	$formadepagos= new Tesformadepagosabonos();
	$this->pago=$formadepagos->find_first((int)$fpago);
	
}

public function ingresoletras_i($c)
{
	$this->vale='hola'.$c;
	$detalle=new Tesabonosdetalles();
	$SALD=new Tessalidasinternas();
	/* Proveedor y bancos */
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesabonos_id='.Session::get('A_ID'));
	if (Input::hasPost('salidas-'.$c))
	{
		$value=Input::post('salidas-'.$c);
		$this->vale.='salidas';
		$salidas=new Tessalidasinternas(Input::post('salidas-'.$c));
		$salidas->testipocambios_id=Session::get('CAMBIO_ID');
		$salidas->numero=$SALD->generarNumeroLetras('001');
		$salidas->femision=$value['femision'];
		$salidas->cuentaporpagar='42301';
		$salidas->pr='NN';
		$salidas->igv='0';
		$salidas->fregistro=date('Y-m-d');
		$salidas->estadoingreso='Pendiente';
		$salidas->aclempresas_id=Session::get('EMPRESAS_ID');
		if ($salidas->save()) 
		{
			/*Auditorias*/
		   	 	Aclauditorias::add("Ingreso interno tipo (letrainterna) ,Total = {$salidas->total}, Tessalidasinternas->id=>{$salidas->id}",'Tessalidasinternas');
		   		/*fin Aurditorias*/			
			$letrassalida=new Tesletrassalidasinternas();
			 if(!$letrassalida->exists('tessalidasinternas_id='.$salidas->id)){
			$letrassalida->tessalidasinternas_id=$salidas->id;
			$letrassalida->factura_id=Input::post('ids-'.$c);
			$letrassalida->numerodeletra=$salidas->numero;
			$letrassalida->estadoletras='Sin Numero';
			$letrassalida->monto=$salidas->total;
			$letrassalida->estado='1';
			$letrassalida->userid=Auth::get('id');
			$letrassalida->aclempresas_id=$salidas->aclempresas_id;
			$letrassalida->save();
			/*Auditorias*/
		   	 	Aclauditorias::add("Ingreso de letra interna Numero = {$letrassalida->numerodeletra} ,Total = {$letrassalida->total}, Tesletrassalidasinternas->id=>{$letrassalida->id}",'Tesletrassalidasinternas');
		   		/*fin Aurditorias*/
			 }
			$detalle=new Tesabonosdetalles();
			$detalle->tesabonos_id=Session::get('A_ID');
			$detalle->tesingresos_id=0;
			$detalle->tessalidasinternas_id=$salidas->id;
			$detalle->tessalidas_id=0;
			$detalle->monto=$salidas->total;
			$detalle->abono='1';
			$detalle->cargos='0';
			$detalle->tesdatos_id=$salidas->tesdatos_id;
			$detalle->plancontable='42301';
			$detalle->save();			
			/*Auditorias*/
		   	 	Aclauditorias::add("Agrego un abono ,Total = {$detalle->monto}, Tesabonosdetalles->id=>{$detalle->id}",'Tesabonosdetalles');
		   		/*fin Aurditorias*/
			$salidas=new Tessalidas();
			$this->salida=$salidas->find_first($detalle->tessalidasinternas_id);
			$this->tesdetalles=$detalle->find_first($detalle->id);
			$this->vale.=' grabo';
        } 
  	 }
	 
	if (Input::hasPost('tesdetalles-'.$c))
	{
		$this->vale.=' detalle ';
		$detalle=new Tesabonosdetalles(Input::post('tesdetalles-'.$c));
		
		if ($detalle->save()) 
		{
			$this->tesdetalles=$detalle->find_first($detalle->id);
			$salidas=new Tessalidas();
			$this->salida=$salidas->find_first($detalle->tessalidasinternas_id);
			$this->vale.='grabo';
        } 
    }
	if (Input::hasPost('terminar'))
	{
		Flash::valid('Datos Guardados Correctamente');
		return Redirect::toAction('terminar');
       
    }
}
public function terminar()
{
	$this->LI= new tesletrasingresos();
	$abs = new Tesabonos();
	$ab=$abs->find_first((int)Session::get('A_ID'));
	$this->ab=$ab;
		if(empty($ab->numero))$this->numero=$abs->numero(); else $this->numero=$ab->numero;
		if(empty($ab->asiento))$this->asiento=$abs->asiento(); else $this->asiento=$ab->asiento;
	$detalle=new Tesabonosdetalles();
	$this->detalles=$detalle->find('conditions: tesabonos_id='.Session::get('A_ID'));
	$this->importe=$detalle->sum('monto','conditions: tesabonos_id='.Session::get('A_ID').' AND abono="1"');
	$empresas = new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
}
public function finalizar($id=0)
{
	$this->LI= new tesletrasingresos();
	View::select('terminar');
	$detalle=new Tesabonosdetalles();
	/*Monto del Vouchers*/
	$monto_total=$this->sumar_abonos($detalle->find('conditions: abono=1 AND tesabonos_id='.Session::get('A_ID')));
	$abs = new Tesabonos();
	$ab=$abs->find_first((int)Session::get('A_ID'));
	if($id!=0){
	if(empty($ab->numero))$ab->numero=$abs->numero();
	if(empty($ab->asiento))$ab->asiento=$abs->asiento();
	$ab->total=$monto_total;
	$ab->estadov='Terminado';
	$ab->mes=date('m');
	$ab->estado='1';
	$ab->activo='1';
	$ab->save();
	/*Auditorias*/
	Aclauditorias::add("Termino la creacion de un vauchers ,Total = {$ab->total}, Tesabonos->id=>{$ab->id}",'Tesabonos');   /*fin Aurditorias*/
	}
	$this->numero=$ab->numero;
	$this->asiento=$ab->asiento;
	$this->detalles=$detalle->find('conditions: tesabonos_id='.Session::get('A_ID'));
	$empresas = new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	
	$this->ab=$ab;
}

/*
# Para elimiar un detalle hay que validar si tiene alguna relacion con salida o ingreso
# si es ingreso el ingreso regresar a pendiente 
#
#
#
#
*/
public function eliminardetalle($id)
{
	$detalles=new Tesabonosdetalles();
	$detalle=$detalles->find_first((int)$id);
	if($detalle)
	{
		if($detalle->cargos!=0)
		{
			if($detalle->tesingresos_id!=0)
			{
				$Ingresos=new Tesingresos();
				$Ingresos->delete($detalle->tesingresos_id);
				/*$DETRACCIONES= new Tesdetracciones();
				if($DETRACCIONES->exists('tesingresos_id='.$ingreso->id))
				{
					$DETRACCIONES->delete('tesingresos_id='.$ingreso->id);
				}*/
				
	/*Auditorias*/
	Aclauditorias::add("Elimido un ingreso,Tesingresos->id=>{$Ingresos->id}",'Tesingresos');  
	/*fin Aurditorias*/
			}
			if($detalle->tessalidas_id!=0)
			{
				$Salidas=new Tessalidas();
				$salida=$Salidas->find_first((int)$detalle->tessalidas_id);
				if($salida->estadosalida=='PAGADO'){$salida->estadosalida='Pendiente';}else{}
				$salida->save();

			}
			if($detalle->tessalidasinternas_id!=0)
			{
				$Salidas=new Tessalidasinternas();
				$salida=$Salidas->find_first((int)$detalle->tessalidasinternas_id);
				if($salida->estadosalida=='PAGADO'){$salida->estadosalida='Pendiente';}else{}
				$salida->save();

			}
			$detalles->delete((int)$id);				
	/*Auditorias*/
	Aclauditorias::add("Elimido un detalle de abono ,Tesabonosdetalles->id=>{$detalles->id}",'Tesabonosdetalles');  
	/*fin Aurditorias*/
			return Redirect::toAction('grabardetalle');
		}
		
		if($detalle->abono!=0)
		{
			if($detalle->tesingresos_id!=0)
			{
				$Ingresos=new Tesingresos();
				
				$cheques= new Teschequesingresos();
				if($cheques->exists('tesingresos_id='.$detalle->tesingresos_id))
				{
					$cheques->delete('tesingresos_id='.$detalle->tesingresos_id);
				}
				$Ingresos->delete($detalle->tesingresos_id);				
	/*Auditorias*/
	Aclauditorias::add("Elimido un ingreso,Tesingresos->id=>{$Ingresos->id}",'Tesingresos');  
	/*fin Aurditorias*/
			}
			if($detalle->tessalidas_id!=0)
			{
				$Salidas=new Tessalidas();
				$salida=$Salidas->find_first((int)$detalle->tessalidas_id);
				if($salida->estadosalida=='PAGADO'){$salida->estadosalida='Pendiente';}else{}
				$salida->save();

			}
			if($detalle->tessalidasinternas_id!=0)
			{
				$Salidas=new Tessalidasinternas();
				$salida=$Salidas->find_first((int)$detalle->tessalidasinternas_id);
				if($salida->estadosalida=='PAGADO'){$salida->estadosalida='Pendiente';}else{}
				$salida->save();

			}
			$detalles->delete((int)$id);
							
	/*Auditorias*/
	Aclauditorias::add("Elimido un detalle de abono ,Tesabonosdetalles->id=>{$detalles->id}",'Tesabonosdetalles');  
	/*fin Aurditorias*/
			return Redirect::toAction('grabardetalle');
		}
	
	}else
	{
		Flash::warning('No existe nigun registro con este valor '.$id);
		return Redirect::toAction('grabardetalle');
	}
}
public function anularvouchers()
{
	$id=Session::get('A_ID');
	$vauchers = new Tesabonos();
    $ab=$vauchers->find_first($id);
    $ab->estadov='ANULADO';
	$ab->save();												
					/*Auditorias*/
					Aclauditorias::add("Anulo un abono,Tesabonos->id=>{$ab->id}",'Tesabonos');
					/*fin Aurditorias*/
	$detalles=new Tesabonosdetalles();
	$DETALLES=$detalles->find('conditions: tesabonos_id='.(int)$ab->id);
	if($DETALLES)
	{
		foreach($DETALLES as $detalle):
		if($detalle->cargos!=0)/* Si es cargo */
		{
			if($detalle->tesingresos_id!=0)/*si es ingreso*/
			{
				
				$id_detraccion_ingreso=$detalle->tesingresos_id;
				$Ingresos=new Tesingresos();
				$ingreso=$Ingresos->find_first((int)$detalle->tesingresos_id);
				$ingreso->estadoingreso='Pendiente';
				$ingreso->save();												
					/*Auditorias*/
					Aclauditorias::add("Restablecio una Salida Numero=({$ingreso->serie}-{$ingreso->numero}),Tesingresos->id=>{$Ingresos->id}",'Tesingresos');  
					/*fin Aurditorias*/
				/*$DETRACCIONES= new Tesdetracciones();
				if($DETRACCIONES->exists('tesingresos_id='.$ingreso->id))
				{
					$DETRACCIONES->delete('tesingresos_id='.$ingreso->id);
				}*/
			}
			if($detalle->tessalidas_id!=0)
			{
				$Salidas=new Tessalidas();
				$salida=$Salidas->find_first((int)$detalle->tessalidas_id);
				$salida->estadosalida='ANULADO';
				$salida->save();
					/*Auditorias*/
					Aclauditorias::add("Anulo una Salida Numero=({$salida->serie}-{$salida->numero}),Tessalidas->id=>{$salida->id}",'Tessalidas'); 
			}
			$detalle->estado=0;
			$detalle->save();												
					/*Auditorias*/
					Aclauditorias::add("Anulo una detalle de abono,Tesabonosdetalles->id=>{$detalle->id}",'Tesabonosdetalles');
					/*fin Aurditorias*/
			//return Redirect::toAction('grabardetalle');
		}
		
		if($detalle->abono!=0)/* Si es abono */
		{
			if($detalle->tesingresos_id!=0)
			{
				if($detalle->getTesingresos()->tesdocumentos_id=='13'){
				$Ingresos=new Tesingresos();
				$ingreso=$Ingresos->find_first((int)$detalle->tesingresos_id);
				if($ingreso->estadoingreso=='PAGADO'){$ingreso->estadoingreso='Pendiente';}else{}
				$ingreso->save();												
					/*Auditorias*/
					Aclauditorias::add("Restablecio un ingreso Numero=({$ingreso->serie}-{$ingreso->numero}),Tesingresos->id=>{$Ingresos->id}",'Tesingresos');  
					/*fin Aurditorias*/
				}else{
				$Ingresos=new Tesingresos();
				$ingreso=$Ingresos->find_first((int)$detalle->tesingresos_id);
				$ingreso->estadoingreso='ANULADO';
				$ingreso->save();
					/*Auditorias*/
					Aclauditorias::add("Anulo un ingreso Numero=({$ingreso->serie}-{$ingreso->numero}),Tesingresos->id=>{$Ingresos->id}",'Tesingresos');  
					/*fin Aurditorias*/
				}
				$letrasapp=new Tesaplicacionletraingresos();
				$letrasapp->delete('tesingresos_id='.$detalle->getTesingresos()->getTesletrasingresos()->id);
			}
			if($detalle->tessalidas_id!=0)
			{
				$Salidas=new Tessalidas();
				$Salidas->find_first((int)$detalle->tessalidas_id);
				$Salidas->estadosalida='ANULADO';
				$Salidas->save();
					/*Auditorias*/
					Aclauditorias::add("Anulo una salida Numero=({$Salidas->serie}-{$Salidas->numero}),Tessalidas->id=>{$Salidas->id}",'Tessalidas');  
					/*fin Aurditorias*/
			}
			$tesdetracciones= new Tesdetracciones();
			$tesdetracciones->delete('monto="'.$detalle->monto.'" AND tesingresos_id='.$vauchers->getIngresoDetraccion($ab->id));
			//return Redirect::toAction('grabardetalle');
		}
		endforeach;
		Flash::warning('Abono Anulado');
		return Redirect::toAction('');
	}else
	{
		Flash::warning('No existe nigun registro con este valor '.$id);
		return Redirect::toAction('');
	}
}
public function eliminar($id) {
        try {
            $menu = new Tesabonos();
            $menu->find_first($id);
            if ($menu->delete()) {
                Flash::valid("Vouchers <b>{$menu->id}</b> fué Eliminado...!!!");
                Aclauditorias::add("Eliminó Vouchers {$menu->numero} del sistema", 'Tesabonos');
            } else {
                Flash::warning("No se Pudo Eliminar el Menu <b>{$menu->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::redirect();
    }

/* funciones para varios procesos*/

public function ordenes()
{
	View::template(null);
	$ORD=new Tesordendecompras();
	$q=$_GET['q'];
	$results = $ORD->getOrdenes($q);
	foreach ($results as $value)
	{
		$json = array();
		$json['id'] = $value->id;
		$json['name'] = $value->numero_interno.', '.$value->razonsocial;
		$this->data[] = $json;
	}
}

public function cuentacorriente($id=0)
{
	$this->id=$id;
}
/* recibe el id de la cuenta corriente*/
public function numerocheque($id=0)
{
	/*$salidas=new Tessalidas();
	/*$this->id=$salidas->generarNumerocheque($id);*/
	$this->id='';
}
public function plancontable($id=0)
{
	View::select('numerocheque');
	$plan=Load::model('tescuentascorrientes');
	$p=$plan->find_first((int)$id);
	$this->id=$p->cuentaplan;
}



function sumar_abonos($detalles)
{
	$total=0;
	foreach($detalles as $det):
	/*if(!empty($det->tessalidas_id))
	{
		if($det->getTessalidas()->tesdocumentos_id!=13)
		{
			$total=$total+$det->monto;
		}
	}elseif(!empty($det->tessalidasinternas_id))
	{
		if($det->getTessalidasinternas()->tesdocumentos_id!=13)
		{
			$total=$total+$det->monto;
		}
	}else{*/
	$total=$total+$det->monto;
	/*}*/
	endforeach;
	
	return $total;
	
}
/*Cuando el abono es antes y despues la aplicacion*/
public function listado_app()
{
	$abonos = new Tesabonos();
	$this->abonos_adelanto=$abonos->getAbonos_ad();
}
public function creardetalle_app()
	{
		if(Session::has('A_ID'))
		{
			$abs = new Tesabonos();
			$ab=$abs->find_first((int)Session::get('A_ID'));
			/*Solo facturas*/
			$this->ab=$ab;
			
		 	if(!empty($ab->tesdatos_id))
			{
			  Session::set('cliente_id',$ab->tesdatos_id);
			  $tesdatos= new Tesdatos();
			  $this->tesdatos=$tesdatos->find_first((int)Session::get('cliente_id'));
			
			$conditions=' AND s.tesmonedas_id='.$ab->tesmonedas_id;
			$tesformadepagosabonos_id=$ab->tesformadepagosabonos_id;
			$salidas= new Tessalidas();
			
			if($tesformadepagosabonos_id==3)
			{
				//$conditions.=' AND !ISNULL()';
			$this->salidas=$salidas->getLetrassinabono($ab->tesdatos_id,$conditions,$ab->tesformadepagosabonos_id);
			}else{
			$this->salidas=$salidas->getPendientescobro($ab->tesdatos_id,$conditions,$ab->tesformadepagosabonos_id);
			}
			}else{
				
						Flash::warning('Para poder Continuar debera seleccionar un Cliente!!'.Session::get('cliente_id'));
						return Redirect::toAction('editar');
				}
			
		}
	}
	
public function grabardetalle_app()
{
	$this->salida_id=0;
	if(Input::post('s'))
	{
		$abs = new Tesabonos();
		$ab=$abs->find_first((int)Session::get('A_ID'));
		$code='Es una Ingreso Normal';
		$ids=$this->ids=Input::post('s');
		$salidas= new Tessalidas(); 
		$salidasinternas= new Tessalidasinternas();
		$t=0;
		foreach(explode(',',$ids) as $item=>$value):
			$monto_A=0;
			$VAL=explode('-',$value);
			if($VAL[1]=='RUC')$salida=$salidas->find_first((int)$VAL[0]);	
			if($VAL[1]=='INT')$salida=$salidasinternas->find_first((int)$VAL[0]);	
		 	$detalle=new Tesabonosdetalles();
		 	$detalle->tesabonos_id=Session::get('A_ID');
		 	if($VAL[1]=='RUC'){$detalle->tessalidas_id=(int)$salida->id;$monto_A=$salidas->getAdelantos($salida->id);}
		 	if($VAL[1]=='INT'){$detalle->tessalidasinternas_id=$salida->id;$monto_A=$salidas->getAdelantos($salida->id,'tessalidasinternas_id');}
		 	if($salida->tesdocumentos_id==13)
			{
				$detalle->abono='1';
		    	$detalle->cargos='0';
		 	}elseif($ab->tesformadepagosabonos_id==10 || $ab->tesformadepagosabonos_id==14)
		 	{
				$detalle->abono='1';
		    	$detalle->cargos='0';
		 	}else
		 	{
		    	$detalle->abono='0';
		    	$detalle->cargos='1';
		 	}
		 	$detalle->tesdatos_id=$salida->tesdatos_id;
		 	if($VAL[1]=='RUC')
		 	{
			 	if($ab->tesformadepagosabonos_id==10)/*detraccion*/
				{
					$detalle->monto=($salida->totalconigv-$monto_A)*Session::get('DETRACCION');
			  	}elseif($ab->tesformadepagosabonos_id==14)/*retencion*/
			 	{
			  		$detalle->monto=($salida->totalconigv-$monto_A);/**Session::get('RETENCION');*/
			 	}else{
			  		$detalle->monto=$salida->totalconigv-$monto_A;
			 	}
		 	}
		 	
			if($VAL[1]=='INT') $detalle->monto=$salida->total-$monto_A;
		 	$code.=' EL monto de la factura es '.$detalle->monto;
		 	$detalle->tescuentascorrientes_id=0;
		 	$detalle->estado=1;
		 	$detalle->plancontable=0;
		 	$detalle->save();
		 /*Auditorias*/
		 //Aclauditorias::add("Agrego un detalle {$detalle->monto} $detalle->tessalidasinternas_id=>{$detalle->tessalidasinternas_id},$detalle->tessalidas_id=>{$detalle->tessalidas_id}",'tesabonos');
		 /*fin Aurditorias*/
			if($ab->tesformadepagosabonos_id==10 || $ab->tesformadepagosabonos_id==14)
			{
				$salida->estadosalida='Pendiente'; 
			}else{
				/*VALIDA SI EL ABONO ES = AL TOTAL DE LA FACTURA*/				
				//$salida->estadosalida='PAGADO';
			}
			$salida->save();
			
		endforeach;
		
		$_POST[]=array();
		$abs = new Tesabonos();
		$ab=$abs->find_first((int)Session::get('A_ID'));
		$ab->importe=$t.'';
		$ab->estado='1';
		$ab->activo='1';
		$ab->save();
		Flash::valid($code);
		if($ab->tesformadepagosabonos_id==10 || $ab->tesformadepagosabonos_id==14)
		{
			//Redirect::toAction('detalle_abono/');
		}else
		{
			//Redirect::toAction('grabardetalle');
		}
	/*SUMA DE LAS FACTURAS*/
		$D=new Tesabonosdetalles();
			$D->getMenor_Mayor(Session::get('A_ID'),$ab->total);
	
	}
		/*Para Grabar el detalle de una tranferencia */
		$abs = new Tesabonos();
		$ab=$abs->find_first((int)Session::get('A_ID'));
		if($ab->tesformadepagosabonos_id==10 || $ab->tesformadepagosabonos_id==14)
		{
			//Redirect::toAction('detalle_abono/');
		}
		$this->ab=$ab;
		if(empty($ab->numero))$this->numero=$abs->numero(); else $this->numero=$ab->numero;
		if(empty($ab->asiento))$this->asiento=$abs->asiento(); else $this->asiento=$ab->asiento;
		
		$detalle=new Tesabonosdetalles();
		$this->detalles=$detalle->find('conditions: tesabonos_id='.Session::get('A_ID'));
		$empresas = new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		
		/*Chueqes*/
		if($detalle->exists('cargos="0" AND abono="1" AND tessalidas_id!=0 AND tesabonos_id='.Session::get('A_ID')))
		{
			$s_d=$detalle->find_first('conditions: cargos="0" AND abono="1" AND tessalidas_id!=0 AND tesabonos_id='.Session::get('A_ID'));
			$this->salida_id=$s_d->tessalidas_id;
		}
		
		/*Letras*/
		if($detalle->exists('cargos="0" AND abono="1" AND tessalidas_id=0 AND tesingresos_id!=0 AND tesabonos_id='.Session::get('A_ID')))
		{
			$this->salida_id=1;
		}
		//Redirect::toAction('terminar/');
	}
public function editar_detalle($id=0,$url='')
{
	$detalle=new Tesabonosdetalles();
	/*if()
	{
	}*/
}

public function no_mostrar_adelanto_app($id)
{
	$abs = new Tesabonos();
	$abs->find_first($id);
	$abs->adelanto_no_mostrar='1';
	$abs->save();
	return Redirect::toAction('listado_app');
}


}
?>