<?php 
View::template('spatricia/default');
Load::models('tessalidas','tesdetallesalidas','tesexportaciones','prodetalletransportes');
class ExportacionesController extends AdminController
{
	public function exportacion($id)
	{
		/*base64_encode($str);
		base64_decode($str);*/
		
		Session::set('SALIDA_ID',$id);
		if (Input::hasPost('expo')) 
		{
		$exportaciones= new Tesexportaciones(Input::post('expo'));
		if($exportaciones->save())
		{
			Flash::valid('Segrabo el tipo de esxportacion');
			return Redirect::toAction('exportacion/'.$id);
		}else
		{
			Flash::warning('No se Pudieron Guardar los Datos...!');
			return Redirect::toAction('exportacion/'.$id);
		}
		}
		if (Input::hasPost('salida')) 
		{
			$salidas= new Tessalidas(Input::post('salida'));
            if ($salidas->save())
			{	
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida/'.$id);
			}else
			{
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$salidas= new Tessalidas();
		
		$detalles = new Tesdetallesalidas();
		
		$expo= new Tesexportaciones();
		$this->valores=$expo->find('conditions: tessalidas_id='.$id,'order: id DESC');
		$this->salida=$salidas->find_first((int)$id);
		$this->detalles=$detalles->find('conditions: tessalidas_id='.(int)$id);
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.$id);
		
	}
	public function versalida($id)
	{
		$salidas= new Tessalidas();
		
		$detalles = new Tesdetallesalidas();
		
		$expo= new Tesexportaciones();
		$this->valores=$expo->find('conditions: tessalidas_id='.$id,'order: id DESC');
		$this->salida=$salidas->find_first((int)$id);
		$this->detalles=$detalles->find('conditions: tessalidas_id='.(int)$id);
		$detalleT= new Prodetalletransportes();
		$this->prodetalletransportes=$detalleT->find_first('conditions: tessalidas_id='.$id);
	}
	public function editar_detalle($id=0,$url='')
	{
	if (Input::hasPost('detalle')) 
	{
		$detalles= new Tesdetallesalidas(Input::post('detalle'));
		$detalles->save();
		return Redirect::toAction('exportacion/'.$detalles->tessalidas_id);
	}
	$detalles= new Tesdetallesalidas();
	$this->detalle=$detalles->find_first($id);
	}
	public function eliminar($salida,$id)
	{
		$expo= new Tesexportaciones();
		$expo->delete($id);
		return Redirect::toAction('exportacion/'.$salida);
	}
}
?>