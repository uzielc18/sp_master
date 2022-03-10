<?php
View::template('spatricia/default');
Load::models('testipoproductos','teslineaproductos');
class TipoproductosController extends AdminController {
	public $per_page=15;
	public function index($linea_id=1,$pag= 1) {
        try {
			$lineadeproductos= new Teslineaproductos();
			$this->lineaproducto=$lineadeproductos->find_first("columns:id, nombre","conditions: id=".(int)$linea_id);
			$this->LP=$lineadeproductos;
            $testipoproductos = new Testipoproductos();
            $this->testipoproductos = $testipoproductos->paginate("page: $pag","per_page: $this->per_page",'conditions: estado=1 and teslineaproductos_id='.$linea_id.' and aclempresas_id='.Session::get("EMPRESAS_ID"),'order: nombre asc');
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	
	public function crear($linea_id=1) {
		try {
		$id = Filter::get($linea_id, 'digits');
        $this->titulo = 'Crear Tipo Producto';
		$tipos = new Testipoproductos();
		$lineadeproductos= new Teslineaproductos();
		$this->lineaproducto=$lineadeproductos->find_first("columns:id, nombre","conditions: id=".(int)$linea_id.' and aclempresas_id='.Session::get("EMPRESAS_ID"));
        $this->tipos = $tipos->find('conditions: estado=1 and teslineaproductos_id='.$linea_id.' and aclempresas_id='.Session::get("EMPRESAS_ID"),'order: nombre asc');
        
            if (Input::hasPost('testipoproductos')) {
                $testipoproductos = new Testipoproductos(Input::post('testipoproductos'));
				$testipoproductos->estado=1;
				$testipoproductos->userid=Auth::get('id');
				$testipoproductos->activo='1';
				$testipoproductos->aclempresas_id=Session::get("EMPRESAS_ID");
                if ($testipoproductos->save()) {
                    Flash::valid('Tipo de Producto fué agregada Exitosamente...!!!');
                    Aclauditorias::add("Agregó Tipo de Producto {$testipoproductos->nombre} al sistema");
                    return Redirect::redirect("santapatricia/tipoproductos/crear/".$this->lineaproducto->id);
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
		$this->titulo = 'Editar Tipo Producto';
		$tipos = new Testipoproductos();
		$lineadeproductos= new Teslineaproductos();
		    View::select('crear');
            $this->testipoproductos = $tipos->find_first($id);
			$this->lineaproducto=$lineadeproductos->find_first("columns:id, nombre","conditions: id=".(int)$this->testipoproductos->teslineaproductos_id);
        $this->tipos = $tipos->find('conditions: estado=1 and teslineaproductos_id='.$this->testipoproductos->teslineaproductos_id.' and aclempresas_id='.Session::get("EMPRESAS_ID"),'order: nombre asc');

            if (Input::hasPost('testipoproductos')) {
					$testipoproductos=$tipos;
					$testipoproductos->userid=Auth::get('id');
					$testipoproductos->aclempresas_id=Session::get("EMPRESAS_ID");
                if ($testipoproductos->update(Input::post('testipoproductos'))) {
                    Flash::valid('El Tipo de Producto fué actualizada Exitosamente...!!!');
                    Aclauditorias::add("Editó el Tipo de Producto {$testipoproductos->nombre}", 'testipoproductos');
                    return Redirect::toAction("crear/".$this->lineaproducto->id);
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->testipoproductos); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
   	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $testipoproductos = new Testipoproductos();
            if (!$testipoproductos->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Tipo de Producto");
            }else if ($testipoproductos->activar()) {
                Flash::valid("El Tipo de Producto<b>{$testipoproductos->nombre}</b> Esta ahora <b>Activo</b>...!!!");
                Aclauditorias::add("Colocó al Tipo de Producto {$testipoproductos->nombre} como activo", 'testipoproductos');
            } else {
                Flash::warning('No se pudo Activar el Area!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('index/'.$testipoproductos->teslineaproductos_id);
    }
     public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $testipoproductos = new Testipoproductos();
            if (!$testipoproductos->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Tipo de producto con este valor");
            }else if ($testipoproductos->desactivar()) {
                Flash::valid("El Tipo de producto <b>{$testipoproductos->nombre}</b> Esta ahora <b>Inactivo</b>...!!!");
                Aclauditorias::add("Colocó el Tipo de producto {$testipoproductos->nombre} como inactivo", 'testipoproductos');
            } else {
                Flash::warning('No se Pudo Desactivar el Tipo de producto...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('index/'.$testipoproductos->teslineaproductos_id);
    }
	
	public function borrar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $testipoproductos = new Testipoproductos();

            if (!$testipoproductos->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Tipo de producto");
            } else if ($testipoproductos->borrar()) {
                Flash::valid("Tipo de producto <b>{$testipoproductos->nombre}</b> fué Eliminado...!!!");
                Aclauditorias::add("Tipo de producto {$testipoproductos->nombre} del sistema", 'teslineaproductos');
            } else {
                Flash::warning("No se Pudo Eliminar el Tipo de producto <b>{$lineaproductos->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::redirect("santapatricia/tipoproductos/index/".$testipoproductos->teslineaproductos_id);
    } 
}


?>