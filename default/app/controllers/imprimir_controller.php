<?php 
Load::models('tesdocumentos','tessalidas','prodetalletransportes','tesdetallesalidas','aclempresas','tesingresos','tesletrassalidas','tesfacturasadelantos','tesordendecompras','tesaplicacionfacturasadelantos','tesdatos','prodetalleacabados','prodetalleprocesos','proprocesos','sc_urdidos','tesdetallesalidasinternas','tessalidasinternas','tesletrassalidasinternas','tesexportaciones');
class ImprimirController extends AppController {
    protected function before_filter() {
        if (Input::isAjax() ){
			View::response('view');
            //View::select(NULL, NULL);
        }
    }
	public function index()
	{
		
	}
	
	public function facturas()
	{
		View::template('');
		$detalles = new Tesdetallesalidas();
		$DETALLES=$detalles->find('columns: tesdetallesalidas.'.join(',tesdetallesalidas.',$detalles->fields).',
sum(ifnull(tesdetallesalidas.cantidad,0)) as totalcantidad,
sum(ifnull(tesdetallesalidas.importe,0)) as totalimporte,
sum(ifnull(tesdetallesalidas.descuento,0)) as totaldescuento','conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"),'group: tesproductos_id,descuento');
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas= new Tessalidas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$DETALLES;
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		$letrassalida=new Tesletrassalidas();
		$this->lets=$letrassalida->find('conditions: factura_id='.Session::get('SALIDA_ID'));
		/* Busa las aplicaciones a esta factura */
		$aplicaciones= new Tesaplicacionfacturasadelantos();
		$this->aplicaciones=FALSE ;
		if($aplicaciones->exists('tessalidas_id='.$this->salida->id))
		{
			$this->aplicaciones =$aplicaciones->find('conditions: tessalidas_id='.$this->salida->id);
		}
	}
	public function exportaciones()
	{
		View::template('');
		$detalles = new Tesdetallesalidas();
		$DETALLES=$detalles->find('columns: tesdetallesalidas.'.join(',tesdetallesalidas.',$detalles->fields).',
sum(ifnull(tesdetallesalidas.cantidad,0)) as totalcantidad,
sum(ifnull(tesdetallesalidas.importe,0)) as totalimporte,
sum(ifnull(tesdetallesalidas.descuento,0)) as totaldescuento','conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"),'group: tesproductos_id,descuento');
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas= new Tessalidas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$DETALLES;
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		$letrassalida=new Tesletrassalidas();
		$this->lets=$letrassalida->find('conditions: factura_id='.Session::get('SALIDA_ID'));
		/*Datos de la exportacion*/
		
		$exportacion= new Tesexportaciones();
		$this->valores=$exportacion->find('conditions: tessalidas_id='.Session::get("SALIDA_ID"),'order: id DESC');
		/* Busa las aplicaciones a esta factura */
		$aplicaciones= new Tesaplicacionfacturasadelantos();
		
		$this->aplicaciones=FALSE ;
		if($aplicaciones->exists('tessalidas_id='.$this->salida->id))
		{
			$this->aplicaciones =$aplicaciones->find('conditions: tessalidas_id='.$this->salida->id);
		}
	}
	public function guias()
	{
		View::template('');
		$detalles = new Tesdetallesalidas();
		$DETALLES=$detalles->find('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"),'order: tesproductos_id ASC');
		//$DETALLES=$detalles->find('columns: tesdetallesalidas.'.join(',tesdetallesalidas.',$detalles->fields).',sum(tesdetallesalidas.cantidad) as totalcantidad,sum(tesdetallesalidas.importe) as totalimporte,sum(tesdetallesalidas.descuento) as totaldescuento','conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"),'group: tesproductos_id');
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas= new Tessalidas();
		$this->salida=$salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		/*PARA LAS GUIAS DE PLEGADOR EN SANTA PATRICIA*/
		$this->glosa=$this->salida->glosa;
		if(!empty($this->salida->glosa)){
		$gl=explode(',',$this->glosa);
		$det=array();
		foreach($gl as $id=>$value)
		{
			if(!empty($value)){
			$cl=explode(':',$value);
			$det[$cl[0]]=$cl[1];
			}
		}
		$this->titulo_hilo=$det['HILOS'];
		$this->color=$det['COLOR'];
		$this->ancho=$det['ANCHO'];
		$this->nhilos=$det['#HILOS'];
		if(array_key_exists('TOTAL',$det))
		{
			$this->total=$det['TOTAL'];
		}else
		{
			$this->total=0;
		}
		$this->metros=$det['METROS'];
		}else
		{
			
		}
		
		$SCURDIDOS= new ScUrdidos();
		if($SCURDIDOS->exists('tessalidas_id='.$salida->id)){
		$this->urdido=$SCURDIDOS->find_first('conditions: tessalidas_id='.$salida->id);
		}else{
		$this->urdido=FALSE;
		}
		if(!empty($salida->direccion_entrega)){$this->direccion=$salidas->getTesdatos()->direccion;}else{$this->direccion=$salida->direccion_entrega;}
		$this->ubigeo=$salidas->getTesdatos()->departamento.' - '.$salidas->getTesdatos()->provincia.' - '.$salidas->getTesdatos()->distrito;
		$this->detalles=$DETALLES;
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		$letrassalida=new Tesletrassalidas();
		$this->lets=$letrassalida->find('conditions: factura_id='.Session::get('SALIDA_ID'));
		/*TIntoreria*/
		$PR= new Proprocesos();
		$detalle_A=new Prodetalleacabados();
		$detalle_p= new Prodetalleprocesos();
		$this->proceso=$PR->find_first('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
		if($this->proceso){
		$this->detalle_A=$detalle_A->getAcabos($this->proceso->id,0);
		$this->detalle_B=$detalle_A->getAcabos($this->proceso->id,1);
		}
		/*Transportes*/
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.$salidas->id);
		
	}
	
	public function detalles()
	{
		View::select('guias');
		View::template('');
		$detalles = new Tesdetallesalidasinternas();
		$DETALLES=$detalles->find('conditions: tessalidasinternas_id='.(int)Session::get("SALIDA_ID_I"));
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas= new Tessalidasinternas();
		$this->salida=$salida=$salidas->find_first((int)Session::get('SALIDA_ID_I'));
		/*PARA LAS GUIAS DE PLEGADOR EN SANTA PATRICIA*/
		$this->glosa=$this->salida->glosa;
		if(!empty($this->salida->glosa)){
		$gl=explode(',',$this->glosa);
		$det=array();
		foreach($gl as $id=>$value)
		{
			if(!empty($value)){
			$cl=explode(':',$value);
			$det[$cl[0]]=$cl[1];
			}
		}
		$this->titulo_hilo=$det['HILOS'];
		$this->color=$det['COLOR'];
		$this->ancho=$det['ANCHO'];
		$this->nhilos=$det['#HILOS'];
		if(array_key_exists('TOTAL',$det))
		{
			$this->total=$det['TOTAL'];
		}else
		{
			$this->total=0;
		}
		$this->metros=$det['METROS'];
		}else
		{
			
		}
				
		$this->urdido=FALSE;
		$this->proceso=FALSE;
		
		$this->direccion=$salidas->getTesdatos()->direccion;
		$this->ubigeo=$salidas->getTesdatos()->departamento.' - '.$salidas->getTesdatos()->provincia.' - '.$salidas->getTesdatos()->distrito;
		$this->detalles=$DETALLES;
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
		$letrassalida=new Tesletrassalidasinternas();
		$this->lets=$letrassalida->find('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
		
		/*Transportes*/
		$detalleT= new Prodetalletransportes();
		if($detalleT->exists('tessalidasinternas_id='.$salidas->id)){
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidasinternas_id='.$salidas->id);
		}else
		{
			$this->prodetalletransportes=FALSE;
		}
	}
	/*NOTA DE CREDITO Y NOTA DE DEBITO*/
	public function notas($id)
	{
		$salidas=new Tessalidas();
		$this->salida=$salidas->find_first($id);
		$DETALLES=new Tesdetallesalidas();
		$this->detalles=$DETALLES->find('conditions: tessalidas_id='.$id);
	}
	
	
/*modificar àra imprimri una letra*/	
public function letras($factura_id=0,$dato_id,$letra_id=0){
	$letras= new Tesletrassalidas();
	$tesdatos= new Tesdatos();
	$this->dato=$tesdatos->find_first($dato_id);
	if($letras->exists('factura_id="'.$factura_id.'" AND aclempresas_id='.Session::get('EMPRESAS_ID').''))
	{
		$this->letras=$letras->getLetras_rtf($factura_id,$letra_id);
	}else
	{
	return Router::toAction('');
	}
}
public function letra($factura_id=0,$dato_id,$letra_id=0){
	$letras= new Tesletrassalidas();
	$tesdatos= new Tesdatos();
	$this->dato=$tesdatos->find_first($dato_id);
	if($letras->exists('id='.$letra_id))
	{
		$this->letras=$letras->getLetras_rtf($factura_id,$letra_id);
	}else
	{
	return Router::toAction('');
	}


}
public function descargar($ruta) 
{
	View::response('view');
	$this->ruta=$ruta;	
}

}
?>