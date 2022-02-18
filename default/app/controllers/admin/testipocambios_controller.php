<?php 
View::template('backend/backend');
load::models('testipocambios');
class TestipocambiosController extends AdminController
{
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::response('view');
            //View::select(NULL, NULL);
        }
    }
	public function index($Y='',$m='',$page=1) {
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
	   $obj=new Testipocambios();
	   $this->results = $obj->find('conditions: DATE_FORMAT(fecha,"%Y-%m")="'.$anio.'-'.$mes_activo.'"',"order: fecha ASC");
    }

    /**
     * Crea un Registro
     */
    public function crear(){
		$obj= new Testipocambios();
        if (Input::hasPost('testipocambios')) {

            $obj = $obj;
            //En caso que falle la operación de guardar
            if (!$obj->save(Input::post('testipocambios'))) {
                Flash::error('Falló Operación');
                //se hacen persistente los datos en el formulario
                $this->testipocambios = $obj;
                return;
            }

            return Router::redirect();
        }
        // Solo es necesario para el autoForm
        $this->testipocambios = $obj;
    }

    /**
     * Edita un Registro
     */
    public function editar($id) {
        View::select('crear');

		$obj= new Testipocambios();
        //se verifica si se ha enviado via POST los datos
        if (Input::hasPost('testipocambios')) {
            $obj = $obj;
            if (!$obj->update(Input::post('testipocambios'))) {
                Flash::error('Falló Operación');
                //se hacen persistente los datos en el formulario
                $this->testipocambios = Input::post('testipocambios');
            }else{
                return Router::toAction('');
            }
        }

        //Aplicando la autocarga de objeto, para comenzar la edición
        $this->testipocambios = $obj->find((int) $id);
    }

    /**
     * Borra un Registro
     */
    public function borrar($id) {
		
		$obj= new Testipocambios();
        if (!$obj->delete((int) $id)) {
            Flash::error('Falló Operación');
        }
        //enrutando al index para listar los articulos
        Router::redirect();
    }

    /**
     * Ver un Registro
     */
    public function ver($id) {
		
		$obj= new Testipocambios();
        $this->result = $obj->find_first((int) $id);
    }
	
	public function guardarhoy()
	{
		
		$id=$_POST['id'];
		$fecha=$_POST['fecha'];
		$compra= $_POST['compra'];
		$cambios= new Testipocambios();
		if(!$cambios->exists('fecha="'.$fecha.'"'))
		{
			$this->id='nuevo';
			$obj= new Testipocambios();
			$obj->fecha=$fecha;
			$obj->compra=$compra;
			$obj->userid=Auth::get('id');
			$obj->aclempresas_id=Session::get('EMPRESAS_ID');
			$obj->estado=1;
			$obj->activo='1';
			if($obj->save()){
			$this->id=$obj->id;
			Session::set("CAMBIO_ID",$obj->id);
			Session::set("CAMBIO_MONTO",$obj->compra);
			$camb=$cambios->find_first((int)$obj->id-1);
			$camb->activo='0';
			$camb->save();
			}
		}else
		{
			$this->id='Editar';
			$cambio=$cambios->find_first($id);
			$cambio->compra=$_POST['compra'];
			if($cambio->save()){
			$this->id=$cambio->id;
			Session::set("CAMBIO_ID",$cambio->id);
			Session::set("CAMBIO_MONTO",$cambio->compra);
			}
		}
}
public function getCambioFecha($fecha='')
{
	$cambios=new Testipocambios();
	if($cambios->exists('fecha="'.$fecha.'"'))
	{
		$val=$cambios->find_first('conditions: fecha="'.$fecha.'"');
		return $val->id;
	}else
	{
		return 0;
	}
	
}
public function reparaeltipodecambio()
{
	$n=0;
	$INGRESOS= Load::model('tesingresos');
	$cambios=new Testipocambios();
	$mensaje=' !!! se cambiaron los siguentes ids:';
	if(Input::post('estado'))$estado=Input::post('estado');else $estado='Pendiente';
	$c=0;
	if(isset($_POST['c'])){$c=$_POST['c'];}
	if($c==1)
	{
		$ingresos=$INGRESOS->find('conditions: estadoingreso="'.$estado.'"');
		$n=0;
		foreach($ingresos as $ingreso)
		{
			$a=$this->getCambioFecha($ingreso->femision);
			if($a!=0)
			{
				$n++;
				$INGRESOS->find_first($ingreso->id);
				$z=$INGRESOS->testipocambios_id;
				$INGRESOS->testipocambios_id=$a;
				if($INGRESOS->save())
				{
					$mensaje.=' ('.$z.' por '.$a;
				}else
				{
					$mensaje.=' No se Cambio NAda';
				}
			}
		}
	}
	$this->c=$c;
	
    if($n==0){Flash::error('No se modifico ningun elemento'.$mensaje);}else{
            Flash::valid('Se modifico '.$n.' Elementos'.$mensaje);}
	$this->estado=$estado;
	$this->ingresos=$INGRESOS->find('conditions: estadoingreso="'.$estado.'"');
	$estados=$INGRESOS->find('columns: estadoingreso','group: estadoingreso');
	$this->estados=$estados;
	
}
	
public function reparaeltipodecambio_salidas()
{
	View::select('reparaeltipodecambio');
	$n=0;
	$SALIDAS= Load::model('tessalidas');
	$cambios=new Testipocambios();
	$mensaje=' !!! se cambiaron los siguentes ids:';
	if(Input::post('estado'))$estado=Input::post('estado');else $estado='Pendiente';
	$c=0;
	if(isset($_POST['c'])){$c=$_POST['c'];}
	if($c==1)
	{
		$salidas=$SALIDAS->find('conditions: estadosalida="'.$estado.'"');
		$n=0;
		foreach($salidas as $salida)
		{
			$a=$this->getCambioFecha($salida->femision);
			if($a!=0)
			{
				$n++;
				$SALIDAS->find_first($salida->id);
				$z=$SALIDAS->testipocambios_id;
				$SALIDAS->testipocambios_id=$a;
				if($SALIDAS->save())
				{
					$mensaje.=' ('.$z.' por '.$a;
				}else
				{
					$mensaje.=' No se Cambio NAda';
				}
			}
		}
	}
	$this->c=$c;
	
    if($n==0){Flash::error('No se modifico ningun elemento'.$mensaje);}else{
            Flash::valid('Se modifico '.$n.' Elementos'.$mensaje);}
	$this->estado=$estado;
	$this->ingresos=$SALIDAS->find('conditions: estadosalida="'.$estado.'"');
	$estados=$SALIDAS->find('columns: estadosalida','group: estadosalida');
	$this->estados=$estados;
	
}	
public function cambiar($id,$fecha)
{
	$this->id=$id;
	$this->fecha=$fecha;
	$cambios=new Testipocambios();
	$this->item=$cambios;
	$cambios->cambiar($id,$fecha);
	
	
}

}
?>