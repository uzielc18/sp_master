<?php
Load::models('aclempresas');
View::template('backend/backend');
class AclempresasController extends AdminController {
 	   protected function before_filter() {
        if ( Input::isAjax() ){
			View::response('view');
            //View::select(NULL, NULL);
        }
       }
	   public function index($pag= 1) {
        try {
            $em = new Aclempresas();
            $this->aclempresas = $em->paginate("page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function crear() {
        $this->titulo = 'Crear Empresa';
        try {
            if (Input::hasPost('aclempresas')) {
                $em = new Aclempresas(Input::post('aclempresas'));
				$em->estado=1;
				$em->userid=Auth::get('id');
				$em->activo='0';
				if ($em->save()) {
                    Flash::valid('Empresa fué agregada Exitosamente...!!!');
                    Acciones::add("Agregó Empresa {$em->nombre} al sistema");
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
        $this->titulo = 'Editar Empresa';
        try {
            View::select('crear');

            $em = new Aclempresas();

            $this->aclempresas = $em->find_first($id);

            if (Input::hasPost('aclempresas')) {
					$em->userid=Auth::get('id');
                if ($em->update(Input::post('aclempresas'))) {
                    Flash::valid('La Empresa fué actualizada Exitosamente...!!!');
                    Acciones::add("Editó la Empresa {$em->nombre}", 'aclempresas');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->em); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $em = new Aclempresas();

            if (!$em->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Empresa con id '{$id}'");
            } else if ($em->activar()) {
                Flash::valid("La Empresa <b>{$em->nombre}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó a la Empresa {$em->nombre} como activo", 'aclempresas');
            } else {
                Flash::warning("No se Pudo Activar la Empresa <b>{$em->em}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        Router::redirect();
    }
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $em = new Aclempresas();

            if (!$em->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Empresa con id '{$id}'");
            } else if ($em->desactivar()) {
                Flash::valid("La Empresa <b>{$em->nombre}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó a la Empresa {$em->nombre} como inactivo", 'aclempresas');
            } else {
                Flash::warning("No se Pudo Desactivar la Empresa <b>{$em->em}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
    public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $em = new Aclempresas();

            if (!$em->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Empresa con id '{$id}'");
            } else if ($em->delete()) {
                Flash::valid("La Empresa <b>{$em->em}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó la Empresa {$em->em} del sistema", 'aclempresas');
            } else {
                Flash::warning("No se Pudo Eliminar la Empresa <b>{$em->em}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

}

?>