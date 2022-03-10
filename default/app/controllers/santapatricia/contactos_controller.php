<?php
View::template('spatricia/default');
Load::models('tescontactos','tesdatos');
class ContactosController extends AdminController {
	public $per_page=10;
	public $proveedor=2;
	//public $empresa=Session::get('EMPRESAS_ID');
    protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	public function index() {

    }
	public function listado($id=1)
	{
		$id = Filter::get($id, 'digits');
		$this->id=$id;
		$tescontactos= new Tescontactos();
		$this->contactos=$tescontactos->find('conditions: estado=1 and tesdatos_id='.$id);
	
    }
	
	public function crear($id=0)
	{
		try {
		$id = Filter::get($id, 'digits');
		$this->id=$id;
		$contactos= new Tescontactos();
		$this->titulo='Registrar nuevo Contacto';
		$this->boton="Guardar";
		if (Input::hasPost('tescontactos')) {
                $cont = new Tescontactos(Input::post('tescontactos'));
				$cont->tesdatos_id=$id;
				$cont->activo='0';
				$cont->estado=1;
				$cont->aclempresas_id=Session::get('EMPRESAS_ID');
				$cont->userid=Auth::get('id');
				if($cont->save())
				{
					Flash::valid('El Contacto ha sido agregado exitosamente...!!!');
                    Aclauditorias::add("Agregó al Contacto {$contactos->nombre} al sistema", 'tescontactos');
                    return Redirect::toAction('listado/'.$cont->tesdatos_id);
                } else {
                    Flash::warning('No se pudieron guardar los datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
           View::excepcion($e);
       }
	}
	
	public function editar($id) {
        try {
			$this->estados;
			$this->empresa=Session::get('EMPRESAS_ID');
			View::select('crear');
            $id = Filter::get($id, 'digits');
			$contactos= new Tescontactos();
			$this->boton="Editar";
            $this->tescontactos = $contactos->find_first($id);
			$this->id=$this->tescontactos->tesdatos_id;
			$this->titulo='Modificar al Contacto ('.$this->tescontactos->nombre.')';
			if (Input::hasPost('tescontactos')) {
					$contactos->userid=Auth::get('id');
					$contactos->aclempresas_id=Session::get('EMPRESAS_ID');
				if ($contactos->update(Input::post('tescontactos'))) {
					Flash::valid('El Contacto ha sido actualizado exitosamente...!!!');
                    Aclauditorias::add("Editó al Contacto {$contactos->nombres}", 'tescontactos');
                    return Redirect::toAction('listado/'.$contactos->tesdatos_id);
                } else {
                    Flash::warning('No se pudieron guardar los datos...!!!');
                    unset($this->tescontactos); //para que cargue el $_POST en el form
                }
            } else if (!$this->tescontactos) {
                Flash::warning("No existe ningun Contacto con id '{$id}'");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	
	
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $contactos = new Tescontactos();
            if (!$contactos->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Contacto con id '{$id}'");
            }else if ($contactos->activar()) {
                Flash::valid("El Proveedor<b>{$contactos->nombre}</b> esta ahora <b>activo</b>...!!!");
                Aclauditorias::add("Colocó al Contacto {$contactos->nombre} como activo", 'testproductos');
            } else {
                Flash::warning('No se pudo activar el Contacto!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$contactos->tesdatos_id);
    }
     public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $contactos = new Tescontactos();
            if (!$contactos->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Contacto");
            }else if ($contactos->desactivar()) {
                Flash::valid("El Contacto <b>{$contactos->nombre}</b> esta ahora <b>inactivo</b>...!!!");
                Aclauditorias::add("Colocó el Contacto {$contactos->nombre} como inactivo", 'testproductos');
            } else {
                Flash::warning('No se pudo desactivar el Contacto...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$contactos->tesdatos_id);
    }
	
	
	public function borrar($id=0)
	{
        try {
            $id = Filter::get($id, 'digits');

            $contactos = new Tescontactos();

            if (!$contactos->find_first($id)) { //si no existe
                Flash::warning("No EL dato a eliminar");
            } else if ($contactos->borrar()) {
                Flash::valid("EL Contacto <b>{$contactos->nombres}</b> fué eliminado...!!!");
                Aclauditorias::add("El Contacto {$contactos->nombres} del sistema", 'tescontactos');
            } else {
                Flash::warning("No se pudo eliminar el contacto <b>{$contactos->nombres}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('tesdatosid/'.$contactos->tesdatos_id);
    }
	public function tesdatosid($id)
	{
		//try {
            $id = Filter::get($id, 'digits');
			$datos = new Tesdatos();
			$this->item=$datos->dato_id((int)$id);
        //} catch (KumbiaException $e) {
        //    View::excepcion($e);
        //}
	}

}
