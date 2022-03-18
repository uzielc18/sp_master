<?php 
/*LETRAS SALIDAS*/

class LetrassalidasController extends AdminController
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
public function index($c='',$d='')
{
	$campo=base64_decode($c);
	$direccion=base64_decode($d);
	if($c=='')
	{
		$campo='t.fvencimiento';
		$direccion='ASC';
	}
	
	$documentos= new Tesdocumentos();
	$doc= $documentos->find_first((int)34);
	Session::set('DOC_ID',$doc->id);
	Session::set('DOC_NOMBRE',$doc->nombre);
	Session::set('DOC_ABR',$doc->abr);
	Session::set('DOC_CODIGO',$doc->codigo);
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$ingresos= new Tessalidas();
	$letras= new Tesletrassalidas();
	$campos = 'tesletrassalidas.' . join(',tesletrassalidas.', $letras->fields);
	$join = 'INNER JOIN tessalidas as t ON t.id = tesletrassalidas.tessalidas_id AND t.aclempresas_id='.Session::get('EMPRESAS_ID');
	//!ISNULL(tesletrassalidas.numerounico) ISNULL(tesletrassalidas.numerounico) AND
	//$this->letras=$letras->find('conditions: !ISNULL(tesletrassalidas.numerounico)',"join: $join", "columns: $campos",'order: t.fvencimiento');
	$this->salidas=$letras->find('conditions: ISNULL(tesletrassalidas.numerounico) AND (tesletrassalidas.estadoletras="Sin Numero" OR tesletrassalidas.estadoletras="Editable") AND tesletrassalidas.estadoletras!="PAGADO" AND estadosalida!="ANULADO"',"join: $join", "columns: $campos",'order: '.$campo.' '.$direccion);/* t.fvencimiento, */
	$this->titulo="Letras Emitidas";
}

public function aceptadas($c='',$d='')
{
	View::select('index');
	$campo=base64_decode($c);
	$direccion=base64_decode($d);
	if($c=='')
	{
		$campo='t.fvencimiento';
		$direccion='ASC';
	}
	
	$documentos= new Tesdocumentos();
	$doc= $documentos->find_first((int)34);
	Session::set('DOC_ID',$doc->id);
	Session::set('DOC_NOMBRE',$doc->nombre);
	Session::set('DOC_ABR',$doc->abr);
	Session::set('DOC_CODIGO',$doc->codigo);
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrassalidas();
	
	$campos = 'tesletrassalidas.' . join(',tesletrassalidas.', $letras->fields);
	$join = 'INNER JOIN tessalidas as t ON t.id = tesletrassalidas.tessalidas_id AND t.aclempresas_id='.Session::get('EMPRESAS_ID');
	
	$this->salidas=$letras->find('conditions: ISNULL(tesletrassalidas.numerounico) AND tesletrassalidas.estadoletras="ACEPTADO" AND estadosalida!="ANULADO"',"join: $join", "columns: $campos",'order: '.$campo.' '.$direccion);/* t.fvencimiento, */
	//$this->salidas=$letras->find('conditions: estadoletras="ACEPTADO"','order: numerodeletra');
	
	$this->titulo="Letras Aceptadas";
}

public function crear()
{
	$SALD=new Tessalidas();
	$this->salida['serie']='LTR';
	$this->salida['numero']=$SALD->generarNumeroLetras();
	$this->salida['npedido']=$SALD->getNumeropedido('LA','LTR');
	$letras= new Tesletrassalidas();
	$salidas= new Tessalidas();
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	if (Input::hasPost('salida')) {
            if(Input::post('salida'))
		{
			$salidas= new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id=34;
			$salidas->estadosalida='Editable';
			$salidas->estado='1';
			/*Cuenta cuentagastos cuentaporpagar*/
			$salidas->cuentaporcobrar='12321';
			$salidas->userid=Auth::get('id');
			$salidas->activo='1';
			$salidas->testipocambios_id=Session::get('CAMBIO_ID');
			$salidas->userid=Auth::get('id');
			$salidas->aclusuarios_id=Auth::get('id');
			$salidas->aclempresas_id=Session::get("EMPRESAS_ID");
			if($salidas->save())
			{
				$le= new Tesletrassalidas();
				$le->tessalidas_id=$salidas->id;
				$le->numerodecuenta='12321';
				$le->numerodeletra=$salidas->numero;
				$le->monto=$salidas->totalconigv;
				$le->estado='1';
				$le->userid=Auth::get('id');
				$le->estadoletras='Editable';
				$le->aclempresas_id=Session::get('EMPRESAS_ID');
				$le->save();
				Flash::valid('La letra fue creada');
				return Redirect::toAction('');
			}
		}
      }
}
public function editar($id)
{
	$letras= new Tesletrassalidas();
	$salidas= new Tessalidas();
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	if (Input::hasPost('letras')) {
            if(Input::post('letras'))
		{
			$le= new Tesletrassalidas(Input::post('letras'));
			if($le->save())
			{
				$salida= new Tessalidas(Input::post('salidas'));
				$salida->numero=$le->numerodeletra;
				$salida->totalconigv=$le->monto;
				$salida->save();
				Flash::valid('La letra fue Modificada');
				return Redirect::toAction('');
			}
		}
      }

        //Aplicando la autocarga de objeto, para comenzar la edición
        $this->letras= $letras->find_first((int)$id);
		$this->salidas=$salidas->find_first((int)$this->letras->tessalidas_id);
}

