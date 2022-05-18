<?php

class IngresosController extends AdminController
{
	public $documento=27;
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	
	public function index()
	{
		/*
		# SELECT tesdocumentos.*, count(tesingresos.id) as N FROM tesdocumentos LEFT JOIN tesingresos ON tesingresos.tesdocumentos_id=tesdocumentos.id WHERE tesdocumentos.estado=1 AND tesdocumentos.activo=1 group by tesdocumentos.id order by n desc
		*/
		Session::delete('DOC_ID');
		Session::delete('DOC_NOMBRE');
		Session::delete('DOC_ABR');
		Session::delete('DOC_CODIGO');
		Session::delete('V_ID');
		$documentos= new Tesdocumentos();
		$campos='tesdocumentos.' . join(',tesdocumentos.',$documentos->fields) . ',count(tesingresos.id) as N';
		$this->documentos= $documentos->find('columns: '.$campos,'conditions: tesdocumentos.activo=1 AND tesdocumentos.estado=1 AND tesdocumentos.id!='.$this->documento.' group by tesdocumentos.id order by N desc','join: LEFT JOIN tesingresos ON tesingresos.tesdocumentos_id=tesdocumentos.id AND tesingresos.pr="NN"');
	}
	public function cargar($id)
	{
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)$id);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		return Redirect::toAction('listado');
	}
	public function cargaringreso($id=0,$url='crear')
	{
		if($id!=0){
		Session::set("INGRESO_ID",$id);
		}else
		{
			Session::delete("INGRESO_ID");
		}
		return Redirect::toAction($url);
	}
