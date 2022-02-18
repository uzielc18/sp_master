<?php
Load::models('aclmenus');
View::template('backend/backend');
class MenuController extends AdminController {

    public function index($visible=3,$pagina = 1) {
        try {
			$this->pagina=$pagina;
			$this->visible=$visible;
            $menus = new Aclmenus();
            $this->menus = $menus->menus_paginados($pagina,$visible);
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function crear() {
        $this->titulo = 'Crear Menu';
        try {
            if (Input::hasPost('aclmenus')) {
                $menu = new Aclmenus(Input::post('aclmenus'));
				$menu->estado=1;
				$menu->userid=Auth::get('id');
                if ($menu->save()) {
                    Flash::valid('El Menu fué agregado Exitosamente...!!!');
                    Acciones::add("Agregó el Menú {$menu->nombre} al sistema", 'menu');
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
        $this->titulo = 'Editar Menu';
        try {
            View::select('crear');

            $menu = new Aclmenus();

            $this->aclmenus = $menu->find_first($id);

            if (Input::hasPost('aclmenus')) {

                if ($menu->update(Input::post('aclmenus'))) {
                    Flash::valid('El Menu fué actualizado Exitosamente...!!!');
                    Acciones::add("Editó el Menú {$menu->nombre}", 'aclmenu');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->menu); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function activar($id) {
        try {
            $menu = new Aclmenus();
            $menu->find_first($id);
            if ($menu->activar()) {
                Flash::valid("El menu <b>{$menu->nombre}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó al Menu {$menu->nombre} como activo", 'menu');
            } else {
                Flash::warning("No se Pudo Activar el menu <b>{$menu->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

    public function desactivar($id) {
        try {
            $menu = new Aclmenus();
            $menu->find_first($id);
            if ($menu->desactivar()) {
                Flash::valid("El menu <b>{$menu->nombre}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó al Menu {$menu->nombre} como inactivo", 'menu');
            } else {
                Flash::warning("No se Pudo Desactivar el menu <b>{$menu->menu}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

    public function eliminar($id) {
        try {
            $menu = new Aclmenus();
            $menu->find_first($id);
            if ($menu->delete()) {
                Flash::valid("El Menu <b>{$menu->nombre}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó al Menu {$menu->nombre} del sistema", 'menu');
            } else {
                Flash::warning("No se Pudo Eliminar el Menu <b>{$menu->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

}
