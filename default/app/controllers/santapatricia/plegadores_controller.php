<?php
View::template('spatricia/default');
Load::models('proplegadores','testipoproductos','tesproductos','aclempresas','promovimientos','estadoplegador','tesdocumentos','tessalidas','tesdetallesalidas','tescajas','tesingresos','tescolores','tipoplegadores','protransportes','protransportistas','subcuentas','prodetalletransportes','aclconfiguraciones','prodetallemovimientos');
class PlegadoresController extends AdminController {
	public $per_page=20;
	public $proveedor=2;
	public $tipoproductos=37; //testipoproductos_id;
	//public $empresa=Session::get('EMPRESAS_ID');
    protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	public function index(){

    }
	public function listado($page=1)
	{
		try {
		$this->page=$page;
		$proplegadores = new Proplegadores();
		$this->plegadores=$proplegadores->paginate('page: '.$page,'per_page: '.$this->per_page,'conditions: estado=1','order: fecha_at DESC');
	 	} catch (KumbiaException $e) {
          View::excepcion($e);
        }
    }
	public function crear($page=1)
	{
	//try {
		$productos= new Tesproductos();
		$this->page=$page;
		$this->empresa=Session::get('EMPRESAS_ID');
		$plegadores= new Proplegadores();
		$emp= new Aclempresas();
		$this->titulo='Registrar nuevo Plegador';
		$this->boton="Guardar";
		$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
		$this->codigo=$productos->generacodigo();
             if(Input::hasPost('tesproductos'))
			 {
				 $PRO_DAT=Input::post('tesproductos');
				 if($PRO_DAT['id']==''){
				 $pro=new Tesproductos(Input::post('tesproductos'));
				 }else
				 {
				$pro= $productos->find_first((int)$PRO_DAT['id']);
				 }
				 $pro->activo='1';
				 $pro->estado=1;
				 $pro->aclusuarios_id=Auth::get('id');
				 $pro->testipoproductos_id=$this->tipoproductos;
				 $pro->aclempresas_id=Session::get('EMPRESAS_ID');
				 if($pro->save())
				 {
					if (Input::hasPost('proplegadores'))
					{
						$dat = new Proplegadores(Input::post('proplegadores'));
						$dat->tesproductos_id=$pro->id;
						$dat->estadoplegador_id=1;
						$dat->codigo=$pro->codigo;
						$dat->prefijo=$pro->prefijo;
						$dat->activo=1;
						$dat->estado=1;
						$dat->userid=Auth::get('id');
						if ($dat->save()) {
							Flash::valid('El Plegador Ha Sido Agregado Exitosamente...!!!');
							Aclauditorias::add("Agregó al Plegador {$dat->codigo} al sistema", 'proplegadores');
							return Redirect::toAction('listado/'.$page);
						} else {
							Flash::warning('No se Pudieron Guardar los datos de la repuesto...!!!');
						}
					}
				 }else
				 {
					 Flash::warning('No se Pudo Guardar el producto Guardar los Datos...!!!');
					 return Redirect::toAction('listado/'.$page);
				}
			}
        /*} catch (KumbiaException $e) {
          View::excepcion($e);
        }*/
	}
	
