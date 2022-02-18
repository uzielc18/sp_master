<?php
Load::models('testipodatos');
View::template('backend/backend');
class TestipodatosController extends AppController {
	public function index($pag= 1) {
        try {
            $testipodatos = new Testipodatos();
            $this->testipodatos = $testipodatos->paginate("page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function crear() {
        $this->titulo = 'Crear Estado';
        try {
            if (Input::hasPost('testipodatos')) {
                $testipodatos = new Testipodatos(Input::post('testipodatos'));
				$testipodatos->estado=1;
				$testipodatos->userid=Auth::get('id');
				$testipodatos->activo='1';
                if ($testipodatos->save()) {
                    Flash::valid('Estado fué agregado Exitosamente...!!!');
                    Acciones::add("Agregó Estado {$testipodatos->nombre} al sistema");
					return Router::toAction('');
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

            $testipodatos = new Testipodatos();

            $this->testipodatos = $testipodatos->find_first($id);

            if (Input::hasPost('testipodatos')) {
					$testipodatos->userid=Auth::get('id');
                if ($testipodatos->update(Input::post('testipodatos'))) {
                    Flash::valid('El Estado fué actualizado Exitosamente...!!!');
                    Acciones::add("Editó el Estado {$testipodatos->nombre}", 'testipodatos');
                    return Router::toAction('');
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->testipodatos); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $testipodatos = new Testipodatos();

            if (!$testipodatos->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Estado con id '{$id}'");
            } else if ($testipodatos->activar()) {
                Flash::valid("El Estado <b>{$testipodatos->testipodatos}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó al Estado {$testipodatos->testipodatos} como activo", 'testipodatos');
            } else {
                Flash::warning("No se Pudo Activar el Estado <b>{$testipodatos->testipodatos}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $testipodatos = new Testipodatos();

            if (!$testipodatos->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Estado con id '{$id}'");
            } else if ($testipodatos->desactivar()) {
                Flash::valid("El Estado <b>{$testipodatos->testipodatos}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó al Estado {$testipodatos->testipodatos} como inactivo", 'testipodatos');
            } else {
                Flash::warning("No se Pudo Desactivar el Estado <b>{$testipodatos->testipodatos}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $testipodatos = new Testipodatos();

            if (!$testipodatos->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Estado con id '{$id}'");
            } else if ($testipodatos->delete()) {
                Flash::valid("El Estado <b>{$testipodatos->testipodatos}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó el Estado {$testipodatos->testipodatos} del sistema", 'testipodatos');
            } else {
                Flash::warning("No se Pudo Eliminar el Estado <b>{$testipodatos->testipodatos}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

}
?>