<?php
View::template('spatricia/default');
Load::models('teslineaproductos');
class LineaproductosController extends AdminController {
	public $per_page=15;
	public function index($pag= 1) {
        try {
            $lineaproductos = new Teslineaproductos();
            $this->teslineaproductos = $lineaproductos->paginate("page: $pag","per_page: $this->per_page","conditions: estado=1 and aclempresas_id=".Session::get("EMPRESAS_ID"),'order: nombre asc');
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	
	public function crear() {
        $this->titulo = 'Crear Linea de Producto';
		$lineas = new Teslineaproductos();
		$this->lineas = $lineas->find("columns: id, nombre, abr, detalle","conditions: estado=1 and aclempresas_id=".Session::get("EMPRESAS_ID"),'order: nombre asc');
        try {
            if (Input::hasPost('teslineaproductos')) {
                $lineaproductos = new Teslineaproductos(Input::post('teslineaproductos'));
				$lineaproductos->estado='1';
				$lineaproductos->userid=Auth::get('id');
				$lineaproductos->activo='1';
				$lineaproductos->aclempresas_id=Session::get("EMPRESAS_ID");
                if ($lineaproductos->save()) {
                    Flash::valid('Linea de producto fué agregada Exitosamente...!!!');
                    Aclauditorias::add("Linea de producto {$lineaproductos->nombre} al sistema",'teslineaproductos');
					Input::delete();
                    return Redirect::toAction("crear/");
                } else {
                    Flash::warning('No se pudieron guardar los datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function editar($id=0) {
        $this->titulo = 'Editar Linea de Producto';
        try {
            View::select('crear');

			$teslineaproductos = new Teslineaproductos();
			$this->lineas = $teslineaproductos->find("columns: id, nombre, abr, detalle","conditions: estado=1 and aclempresas_id=".Session::get("EMPRESAS_ID"),'order: nombre asc');
            $this->teslineaproductos = $teslineaproductos->find_first((int)$id);

            if (Input::hasPost('teslineaproductos')) {
					$lineaproductos=$teslineaproductos;
					$lineaproductos->userid=Auth::get('id');
					$lineaproductos->aclempresas_id=Session::get("EMPRESAS_ID");
                if ($lineaproductos->update(Input::post('teslineaproductos'))) {
                    Flash::valid('La linea de Producto fué actualizada exitosamente...!!!');
                    Aclauditorias::add("Editó la Linea de Producto {$lineaproductos->nombre}", 'teslineaproductos');
                    return Redirect::toAction('crear');
                } else {
                    Flash::warning('No se pudieron guardar los datos...!!!');
                    unset($this->teslineaproductos); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
   	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $lineaproductos = new Teslineaproductos();
            if (!$lineaproductos->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ninguna Linea de Producto");
            }else if ($lineaproductos->activar()) {
                Flash::valid("Linea de Producto<b>{$lineaproductos->nombre}</b> esta ahora <b>activo</b>...!!!");
                Aclauditorias::add("Colocó la Linea de Producto {$lineaproductos->nombre} como activo", 'teslineaproductos');
            } else {
                Flash::warning('No se pudo activar la Linea de Producto!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('');
    }
     public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $lineaproductos = new Teslineaproductos();
            if (!$lineaproductos->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ninguna Linea de producto");
            }else if ($lineaproductos->desactivar()) {
                Flash::valid("La linea de Producto <b>{$lineaproductos->nombre}</b> esta ahora <b>inactivo</b>...!!!");
                Aclauditorias::add("Colocó la Linea de Producto {$lineaproductos->nombre} como inactivo", 'teslineaproductos');
            } else {
                Flash::warning('No se Pudo Desactivar esta Linea de Producto...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('');
    }
	public function borrar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $lineaproductos = new Teslineaproductos();

            if (!$lineaproductos->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna lina de producto");
            } else if ($lineaproductos->borrar()) {
                Flash::valid("La Linea de Prodcuto <b>{$lineaproductos->nombre}</b> fué eliminado...!!!");
                Aclauditorias::add("La Linea de Prodcuto {$lineaproductos->nombre} del sistema", 'teslineaproductos');
            } else {
                Flash::warning("No se pudo eliminar el La Linea de Prodcuto <b>{$lineaproductos->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::redirect();
    }
	   
}


?>