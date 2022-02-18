<?php
Load::models('placargos','aclempresas');
View::template('backend/backend');
class PlacargosController extends AdminController {
	 public function index($id=1,$pag= 1) {
        try {
            $placargos = new Placargos();
            $this->placargos = $placargos->paginate("page: $pag");
			$this->placargosC= $placargos->paginate("conditions: aclempresas_id=2","page: $pag");
			$this->placargosP= $placargos->paginate("conditions: aclempresas_id=1","page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	 /*public function index($id=1,$pag= 1) {
        try {
            $pla = new Placargos();
			$Em=new Aclempresas();
			$this->empresa=$Em->find('conditions: activo=1');
			$this->empresa=$Em->find_first((int)$id);
			$this->placargos= $pla->paginate("conditions: aclempresas_id=".$id,"page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }*/
         public function crear() {
        $this->titulo = 'Crear Cargo';
        try {
            if (Input::hasPost('placargos')) {
                $pc = new Placargos(Input::post('placargos'));
				$pc->estado=1;
				$pc->userid=Auth::get('id');
				$pc->activo='0';
                if ($pc->save()) {
                    Flash::valid('Cargo fué agregado Exitosamente...!!!');
                    Acciones::add("Agregó el Cargo {$pc->nombre} al sistema");
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
        $this->titulo = 'Editar Cargo';
        try {
            View::select('crear');

            $pc = new Placargos();

            $this->placargos = $pc->find_first($id);

            if (Input::hasPost('placargos')) {
					$pc->userid=Auth::get('id');
                if ($pc->update(Input::post('placargos'))) {
                    Flash::valid('Cargo actualizado Exitosamente...!!!');
                    Acciones::add("Editó el Cargo {$pc->nombre}", 'placargos');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->placargos); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	   public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $pc = new Placargos();
            if (!$pc->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Cargo con id '{$id}'");
            }else if ($pc->activar()) {
                Flash::valid("El Cargo<b>{$pc->nombre}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó el Cargo {$pc->nombre} como activo", 'placargos');
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
            $pc = new Placargos();
            if (!$pc->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Cargo con id '{$id}'");
            }else if ($pc->desactivar()) {
                Flash::valid("El Cargo <b>{$pc->nombre}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó el Cargo {$pc->nombre} como Inactivo", 'placargos');
            } else {
                Flash::warning('No se Pudo Desactivar el Cargo...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	 public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $placargos = new Placargos();

            if (!$placargos->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Cargo con id '{$id}'");
            } else if ($placargos->delete()) {
                Flash::valid("El Cargo <b>{$placargos->placargos}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó el Cargo {$placargos->placargos} del sistema", 'placargos');
            } else {
                Flash::warning("No se Pudo Eliminar el Cargo<b>{$placargos->placargos}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

}
	
?>