public function editar_interna($id)
{
	$letras= new Tesletrassalidasinternas();
	$salidas= new Tessalidasinternas();
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	if (Input::hasPost('letras')) {
            if(Input::post('letras'))
		{
			$le= new Tesletrassalidasinternas(Input::post('letras'));
			if($le->save())
			{
				$salida= new Tessalidasinternas(Input::post('salidas'));
				$salida->numero=$le->numerodeletra;
				$salida->total=$le->monto;
				$salida->save();
				Flash::valid('La letra fue Modificada');
				return Redirect::toAction('internas');
			}
		}
      }

        //Aplicando la autocarga de objeto, para comenzar la edición
        $this->letras= $letras->find_first((int)$id);
		$this->salidas=$salidas->find_first((int)$this->letras->tessalidasinternas_id);
}


public function ingresarnumero($id=0)
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrassalidas();
	$bancos= new Tesbancos();
	$this->bancos=$bancos->find('conditions: aclempresas_id=1');
	if($letras->exists('id='.$id.' AND aclempresas_id='.Session::get('EMPRESAS_ID').''))
	{
		$this->letras=$letras->find_first($id);
		if(Input::post('letras'))
		{
			$le= new Tesletrassalidas(Input::post('letras'));
			$le->estadoletras='Registrado';
			if($le->save())
			{
				$ingresos= new Tessalidas();
				$ing=$ingresos->find_first((int)$le->tessalidas_id);
				$ing->movimiento="SALIDA";
				$ing->estadosalida='Registrado';
				$ing->save();
				Flash::valid('La letra fue registrada');
				return Redirect::toAction('letrasbancos');
			}
		}
	
	}else
	{
		Flash::warning('Esta letra tiene Problemas porfavor consultar con el administrador');
		return Redirect::toAction('letrasbancos');
		
	}
	
}
/*funcion para Protestar */
public function protestar($id=0)
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrassalidas();
	if($letras->exists('id='.$id.' AND aclempresas_id='.Session::get('EMPRESAS_ID').''))
	{
		$this->letras=$letras->find_first((int)$id);
		$les= new Tesletrassalidas();
		$les->find_first((int)$id);
		$les->estadoletras='PAGADO';
		if($les->save())
		{
			$salidas= new Tessalidas();
			$salidas->find_first((int)$les->tessalidas_id);
			$salidas->estadosalida='PAGADO';
			$salidas->save();
			Flash::info('La letra fue Protestada');
			$salida=$salidas->find_first((int)$les->tessalidas_id);
				/*genera la nueva salida pero*/
				$new_salida=new Tessalidas();
				$new_salida->aclusuarios_id	=$salida->aclusuarios_id;
				$new_salida->tesmonedas_id=$salida->tesmonedas_id;
				$new_salida->tesdatos_id=$salida->tesdatos_id;
				$new_salida->direccion_entrega=$salida->direccion_entrega;
				$new_salida->tesdocumentos_id=$salida->tesdocumentos_id;
				$new_salida->testipocambios_id=$salida->testipocambios_id;
				$new_salida->codigo=$salida->codigo;
				$new_salida->serie=$salida->serie;
				$new_salida->numero=$salida->numero;
				$new_salida->femision=date("Y-m-d");
				$new_salida->fvencimiento=date("Y-m-d");
				$new_salida->fregistro=date("Y-m-d");
				$new_salida->npedido=$salida->npedido;
				$new_salida->numeroguia=$salida->serie.'-'.$salida->numero.','.$salida->id;
				$new_salida->numerofactura=$salida->serie.'-'.$salida->numero.','.$salida->id;
				$new_salida->ordendecompra=$salida->ordendecompra;
				$new_salida->glosa=$salida->glosa;
				$new_salida->cuentagastos=$salida->cuentagastos;
				$new_salida->cuentaporpagar=$salida->cuentaporpagar;
				$new_salida->totalconigv=$salida->totalconigv;
				$new_salida->totalenletras=$salida->totalenletras;
				$new_salida->movimiento="PT";
				$new_salida->finiciotraslado=$salida->finiciotraslado;
				$new_salida->estado=$salida->estado;
				$new_salida->estadosalida='Pendiente';
				$new_salida->userid=$salida->userid;
				$new_salida->aclempresas_id=$salida->aclempresas_id;
				$new_salida->tescuentascorrientes_id=$salida->tescuentascorrientes_id;
				$new_salida->hilodestino_id=$salida->hilodestino_id;
				$new_salida->tescondicionespagos_id=$salida->tescondicionespagos_id;
				if($new_salida->save())
				{
					$le= new Tesletrassalidas();
					$le->tessalidas_id=$new_salida->id;
					$le->numerodecuenta='12321';
					$le->numerodeletra=$new_salida->numero;
					$le->monto=$new_salida->totalconigv;
					$le->numerounico=$les->numerounico;
					$le->banco=$les->banco;
					$le->factura_id=$les->factura_id;
					$le->estado='1';
					$le->userid=Auth::get('id');
					$le->estadoletras='Pendiente';
					$le->aclempresas_id=Session::get('EMPRESAS_ID');
					$le->save();
					Flash::valid('La letra fue creada');
					return Redirect::toAction('');
				}
			
			
		}
	}
	
	
	return Redirect::toAction('letras_pro');
}

