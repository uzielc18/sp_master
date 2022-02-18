<?php

Load::models('aclrecursos');
View::template('backend/backend');
class RecursosController extends AdminController {
 public $perpage=20;
    public function index($pagina = 1) {
        try {
            $recursos = new Aclrecursos();
            $this->recursos = $recursos->paginate("per_page: $this->perpage","page: $pagina");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function crear() {
        try {
            $this->titulo = 'Crear Recurso';

            if (Input::hasPost('recurso')) {
                $recurso = new Recursos(Input::post('recurso'));
                if ($recurso->save()) {
                    Flash::valid('El Recurso Ha Sido Agregado Exitosamente...!!!');
                    Acciones::add("Agregó al Recurso {$recurso->recurso} al Sistema", 'recursos');
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
        try {
            $this->titulo = 'Editar Recurso';
            View::select('crear');

            $recurso = new Aclrecursos();
            $this->aclrecursos = $recurso->find_first($id);

            if (Input::hasPost('aclrecursos')) {
                if ($recurso->update(Input::post('aclrecursos'))) {
                    Flash::valid('El Recurso ha sido Actualizado Exitosamente...!!!');
                    Acciones::add("Editó al Recurso {$recurso->recurso}", 'recursos');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->recurso); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function activar($id) {
        try {
            $rec = new Aclrecursos();
            $rec->find_first($id);
            if ($rec->activar()) {
                Flash::valid("El recurso <b>{$rec->recurso}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó al Recurso {$rec->recurso} como activo", 'recursos');
            } else {
                Flash::warning("No se Pudo Activar el Recurso <b>{$rec->recurso}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

    public function desactivar($id) {
        try {
            $rec = new Aclrecursos();
            $rec->find_first($id);
            if ($rec->desactivar()) {
                Flash::valid("El recurso <b>{$rec->recurso}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó al Recurso {$rec->recurso} como inactivo", 'recursos');
            } else {
                Flash::warning("No se Pudo Desactivar el Recurso <b>{$rec->recurso}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

    public function eliminar($id) {
        try {
            $rec = new Aclrecursos();
            $rec->find_first($id);
            if ($rec->delete()) {
                Flash::valid("El recurso <b>{$rec->recurso}</b> ha sido Eliminado...!!!");
                Acciones::add("Eliminó al Recurso {$rec->recurso} del Sistema", 'recursos');
            } else {
                Flash::warning("No se Pudo Eliminar el Recurso <b>{$rec->recurso}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

    public function escaner($pagina = 1) {
        try {
            $recurso = new Aclrecursos();
            $this->recursos = $recurso->obtener_recursos_nuevos($pagina);
            if (Input::hasPost('guardar')) {
                if ($recurso->guardar_nuevos()) {
                    $this->recursos = $recurso->obtener_recursos_nuevos($pagina);
                    Input::delete();
                    Flash::valid('Los Recursos Fueron Guardados Exitosamente...!!!');
                    Acciones::add('Agrego Nuevos Recursos al Sistema', 'recursos');
                } else {
                    Flash::warning('Por favor Complete los datos requeridos he intente guardar nuevamente');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

}
