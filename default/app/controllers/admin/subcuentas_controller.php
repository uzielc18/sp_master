<?php 
Load::models('subcuentas','cuentas');
View::template('backend/backend');
class SubcuentasController extends AdminController
{
	 protected function before_filter() {
        if ( Input::isAjax() ){
			View::response('view');
            //View::select(NULL, NULL);
        }
    }
	public function index() 
	{
        try {
			$this->ajax=FALSE;
			if ( Input::isAjax() ){
				$this->ajax=TRUE;
        	}	
            $subcuentas = new Subcuentas();

            	$this->subcuentas = $subcuentas->find();

        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function crear($id=0,$subcuentas_id=0)
	{
		try {
        $this->titulo = 'Crear Sub Cuenta';
        
			$id = Filter::get($id, 'digits');
			$this->id=$id;
			$this->subcuentas_id=$subcuentas_id;
			if($subcuentas_id!=0)
			{
				$sub=new Subcuentas();
				$this->subdetalle=$sub->find_first((int)$subcuentas_id);
			}else
			{
				$cuenta=new Cuentas();
				$this->subdetalle=$cuenta->find_first($id);
			}
            if (Input::hasPost('subcuentas')) {
                $subcuentas = new Subcuentas(Input::post('subcuentas'));
				$subcuentas->codigo=Input::post('codigo').Input::post('codigon');
				$subcuentas->userid=Auth::get('id');
				$subcuentas->activo='1';
				$subcuentas->subcuentas_id=$subcuentas_id;
				if ($subcuentas->save()) {
                    Flash::valid('Sub Cuenta fué agregada Exitosamente...!!!');
                    Acciones::add("Agregó Sub Cuenta {$subcuentas->descripcion} al sistema");
                    return Router::redirect('admin/cuentas/listado');
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function editar($id) {
        $this->titulo = 'Editar Sub Cuenta';
        try {

            $subcuentas = new Subcuentas();

            $this->subcuentas = $subcuentas->find_first($id);

            if (Input::hasPost('subcuentas')) {
					$subcuentas->userid=Auth::get('id');
                if ($subcuentas->update(Input::post('subcuentas'))) {
                    Flash::valid('La Sub Cuenta fué actualizada Exitosamente...!!!');
                    Acciones::add("Editó la Sub Cuenta {$subcuentas->descripcion}", 'subcuentas');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->subcuentas); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $subcuentas = new Subcuentas();

            if (!$subcuentas->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Sub Cuenta con id '{$id}'");
            } else if ($subcuentas->activar()) {
                Flash::valid("La Sub Cuenta <b>{$subcuentas->ceuntas}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó a la Sub Cuenta {$subcuentas->subcuentas} como activo", 'subcuentas');
            } else {
                Flash::warning("No se Pudo Activar la Sub Cuenta <b>{$subcuentas->subcuentas}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        Router::redirect();
    }
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $subcuentas = new Subcuentas();

            if (!$subcuentas->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Sub Cuenta con id '{$id}'");
            } else if ($subcuentas->desactivar()) {
                Flash::valid("La Sub Cuenta <b>{$subcuentas->subcuentas}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó a la Sub Cuenta {$subcuentas->subcuentas} como inactivo", 'subcuentas');
            } else {
                Flash::warning("No se Pudo Desactivar la Sub Cuenta <b>{$subcuentas->subcuentas}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
     public function eliminar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $subcuentas = new Subcuentas();

            if (!$subcuentas->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Sub Cuenta con id '{$id}'");
            } else if ($subcuentas->delete()) {
                Flash::valid("La Sub Cuenta <b>{$subcuentas->subcuentas}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó la Sub Cuenta {$subcuentas->subcuentas} del sistema", 'subcuentas');
            } else {
                Flash::warning("No se Pudo Eliminar la Sub Cuenta <b>{$subcuentas->subcuentas}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
}
?>