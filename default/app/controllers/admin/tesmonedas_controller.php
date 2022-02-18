<?php
Load::models('tesmonedas');
View::template('backend/backend');
class TesmonedasController extends AdminController {
	public function index($pag= 1) {
        try {
            $mon = new Tesmonedas();
            $this->tesmonedas = $mon->paginate("page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function crear() {
        $this->titulo = 'Crear Moneda';
        try {
            if (Input::hasPost('tesmonedas')) {
                $mon = new Tesmonedas(Input::post('tesmonedas'));
				$mon->estado='1';
				$mon->userid=Auth::get('id');
				$mon->activo='0';
                if ($mon->save()) {
                    Flash::valid('Moneda fué agregada Exitosamente...!!!');
                    Acciones::add("Agregó Moneda {$mon->nombre} al sistema");
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
        $this->titulo = 'Editar Moneda';
        try {
            View::select('crear');

            $mon = new Tesmonedas();

            $this->tesmonedas = $mon->find_first($id);

            if (Input::hasPost('tesmonedas')) {
					$mon->userid=Auth::get('id');
                if ($mon->update(Input::post('tesmonedas'))) {
                    Flash::valid('La Moneda fué actualizada Exitosamente...!!!');
                    Acciones::add("Editó la Moneda {$mon->nombre}", 'tesmonedas');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->mon); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
    	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $mon = new Tesmonedas();
            if (!$mon->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ninguna Moneda con id '{$id}'");
            }else if ($mon->activar()) {
                Flash::valid("La Moneda<b>{$mon->mon}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó a la Moneda{$mon->mon} como activo", 'tesmonedas');
            } else {
                Flash::warning('No se pudo Activar la Moneda!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
     public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $mon = new Tesmonedas();
            if (!$mon->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ninguna Moneda con id '{$id}'");
            }else if ($mon->desactivar()) {
                Flash::valid("La Moneda <b>{$mon->mon}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó la Moneda {$mon->mon} como inactivo", 'tesmonedas');
            } else {
                Flash::warning('No se Pudo Desactivar la Moneda...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $mon = new Tesmonedas();

            if (!$mon->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Moneda con id '{$id}'");
            } else if ($mon->delete()) {
                Flash::valid("La Moneda <b>{$mon->mon}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó la Moneda {$mon->mon} del sistema", 'tesmonedas');
            } else {
                Flash::warning("No se Pudo Eliminar la Moneda <b>{$mon->mon}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
	   
}


?>