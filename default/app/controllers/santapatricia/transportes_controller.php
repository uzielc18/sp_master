<?php
View::template('spatricia/default');
Load::models('protransportes','testipoproductos','tesproductos','aclempresas');
class TransportesController extends AdminController {
	public $per_page=10;
	public $proveedor=2;
	public $tipoproductos=38; //testipoproductos_id;
	//public $empresa=Session::get('EMPRESAS_ID');
    protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	public function index() {

    }
	public function listado($page=1)
	{
		 try {
		$this->page=$page;
		$protransportes = new Protransportes();
		$this->transportes=$protransportes->paginate('page: '.$page,'per_page: '.$this->per_page,'conditions: estado =1 AND aclempresas_id='.Session::get('EMPRESAS_ID'));
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
		$transportes= new Protransportes();
		$emp= new Aclempresas();
		$this->titulo='Registrar nuevo Transporte';
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
					if (Input::hasPost('protransportes'))
					{
						$dat = new Protransportes(Input::post('protransportes'));
						$dat->tesproductos_id=$pro->id;
						$dat->codigo=$pro->codigo;
						$dat->prefijo=$pro->prefijo;
					 	$dat->aclempresas_id=Session::get('EMPRESAS_ID');
						$dat->activo='1';
						$dat->estado='1';
						if ($dat->save()) {
							Flash::valid('El Transporte Ha Sido Agregado Exitosamente...!!!');
							Aclauditorias::add("Agregó al Transporte {$dat->codigo} al sistema", 'protransportes');
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
	
	public function editar($id,$page=1) {
        try {
			View::select('crear');
			$productos= new Tesproductos();
			$this->page=$page;
			$this->empresa=Session::get('EMPRESAS_ID');
			$transportes= new Protransportes();
			$emp= new Aclempresas();
			$this->titulo='Editar Transporte';
			$this->boton="Editar";
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
			$this->protransportes= $transportes->find_first((int)$id);
			$this->tesproductos= $productos->find_first((int)$this->protransportes->tesproductos_id);
			$this->codigo=$this->tesproductos->codigo;
			if(Input::hasPost('tesproductos'))
			{
				$productos->testipoproductos_id=$this->tipoproductos;
				$productos->update(Input::post('tesproductos'));
				if (Input::hasPost('protransportes')) {
						$transportes->tesproductos_id=$productos->id;
					if ($transportes->update(Input::post('protransportes'))) {
						Flash::valid('La Transporte Ha Sido Actualizado Exitosamente...!!!');
						Aclauditorias::add("Editó al personal {$transportes->codigo}", 'protransportes');
						
						return Redirect::toAction('listado/'.$page);
					} else {
						Flash::warning('No se Pudieron Guardar los Datos...!!!');
						unset($this->protransportes); //para que cargue el $_POST en el form
					}
				} else if (!$this->protransportes) {
					Flash::warning("No existe ningun Maquinas con id '{$id}'");
					return Redirect::redirect($this->module_name.'/personal/listado');
				}
			}
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	
	
	public function activar($id,$page=1) {
        try {
            $id = Filter::get($id, 'digits');
            $dat = new Protransportes();
            if (!$dat->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Maquinas con id '{$id}'");
            }else if ($dat->activar()) {
                Flash::valid("El Transporte<b>{$dat->codigo}</b> Esta ahora <b>Activo</b>...!!!");
                Aclauditorias::add("Colocó al Transporte {$dat->codigo} como activo", 'testproductos');
            } else {
                Flash::warning('No se pudo Activar el Transporte!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$apge);
    }
     public function desactivar($id,$page=1) {
        try {
            $id = Filter::get($id, 'digits');
            $dat = new Protransportes();
            if (!$dat->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Producto");
            }else if ($dat->desactivar()) {
                Flash::valid("El Transporte <b>{$dat->codigo}</b> Esta ahora <b>Inactivo</b>...!!!");
                Aclauditorias::add("Colocó el Transporte {$dat->codigo} como inactivo", 'testproductos');
            } else {
                Flash::warning('No se Pudo Desactivar el Transporte...!!!');
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

            $dat = new Protransportes();

            if (!$dat->find_first($id)) { //si no existe
                Flash::warning("No EL dato a eliminar");
            } else if ($dat->borrar()) {
                Flash::valid("EL Maquinas <b>{$dat->codigo}</b> fué Eliminado...!!!");
                Aclauditorias::add("El Maquinas {$dat->codigo} del sistema", 'protransportes');
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
		$results = $obj->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND CONCAT_WS(" ",codigo,detalle,abr) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			/*  codigo	varchar(10)	Sí 	NULL 	 
				prefijo	varchar(10)	Sí 	NULL 	 
				codigobarras	int(10)	Sí 	NULL 	 
				nombre	varchar(250)	Sí 	NULL 	 
				abr	varchar(10)	Sí 	NULL 	 
				nombrecorto	varchar(50)	Sí 	NULL 	 
				detalle	text	Sí 	NULL 	 
				precio*/
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
	
	public function ver($id=0,$page=1)
	{
        try {
			$this->page=$page;
			$this->estados;
			$this->empresa=Session::get('EMPRESAS_ID');
			$emp= new Aclempresas();
            $id = Filter::get($id, 'digits');
			$dat = new Protransportes();
			$pro= new Tesproductos();
			$this->boton="Editar";
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.$this->empresa);
            $this->protransportes = $dat->find_first($id);
			/*
			Valida la existencia del distrito creado en todo caso le permite editar
			*/
			$this->tesproductos=$pro->find_first($this->protransportes->id);
			$this->titulo='Detalle al Transporte ('.$this->protransportes->codigo.')';
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
	}
	
	
	public function crear_externo($page=1)
	{
	//try {
		$productos= new Tesproductos();
		$this->page=$page;
		$this->empresa=Session::get('EMPRESAS_ID');
		$transportes= new Protransportes();
		$emp= new Aclempresas();
		$this->titulo='Registrar nuevo Transporte';
		$this->boton="Guardar";
		$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
		$this->codigo=$productos->generacodigo();
		if(Input::hasPost('tesproductos'))
		{
				$PRO_DAT=Input::post('tesproductos');
				$pro= $productos->find_first((int)$PRO_DAT['id']);
				
					if (Input::hasPost('protransportes'))
					{
						$dat = new Protransportes(Input::post('protransportes'));
						$dat->tesproductos_id=$pro->id;
						$dat->codigo=$pro->codigo;
						$dat->prefijo=$pro->prefijo;
					 	$dat->aclempresas_id=Session::get('EMPRESAS_ID');
						$dat->activo='1';
						$dat->estado='1';
						if ($dat->save()) {
							Flash::valid('El Transporte Ha Sido Agregado Exitosamente...!!!');
							Aclauditorias::add("Agregó al Transporte {$dat->codigo} al sistema", 'protransportes');
						} else {
							Flash::warning('No se Pudieron Guardar los datos...!!!');
						}
					}
		}
        /*} catch (KumbiaException $e) {
          View::excepcion($e);
        }*/
	}
}
?>