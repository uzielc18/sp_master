<?php


class ProformasController extends AdminController
{
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	
	public function index()
	{
		$facturas= new Tesingresos();
		$this->facturas= $facturas->obtenerProformas();
	}
	public function crear()
	{
	//try {
		$this->DETALLE=FALSE;
		$this->monedas=0;
		$PRO=new Tesingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->titulo = 'Crear Proforma';
		$this->numero=$PRO->generarNumero();
		$this->observacion='30 días Habiles. Cargo por Financiamiento del 1,5% se realizará sobre los saldos pendientes de pago después de 30 días';
		$this->pre_descripcion='30 días Habiles. Cargo por Financiamiento del 1,5% se realizará sobre los saldos pendientes de pago después de 30 días';
		$this->subtotal=00.00;
		$this->paid=00.00;
		if(Session::has('PROFORMA_ID')){
		if($PRO->exists("id=".(int)Session::get('PROFORMA_ID'))){
		$this->DETALLE=TRUE;
		/*
		Cargar los detalles
		*/
		$detalles=new Tesdetallefacturas();
		$proforma=$PRO->find_first((int)Session::get('PROFORMA_ID'));
		
		$this->detalles=$detalles->find('conditions: tesfacturas_id='.$proforma->id);
		$this->id=$proforma->id;
		
		$this->numero=$proforma->numero;
		$this->descripcion=$proforma->descripcion;
		$this->fecha=$proforma->fecha;
		$this->observacion=$proforma->observacion;
		$this->pre_descripcion=$proforma->observacion;
		$this->paid=$proforma->descuento;
		$this->descuento=$proforma->descuento;
		$this->monedas=$proforma->tesmonedas_id;
		
		}else{
		$this->DETALLE=FALSE;
		$this->numero=$PRO->generarNumero();
		$this->observacion='30 días Habiles. Cargo por Financiamiento del 1,5% se realizará sobre los saldos pendientes de pago después de 30 días';
		$this->pre_descripcion='30 días Habiles. Cargo por Financiamiento del 1,5% se realizará sobre los saldos pendientes de pago después de 30 días';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->monedas=0;
		}
		}
	/*} catch (KumbiaException $e)
	{
       View::excepcion($e);
    }*/
	}
	/*
	#
	# Ver proforma para editar
	#
	*/
	public function cargar($id=0)
	{
		if($id!=0){
		Session::set("PROFORMA_ID",$id);
		}else
		{
			Session::delete("PROFORMA_ID");
		}
		return Redirect::toAction('crear');
	}
	/*
	GRABAR LA PROFORMA
	###
	*/
	public function grabar($val=0)
	{
		if ($val==1)
		{
			$PRO=new Tesingresos();
			if($_POST['id']==''){
        	$facturas = new Tesingresos();
			$facturas->id=$_POST['id'];
			}else
			{
			$facturas=$PRO->find_first((int)$_POST['id']);
			}
			$facturas->numero=$_POST['numero'];
			$facturas->tesmonedas_id=$_POST['monedas'];
			$facturas->fecha=$_POST['fecha'];
			$facturas->fechafin=date("Y-m-d",strtotime($_POST['fecha']."+ 30 days"));
			$facturas->descripcion=$_POST['descripcion'];
			$facturas->observacion=$_POST['observacion'];
			$facturas->descuento=$_POST['descuento'];
			$facturas->estado=1;
			$facturas->userid=Auth::get('id');
			$facturas->activo='1';
			$facturas->aclusuarios_id=Auth::get('id');
			$facturas->aclempresas_id=Session::get("EMPRESAS_ID");
            if ($facturas->save())
			{
				Session::set("PROFORMA_ID",$facturas->id);
            	Flash::valid('La proforma fué agregada Exitosamente...!!!');
                Aclauditorias::add("Agregó Proforma {$facturas->descripcion} al sistema");
                return Redirect::toAction('respuesta/'.$facturas->id);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
             }
         }
	}
	
	/*
	GRABAR DETALLE DE LA PROFORMA
	###
	*/
	public function grabarDetalle($val=0)
	{
		View::select('respuesta');
		$this->id=0;
		if($val!=0)
		{
			$DET=new Tesdetallefacturas();
			if($_POST['id_detalle']=='0')
			{
			$detalle= new Tesdetallefacturas();
			}else{
			$detalle= $DET->find_first((int)$_POST['id_detalle']);
			}
			$detalle->tesfacturas_id=Session::get('PROFORMA_ID');
			$detalle->tesunidadesmedidas_id=$_POST['tesunidadesmedidas_id'];
			$detalle->tesproductos_id=$_POST['productos_id'];
			$detalle->descripcion=$_POST['pro_descripcion'];
			$detalle->cantidad=$_POST['cantidad'];
			$detalle->precio=$_POST['precio'];
			$detalle->total=$_POST['total'];
			$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
			$detalle->estado=1;
			if($detalle->save()){
			$this->id=$detalle->id;
			}
		}
		
	}
	
	public function eliminarDetalle($val=0)
	{
		View::select('respuesta');
		$this->id=0;
		if($val!=0)
		{
			$DET=new Tesdetallefacturas();
			$DET->delete($val);
		}
	}
		
