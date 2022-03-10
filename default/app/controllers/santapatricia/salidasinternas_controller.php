<?php 
View::template('spatricia/default');
Load::models('tesproductos','testipoproductos','teslineaproductos','tesdocumentos','tessalidasinternas','prodetalletransportes','tescajas','tesdetallesalidasinternas');
class SalidasinternasController extends AdminController
{
	public $per_page=30;
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	public function salidas($Y='',$m='')
	{
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)15);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		/*
		serie 002
		salida de Hilos  AND DATE_FORMAT(femision, "%Y-%m")="'.date("Y-m").'" 
		*/
		$salidas= new Tessalidasinternas();
		$this->salidas= $salidas->find('conditions: npedido like "HL%" AND DATE_FORMAT(femision, "%Y-%m")="'.$anio.'-'.$mes_activo.'" AND estado=1 AND tesdocumentos_id='.Session::get('DOC_ID').'','order: fecha_at DESC');
		Session::delete("SALIDA_ID_I");
	}
	public function crearsalidas()
	{
		$SALD=new Tessalidasinternas();
		$this->salida['serie']='999';
		$this->salida['numero']=$SALD->generarNumeroH(Session::get('DOC_ID'),'999','HL');
		$this->salida['npedido']=$SALD->getNumeropedido('HL');
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
			$salidas->tesdocumentos_id=Session::get('DOC_ID');
			$salidas->femision=date('Y-m-d');
			$salidas->fvencimiento=date("Y-m-d",strtotime($salidas->femision."+ 30 days"));
			$salidas->estadosalida='Editable';
			$salidas->estado=1;
			$salidas->userid=Auth::get('id');
			$salidas->activo='1';
			$salidas->testipocambios_id=Session::get('CAMBIO_ID');
			$salidas->userid=Auth::get('id');
			$salidas->aclusuarios_id=Auth::get('id');
			$salidas->aclempresas_id=Session::get("EMPRESAS_ID");
            if ($salidas->save())
			{
				$detalleT= new Prodetalletransportes(Input::post('prodetalletransportes'));
				$detalleT->tessalidasinternas_id=$salidas->id;
				$detalleT->numero=$salidas->numero;
				$detalleT->serie=$salidas->serie;
				$detalleT->fechatraslado=$salidas->finiciotraslado;
				$detalleT->estado='1';
				$detalleT->userid=Auth::get('id');
				$detalleT->save();
				
				Session::set("SALIDA_ID_I",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('agregardetalles/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('crearsalidas/');
             }
		}
	}
	/*INGRESA CON SESSION SALIDA_ID*/
	public function agregardetalles($id_t_p=0)
	{
		$salidas= new Tessalidasinternas();
		$detalles = new Tesdetallesalidasinternas();
		$cajas=new Tescajas();
		$tipos=new Testipoproductos();
		//$campos= 'tescajas.'.join(',tescajas.',$cajas->fields).'';
		$this->tipos=$tipos->find('conditions: teslineaproductos_id=3');
		$this->id_t_p=$id_t_p;
		$this->cajas=$cajas->getStokdecajas($id_t_p);
		//$this->cajas=$cajas->find('columns: '.$campos,'conditions: tescajas.enalmacen="1"','join: INNER JOIN tesdetalleingresos as d ON d.id=tescajas.tesdetalleingresos_id','order: tescajas.numerodecaja ASC');
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID_I'));
		$this->detalles=$detalles->find('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
			$salidas->tesdocumentos_id=Session::get('DOC_ID');
			$salidas->femision=date('Y-m-d');
			$salidas->fvencimiento=date("Y-m-d",strtotime($salidas->femision."+ 30 days"));
			$salidas->estadosalida='Editable';
			$salidas->estado=1;
			$salidas->userid=Auth::get('id');
			$salidas->activo='1';
			$salidas->testipocambios_id=Session::get('CAMBIO_ID');
			$salidas->userid=Auth::get('id');
			$salidas->aclusuarios_id=Auth::get('id');
			$salidas->aclempresas_id=Session::get("EMPRESAS_ID");
            if ($salidas->save())
			{
				$detalleT= new Prodetalletransportes(Input::post('prodetalletransportes'));
				$detalleT->tessalidasinternas_id=$salidas->id;
				$detalleT->numero=$salidas->numero;
				$detalleT->serie=$salidas->serie;
				$detalleT->fechatraslado=$salidas->finiciotraslado;
				$detalleT->save();
				
				Session::set("SALIDA_ID",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
                return Redirect::toAction('agregardetalles/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('agregardetalles/');
             }
		}
	}
	
	/*Finalizar y destruir la session creadas para la generacion de la salida de hilo*/
	public function versalida()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidasinternas(Input::post('salida'));
            if ($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$salidas= new Tessalidasinternas();
		$detalles = new Tesdetallesalidasinternas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID_I'));
		$this->detalles=$detalles->find('conditions: tessalidasinternas_id='.(int)Session::get("SALIDA_ID_I"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
	}
	
	public function versalida_i()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		
		$salidas= new Tessalidasinternas();
		$detalles = new Tesdetallesalidasinternas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID_I'));
		$this->detalles=$detalles->find('conditions: tessalidasinternas_id='.(int)Session::get("SALIDA_ID_I"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
	}
	
public function cargarsalida($id=0,$url='agregardetalles')
	{
		if($id!=0){
		Session::set("SALIDA_ID_I",$id);
		}else
		{
			Session::delete("SALIDA_ID_I");
		}
		return Redirect::toAction($url);
}

public function eliminar($id)
{
	$salidas= new Tessalidasinternas();
	$detalles = new Tesdetallesalidasinternas();
	if($detalles->exists(''))
	{
	}
}

}
?>