public function listado($Y='',$m='')
	{
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		Session::delete("INGRESO_ID");
		$ingresos= new Tesingresos();
		/*AND (estadoingreso="Pendiente" OR estadoingreso="Editable") AND (estadoingreso="Pendiente" OR estadoingreso="Editable" or estadoingreso="Detraccion") */
		
		$this->facturas= $ingresos->find("conditions: DATE_FORMAT(femision,'%Y-%m')='".$anio."-".$mes_activo."' AND pr='NN' AND tesdatos_id!=2600  AND estado=1 AND tesdocumentos_id=".Session::get('DOC_ID')." AND aclempresas_id=".Session::get('EMPRESAS_ID')." ORDER BY femision DESC");
		if(Session::get('DOC_ID')==7)
		{
			$this->guias= $ingresos->find('conditions: (ISNULL(movimiento) OR movimiento!="INTE") AND serie!="999" AND estado=1 AND estadoingreso="Pendiente" AND tesdocumentos_id=15 AND aclempresas_id='.Session::get('EMPRESAS_ID'),'order: tesdatos_id DESC');
		}
	}
	public function pendientes(){
		$ingresos= new Tesingresos();
		$this->inventarios= $ingresos->find('conditions: pr="NN" AND tesdatos_id!=2600 AND (estadoingreso="Pendiente") AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID').' AND aclempresas_id='.Session::get('EMPRESAS_ID'),'order: tesdatos_id DESC limit 0,100');
		}
	public function crear_todo($tipos_id=1,$page=1)
	{
	//try {
		
		$this->totalenletras='';
		$this->DETALLE=FALSE;
		$tipos= new Testipoproductos();
		$productos = new Tesproductos();
		$this->tipos_id=$tipos_id;
	
		$this->productos=$productos->paginate('page: '.$page,'per_page: 80','conditions: estado=1 and activo=1 and testipoproductos_id='.$tipos_id);

		$this->tipo=$tipos->find_first((int)$tipos_id);
		$this->monedas=0;
		$ING=new Tesingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->titulo = 'Crear Inventario INICAL';
		$this->numero=$ING->generarNumero(Session::get('DOC_ID'));
		//$this->observacion='30 días Habiles. Cargo por Financiamiento del 1,5% se realizará sobre los saldos pendientes de pago después de 30 días';
		$this->cliente_id="";
		$this->cliente="";
		if(Session::has('INGRESO_ID')){
		if($ING->exists("id=".(int)Session::get('INGRESO_ID'))){
		$this->DETALLE=TRUE;
		/*
		Cargar los detalles
		*/
		$detalles=new Tesdetalleingresos();
		$inventario=$ING->find_first((int)Session::get('INGRESO_ID'));
		
		$this->detalles=$detalles;
		$this->id=$inventario->id;
		$this->numero=$inventario->numero;
		$this->glosa=$inventario->glosa;
		$this->serie=$inventario->serie;
		$this->numero=$inventario->numero;
		$this->codigo=$inventario->codigo;//fvencimiento
		$this->femision=$inventario->femision;
		$this->fvencimiento=$inventario->fvencimiento;
		$this->monedas=$inventario->tesmonedas_id;
		$this->cliente_id=$inventario->tesdatos_id;
		$this->totalconigv=$inventario->totalconigv;
		$this->igv=$inventario->igv;
		$this->subt=$this->totalconigv-$this->igv;
		$this->paid=$inventario->igv;
		$this->totalenletras=$inventario->totalenletras;
		
		}else{
		$this->DETALLE=FALSE;
		$this->numero=$ING->generarNumero(Session::get('DOC_ID'));
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->monedas=0;
		$this->cliente_id="";
		$this->cliente="";
		$this->id='';
		$this->totalenletras='';
		}
		}
	/*} catch (KumbiaException $e)
	{
       View::excepcion($e);
    }*/
	}
	
	
	public function crear()
	{
		$this->movimiento='CP';
		$this->monedas_nombre='';
		$this->totalenletras='';
		$this->DETALLE=FALSE;
		$this->monedas=0;
		$ING=new Tesingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->titulo = Session::get('DOC_NOMBRE');
		//$this->numero=$ING->generarNumero(Session::get('DOC_ID'));
		$this->observacion='';
		$this->pre_descripcion='';
		$this->subt=0.00;
		$this->detalles='';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->totalconigv=00.00;
		$this->igv=00.00;
		$this->cliente_id="0";
		$this->cliente="";
		$this->cuantagastos=NULL;
		$this->cuentaporpagar=NULL;
		$this->cuantagastos_name=NULL;
		$this->cuentaporpagar_name=NULL;
		$this->nombre=NULL;
		if(Session::has('INGRESO_ID')){
		if($ING->exists("id=".(int)Session::get('INGRESO_ID'))){
		$this->DETALLE=TRUE;
		/*
		Cargar los detalles
		*/
		$detalles=new Tesdetalleingresos();
		$ingreso=$ING->find_first((int)Session::get('INGRESO_ID'));
		
		$this->detalles=$detalles->find('conditions: tesingresos_id='.$ingreso->id);
		$this->id=$ingreso->id;
		$this->numero=$ingreso->numero;
		$this->glosa=$ingreso->glosa;
		$this->serie=$ingreso->serie;
		$this->numero=$ingreso->numero;
		$this->codigo=$ingreso->codigo;//fvencimiento
		$this->femision=$ingreso->femision;
		$this->fvencimiento=$ingreso->fvencimiento;
		$this->monedas=$ingreso->tesmonedas_id;
		$this->monedas_nombre=$ingreso->getTesmonedas()->nombre;
		$this->cliente_id=$ingreso->tesdatos_id;
		/*nombre del cliente*/
		$this->nombre=$ingreso->getTesdatos()->razonsocial." ruc: ".$ingreso->getTesdatos()->ruc." (".$ingreso->getTesdatos()->departamento.' - '.$ingreso->getTesdatos()->provincia.' - '.$ingreso->getTesdatos()->distrito.' - '.$ingreso->getTesdatos()->direccion.')';
		/**/
		$this->totalconigv=$ingreso->totalconigv;
		$this->igv=$ingreso->igv;
		$this->paid=$ingreso->igv;
		$this->fregistro=$ingreso->fregistro;
		$this->npedido=$ingreso->npedido;
		$this->numeroguia=$ingreso->numeroguia;
		$this->numerofactura=$ingreso->numerofactura;
		$this->ordendecompra=$ingreso->ordendecompra;
		$this->finiciotraslado=$ingreso->finiciotraslado;
		$this->movimiento=$ingreso->movimiento;
		$this->cuantagastos=$ingreso->cuantagastos;
		$this->cuentaporpagar=$ingreso->cuentaporpagar;
		$this->totalenletras=$ingreso->totalenletras;
		
		$this->subt=$ingreso->totalconigv-$ingreso->igv;
		if(!empty($this->cuentaporpagar)){
		/* detalle de las cuentas*/
		$cc=new Subcuentas();
		if(!empty($this->cuantagastos)){
		$cg=$cc->find_first('codigo='.$this->cuantagastos);
		$this->cuantagastos_name=$cg->codigo.'<span style=\"font-size:9px;\">('.$cg->descripcion.')</span>';
		}
		if(!empty($this->cuentaporpagar)){
		$cp=$cc->find_first('codigo='.$this->cuentaporpagar);
		$this->cuentaporpagar_name=$cp->codigo.'<span style=\"font-size:9px;\">('.$cp->descripcion.')</span>';
		}
		/**/
		}
		$this->codigo=$ingreso->codigo;
		
		}else{
		$this->DETALLE=FALSE;
		$this->numero=$ING->generarNumero(Session::get('DOC_ID'));
		$this->observacion='';
		$this->pre_descripcion='';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->monedas=0;
		$this->cliente_id="";
		$this->cliente="";
		$this->totalenletras='';
		}
		}
	/*} catch (KumbiaException $e)
	{
       View::excepcion($e);
    }*/
	}
	/*
	GRABAR LA PROFORMA
	###
	*/
	public function grabar($val=0)
	{
		if(($_POST['cliente_id'])!=0){
		if ($val==1)
		{
			$ING=new Tesingresos();
			if($_POST['id']=='' || $_POST['id']==0){
        	$ingresos = new Tesingresos();
			$ingresos->aclusuarios_id=Auth::get('id');
			$ingresos->id=$_POST['id'];
			}else
			{
			$ingresos=$ING->find_first((int)$_POST['id']);
			}
			if(isset($_POST['serie']))$ingresos->serie=$serie=$_POST['serie'];
			if(isset($_POST['numero']))$ingresos->numero=$_POST['numero'];
			$ingresos->tesdocumentos_id=(int)Session::get('DOC_ID');
			if(isset($_POST['cliente_id']))$ingresos->tesdatos_id=$_POST['cliente_id'];
			if(isset($_POST['monedas']))$ingresos->tesmonedas_id=$_POST['monedas'];
			if(isset($_POST['femision']))$ingresos->femision=$_POST['femision'];
			if(isset($_POST['fvencimiento']))$ingresos->fvencimiento=$_POST['fvencimiento']; else $ingresos->fvencimiento=date("Y-m-d",strtotime($_POST['femision']."+ 30 days"));
			$ingresos->fregistro=date('Y-m-d');
			if(isset($_POST['npedido']))$ingresos->npedido=$_POST['npedido'];
			if(isset($_POST['numeroguia']))$ingresos->numeroguia=$_POST['numeroguia'];
			if(isset($_POST['numerofactura']))$ingresos->numerofactura=$_POST['numerofactura'];
			if(isset($_POST['ordendecompra']))$ingresos->ordendecompra=$_POST['ordendecompra'];
			if(isset($_POST['finiciotraslado']))$ingresos->finiciotraslado=$_POST['finiciotraslado'];
			if(isset($_POST['movimiento']))$ingresos->movimiento=$_POST['movimiento'];
			if(isset($_POST['glosa']))$ingresos->glosa=$_POST['glosa'];
			if(isset($_POST['totalconigv']))$ingresos->totalconigv=$_POST['totalconigv'];
			if(isset($_POST['cuantagastos']))$ingresos->cuantagastos=$_POST['cuantagastos'];
			if(isset($_POST['cuentaporpagar']))$ingresos->cuentaporpagar=$_POST['cuentaporpagar'];
			if(isset($_POST['igv']))$ingresos->igv=$_POST['igv'];
			if(isset($_POST['totalenletras']))$ingresos->totalenletras=$_POST['totalenletras'];
			if(isset($_POST['codigo']))$ingresos->codigo=$_POST['codigo'];
			if(isset($_POST['lote_urdido']))$ingresos->codigo=$_POST['lote_urdido'];
			if(isset($_POST['pr']))$ingresos->pr=$_POST['pr'];else $ingresos->pr='NN';
			$ingresos->estado=1;
			$ingresos->userid=Auth::get('id');
			$ingresos->activo='1';
			$ingresos->userid=Auth::get('id');
			$ingresos->estadoingreso='Editable';
			$ingresos->testipocambios_id=$ingresos->getCambio($_POST['femision']);
			$ingresos->aclusuarios_id=Auth::get('id');
			$ingresos->aclempresas_id=Session::get("EMPRESAS_ID");
			/*
			*/
            if ($ingresos->save())
			{
				/* inicio 
				para la generacion de vauchers
				cuardar en las tablas dependientes a tesingresos como son : teschequesingresos, tesletrasingresos ,tesdetracciones solo cuando el ingreso es de manera directa y no viene de una cancelacion*/
				switch($ingresos->tesdocumentos_id)
				{
					/*LETRAS*/
					case 34: $LETRAS= new Tesletrasingresos();
							 if(!$LETRAS->exists('tesingresos_id='.$ingresos->id)){
							 $LETRAS->tesingresos_id=$ingresos->id;
							 $LETRAS->numeroletra=$ingresos->numero;
							 $LETRAS->numerounico='';
							 $LETRAS->monto=$ingresos->totalconigv;
							 $LETRAS->numerofactura=$ingresos->numerofactura;
							 $LETRAS->estadodeletra='';
							 $LETRAS->estado=1;
							 if($ingresos->movimiento=='LA')$LETRAS->activo=1;
							 $LETRAS->userid=Auth::get('id');
							 $LETRAS->aclempresas_id=Session::get('EMPRESAS_ID');
							 $LETRAS->save();
							 }
					;break;
					/*CHEQUES*/
					case 35: 
					 		 $CHEQUES= new Teschequesingresos();
							 if(!$CHEQUES->exists('tesingresos_id='.$ingresos->id)){
					 		 $CHEQUES->tesingresos_id=$ingresos->id;
					 		 $CHEQUES->monto=$ingresos->totalconigv;
					 		 $CHEQUES->estadocheque='';
					 		 $CHEQUES->numerocheque=$ingresos->numero;
					 		 $CHEQUES->fecha_cobro='';
					 		 if(isset($_POST['tesbancos_id']))$CHEQUES->tesbancos_id=$_POST['tesbancos_id'];
					 		 $CHEQUES->estado=1;
					 		 $CHEQUES->userid=Auth::get('id');
					 		 $CHEQUES->aclempresas_id=Session::get('EMPRESAS_ID');
					 		 $CHEQUES->save();
							 }
					;break;
					default:
					;break; 
				}
				
				
				
				/* fin */
				Session::set("INGRESO_ID",$ingresos->id);
            	//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo un Nuevo ingreso ".Session::get('DOC_NOMBRE')."-{$ingresos->numero} al sistema");
                return Redirect::toAction('respuesta/'.$ingresos->id);
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $i=0;
				 if(Session::has('INGRESO_ID'))$i=Session::get('INGRESO_ID');
				 return Redirect::toAction('respuesta/'.$i);
             }
         }
		}else
		{
			Flash::warning('No se Pudieron Guardar los Datos ...!!!');
			$i=0;
			if(Session::has('INGRESO_ID'))$i=Session::get('INGRESO_ID');
			return Redirect::toAction('respuesta/'.$i);

		}
	}
	
	/*
	GRABAR DETALLE 
	###
	*/
	public function grabarDetalle($val=0)
	{
		try{
			View::select('respuesta');
		$this->id=0;
		if($val!=0)
		{

			$DET=new Tesdetalleingresos();
			if(!$DET->exists('id='.$_POST['id_detalle']))
			{
				$detalle= new Tesdetalleingresos();
			}else{
				$detalle= $DET->find_first((int)$_POST['id_detalle']);
			}
			$detalle->tesingresos_id=Session::get('INGRESO_ID');
			if(isset($_POST['tesunidadesmedidas_id']))$detalle->tesunidadesmedidas_id=$_POST['tesunidadesmedidas_id'];
			if(isset($_POST['colores_id']))$detalle->tescolores_id=$_POST['colores_id'];
			if(isset($_POST['productos_id']))$detalle->tesproductos_id=$_POST['productos_id'];
			if(isset($_POST['pro_descripcion']))$detalle->concepto=$_POST['pro_descripcion'];
			/* la cantidad es = al peso neto*/
			if(isset($_POST['cantidad']))
			{
				$detalle->cantidad=$this->definido($_POST['cantidad']);
			}else{
				if(isset($_POST['pesoneto']))
				{
					$detalle->pesoneto=$this->definido($_POST['pesoneto']);
					$detalle->cantidad=$this->definido($_POST['pesoneto']);
				}
			}
			/**/
			if(isset($_POST['concepto']))$detalle->concepto=$_POST['concepto'];
			if(isset($_POST['lote']))$detalle->lote=$this->definido($_POST['lote']);
			if(isset($_POST['empaque']))$detalle->empaque=$this->definido($_POST['empaque']);
			if(isset($_POST['bobinas']))$detalle->bobinas=$this->definido($_POST['bobinas']);
			if(isset($_POST['pesoneto']))$detalle->pesoneto=$this->definido($_POST['pesoneto']);
			if(isset($_POST['pesobruto']))$detalle->pesobruto=$this->definido($_POST['pesobruto']);
			if(isset($_POST['precio']))$detalle->preciounitario=$_POST['precio'];
			if(isset($_POST['total']))$detalle->importe=$_POST['total'];
			$detalle->userid=Auth::get('id');
			$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
			$detalle->estado=1;
			if($detalle->save()){
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
								$precioS=($detalle->preciounitario/$cam->compra);
						 		break;
						case '0':  //moneda es SOLES
								$precioS=$detalle->preciounitario;
								$precioD=($detalle->preciounitario/$cam->compra);
						 		break;
					}
					
					
				$producto = new Tesproductos();
				if($detalle->tesproductos_id!=0){
				$pr=$producto->find_first((int)$detalle->tesproductos_id);
				$pr->precio=$this->definido($precioS);
				$pr->preciod=$this->definido($precioD);
				$pr->save();
				}
				/*validar solo en el caso de guias*/
				if(Session::get('DOC_ID')==15)
				{
					switch($detalle->getTesproductos()->testipoproductos_id)
					{
						/*PARA GRABAR EL MOV del plegador*/
						case '37': 
							$mov=new Promovimientos();
							$mov->update_all('estado=1','conditions: proplegadores_id='.$detalle->getTesproductos()->getProplegadores()->id);
							if($mov->exists('proplegadores_id='.$detalle->getTesproductos()->getProplegadores()->id.' AND tesingresos_id='.Session::get('INGRESO_ID')))
							{
							$MOV=new Promovimientos();
							$mov=$MOV->find_first('proplegadores_id='.$detalle->getTesproductos()->getProplegadores()->id.' AND tesingresos_id='.Session::get('INGRESO_ID'));
							}else
							{
							$mov=new Promovimientos();
							}
							/* COLOR DE URDIDO*/
							$gl=explode(',',$detalle->getTesingresos()->glosa);
							foreach($gl as $id=>$value)
							{
								if(!empty($value)){
								$cl=explode(':',$value);
								$det[trim($cl[0])]=$cl[1];
								}
							}
							/**/
							$mov->proplegadores_id=$detalle->getTesproductos()->getProplegadores()->id;
							$mov->observacion='Ingreso de plegador';
							$mov->tesingresos_id=Session::get('INGRESO_ID');
							$mov->descripcion=$detalle->getTesingresos()->glosa;
							if(array_key_exists('COLOR',$det))$mov->colorurdido=$det['COLOR'];
							$mov->testsalidas_id='0';
							$mov->estadomov='final';
							$mov->estado='1';
							$mov->activo='1';
							$mov->userid=Auth::get('id');
								if($mov->save())
								{
									$plegador= new Proplegadores();
									$pl=$plegador->find_first((int)$detalle->getTesproductos()->getProplegadores()->id);
									$pl->estadoplegador_id=5;
									$pl->save();
								}
							;break;
						default: break;
					}
				}
				$this->id=$detalle->id;
				unset($_POST);
			}else
			{
				$this->id=0;
			}
		}

		}catch(\Exception $e){
			var_dump($e);
		}
		
		
	}
	/*
	# @serie LA serie de la guia de remision.
	# @numero el numero de la guia de remision. 
	#
	*/
	public function facturaguia($serie=0,$numero=0)
	{
		
		$this->DETALLE=TRUE;
		if($serie==0 && $numero==0)
		{
		$ingresos= new Tesingresos();
		$guia=$ingresos->find_first(Session::get('INGRESO_ID'));
		$guia->update('numeroguia: ');
		$guia->save();
		$this->DETALLE=FALSE;
		$detalles= new Tesdetalleingresos();
		$detalles->delete('tesingresos_id='.Session::get('INGRESO_ID'));
		$this->monedas=$guia->tesmonedas_id;
		}else
		{
		$ingresos= new Tesingresos();
		$guia=$ingresos->find_first('columns: id,tesmonedas_id','conditions: tesdocumentos_id=15 AND aclempresas_id='.Session::get('EMPRESAS_ID').' AND serie="'.$serie.'" AND numero="'.$numero.'"');
		$this->monedas=$guia->tesmonedas_id;
		$m=$guia->tesmonedas_id;
		$detalles= new Tesdetalleingresos();
		$det_guia=$detalles->find('conditions: tesingresos_id='.$guia->id);
			foreach($det_guia as $d)
			{
				$detalle= new Tesdetalleingresos();
				$detalle->tesingresos_id=Session::get('INGRESO_ID');
				$detalle->tesunidadesmedidas_id=$d->tesunidadesmedidas_id;
				$detalle->tescolores_id=$d->tescolores_id;
				$detalle->tesproductos_id=$d->tesproductos_id;
				$detalle->descripcion=$d->descripcion;
				$detalle->cantidad=$d->pesoneto;
				$detalle->lote=$d->lote;
				$detalle->empaque=$d->empaque;
				$detalle->bobinas=$d->bobinas;
				$detalle->pesoneto=$d->pesoneto;
				$detalle->pesobruto=$d->pesobruto;
				switch ($m)
					{
						case 1: //moneda es SOLES
								$precio=$d->getTesproductos()->precio;
						 		break;
						case 2:  //moneda es Dolares
								$precio=$d->getTesproductos()->preciod;
						 		break;
						case 4:  //moneda es SOLES
								$precio=$d->getTesproductos()->precio;
						 		break;
						case 5:  //moneda es Dolares
								$precio=$d->getTesproductos()->preciod;
						 		break;
						case 0:  //moneda es SOLES
								$precio=$d->getTesproductos()->precio;
						 		break;
					}
				$detalle->preciounitario=$precio;
				$detalle->importe=$d->pesoneto*$precio;
				$detalle->userid=Auth::get('id');
				$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
				$detalle->estado=1;
				$detalle->save();
			}
			$this->detalles=$detalles->find('conditions: tesingresos_id='.Session::get('INGRESO_ID'));
		}
	}
	
	public function eliminarDetalle($val=0)
	{
		View::select('respuesta');
		$this->id=0;
		if($val!=0)
		{
			$DET=new Tesdetalleingresos();
			$DET->delete($val);
		}
	}
		
	public function respuesta($id=0)
	{
		View::template(null);
		$this->id=$id;
	}
   	
	public function borrar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $obj  = new Tesingresos();

            if (!$obj ->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Producto");
            } else if ($obj ->borrar()) {
                Flash::valid("EL inventario fue Anulado <b>{$obj->numero}</b> !!!");
                Aclauditorias::add("EL inventario fue Anulado {$obj->numero} del sistema", 'tesingresos');
            } else {
                Flash::warning("No se Pudo Anular el Documento <b>{$tesproductos->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado');
    }
	
	public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $obj  = new Tesingresos();

            if (!$o=$obj ->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Producto");
            } else if ($obj ->delete($id)) {
                Flash::valid("EL Documento Fue Borrado fue Borrado <b>{$o->numero}</b>!!!");
               Aclauditorias::add("EL Documento <b>{$o->numero}</b> fue Borrado del sistema", 'tesproductos');
            } else {
                Flash::warning("No se Pudo Eliminar el Documento <b>{$tesproductos->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado');
    }
	
	public function producto()
	{
		$this->data=[];
		View::template(null);
		$inventario_id=0;
		if(Session::has('INGRESO_ID'))
		{
			$inventario_id=Session::get('INGRESO_ID');
		}
		$q=$_GET['q'];
		$inventario= new Tesdetalleingresos();
		$obj = new Tesproductos();
		$results = $obj->getProductos_tipo($q,0);
		foreach ($results as $value)
		{
			
			$ID=array();
			foreach($value->fields as $field)
			{
				$ID[$field]=$value->$field;
			}
			$id=$ID;
			//$name=$value->nombre;
			switch($value->testipoproductos_id)
			{
				case 37: $opcional=' - '.$value->getProplegadores()->numero;break;
				default: 
				$opcional='';
				break;
			}
			$n=(string)$value->detalle.' ('.$value->codigo.$opcional.')';
			$name=$this->htmlcode($n);
			$json = []();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
		}
	}
	public function medidas()
	{
		$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj= new Tesunidadesmedidas();
		$results = $obj->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' and CONCAT_WS(" ",nombre,detalle) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			$json = [];
			$json['id'] =$value->id;
			$json['name'] = $value->nombre;
			$this->data[] = $json;
		}
	}
	public function cuentasG()
	{$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj=new Subcuentas();
		$results = $obj->find('conditions: CONCAT_WS(" ",codigo,descripcion) like "'.$q.'%"');
		foreach ($results as $value)
		{
			$json = [];
			$json['id'] =$value->codigo;
			$json['name'] = $value->codigo.'<span style="font-size:9px;">('.$value->descripcion.')</span>';
			$this->data[] = $json;
		}
	}
	public function cuentasP()
	{$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj=new Subcuentas();
		$results = $obj->find('conditions: CONCAT_WS(" ",codigo,descripcion) like "'.$q.'%"');
		foreach ($results as $value)
		{
			$json = [];
			$json['id'] =$value->codigo;
			$json['name'] = $value->codigo.'<span style="font-size:9px;">('.$value->descripcion.')</span>';
			$this->data[] = $json;
		}
	}
	
	public function buscarcliente() 
	{
		try{
			$this->data=[];
			$q=$_GET['q'];
			$obj = new Tesdatos();
			$results = $obj->find('columns:id,codigo,razonsocial,ruc,departamento,provincia,distrito,pais,direccion','conditions: testipodatos_id="1" and CONCAT_WS(" ",codigo,razonsocial,ruc,pais) like "%'.$q.'%" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
			foreach ($results as $value)
			{
				$id=$value->id;
				$name=$value->razonsocial."\n ruc: ".$value->ruc." \n(".$value->departamento.' - '.$value->provincia.' - '.$value->distrito.' - '.$value->direccion.')';
				$json = [];
				$json['id'] =$id;
				$json['name'] = $name;
				$this->data[] = $json;
			}
		}catch(\Exception $e){
			var_dump($e);
			exit();
		}
		
    }
	
	private function htmlcode($text)
    {
        $stvarno = array ("<", ">","ñ","Ñ","á","é","í","ó","ú","Á","É","Í","Ó","Ú");
        $zamjenjeno = array ("&lt;","&gt;","&ntilde;","&Ntilde;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;");
        $final = str_replace($stvarno, $zamjenjeno, $text);
        return $final;
    }
    private function clear($text)
    {
        $final = stripslashes(stripslashes($text));
        return $final;
    }
	private function definido($variable)
	{
		if($variable=='undefined')
		{
			$val='0';
		}else
		{
			$val=$variable;
		}
		return $val;
	}
	
	public function colores()
	{$this->data=[];
		View::select('producto');
		$q=strtoupper($_GET['q']);
		$obj= new Tescolores();
		$results = $obj->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND CONCAT_WS(" ",nombre,codigo) like "%'.$q.'%"');
		//$results = $obj->find();
		foreach ($results as $value)
		{
			$json = []();
			$json['id'] =$value->id;
			$json['name'] = $value->nombre.' ';
			$this->data[] = $json;
		}
	}
	/*
	# busca las guia para el ingreso de la factura en caso sea nescesario.
	*/
	public function guiadelafactura()
	{
		$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj= new Tesingresos();
		$results = $obj->find('conditions: tesdocumentos_id=15 AND aclempresas_id='.Session::get('EMPRESAS_ID').' AND CONCAT_WS("-",serie,numero) like "'.$q.'%"');
		foreach ($results as $value)
		{	
		/*
		#valida la existencia de dicho serie-numero en otro ingreso anterior
		*/
			if(!$obj->exists('numeroguia="'.$value->serie.'-'.$value->numero.'"')){
			$json = [];
			$json['id'] =$value->serie.'-'.$value->numero;
			$json['name'] = $value->serie.'-'.$value->numero;
			$this->data[] = $json;
			}
		}
	}
/*
# generar la factura a cancelar desde la guia ingresada
@ id = del ingreso de la guia a generar
*/
public function generarfactura($id=0)
{
	$ingresos= new Tesingresos();
	$ingreso_guia=$ingresos->find_first((int)$id);
	$m=$ingreso_guia->tesmonedas_id;
	$new_ingreso= new Tesingresos();
			$new_ingreso->tesdocumentos_id=7;
			$new_ingreso->tesdatos_id=$ingreso_guia->tesdatos_id;
			$new_ingreso->tesmonedas_id=$ingreso_guia->tesmonedas_id;
			$new_ingreso->femision=$ingreso_guia->femision;
			$new_ingreso->fvencimiento=$ingreso_guia->fvencimiento;
			$new_ingreso->fregistro=date('Y-m-d');
			$new_ingreso->npedido=$ingreso_guia->npedido;
			$new_ingreso->numeroguia=$ingreso_guia->serie.'-'.$ingreso_guia->numero;
			$new_ingreso->ordendecompra=$ingreso_guia->ordendecompra;
			$new_ingreso->finiciotraslado=$ingreso_guia->finiciotraslado;
			$new_ingreso->movimiento=$ingreso_guia->movimiento;
			$new_ingreso->glosa=$ingreso_guia->glosa;
			$new_ingreso->totalconigv=0;
			$new_ingreso->cuantagastos=$ingreso_guia->cuantagastos;
			$new_ingreso->cuentaporpagar=$ingreso_guia->cuentaporpagar;
			$new_ingreso->igv=0;
			$new_ingreso->totalenletras=$ingreso_guia->totalenletras;
			$new_ingreso->pr='NN';
			$new_ingreso->produccion=$ingreso_guia->produccion;/* VAlida la creacion dela factura de las guia en caso de ser para plegador debera crear diferente de NULL*/
			$new_ingreso->estado=1;
			$new_ingreso->userid=Auth::get('id');
			$new_ingreso->activo='1';
			$new_ingreso->testipocambios_id=$ingresos->getCambio($ingreso_guia->femision);
			$new_ingreso->userid=Auth::get('id');
			$new_ingreso->aclusuarios_id=Auth::get('id');
			$new_ingreso->estadoingreso='Pendiente';
			$new_ingreso->aclempresas_id=Session::get("EMPRESAS_ID");
			$new_ingreso->save();
			$ingreso_guia->numerofactura=$new_ingreso->serie.'-'.$new_ingreso->numero;
			$ingreso_guia->estadoingreso="Con factura";
			$ingreso_guia->save();
	$detalles= new Tesdetalleingresos();
	$det_guia=$detalles->find('conditions: tesingresos_id='.$ingreso_guia->id);
			foreach($det_guia as $d)
			{
				$detalle= new Tesdetalleingresos();
				$detalle->tesingresos_id=$new_ingreso->id;
				$detalle->tesunidadesmedidas_id=$d->tesunidadesmedidas_id;
				$detalle->tescolores_id=$d->tescolores_id;
				$detalle->tesproductos_id=$d->tesproductos_id;
				$detalle->descripcion=$d->descripcion;
				if(empty($d->cantidad))$detalle->cantidad=$d->pesoneto;else $detalle->cantidad=$d->cantidad;
				$detalle->pesoneto=$d->pesoneto;
				$detalle->lote=$d->lote;
				$detalle->empaque=$d->empaque;
				$detalle->bobinas=$d->bobinas;
				if(empty($d->pesoneto))$detalle->pesoneto=$d->cantidad;else $detalle->pesoneto=$d->pesoneto;
				$detalle->pesobruto=$d->pesobruto;
				switch ($m)
					{
						case 1: //moneda es SOLES
								$precio=$d->getTesproductos()->precio;
						 		break;
						case 2:  //moneda es Dolares
								$precio=$d->getTesproductos()->preciod;
						 		break;
						case 4:  //moneda es SOLES
								$precio=$d->getTesproductos()->precio;
						 		break;
						case 5:  //moneda es Dolares
								$precio=$d->getTesproductos()->preciod;
						 		break;
						case 0:  //moneda es SOLES
								$precio=$d->getTesproductos()->precio;
						 		break;
					}
				$detalle->preciounitario=$precio;
				$detalle->importe=$d->pesoneto*$precio;
				$detalle->userid=Auth::get('id');
				$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
				$detalle->estado=1;
				$detalle->prorollos_id=$d->prorollos_id;
				$detalle->save();
			}
	$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)7);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
	return Redirect::toAction('cargaringreso/'.$new_ingreso->id);
}

