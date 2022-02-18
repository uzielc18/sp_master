<?php
Load::models('proacabados');
View::template('backend/backend');
class ProacabadosController extends AdminController {
	public function index($pag= 1) {
        try {
            $ac = new Proacabados();
            $this->proacabados = $ac->paginate("page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function crear() {
        $this->titulo = 'Crear Acabado';
        try {
            if (Input::hasPost('proacabados')) {
                $ac = new Proacabados(Input::post('proacabados'));
				$ac->estado=1;
				$ac->userid=Auth::get('id');
				$ac->activo='0';
                if ($ac->save()) {
                    Flash::valid('Acabado fué agregado Exitosamente...!!!');
                    Acciones::add("Agregó Acabado {$ac->nombre} al sistema");
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
        $this->titulo = 'Editar Acabado';
        try {
            View::select('crear');

            $ac = new Proacabados();

            $this->proacabados = $ac->find_first($id);

            if (Input::hasPost('proacabados')) {
					$ac->userid=Auth::get('id');
                if ($ac->update(Input::post('proacabados'))) {
                    Flash::valid('El Acabado fué actualizado Exitosamente...!!!');
                    Acciones::add("Editó el Acabado {$ac->nombre}", 'proacabados');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->ac); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $ac = new Proacabados();

            if (!$ac->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Acabado con id '{$id}'");
            } else if ($ac->activar()) {
                Flash::valid("El Acabado <b>{$ac->nombre}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó al Acbado {$ac->nombre} como activo", 'proacabados');
            } else {
                Flash::warning("No se Pudo Activar el Acabado <b>{$ac->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        Router::redirect();
    }
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $ac = new Proacabados();

            if (!$ac->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Acabado con id '{$id}'");
            } else if ($ac->desactivar()) {
                Flash::valid("El Acabado <b>{$ac->nombre}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó al Acabado {$ac->nombre} como inactivo", 'proacabados');
            } else {
                Flash::warning("No se Pudo Desactivar el Acabado <b>{$ac->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
	public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $ac = new Proacabados();

            if (!$ac->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Acabado con id '{$id}'");
            } else if ($ac->delete()) {
                Flash::valid("El Acabado <b>{$ac->ac}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó el Acabado {$ac->ac} del sistema", 'proacabados');
            } else {
                Flash::warning("No se Pudo Eliminar el Acabado <b>{$ac->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
	
}
?>