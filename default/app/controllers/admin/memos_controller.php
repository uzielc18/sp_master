<?php
Load::models('memos','aclempresas','acldatos');
View::template('backend/backend');
class MemosController extends AppController {
	public $SP=1;
	public $SC=2;
	public function index($pag= 1) {
        try {
            $me = new Memos();
			$this->memosC= $me->paginate("conditions: aclempresas_id=$this->SC","page: $pag");
			$this->memosP= $me->paginate("conditions: aclempresas_id=$this->SP","page: $pag");
			$this->SC;
			$this->SP;
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function crear($id=0) {
        $this->titulo = 'Crear Memo';
        try {
			$empresas= new Aclempresas();
			$this->empresa=$empresas->find_first($id);
            if (Input::hasPost('memos')) {
                $me = new Memos(Input::post('memos'));
				$me->estado='1';
				$me->aclempresas_id=$id;
				$me->userid=Auth::get('id');
				$me->activo='0';
                if ($me->save()) {
                    Flash::valid('Memo fué agregada Exitosamente...!!!');
                    Acciones::add("Agregó Memo {$me->nombre} al sistema");
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function editar($id) {
        $this->titulo = 'Editar Memo';
        try {
            View::select('editar');

            $me = new Memos();

            $this->memos = $me->find_first($id);

            if (Input::hasPost('memos')) {
					$me->userid=Auth::get('id');
                if ($me->update(Input::post('memos'))) {
                    Flash::valid('El Memo fué actualizado Exitosamente...!!!');
                    Acciones::add("Editó el Memo {$me->id}", 'memos');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->me); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $me = new Memos();
            if (!$me->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Memo con id '{$id}'");
            }else if ($me->activar()) {
                Flash::valid("El Memo<b>{$me->id}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó al Memo {$me->id} como activo", 'plareas');
            } else {
                Flash::warning('No se pudo Activar el Memo!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $me = new Memos();
            if (!$me->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Memo con id '{$id}'");
            }else if ($me->desactivar()) {
                Flash::valid("El Memo <b>{$me->id}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó al Memo {$me->id} como inactivo", 'memos');
            } else {
                Flash::warning('No se Pudo Desactivar el Memo...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $me = new Memos();

            if (!$me->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Memo con id '{$id}'");
            } else if ($me->delete()) {
                Flash::valid("El Memo <b>{$me->id}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó el Memo {$me->id} del sistema", 'memos');
            } else {
                Flash::warning("No sepudo eliminar el Memo <b>{$me->id}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
  public function ver($id) {
        try {
            $me = new Memos();
			$this->item= $me->find_first($id);
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	
	public function resultados($id=0)
	{
		View::response('view');
		$q=$_GET['q'];
		$obj = new Acldatos();
		$results = $obj->find('conditions: estado=1 and aclempresas_id='.$id.' and CONCAT_WS(" ",nombre,appaterno,apmaterno,dni) like "%'.$q.'%"');
		$this->data=array();
		foreach ($results as $value)
		{
			$id=$value->id;
			$name=$value->nombre.' '.$value->appaterno.' '.$value->apmaterno;
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
		}
	}

}


?>