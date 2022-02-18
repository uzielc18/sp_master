<?php
Load::models('cuentas','subcuentas');
View::template('backend/backend');
class CuentasController extends AdminController {
	 protected function before_filter() {
        if ( Input::isAjax() ){
			View::response('view');
            //View::select(NULL, NULL);
        }
    }
 	public function index() {
        try {
            $cuentas = new Cuentas();
            $this->cuentas = $cuentas->find('order: codigo asc');
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function listado() {
        try {
            $cuentas = new Cuentas();
            $this->cuentas = $cuentas->find();
			$this->SUB= new Subcuentas();
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function crear($id=0) {
		$this->titulo = 'Crear Cuenta';
        try {
			$this->id=$id;
            if (Input::hasPost('cuentas')) {
                $cuentas = new Cuentas(Input::post('cuentas'));
				$cuentas->userid=Auth::get('id');
				$cuentas->activo='0';
				if ($cuentas->save()) {
                    Flash::valid('Cuenta fué agregada Exitosamente...!!!');
                    Acciones::add("Agregó Cuenta {$cuentas->descripcion} al sistema");
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function editar($id=0) {
        $this->titulo = 'Editar Cuenta';
        try {
            View::select('crear');

            $cuentas = new Cuentas();
			$this->id=0;
            $this->cuentas = $cuentas->find_first($id);

            if (Input::hasPost('cuentas')) {
					$cuentas->userid=Auth::get('id');
                if ($cuentas->update(Input::post('cuentas'))) {
                    Flash::valid('La Cuenta fué actualizada Exitosamente...!!!');
                    Acciones::add("Editó la Cuenta {$cuentas->descripcion}", 'cuentas');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->cuentas); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $cuentas = new Cuentas();

            if (!$cuentas->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Cuenta con id '{$id}'");
            } else if ($cuentas->activar()) {
                Flash::valid("La Cuenta <b>{$cuentas->codigo}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó a la Cuenta {$cuentas->codigo} como Activo", 'cuentas');
            } else {
                Flash::warning("No se Pudo Activar la Cuenta <b>{$cuentas->cuentas}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        Router::redirect();
    }
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $cuentas = new Cuentas();

            if (!$cuentas->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Cuenta con id '{$id}'");
            } else if ($cuentas->desactivar()) {
                Flash::valid("La Cuenta <b>{$cuentas->codigo}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó a la Cuenta {$cuentas->codigo} como Inactivo", 'cuentas');
            } else {
                Flash::warning("No se Pudo Desactivar la Cuenta <b>{$cuentas->codigo}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
     public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $cuentas = new Cuentas();

            if (!$cuentas->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Cuenta con id '{$id}'");
            } else if ($cuentas->delete()) {
                Flash::valid("La Cuenta <b>{$cuentas->codigo}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó la Cuenta {$cuentas->codigo} del sistema", 'cuentas');
            } else {
                Flash::warning("No se Pudo Eliminar la Cuenta <b>{$cuentas->codigo}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

}

?>