<?php
View::template('spatricia/default');
Load::models('acldatos','plareas','aclempresas');
class PersonalController extends AdminController {
	public $per_page=15;
	//public $empresa=Session::get('EMPRESAS_ID');
    protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	public function index() {

    }
	public function listado($page=1)
	{
		/*
		Sebe saber el id de la empresa para poder ver sus trabajadores Santa Patricia es =1 y Santa Carmela = 2.
		*/
		$datos= new Acldatos();
		$this->datos=$datos->personal_paginado($page,$this->per_page,Session::get('EMPRESAS_ID'));
	
    }
	public function crear()
	{
		$this->estados;
		$this->empresa=Session::get('EMPRESAS_ID');
		$dt= new Acldatos();
		$emp= new Aclempresas();
		$this->titulo='Registrar nuevo Personal';
		$this->boton="Guardar";
		$this->dats = $dt->find('columns:id, nombre, appaterno, apmaterno, dni, estado','conditions: aclempresas_id='.Session::get('EMPRESAS_ID'));
		$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
		try {
             if (Input::hasPost('datos')) {
                $dat = new Acldatos(Input::post('datos'));
				$dat->activo='0';
				$dat->estado=1;
				$dat->aclusuarios_id=0;
				$dat->aclempresas_id=Session::get('EMPRESAS_ID');
				//$dat->fnacimiento=Input::post('anio').'-'.Input::post('mes').'-'.Input::post('dia');
				$dat->userid=Auth::get('id');
                if ($dat->save()) {
					
                    Flash::valid('El Personal Ha Sido Agregado Exitosamente...!!!');
                    Aclauditorias::add("Agregó al Personal {$dat->nombre} al sistema", 'acldatos');
                    return Redirect::redirect();
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
			$this->estados;
			$this->empresa=Session::get('EMPRESAS_ID');
			View::select('crear');
			$emp= new Aclempresas();
            $id = Filter::get($id, 'digits');
			$dat = new Acldatos();
			$this->boton="Editar";
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.$this->empresa);
            $this->datos = $dat->find_first($id);
			$this->titulo='Editar al personal ('.$this->datos->nombre.')';
			$this->dats = $dat->find('columns:id, nombre, appaterno, apmaterno, dni, estado','conditions: aclempresas_id='.Session::get('EMPRESAS_ID'));

            if (Input::hasPost('datos')) {
					$dat->userid=Auth::get('id');
					$dat->aclempresas_id=Session::get('EMPRESAS_ID');
					$dat->fnacimiento=Input::post('anio').'-'.Input::post('mes').'-'.Input::post('dia');
				if ($dat->update(Input::post('datos'))) {
                    Flash::valid('El Personal Ha Sido Actualizado Exitosamente...!!!');
                    Aclauditorias::add("Editó al personal {$dat->nombre}", 'acldatos');
                    return Redirect::redirect('santapatricia/personal/listado');
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->datos); //para que cargue el $_POST en el form
                }
            } else if (!$this->datos) {
                Flash::warning("No existe ningun Personal con id '{$id}'");
                return Redirect::redirect('santapatricia/personal/listado');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function areas($id=0)
	{
		
		$id = Filter::get($id, 'digits');
		$this->areas=new Plareas();
		$this->id=$id;
	}
	public function borrar($id=0)
	{
        try {
            $id = Filter::get($id, 'digits');

            $dat = new Acldatos();

            if (!$dat->find_first($id)) { //si no existe
                Flash::warning("No EL dato a eliminar");
            } else if ($dat->borrar()) {
                Flash::valid("EL personal <b>{$dat->nombre}</b> fué Eliminado...!!!");
                Aclauditorias::add("El personal {$dat->nombre} del sistema", 'acldatos');
            } else {
                Flash::warning("No se Pudo Eliminar el personal <b>{$dat->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        Redirect::redirect('santapatricia/personal/listado');
    }

}
?>