public function varios()
{
		$this->monedas_nombre='';
		$this->totalenletras='';
		$this->DETALLE=FALSE;
		$this->monedas=0;
		$ING=new Tesingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->titulo = Session::get('DOC_NOMBRE');
		//$this->numero=$ING->generarNumero(Session::get('DOC_ID'));
		$this->observacion='';
		$this->pre_descripcion='';
		$this->subt=0.00;
		$this->detalles='';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->totalconigv=00.00;
		$this->igv=00.00;
		$this->cliente_id="0";
		$this->cliente="";
		$this->cuantagastos=NULL;
		$this->cuentaporpagar=NULL;
		$this->cuantagastos_name=NULL;
		$this->cuentaporpagar_name=NULL;
		$this->nombre=NULL;
		if(Session::has('INGRESO_ID')){
		if($ING->exists("id=".(int)Session::get('INGRESO_ID'))){
		$this->DETALLE=TRUE;
		/*
		Cargar los detalles
		*/
		$detalles=new Tesdetalleingresos();
		$ingreso=$ING->find_first((int)Session::get('INGRESO_ID'));
		
		$this->detalles=$detalles->find('conditions: tesingresos_id='.$ingreso->id);
		$this->id=$ingreso->id;
		$this->numero=$ingreso->numero;
		$this->glosa=$ingreso->glosa;
		$this->serie=$ingreso->serie;
		$this->numero=$ingreso->numero;
		$this->codigo=$ingreso->codigo;//fvencimiento
		$this->femision=$ingreso->femision;
		$this->fvencimiento=$ingreso->fvencimiento;
		$this->monedas=$ingreso->tesmonedas_id;
		$this->monedas_nombre=$ingreso->getTesmonedas()->nombre;
		$this->cliente_id=$ingreso->tesdatos_id;
		/*nombre del cliente*/
		$this->nombre=$ingreso->getTesdatos()->razonsocial." ruc: ".$ingreso->getTesdatos()->ruc." (".$ingreso->getTesdatos()->departamento.' - '.$ingreso->getTesdatos()->provincia.' - '.$ingreso->getTesdatos()->distrito.' - '.$ingreso->getTesdatos()->direccion.')';
		/**/
		$this->totalconigv=$ingreso->totalconigv;
		$this->igv=$ingreso->igv;
		$this->paid=$ingreso->igv;
		$this->fregistro=$ingreso->fregistro;
		$this->npedido=$ingreso->npedido;
		$this->numeroguia=$ingreso->numeroguia;
		$this->numerofactura=$ingreso->numerofactura;
		$this->ordendecompra=$ingreso->ordendecompra;
		$this->finiciotraslado=$ingreso->finiciotraslado;
		$this->movimiento=$ingreso->movimiento;
		$this->cuantagastos=$ingreso->cuantagastos;
		$this->cuentaporpagar=$ingreso->cuentaporpagar;
		$this->totalenletras=$ingreso->totalenletras;
		
		$this->subt=$this->totalconigv-$this->igv;
		
		
		if(!empty($this->cuentaporpagar)){
		/* detalle de las cuentas*/
		$cc=new Subcuentas();
		if(!empty($this->cuantagastos)){
		$cg=$cc->find_first('codigo='.$this->cuantagastos);
		$this->cuantagastos_name=$cg->codigo.'<span style=\"font-size:9px;\">('.$cg->descripcion.')</span>';
		}
		if(!empty($this->cuentaporpagar)){
		$cp=$cc->find_first('codigo='.$this->cuentaporpagar);
		$this->cuentaporpagar_name=$cp->codigo.'<span style=\"font-size:9px;\">('.$cp->descripcion.')</span>';
		}
		/**/
		}
		$this->codigo=$ingreso->codigo;
		}else{
		$this->DETALLE=FALSE;
		$this->numero=$ING->generarNumero(Session::get('DOC_ID'));
		$this->observacion='';
		$this->pre_descripcion='';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->monedas=0;
		$this->cliente_id="";
		$this->cliente="";
		$this->totalenletras='';
		}
		}
		
	/*} catch (KumbiaException $e)
	{
       View::excepcion($e);
    }*/
}