public function renovacion($id)/*recibe el id de la letra a renovar*/
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrassalidas();
	$this->letra=$letra=$letras->find_first((int)$id);
	$salidas= new Tessalidas();
	$this->salida=$salida=$salidas->find_first($letra->tessalidas_id);
	
	$this->NR=$letras->nrenovacion($salidas->numero);
	if (Input::hasPost('salida')) {
            if(Input::post('salida'))
		{
			$salidas= new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id=34;
			$salidas->estado='1';
			/*Cuenta cuentagastos cuentaporpagar*/
			$salidas->cuentaporcobrar='12321';
			$salidas->userid=Auth::get('id');
			$salidas->numeroguia=$salida->serie.'-'.$salida->numero.','.$salida->id;
			$salidas->numerofactura=$salida->serie.'-'.$salida->numero.','.$salida->id;
			$salidas->activo='1';
			/*$salidas->testipocambios_id=Session::get('CAMBIO_ID');*/
			$salidas->userid=Auth::get('id');
			$salidas->aclusuarios_id=Auth::get('id');
			$salidas->aclempresas_id=Session::get("EMPRESAS_ID");
			if($salidas->save())
			{
				$le= new Tesletrassalidas();
				$le->tessalidas_id=$salidas->id;
				$le->numerodecuenta='12321';
				$le->numerodeletra=$letra->numerodeletra;
				$le->numerounico=$letra->numerounico;
				$le->factura_id=$letra->factura_id;
				$le->banco=$letra->banco;
				$le->nrenovacion=$letras->nrenovacion($salidas->numero);
				$le->monto=$salidas->totalconigv;
				$le->estado='1';
				$le->userid=Auth::get('id');
				$le->estadoletras='Registrado';
				$le->aclempresas_id=Session::get('EMPRESAS_ID');
				$le->save();
				$salida->estadosalida="PAGADO";
				$salida->save();
				$letra->estadoletras="PAGADO";
				$letra->save();
				Flash::valid('La letra fue creada');
				return Redirect::toAction('letrara/'.$le->id);
				//return Redirect::toAction('verrenovacion/'.$le->id);
			}
		}
      }
}
public function verrenovacion($id)
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrassalidas();
	$this->letra=$letra=$letras->find_first((int)$id);
	$salidas= new Tessalidas();
	$this->salida=$salidas->find_first($letra->tessalidas_id);
	
}
public function pagadobanco($id=0,$url)
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrassalidas();
	if($letras->exists('id='.$id.' AND aclempresas_id='.Session::get('EMPRESAS_ID').''))
	{
		$this->letras=$letras->find_first((int)$id);
		
			$les= new Tesletrassalidas();
			$les->find_first((int)$id);
			$les->estadoletras='PAGADO';
			$les->fecha_pago=date('Y-m-d');
			if($les->save())
			{
				Flash::valid("La Letra Fue Pagada");
				return Redirect::toAction($url);
			}
		}
	
	
	return Redirect::toAction('letrasbancos');
}


