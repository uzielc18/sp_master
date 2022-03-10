<?php
View::template('spatricia/default');
Load::models('prorollos','aclempresas','prodetalleproduccion','tesproductos'); 
class RevisionesController extends AppController
{
	protected function before_filter() 
{
        if(Input::isAjax())
		{
			View::template(null);
			//View::select(NULL, NULL);
        }
		
}
	public function index()
	{
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$rollos= new Prorollos();
		$this->rollos=$rollos->find('conditions: estadorollo="Sin Revisar"','order: numero ASC');
	}
	public function cambiarestado($id=0,$estado='',$action)
	{
		$rollos=new Prorollos();
		$rollo=$rollos->find_first((int)$id);
		if($rollo->prodetalleproduccion_id){
		$rollo->estadorollo=$estado;
		}else
		{
		 $rollo->estadorollo=$estado;
		}
		if($rollo->save()){
			if($estado=='Control de Calidad')
			{
				Flash::valid('Rollo enviado a control de Calidad');
			}
			if($estado=='Sin procesos')
			{
				Flash::valid('Rollo Enviado a almacen de crudos');
			}
			if($estado=='')
			{
				Flash::valid('Rollo Enviado a almacen de crudos');
			}
		if ( Input::isAjax() ){
		return Redirect::toAction('vista_view');
        }else{	
		return Redirect::toAction($action);
		}
		}
	}
	public function vista_view()
	{
		View::template(null);
		//View::select(NULL, NULL);
	}
	public function control()
	{
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$rollos= new Prorollos();
		$this->rollos=$rollos->find('conditions: estadorollo="Control de Calidad"','order: numero ASC');
	}
	public function telacruda($anio='')
	{
		$this->anio=Session::get('ANIO');
		if($anio!='')$this->anio=$anio;else $anio=Session::get('ANIO');
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$rollos= new Prorollos();
		$this->rollos=$rollos->find('conditions: DATE_FORMAT(fechacorte,\'%Y-%m\' )="'.$anio.date("-m").'" AND estadorollo="Sin procesos"','order: numero ASC');
	}
	public function telacruda_mes()
	{
		View::template(null);
		$fecha=$this->fecha=$_POST['fecha'];
		$this->fecha=$fecha;
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$rollos= new Prorollos();
		$this->rollos=$rollos->find('conditions: DATE_FORMAT(fechacorte,\'%Y-%m\' )="'.$fecha.'" AND estadorollo="Sin procesos"','order: numero ASC');
	}
	public function telacruda_listado(){
		
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$rollos= new Prorollos();
		$this->rollos=$rollos->find('conditions: estadorollo="Sin procesos"','order: numero ASC');
	}
	public function telacruda_listado_ver(){
		echo $ids=$_POST['productos'];
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$rollos= new Prorollos();
		$this->rollos=$rollos->find('conditions: id in('.$ids.') AND estadorollo="Sin procesos"','order: numero ASC');
	}
	public function grabarrollo()
	{
		View::select('cambiarestado');
		$id=$_POST['id'];
		$rollos= new Prorollos();
		$rollo=$rollos->find_first((int)$id);
		if(isset($_POST['numero']))$rollo->numero=$_POST['numero'];
		if(isset($_POST['metros']))$rollo->metros=$_POST['metros'];
		if(isset($_POST['peso']))$rollo->peso=$_POST['peso'];
		if(isset($_POST['rendimiento']))$rollo->rendimiento=$_POST['rendimiento'];
		if(isset($_POST['estadorollo']))$rollo->estadorollo=$_POST['estadorollo'];
		if(isset($_POST['ordencompra']))$rollo->ordencompra=$_POST['ordencompra'];
		if(isset($_POST['tesordendecompras_id']))$rollo->tesordendecompras_id=$_POST['tesordendecompras_id'];
		if(isset($_POST['revisador']))$rollo->revisador=$_POST['revisador'];
		if(isset($_POST['observacion_control']))$rollo->observacion_control=$_POST['observacion_control'];
		$rollo->save();
		if($rollo->prodetalleproduccion_id){
		$detalle= new Prodetalleproduccion();
		$dd=$detalle->find_first((int)$rollo->prodetalleproduccion_id);
		$dd->metrosrevisados=$_POST['metros'];
		if($dd->save())
		{
			Flash::valid('Metros Modificados');
		}
		}else
		{
			Flash::valid('EL rollo no es de produccion si no de ingreso directo'.$rollo->prodetalleproduccion_id);
		}
	}
	public function listado_articulo($id=0)
	{
		$this->id=$id;
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$rollos= new Prorollos();
		/**/
		$productos = new Tesproductos();
		$this->producto=$productos->find_first($id);
		$this->rollos=$rollos->getRollos_listado($id);
		$this->produccion=$rollos->getRollos_producion($id);
	}
}
?>