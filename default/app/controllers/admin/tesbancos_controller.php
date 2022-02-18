<?php
Load::models('tesbancos');
View::template('backend/backend');
class TesbancosController extends AdminController {
	public function index($pag= 1) {
        try {
            $ban = new Tesbancos();
            //$this->tesbancos = $ban->paginate("page: $pag");
			$this->tesbancosC= $ban->paginate("conditions: aclempresas_id=2",
			"page: $pag");
			$this->tesbancosP= $ban->paginate("conditions: aclempresas_id=1",
			"page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function crear() {
        $this->titulo = 'Crear Banco';
        try {
            if (Input::hasPost('tesbancos')) {
                $ban = new Tesbancos(Input::post('tesbancos'));
				$ban->estado=1;
				$ban->userid=Auth::get('id');
				$ban->activo='0';
                if ($ban->save()) {
                    Flash::valid('Banco fué agregado Exitosamente...!!!');
                    Acciones::add("Agregó Banco {$ban->nombre} al sistema");
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
        $this->titulo = 'Editar Banco';
        try {
            View::select('crear');

            $ban = new Tesbancos();

            $this->tesbancos = $ban->find_first($id);

            if (Input::hasPost('tesbancos')) {
					$ban->userid=Auth::get('id');
                if ($ban->update(Input::post('tesbancos'))) {
                    Flash::valid('El Banco fué actualizado Exitosamente...!!!');
                    Acciones::add("Editó el Banco {$ban->nombre}", 'tesbancos');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->ban); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $ban = new Tesbancos();
            if (!$ban->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Banco con id '{$id}'");
            }else if ($ban->activar()) {
                Flash::valid("El Banco<b>{$ban->nombre}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó al Banco {$ban->nombre} como activo", 'tesbancos');
            } else {
                Flash::warning('No se pudo Activar el Banco!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $ban = new Tesbancos();
            if (!$ban->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Banco con id '{$id}'");
            }else if ($ban->desactivar()) {
                Flash::valid("El Banco <b>{$ban->nombre}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó el Banco {$ban->nombre} como inactivo", 'tesbancos');
            } else {
                Flash::warning('No se Pudo Desactivar el Banco...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $ban = new Tesbancos();

            if (!$ban->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Banco con id '{$id}'");
            } else if ($ban->delete()) {
                Flash::valid("El Banco <b>{$ban->nombre}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó el Banco {$ban->nombre} del sistema", 'tesbancos');
            } else {
                Flash::warning("No se Pudo Eliminar el Banco <b>{$ban->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
	
}
?>