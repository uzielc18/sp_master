<?php 
View::template('spatricia/default');
Load::models('tesvauchers','tesdetallevauchers','tesingresos','tesmonedas','voucherformadepagos','aclempresas','tesdatos','tessalidas','tesletrasingresos','tesdetracciones','tesaplicacionletraingresos');
class VouchersController extends AdminController
{
	protected function before_filter(){ 
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	
	public function index($id=1,$Y='',$m='')
	{
		$cond='';
		if($id!=0)$cond=' AND voucherformadepagos_id='.$id;
		$this->id=$id;
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		Session::delete('V_ID');
		Session::delete('V_ID_O');
		Session::delete('proveedor_id');
		$vouchers=new Tesvauchers();
		$this->vouchers=$vouchers->find('conditions: (DATE_FORMAT(fecha,"%Y-%m")="'.$anio.'-'.$mes_activo.'" OR ISNULL(fecha)) AND aclempresas_id='.Session::get('EMPRESAS_ID').$cond,'order: numero DESC');
		$formas = new Voucherformadepagos();
		$this->pagos=$formas->find('conditions: activo=1');
		
	}
	public function cargar($id,$url='editar')
	{
		$vcs = new Tesvauchers();
		$vc=$vcs->find_first((int)$id);
		Session::set('V_ID',$vc->id);
		//Session::set('proveedor_id',$proveddor);
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
	if (Input::hasPost('tesvauchers')) {
                $vc = new Tesvauchers(Input::post('tesvauchers'));
				$vc->aclusuarios_id=Auth::get('id');
				$vc->testipocambios_id=Session::get('CAMBIO_ID');
				$vc->estado='0';
				$vc->userid=Auth::get('id');
				$vc->activo='0';
				$vc->estadov='Pendiente';
				$vc->aclempresas_id=Session::get('EMPRESAS_ID');
                if ($vc->save()) {
					
					$proveedor=$_POST['proveedor'];
					$this->p=$proveedor;
					Session::set('V_ID',$vc->id);
					Session::set('proveedor_id',$proveedor);
                    Flash::valid('Vaoucher fué agregado Exitosamente...!!!');
                    Aclauditorias::add("Genero un vouchers con numero {$vc->numero} al sistema");
					if($vc->voucherformadepagos_id==11){
						if(Session::get('proveedor_id')!=0)
						{
						return Redirect::toAction('creardetalle');
						}else
						{
						Flash::warning('Para poder Continuar deve seleccionar un Proveedor!!');
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
		$this->p=Session::get('proveedor_id');
		View::select('crear');
		$this->titulo='Editar';
    	$vcs = new Tesvauchers();
		$vc=$vcs->find_first((int)Session::get('V_ID'));
		if (Input::hasPost('tesvauchers')) {
				if ($vc->update(Input::post('tesvauchers'))) {
					
					$proveedor=$_POST['proveedor'];
					Session::set('V_ID',$vc->id);
					Session::set('proveedor_id',$proveedor);
                    
                    Aclauditorias::add("Genero un vouchers con numero {$vc->numero} al sistema");
                    if($vc->voucherformadepagos_id==11){
						if(Session::get('proveedor_id')!=0)
						{
						Flash::valid('Vaoucher fué agregado Exitosamente...!!!');
						return Redirect::toAction('creardetalle');
						}else
						{
						Flash::warning('Para poder Continuar debera seleccionar un Proveedor!!');
						return Redirect::toAction('editar');
						}
					}else
					{
						Flash::valid('Vaoucher fué agregado Exitosamente...!!!');
						return Redirect::toAction('creardetalle');
					}
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
	$this->tesvauchers=$vc;
	}
	
	public function creardetalle()
	{
		$DETRACCIONES= new Tesdetracciones();
		if(Session::has('V_ID'))
		{
			$vcs = new Tesvauchers();
			$vc=$vcs->find_first((int)Session::get('V_ID'));
			if($vc->voucherformadepagos_id==13){
				$detalle=new Tesdetallevauchers();
				$this->DD=$detalle;
				$this->detalle=$detalle->find_first('conditions: tesvauchers_id='.Session::get('V_ID'));
			}
			$detraccion=$vc->voucherformadepagos_id;
			/*Solo facturas*/
			$this->vc=$vc;
			if($detraccion!=10)
			{
				$conditions=' tesdocumentos_id!=15 AND tesdocumentos_id!=37 AND tesdocumentos_id!=38 AND tesdocumentos_id!=39 AND tesdocumentos_id!=27 AND tesdocumentos_id!=35 AND tesdocumentos_id!=34 AND estadoingreso!="PAGADO" AND estadoingreso!="TERMINADO" AND aclempresas_id='.Session::get('EMPRESAS_ID');
			}else{
				$conditions=' tesdocumentos_id=7 AND estadoingreso!="PAGADO" AND cuantagastos="63301" AND estadoingreso!="Detraccion" AND aclempresas_id='.Session::get('EMPRESAS_ID');
			}
		 	if(Session::get('proveedor_id')!=0)
			{
			  $conditions.=' AND tesdatos_id='.Session::get('proveedor_id');
			  $tesdatos= new Tesdatos();
			  $this->tesdatos=$tesdatos->find_first((int)Session::get('proveedor_id'));
			}
			if($detraccion==10)
			{
			$ingresos= new Tesingresos();
			$this->ingresos=$ingresos->getFacturasDetraccion($conditions);
				
			}else{
			$conditions.=' AND tesmonedas_id='.$vc->tesmonedas_id;
			$ingresos= new Tesingresos();
			echo $conditions;
			$this->ingresos=$ingresos->find('conditions: '.$conditions);
			}
		}
	}
public function grabardetalle()
	{
		$DETRACCIONES= new Tesdetracciones();
		$this->salida_id=0;
		if(Input::post('i')){
		$code='Es una Ingreso Normal';
		$ids=$this->ids=Input::post('i');
		$ingresos= new Tesingresos();
		$t=0;
		foreach(explode(',',$ids) as $item=>$value):
		$ingreso=$ingresos->find_first((int)$value);
		 
		 $detalle=new Tesdetallevauchers();
		 $detalle->tesvauchers_id=Session::get('V_ID');
		 $detalle->tesingresos_id=$ingreso->id;
		 $detalle->tessalidas_id=0;
		 if($ingreso->tesdocumentos_id==13){
		 $detalle->abono='1';
		 $detalle->cargos='0';
		 }else
		 {
		 $detalle->abono='0';
		 $detalle->cargos='1';
		 }
		 $detalle->tesdatos_id=$ingreso->tesdatos_id;
		 $monto_resta=0;
		 
		 if($ingreso->estadoingreso=="ADELANTADO")
		 {
			 $code.=' Tiene Adelanto';
			 $monto_resta=$monto_resta+number_format(($ingreso->getAdelantos()),2,'.','');
		     
		 }
		 $v='No tiene detraccion';
		 if($DETRACCIONES->exists('tesingresos_id='.$ingreso->id))
		 {
			 $code.=' es detraccion';
			 switch ($ingreso->tesmonedas_id)
				{
					/*soles*/	case 1: $v='Soles'; $monto_resta=$monto_resta+number_format($ingreso->getTesdetracciones()->monto,2,'.',''); break;
					/*Dolares*/ case 2: $v='Dolares';$monto_resta=$monto_resta+number_format(($ingreso->getTesdetracciones()->monto/$ingreso->getTestipocambios()->compra),2,'.',''); break;
					/*soles*/	case 4: $v='Soles';$monto_resta=$monto_resta+number_format($ingreso->getTesdetracciones()->monto,2,'.',''); break;
					/*dolares*/ case 5: $v='Dolares';$monto_resta=$monto_resta+number_format(($ingreso->getTesdetracciones()->monto/$ingreso->getTestipocambios()->compra),2,'.',''); break;
					/*soles*/	case 0:$v='Soles'; $monto_resta=$monto_resta+number_format(0,2,'.','');
					break;
					default: $v='Default';$monto_resta=$monto_resta+number_format(0,2,'.',''); break;
				}
		 }
		 
		$detalle->monto=$ingreso->totalconigv-$monto_resta;
		$code.=' EL monto de la factura es '.$monto_resta.$v.$ingreso->id;
		$detalle->tescuentascorrientes_id=0;
		$detalle->estado=1;
		$detalle->plancontable=0;
		$detalle->save();
		$ingreso->estadoingreso='PAGADO';
		$ingreso->save();
		endforeach;
		Input::delete();
		$vcs = new Tesvauchers();
		$vc=$vcs->find_first((int)Session::get('V_ID'));
		$vc->importe=$t.'';
		$vc->estado='1';
		$vc->activo='1';
		$vc->save();
		Flash::valid($code);
		return Redirect::toAction('grabardetalle');
		}
		/*Para Grabar el detalle de una tranferencia */
		
		if(Input::hasPost('detalle'))
		{
			$code='Es una transferencia a caja';
			$detalle=new Tesdetallevauchers(Input::post('detalle'));
			$detalle->tesvauchers_id=Session::get('V_ID');
		 	$detalle->tesingresos_id='0';
		 	$detalle->tessalidas_id='0';
		 	$detalle->tesdatos_id='14888';
			$detalle->tescuentascorrientes_id=0;
		    $detalle->estado=1;
		    $detalle->plancontable='10103';
			$detalle->abono='0';
		 	$detalle->cargos='1';
			$detalle->save();
			return Redirect::toAction('grabardetalle');
		}	
					
		$vcs = new Tesvauchers();
		$vc=$vcs->find_first((int)Session::get('V_ID'));
		$this->vc=$vc;
		if(empty($vc->numero))$this->numero=$vcs->numero(); else $this->numero=$vc->numero;
		if(empty($vc->asiento))$this->asiento=$vcs->asiento(); else $this->asiento=$vc->asiento;
		$detalle=new Tesdetallevauchers();
		$this->detalles=$detalle->find('conditions: tesvauchers_id='.Session::get('V_ID'));
		$empresas = new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		
		/*Chueqes*/
		if($detalle->exists('cargos="0" AND abono="1" AND tessalidas_id!=0 AND tesvauchers_id='.Session::get('V_ID'))){
		$s_d=$detalle->find_first('conditions: cargos="0" AND abono="1" AND tessalidas_id!=0 AND tesvauchers_id='.Session::get('V_ID'));
		$this->salida_id=$s_d->tessalidas_id;
		}
		
		/*Letras*/
		if($detalle->exists('cargos="0" AND abono="1" AND tessalidas_id=0 AND tesingresos_id!=0 AND tesvauchers_id='.Session::get('V_ID'))){
		 $this->salida_id=1;
		}
		
	}
/*
APLICACION A LETRA DE ADELANTO
*/	
public function aplicacion($fpago=1,$app=0)
{
	/* validar que eista solo un cargo para poder aplicar varias letras */
	$detalles=new Tesdetallevauchers();
	
	$this->dets=$dets=$detalles->find('conditions: tesvauchers_id='.Session::get('V_ID').' AND cargos=1');
	$this->permite_select=1;
	$this->tessalidas_id=$app;
	$this->fpago=$fpago;
	
	if (Input::hasPost('tesdetallevauchers'))
	{
		$detalle=new Tesdetallevauchers(Input::post('tesdetallevauchers'));
		
		if ($detalle->save()) 
		{
		/*validar si es el monto de abono es mayor o menor al monto de total de cargos*/
		$D=new Tesdetallevauchers();
		//$D->getMenor_Mayor($detalle->tesvauchers_id,$detalle->monto);
			Flash::valid('Datos Guardados Correctamente');
			return Redirect::toAction('terminar');
        } else {
            Flash::warning('No se Pudieron Guardar los Datos...!!!');
        }
    }
	
	if(Input::hasPost('tesaplicacionletraingresos'))
	{
		Input::post('tesaplicacionletraingresos');
		$ids=$this->ids=Input::post('l');
		$this->MONTOS=Input::post('M');
		$MONTOS=$this->MONTOS;
		$detalleaplicacion=new Tesaplicacionletraingresos();
		$c_ids=count(explode(',',$ids));
		$c=2;
		$detalleaplicacion->guardarApllicacion_letra($ids,$MONTOS,$dets,Session::get('V_ID'));
		$monto='';
		Flash::valid('Datos Guardados Correctamente');
		return Redirect::toAction('aplicacion/'.$fpago.'/'.$app=$c);
        
    }
	$vcs = new Tesvauchers();
	
	$vc=$vcs->find_first((int)Session::get('V_ID'));
	
	
	$this->tesmonedas_id=$vc->tesmonedas_id;
		
	$detalle=new Tesdetallevauchers();
		
	$d=$detalle->find_first('conditions: tesvauchers_id='.Session::get('V_ID'));
	//$this->proveedor=$d->getTesdatos()->razonsocial;
	//$this->tesdatos_id=$d->tesdatos_id;
	$total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID'));
	/*REMPLAZAR CON LA FUNCTION*/
	
	$tota_cargos=$detalle->sum('monto','conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
	$this->total=$total-$tota_cargos;
	
	/*Validar si hay un abono Tipo Aplicacion*/
	if($detalle->getAbonoA(Session::get('V_ID')))
	{
		$this->tessalidas_id=$app;
		$this->tesdetallevauchers=$detalle->findAbonoA(Session::get('V_ID'));
		$this->detalle_abonos=$detalle->find('conditions: tesvauchers_id='.Session::get('V_ID').' AND abono=1');
	}else{
	$this->tesdetallevauchers='';
	}
	/*Listado de las letras por adelanto dependiendo de la moneda del vouchers*/
	if($app==0){
		$mas='';
		switch ($vc->tesmonedas_id)
		{
			case 1: $simbolo='S/. ';$mas=' AND !ISNULL(l.monto )'; break;
			case 2: $simbolo='$. ';$mas=' AND ISNULL( l.monto_n )'; break;
			case 4: $simbolo='S/. ';$mas=' AND !ISNULL( l.monto )'; break;
			case 5: $simbolo='$. ';$mas=' AND ISNULL( l.monto_n )'; break;
			case 0: $simbolo='S/. ';$mas=' AND !ISNULL( l.monto )'; break;
		}
	
	/*Listar por Proveedor*/
	if(Session::get('proveedor_id')!=0)
	{
		$letrasingresos=new Tesletrasingresos();
		$this->adelantos=$letrasingresos->find_all_by_sql('SELECT i.numero as numero, l.* FROM `tesletrasingresos` as l INNER JOIN tesingresos as i ON l.tesingresos_id=i.id AND i.tesdatos_id='.Session::get('proveedor_id').' WHERE ISNULL(l.numerofactura) AND l.activo=1 AND l.estado!=9'.$mas);
		$this->cheques=$vcs->getCheques(Session::get('proveedor_id'));
	/*los cheques*/
	
	}else
	{
		Flash::warning('Para poder Continuar debera seleccionar un Proveedor!!');
		return Redirect::toAction('editar');
	}
  }

}
/*Para aplicar un abono al vaucher de aplicacion que es un cheque*/	
public function ndetalle($detalle_id,$salida_id)
{
	$detalle=new Tesdetallevauchers();
	$det=$detalle->find_first($detalle_id);
	$new_detalle=new Tesdetallevauchers();
	$new_detalle->tesvauchers_id=Session::get('V_ID');
	$new_detalle->tesingresos_id=$det->tesingresos_id;
	$new_detalle->tessalidas_id=$det->tessalidas_id;
	$new_detalle->abono=$det->abono;
	$new_detalle->cargos=$det->cargos;
	$new_detalle->tesdatos_id=$det->tesdatos_id;
	$new_detalle->monto=$det->monto;
	$new_detalle->tescuentascorrientes_id=$det->tescuentascorrientes_id;
	$new_detalle->estado=$det->estado;
	$new_detalle->plancontable=$det->plancontable;
	$new_detalle->correlativo=$det->correlativo+1;
	$new_detalle->noperacion=$det->noperacion+1;
	$new_detalle->save();
	$salidas=new Tessalidas();
	$salidas->find_first($salida_id);
	$salidas->estadosalida='Pagado';
	$salidas->save();
	return Redirect::toAction('grabardetalle');
}


	
/* Grabar detallle para las detraciiones ##CASO ESPECIAL##*/	
public function grabardetalledetraccion()
	{
		$this->salida_id=0;
		if(Input::post('contador')){
			$c=$_POST['contador'];
		if (Input::hasPost('tesdetallevauchers-'.$c))
		{		
			$values=Input::post('tesdetallevauchers-'.$c);
			$detalle=new Tesdetallevauchers(Input::post('tesdetallevauchers-'.$c));
			$detalle->tesvauchers_id=Session::get('V_ID');
			if ($detalle->save()) 
			{
				$DETRACCIONES= new Tesdetracciones();
				$detraccion=$DETRACCIONES->find_first('conditions: tesingresos_id='.$detalle->tesingresos_id);
				$detraccion->monto=$detalle->monto;
				$detraccion->save();
				Flash::valid('El monto de la detracion fue modificada');
				return Redirect::toAction('grabardetalledetraccion');
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		}
		if(Input::post('i')){
		$ids=$this->ids=Input::post('i');
		$ingresos= new Tesingresos();
		$t=0;
		foreach(explode(',',$ids) as $item=>$value):
		 $ingreso=$ingresos->find_first((int)$value);
		 /*Graba la detraccioin en la tabla detraccion*/
		 $detraccion=$ingresos->getDetraccion_id($ingreso->id);
		 $DETRACCIONES= new Tesdetracciones();
		 $DETRACCIONES->tesingresos_id=$detraccion->id;
		 $DETRACCIONES->monto=$detraccion->detraccion;
	 	 $DETRACCIONES->numerodefactura=$detraccion->serie.'-'.$detraccion->numero;
	 	 $DETRACCIONES->codigodedetraccion='';
	 	 $DETRACCIONES->fechadeposito='';
	 	 $DETRACCIONES->estado="1";
	 	 $DETRACCIONES->userid=Auth::get('id');
	 	 $DETRACCIONES->aclempresas_id=Session::get('EMPRESAS_ID');
		 $DETRACCIONES->save();
		 /* ID  de la Detraccion */
		 $detalle=new Tesdetallevauchers();
		 $detalle->tesvauchers_id=Session::get('V_ID');
		 $detalle->tesingresos_id=$ingreso->id;
		 $detalle->tessalidas_id=0;
		 $detalle->abono='0';
		 $detalle->cargos='1';
		 $detalle->tesdatos_id=$ingreso->tesdatos_id;
		 $detalle->monto=$detraccion->detraccion;
		 $detalle->tescuentascorrientes_id=0;
		 $detalle->estado=1;
		 $detalle->plancontable=0;
		 $detalle->save();
		 
		 /* Carga de la EL datalle del vaoucher tipo cargo en Formularios*/
		 $DET= new Tesdetallevauchers();
		 $this->tesdetallevauchers=$DET->find_first((int)$detalle->id);
		 
		 
		 if($ingreso->tesdocumentos_id==13){$t=$t-$detalle->monto;}else{$t=$t+$detalle->monto;}		 
		$ingreso->estadoingreso='Detraccion';
		$ingreso->save();
		endforeach;
		Input::delete();
		$vcs = new Tesvauchers();
		$vc=$vcs->find_first((int)Session::get('V_ID'));
		$vc->importe=$t.'';
		$vc->estado='1';
		$vc->activo='1';
		$vc->save();
		return Redirect::toAction('grabardetalledetraccion');
		}
	
		$vcs = new Tesvauchers();
		
		$vc=$vcs->find_first((int)Session::get('V_ID'));
		$this->vc=$vc;
		if(empty($vc->numero))$this->numero=$vcs->numero(); else $this->numero=$vc->numero;
		if(empty($vc->asiento))$this->asiento=$vcs->asiento(); else $this->asiento=$vc->asiento;
		$detalle=new Tesdetallevauchers();
		$this->detalles=$detalle->find('conditions: tesvauchers_id='.Session::get('V_ID'));
		$empresas = new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		
		/*Chueqes*/
		if($detalle->exists('cargos="0" AND abono="1" AND tessalidas_id!=0 AND tesvauchers_id='.Session::get('V_ID'))){
		$s_d=$detalle->find_first('conditions: cargos="0" AND abono="1" AND tessalidas_id!=0 AND tesvauchers_id='.Session::get('V_ID'));
		$this->salida_id=$s_d->tessalidas_id;
		}
		
		/*Letras*/
		if($detalle->exists('cargos="0" AND abono="1" AND tessalidas_id=0 AND tesingresos_id!=0 AND tesvauchers_id='.Session::get('V_ID'))){
		 $this->salida_id=1;
		}
		
	}
	
/*Detraccion*/

public function detraccion($fpago=10,$salidas_id=0)
{
	$this->tessalidas_id=$salidas_id;
	
	if (Input::hasPost('tesdetallevauchers'))
	{		
		$values=Input::post('tesdetallevauchers');
		$detalle=new Tesdetallevauchers(Input::post('tesdetallevauchers'));
		$detalle->tesvauchers_id=Session::get('V_ID');
		if ($detalle->save()) 
		{
				$detalles=new Tesdetallevauchers();
				$det=$detalles->find_first('conditions: tesvauchers_id='.Session::get('V_ID').' AND cargos="1"');
				$DETRACCIONES= new Tesdetracciones();
				$detraccion=$DETRACCIONES->find_first('conditions: tesingresos_id='.$det->tesingresos_id);
				$detraccion->monto=$detalle->monto;
				$detraccion->codigodedetraccion=$detalle->noperacion;
	 	 		$detraccion->fechadeposito=$values['fecha'];
				$detraccion->update();
			Flash::valid('Datos Guardados Correctamente');
			return Redirect::toAction('terminar');
        } else {
            Flash::warning('No se Pudieron Guardar los Datos...!!!');
        }
    }

	$vcs = new Tesvauchers();
	$vc=$vcs->find_first((int)Session::get('V_ID'));
	$this->tesmonedas_id=$vc->tesmonedas_id;
	$detalle=new Tesdetallevauchers();
	/* Proveedor y bancos */
	$d=$detalle->find_first('conditions: tesvauchers_id='.Session::get('V_ID'));
	$this->proveedor=$d->getTesdatos()->razonsocial;
	$this->tesdatos_id=$d->tesdatos_id;
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID'));
	if($detalle->exists('abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID')))
	{
		$this->tesdetallevauchers=$detalle->find_first('conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
	}
	$formadepagos= new  Voucherformadepagos();
	$this->pago=$formadepagos->find_first((int)$fpago);
	$this->numero=$detalle->ndocumento();
	

	
}
	
/*
EFECTIVO
@fpago = id de la forma de pago 
*/
public function efectivo($fpago=1)
{
	if (Input::hasPost('tesdetallevauchers'))
	{
		$detalle=new Tesdetallevauchers(Input::post('tesdetallevauchers'));
		if ($detalle->save()) 
		{
			/*validar si es el monto de abono es mayor o menor al monto de total de cargos*/
			$D=new Tesdetallevauchers();
			$D->getMenor_Mayor($detalle->tesvauchers_id,$detalle->monto);
			Flash::valid('Datos Guardados Correctamente');
			return Redirect::toAction('terminar');
        } else {
            Flash::warning('No se Pudieron Guardar los Datos...!!!');
        }
    }
	
	$detalle=new Tesdetallevauchers();
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID'))-$detalle->sum('monto','conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
	
	if($detalle->exists('tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID')))
	{
		$this->tesdetallevauchers=$detalle->find_first('conditions: tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
		$this->total=$this->tesdetallevauchers->monto;
	}
	$formadepagos= new  Voucherformadepagos();
	$this->pago=$formadepagos->find_first((int)$fpago);
	$this->numero=$detalle->ndocumento();
}
/*
Cheques
@fpago = id de la forma de pago
*/
public function cheques($fpago=1,$salidas_id=0)
{
	$tesdatos= new Tesdatos();
	$this->tessalidas_id=$salidas_id;
	if (Input::hasPost('salidas'))
	{
		$salidas=new Tessalidas(Input::post('salidas'));
		$salidas->testipocambios_id=Session::get('CAMBIO_ID');
		if ($salidas->save()) 
		{
			$detalle=new Tesdetallevauchers();
			$detalle->tesvauchers_id=Session::get('V_ID');
			$detalle->tesingresos_id='0';
			$detalle->tessalidas_id=$salidas->id;
			$detalle->abono='1';
			$detalle->cargos='0';
			$detalle->tesdatos_id=$salidas->tesdatos_id;
			$detalle->plancontable=$salidas->getTescuentascorrientes()->cuentaplan;
			$detalle->save();
			Flash::valid('Datos Guardados Correctamente');
			return Redirect::toAction('cheques/'.$fpago.'/'.$salidas->id);
        } else {
            Flash::warning('No se Pudieron Guardar los Datos...!!!');
        }
    }
	
	if (Input::hasPost('tesdetallevauchers'))
	{
		$detalle=new Tesdetallevauchers(Input::post('tesdetallevauchers'));
		
		if ($detalle->save()) 
		{
		/*validar si es el monto de abono es mayor o menor al monto de total de cargos*/
		$D=new Tesdetallevauchers();
		$D->getMenor_Mayor($detalle->tesvauchers_id,$detalle->monto);
			Flash::valid('Datos Guardados Correctamente');
			return Redirect::toAction('terminar');
        } else {
            Flash::warning('No se Pudieron Guardar los Datos...!!!');
        }
    }

	$salidas=new Tessalidas();
	$this->S=$salidas;
	$vcs = new Tesvauchers();
	$vc=$vcs->find_first((int)Session::get('V_ID'));
	$this->tesmonedas_id=$vc->tesmonedas_id;
	$detalle=new Tesdetallevauchers();
	/* Proveedor y bancos */
	$d=$detalle->find_first('conditions: tesvauchers_id='.Session::get('V_ID'));
	if($d){
	$this->proveedor=$d->getTesdatos()->razonsocial;
	$this->tesdatos_id=$d->tesdatos_id;
	}else
	{
	$dato=$tesdatos->find_first((int)Session::get('proveedor_id'));
	$this->proveedor=$dato->razonsocial;
	$this->tesdatos_id=$dato->id;		
	}
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID'))-$detalle->sum('monto','conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
	if($detalle->exists('tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID')))
	{
		$this->tesdetallevauchers=$detalle->find_first('conditions: tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
		$this->total=$this->tesdetallevauchers->monto;
	}
	if($salidas_id!=0)
	{
		$this->salida=$salidas->find_first($salidas_id);
	}
}	

/*
#
# Telecrediro, giro, abono CTA corriente
#
#
*/
public function bancos($fpago=1,$salidas_id=0)
{
	$this->tessalidas_id=$salidas_id;
	
	if (Input::hasPost('tesdetallevauchers'))
	{
		$detalle=new Tesdetallevauchers(Input::post('tesdetallevauchers'));
		
		if ($detalle->save()) 
		{
		/*validar si es el monto de abono es mayor o menor al monto de total de cargos*/
		$D=new Tesdetallevauchers();
		/*ravizar*/
		$D->getMenor_Mayor($detalle->tesvauchers_id,$detalle->monto);
			Flash::valid('Datos Guardados Correctamente');
			return Redirect::toAction('terminar');
        } else {
            Flash::warning('No se Pudieron Guardar los Datos...!!!');
        }
    }

	$salidas=new Tessalidas();
	$this->S=$salidas;
	$vcs = new Tesvauchers();
	$vc=$vcs->find_first((int)Session::get('V_ID'));
	$this->tesmonedas_id=$vc->tesmonedas_id;
	$detalle=new Tesdetallevauchers();
	/* Proveedor y bancos */
	$d=$detalle->find_first('conditions: tesvauchers_id='.Session::get('V_ID'));
	$this->proveedor=$d->getTesdatos()->razonsocial;
	$this->tesdatos_id=$d->tesdatos_id;
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID'))-$detalle->sum('monto','conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
	if($detalle->exists('tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID')))
	{
		$this->tesdetallevauchers=$detalle->find_first('conditions: tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
		$this->total=$this->tesdetallevauchers->monto;
	}
	$formadepagos= new  Voucherformadepagos();
	$this->pago=$formadepagos->find_first((int)$fpago);
	$this->numero=$detalle->ndocumento();
	

}	

/*
#
# Letras de pago
# crea ingresos de letras para pagar una factura o mas 
*/
public function letras($fpago=3,$salidas_id=0,$doc=NULL,$NL=0)
{
	$this->nletras=$NL;
	$this->numerofactura=$doc;
	$this->tessalidas_id=$salidas_id;
	$vcs = new Tesvauchers();
	$vc=$vcs->find_first((int)Session::get('V_ID'));
	$this->tesmonedas_id=$vc->tesmonedas_id;
	$detalle=new Tesdetallevauchers();
	/* Proveedor y bancos */
	$d=$detalle->find_first('conditions: tesvauchers_id='.Session::get('V_ID'));
	$this->proveedor=$d->getTesdatos()->razonsocial;
	$this->tesdatos_id=$d->tesdatos_id;
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID'))-$detalle->sum('monto','conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
	if($detalle->existsLetra(Session::get('V_ID')))
	{
		/*VAlidar que solo se tome en cuenta las letras mas no las nc*/
		$this->tesdetallevauchers=$detalle->findLetras(Session::get('V_ID'));
		$this->tessalidas_id=1;
		$this->nletras=$detalle->existsLetra(Session::get('V_ID'));
	}
	$formadepagos= new  Voucherformadepagos();
	$this->pago=$formadepagos->find_first((int)$fpago);
	
}

public function ingresoletras($c)
{
	$this->vale='hola'.$c;
	$detalle=new Tesdetallevauchers();
	/* Proveedor y bancos */
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID'));
	if (Input::hasPost('ingresos-'.$c))
	{
		$value=Input::post('ingresos-'.$c);
		$this->vale.='ingresos';
		$ingresos=new Tesingresos(Input::post('ingresos-'.$c));
		$ingresos->testipocambios_id=$ingresos->getCambio($value['femision']);
		$ingresos->femision=$value['femision'];
		$ingresos->cuentaporpagar='42301';
		$ingresos->pr='NN';
		$ingresos->igv='0';
		$ingresos->fregistro=date('Y-m-d');
		$ingresos->estadoingreso='Pendiente';
		$ingresos->aclempresas_id=Session::get('EMPRESAS_ID');
		if ($ingresos->save()) 
		{
			 $LETRAS= new Tesletrasingresos();
			 if(!$LETRAS->exists('tesingresos_id='.$ingresos->id)){
			 $LETRAS->tesingresos_id=$ingresos->id;
			 $LETRAS->numeroletra=$ingresos->numero;
			 $LETRAS->numerounico='';
			 $LETRAS->monto=$ingresos->totalconigv;
			 $LETRAS->numerofactura=$ingresos->numerofactura;
			 $LETRAS->estadodeletra='';
			 $LETRAS->estado=1;
			 $LETRAS->userid=Auth::get('id');
			 $LETRAS->aclempresas_id=Session::get('EMPRESAS_ID');
			 $LETRAS->save();
			 }
			$detalle=new Tesdetallevauchers();
			$detalle->tesvauchers_id=Session::get('V_ID');
			$detalle->tesingresos_id=$ingresos->id;
			$detalle->tessalidas_id=0;
			$detalle->monto=$ingresos->totalconigv;
			$detalle->abono='1';
			$detalle->cargos='0';
			$detalle->tesdatos_id=$ingresos->tesdatos_id;
			$detalle->plancontable='42301';
			$detalle->save();
			$ingresos=new Tesingresos();
			$this->ingreso=$ingresos->find_first($detalle->tesingresos_id);
			$this->tesdetallevauchers=$detalle->find_first($detalle->id);
			$this->vale.=' grabo';
        } 
  	 }
	 
	if (Input::hasPost('tesdetallevauchers-'.$c))
	{
		$this->vale.=' detalle ';
		$detalle=new Tesdetallevauchers(Input::post('tesdetallevauchers-'.$c));
		
		if ($detalle->save()) 
		{
			$this->tesdetallevauchers=$detalle->find_first($detalle->id);
			$ingresos=new Tesingresos();
			$this->ingreso=$ingresos->find_first($detalle->tesingresos_id);
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
# Cuotas de leassing y de prestamos
# crea ingresos de letras para pagar una factura o mas 
*/
public function cuotas($fpago=3,$salidas_id=0,$doc=NULL,$NL=0)
{
	$this->nletras=$NL;
	$this->tesdocumentos_id=$doc;
	$this->tessalidas_id=$salidas_id;
	$vcs = new Tesvauchers();
	$vc=$vcs->find_first((int)Session::get('V_ID'));
	$this->tesmonedas_id=$vc->tesmonedas_id;
	$detalle=new Tesdetallevauchers();
	/* Proveedor y bancos */
	$d=$detalle->find_first('conditions: tesvauchers_id='.Session::get('V_ID'));
	$this->proveedor=$d->getTesdatos()->razonsocial;
	$this->tesdatos_id=$d->tesdatos_id;
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID'))-$detalle->sum('monto','conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
	if($detalle->exists('abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID')))
	{
		$this->tesdetallevauchers=$detalle->find('conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
		$this->tessalidas_id=1;
		$this->nletras=$detalle->count('conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID'));
	}
	$formadepagos= new  Voucherformadepagos();
	$this->pago=$formadepagos->find_first((int)$fpago);
	
}

public function ingresocuotas($c)
{
	$this->vale='hola'.$c;
	$detalle=new Tesdetallevauchers();
	/* Proveedor y bancos */
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID'));
	if (Input::hasPost('ingresos-'.$c))
	{
		$value=Input::post('ingresos-'.$c);
		$this->vale.='ingresos';
		$ingresos=new Tesingresos(Input::post('ingresos-'.$c));
		$ingresos->testipocambios_id=$ingresos->getCambio($value['femision']);
		$ingresos->pr='NN';
		$ingresos->igv='0';
		$ingresos->fregistro=date('Y-m-d');
		$ingresos->estadoingreso='Pendiente';
		$ingresos->aclempresas_id=Session::get('EMPRESAS_ID');
		$ingresos->save();
			$detalle=new Tesdetallevauchers();
			$detalle->tesvauchers_id=Session::get('V_ID');
			$detalle->tesingresos_id=$ingresos->id;
			$detalle->tessalidas_id=0;
			$detalle->monto=$ingresos->totalconigv;
			$detalle->abono='1';
			$detalle->cargos='0';
			$detalle->tesdatos_id=$ingresos->tesdatos_id;
			$detalle->plancontable=$ingresos->cuentaporpagar;
			$detalle->save();
			$ingresos=new Tesingresos();
			$this->ingreso=$ingresos->find_first($detalle->tesingresos_id);
			$this->tesdetallevauchers=$detalle->find_first($detalle->id);
			$this->vale.=' grabo';
     
  	 }
	 
	if (Input::hasPost('tesdetallevauchers-'.$c))
	{
		$this->vale.=' detalle ';
		$detalle=new Tesdetallevauchers(Input::post('tesdetallevauchers-'.$c));
		
		if ($detalle->save()) 
		{
			$this->tesdetallevauchers=$detalle->find_first($detalle->id);
			$ingresos=new Tesingresos();
			$this->ingreso=$ingresos->find_first($detalle->tesingresos_id);
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
	$vcs = new Tesvauchers();
	$vc=$vcs->find_first((int)Session::get('V_ID'));
	$this->vc=$vc;
		if(empty($vc->numero))$this->numero=$vcs->numero(); else $this->numero=$vc->numero;
		if(empty($vc->asiento))$this->asiento=$vcs->asiento(); else $this->asiento=$vc->asiento;
	$detalle=new Tesdetallevauchers();
	$this->detalles=$detalle->find('conditions: tesvauchers_id='.Session::get('V_ID'));
	$empresas = new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
}
public function finalizar($id=0)
{
	$this->LI= new tesletrasingresos();
	View::select('terminar');
	$detalles=new Tesdetallevauchers();
	/*Monto del Vouchers*/
	$monto_total=$detalles->sum('monto','conditions: tesvauchers_id='.Session::get('V_ID').' AND abono="1"');
	/*if($detalle->getAbonoNC(Session::get('V_ID')))
	{*/
	 $total_nc=$detalles->findAbonoNC(Session::get('V_ID'));
		$monto_total=$monto_total-$total_nc;
	/*}*/
	$vcs = new Tesvauchers();
	$vc=$vcs->find_first((int)Session::get('V_ID'));
	if($id!=0){
	if(empty($vc->numero))$vc->numero=$vcs->numero();
	if(empty($vc->asiento))$vc->asiento=$vcs->asiento();
	//$vc->fecha=date('Y-m-d');
	$vc->importe=$monto_total;
	if($vc->voucherformadepagos_id==10)$vc->tesmonedas_id=1;
	$vc->estadov='Terminado';
	$mes=explode("-",$vc->fecha);
	$vc->mes=$mes[1];
	$vc->estado='1';
	$vc->activo='1';
	if(empty($vc->total))
	{
		$vc->total=$monto_total;
		
		if(!empty($vc->tesingresos_ingreso)){
			$detalles=new Tesdetallevauchers();
		}
	}
	$vc->save();
	}
	$this->numero=$vc->numero;
	$this->asiento=$vc->asiento;
	$this->detalles=$detalles->find('conditions: tesvauchers_id='.Session::get('V_ID'));
	$empresas = new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	
	$this->vc=$vc;
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
	$detalles=new Tesdetallevauchers();
	$detalle=$detalles->find_first((int)$id);
	if($detalle)
	{
		if($detalle->cargos!=0)
		{
			if($detalle->tesingresos_id!=0)
			{
				$Ingresos=new Tesingresos();
				$ingreso=$Ingresos->find_first((int)$detalle->tesingresos_id);
				if($ingreso->estadoingreso=='PAGADO'){$ingreso->estadoingreso='Pendiente';}else{}
				$ingreso->save();
				/*$DETRACCIONES= new Tesdetracciones();
				if($DETRACCIONES->exists('tesingresos_id='.$ingreso->id))
				{
					$DETRACCIONES->delete('tesingresos_id='.$ingreso->id);
				}*/
				/*LETRAS INGRESO DE ADELANTO*/
				$tesaplicacionletraingresos = new Tesaplicacionletraingresos();
				$letra_ingreso= new Tesletrasingresos();
				$tesaplicacionletraingresos->delete('tesdetallevauchers_id='.$detalle->id);
				if($letra_ingreso->exists('id='.$detalle->tesingresos_id))
				{
					$letra=$letra_ingreso->find_first($detalle->tesingresos_id);
					if($tesaplicacionletraingresos->exists('tesletrasingresos_id='.$detalle->tesingresos_id.' AND tesvauchers_id='.$detalle->tesvauchers_id.' AND tesdetallevauchers_id='.$detalle->id))
					{
						if($tesaplicacionletraingresos->delete('tesletrasingresos_id='.$detalle->tesingresos_id.' AND tesvauchers_id='.$detalle->tesvauchers_id.' AND tesdetallevauchers_id='.$detalle->id))
						{
							Flash::valid('Aplicacion Eliminada');
						}else
					{
						Flash::valid('Aplicacion No fue eliminada');
					}
				   
				 }
			   }
			}
			if($detalle->tessalidas_id!=0)
			{
				$Salidas=new Tessalidas();
				$Salidas->find((int)$detalle->tessalidas_id);
				$Salidas->estado='3';
				$Salidas->save();
				//$Salidas->delete((int)$detalle->tessalidas_id);
			}
			$detalles->delete((int)$id);
			return Redirect::toAction('grabardetalle');
		}
		
		if($detalle->abono!=0)
		{
			if($detalle->tesingresos_id!=0)
			{
				if($detalle->getTesingresos()->tesdocumentos_id=='13'){
				$Ingresos=new Tesingresos();
				$ingreso=$Ingresos->find_first((int)$detalle->tesingresos_id);
				if($ingreso->estadoingreso=='PAGADO'){$ingreso->estadoingreso='Pendiente';}else{}
				$ingreso->save();
				
				}else{
					
				$Ingresos=new Tesingresos();
				$Ingresos->find((int)$detalle->tesingresos_id);
				$Ingresos->estado='3';
				$Ingresos->save();
				//$Ingresos->delete((int)$detalle->tesingresos_id);
				}
			}
			if($detalle->tessalidas_id!=0)
			{
				$Salidas=new Tessalidas();
				//$Salidas->delete((int)$detalle->tessalidas_id);
				$s=$Salidas->find((int)$detalle->tessalidas_id);
				if($s->tesdocumentos_id==35)
				{
				$Salidas->estadosalida="Pendiente";
				$Salidas->estado='1';	
				}else{
				$Salidas->estado='3';
				}
				$Salidas->save();
			}
			$detalles->delete((int)$id);
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
	$id=Session::get('V_ID');
	$vauchers = new Tesvauchers();
    $vc=$vauchers->find_first($id);
    $vc->estadov='ANULADO';
	$vc->save();
	$detalles=new Tesdetallevauchers();
	$DETALLES=$detalles->find('conditions: tesvauchers_id='.(int)$vc->id);
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
				$LETRAS=new Tesletrasingresos();
				if($LETRAS->exists("tesingresos_id=".$detalle->tesingresos_id))
				{
					$LETRAS->find_first('conditions: tesingresos_id='.$detalle->tesingresos_id);
					$LETRAS->estadoletra='Pendiente';
					$LETRAS->update();
				}
			}
			if($detalle->tessalidas_id!=0)
			{
				$Salidas=new Tessalidas();
				$salida=$Salidas->find_first((int)$detalle->tessalidas_id);
				$salida->estadosalida='ANULADO';
				$salida->save();
			}
			$detalle->estado=0;
			$detalle->save();
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
				}else{
				$Ingresos=new Tesingresos();
				$ingreso=$Ingresos->find_first((int)$detalle->tesingresos_id);
				$ingreso->estadoingreso='ANULADO';
				$ingreso->save();
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
			}
			$tesdetracciones= new Tesdetracciones();
			$tesdetracciones->delete('monto="'.$detalle->monto.'" AND tesingresos_id='.$vauchers->getIngresoDetraccion($vc->id));
			//return Redirect::toAction('grabardetalle');
		}
		endforeach;
		Flash::warning('Vauchers Anulado');
		return Redirect::toAction('');
	}else
	{
		Flash::warning('No existe nigun registro con este valor '.$id);
		return Redirect::toAction('');
	}
}
public function eliminar($id) {
        try {
            $menu = new Tesvauchers();
            $menu->find_first($id);
            if ($menu->delete()) {
                Flash::valid("Vouchers <b>{$menu->id}</b> fué Eliminado...!!!");
                Aclauditorias::add("Eliminó Vouchers {$menu->numero} del sistema", 'menu');
            } else {
                Flash::warning("No se Pudo Eliminar el Menu <b>{$menu->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::redirect();
    }

/* funciones para varios procesos*/

public function cuentacorriente($id=0)
{
	$this->id=$id;
}
/* recibe el id de la cuenta corriente*/
public function numerocheque($id=0)
{
	$salidas=new Tessalidas();
	$this->id=$salidas->generarNumerocheque($id);
}
public function plancontable($id=0)
{
	View::select('numerocheque');
	$plan=Load::model('tescuentascorrientes');
	$p=$plan->find_first((int)$id);
	$this->id=$p->cuentaplan;
}

public function reparar_v()
{
	$valores='';
	$detalles=new Tesdetallevauchers();
	$apps=new Tesaplicacionletraingresos();
	$sql="SELECT d.*,(SELECT s.tesingresos_id FROM `tesdetallevauchers` s WHERE s.tesvauchers_id=v.id AND s.abono=1 AND s.tesingresos_id!=0 AND !isnull(s.tesingresos_id) group by s.tesvauchers_id) as id_ingreso FROM tesdetallevauchers as d,tesvauchers as v WHERE v.id=d.tesvauchers_id AND v.voucherformadepagos_id=11 AND d.cargos=1";
	$dets=$detalles->find_all_by_sql($sql);	
	foreach($dets as $de):
	if($apps->exists("tesingresos_id=".$de->tesingresos_id." AND tesvauchers_id=".$de->tesvauchers_id))
	{
		/*$ap=$apps->find_first("conditions: tesingresos_id=".$de->tesingresos_id." AND tesvauchers_id=".$de->tesvauchers_id);
		$ap->tesdetallevauchers_id=$de->id;
		$ap->save();*/
	}else
	{
		$ap=new Tesaplicacionletraingresos();
		$ap->tesletrasingresos_id=$apps->getLetra_ingresos($de->id_ingreso)->id;
		$ap->tesingresos_id=$de->tesingresos_id;
		$ap->numerodefactura=$de->getTesingresos()->serie.'-'.$de->getTesingresos()->numero;
		$ap->monto=$de->monto;
		$ap->estado='1';
		$ap->userid=0;
		$ap->aclempresas_id=1;
		$ap->tesvauchers_id=$de->tesvauchers_id;
		$ap->tesdetallevauchers_id=$de->id;
		$ap->save();
		$valores.=$de->id.', '.$de->tesingresos_id.', '.$de->tesvauchers_id.', '.$de->getTesingresos()->serie.'-'.$de->getTesingresos()->numero.', '.$de->monto.', L:'.$apps->getLetra_ingresos($de->id_ingreso)->id.', I:'.$de->id_ingreso.'<br />';
	}
	endforeach;	
	$this->valores=$valores;
}

}
?>