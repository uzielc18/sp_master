<?php
Load::models('tesdocumentos');
View::template('backend/backend');
class TesdocumentosController extends AdminController {
	public function index($pag= 1) {
        try {
            $doc = new Tesdocumentos();
            $this->tesdocumentos = $doc->paginate("page: $pag");
			
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function crear() {
        $this->titulo = 'Crear Banco';
        try {
            if (Input::hasPost('tesdocumentos')) {
                $doc = new Tesdocumentos(Input::post('tesdocumentos'));
				$doc->estado=1;
				$doc->userid=Auth::get('id');
				$doc->activo='0';
                if ($doc->save()) {
                    Flash::valid('Documento fué agregado Exitosamente...!!!');
                    Acciones::add("Agregó Documento {$doc->nombre} al sistema");
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
        $this->titulo = 'Editar Color';
        try {
            View::select('crear');

            $doc = new Tesdocumentos();

            $this->tesdocumentos = $doc->find_first($id);

            if (Input::hasPost('tesdocumentos')) {
					$doc->userid=Auth::get('id');
                if ($doc->update(Input::post('tesdocumentos'))) {
                    Flash::valid('La Documento fué actualizado Exitosamente...!!!');
                    Acciones::add("Editó el Documento {$doc->nombre}", 'tesdocumentos');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->doc); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $doc = new Tesdocumentos();
            if (!$doc->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Documento con id '{$id}'");
            }else if ($doc->activar()) {
                Flash::valid("El Documento<b>{$doc->nombre}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó al Documento {$doc->nombre} como activo", 'tesdocumentos');
            } else {
                Flash::warning('No se pudo Activar el Documento!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $doc = new Tesdocumentos();
            if (!$doc->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Documento con id '{$id}'");
            }else if ($doc->desactivar()) {
                Flash::valid("El Documento <b>{$doc->nombre}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó el Documento {$doc->nombre} como inactivo", 'tesdocumentos');
            } else {
                Flash::warning('No se Pudo Desactivar el Documento...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $doc = new Tesdocumentos();

            if (!$doc->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Documento con id '{$id}'");
            } else if ($doc->delete()) {
                Flash::valid("El Documento <b>{$doc->nombre}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó el Documento {$doc->nombre} del sistema", 'tesdocumentos');
            } else {
                Flash::warning("No se Pudo Eliminar el Documento <b>{$doc->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
	
}
?>