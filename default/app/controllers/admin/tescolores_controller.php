<?php
Load::models('tescolores','aclempresas');
View::template('backend/backend');
class TescoloresController extends AdminController {
	public function index($id=1,$pag= 1) {
        try {
			
			$this->id_e=$id;
            $col = new Tescolores();
			$Em=new Aclempresas();
			$this->empresas=$Em->find('conditions: activo=1');
			$this->empresa=$Em->find_first((int)$id);
			$this->tescolores= $col->paginate("conditions: aclempresas_id=".$id,"page: $pag");
			$this->page=$pag;
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function crear($id=0,$id_e=1,$pag= 1) {
		
		$this->page=$pag;
		$this->id_e=$id_e;
        $this->titulo = 'Crear Colores';
		$C=new Tescolores();
        try {
            if (Input::hasPost('tescolores')) {
                $col = new Tescolores(Input::post('tescolores'));
				$col->codigo=$C->codigo();
				$col->estado=1;
				$col->userid=Auth::get('id');
				$col->activo='0';
                if ($col->save()) {
                    Flash::valid('Color fué agregado Exitosamente...!!!');
                    Acciones::add("Agregó Color {$col->nombre} al sistema");
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function editar($id,$id_e,$pag=1) {
        $this->titulo = 'Editar Color';
		$this->page=$pag;
		$this->id_e=$id_e;
        try {
            View::select('crear');

            $col = new Tescolores();

            $this->tescolores = $col->find_first($id);

            if (Input::hasPost('tescolores')) {
					$col->userid=Auth::get('id');
                if ($col->update(Input::post('tescolores'))) {
                    Flash::valid('El Color fué actualizado Exitosamente...!!!');
                    Acciones::add("Editó el Color {$col->nombre}", 'tescolores');
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->col); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function activar($id,$id_e,$pag=1) {
		$this->page=$pag;
		$this->id_e=$id_e;
        try {
            $id = Filter::get($id, 'digits');
            $col = new Tescolores();
            if (!$col->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Color con id '{$id}'");
            }else if ($col->activar()) {
                Flash::valid("El Color<b>{$col->nombre}</b> Esta ahora <b>Activo</b>...!!!");
                Acciones::add("Colocó al Color {$col->nombre} como activo", 'tescolores');
            } else {
                Flash::warning('No se pudo Activar el Color!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function desactivar($id,$id_e,$pag=1) {
		$this->page=$pag;
		$this->id_e=$id_e;
        try {
            $id = Filter::get($id, 'digits');
            $col = new Tescolores();
            if (!$col->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Color con id '{$id}'");
            }else if ($col->desactivar()) {
                Flash::valid("El Color <b>{$col->nomnbre}</b> Esta ahora <b>Inactivo</b>...!!!");
                Acciones::add("Colocó el Color {$col->nombre} como inactivo", 'tescolores');
            } else {
                Flash::warning('No se Pudo Desactivar el Color...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
	public function eliminar($id,$id_e,$pag=1) {
		$this->page=$pag;
		$this->id_e=$id_e;
        try {
            $id = Filter::get($id, 'digits');
            $col = new Tescolores();

            if (!$col->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Color con id '{$id}'");
            } else if ($col->delete()) {
                Flash::valid("El Color <b>{$col->nombre}</b> fué Eliminado...!!!");
                Acciones::add("Eliminó el Color {$col->nombre} del sistema", 'tescolores');
            } else {
                Flash::warning("No se Pudo Eliminar el Color <b>{$col->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }
	public function reporte (){
		View::template(NULL);
                $this->texto = 'Hola Mundo';
	}

	
	
}
?>