	public function direcciones_clientes($id,$value=0)
	{
		$this->id=$id;$this->value=$value;
		
	}
	public function editar($id,$page=1) {
        try {
			View::select('crear');
			$productos= new Tesproductos();
			$this->page=$page;
			$this->empresa=Session::get('EMPRESAS_ID');
			$plegadores= new Proplegadores();
			$emp= new Aclempresas();
			$this->titulo='Editar Plegador';
			$this->boton="Editar";
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
			$this->proplegadores= $plegadores->find_first((int)$id);
			$this->tesproductos= $productos->find_first((int)$this->proplegadores->tesproductos_id);
			$this->codigo=$this->tesproductos->codigo;
			if(Input::hasPost('tesproductos'))
			{
				$productos->testipoproductos_id=$this->tipoproductos;
				$productos->update(Input::post('tesproductos'));
				if (Input::hasPost('proplegadores')) {
						$plegadores->tesproductos_id=$productos->id;
					if ($plegadores->update(Input::post('proplegadores'))) {
						Flash::valid('La Plegador Ha Sido Actualizado Exitosamente...!!!');
						Aclauditorias::add("Editó al personal {$plegadores->codigo}", 'proplegadores');
						
						return Redirect::toAction('listado/'.$page);
					} else {
						Flash::warning('No se Pudieron Guardar los Datos...!!!');
						unset($this->proplegadores); //para que cargue el $_POST en el form
					}
				} else if (!$this->proplegadores) {
					Flash::warning("No existe ningun Maquinas con id '{$id}'");
					return Redirect::redirect('santapatricia/personal/listado');
				}
			}
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	
	
	public function activar($id,$page=1) {
        try {
            $id = Filter::get($id, 'digits');
            $dat = new Proplegadores();
            if (!$dat->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Maquinas con id '{$id}'");
            }else if ($dat->activar()) {
                Flash::valid("El Plegador<b>{$dat->numero}</b> Esta ahora <b>Activo</b>...!!!");
                Aclauditorias::add("Colocó al Plegador {$dat->numero} como activo", 'testproductos');
            } else {
                Flash::warning('No se pudo Activar el Plegador!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$page);
    }
     public function desactivar($id,$page=1) {
        try {
            $id = Filter::get($id, 'digits');
            $dat = new Proplegadores();
            if (!$dat->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Producto");
            }else if ($dat->desactivar()) {
                Flash::valid("El Plegador <b>{$dat->numero}</b> Esta ahora <b>Inactivo</b>...!!!");
                Aclauditorias::add("Colocó el Plegador {$dat->numero} como inactivo", 'testproductos');
            } else {
                Flash::warning('No se Pudo Desactivar el Plegador...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$page);
    }
	
	public function borrar($id=0,$page=0)
	{
        try {
            $id = Filter::get($id, 'digits');

            $dat = new Proplegadores();

            if (!$dat->find_first($id)) { //si no existe
                Flash::warning("No EL dato a eliminar");
            } else if ($dat->borrar()) {
                Flash::valid("EL Maquinas <b>{$dat->codigo}</b> fué Eliminado...!!!");
                Aclauditorias::add("El Maquinas {$dat->codigo} del sistema", 'proplegadores');
            } else {
                Flash::warning("No se Pudo Eliminar el Maquinas <b>{$dat->codigo}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$page);
    }
	
	public function resultados() 
	{
		$q=$_GET['q'];
		$obj = new Tesproductos();
		$results = $obj->find('conditions: CONCAT_WS(" ",codigo,nombre,nombrecorto,abr) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			$ID=array("id"=>$value->id,"testipoproductos_id"=>$value->testipoproductos_id,"codigo"=>$value->codigo,"prefijo"=>$value->prefijo,"codigobarras"=>$value->codigobarras,"nombre"=>$value->nombre,"abr"=>$value->abr,"nombrecorto"=>$value->nombrecorto,"detalle"=>$value->detalle,"precio"=>$value->precio);
			$id=$ID;
			//$name=$value->nombre;
			$name=$value->nombre.' : '.$value->codigo;
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
		}
    }
	
	public function ver_($id=0,$page=1)/*Falta la vista*/
	{
        try {
			$this->page=$page;
			$this->estados;
			$this->empresa=Session::get('EMPRESAS_ID');
			$emp= new Aclempresas();
            $id = Filter::get($id, 'digits');
			$dat = new Proplegadores();
			$pro= new Tesproductos();
			$this->boton="Editar";
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.$this->empresa);
            $this->proplegadores = $dat->find_first($id);
			/*
			Valida la existencia del distrito creado en todo caso le permite editar
			*/
			$this->tesproductos=$pro->find_first($this->proplegadores->id);
			$this->titulo='Detalle al Plegador ('.$this->proplegadores->codigo.')';
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
	}
	public function cargarsalida($id=0,$url='crearsalida')
	{
		if($id!=0){
		Session::set("SALIDA_ID",$id);
		}else
		{
			Session::delete("SALIDA_ID");
		}
		return Redirect::toAction($url);
	}
	public function cargaringreso($id=0,$url='crearingreso')
	{
		if($id!=0){
		Session::set("INGRESO_ID",$id);
		}else
		{
			Session::delete("INGRESO_ID");
		}
		return Redirect::toAction($url);
	}
	public function salidas($Y='',$m='')
	{
		Session::set('tipo_plegador','1');
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
		salida de plegadores
		*/
		$salidas= new Tessalidas();
		$this->salidas= $salidas->find('conditions: DATE_FORMAT(femision,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND npedido like "PL%" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at DESC');
	}
	public function crearsalida($hilo='')
	{
		$this->hilo=FALSE;
		if($hilo=='hilo')
		{
			$this->hilo=TRUE;
		}
		$this->monedas_nombre='';
		$this->totalenletras='';
		$this->DETALLE=FALSE;
		$this->monedas=0;
		$SALD=new Tessalidas();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->titulo = Session::get('DOC_NOMBRE');
		$this->serie='002';
		$this->numero=$SALD->generarNumero(Session::get('DOC_ID'),'002');
		$this->npedido=$SALD->getNumeropedido('PL');
		$this->observacion='Salida de Plegadores';
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
		$this->titulo_hilo=NULL;
		$this->color=NULL;
		$this->protransportes_id=NULL;
		$this->protransportistas_id=NULL;
		
		if(Session::has('SALIDA_ID')){
		if($SALD->exists("id=".(int)Session::get('SALIDA_ID'))){
		$this->DETALLE=TRUE;
		
		/*
		Cargar los detalles
		*/
		$detalles=new Tesdetallesalidas();
		$salidas=$SALD->find_first((int)Session::get('SALIDA_ID'));
		
		$this->detalles=$detalles->find('conditions: tessalidas_id='.$salidas->id);
		$this->id=$salidas->id;
		$this->numero=$salidas->numero;
		$this->glosa=$salidas->glosa;
		$gl=explode(',',$this->glosa);
		$det=array();
		foreach($gl as $id=>$value)
		{
			if(!empty($value)){
			$cl=explode(':',$value);
			$det[$cl[0]]=$cl[1];
			}
		}
		$this->titulo_hilo=$det['HILOS'];
		$this->color=$det['COLOR'];
		$this->ancho=$det['ANCHO'];
		$this->nhilos=$det['#HILOS'];
		$this->metros=$det['METROS'];
		$this->serie=$salidas->serie;
		$this->numero=$salidas->numero;
		$this->codigo=$salidas->codigo;//fvencimiento
		$this->femision=$salidas->femision;
		$this->fvencimiento=$salidas->fvencimiento;
		$this->monedas=$salidas->tesmonedas_id;
		$this->monedas_nombre=$salidas->getTesmonedas()->nombre;
		$this->cliente_id=$salidas->tesdatos_id;
		$this->direccion=$salidas->direccion_entrega;
		/*nombre del cliente*/
		$this->nombre=$salidas->getTesdatos()->razonsocial." ruc: ".$salidas->getTesdatos()->ruc;
		/**/
		$this->totalconigv=$salidas->totalconigv;
		$this->igv=$salidas->igv;
		$this->paid=$salidas->igv;
		$this->fregistro=$salidas->fregistro;
		$this->npedido=$salidas->npedido;
		$this->numeroguia=$salidas->numeroguia;
		$this->numerofactura=$salidas->numerofactura;
		$this->ordendecompra=$salidas->ordendecompra;
		$this->finiciotraslado=$salidas->finiciotraslado;
		$this->movimiento=$salidas->movimiento;
		$this->cuantagastos=$salidas->cuantagastos;
		$this->cuentaporpagar=$salidas->cuentaporpagar;
		$this->totalenletras=$salidas->totalenletras;
		$this->subt=$this->totalconigv-$this->igv;
		if(!empty($this->cuentaporpagar)){
		/* detalle de las cuentas*/
		$cc=new Subcuentas();
		$cg=$cc->find_first('codigo='.$this->cuantagastos);
		$cp=$cc->find_first('codigo='.$this->cuentaporpagar);
		$this->cuantagastos_name=$cg->codigo.'<span style=\"font-size:9px;\">('.$cg->descripcion.')</span>';;
		$this->cuentaporpagar_name=$cp->codigo.'<span style=\"font-size:9px;\">('.$cp->descripcion.')</span>';;
		/**/
		}
		$detalleT= new Prodetalletransportes();
		if($detalleT->exists('tessalidas_id='.$salidas->id)){
				$tt=$detalleT->find_first('conditions: tessalidas_id='.$salidas->id);
				$this->protransportes_id=$tt->protransportes_id;
				$this->protransportistas_id=$tt->protransportistas_id;
				$this->n_protransportes_id=$tt->getProtransportes()->marca;
				$this->n_protransportistas_id=$tt->getProtransportistas()->nombre;
		}
		$this->codigo=$salidas->codigo;
		
		}else{
		$this->DETALLE=FALSE;
		$this->numero=$ING->generarNumero(Session::get('DOC_ID'));
		$this->observacion='';
		$this->pre_descripcion='';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->monedas=0;
		$this->cliente_id=0;
		$this->cliente="";
		$this->totalenletras='';
		}
		}
	/*} catch (KumbiaException $e)
	{
       View::excepcion($e);
    }*/
	}
	
	public function anular_salida($id=0)
	{
		if($id!=0)
		{
			$SALD=new Tessalidas();
			$salida=$SALD->find_first($id);
			$salida->estadosalida='ANULADO';
			$salida->save();
			$detalles=new Tesdetallesalidas();
			$detll=$detalles->find('conditions: tessalidas_id='.$id);
			foreach($detll as $det)
			{
				$dat = new Proplegadores();
				$dat->find_first('conditions: tesproductos_id='.$det->tesproductos_id);
				$dat->estadoplegador_id=1;
				$dat->save();
				//estado=1;
				
			}
		Flash::valid('GUIA ANULADA');
		return Redirect::toAction('salidas');			
		}else
		{
		 Flash::error('No existe dicha guia');
		 return Redirect::toAction('salidas');	
		}
		
	}
	
	/* PARA SALIDA DE HILOS*/
	public function cajas()
	{
		$this->data='';
		$q=$_GET['q'];
		$obj= new Tescajas();
		$DET= new Tesdetallesalidas();
		$results = $obj->find('conditions: numerodecaja like "'.$q.'%"');
		foreach ($results as $value)
		{
			if(!$DET->exists('empaque='.$value->numerodecaja)){
			$json = array();
			$json['id'] =$value->numerodecaja;
			$json['name'] = $value->numerodecaja;
			$this->data[] = $json;
			}
		}
	}
	public function colores()
	{$this->data='';
		View::select('cajas');
		$q=$_GET['q'];
		$obj= new Tescolores();
		$results = $obj->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND CONCAT_WS(" ",nombre,codigo) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =$value->nombre;
			$json['name'] = $value->nombre.' ';
			$this->data[] = $json;
		}
	}
	public function ingresos($Y='',$m='')
	{
		Session::delete('tipo_plegador');
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
		salida de plegadores
		*/
		$ingresos= new Tesingresos();
		$this->ingresos= $ingresos->find('conditions: DATE_FORMAT(femision,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND pr="PL" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: numero DESC');
		
		$salidas= new Tessalidas();
		$this->salidas= $salidas->find('conditions: ISNULL(estadosalida) AND codigo="P" AND npedido like "PL%" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at DESC');
		
	}
	public function crearingreso($hilo='')
	{
		$this->hilo=FALSE;
		if($hilo=='hilo')
		{
			$this->hilo=TRUE;
		}
		$this->monedas_nombre='';
		$this->totalenletras='';
		$this->DETALLE=FALSE;
		$this->monedas=0;
		$SALD=new Tesingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->titulo = Session::get('DOC_NOMBRE');
		$this->serie='001';
		$this->numero='';
		$this->npedido='';
		$this->observacion='INGRESO DE PLEGADORES';
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
		if($SALD->exists("id=".(int)Session::get('INGRESO_ID'))){
		$this->DETALLE=TRUE;
		/*
		Cargar los detalles
		*/
		$detalles=new Tesdetalleingresos();
		$salidas=$SALD->find_first((int)Session::get('INGRESO_ID'));
		
		$this->detalles=$detalles->find('conditions: tesingresos_id='.$salidas->id);
		$this->id=$salidas->id;
		$this->numero=$salidas->numero;
		$this->glosa=$salidas->glosa;
		$gl=explode(',',$this->glosa);
		$det=array();
		$this->gl=$gl;
		foreach($gl as $id=>$value)
		{
			if(!empty($value)){
			$cl=explode(':',$value);
			$det[trim($cl[0])]=$cl[1];
			}
		}
		if(array_key_exists('HILOS',$det))$this->titulo_hilo=$det['HILOS'];
		if(array_key_exists('COLOR',$det))$this->color=$det['COLOR'];
		if(array_key_exists('ANCHO',$det))$this->ancho=$det['ANCHO'];
		if(array_key_exists('#HILOS',$det))$this->nhilos=$det['#HILOS'];
		if(array_key_exists('METROS',$det))$this->metros=$det['METROS'];
		if(array_key_exists('PLEGADORES',$det))$this->plegadores=$det['PLEGADORES'];
		if(array_key_exists('T.KILOS',$det))$this->totalkilos=$det['T.KILOS'];
		$this->serie=$salidas->serie;
		$this->numero=$salidas->numero;
		$this->codigo=$salidas->codigo;//fvencimiento
		$this->femision=$salidas->femision;
		$this->fvencimiento=$salidas->fvencimiento;
		$this->monedas=$salidas->tesmonedas_id;
		$this->monedas_nombre=$salidas->getTesmonedas()->nombre;
		$this->cliente_id=$salidas->tesdatos_id;
		/*nombre del cliente*/
		$this->nombre=$salidas->getTesdatos()->razonsocial." ruc: ".$salidas->getTesdatos()->ruc." (".$salidas->getTesdatos()->departamento.' - '.$salidas->getTesdatos()->provincia.' - '.$salidas->getTesdatos()->distrito.' - '.$salidas->getTesdatos()->direccion.')';
		/**/
		$this->totalconigv=$salidas->totalconigv;
		$this->igv=$salidas->igv;
		$this->paid=$salidas->igv;
		$this->fregistro=$salidas->fregistro;
		$this->npedido=$salidas->npedido;
		$this->numeroguia=$salidas->numeroguia;
		$this->numerofactura=$salidas->numerofactura;
		$this->ordendecompra=$salidas->ordendecompra;
		$this->finiciotraslado=$salidas->finiciotraslado;
		$this->movimiento=$salidas->movimiento;
		$this->cuantagastos=$salidas->cuantagastos;
		$this->cuentaporpagar=$salidas->cuentaporpagar;
		$this->totalenletras=$salidas->totalenletras;
		
		$this->subt=$this->totalconigv-$this->igv;
		if(!empty($this->cuentaporpagar)){
		/* detalle de las cuentas*/
		$cc=new Subcuentas();
		$cg=$cc->find_first('codigo='.$this->cuantagastos);
		$cp=$cc->find_first('codigo='.$this->cuentaporpagar);
		$this->cuantagastos_name=$cg->codigo.'<span style=\"font-size:9px;\">('.$cg->descripcion.')</span>';;
		$this->cuentaporpagar_name=$cp->codigo.'<span style=\"font-size:9px;\">('.$cp->descripcion.')</span>';;
		/**/
		}
		$this->codigo=$salidas->codigo;
		
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
	public function veringreso($hilo='')
	{
		$this->hilo=FALSE;
		if($hilo=='hilo')
		{
			$this->hilo=TRUE;
		}
		$this->DETALLE=FALSE;
		$this->monedas=0;
		$SALD=new Tesingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->titulo = Session::get('DOC_NOMBRE');
		if(Session::has('INGRESO_ID')){
			if($SALD->exists("id=".(int)Session::get('INGRESO_ID'))){
			$this->DETALLE=TRUE;
			/*
			Cargar los detalles
			*/
			$detalles=new Tesdetalleingresos();
			$ingresos=$SALD->find_first((int)Session::get('INGRESO_ID'));
			$this->detalles=$detalles->find('conditions: tesingresos_id='.$ingresos->id);
			$this->id=$ingresos->id;
			$this->numero=$ingresos->numero;
			$this->glosa=$ingresos->glosa;
			$gl=explode(',',$this->glosa);
			$det=array();
			$this->gl=$gl;
			foreach($gl as $id=>$value)
			{
				if(!empty($value)){
				$cl=explode(':',$value);
				$det[trim($cl[0])]=$cl[1];
				}
			}
			if(array_key_exists('HILOS',$det))$this->titulo_hilo=$det['HILOS'];
			if(array_key_exists('COLOR',$det))$this->color=$det['COLOR'];
			if(array_key_exists('ANCHO',$det))$this->ancho=$det['ANCHO'];
			if(array_key_exists('#HILOS',$det))$this->nhilos=$det['#HILOS'];
			if(array_key_exists('METROS',$det))$this->metros=$det['METROS'];
			if(array_key_exists('PLEGADORES',$det))$this->plegadores=$det['PLEGADORES'];
			if(array_key_exists('T.KILOS',$det))$this->totalkilos=$det['T.KILOS'];
			$this->serie=$ingresos->serie;
			$this->numero=$ingresos->numero;
			$this->codigo=$ingresos->codigo;//fvencimiento
			$this->femision=$ingresos->femision;
			$this->fvencimiento=$ingresos->fvencimiento;
			$this->monedas=$ingresos->tesmonedas_id;
			$this->monedas_nombre=$ingresos->getTesmonedas()->nombre;
			$this->cliente_id=$ingresos->tesdatos_id;
			/*nombre del cliente*/
			$this->nombre=$ingresos->getTesdatos()->razonsocial." ruc: ".$ingresos->getTesdatos()->ruc." (".$ingresos->getTesdatos()->departamento.' - '.$ingresos->getTesdatos()->provincia.' - '.$ingresos->getTesdatos()->distrito.' - '.$ingresos->getTesdatos()->direccion.')';
			/**/
			$this->totalconigv=$ingresos->totalconigv;
			$this->igv=$ingresos->igv;
			$this->paid=$ingresos->igv;
			$this->fregistro=$ingresos->fregistro;
			$this->npedido=$ingresos->npedido;
			$this->numeroguia=$ingresos->numeroguia;
			$this->numerofactura=$ingresos->numerofactura;
			$this->ordendecompra=$ingresos->ordendecompra;
			$this->finiciotraslado=$ingresos->finiciotraslado;
			$this->movimiento=$ingresos->movimiento;
			$this->cuantagastos=$ingresos->cuantagastos;
			$this->cuentaporpagar=$ingresos->cuentaporpagar;
			$this->totalenletras=$ingresos->totalenletras;
			
			$this->subt=$this->totalconigv-$this->igv;
				if(!empty($this->cuentaporpagar)){
				/* detalle de las cuentas*/
				$cc=new Subcuentas();
				$cg=$cc->find_first('codigo='.$this->cuantagastos);
				$cp=$cc->find_first('codigo='.$this->cuentaporpagar);
				$this->cuantagastos_name=$cg->codigo.'<span style=\"font-size:9px;\">('.$cg->descripcion.')</span>';;
				$this->cuentaporpagar_name=$cp->codigo.'<span style=\"font-size:9px;\">('.$cp->descripcion.')</span>';;
				/**/
				}
			$this->codigo=$ingresos->codigo;
			/* Movimiento del Plegador*/
			$movimientos= new Promovimientos();
			$this->movimiento=$movimientos->find_first('conditions: tesingresos_id='.(int)$ingresos->id);
			$detallesmovimientos=new Prodetallemovimientos();
			$this->detalles_mov=$detallesmovimientos->find('conditions: promovimientos_id='.(int)$this->movimiento->id);
			}
		}
	}
	/*Ingresar los movimientos de un hilo cuando el plegador regrsa*/
	public function ingresar_mov($id)
	{
		if(Input::hasPost('detallemov'))
		{
			$detalle = new Prodetallemovimientos(Input::post('detallemov'));
			$detalle->estado=1;
			$detalle->aclusuarios_id=Auth::get('id');
			$detalle->save();
		}
		$detalle = new Prodetallemovimientos();
		$this->detalles_mov=$detalle->find('conditions: promovimientos_id='.(int)$id);
	}
	
	public function plegadoresmov()
	{
		$plegadores = new Proplegadores();
		$this->plegadores=$plegadores->plconmovimientos();
	}
	
	public function seguimiento_plegadores()
	{
		$plegadores = new Proplegadores();
		$this->maquinas=$plegadores->getMaquinas();
		$this->plegadores=$plegadores->getSeguimientos();
	}
	public function reporte_plegadores()
	{
		$plegadores = new Proplegadores();
		$this->plegadores=$plegadores->plreporte();
	}
	public function vermovimientos($numero=0,$Y='')
	{
		$this->id=$numero;
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$movimientos= new Promovimientos();
		$plegadores= new Proplegadores();
		/*DATE_FORMAT(femision,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND */
		$this->plegador=$plegadores->find_first('conditions: numero='.$numero);
		$this->movimientos=$movimientos->find('conditions: DATE_FORMAT(fecha_at,"%Y")="'.$anio.'" AND estado=1 AND proplegadores_id='.$this->plegador->id);
	}
public function buscarplegador($mov='S',$tipo_pegador=0)
{
	$this->data='';
	$inventario_id=0;
	$q=$_GET['q'];
	if($mov=='S')
	{
		/*para validar el origen de la consulta*/
		if(Session::has('SALIDA_ID'))
		{
			$inventario_id=Session::get('SALIDA_ID');
		}
		$tipo_pegador=(int)Session::get('tipo_plegador');
		if($tipo_pegador==1)$lik=" AND pares='-'";if($tipo_pegador==2)$lik=" AND pares !='-'";
		$inventario= new Tesdetallesalidas();
		$campo='tessalidas_id';
		$estado='1';
	/**/
	}
	if($mov=='I')
	{
		if(Session::has('INGRESO_ID'))
		{
			$inventario_id=Session::get('INGRESO_ID');
		}
		$inventario= new Tesdetalleingresos();
		$campo='tesingresos_id';
		$estado='4';
	/**/
	}
	$obj = new Tesproductos();
	$plegador= new Proplegadores();
	$columns='tesproductos.' . join(',tesproductos.', $obj->fields);
	$results = $obj->find('columns: '.$columns,'conditions: tesproductos.testipoproductos_id=37 AND tesproductos.estado=1 and tesproductos.aclempresas_id='.Session::get('EMPRESAS_ID').' and CONCAT_WS(" ",tesproductos.nombre,tesproductos.detalle,tesproductos.codigo) like "%'.$q.'%"','join: INNER JOIN  `proplegadores` AS pp ON pp.tesproductos_id = tesproductos.id'.$lik);
	foreach ($results as $value)
	{
		if(!$inventario->exists($campo.'='.$inventario_id.' AND tesproductos_id='.$value->id))
		{
			if($plegador->exists('tesproductos_id='.$value->id.' AND estadoplegador_id='.$estado))
			{
				$ID=array();
				foreach($value->fields as $field)
				{
					$ID[$field]=$value->$field;
				}
				
				//$name=$value->nombre;
				switch($value->testipoproductos_id)
				{
					case 37: $opcional=' - '.$value->getProplegadores()->numero;$ID['NUMERO']=$value->getProplegadores()->numero;$ID['PESO']=$value->getProplegadores()->peso;break;
					default: 
					$opcional='';
					break;
				}
				$id=$ID;
				$n=$value->detalle.' ('.$value->codigo.$opcional.')';
				$name=$this->clear($this->htmlcode($n));
				$json = array();
				$json['id'] =$id;
				$json['name'] = $name;
				$this->data[] = $json;
			}
		}
	}
}

public function titulo_urdido()
{
	$this->data='';
		View::select('buscarplegador');
		$q=$_GET['q'];
		$obj= new Tesproductos();
		$results = $obj->getProductos_tipo($q,3);
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =(string)$value->nombrecorto;
			$json['name'] = (string)$value->nombrecorto;
			$this->data[] = $json;
		}
}
/*
#
# @id el id de la guia de salida que se tiene que ingresar.
#
*/
public function generaringreso($id)
{
	$salidas= new Tessalidas();
	$dsalidas=new Tesdetallesalidas();
	$salida=$salidas->find_first((int)$id);
	$new_ingreso= new Tesingresos();
			$new_ingreso->tesdocumentos_id=15;
			$new_ingreso->tesdatos_id=$salida->tesdatos_id;
			$new_ingreso->tesmonedas_id=$salida->tesmonedas_id;
			$new_ingreso->femision=$salida->femision;
			$new_ingreso->fvencimiento=$salida->fvencimiento;
			$new_ingreso->fregistro=date('Y-m-d');
			$new_ingreso->npedido=$salida->npedido;
			$new_ingreso->numeroguia='';
			$new_ingreso->ordendecompra=$salida->serie.'-'.$salida->numero;
			$new_ingreso->finiciotraslado=$salida->finiciotraslado;
			$new_ingreso->movimiento=$salida->movimiento;
			$new_ingreso->glosa=$salida->glosa.',PLEGADORES:';
			$new_ingreso->pr='PL';
			$new_ingreso->estado=1;
			$new_ingreso->produccion="0";
			$new_ingreso->userid=Auth::get('id');
			$new_ingreso->activo='1';
			$new_ingreso->userid=Auth::get('id');
			$new_ingreso->aclusuarios_id=Auth::get('id');
			$new_ingreso->estadoingreso='Pendiente';
			$new_ingreso->aclempresas_id=Session::get("EMPRESAS_ID");
			$new_ingreso->save();
			Session::set('INGRESO_ID',$new_ingreso->id);
	$salida->codigo='T';
	$salida->save();
	$det_guia=$dsalidas->find('conditions: tessalidas_id='.$salida->id);
			foreach($det_guia as $d)
			{
				$detalle= new Tesdetalleingresos();
				$detalle->tesingresos_id=$new_ingreso->id;
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
				$detalle->userid=Auth::get('id');
				$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
				$detalle->estado=1;
				$detalle->save();
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
						$mov->proplegadores_id=$detalle->getTesproductos()->getProplegadores()->id;
						$mov->observacion='Ingreso de plegador';
						$mov->tesingresos_id=Session::get('INGRESO_ID');
						$mov->descripcion=$detalle->getTesingresos()->glosa;
						$mov->colorurdido=$detalle->getTescolores()->nombre;
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
	$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)15);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
	return Redirect::toAction('cargaringreso/'.$new_ingreso->id);
	
}
public function buscarhilo()
{	$this->data='';
		View::select('buscarplegador');
		$q=$_GET['q'];
		$obj= new Tesproductos();
		$results = $obj->getProductos_tipo($q,3);
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =(string)$value->id;
			$json['name'] = (string)$value->nombrecorto;
			$this->data[] = $json;
		}
}

/*Private function*/
private function htmlcode($text)
    {
        $stvarno = array ("<", ">");
        $zamjenjeno = array ("&lt;","&gt;");
        $final = str_replace($stvarno, $zamjenjeno, $text);
        return $final;
    }
    private function clear($text)
    {
        $final = stripslashes(stripslashes( $text));
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
/*Ver Salida para imprimir*/
public function versalida($hilo='')
	{
		$this->hilo=FALSE;
		if($hilo=='hilo')
		{
			$this->hilo=TRUE;
		}
		
		$this->DETALLE=FALSE;
		$this->monedas=0;
		$SALD=new Tessalidas();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		if(Session::has('SALIDA_ID')){
		if($SALD->exists("id=".(int)Session::get('SALIDA_ID'))){
		$this->DETALLE=TRUE;
		/*
		Cargar los detalles
		*/
		$detalles=new Tesdetallesalidas();
		$salidas=$SALD->find_first((int)Session::get('SALIDA_ID'));
		
		$this->detalles=$detalles->find('conditions: tessalidas_id='.$salidas->id);
		$this->id=$salidas->id;
		$this->glosa=$salidas->glosa;
		$gl=explode(',',$this->glosa);
		$det=array();
		foreach($gl as $id=>$value)
		{
			if(!empty($value)){
			$cl=explode(':',$value);
			$det[$cl[0]]=$cl[1];
			}
		}
		$this->titulo_hilo=$det['HILOS'];
		$this->color=$det['COLOR'];
		$this->ancho=$det['ANCHO'];
		$this->nhilos=$det['#HILOS'];
		$this->metros=$det['METROS'];
		$this->serie=$salidas->serie;
		$this->numero=$salidas->numero;
		$this->codigo=$salidas->codigo;//fvencimiento
		$this->femision=$salidas->femision;
		$this->fvencimiento=$salidas->fvencimiento;
		$this->monedas=$salidas->tesmonedas_id;
		$this->monedas_nombre=$salidas->getTesmonedas()->nombre;
		$this->cliente_id=$salidas->tesdatos_id;
		/*nombre del cliente*/
		$this->nombre=$salidas->getTesdatos()->razonsocial;
		$this->ruc=$salidas->getTesdatos()->ruc;
		$this->ubigeo=$salidas->getTesdatos()->departamento.' - '.$salidas->getTesdatos()->provincia.' - '.$salidas->getTesdatos()->distrito;
		$this->direccion=$salidas->getTesdatos()->direccion;
		$this->codigo_ant=$salidas->getTesdatos()->codigo_ant;
		/**/
		$this->totalconigv=$salidas->totalconigv;
		$this->igv=$salidas->igv;
		$this->paid=$salidas->igv;
		$this->fregistro=$salidas->fregistro;
		$this->npedido=$salidas->npedido;
		$this->numeroguia=$salidas->numeroguia;
		$this->numerofactura=$salidas->numerofactura;
		$this->ordendecompra=$salidas->ordendecompra;
		$this->finiciotraslado=$salidas->finiciotraslado;
		$this->movimiento=$salidas->movimiento;
		$this->cuantagastos=$salidas->cuantagastos;
		$this->cuentaporpagar=$salidas->cuentaporpagar;
		$this->totalenletras=$salidas->totalenletras;
		$this->estadosalida=$salidas->estadosalida;
		$this->subt=$this->totalconigv-$this->igv;
		if(!empty($this->cuentaporpagar)){
		/* detalle de las cuentas*/
		$cc=new Subcuentas();
		$cg=$cc->find_first('codigo='.$this->cuantagastos);
		$cp=$cc->find_first('codigo='.$this->cuentaporpagar);
		$this->cuantagastos_name=$cg->codigo.'<span style=\"font-size:9px;\">('.$cg->descripcion.')</span>';;
		$this->cuentaporpagar_name=$cp->codigo.'<span style=\"font-size:9px;\">('.$cp->descripcion.')</span>';;
		/**/
		}
		$this->codigo=$salidas->codigo;
		
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.$salidas->id);
		}
		}
	/*} catch (KumbiaException $e)
	{
       View::excepcion($e);
    }*/
	}
	public function versalidaguiaventa($hilo='')
	{
		
	}
	public function versalidaguiatransf($hilo='')
	{
		
	}
	
	public function versalidafactventa($hilo='')
	{
		
	}
	
	public function letras($hilo='')
	{
		
	}
public function transfdevol($hilo='')
	{
		
	}

public function terminar($url='ingresos')
{
	if($url=='ingresos')
	{
		$id=Session::get('INGRESO_ID');
		$ingresos= new Tesingresos();
		$ingreso= $ingresos->find_first((int)$id);
		$ingreso->estadoingreso='Pendiente';
		$ingreso->save();
	}else
	{
		$id=Session::get('SALIDA_ID');
		$salidas= new Tessalidas();
		$salida= $salidas->find_first((int)$id);
		$salida->save();
		$salida->estadosalida='Pendiente';
	}	
	return Redirect::toAction($url);
}

public function cambiodetipoplegador($op=0)
{
	View::select('index');
	if($op!=0){
	Session::set('tipo_plegador',$op);
	}
}

public function ver_ficha($Y='',$m='')
{
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	/*validar el ultimo id_guia_ultimo_ingreso_plegador de la tabla de configuraciones*/
	$conf= new Aclconfiguraciones();
	$con=$conf->find_first('conditions: variable="id_guia_ultimo_ingreso_plegador"');
	if(empty($con->valor))$id_guia=0;else $id_guia=$con->valor;
	$ingresos= new Tesingresos();
	$this->ingresos= $ingresos->getLista_guias(0,$anio.'-'.$mes_activo);

}

function actualizar_id_guia_ultimo_ingreso_plegador($id_guia)
{
	View::select('index');
	$conf= new Aclconfiguraciones();
	$con=$conf->find_first('conditions: variable="id_guia_ultimo_ingreso_plegador"');
	$con->valor=$id_guia;
	$con->save();
}

}
?>