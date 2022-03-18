<?php
/*LETRAS INGRESOS*/

class LetrasController extends AdminController
{
	
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
/*
#
# Listado de letras Sin Registrar 
# Listado de letras Registradas ordenadas por fecha de vencimiento
*/
public function index()
{
	$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)34);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$ingresos= new Tesingresos();
	$letras= new Tesletrasingresos();
	$campos = 'tesletrasingresos.' . join(',tesletrasingresos.', $letras->fields);
	$join = 'INNER JOIN tesingresos as t ON t.id = tesletrasingresos.tesingresos_id AND t.aclempresas_id='.Session::get('EMPRESAS_ID');
	$this->letras=$letras->find('conditions: tesletrasingresos.estadodeletra!="PAGADO" AND !ISNULL(tesletrasingresos.numerounico) AND t.estadoingreso!="PAGADO"',"join: $join", "columns: $campos",'order: t.fvencimiento');
	$this->ingresos=$letras->find('conditions: ISNULL(tesletrasingresos.numerounico) AND estadoingreso!="ANULADO"',"join: $join", "columns: $campos",'order: t.fvencimiento');
}

public function ingresarnumero($id=0)
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrasingresos();
	
	$bancos= new Tesbancos();
	$this->bancos=$bancos->find('conditions: aclempresas_id=1');
	
	if($letras->exists('id='.$id.' AND aclempresas_id='.Session::get('EMPRESAS_ID').''))
	{
		$this->letras=$letras->find_first($id);
		if(Input::post('letras'))
		{
			$le= new Tesletrasingresos(Input::post('letras'));
			$le->estadodeletra='Por Pagar';
			if($le->save())
			{
				$ingresos= new Tesingresos();
				$ing=$ingresos->find_first((int)$le->tesingresos_id);
				$ing->estadoingreso='Registrado';
				$ing->save();
				Flash::valid('La letra fue registrada');
				return Redirect::redirect('');
			}
		}
	
	}else
	{
		Flash::warning('Esta letra tiene Problemas porfavor consultar con el administrador');
		return Redirect::redirect('');
		
	}
	
}

public function letrasporfecha()
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrasingresos();
	$this->letras=$letras->reporteFechas();
}
public function totalporsemana()
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrasingresos();
	$this->letras=$letras->getTotalporsemana();
}
public function obligacionesporfecha($todo=0)
{
	$this->todo=0;
	if($todo==0)$this->todo=1;
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesingresos();
	$this->obligaciones=$letras->reporteFechas($todo);
}