	public function respuesta($id=0)
	{
		View::template(null);
		$this->id=$id;
	}
	
	public function editar($id) {
        $this->titulo = 'Editar Proforma';
        try {
            View::select('crear');
			$this->tipo_id=0;
            $tesfacturas = new Tesingresos();
            $this->tesfacturas = $tesfacturas->find_first($id);

            if (Input::hasPost('tesfacturas')) {
					$tesfacturas->userid=Auth::get('id');
					$tesfacturas->aclempresas_id=Session::get("EMPRESAS_ID");
                if ($tesfacturas->update(Input::post('tesfacturas'))) {
                    Flash::valid('La Proforma fué actualizada Exitosamente...!!!');
                    Aclauditorias::add("Editó la Proforma {$tesfacturas->descripcion}", 'tesfacturas');
                    return Redirect::toAction('respuesta/'.$tesfacturas->id);
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->tesfacturas); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
   	
	public function activar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $obj = new Tesingresos();
            if (!$obj ->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Area con id '{$id}'");
            }else if ($obj ->activar()) {
                Flash::valid("El Producto<b>{$obj->descripcion}</b> Esta ahora <b>Activo</b>...!!!");
                Aclauditorias::add("Colocó al Producto {$obj->descripcion} como activo", 'testproductos');
            } else {
                Flash::warning('No se pudo Activar el Producto!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado');
    }
    
	public function desactivar($id) {
        try {
            $id = Filter::get($id, 'digits');
            $obj  = new Tesingresos();
            if (!$obj ->find_first($id)){ //si no existe el usuario
                Flash::warning("No existe ningun Producto");
            }else if ($obj ->desactivar()) {
                Flash::valid("El Producto <b>{$obj->descripcion}</b> Esta ahora <b>Inactivo</b>...!!!");
                Aclauditorias::add("Colocó el Producto {$obj->descripcion} como inactivo", 'testproductos');
            } else {
                Flash::warning('No se Pudo Desactivar el Producto...!!!');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado');
    }
	
	public function borrar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $obj  = new Tesingresos();

            if (!$obj ->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Producto");
            } else if ($obj ->borrar()) {
                Flash::valid("Producto <b>{$obj->nombre}</b> fué Eliminado...!!!");
                Aclauditorias::add("Producto {$obj->nombre} del sistema", 'tesproductos');
            } else {
                Flash::warning("No se Pudo Eliminar el Prodcuto <b>{$tesproductos->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Redirect::toAction('listado');
    }
	
	public function producto()
	{
		$proforma_id=0;
		if(Session::has('PROFORMA_ID'))
		{
			$proforma_id=Session::get('PROFORMA_ID');
		}
		$q=$_GET['q'];
		$proforma= new Tesdetallefacturas();
		$obj = new Tesproductos();
		$results = $obj->find('conditions: estado=1 and aclempresas_id='.Session::get('EMPRESAS_ID').' and CONCAT_WS(" ",nombre,detalle,codigo) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			if(!$proforma->exists('tesfacturas_id='.$proforma_id.' AND tesproductos_id='.$value->id)){
			$ID=array();
			foreach($value->fields as $field)
			{
				$ID[$field]=$value->$field;
			}
			$id=$ID;
			//$name=$value->nombre;
			$n=$value->nombre." - (".$value->codigo.')'.$value->detalle;
			$name=$this->clear($this->htmlcode($n));
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
			}
		}
	}
	public function medidas()
	{
		View::select('producto');
		$q=$_GET['q'];
		$obj= new Tesunidadesmedidas();
		$results = $obj->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' and CONCAT_WS(" ",nombre,detalle) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $value->nombre;
			$this->data[] = $json;
		}
	}
	public function grabarcliente($val=0)
	{
		if($val!=0)
		{
			$PROENV= new Tesproformaenviadas();
			$PROENV->tesfacturas_id=$_POST['proforma_id'];
			$PROENV->tesdatos_id=$_POST['cliente_id'];
			$PROENV->save();
			$this->id=$PROENV->id;
		}
	}
	
	public function borrarEnvio($val=0)
	{
		View::select('grabarcliente');
		if($val!=0)
		{
			$PROENV= new Tesproformaenviadas();
			$PROENV->delete($_POST['tesproformaenviadas_id']);
			$this->id=0;
			return TRUE;
		}
	}
	
	public function buscarcliente() 
	{
		$q=$_GET['q'];
		$obj = new Tesdatos();
		$results = $obj->find('columns:id,codigo,razonsocial,ruc,departamento,provincia,distrito,pais,direccion','conditions: testipodatos_id="2" and CONCAT_WS(" ",codigo,razonsocial,ruc,pais) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			$id=$value->id;
			$name=$value->razonsocial."\n ruc: ".$value->ruc." \n(".$value->departamento.' - '.$value->provincia.' - '.$value->distrito.' - '.$value->direccion.')';
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
		}
    }
	
	private function htmlcode($text)
    {
        $stvarno = array ("<", ">");
        $zamjenjeno = array ("&lt;","&gt;");
        $final = str_replace($stvarno, $zamjenjeno, $text);
        return $final;
    }
    private function clear($text)
    {
        $final = stripslashes(stripslashes( $text));
        return $final;
    }
	
}


?>