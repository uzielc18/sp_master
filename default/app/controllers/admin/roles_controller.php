<?php
Load::models('aclroles');
View::template('backend/backend');
class RolesController extends AdminController {

    public function index($pag= 1) {
        try {
            $roles = new Aclroles();
            $this->roles = $roles->paginate("page: $pag");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function crear() {
        $this->titulo = 'Crear Rol (Perfil)';
        try {

            if (Input::hasPost('rol')) {
                $rol = new Aclroles(Input::post('rol'));
                if (Input::hasPost('roles_padres')) {
                    $rol->padres = join(',', Input::post('roles_padres'));
                }
					$rol->activo='0';
                if ($rol->save()) {
                    Flash::valid('El Rol Ha Sido Agregado Exitosamente...!!!');
                    Aclauditorias::add("Agregó el Rol {$rol->rol} al sistema", 'aclroles');
                    return Redirect::to();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function editar($id=0) {
        $this->titulo = 'Editar Rol (Perfil)';
        try {

            $id = Filter::get($id, 'digits');

            View::select('crear');

            $rol = new Aclroles();

            $this->rol = $rol->find_first($id);

            if (Input::hasPost('rol')) {

                if (Input::hasPost('roles_padres')) {
                    $padres = Input::post('roles_padres');
                    sort($padres);
                    $rol->padres = join(',', $padres);
                }

                if ($rol->update(Input::post('rol'))) {
                    Flash::valid('El Rol Ha Sido Actualizado Exitosamente...!!!');
                    Aclauditorias::add("Editó el Rol {$rol->rol}", 'roles');
                    return Redirect::to();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->rol); //para que cargue el $_POST en el form
                }
            } else if (!$this->rol) {
                Flash::warning("No existe ningun rol con id '{$id}'");
                return Redirect::to();
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function agregar_padre($id) {
        View::template(NULL);
        try {
            $id = Filter::get($id, 'digits');

            $roles = new Aclroles();

            $this->rol = $roles->find_first($id);
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $rol = new Aclroles();

            if (!$rol->find_first($id)) { //si no existe
                Flash::warning("No existe ningun rol con id '{$id}'");
            } else if ($rol->delete()) {
                Flash::valid("El rol <b>{$rol->rol}</b> fué Eliminado...!!!");
                Aclauditorias::add("Eliminó el Rol {$rol->rol} del sistema", 'roles');
            } else {
                Flash::warning("No se Pudo Eliminar el Rol <b>{$rol->rol}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::to();
    }

    public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $rol = new Aclroles();

            if (!$rol->find_first($id)) { //si no existe
                Flash::warning("No existe ningun rol con id '{$id}'");
            } else if ($rol->activar()) {
                Flash::valid("El rol <b>{$rol->rol}</b> Esta ahora <b>Activo</b>...!!!");
                Aclauditorias::add("Colocó al Rol {$rol->rol} como activo", 'roles');
            } else {
                Flash::warning("No se Pudo Activar el Rol <b>{$rol->rol}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        Redirect::to();
    }

    public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $rol = new Aclroles();

            if (!$rol->find_first($id)) { //si no existe
                Flash::warning("No existe ningun rol con id '{$id}'");
            } else if ($rol->desactivar()) {
                Flash::valid("El rol <b>{$rol->rol}</b> Esta ahora <b>Inactivo</b>...!!!");
                Aclauditorias::add("Colocó al Rol {$rol->rol} como inactivo", 'roles');
            } else {
                Flash::warning("No se Pudo Desactivar el Rol <b>{$rol->rol}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::to();
    }

}
