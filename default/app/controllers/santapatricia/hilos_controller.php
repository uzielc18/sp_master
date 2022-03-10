<?php 
View::template('spatricia/default');
Load::models('tesproductos','testipoproductos','teslineaproductos','tesdocumentos','tessalidas','prodetalletransportes','tescajas','tesdetallesalidas','tesdetalleingresos');
class HilosController extends AppController
{
	public $per_page=30;
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	public function index($lineas_id=3,$tipos_id=0)
	{
		$lineas= new Teslineaproductos();
		$tipos= new Testipoproductos();
		$productos = new Tesproductos();
		$this->linea=$lineas->find_first((int)$lineas_id);
		$this->id=$lineas_id;
		$this->tipos_id=$tipos_id;
		if($tipos_id==0){
			$this->tipos=$tipos->find('conditions: estado=1 and activo=1 and teslineaproductos_id='.$lineas_id);
			$this->PR=$productos;
		}else
		{
			$this->productos=$productos->find('conditions: estado=1 and activo=1 and testipoproductos_id='.$tipos_id);
			$this->tipo=$tipos->find_first((int)$tipos_id);
		}
	}
	public function listado($pag= 1) {
        try {
            $testproductos = new Tesproductos();
            $this->tesproductos = $testproductos->paginate("page: $pag","per_page: $this->per_page","conditions: estado=1 and aclempresas_id=".Session::get("EMPRESAS_ID"),'order: testipoproductos_id desc');
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	
	public function crear($tipo_id=0) {
		try {
		$this->tipo_id=$tipo_id;
        if($tipo_id!=0)
		{
			$tipo=new Testipoproductos();
			$this->tipos=$tipo->find_first('columns: id,teslineaproductos_id,nombre,abr','conditions: id='.(int)$tipo_id);
		}
		$this->titulo = 'Crear Productos';
		$productos = new Tesproductos();
		$this->codigo=$productos->generacodigo();
        
            if (Input::hasPost('testproductos')) {
                $testproductos = new Tesproductos(Input::post('testproductos'));
				$testproductos->estado=1;
				$testproductos->userid=Auth::get('id');
				$testproductos->activo='1';
				$testproductos->aclusuarios_id=Auth::get('id');
				$testproductos->aclempresas_id=Session::get("EMPRESAS_ID");
                if ($testproductos->save()) {
                    Flash::valid('EL producto fué agregada Exitosamente...!!!');
			        /*Auditorias*/
                    Aclauditorias::add("Agregó producto {$testproductos->nombre} al sistema,tesproductos->id={$testproductos->id}",'tesproductos');
					/*Fin de Auditorias*/
                    return Redirect::toAction('crear/'.$tipo_id);
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function editar($id,$lineas_id=0,$tipos_id=0) {
        $this->titulo = 'Editar Productos';
        try {
            View::select('crear');
			$this->tipo_id=$tipos_id;
			$this->tipos_id=$tipos_id;
			$this->lineas_id=$lineas_id;
			$tipo=new Testipoproductos();
			$this->tipos=$tipo->find_first('columns: id,teslineaproductos_id,nombre,abr','conditions: id='.(int)$tipos_id);
            $testproductos = new Tesproductos();
            $this->testproductos = $testproductos->find_first($id);
			$this->codigo=$this->testproductos->codigo;

            if (Input::hasPost('testproductos')) {
					$testproductos->userid=Auth::get('id');
					$testproductos->aclempresas_id=Session::get("EMPRESAS_ID");
                if ($testproductos->update(Input::post('testproductos'))) {
                    Flash::valid('El producto fué actualizada Exitosamente...!!!');
			        /*Auditorias*/
                    Aclauditorias::add("Edito producto {$testproductos->nombre} al sistema,tesproductos->id={$testproductos->id}",'tesproductos');
					/*Fin de Auditorias*/
                    //return Redirect::toAction('index/'.$lineas_id.'/'.$tipos_id);
					return Redirect::toAction('none');
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->testproductos); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
   	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $testproductos = new Tesproductos();
            if (!$testproductos->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Area con id '{$id}'");
            }else if ($testproductos->activar()) {
                Flash::valid("El Producto<b>{$testproductos->nombre}</b> Esta ahora <b>Activo</b>...!!!");
			        /*Auditorias*/
                    Aclauditorias::add("Colo el producto {$testproductos->nombre} como activo al sistema,tesproductos->id={$testproductos->id}",'tesproductos');
					/*Fin de Auditorias*/
            } else {
                Flash::warning('No se pudo Activar el Producto!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado');
    }
     public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $testproductos = new Tesproductos();
            if (!$testproductos->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Producto");
            }else if ($testproductos->desactivar()) {
                Flash::valid("El Producto <b>{$testproductos->nombre}</b> Esta ahora <b>Inactivo</b>...!!!");
			        /*Auditorias*/
                    Aclauditorias::add("Colo el producto {$testproductos->nombre} como inactivo al sistema,tesproductos->id={$testproductos->id}",'tesproductos');
					/*Fin de Auditorias*/
            } else {
                Flash::warning('No se Pudo Desactivar el Producto...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado');
    }
	public function borrar($id,$lineas_id=0,$tipos_id=0) {
        try {
            $id = Filter::get($id, 'digits');

            $tesproductos = new Tesproductos();

            if (!$tesproductos->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Producto");
            } else if ($tesproductos->borrar()) {
                Flash::valid("Producto <b>{$tesproductos->nombre}</b> fué Eliminado...!!!");
			        /*Auditorias*/
                    Aclauditorias::add("Colo Borro el producto {$testproductos->nombre} del sistema,tesproductos->id={$testproductos->id}",'tesproductos');
					/*Fin de Auditorias*/
            } else {
                Flash::warning("No se Pudo Eliminar el Prodcuto <b>{$tesproductos->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('index/'.$lineas_id.'/'.$tipos_id);
    }
	public function tipoproductos($id=0)
	{
		$id = Filter::get($id, 'digits');
		$this->tipoproductos=new Testipoproductos();
		$this->id=$id;
	}
	/*
	recibe el id del tipo de producto a listarse
	*/
	public function listadoportipo($id=0)
	{
		$tesproductos = new Tesproductos();
		$id = Filter::get($id, 'digits');
		$this->productos = $tesproductos->find("conditions: estado=1 and aclempresas_id=".Session::get("EMPRESAS_ID").' and testipoproductos_id='.$id,'order: nombre asc');
	}
	public function none()
	{
		
	}
	public function actualizar()
	{
		if(1==0){
		$val= new Tesproductos();
		if($val->actualizarP()){
		return Redirect::toAction('listado');
		}
		}
	}
	
	public function salidas()
	{
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
		$this->salidas= $salidas->find('conditions: npedido like "HL%" AND aclempresas_id='.Session::get("EMPRESAS_ID").' AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at DESC');
		Session::delete("SALIDA_ID");
	}
	public function crearsalidas()
	{
		$SALD=new Tessalidas();
		$this->salida['serie']='002';
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'002');
		$this->salida['npedido']=$SALD->getNumeropedido('HL');
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
				
			        /*Auditorias*/
                    Aclauditorias::add("Creo una Salida tipo (Hilo), numero = {$salidas->serie}-{$salidas->numero},Tessalidas->id={$salidas->id}",'Tessalidas');
					/*Fin de Auditorias*/
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
	public function agregardetalles($id_t_p=0,$pag=1)
	{
		$salidas= new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$cajas=new Tescajas();
		$tipos=new Testipoproductos();
	    $this->tipos=$tipos->find('conditions: teslineaproductos_id=3');
		$this->id_t_p=$id_t_p;
		$this->cajas=$cajas->getStokdecajas($id_t_p,$pag);
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
				$detalleT= new Prodetalletransportes(Input::post('prodetalletransportes'));
				$detalleT->tessalidas_id=$salidas->id;
				$detalleT->numero=$salidas->numero;
				$detalleT->serie=$salidas->serie;
				$detalleT->fechatraslado=$salidas->finiciotraslado;
				$detalleT->save();
				
				Session::set("SALIDA_ID",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
			        /*Auditorias*/
                    Aclauditorias::add("Colo el producto {$testproductos->nombre} como inactivo al sistema,tesproductos->id={$testproductos->id}",'tesproductos');
					/*Fin de Auditorias*/
				
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
/*REPORTE DE HILO POR CLIENTE Y FECHA DE COMPRA Y PRECIO*/

	public function hiloprecios()
	{
		$productos = new Tesproductos();
		$this->PP=$productos->getLineap_json(3);
		$this->CC=$productos->getColores_json();
	}
	public function ver_hilo()
	{
		$a=Input::post('local_producto');
		$b=Input::post('local_color');
		$detalles_ingresos=new Tesdetalleingresos();
		$this->precios=$detalles_ingresos->getPrecios($a,$b);
		
	}
	public function hiloutilizado()
	{
		$productos = new Tesproductos();
		$this->LL=$productos->getLotesXfechas_json();
	}
	public function ver_hiloutilizado($id)
	{
		$salidas = new Tessalidas();
		$productos= new Tesproductos();
		$this->hilos=$salidas->getLotesdehilos($id);
		$this->producto=$productos->find_first((int)$id);
		
	}
	public function hiloutilizado_detalle($hilo_id)
	{
		$lote=$_POST['lote'];
		$salidas = new Tessalidas();
		$this->hilos=$salidas->getDetalle_utilizacion($lote,$hilo_id);
	}
	

}
?>