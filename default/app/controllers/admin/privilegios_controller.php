<?php
View::template('backend/backend');
class PrivilegiosController extends AdminController {

    public $model = 'aclpermisos';

    public function index($page=1) {
        try {
            $this->results = Load::model('aclrecursos')->paginate("per_page: 30","page: $page", 'order: recurso');
            $this->roles = Load::model('aclroles')->find();
            $this->privilegios = Load::model('aclpermisos')->obtener_privilegios();
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function asignar_privilegios($page = 1) {
        //por ahora este paso no es auditable :-s
        try {
            if (Input::hasPost('priv') || Input::hasPost('privilegios_pagina')) {
                $obj = Load::model('aclpermisos');
                $datos = Input::post('priv');
                $priv_pag = Input::post('privilegios_pagina');
                if ($obj->editarPrivilegios($datos, $priv_pag)) {
                    Flash::valid('Los privilegios Fueron Editados Exitosamente...!!!');
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction("index/$page");
    }

}