public function letrasbancos()
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$ingresos= new Tessalidas();
	$letras= new Tesletrassalidas();
	$campos = 'tesletrassalidas.' . join(',tesletrassalidas.', $letras->fields);
	$join = 'INNER JOIN tessalidas as t ON t.id = tesletrassalidas.tessalidas_id AND movimiento="SALIDA" AND  t.aclempresas_id='.Session::get('EMPRESAS_ID');
	$this->letras=$letras->find('conditions: estadoletras!="PAGADO" AND !ISNULL(tesletrassalidas.numerounico)',"join: $join", "columns: $campos",'order: t.fvencimiento');
}
public function letras_pro()
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$ingresos= new Tessalidas();
	$letras= new Tesletrassalidas();
	$campos = 'tesletrassalidas.' . join(',tesletrassalidas.', $letras->fields);
	/* AND DATE_ADD(fvencimiento, INTERVAL 8 DAY)<="'.date('Y-m-d').'"*/
	$join = 'INNER JOIN tessalidas as t ON t.id = tesletrassalidas.tessalidas_id AND DATE_ADD(fvencimiento, INTERVAL 8 DAY)<="'.date('Y-m-d').'" AND t.aclempresas_id='.Session::get('EMPRESAS_ID');
	$this->letras=$letras->find('conditions: estadoletras!="PAGADO" AND !ISNULL(tesletrassalidas.numerounico)',"join: $join", "columns: $campos",'order: t.fvencimiento');
}
public function letras_protestadas()
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$ingresos= new Tessalidas();
	$letras= new Tesletrassalidas();
	$campos = 'tesletrassalidas.' . join(',tesletrassalidas.', $letras->fields);
	/* AND DATE_ADD(fvencimiento, INTERVAL 8 DAY)<="'.date('Y-m-d').'"*/
	$join = 'INNER JOIN tessalidas as t ON t.id = tesletrassalidas.tessalidas_id AND movimiento="PT" AND t.aclempresas_id='.Session::get('EMPRESAS_ID');
	$this->letras=$letras->find('conditions: tesletrassalidas.estadoletras!="PAGADO" AND t.movimiento="PT" AND !ISNULL(tesletrassalidas.numerounico)',"join: $join", "columns: $campos",'order: t.fvencimiento');
}
public function letrasporfecha()
{
	/*Pendientes */
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrassalidas();
	$this->letras=$letras->reporteFechas(1);
}
public function letrasmes($anio,$m)
{
	$this->anio=Session::get('ANIO');
	if($anio!='')$this->anio=$anio;
	$this->m=$m;
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrassalidas();
	$cond=$anio.'-'.$m;
	$this->obligaciones=$letras->reporteFechas_mes($cond);
	$this->resumenes=$letras->getResumenFechasMes($cond);
}
public function letraspagadas($anio,$m)
{
	$this->anio=Session::get('ANIO');
	if($anio!='')$this->anio=$anio;
	$this->m=$m;
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrassalidas();
	$cond=$anio.'-'.$m;
	$this->obligaciones=$letras->reporteFechas_mes_pagado($cond);
	$this->resumenes=$letras->getResumenFechasMes($cond);
}

/* Reportes de letras para gestion */

public function reporte_letras($datos_id=0,$estado='')
{
	
}

/*############################ INTERNAS LETRAS */
public function internas()
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras_i= new Tesletrassalidasinternas();
	$this->letras=$letras_i->find('conditions: ISNULL(numerounico) AND aclempresas_id='.Session::get('EMPRESAS_ID'));
}
public function internas_registradas()
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras_i= new Tesletrassalidasinternas();
	echo 'conditions: !ISNULL(numerounico) AND estado !="PAGADO" AND aclempresas_id='.Session::get('EMPRESAS_ID');
	$this->letras=$letras_i->find('conditions: !ISNULL(numerounico) AND estadoletras !="PAGADO" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
}

