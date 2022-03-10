<?php
View::template('spatricia/default');
Load::models('tesdatos','aclempresas','tescontactos','ubigeo','tesdatosdirecciones');
class ClientesController extends AdminController {
	public $per_page=100;
	public $proveedor=2;
	//public $empresa=Session::get('EMPRESAS_ID');
    protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	public function index(){

    }
	public function listado($campo='codigo',$direccion="ASC",$page=1)
	{
		 try {
		/*
		Se debe saber el id de la empresa para poder ver sus trabajadores Santa Patricia es =1 y Santa Carmela = 2.
		*/
		$this->page=$page;
		$this->campo=$campo;
		$this->direccion=$direccion;
		$tesdatos= new Tesdatos();
		$this->datos=$tesdatos->TesdatosPag($page,$this->per_page,$this->proveedor,$campo,$direccion);
	 	} catch (KumbiaException $e) {
          View::excepcion($e);
        }
    }
	public function crear($campo='codigo',$direccion="ASC",$page=1)
	{
	try {
		$this->campo=$campo;
		$this->direccion=$direccion;
		$this->page=$page;
		$this->estados;
		$this->empresa=Session::get('EMPRESAS_ID');
		$dt= new Tesdatos();
		$emp= new Aclempresas();
		$this->codigo=$dt->generacodigo(2);
		$this->titulo='Registrar nuevo Cliente';
		$this->boton="Guardar";
		$this->dats = $dt->find('columns:id, nombre, appaterno, apmaterno, dni, estado','conditions: aclempresas_id='.Session::get('EMPRESAS_ID'));
		$this->empresas=$emp->find_first('columns: nombre','conditions: id='.Session::get('EMPRESAS_ID'));
		
             if (Input::hasPost('tesdatos')) {
                $dat = new Tesdatos(Input::post('tesdatos'));
				$dat->testipodatos_id=$this->proveedor;
				$dat->activo='1';
				$dat->estado=1;
				(string)$val=time();
				$dat->sufijo=substr($val,-2);
				$dat->aclempresas_id=Session::get('EMPRESAS_ID');
				$dat->fnacimiento=Input::post('anio').'-'.Input::post('mes').'-'.Input::post('dia');
				$dat->userid=Auth::get('id');
                if ($dat->save()) {
					if(Input::hasPost('tescontactos'))
					{
						$cont= new Tescontactos();
						$cont->tesdatos_id=$dat->id;
						$cont->activo='0';
						$cont->estado=1;
						$cont->aclempresas_id=Session::get('EMPRESAS_ID');
						$cont->userid=Auth::get('id');
						$cont->save();
						
						
					}
					
					
					$direcciones= new Tesdatosdirecciones();
					$direcciones->direccion=$dat->direccion;
					if(!empty($dat->distrito)){$direcciones->distrito=$dat->distrito;}else{$direcciones->distrito=$dat->distrito_at;}
					$direcciones->provincia=$dat->provincia;
					$direcciones->departamento=$dat->departamento;
					$direcciones->tesdatos_id=$dat->id;
					$direcciones->direccion=$dat->direccion;
					$direcciones->save();	
					
                    Flash::valid('El Cliente fué agregado exitosamente...!!!');
                    Aclauditorias::add("Agregó al Cliente {$dat->razonsocial} al sistema", 'tesdatos');
                    return Redirect::toAction('direccionfiscal/'.$direcciones->id.'/'.$dat->id);
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
          View::excepcion($e);
        }
	}
	public function direccionfiscal($id=0,$dato_id=0)
	{
		if(Input::hasPost('direccion'))
		{
			$D=new Tesdatosdirecciones(Input::Post('direccion'));
			$D->save();
			return Redirect::toAction('direccionfiscal/0/'.$dato_id);
		}
		$this->id=$id;
		$this->dato_id=$dato_id;
		$dat = new Tesdatos();
		if(Session::has('DATOS'))
		{
			//echo "si existe";
			//$DATOS_CON_MOVIMIENTOS=$dat->getDatosConMovimientos(2);
			//Session::set('DATOS',$DATOS_CON_MOVIMIENTOS);
		}else
		{
			$DATOS_CON_MOVIMIENTOS=$dat->getDatosConMovimientos(2);
			Session::set('DATOS',$DATOS_CON_MOVIMIENTOS);
		}
		$this->clave= array_search($dato_id,Session::get('DATOS'));
		$this->tesdatos = $dat->find_first($dato_id);
		$this->titulo='Modificar Cliente ('.$this->tesdatos->razonsocial.')';
		$direcciones= new Tesdatosdirecciones();
		$this->direcciones=$direcciones->find('conditions: id!='.$id.' AND tesdatos_id='.$dato_id);
		if($id!=0){$this->direccion=$direcciones->find_first($id);$this->boton="Editar Direccion";}else{$this->boton="Crear Direccion";}
		
	}
	public function eliminar_direccion($id=0,$dato_id=0)
	{
		if($dato_id!=0)
		{
		$direcciones= new Tesdatosdirecciones();
		$direcciones->delete($id);
		return Redirect::toAction('direccionfiscal/0/'.$dato_id);
		}else
		{			
		return Redirect::toAction('listado/');
		}
	}
	public function siguente($id)
	{
	$array=Session::get('DATOS');
	return Redirect::toAction('direccionfiscal/0/'.$array[$id]);
	}
	public function editar($campo='codigo',$direccion="ASC",$id,$page=1) {
        try {
			$this->campo=$campo;
			$this->direccion=$direccion;
			$this->page=$page;
			$this->estados;
			$this->empresa=Session::get('EMPRESAS_ID');
			$emp= new Aclempresas();
            $id = Filter::get($id, 'digits');
			$dat = new Tesdatos();
			$contactos= new Tescontactos();
			$this->boton="Editar";
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.$this->empresa);
            $this->tesdatos = $dat->find_first($id);
			$this->departamento=$this->tesdatos->departamento;
			$this->provincia=$this->tesdatos->provincia;
			$this->distrito=$this->tesdatos->distrito;
			/*
			Valida la existencia del distrito creado en todo caso le permite editar
			*/
			$this->ant=1;
			if(empty($this->tesdatos->departamento))
			{
				$this->ant=0;
				$U= new Ubigeo();
				if(($U->exists('distrito="'.$this->tesdatos->distrito_ant.'"'))==1)
				{
					$this->ant=1;
					$ubigeo=$U->find_first('conditions: distrito="'.$this->tesdatos->distrito_ant.'"');
					$this->departamento=$ubigeo->departamento;
					$this->provincia=$ubigeo->provincia;
					$this->distrito=$ubigeo->distrito;
				}
				
			}
			$this->tescontactos=$contactos->find_first('conditions: estado="1" and activo="1" and tesdatos_id='.$id);
			$this->codigo=$this->tesdatos->codigo;
			$this->titulo='Modificar Cliente ('.$this->tesdatos->razonsocial.')';
			$this->dats = $dat->find('columns:id, nombre, appaterno, apmaterno, dni, estado','conditions: aclempresas_id='.Session::get('EMPRESAS_ID'));

            if (Input::hasPost('tesdatos')) {
					$dat->testipodatos_id=$this->proveedor;
					$dat->userid=Auth::get('id');
					$dat->aclempresas_id=Session::get('EMPRESAS_ID');
					$dat->fnacimiento=Input::post('anio').'-'.Input::post('mes').'-'.Input::post('dia');
				if ($dat->update(Input::post('tesdatos'))) {
					
					if (Input::hasPost('tescontactos')){
					$ct=Input::post('tescontactos');
					if(!empty($ct['nombres'])){
						$contactos->save(Input::post('tescontactos'));
						}
					}
					$direcciones= new Tesdatosdirecciones();
					$direcciones->find_first('conditions: direccion="'. $this->tesdatos->direccion.'" AND tesdatos_id='.$dat->id);
					$direcciones->direccion=$dat->direccion;
					if(!empty($dat->distrito)){$direcciones->distrito=$dat->distrito;}else{$direcciones->distrito=$dat->distrito_at;}
					$direcciones->provincia=$dat->provincia;
					$direcciones->departamento=$dat->departamento;
					$direcciones->tesdatos_id=$dat->id;
					$direcciones->save();	
					
                    Flash::valid('El Cliente ha sido actualizado exitosamente...!!!');
                    Aclauditorias::add("Editó al personal {$dat->razonsocial}", 'tesdatos');
                    
					return Redirect::toAction('listado/'.$campo.'/'.$direccion.'/'.$page);
                } else {
                    Flash::warning('No se pudieron guardar los datos...!!!');
                    unset($this->tesdatos); //para que cargue el $_POST en el form
                }
            } else if (!$this->tesdatos) {
                Flash::warning("No existe ningun Cliente con id '{$id}'");
                return Redirect::redirect('santapatricia/personal/listado');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	
	
	public function activar($campo,$direccion,$id,$page=1) {
        try {
            $id = Filter::get($id, 'digits');
            $dat = new Tesdatos();
            if (!$dat->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Proveedor con id '{$id}'");
            }else if ($dat->activar()) {
                Flash::valid("El Cliente<b>{$dat->razonsocial}</b> Esta ahora <b>activo</b>...!!!");
                Aclauditorias::add("Colocó al Cliente {$dat->razonsocial} como activo", 'testproductos');
            } else {
                Flash::warning('No se pudo activar el Cliente!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$campo.'/'.$direccion.'/'.$page);
    }
	
	
     public function desactivar($campo,$direccion,$id,$page=1) {
        try {
            $id = Filter::get($id, 'digits');
            $dat = new Tesdatos();
            if (!$dat->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Cliente");
            }else if ($dat->desactivar()) {
                Flash::valid("El Cliente <b>{$dat->razonsocial}</b> esta ahora <b>inactivo</b>...!!!");
                Aclauditorias::add("Colocó el Cliente {$dat->razonsocial} como inactivo", 'tesdatos');
            } else {
                Flash::warning('No se pudo desactivar el Cliente...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$campo.'/'.$direccion.'/'.$page);
    }
	
	
	public function borrar($campo,$direccion,$id=0,$page=0)
	{
        try {
            $id = Filter::get($id, 'digits');

            $dat = new Tesdatos();

            if (!$dat->find_first($id)) { //si no existe
                Flash::warning("No existe el dato a eliminar");
            } else if ($dat->borrar()) {
                Flash::valid("EL Cliente <b>{$dat->razonsocial}</b> fué Eliminado...!!!");
                Aclauditorias::add("El Cliente {$dat->razonsocial} del sistema", 'tesdatos');
            } else {
                Flash::warning("No se pudo eliminar el Cliente <b>{$dat->razonsocial}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado/'.$campo.'/'.$direccion.'/'.$page);
    }
	
	public function resultados() 
	{
		$q=$_GET['q'];
		$obj = new Tesdatos();
		$results = $obj->find('columns:id,codigo,razonsocial,ruc,departamento,provincia,distrito,pais,direccion,telefono','conditions: testipodatos_id="2" and CONCAT_WS(" ",codigo,razonsocial,ruc,pais) like "%'.$q.'%" AND aclempresas_id='.Session::get('EMPRESAS_ID'));
		foreach ($results as $value)
		{
			$id=$value->id;
			$name=$value->codigo.': '.$value->razonsocial.", ruc: ".$value->ruc." (".$value->pais.' '.$value->departamento.' '.$value->provincia.' '.$value->distrito.''.')';
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$json['razonsocial']=$value->razonsocial;
			$json['ruc']=$value->ruc;
			$json['telefono']=$value->telefono;
			$json['direccion']=$value->direccion;
			$this->data[] = $json;
		}
    }
	
	public function ver($campo,$direccion,$id=0,$page=1)
	{
        try {
			$this->campo=$campo;
			$this->direccion=$direccion;
			$this->page=$page;
			$this->estados;
			$this->empresa=Session::get('EMPRESAS_ID');
			$emp= new Aclempresas();
            $id = Filter::get($id, 'digits');
			$dat = new Tesdatos();
			$contactos= new Tescontactos();
			$this->boton="Editar";
			$this->empresas=$emp->find_first('columns: nombre','conditions: id='.$this->empresa);
            $this->tesdatos = $dat->find_first($id);
			/*
			Valida la existencia del distrito creado en todo caso le permite editar
			*/
			$this->tescontactos=$contactos->find_first('conditions: estado="1" and activo="1" and tesdatos_id='.$id);
			$this->titulo='Detalle al Cliente ('.$this->tesdatos->razonsocial.')';
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
	}
	
	public function actualizarcodigo()
	{
		$datos= new Tesdatos();
		$dats=$datos->find('conditions: ISNULL(sufijo) AND testipodatos_id=2 AND estado=1 AND activo=1');
		
		foreach($dats as $dat)
		{
			(string)$val=time();
			$newdat=$datos->find_first((int)$dat->id);
			$newdat->sufijo=mt_rand(10, 99);
			$newdat->save();
		}
		Flash::valid("Actualizado!!!");
		return Redirect::toAction('listado');
	}
	
public function reporte_clientes($campo='codigo',$direccion="ASC")
{
	$this->campo=$campo;
	$this->direccion=$direccion;
	$tesdatos= new Tesdatos();
	$orden=$campo.' '.$direccion;
	$this->datos=$tesdatos->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND testipodatos_id=2','order: '.$orden);
}
public function reporte_clientes_imprimir($campo='codigo',$direccion="ASC")
{
	$this->campo=$campo;
	$this->direccion=$direccion;
	$tesdatos= new Tesdatos();
	$orden=$campo.' '.$direccion;
	$this->datos=$tesdatos->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND testipodatos_id=2','order: '.$orden);
}
public function cambiar_codigo($id)
{
	$datos= new Tesdatos();
		$dats=$datos->find('conditions: testipodatos_id='.$id.' and aclempresas_id=1');
		$n=0;
		foreach($dats as $dat)
		{
			$n++;
			(string)$val=time();
			$newdat=$datos->find_first((int)$dat->id);
			$maximo=$n;
			switch($maximo)
			{
				case $maximo<10: $maximo="000000".$maximo; break;
				case $maximo<100: $maximo="00000".$maximo; break;
				case $maximo<1000: $maximo="0000".$maximo; break;
				case $maximo<10000: $maximo="000".$maximo; break;
				case $maximo<100000: $maximo="00".$maximo; break;
				case $maximo<1000000: $maximo="0".$maximo; break;
				default: $maximo=$maximo;
			}
			$newdat->codigo=$maximo;
			$newdat->sufijo=mt_rand(10, 99);
			$newdat->save();
		}
		Flash::valid("Actualizado!!!");
		return Redirect::toAction('listado');
}
public function modificar_direcciones($valid=0)
{
	if($valid==43408841){
	$datos=new Tesdatos();
	$dts=$datos->find_all_by_sql('SELECT distrito_ant,distrito,departamento,provincia,direccion,id FROM `tesdatos` WHERE aclempresas_id='.Session::get('EMPRESAS_ID').' AND testipodatos_id=2');
	foreach($dts as $dt):
	$direcciones= new Tesdatosdirecciones();
	$direcciones->direccion=$dt->direccion;
	if(!empty($dt->distrito)){$direcciones->distrito=$dt->distrito;}else{$direcciones->distrito=$dt->distrito_at;}
	$direcciones->provincia=$dt->provincia;
	$direcciones->departamento=$dt->departamento;
	$direcciones->tesdatos_id=$dt->id;
	$direcciones->save();	
	endforeach;
	}
	Flash::valid("Actualizado!!!");
	return Redirect::toAction('listado');
}

public function listar_direcciones()
{
	Session::delete('DATOS');
	$direcciones= new Tesdatosdirecciones();
	$this->direcciones=$direcciones->getDirecciones(2);
}
}
?>
