<?php
View::template('spatricia/default');
Load::models('acldatos','plareas','aclempresas','protransportistas','prodetalletransportes');
class TransportistasController extends AdminController {
	public $per_page=15;
	//public $empresa=Session::get('EMPRESAS_ID');
    protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
        }
    }
	public function index() {
		

    }
	public function listado($page=1)
	{
		/*
		Sebe saber el id de la empresa para poder ver sus trabajadores Santa Patricia es =1 y Santa Carmela = 2.
		*/
		$transportistas= new Protransportistas();
		$this->transportistas=$transportistas->paginate('page: '.$page,'per_page: '.$this->per_page,'conditions: estado=1');
	
    }
	
	public function detalles($id=0,$origen='Transporte')
	{
		$detalles= new Prodetalletransportes();
		$this->origen=$origen;
		$this->id=$id;
		$this->detalles=$detalles->detalles($id,$origen);
	}
	
	public function crear()
	{
		$this->estados;
		$this->empresa=Session::get('EMPRESAS_ID');
		$dat= new Protransportistas();
		$emp= new Aclempresas();
		$this->titulo='Registrar Nuevo Transportista';
		$this->boton="Guardar";
		$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
		try {
             if (Input::hasPost('datos')) {
                $dat = new Acldatos(Input::post('datos'));
				$dat->activo='0';
				$dat->estado=1;
				$dat->aclempresas_id=Session::get('EMPRESAS_ID');
				$dat->userid=Auth::get('id');
                if ($dat->save()){
				$trans= new Protransportistas(Input::post('transportistas'));
				$trans->acldatos_id=$dat->id;
				$trans->nombre=$dat->nombre.' '.$dat->appaterno.' '.$dat->apmaterno;
				$trans->activo='1';
				$trans->estado='1';
				$trans->save();
                    Flash::valid('El Personal Ha Sido Agregado Exitosamente...!!!');
                    Aclauditorias::add("Agregó al Personal {$dat->nombre} al sistema", 'acldatos');
                    return Redirect::toAction('listado');
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
			$trans= new Protransportistas();
			$this->boton="Editar";
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.$this->empresa);
            $this->transportistas = $trans->find_first($id);
			$this->datos= $dat->find_first($this->transportistas->acldatos_id);
			$this->titulo='Editar Transportista ('.$this->datos->nombre.')';

            if (Input::hasPost('datos')) {
					$dat->userid=Auth::get('id');
					$dat->aclempresas_id=Session::get('EMPRESAS_ID');
					$dat->fnacimiento=Input::post('anio').'-'.Input::post('mes').'-'.Input::post('dia');
				if ($dat->update(Input::post('datos'))) {
                    $trans->acldatos=$dat->id;
					$trans->nombre=$dat->nombre.' '.$dat->appaterno.' '.$dat->apmaterno;
					$trans->update(Input::post('transportistas'));
					Flash::valid('El Personal Ha Sido Actualizado Exitosamente...!!!');
                    Aclauditorias::add("Editó al personal {$dat->nombre}", 'acldatos');
                    return Redirect::toAction('listado');
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->transportistas); //para que cargue el $_POST en el form
                }
            } else if (!$this->transportistas) {
                Flash::warning("No existe ningun Transportista con id '{$id}'");
                return Redirect::toAction('listado');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function borrar($id=0)
	{
        try {
            $id = Filter::get($id, 'digits');

            $dat = new Protransportistas();

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
        return Redirect::toAction('listado');
    }
	
	public function resultados() 
	{
		$q=$_GET['q'];
		$obj = new Acldatos();
		$results = $obj->find('conditions: estado=1 and aclempresas_id='.Session::get('EMPRESAS_ID').' and CONCAT_WS(" ",nombre,appaterno,apmaterno,dni) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			$ID=array();
			foreach($value->fields as $field)
			{
				$ID[$field]=$value->$field;
			}
			$id=$ID;
			//$name=$value->nombre;
			$name=$value->nombre.' : '.$value->dni;
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
		}
    }
	
	
	public function crear_externo()
	{
		$this->estados;
		$this->e=0;
		$this->empresa=Session::get('EMPRESAS_ID');
		$dat= new Protransportistas();
		$emp= new Aclempresas();
		$this->titulo='Registrar Nuevo Transportista';
		$this->boton="Guardar";
		$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
		try {
             if (Input::hasPost('datos')) {
                $trans= new Protransportistas(Input::post('transportistas'));
				$trans->activo='1';
				$trans->estado='1';
				$trans->save();
                    Flash::valid('El Personal Ha Sido Agregado Exitosamente...!!!');
                    Aclauditorias::add("Agregó al Personal {$dat->nombre} al sistema", 'acldatos');
                } else {
                    //Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
	}	
	
	public function editar_externo($id)
	{
		View::select('crear_externo');
		$this->estados;
		$this->empresa=Session::get('EMPRESAS_ID');
		$dat= new Protransportistas();
		$this->transportistas=$dat->find_first($id);
		$this->e=1;
		$emp= new Aclempresas();
		$this->titulo='Registrar Nuevo Transportista';
		$this->boton="Guardar";
		$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
		try {
             if (Input::hasPost('datos')) {
                $trans= new Protransportistas(Input::post('transportistas'));
				$trans->activo='1';
				$trans->estado='1';
				$trans->save();
                    Flash::valid('El Personal Ha Sido editado Exitosamente...!!!');
                    Aclauditorias::add("Edito el personal esterno {$dat->nombre} en el sistema", 'acldatos');
					return Redirect::toAction('listado');
                } else {
                    //Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
	}
	
}
?>