/*public function pagar la letra interna*/
public function pagarinterna($id,$url)
{
	$letras= new Tesletrassalidasinternas();
	if($letras->exists('id='.$id.' AND aclempresas_id='.Session::get('EMPRESAS_ID')))
	{
		$this->letras=$letras->find_first((int)$id);
		
			$les= new Tesletrassalidasinternas();
			$les->find_first((int)$id);
			$les->estadoletras='PAGADO';
			if($les->save())
			{
				Flash::valid("La Letra Fue Pagada");
				return Redirect::toAction($url);
			}
		}
}


public function registrar_interno($id=0,$url)
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$letras= new Tesletrassalidasinternas();
	if($letras->exists('id='.$id.' AND aclempresas_id='.Session::get('EMPRESAS_ID').''))
	{
		$this->letras=$letras->find_first($id);
		if(Input::post('letras'))
		{
			$le= new Tesletrassalidasinternas(Input::post('letras'));
			$le->estadoletras='Resgistrado';
			if($le->save())
			{
				$ingresos= new Tessalidasinternas();
				$ing=$ingresos->find_first((int)$le->tessalidasinternas_id);
				$ing->movimiento="SALIDA";
				$ing->estadosalida='Registrado';
				$ing->save();
				Flash::valid('La letra fue registrada');
				return Redirect::toAction($url);
			}
		}
	
	}else
	{
		Flash::warning('Esta letra tiene Problemas porfavor consultar con el administrador');
		return Redirect::toAction('internas');
		
	}
	
}

public function letrai($id=0){
	$letras= new Tesletrassalidasinternas();
	if($letras->exists('id='.$id.' AND aclempresas_id='.Session::get('EMPRESAS_ID').''))
	{
		$this->letra=$letras->find_first((int)$id);	
	}else
	{
	return Redirect::toAction('internas');
	}
}
public function letrar($id=0){
	$letras= new Tesletrassalidas();
	if($letras->exists('id='.$id.' AND aclempresas_id='.Session::get('EMPRESAS_ID').''))
	{
		$this->letra=$letras->find_first((int)$id);	
	}else
	{
	return Redirect::toAction('');
	}
}
public function letrara($id=0){
	$letras= new Tesletrassalidas();
	if($letras->exists('id='.$id.' AND aclempresas_id='.Session::get('EMPRESAS_ID').''))
	{
		$this->letra=$letras->find_first((int)$id);	
	}else
	{
	return Redirect::toAction('');
	}
}

public function letra_adelanto($id=0)
	{
		$SALD=new Tessalidasinternas();
		if($id==0){
		$this->salida['serie']='LTR';
		$this->salida['numero']=$SALD->generarNumeroLetras();
		$this->salida['npedido']=$SALD->getNumeropedido('LA','LTR');
		}else
		{
		$this->salida=$SALD->find_first($id);
		/*$DETALLES=new Tesdetallesalidas();
		$this->detalle=$DETALLES->find_first('conditions: tessalidas_id='.(int)$id);*/
		}
		if (Input::hasPost('salida')) 
		{
			//$campos=Input::post('salida');
			$salidas= new Tessalidasinternas(Input::post('salida'));
			$salidas->tesdocumentos_id='34';/* id de la letra*/
			/*$salidas->fvencimiento=date("Y-m-d",strtotime($salidas->femision."+ 30 days"));*/
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
				$LETRA= new Tesletrassalidasinternas();
				$salida_p=$SALD->find_first((int)$salidas->id);
				if($LETRA->exists('tessalidasinternas_id='.$salida_p->id))
				{
					$letra_a=$LETRA->find_first('tessalidasinternas_id='.$salida_p->id);
				}else
				{
					$letra_a= new Tesletrassalidasinternas();
				}
				$letra_a->tessalidasinternas_id=$salida_p->id;
				$letra_a->tesmonedas_id=$salida_p->tesmonedas_id;
				$letra_a->tesdatos_id=$salida_p->tesdatos_id;
				$letra_a->numerodeletra=$salida_p->numero;
				$letra_a->monto=$salida_p->total;
				$letra_a->estado='1';
				$letra_a->userid=Auth::get('id');
				$letra_a->aclempresas_id=Session::get("EMPRESAS_ID");
				if($letra_a->save())
				{
				Flash::valid('Letra creada.. ('.$letra_a->montototal.'---'.$salida_p->totalconigv.') !!!');
				}else
				{Flash::warning('No se pudo crear la letra...('.$letra_a->montototal.')!!!');
				}
            				
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('verletra_adelanto/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 $this->detalle=Input::post('detalle');
				 return Redirect::toAction('letra_adelanto/'.$id);
             }
		}
	}
 public function verletra_adelanto()
 {
	$id=Session::get('SALIDA_ID_I');
	$letras= new Tesletrassalidasinternas();
	$salidas= new Tessalidasinternas();
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	if (Input::hasPost('letras')) {
            if(Input::post('letras'))
		{
			$le= new Tesletrassalidasinternas(Input::post('letras'));
			$le->estadoletras='Pendiente';
			if($le->save())
			{
				$salida= new Tessalidasinternas(Input::post('salidas'));
				$salida->estadosalida='Pendiente';
				$salida->total=$le->monto;
				$salida->save();
				Flash::valid('La letra fue Modificada');
				return Redirect::toAction('internas');
			}
		}
      }

        //Aplicando la autocarga de objeto, para comenzar la edición
		
		$this->salidas=$salidas->find_first((int)$id);
        $this->letras= $letras->find_first('conditions: tessalidasinternas_id='.$id);
 }
