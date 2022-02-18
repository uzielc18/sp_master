<?php
Load::models('tesunidadesmedidas');
View::template('backend/backend');
class TesunidadesmedidasController extends AppController {
	public function index($pag= 1) {
        try {
            $um = new Tesunidadesmedidas();
            //$this->tesunidadesmedidas = $um->paginate("page: $pag");
			$this->tesunidadesmedidasC= $um->paginate("conditions: aclempresas_id=2","page: $pag");
				$this->tesunidadesmedidasP= $um->paginate("conditions: aclempresas_id=1","page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
    	
	public function crear() {
        $this->titulo = 'Crear Unidad de Medida';
        try {
            if (Input::hasPost('tesunidadesmedidas')) {
                $um = new Tesunidadesmedidas(Input::post('tesunidadesmedidas'));
				$um->estado='1';
				$um->userid=Auth::get('id');
				$um->activo='0';
                if ($um->save()) {
                    Flash::valid('Unidad de Medida fué agregada Exitosamente...!!!');
                    Acciones::add("Agregó Unidad de Medida {$um->nombre} al sistema");
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
        $this->titulo = 'Editar Unidad de Medida';
        try {
            View::select('crear');

            $um = new Tesunidadesmedidas();

            $this->tesunidadesmedidas = $um->find_first($id);

            if (Input::hasPost('tesunidadesmedidas')) {
					$um->userid=Auth::get('id');
                if ($um->update(Input::post('tesunidadesmedidas'))) {
                    Flash::valid('Unidad de Medida actualizada Exitosamente...!!!');
                    Acciones::add("Editó el Unidad de Medida {$um->nombre}", 'tesunidadesmedidas');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->tesunidadesmedidas); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
     	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $um = new Tesunidadesmedidas();
            if (!$um->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Area con id '{$id}'");
            }else if ($um->activar()) {
                Flash::valid("Unidad de Medida<b>{$um->um}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó a la Unidad de Medida {$um->um} como activo", 'tesunidadesmedidas');
            } else {
                Flash::warning('No se pudo Activar la Unidad de medida!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
      public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $um = new Tesunidadesmedidas();
            if (!$um->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Area con id '{$id}'");
            }else if ($um->desactivar()) {
                Flash::valid("Unidad de Medida <b>{$um->um}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó el Area {$um->um} como inactivo", 'tesunidadesmedidas');
            } else {
                Flash::warning('No se Pudo Desactivar la Unidad de Medida...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $um = new Tesunidadesmedidas();

            if (!$um->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Unidad de Medida con id '{$id}'");
            } else if ($um->delete()) {
                Flash::valid("La Unidad de Medida <b>{$um->um}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó la Unidad de Medida {$um->um} del sistema", 'tesunidadesmedidas');
            } else {
                Flash::warning("No se Pudo Eliminar la Unidad de Medida <b>{$um->um}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
	   
}


?>