public function obligacionespendientes()
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesingresos();
	$this->obligaciones=$letras->reporteFechas(1);
}
public function modificar_estado($id)
{
	$ingresos= new Tesingresos();
	$ingresos->find_first($id);
	$ingresos->estadoingreso="PAGADO";
	$ingresos->save();
	return Redirect::toAction("obligacionesporfecha");
	
}
public function cargar($id,$url='editar')
	{
		$vcs = new Tesvauchers();
		$vc=$vcs->find_first((int)$id);
		Session::set('V_ID_O',$vc->id);
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
				$vc->obligacion='1';
				$vc->aclempresas_id=Session::get('EMPRESAS_ID');
                if ($vc->save()) {
					
					$proveedor=$_POST['proveedor'];
					$this->p=$proveedor;
					Session::set('V_ID_O',$vc->id);
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
		$vc=$vcs->find_first((int)Session::get('V_ID_O'));
		if (Input::hasPost('tesvauchers')) {
				if ($vc->update(Input::post('tesvauchers'))) {
					
					$proveedor=$_POST['proveedor'];
					Session::set('V_ID_O',$vc->id);
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
		if(Session::has('V_ID_O'))
		{
			$vcs = new Tesvauchers();
			$vc=$vcs->find_first((int)Session::get('V_ID_O'));
			$detraccion=$vc->voucherformadepagos_id;
			$fecha = date('Y-m-d');
			$nuevafecha = strtotime ( '+25 day' , strtotime ( $fecha ) ) ;
			$nuevafecha = date ( 'Y-m-d' , $nuevafecha ); //AND i.fvencimiento<="'.$nuevafecha.'"
			$this->vc=$vc;
			$conditions='(i.tesdocumentos_id=34 OR i.tesdocumentos_id=37 OR i.tesdocumentos_id=38 OR i.tesdocumentos_id=39 OR i.tesdocumentos_id=40 ) AND (i.estadoingreso!="PAGADO") AND (i.estadoingreso!="ANULADO") AND i.aclempresas_id='.Session::get('EMPRESAS_ID').'';
		 	if(Session::get('proveedor_id')!=0)
			{
			  $conditions.=' AND i.tesdatos_id='.Session::get('proveedor_id');
			  $tesdatos= new Tesdatos();
			  $this->tesdatos=$tesdatos->find_first((int)Session::get('proveedor_id'));
			}
			$conditions.=' AND i.tesmonedas_id='.$vc->tesmonedas_id;
			$ingresos= new Tesingresos();
			
			$this->ingresos=$ingresos->getPendientesLetrasIngresos($conditions);
		}
	}
	
public function grabardetalle()
	{
		$DETRACCIONES= new Tesdetracciones();
		$this->salida_id=0;
		if(Input::post('i')){
		$ids=$this->ids=Input::post('i');
		$ingresos= new Tesingresos();
		$t=0;
		foreach(explode(',',$ids) as $item=>$value):
		$ingreso=$ingresos->find_first((int)$value);
		 $detalle=new Tesdetallevauchers();
		 $detalle->tesvauchers_id=Session::get('V_ID_O');
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
		 $detalle->monto=number_format($ingreso->totalconigv,2,'.','');
		 $detalle->tescuentascorrientes_id=0;
		 $detalle->estado=1;
		 $detalle->plancontable=0;
		 $detalle->save();
		 $ingresos->estadoingreso='PAGADO';
		 $ingresos->save();
		endforeach;
		Input::delete();
		$vcs = new Tesvauchers();
		$vc=$vcs->find_first((int)Session::get('V_ID_O'));
		$vc->importe=$t.'';
		$vc->estado='1';
		$vc->activo='1';
		$vc->save();
		return Redirect::toAction('grabardetalle');
		}
	
		$vcs = new Tesvauchers();
		
		$vc=$vcs->find_first((int)Session::get('V_ID_O'));
		$this->vc=$vc;
		if(empty($vc->numero))$this->numero=$vcs->numero(); else $this->numero=$vc->numero;
		if(empty($vc->asiento))$this->asiento=$vcs->asiento(); else $this->asiento=$vc->asiento;
		$detalle=new Tesdetallevauchers();
		$this->detalles=$detalle->find('conditions: tesvauchers_id='.Session::get('V_ID_O'));
		$empresas = new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		
		/*Chueqes*/
		if($detalle->exists('cargos="0" AND abono="1" AND tessalidas_id!=0 AND tesvauchers_id='.Session::get('V_ID_O'))){
		$s_d=$detalle->find_first('conditions: cargos="0" AND abono="1" AND tessalidas_id!=0 AND tesvauchers_id='.Session::get('V_ID_O'));
		$this->salida_id=$s_d->tessalidas_id;
		}
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
			return Redirect::toAction('grabardetalle');
        }
    }
	
	$detalle=new Tesdetallevauchers();
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID_O'))-$detalle->sum('monto','conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID_O'));
	if($detalle->exists('tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID_O')))
	{
		$this->tesdetallevauchers=$detalle->find_first('conditions: tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID_O'));
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
	$this->tessalidas_id=$salidas_id;
	
	if (Input::hasPost('salidas'))
	{
		$salidas=new Tessalidas(Input::post('salidas'));
		$salidas->testipocambios_id=Session::get('CAMBIO_ID');
		if ($salidas->save()) 
		{
			$detalle=new Tesdetallevauchers();
			$detalle->tesvauchers_id=Session::get('V_ID_O');
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
	$vc=$vcs->find_first((int)Session::get('V_ID_O'));
	$this->tesmonedas_id=$vc->tesmonedas_id;
	$detalle=new Tesdetallevauchers();
	/* Proveedor y bancos */
	$d=$detalle->find_first('conditions: tesvauchers_id='.Session::get('V_ID_O'));
	$this->proveedor=$d->getTesdatos()->razonsocial;
	$this->tesdatos_id=$d->tesdatos_id;
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID_O'))-$detalle->sum('monto','conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID_O'));
	if($detalle->exists('tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID_O')))
	{
		$this->tesdetallevauchers=$detalle->find_first('conditions: tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID_O'));
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
	$vc=$vcs->find_first((int)Session::get('V_ID_O'));
	$this->tesmonedas_id=$vc->tesmonedas_id;
	$detalle=new Tesdetallevauchers();
	/* Proveedor y bancos */
	$d=$detalle->find_first('conditions: tesvauchers_id='.Session::get('V_ID_O'));
	$this->proveedor=$d->getTesdatos()->razonsocial;
	$this->tesdatos_id=$d->tesdatos_id;
	$this->total=$detalle->sum('monto','conditions: abono=0 AND cargos=1 AND tesvauchers_id='.Session::get('V_ID_O'))-$detalle->sum('monto','conditions: abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID_O'));
	if($detalle->exists('tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID_O')))
	{
		$this->tesdetallevauchers=$detalle->find_first('conditions: tesingresos_id=0 AND abono=1 AND cargos=0 AND tesvauchers_id='.Session::get('V_ID_O'));
		$this->total=$this->tesdetallevauchers->monto;
	}
	$formadepagos= new  Voucherformadepagos();
	$this->pago=$formadepagos->find_first((int)$fpago);
	$this->numero=$detalle->ndocumento();
	

}
public function terminar()
{
	$this->LI= new tesletrasingresos();
	$vcs = new Tesvauchers();
	$vc=$vcs->find_first((int)Session::get('V_ID_O'));
	$this->vc=$vc;
		if(empty($vc->numero))$this->numero=$vcs->numero(); else $this->numero=$vc->numero;
		if(empty($vc->asiento))$this->asiento=$vcs->asiento(); else $this->asiento=$vc->asiento;
	$detalle=new Tesdetallevauchers();
	$this->detalles=$detalle->find('conditions: tesvauchers_id='.Session::get('V_ID_O'));
	$empresas = new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
}
public function finalizar()
{
	$this->LI= new tesletrasingresos();
	View::select('terminar');
	$detalle=new Tesdetallevauchers();
	/*Monto del Vouchers*/
	$monto_total=$detalle->sum('monto','conditions: tesvauchers_id='.Session::get('V_ID_O').' AND abono="1"');
	$vcs = new Tesvauchers();
	$vc=$vcs->find_first((int)Session::get('V_ID_O'));
	if(empty($vc->numero))$vc->numero=$vcs->numero();
	if(empty($vc->asiento))$vc->asiento=$vcs->asiento();
	$vc->fecha=date('Y-m-d');
	$vc->importe=$monto_total;
	$vc->estadov='Terminado';
	$vc->mes=date('m');
	$vc->save();
	$this->numero=$vc->numero;
	$this->asiento=$vc->asiento;
	$this->detalles=$detalle->find('conditions: tesvauchers_id='.Session::get('V_ID_O'));
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
				
			}
			if($detalle->tessalidas_id!=0)
			{
				$Salidas=new Tessalidas();
				$Salidas->delete((int)$detalle->tessalidas_id);
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
				$ingreso->save();}else{
				$Ingresos=new Tesingresos();
				$Ingresos->delete((int)$detalle->tesingresos_id);
				}
			}
			if($detalle->tessalidas_id!=0)
			{
				$Salidas=new Tessalidas();
				$Salidas->delete((int)$detalle->tessalidas_id);
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

/* REPORTES */ 
public function letrasrecibidas($Y='',$m=''){
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
	 $letras= new Tesletrasingresos();
	 $empresas= new Aclempresas();
	 $this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	 $this->letras=$letras->getEmitidas($anio,$mes_activo);
 }
/*PARA REPRAR LAS LETRAS QUE YA ESTAN EN EL DETALLE VAUCHERS*/
public function actualizar_letras($clave='')
{
	if($clave==43408841){	
	$vauchers= new Tesdetallevauchers();
	$letras= new Tesletrasingresos();
	$vs=$vauchers->find_all_by_sql("SELECT l.id as letra_id FROM tesdetallevauchers as d,tesletrasingresos as l
WHERE d.tesingresos_id=l.tesingresos_id AND d.cargos=1 AND l.monto=d.monto AND l.aclempresas_id=".Session::get('EMPRESAS_ID')." GROUP BY l.id");
	foreach($vs as $v):
	$letras= new Tesletrasingresos();
	$l=$letras->find_first($v->letra_id);
	$l->estadodeletra='PAGADO';
	$l->save();
	$ingresos=new Tesingresos();
	$ingresos->find_first($l->tesingresos_id);
	$ingresos->estadoingreso='PAGADO';
	$ingresos->save();
	endforeach;
	}
	return Redirect::redirect("/admin/reparar/");
}


public function letras_datos()
{
	$letras= new Tesletrasingresos();
}


}

?>