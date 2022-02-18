<?php
Load::models('plareas');
View::template('backend/backend');
class PlareasController extends AdminController {
	public function index($id=1,$pag= 1) {
        try {
            $ar = new Plareas();
            //$this->plareas = $ar->paginate("page: $pag");
			$this->plareasC= $ar->paginate("conditions: aclempresas_id=2","page: $pag");
			$this->plareasP= $ar->paginate("conditions: aclempresas_id=1","page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	
	public function crear() {
        $this->titulo = 'Crear Area';
        try {
            if (Input::hasPost('plareas')) {
                $ar = new Plareas(Input::post('plareas'));
				$ar->estado='1';
				$ar->userid=Auth::get('id');
				$ar->activo='0';
                if ($ar->save()) {
                    Flash::valid('Area fué agregada Exitosamente...!!!');
                    Acciones::add("Agregó Area {$ar->nombre} al sistema");
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
        $this->titulo = 'Editar Area';
        try {
            View::select('crear');

            $ar = new Plareas();

            $this->plareas = $ar->find_first($id);

            if (Input::hasPost('plareas')) {
					$ar->userid=Auth::get('id');
                if ($ar->update(Input::post('plareas'))) {
                    Flash::valid('El Area fué actualizada Exitosamente...!!!');
                    Acciones::add("Editó el Area {$ar->nombre}", 'plareas');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->ar); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
    public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $ar = new Plareas();
            if (!$ar->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Area con id '{$id}'");
            }else if ($ar->activar()) {
                Flash::valid("El Area<b>{$ar->nombre}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó al Area {$ar->nombre} como activo", 'plareas');
            } else {
                Flash::warning('No se pudo Activar el Area!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
    public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $ar = new Plareas();
            if (!$ar->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Area con id '{$id}'");
            }else if ($ar->desactivar()) {
                Flash::valid("El Area <b>{$ar->nombre}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó el Area {$ar->nombre} como inactivo", 'plareas');
            } else {
                Flash::warning('No se Pudo Desactivar el Area...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $ar = new Plareas();

            if (!$ar->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Area con id '{$id}'");
            } else if ($ar->delete()) {
                Flash::valid("El Area <b>{$ar->nombre}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó el Area {$ar->nombre} del sistema", 'plareas');
            } else {
                Flash::warning("No se Pudo Eliminar el Area <b>{$ar->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
	   
}


?>