public function ver()
	{
		if (Input::hasPost('ingreso')) {
                $ingresos = new Tesingresos(Input::post('ingreso'));
                
                if($ingresos->save()) {
                    /*Para la busqueda de cajas y sacarlas de almacen si son de autosalida*/
					if($ingresos->autosalida=="1")
					{
						$detalles=new Tesdetalleingresos();
						$cajas= new Tescajas();
						$detalle=$detalles->find('columns: id','conditions: tesingresos_id='.$ingresos->id);
						foreach($detalle as $d):
							foreach($cajas->find('conditions: tesdetalleingresos_id='.$d->id) as $c):
								$c->enalmacen="0";
								$c->save();
							endforeach;
						endforeach;
					}
					Flash::valid('El documento fue terminada de ingresar');
                    Aclauditorias::add("El documento fue terminada de ingresar", 'tesingresos');
                    return Redirect::toAction('listado');
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
          }
		$this->DETALLE=FALSE;
		$ING=new Tesingresos();
		$this->cc=new Subcuentas();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		if($ING->exists("id=".(int)Session::get('INGRESO_ID'))){
		$this->DETALLE=TRUE;
		$detalles=new Tesdetalleingresos();
		$ingreso=$ING->find_first((int)Session::get('INGRESO_ID'));
		$this->ingreso=$ingreso;
		$this->detalles=$detalles->find('conditions: tesingresos_id='.$ingreso->id);
		if($ingreso->estadoingreso=='Editable')Flash::valid('Por favor verifique La factura antes de terminar!!!!!');
		}else{
		$this->DETALLE=FALSE;
		$this->numero=$ING->generarNumero(Session::get('DOC_ID'));
		$this->observacion='';
		$this->pre_descripcion='';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->monedas=0;
		$this->cliente_id="";
		$this->cliente="";
		$this->totalenletras='';
		}
		
	}

public function varios_ver()
	{
		if (Input::hasPost('ingreso')) {
                $ingresos = new Tesingresos(Input::post('ingreso'));
                
                if($ingresos->save()) {
                    Flash::valid('El documento fue terminada de ingresar');
                    Aclauditorias::add("El documento fue terminada de ingresar", 'tesingresos');
                    return Redirect::toAction('listado');
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
          }
		$this->DETALLE=FALSE;
		$ING=new Tesingresos();
		$this->cc=new Subcuentas();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		if($ING->exists("id=".(int)Session::get('INGRESO_ID'))){
		$this->DETALLE=TRUE;
		$detalles=new Tesdetalleingresos();
		$ingreso=$ING->find_first((int)Session::get('INGRESO_ID'));
		$this->ingreso=$ingreso;
		$this->detalles=$detalles->find('conditions: tesingresos_id='.$ingreso->id);
		if($ingreso->estadoingreso=='Editable')Flash::valid('Por favor verifique La factura antes de terminar!!!!!');
		}else{
		$this->DETALLE=FALSE;
		$this->numero=$ING->generarNumero(Session::get('DOC_ID'));
		$this->observacion='';
		$this->pre_descripcion='';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->monedas=0;
		$this->cliente_id="";
		$this->cliente="";
		$this->totalenletras='';
		}
		
	}
/*FUNCTION para las guias que no generar factura y pasar la guia a PAGADO */	
public function guia_pagado($id)
{
	$ingresos= new Tesingresos();
	$ingreso_guia=$ingresos->find_first((int)$id);
	$ingreso_guia->estadoingreso='PAGADO';
	$ingreso_guia->save();
	return Redirect::toAction('listado');
}


/*buscar doc*/
public function buscardoc()
{
	$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj= new Tesingresos();
		$results = $obj->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND CONCAT_WS("-",serie,numero,totalconigv,npedido) like "'.$q.'%"');
		foreach ($results as $value)
		{
			$json = []();
			$json['id'] =$value->id;
			$json['name'] = $value->serie.'-'.$value->numero.' T: '.$value->totalconigv.' Pedido: '.$value->npedido;
			$this->data[] = $json;
		}
}


}
