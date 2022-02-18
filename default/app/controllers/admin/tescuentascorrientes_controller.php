<?php
Load::models('tescuentascorrientes');
View::template('backend/backend');
class TescuentascorrientesController extends AdminController {
	public function index($pag= 1) {
        try {
            $cc = new Tescuentascorrientes();
            $this->tescuentascorrientesC= $cc->paginate("conditions: aclempresas_id=2","page: $pag");
			
            $this->tescuentascorrientesP= $cc->paginate("conditions: aclempresas_id=1","page: $pag");
		
		
			
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

	public function crear() {
        $this->titulo = 'Crear Banco';
        try {
            if (Input::hasPost('tescuentascorrientes')) {
                $cc = new Tescuentascorrientes(Input::post('tescuentascorrientes'));
				$cc->estado=1;
				$cc->userid=Auth::get('id');
				$cc->activo='0';
                if ($cc->save()) {
                    Flash::valid('Cuenta Corriente fué agregada Exitosamente...!!!');
                    Acciones::add("Agregó Cuenta Corriente {$cc->numero} al sistema");
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
            $cc = new Tescuentascorrientes();

            $this->tescuentascorrientes = $cc->find_first($id);

            if (Input::hasPost('tescuentascorrientes')) {
					$cc->userid=Auth::get('id');
                if ($cc->update(Input::post('tescuentascorrientes'))) {
                    Flash::valid('La Cuenta Corriente fué actualizada Exitosamente...!!!');
                    Acciones::add("Editó la Cuenta Corriente {$cc->numero}", 'tescuentascorrientes');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->cc); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $cc = new Tescuentascorrientes();
            if (!$cc->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ninguna Cuenta Corriente con id '{$id}'");
            }else if ($cc->activar()) {
                Flash::valid("La Cuenta Corriente<b>{$cc->numero}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó a la Cuenta Corriente {$cc->numero} como activo", 'tescuentascorrientes');
            } else {
                Flash::warning('No se pudo Activar la Cuenta Corriente!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $cc = new Tescuentascorrientes();
            if (!$cc->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ninguna Cuenta Corriente con id '{$id}'");
            }else if ($cc->desactivar()) {
                Flash::valid("La Cuenta Corriente <b>{$cc->numero}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó la Cuenta Corriente {$cc->numero} como inactivo", 'tescuentascorrientes');
            } else {
                Flash::warning('No se Pudo Desactivar la Cuenta Corriente...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $cc = new Tescuentascorrientes();

            if (!$cc->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Cuenta Corriente con id '{$id}'");
            } else if ($cc->delete()) {
                Flash::valid("La Cuenta Corriente <b>{$cc->numero}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó la Cuenta Corriente {$cc->nuemro} del sistema", 'tescuentascorrientes');
            } else {
                Flash::warning("No se Pudo Eliminar la Cuenta Corriente <b>{$cc->numero}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
	public function bancos($id)
	{
		View::response('view');
		$this->id=$id;
	}
}
?>