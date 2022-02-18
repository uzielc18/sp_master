<?php
Load::models('estadoplegador');
View::template('backend/backend');
class EstadoplegadorController extends AppController {
 	public function index($pag= 1) {
        try {
            $estadoplegador = new Estadoplegador();
            $this->estadoplegador = $estadoplegador->paginate("page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function crear() {
        $this->titulo = 'Crear Estado Plegador';
        try {
            if (Input::hasPost('estadoplegador')) {
                $ep = new Estadoplegador(Input::post('estadoplegador'));
				$ep->estado=1;
				$ep->userid=Auth::get('id');
				$ep->activo='0';
				if ($ep->save()) {
                    Flash::valid('Estado del Plegador fué agregado Exitosamente...!!!');
                    Acciones::add("Agregó Estado {$ep->nombre} al sistema");
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
        $this->titulo = 'Editar Estado';
        try {
            View::select('crear');

            $estadoplegador = new Estadoplegador();

            $this->estadoplegador = $estadoplegador->find_first($id);

            if (Input::hasPost('estadoplegador')) {
					$estadoplegador->userid=Auth::get('id');
                if ($estadoplegador->update(Input::post('estadoplegador'))) {
                    Flash::valid('El Estado del Plegador fué actualizado Exitosamente...!!!');
                    Acciones::add("Editó el Estado del Plegador{$estadoplegador->nombre}", 'estadoplegador');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->estadoplegador); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $ep = new Estadoplegador();

            if (!$ep->find_first($id)) { //si no existe
                Flash::warning("No existe ningun EStado del Plegador con id '{$id}'");
            } else if ($ep->activar()) {
                Flash::valid("El Estado de Plegador <b>{$ep->nombre}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó al Estado de Plegador {$ep->nombre} como activo", 'estadoplegador');
            } else {
                Flash::warning("No se Pudo Activar el Estado de Plegador <b>{$ep->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        Router::redirect();
    }
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $ep = new Estadoplegador();

            if (!$ep->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Estado de Plegador con id '{$id}'");
            } else if ($ep->desactivar()) {
                Flash::valid("El Estado de Plegador<b>{$ep->nombre}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó al Estado de Plegador{$ep->nombre} como inactivo", 'estadoplegador');
            } else {
                Flash::warning("No se Pudo Desactivar el Estado de Plegador <b>{$ep->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
    public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $ep = new Estadoplegador();

            if (!$ep->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Estado con id '{$id}'");
            } else if ($ep->delete()) {
                Flash::valid("El Estado de Plegador<b>{$ep->nombre}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó el Estado de Plegador {$ep->nombre} del sistema", 'estadoplegador');
            } else {
                Flash::warning("No se Pudo Eliminar el Estado de Plagador<b>{$ep->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

}

?>