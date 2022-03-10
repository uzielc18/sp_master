<?php
View::template('spatricia/default');
Load::models('tesproductos','testipoproductos','teslineaproductos');
class ProductosController extends AdminController {
	public $per_page=30;
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	public function index($lineas_id=1,$tipos_id=0)
	{
		$lineas= new Teslineaproductos();
		$tipos= new Testipoproductos();
		$productos = new Tesproductos();
		$this->linea=$lineas->find_first('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND id='.(int)$lineas_id);
		$this->id=$lineas_id;
		$this->tipos_id=$tipos_id;
		if($tipos_id==0){
			$this->tipos=$tipos->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND estado=1 and activo=1 and teslineaproductos_id='.$lineas_id);
			$this->PR=$productos;
		}else
		{
			$this->productos=$productos->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND estado=1 and activo=1 and testipoproductos_id='.$tipos_id);
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
			$this->tipos=$tipo->find_first('columns: id,teslineaproductos_id,nombre','conditions: id='.(int)$tipo_id);
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
                    Aclauditorias::add("Agregó producto {$testproductos->nombre} al sistema");
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
			$this->tipos=$tipo->find_first('columns: id,teslineaproductos_id,nombre','conditions: id='.(int)$tipos_id);
            $testproductos = new Tesproductos();
            $this->testproductos = $testproductos->find_first($id);
			$this->codigo=$this->testproductos->codigo;

            if (Input::hasPost('testproductos')) {
					$testproductos->userid=Auth::get('id');
					$testproductos->aclempresas_id=Session::get("EMPRESAS_ID");
                if ($testproductos->update(Input::post('testproductos'))) {
                    Flash::valid('El producto fué actualizada Exitosamente...!!!');
                    Aclauditorias::add("Editó el producto {$testproductos->nombre}", 'testproductos');
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
                Aclauditorias::add("Colocó al Producto {$testproductos->nombre} como activo", 'testproductos');
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
                Aclauditorias::add("Colocó el Producto {$testproductos->nombre} como inactivo", 'testproductos');
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
                Aclauditorias::add("Producto {$tesproductos->nombre} del sistema", 'tesproductos');
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
		if(0==0){
		$val= new Tesproductos();
		if($val->actualizarP()){
		return Redirect::toAction('listado');
		}
		}
	}

public function producto()
	{
		$this->data='';
		$q=$_GET['q'];
		$obj = new Tesproductos();
		$results = $obj->find('conditions: estado=1 and aclempresas_id='.Session::get('EMPRESAS_ID').' and CONCAT_WS(" ",nombre,detalle,codigo) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			//$name=$value->nombre;
			switch($value->testipoproductos_id)
			{
				case 37: $opcional=' - '.$value->getProplegadores()->numero;break;
				default: 
				$opcional='';
				break;
			}
			$n=(string)$value->detalle.' ('.$value->codigo.$opcional.')';
			$name=$this->clear($this->htmlcode($n));
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $name;
			$json['teslineaproductos_id '] = $value->getTestipoproductos()->teslineaproductos_id ;
			$this->data[] = $json;
		}
	}
	
		
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
}


?>