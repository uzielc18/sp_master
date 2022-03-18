
<?php

class AccidentesController extends AdminController {
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	public function index() {
        try {
            $peraccidentes = new Peraccidentes();
			$anio=date('Y');
			$mes=date('m');
			$dia=date('d');
			$this->meses;
            $this->accidentes= $peraccidentes->accidentesDia($anio,$mes,$dia);
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	
	public function crear() {
        $this->titulo = 'Registrar Accidente';
		$lineas = new Peraccidentes();
        //try {
            if (Input::hasPost('peraccidentes')) {
				
				$hora=Input::post('hora');
				
                $peraccidentes = new Peraccidentes(Input::post('peraccidentes'));
				
				$peraccidentes->aclusuarios_id=Auth::get('id');
				$peraccidentes->estado='1';
				$peraccidentes->userid=Auth::get('id');
				$peraccidentes->aclempresas_id=Session::get("EMPRESAS_ID");
                if ($peraccidentes->save()) {
                    Flash::valid('Rgistrado Exitosamente...!!!');
                    Aclauditorias::add("Accidente del {$peraccidentes->nombreacidentado} al sistema",'peraccidentes');
					Input::delete();
                    return Redirect::toAction('');
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }

            }
        /*} catch (KumbiaException $e) {
            View::excepcion($e);
        }*/
    }
	/*
	Esta vista solo sera vista por un administrador
	*/
	public function editar($id=0) {
        $this->titulo = 'Editar el Accidente';
        try {
            View::select('crear');
			
			$peraccidentes = new Peraccidentes();
			$this->peraccidentes=$peraccidentes->find_first($id);
            if (Input::hasPost('peraccidentes')) {
					$peraccidentes=$peraccidentes;
					$peraccidentes->userid=Auth::get('id');
					$peraccidentes->aclempresas_id=Session::get("EMPRESAS_ID");
                if ($peraccidentes->update(Input::post('peraccidentes'))) {
                    Flash::valid('Actualizado Exitosamente...!!!');
                    Aclauditorias::add("Editó El Accidente de: {$peraccidentes->nombreacidentado}", 'peraccidentes');
                    return Redirect::toAction('');
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->peraccidentes); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function borrar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $peraccidentes = new Peraccidentes();

            if (!$peraccidentes->find_first($id)) { //si no existe
                Flash::warning("No existe ningun Accidente");
            } else if ($peraccidentes->borrar()) {
                Flash::valid("El Accidente <b>{$peraccidentes->nombre}</b> fué Eliminado...!!!");
                Aclauditorias::add("El Accidente {$peraccidentes->nombre} del sistema", 'peraccidentes');
            } else {
                Flash::warning("No se Pudo Eliminar el Accidente <b>{$peraccidentes->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::redirect('');
    }
	
	
	public function reportes($Y='',$m='')
	{
        //try {
			$anio=$this->anio=Session::get('ANIO');
			if($Y!='')$anio=$this->anio=$Y;
			$mes_activo=$this->mes_activo=date('m');
			if($m!='')$mes_activo=$this->mes_activo=$m;
			$this->meses;
            $peraccidentes = new Peraccidentes();
			$this->ACCIDENTE=$peraccidentes;
			$this->accidentes= $peraccidentes->accidentesMes($anio,$mes_activo);
        /*} catch (KumbiaException $e) {
            View::excepcion($e);
        }*/
        //return Redirect::redirect('');
		
	}
	public function resultados()
	{	
		 $q=$_GET['q'];
		 $obj = new Acldatos();
		 $results = $obj->find('columns:id,nombre,appaterno,apmaterno dni','conditions: CONCAT_WS(" ",nombre,appaterno,apmaterno,dni) like "%'.$q.'%"');
		 foreach ($results as $value) {
			 $id=$value->nombre." ".$value->appaterno." ".$value->apmaterno;
			 $name=$value->nombre." ".$value->appaterno." ".$value->apmaterno." (".$value->dni.")";
				$json = array();
				$json['id'] =$id;
				$json['name'] = $name;
				$this->data[] = $json;
		}
		
	}
	public function sinaccidentes()
	{
        try {
				$peraccidentes = new Peraccidentes();
				if($peraccidentes->exists('fecha='.date('Y-m-d')))
				{
					$peraccidentes->aclusuarios_id=Auth::get('id');
					$peraccidentes->nombreaccidentado="No hay Acidentado";
					$peraccidentes->detalle="No hay Accidentes Para este Dia";
					$peraccidentes->fecha=date('Y-m-d');
					$peraccidentes->hora=date('H:00:00');
					$peraccidentes->estado='1';
					$peraccidentes->userid=Auth::get('id');
					$peraccidentes->aclempresas_id=Session::get("EMPRESAS_ID");
					if ($peraccidentes->save()) {
						Flash::valid('Rgistrado Exitosamente...!!!');
						Aclauditorias::add("Accidente del {$peraccidentes->nombreacidentado} al sistema",'peraccidentes');
						
					} else {
						Flash::warning('No se Pudieron Guardar los Datos...!!!');
					}
				}else
				{
					Flash::warning('No se puede crear Accidentes para este dia');
				}
			
			return Redirect::toAction('');
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
	}
	
	public function diario($Y='',$m='')
	{
			$anio=$this->anio=Session::get('ANIO');
			if($Y!='')$anio=$this->anio=$Y;
			$mes_activo=$this->mes_activo=date('m');
			if($m!='')$mes_activo=$this->mes_activo=$m;
			$this->meses;
	}


}?>