/* REPORTES */ 
public function letrasemitidas($Y='',$m=''){
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
	 $letras= new Tesletrassalidas();
	 $empresas= new Aclempresas();
	 $this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	 $this->letras=$letras->getEmitidas($anio,$mes_activo);
 }

public function listadoporestado($e='')
{
	 $letras= new Tesletrassalidas();
	 $empresas= new Aclempresas();
	 $this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	 $letras= new Tesletrassalidas();
	$this->letras=$letras->reporteFechas(1);
}

public function eliminar_letras_r($id)
{
$letras= new Tesletrassalidas();
/*las factiruas pasa a pendientes y se elimina todas las demas letras que tengan el mismo cmapo de facturas_id*/
$letra= $letras->find_first($id);
if(!empty($letra->factura_id)){
	$facturas=explode(',',$letra->factura_id);
	foreach($facturas as $item=>$val)
	{
		$salidas = new Tessalidas();
		$salidas->find_first($val);
		$salidas->estadosalida='Pendiente';
		$salidas->save();
	}

$letras_t=$letras->find('conditions: factura_id="'.$letra->fctura_id.'"');
/*eliminar letras de la tabla tessalidas*/
	foreach($letras_t as $l)
	{
		$s= new Tessalidas();
		$s->delete($l->tessalidas_id);
	}
/*Eliminar de la tabla letras*/
	$letras->delete("factura_id='".$letra->factura_id."'");
}else
{
	$s= new Tessalidas();
	$s->delete($letra->tessalidas_id);
	$letras->delete($id);
}

return Redirect::toAction('');
}


public function eliminar_letras_i($id)
{
$letras= new Tesletrassalidasinternas();
/*las factiruas pasa a pendientes y se elimina todas las demas letras que tengan el mismo cmapo de facturas_id*/
$letra= $letras->find_first($id);
if(!empty($letra->factura_id)){
	$facturas=explode(',',$letra->factura_id);
	foreach($facturas as $item=>$val)
	{
		$salidas = new Tessalidasinternas();
		$salidas->find_first($val);
		$salidas->estadosalida='Pendiente';
		$salidas->save();
	}

$letras_t=$letras->find('conditions: factura_id="'.$letra->fctura_id.'"');
/*eliminar letras de la tabla tessalidas*/
	foreach($letras_t as $l)
	{
		$s= new Tessalidasinternas();
		$s->delete($l->tessalidas_id);
	}
/*Eliminar de la tabla letras*/
	$letras->delete("factura_id='".$letra->factura_id."'");
}else
{
	$s= new Tessalidasinternas();
	$s->delete($letra->tessalidasinternas_id);
	$ls= new tesletrassalidasinternas();
	$ls->delete($id);
	Flash::valid('La letra Eliminada');
	
}

return Redirect::toAction('internas');
}

/*PARA PAGAR VARIAS LETRAS*/
public function pagar()
{
	if (Input::hasPost('facturas')) 
	{
		$values=Input::post('facturas');
		$VAL=explode(',',$values['ids']);
	
		foreach($VAL as $value=>$item)
		{
		$letras= new Tesletrassalidas();
		$letra=$letras->find_first((int)$item);
		$letra->estadoletras="PAGADO";
		$letra->fecha_pago=$values['fecha_pago'];
		$letra->save();
		}
		Flash::valid('Las letras fueron pagadas con fecha '.$values['fecha_pago']);
		return Redirect::toAction($values['url']);
	}
	
}


}

?>