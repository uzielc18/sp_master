<?php
View::template('spatricia/default');
Load::models('promaquinas','testipoproductos','tesproductos','aclempresas','proubicacionmaquina');
class MaquinasController extends AdminController {
	public $per_page=10;
	public $proveedor=2;
	//public $empresa=Session::get('EMPRESAS_ID');
    protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
public function listado($page=1)
	{
		 try {
		/*
		Se debe saber el id de la empresa para poder ver sus trabajadores Santa Patricia es =1 y Santa Carmela = 2.
		*/
		$this->page=$page;
		$promaquinas = new Promaquinas();
		$this->maquinas=$promaquinas->paginate('page: '.$page,'per_page: '.$this->per_page,'conditions: estado=1 AND aclempresas_id='.Session::get('EMPRESAS_ID'));
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
		$maquinas= new Promaquinas();
		$emp= new Aclempresas();
		$this->titulo='Registrar nuevo Maquinas';
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
				 $pro->aclempresas_id=Session::get('EMPRESAS_ID');
				 if($pro->save())
				 {
					if (Input::hasPost('promaquinas'))
					{
						$dat = new Promaquinas(Input::post('promaquinas'));
						$dat->tesproductos_id=$pro->id;
						$dat->detalle=$pro->detalle;
						$dat->nombre=$pro->nombre;
						$dat->imagen='imagen-no-disponible.jpg';
						$dat->activo='1';
						$dat->estado=1;
						$dar->aclempresas_id=Session::get('EMPRESAS_ID');
						if ($dat->save()) {
							Flash::valid('La Maquinaria Ha Sido Agregado Exitosamente...!!!');
							Aclauditorias::add("Agregó al Maquinaria {$dat->numero} al sistema", 'promaquinas');
							return Redirect::toAction('listado/'.$page);
						} else {
							Flash::warning('No se Pudieron Guardar los datos de la maquina...!!!');
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
			$maquinas= new Promaquinas();
			$emp= new Aclempresas();
			$this->titulo='Editar Maquinaria';
			$this->boton="Editar";
			$this->maquinas = $maquinas->find();
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
			$this->promaquinas= $maquinas->find_first((int)$id);
			$this->tesproductos= $productos->find_first((int)$this->promaquinas->tesproductos_id);
			$this->codigo=$this->tesproductos->codigo;
			if(Input::hasPost('tesproductos'))
			{
				$productos->update(Input::post('tesproductos'));
				if (Input::hasPost('promaquinas')) {
						$maquinas->tesproductos_id=$productos->id;
						$maquinas->aclempresas_id=Session::get('EMPRESAS_ID');
					if ($maquinas->update(Input::post('promaquinas'))) {
						Flash::valid('La Maquinaria Ha Sido Actualizado Exitosamente...!!!');
						Aclauditorias::add("Editó al personal {$maquinas->numero}", 'promaquinas');
						
						return Redirect::toAction('listado/'.$page);
					} else {
						Flash::warning('No se Pudieron Guardar los Datos...!!!');
						unset($this->promaquinas); //para que cargue el $_POST en el form
					}
				} else if (!$this->promaquinas) {
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
            $dat = new Promaquinas();
            if (!$dat->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Maquina con id '{$id}'");
            }else if ($dat->activar()) {
                Flash::valid("La Maquina<b>{$dat->numero}</b> Esta ahora <b>Activo</b>...!!!");
                Aclauditorias::add("Colocó a la Maquina {$dat->numero} como activo", 'promaquinas');
            } else {
                Flash::warning('No se pudo Activar la Maquina!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$page);
    }
     public function desactivar($id,$page=1) {
        try {
            $id = Filter::get($id, 'digits');
            $dat = new Promaquinas();
            if (!$dat->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Producto");
            }else if ($dat->desactivar()) {
                Flash::valid("La Maquina <b>{$dat->numero}</b> Esta ahora <b>Inactivo</b>...!!!");
                Aclauditorias::add("Colocó a la MAQUINA {$dat->numero} como inactivo", 'promaquinas');
            } else {
                Flash::warning('No se Pudo Desactivar La Maquina...!!!');
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

            $dat = new Promaquinas();

            if (!$dat->find_first($id)) { //si no existe
                Flash::warning("No EL dato a eliminar");
            } else if ($dat->borrar()) {
                Flash::valid("EL Maquinas <b>{$dat->numero}</b> fué Eliminado...!!!");
                Aclauditorias::add("El Maquinas {$dat->numero} del sistema", 'promaquinas');
            } else {
                Flash::warning("No se Pudo Eliminar el Maquinas <b>{$dat->numero}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$page);
    }
	
public function resultados($tipo=9) 
	{
		$q=$_GET['q'];
		$obj = new Tesproductos();
		$results = $obj->getProductos_tipo($q,$tipo);
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
			$ID=array("id"=>$value->id,"codigo"=>$value->codigo,"prefijo"=>$value->prefijo,"codigobarras"=>$value->codigobarras,"nombre"=>$value->nombre,"abr"=>$value->abr,"nombrecorto"=>$value->nombrecorto,"detalle"=>$value->detalle,"precio"=>$value->precio);
			$id=$ID;
			//$name=$value->nombre;
			$name=$value->nombre.': '.$value->codigo." ".$value->nombrecorto;
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
			$dat = new Promaquinas();
			$pro= new Tesproductos();
			$this->boton="Editar";
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.$this->empresa);
            $this->promaquinas = $dat->find_first($id);
			/*
			Valida la existencia del distrito creado en todo caso le permite editar
			*/
			$this->tesproductos=$pro->find_first($this->promaquinas->id);
			$this->titulo='Detalle al Maquinaria ('.$this->promaquinas->numero.')';
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
	}
	
public function index($id=36)
{
	$promaquinas = new Promaquinas();
	$tipoproductos= new Testipoproductos();
	$this->maquinas=$promaquinas->getLados($id);
	$this->tipos=$tipoproductos->find('conditions: id!=37 AND teslineaproductos_id=9');

}

public function agregarubicaciones($id)
{
	$promaquinas = new Promaquinas();
	$this->maquina=$promaquinas->find_first((int)$id);
	$ubicaciones=new Proubicacionmaquina();
	$this->ubicaciones= $ubicaciones->find('conditions: promaquinas_id='.(int)$id);
	if (Input::hasPost('ubicacion'))
	{
		$ub = new Proubicacionmaquina(Input::post('ubicacion'));
		$ub->estado=1;
		if ($ub->save()) {
			Flash::valid('La Ubicacion fue agregada!!!');
			//Aclauditorias::add("Agregó al Maquinaria {$dat->numero} al sistema", 'promaquinas');
			return Redirect::toAction('agregarubicaciones/'.$id);
		} else {
			Flash::warning('No se Pudieron Guardar los datos de la maquina...!!!');
		}
	}
}
public function editarubicaciones($maquina_id,$id)
{
	View::select('agregarubicaciones');
	$promaquinas = new Promaquinas();
	$this->maquina=$promaquinas->find_first((int)$maquina_id);
	$ubicaciones=new Proubicacionmaquina();
	$this->ubicaciones= $ubicaciones->find('conditions: promaquinas_id='.(int)$maquina_id);
	$this->ubicacion= $ubicaciones->find_first((int)$id);
	if (Input::hasPost('ubicacion'))
	{
		$ub = new Proubicacionmaquina(Input::post('ubicacion'));
		$ub->estado=1;
		if ($ub->save()) {
			Flash::valid('La Ubicacion fue agregada!!!');
			//Aclauditorias::add("Agregó al Maquinaria {$dat->numero} al sistema", 'promaquinas');
			return Redirect::toAction('agregarubicaciones/'.$maquina_id);
		} else {
			Flash::warning('No se Pudieron Guardar los datos de la maquina...!!!');
		}
	}
}


}
?>