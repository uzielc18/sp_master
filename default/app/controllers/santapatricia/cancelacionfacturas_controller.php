<?php 
View::template('spatricia/default');

Load::models('tesdatos','tesdocumentos','tessalidas','tesdetallesalidas','aclempresas','tesletrassalidas','prodetalletransportes','tessalidasinternas','tesletrassalidasinternas','tesaplicacionfacturasadelantos','tesfacturasadelantos');
class CancelacionfacturasController extends AdminController
{
protected function before_filter() 
	{
        if (Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
	}

public function index()
{
		$this->p='0';
		$this->titulo='Canjear facturas por letras';
		if(Input::hasPost('tesdatos_id'))
		{
			Session::set('tesdatos_id',Input::post('tesdatos_id'));
			Session::set('tesmonedas_id',Input::post('tesmonedas_id'));
			return Redirect::toAction('creardetalle');
		}
		
					
              
}
public function creardetalle()
	{
		if(Session::get('tesdatos_id')!=0)
			{
			  
			  $tesdatos= new Tesdatos();
			  $this->tesdatos=$tesdatos->find_first((int)Session::get('tesdatos_id'));
			}
			//echo '(tesdocumentos_id=7 OR tesdocumentos_id=14 OR tesdocumentos_id=13)  AND (estadosalida="Pendiente" OR estadosalida="ADELANTADO" )AND tesdatos_id='.Session::get('tesdatos_id').' AND tesmonedas_id='.Session::get('tesmonedas_id');
			$salidas= new Tessalidas();
			$this->salidas=$salidas->find('conditions: (tesdocumentos_id=7 OR tesdocumentos_id=14 OR tesdocumentos_id=13)  AND (estadosalida="Pendiente" OR estadosalida="ADELANTADO" )AND tesdatos_id='.Session::get('tesdatos_id').' AND tesmonedas_id='.Session::get('tesmonedas_id'));
			
	}
/*el id de una factura*/	
public function variasfacturas($ids='')
{
if($ids==''){
if(!Input::post('numerodeletras')){
	Flash::error('Cuantas letras va a Generar?');
  return Redirect::toAction('creardetalle');
}
}
	$this->ajax=FALSE;
	if (Input::isAjax() ){
			View::template(null);
       $this->ajax=TRUE;     
        }
	$tesdatos= new Tesdatos();
			  $this->tesdatos=$tesdatos->find_first((int)Session::get('tesdatos_id'));
	$salidas= new Tessalidas();
	$this->salidas=$salidas->find('conditions: (tesdocumentos_id=7 OR tesdocumentos_id=14 OR tesdocumentos_id=13) AND tesdatos_id='.Session::get('tesdatos_id'));
	$S= $salidas;
	//$this->factura=$S->find_first((int)$id);
	if (Input::hasPost('numerodeletras')) 
	{
		$letrassalida=new Tesletrassalidas();
		$n=Input::post('numerodeletras');
		$ids=$this->ids=Input::post('s');
		if(!$letrassalida->exists('factura_id="'.$ids.'"'))
		{
			$VAL=explode(',',$ids);
			$g=0;
			$facturas='';
			$totalconigv=0;
			$sumaaltotal=0;
			$restalatotal=0;
			foreach($VAL as $value=>$item)
			{
				$id=$item;
				$salidas=new Tessalidas();
				$salida=$salidas->find_first((int)$item);
				/*NOTA DE CREDITO RESTA*/
				if($salida->tesdocumentos_id==13){
				$salida->estadosalida='TERMINADO';
				$restalatotal=$restalatotal+$salida->totalconigv;
				$salida->save();
				
				}
				/*NOTA DE DEBITO SUMA*/
				if($salida->tesdocumentos_id==14){
				$salida->estadosalida='TERMINADO';
				$sumaaltotal=$sumaaltotal+$salida->totalconigv;
				$salida->save();
				
				}
				if($salida->tesdocumentos_id==7){
				$totalconigv=$totalconigv+$salida->totalconigv;
				/*validar si la suma*/
				$salida->estadosalida='TERMINADO';
				$salida->save();
				
				if($g==0)$coma='';else $coma=',';
				$facturas.=$coma.$salida->serie.'-'.$salida->numero;
				$g++;
				}
			}
			$SALD= $salidas;
			$salidas= $salidas;
			$salida=$salidas->find_first((int)$id);
			$fecha_incio=$salida->femision;
			$TOTALCONIGV=$totalconigv+$sumaaltotal-$restalatotal;
			$monto=$TOTALCONIGV/$n;
			/*dejar libre la creacion de las letras*/
			$credito=$salida->getTesdatos()->credito;
			$diascredito=$salida->getTesdatos()->diascredito;
			$valor_suma_fecha=(int)($diascredito/$n);
			for($i=1;$i<=$n;$i++)
			{
				$new_salida= new Tessalidas();
				$new_salida->aclusuarios_id	=Auth::get('id');
				$new_salida->tesmonedas_id=$salida->tesmonedas_id;
				$new_salida->tesdatos_id=$salida->tesdatos_id;
				$new_salida->tesdocumentos_id=34;
				$new_salida->testipocambios_id=$salida->testipocambios_id;
				$new_salida->codigo=time();
				$new_salida->serie='LTR';
				$new_salida->npedido=$SALD->getNumeropedido('LT','LTR');
				$new_salida->numero=$SALD->generarNumeroLetras();
				$new_salida->numerofactura=$facturas;
				$new_salida->femision=$salida->femision;
				$new_salida->fvencimiento=date("Y-m-d",strtotime($fecha_incio."+ ".(int)$valor_suma_fecha." days"));
				$new_salida->fregistro=$salida->fregistro;
				$new_salida->glosa=$salida->glosa;
				$new_salida->totalconigv=$monto;
				$new_salida->movimiento='SALIDA';
				$new_salida->cuentaporcobrar='12321';/* */
				$new_salida->estado='1';
				$new_salida->estadosalida='Editable';
				$new_salida->userid=Auth::get('id');
				$new_salida->aclempresas_id=$salida->aclempresas_id;
				$new_salida->tescuentascorrientes_id=$salida->tescuentascorrientes_id;
				if($new_salida->save())
				{
					/*Auditorias*/                    
                    Aclauditorias::add("Creo un nueva salida tipo (Letra),Total ={$new_salida->totalconigv}, Numero = {$new_salida->numero},Tessalidas->id={$new_salida->id}",'Tessalidas');
					/*fin Aurditorias*/
					$letrassalida=new Tesletrassalidas();
					$letrassalida->tessalidas_id=$new_salida->id;
					$letrassalida->factura_id=$ids;
					$letrassalida->numerodeletra=$new_salida->numero;
					$letrassalida->monto=$new_salida->totalconigv;
					$letrassalida->estado='1';
					$letrassalida->userid=Auth::get('id');
					$letrassalida->estadodeltras='Editable';
					$letrassalida->aclempresas_id=$salida->aclempresas_id;
					$letrassalida->save();
					/*Auditorias*/                    
                    Aclauditorias::add("Creo un nueva  (Letra),Total ={$letrassalida->monto}, Numero = {$letrassalida->numerodeletra},Tesletrassalidas->id={$letrassalida->id}",'Tesletrassalidas');
					/*fin Aurditorias*/
				}
				$fecha_incio=$new_salida->fvencimiento;
			}
	    }
		return Redirect::toAction('variasfacturas/'.$ids);	
		
	}else
	{
		$VAL=explode(',',$ids);
		$conditions='';
			$g=0;
			foreach($VAL as $value=>$item)
			{
				
				$this->id=$item;
				if($g==0)$coma='';else $coma=' OR';
				$conditions.=$coma.' id='.$item;
				$g++;
			}
			$this->salidas=$salidas->find('conditions: '.$conditions);
		}
		
	$letrassalida=new Tesletrassalidas();
	$this->letras=$letrassalida->find('conditions: factura_id="'.$ids.'"');
	$this->n=$letrassalida->count('factura_id="'.$ids.'"');
	

}
/*salidas sunat
$id= el id de la factura a generar en letras
*/

public function unafactura($id,$n=0)
{
	$this->ajax=FALSE;
	if (Input::isAjax() ){
			View::template(null);
       $this->ajax=TRUE;     
        }
	$this->id=$id;
	$S= new Tessalidas();
	$this->factura=$S->find_first((int)$id);
	if ($n!=0) 
		{
			$letrassalida=new Tesletrassalidas();
			if(!$letrassalida->exists('factura_id='.$id))
			{
			$n=$n;
			$SALD= new Tessalidas();
			$salidas= new Tessalidas();
			$salida=$salidas->find_first((int)$id);
			$fecha_incio=$salida->femision;
			$monto=$salida->totalconigv/$n;
			/*dejar libre la creacion de las letras*/
			$credito=$salida->getTesdatos()->credito;
			
			$diascredito=$salida->getTesdatos()->diascredito;
			$valor_suma_fecha=(int)($diascredito/$n);
			
			
			for($i=1;$i<=$n;$i++)
			{
				$new_salida= new Tessalidas();
				$new_salida->aclusuarios_id	=Auth::get('id');
				$new_salida->tesmonedas_id=$salida->tesmonedas_id;
				$new_salida->tesdatos_id=$salida->tesdatos_id;
				$new_salida->tesdocumentos_id=34;
				$new_salida->testipocambios_id=$salida->testipocambios_id;
				$new_salida->codigo=time();
				$new_salida->serie='LTR';
				$new_salida->npedido=$SALD->getNumeropedido('LT','LTR');
				$new_salida->numero=$SALD->generarNumeroLetras();
				$new_salida->femision=$salida->femision;
				$new_salida->fvencimiento=date("Y-m-d",strtotime($fecha_incio."+ ".(int)$valor_suma_fecha." days"));
				$new_salida->fregistro=$salida->fregistro;
				$new_salida->glosa=$salida->glosa;
				$new_salida->totalconigv=$monto;
				$new_salida->movimiento='SALIDA';
				$new_salida->cuentaporcobrar='12321';/* */
				$new_salida->estado='1';
				$new_salida->estadosalida='Editable';
				$new_salida->userid=Auth::get('id');
				$new_salida->aclempresas_id=$salida->aclempresas_id;
				$new_salida->tescuentascorrientes_id=$salida->tescuentascorrientes_id;
				if($new_salida->save())
				{					
					/*Auditorias*/                    
                    Aclauditorias::add("Creo un nueva salida tipo (Letra),Total ={$new_salida->totalconigv}, Numero = {$new_salida->numero},Tessalidas->id={$new_salida->id}",'Tessalidas');
					/*fin Aurditorias*/
					$letrassalida=new Tesletrassalidas();
					$letrassalida->tessalidas_id=$new_salida->id;
					$letrassalida->factura_id=$salida->id;
					$letrassalida->numerodeletra=$new_salida->numero;
					$letrassalida->monto=$new_salida->totalconigv;
					$letrassalida->estado='1';
					$letrassalida->userid=Auth::get('id');
					$letrassalida->estadodeltras='Editable';
					$letrassalida->aclempresas_id=$salida->aclempresas_id;
					$letrassalida->save();
					
					}
				$fecha_incio=$new_salida->fvencimiento;
				$salida->estadosalida='TERMINADO';
				$salida->save();
			}
		 	}
		}
		/**/
		$tesletrassalidas = new Tesletrassalidas();
				if($tesletrassalidas->exists('factura_id='.$id))
				{
					$total_letras=$tesletrassalidas->sum('monto','conditions: factura_id='.$id);
					if($total_letras<$salidas->totalconigv)
					{
						$SALIDAS= new Tessalidas();
						$salida=$SALIDAS->find_first($id);
						$salida->estadosalida='ADELANTADO';
						$salida->save();
					}
				}
	$letrassalida=new Tesletrassalidas();
	$this->letras=$letrassalida->find('conditions: factura_id='.$id);
	$this->n=$letrassalida->count('factura_id='.$id);
	if(!$this->ajax)
	{
		$salidas= new Tessalidas();
		$this->salidas=$salidas->find_first((int)Session::get('SALIDA_ID'));
		
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
	
	}
	
}
public function grabarletra($id=0,$e='Grabar')
{
	View::template(null);
	$orden=$id;
	$this->orden=$id;
	$this->e=$e;
	if (Input::hasPost('salida-'.$orden)) 
		{
			//Flash::warning('EL post salidas!!!'.$orden);
			$salidas = new Tessalidas(Input::post('salida-'.$orden));
			if($salidas->save()){
					$letras=new Tesletrassalidas();
					$letra=$letras->find_first('conditions: tessalidas_id='.$salidas->id);
					$letra->tessalidas_id=$salidas->id;
					$letra->numerodeletra=$salidas->numero;
					$letra->monto=$salidas->totalconigv;
					$letra->estado='1';
					$letra->userid=Auth::get('id');
					$letra->estadoletras='Sin Numero';
					$letra->aclempresas_id=$salidas->aclempresas_id;
					if(!$letra->save())
					{
						Flash::warning('No se Pudieron Guardar los Datos de la letra...!!!');
					}
					Flash::valid('Letra modificada...!!!');
			}else
			{
				 Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		//Flash::warning('No hya post!!!');
		$letras=new Tesletrassalidas();
		$this->letra=$letras->find_first((int)$id);
}

public function unafactura_i($id,$n=0)
{
	$this->ajax=FALSE;
	if (Input::isAjax() ){
			View::template(null);
       $this->ajax=TRUE;     
        }
	$this->id=$id;
	if ($n!=0) 
		{
			$letrassalida=new Tesletrassalidasinternas();
			if(!$letrassalida->exists('factura_id='.$id))
			{
			$n=$n;
			$SALD= new Tessalidasinternas();
			$salidas= new Tessalidasinternas();
			$salida=$salidas->find_first((int)$id);
			$fecha_incio=$salida->femision;
			$monto=$salida->totalconigv/$n;
			/*dejar libre la creacion de las letras*/
			$credito=$salida->getTesdatos()->credito;
			
			$diascredito=$salida->getTesdatos()->diascredito;
			$valor_suma_fecha=(int)($diascredito/$n);
			
			
			for($i=1;$i<=$n;$i++)
			{
				$new_salida= new Tessalidasinternas();
				$new_salida->aclusuarios_id	=Auth::get('id');
				$new_salida->tesmonedas_id=$salida->tesmonedas_id;
				$new_salida->tesdatos_id=$salida->tesdatos_id;
				$new_salida->tesdocumentos_id=34;
				$new_salida->testipocambios_id=$salida->testipocambios_id;
				$new_salida->codigo=time();
				$new_salida->numero=$SALD->generarNumeroLetras();
				$new_salida->femision=$salida->femision;
				$new_salida->fvencimiento=date("Y-m-d",strtotime($fecha_incio."+ ".(int)$valor_suma_fecha." days"));
				$new_salida->fregistro=$salida->fregistro;
				$new_salida->glosa=$salida->glosa;
				$new_salida->totalconigv=$monto;
				$new_salida->movimiento='SALIDA';
				$new_salida->cuentaporcobrar='12321';/* */
				$new_salida->estado='1';
				$new_salida->estadosalida='Editable';
				$new_salida->userid=Auth::get('id');
				$new_salida->aclempresas_id=$salida->aclempresas_id;
				$new_salida->tescuentascorrientes_id=$salida->tescuentascorrientes_id;
				if($new_salida->save()){
					/*Auditorias*/                    
                    Aclauditorias::add("Creo una nueva salida interna ,Tessalidasinternas->id={$new_salida->id}",'Tessalidasinternas');
					/*fin Aurditorias*/
					$letrassalida=new Tesletrassalidasinternas();
					$letrassalida->tessalidasinternas_id=$new_salida->id;
					$letrassalida->factura_id=$salida->id;
					$letrassalida->numerodeletra=$new_salida->numero;
					$letrassalida->monto=$new_salida->totalconigv;
					$letrassalida->estado='1';
					$letrassalida->userid=Auth::get('id');
					$letrassalida->estadodeltras='Editable';
					$letrassalida->aclempresas_id=$salida->aclempresas_id;
					$letrassalida->save();
					/*Auditorias*/                    
                    Aclauditorias::add("Creo una nueva letra interna ,Tesletrassalidasinternas->id={$letrassalida->id}",'Tesletrassalidasinternas');
					/*fin Aurditorias*/
					}
				$fecha_incio=$new_salida->fvencimiento;
			}
		 	}
		}
		
	$letrassalida=new Tesletrassalidasinternas();
	$this->letras=$letrassalida->find('conditions: factura_id='.$id);
	$this->n=$letrassalida->count('factura_id='.$id);
	if(!$this->ajax)
	{
		$salidas= new Tessalidasinternas();
		$this->salidas=$salidas->find_first((int)Session::get('SALIDA_ID'));	
	}
	
}
public function grabarletra_i($id=0,$e='Grabar')
{
	//View::select('grabarletra');
	View::template(null);
	$orden=$id;
	$this->orden=$id;
	$this->e=$e;
	if (Input::hasPost('salida-'.$orden)) 
		{
			//Flash::warning('EL post salidas!!!'.$orden);
			$salidas = new Tessalidasinternas(Input::post('salida-'.$orden));
			if($salidas->save()){
					$letras=new Tesletrassalidasinternas();
					$letra=$letras->find_first('conditions: tessalidasinternas_id='.$salidas->id);
					$letra->tessalidasinternas_id=$salidas->id;
					$letra->numerodeletra=$salidas->numero;
					$letra->monto=$salidas->totalconigv;
					$letra->estado='1';
					$letra->userid=Auth::get('id');
					$letra->estadoletras='Sin Numero';
					$letra->aclempresas_id=$salidas->aclempresas_id;
					if(!$letra->save())
					{
						Flash::warning('No se Pudieron Guardar los Datos de la letra...!!!');
					}
					Flash::valid('Letra modificada...!!!');
			}else
			{
				 Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		//Flash::warning('No hya post!!!');
		$letras=new Tesletrassalidasinternas();
		$this->letra=$letras->find_first((int)$id);
}
/*recibe el id de salida a pagar el id de la facturaadelanto POR ADELANTO*/
public function aplicaradelanto($id,$f_id,$total=0)
{
	$modulo = Redirect::get('module');
	$this->controller = Redirect::get('controller');
	$adelantos= new Tesfacturasadelantos();
	$tessalidas= new Tessalidas();
	/*el $t_factura_a es la resta de total de factura adleanto menos la suma de todas las plicaciones */
	$t_factura_a=$adelantos->getTotal_aplicacion($f_id);
	/*el $t_salida_a es el total */
	$t_salida_a=$total;//$tessalidas->getTotal_aplicacion($id);
	
	$adelanto=$adelantos->find_first($f_id);
	$this->salida=$salida=$tessalidas->find_first($id);
	 
	 $aplicacion=new Tesaplicacionfacturasadelantos();
	 $aplicacion->tesfacturasadelantos_id=$adelanto->id;
	 $aplicacion->tesmonedas_id=$salida->tesmonedas_id;
	 $aplicacion->serie=$salida->serie;
	 $aplicacion->numero=$salida->numero;
	 if($t_salida_a>=$t_factura_a)$aplicacion->montototal=$t_factura_a;else $aplicacion->montototal=$t_salida_a;
	 $aplicacion->estado='1';
	 $aplicacion->userid=Auth::get('id');
	 $aplicacion->tessalidas_id=$salida->id;
	 $aplicacion->aclempresas_id=Session::get("EMPRESAS_ID");
	 $aplicacion->save();
	 /*Auditorias*/ 
	 //Aclauditorias::add("Creo una nueva letra interna ,Tesletrassalidasinternas->id={$letrassalida->id}",'Tesletrassalidasinternas');
	/*fin Aurditorias*/
	 if($t_factura_a<$t_salida_a)
	 {
		$adelanto->estado='9';
		$adelanto->save();
	 }
	 /*PARA LA CANCELACION DE LA FACTURA*/
	 if(number_format($this->salida->totalconigv,2,'.','')==number_format($aplicacion->montototal,2,'.',''))
	 {	
	 	$salida->estadosalida='PAGADO';
		$salida->save(); 
	 }
	 
		
		$DETALLES=new Tesdetallesalidas();
		$this->detalles=$DETALLES->find('conditions: tessalidas_id='.(int)Session::get('SALIDA_ID'));
		/* mostrar las facturas por adelanto y si monto final */
		$adelantos= new Tesfacturasadelantos();
		$this->adelantos=FALSE;
		if($adelantos->exists('tesdatos_id='.$salida->tesdatos_id.' AND estado=1')){
			
		$this->adelantos=$adelantos->getFacturasAdelantos($salida->tesdatos_id);
		//$this->adelantos=$adelantos->find('conditions: tesdatos_id='.$salida->tesdatos_id.' AND estado=1');
		}
		/* Busa las aplicaciones a esta factura */
		$aplicaciones= new Tesaplicacionfacturasadelantos();
		$this->aplicaciones=FALSE ;
		if($aplicaciones->exists('tessalidas_id='.$salida->id)){
				
		$this->aplicaciones =$aplicaciones->find('conditions: tessalidas_id='.$salida->id);
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
}
?>