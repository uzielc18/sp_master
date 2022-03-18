<?php 

class ConfeccionesController extends AdminController
{
protected function before_filter() 
{
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
		$this->titulo='Guias para confecciones con la serie 002';
		$this->url='crearsalidas';
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)15);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		/*
		serie 002
		salida de Hilos
		*/
		$salidas= new Tessalidas();
		$this->salidas= $salidas->find('conditions: DATE_FORMAT(femision,"%Y-%m")="'.$anio.'-'.$mes_activo.'" AND serie="002" AND estadosalida!="TERMINADO" AND npedido like "TR%" AND estado=1 AND aclempresas_id='.Session::get('EMPRESAS_ID').' AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at DESC');
		Session::delete("SALIDA_ID");
	}

	public function salidas_i()
	{
		$this->titulo='Guias con la serie 999';
		$this->url='crearsalida_interna';
		View::select('salidas');
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)15);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
		/*
		serie 002
		salida de Hilos
		*/
		$salidas= new Tessalidas();
		$this->salidas= $salidas->find('conditions: estadosalida!="TERMINADO" AND npedido like "TR%" AND serie="999" AND estado=1 AND aclempresas_id='.Session::get('EMPRESAS_ID').' AND tesdocumentos_id='.Session::get('DOC_ID'),'order: fecha_at DESC');
		Session::delete("SALIDA_ID");
	}
	public function crearsalidas()
	{
		$SALD=new Tessalidas();
		$this->salida['serie']='002';
		$this->salida['numero']=$SALD->generarNumero(Session::get('DOC_ID'),'002');
		$this->salida['npedido']=$SALD->getNumeropedido('TR','002');
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
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
				$detalleT->tessalidas_id=$salidas->id;
				$detalleT->numero=$salidas->numero;
				$detalleT->serie=$salidas->serie;
				$detalleT->fechatraslado=$salidas->finiciotraslado;
				$detalleT->estado='1';
				$detalleT->userid=Auth::get('id');
				$detalleT->save();
				
				Session::set("SALIDA_ID",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
				
		         /*Auditorias*/
				 Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')." , numero = {$salidas->serie}-{$salidas->numero} al sistema, Total={$salidas->totalconigv},Tessalidas->id={$salidas->id}",'Tessalidas');
				/*fin Aurditorias*/
				
                return Redirect::toAction('agregardetalles/');
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				 $this->salida=Input::post('salida');
				 return Redirect::toAction('crearsalidas/');
             }
		}
	}
	
	public function crearsalida_interna()
	{
		View::select('crearsalidas');
		$SALD=new Tessalidas();
		$this->salida['serie']='999';
		$this->salida['numero']=$SALD->generarNumero_interno(Session::get('DOC_ID'),'999','TR');
		$this->salida['npedido']=$SALD->getNumeropedido('TR','999');
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
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
				$detalleT->tessalidas_id=$salidas->id;
				$detalleT->numero=$salidas->numero;
				$detalleT->serie=$salidas->serie;
				$detalleT->fechatraslado=$salidas->finiciotraslado;
				$detalleT->estado='1';
				$detalleT->userid=Auth::get('id');
				$detalleT->save();
				
				Session::set("SALIDA_ID",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
                /*Auditorias*/
				 Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')." , numero = {$salidas->serie}-{$salidas->numero} al sistema, Total={$salidas->totalconigv},Tessalidas->id={$salidas->id}",'Tessalidas');
				/*fin Aurditorias*/
				
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
	public function agregardetalles()
	{
		$salidas= new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$rollos=new Prorollos();
		$this->rollos=$rollos->find('conditions: (estadorollo="VENTA") AND enalmacen="1"');
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$detalles->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
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
				$detalleT->tessalidas_id=$salidas->id;
				$detalleT->numero=$salidas->numero;
				$detalleT->serie=$salidas->serie;
				$detalleT->fechatraslado=$salidas->finiciotraslado;
				$detalleT->save();
				
				Session::set("SALIDA_ID",$salidas->id);
            	
				//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Edito la Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
				
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
			$salidas= new Tessalidas(Input::post('salida'));
            if ($salidas->save())
			{
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida/');
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$salidas= new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$detalles->find('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
		
		
	}
	
	public function versalida_i()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas= new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$this->salida=$salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles=$detalles->find('conditions: tessalidas_id='.(int)Session::get("SALIDA_ID"));
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
	}
	
	public function cargarsalida($id=0,$url='agregardetalles')
	{
		if($id!=0){
		Session::set("SALIDA_ID",$id);
		}else
		{
			Session::delete("SALIDA_ID");
		}
		return Redirect::toAction($url);
	}	


}

?>