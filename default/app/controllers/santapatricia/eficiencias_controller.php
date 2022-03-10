<?php 
View::template('spatricia/default');
Load::models('promaquinas','acldatos','proeficiencias','tesproductos','proturnos');
class EficienciasController extends AdminController
{

protected function before_filter()
{
	if (Input::isAjax())
	{
		View::template(null);
		//View::select(NULL, NULL);
	}
}
public function selector()
{
	/*Seleccionar el tipo de maquina*/
	$tipoproductos= new Testipoproductos();
	$this->tipos=$tipoproductos->find('conditions: id!=37 AND teslineaproductos_id=9');
}	
public function index($Y='',$m='')
{	Session::delete('MAQUINA_ID');
	Session::delete('MAQUINA_NOMBRE');
	Session::delete('MAQUINA_PREFIJO');
	Session::delete('MAQUINA_NUMERO');
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	$proeficiencias=new Proeficiencias();
	$n=0;
	foreach($acldatos=$proeficiencias->find('conditions: !ISNULL(acldatos_id)','columns: id,acldatos_id','group: acldatos_id') as $t):
	$this->vals=count($acldatos);
	$n++;
	$a=$t->acldatos_id;
	$this->{'nombre'.$n}=$t->getAcldatos()->nombre.' '.$t->getAcldatos()->appaterno;
	$this->{'maquinas'.$n}=$proeficiencias->find('conditions: acldatos_id='.$t->acldatos_id,'columns: promaquinas_id','group: promaquinas_id','order: promaquinas_id ASC');
	$this->{'fechas'.$n}=$proeficiencias->find('conditions: acldatos_id='.$t->acldatos_id.' AND DATE_FORMAT(fecha,"%Y-%m")="'.$anio.'-'.$mes_activo.'"','columns: id,promaquinas_id,fecha,valor','order: fecha ASC');
	//$this->valores=$proeficiencias->find('columns: id,promaquinas_id,fecha,valor');
	endforeach;
}


public function turnos($Y='',$m='')
{	Session::delete('MAQUINA_ID');
	Session::delete('MAQUINA_NOMBRE');
	Session::delete('MAQUINA_PREFIJO');
	Session::delete('MAQUINA_NUMERO');
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	$proeficiencias=new Proeficiencias();
	$proturnos = new Proturnos();
	$n=0;
	foreach($proturnos->find('conditions: 1=1','order: hora_inicio ASC') as $t):
	$n++;
	$a=$t->id;
	$this->{'nombre'.$n}=$t->nombre;
	$this->{'maquinas'.$a}=$proeficiencias->find('conditions: proturnos_id='.$t->id,'columns: promaquinas_id','group: promaquinas_id','order: promaquinas_id ASC');
	$this->{'fechas'.$a}=$proeficiencias->find('conditions: proturnos_id='.$t->id.' AND DATE_FORMAT(fecha,"%Y-%m")="'.$anio.'-'.$mes_activo.'"','columns: id,promaquinas_id,fecha,valor','order: fecha ASC');
	//$this->valores=$proeficiencias->find('columns: id,promaquinas_id,fecha,valor');
	endforeach;
	$this->vals=$n;
	
}
public function maquinas($Y='',$m='')
{	Session::delete('MAQUINA_ID');
	Session::delete('MAQUINA_NOMBRE');
	Session::delete('MAQUINA_PREFIJO');
	Session::delete('MAQUINA_NUMERO');
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	$proeficiencias=new Proeficiencias();
	$maquinas = new Promaquinas();
	$n=0;
	foreach($maquinas->getListado(36) as $t):
	$n++;
	$a=$t->id;
	$this->{'nombre'.$n}=$t->nombre;
	$this->{'maquinas'.$a}=$proeficiencias->find('conditions: promaquinas_id='.$t->id,'columns: promaquinas_id','group: promaquinas_id','order: promaquinas_id ASC');
	$this->{'fechas'.$a}=$proeficiencias->find('conditions: promaquinas_id='.$t->id.' AND DATE_FORMAT(fecha,"%Y-%m")="'.$anio.'-'.$mes_activo.'"','columns: id,promaquinas_id,fecha,valor,proturnos_id,tejedor','order: fecha,proturnos_id ASC');
	//$this->valores=$proeficiencias->find('columns: id,promaquinas_id,fecha,valor');
	endforeach;
	$this->vals=$n;
	
}
public function produccion($Y='',$m='')
{	Session::delete('MAQUINA_ID');
	Session::delete('MAQUINA_NOMBRE');
	Session::delete('MAQUINA_PREFIJO');
	Session::delete('MAQUINA_NUMERO');
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	$proeficiencias=new Proeficiencias();
	$n=0;
	foreach($acldatos=$proeficiencias->find('conditions: !ISNULL(acldatos_id)','columns: id,acldatos_id','group: acldatos_id') as $t):
	$this->vals=count($acldatos);
	$n++;
	$a=$t->acldatos_id;
	$this->{'nombre'.$n}=$t->getAcldatos()->nombre.' '.$t->getAcldatos()->appaterno;
	$this->{'maquinas'.$n}=$proeficiencias->getEfi($t->acldatos_id,'p.id,p.promaquinas_id,p.fecha,p.valor,p.acldatos_id',' GROUP BY p.promaquinas_id',' ORDER BY p.promaquinas_id ASC');
	$this->{'fechas'.$n}=$proeficiencias->getEfi($t->acldatos_id,'p.id,p.promaquinas_id,p.fecha,p.valor,p.acldatos_id',' GROUP BY s.id',' ORDER BY p.fecha, p.proturnos_id, p.promaquinas_id ASC',$anio,$mes_activo);
	//$this->valores=$proeficiencias->find('columns: id,promaquinas_id,fecha,valor');
	endforeach;
}

public function eficiencia($Y='',$m='')
{	Session::delete('MAQUINA_ID');
	Session::delete('MAQUINA_NOMBRE');
	Session::delete('MAQUINA_PREFIJO');
	Session::delete('MAQUINA_NUMERO');
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	$proeficiencias=new Proeficiencias();
	$n=0;
	foreach($acldatos=$proeficiencias->find('conditions: !ISNULL(acldatos_id)','columns: id,acldatos_id','group: acldatos_id') as $t):
	$this->vals=count($acldatos);
	$n++;
	$a=$t->acldatos_id;
	$this->{'id'.$n}=$a;
	$this->{'nombre'.$n}=$t->getAcldatos()->nombre.' '.$t->getAcldatos()->appaterno;
	$this->{'maquinas'.$n}=$proeficiencias->getEfi($t->acldatos_id,'p.id,p.promaquinas_id,p.fecha,p.valor,p.acldatos_id',' GROUP BY p.promaquinas_id',' ORDER BY p.promaquinas_id ASC');
	$this->{'fechas'.$n}=$proeficiencias->getEfi($t->acldatos_id,'p.id,p.promaquinas_id,p.fecha,p.valor,p.acldatos_id',' GROUP BY s.id',' ORDER BY p.fecha, p.proturnos_id, p.promaquinas_id ASC',$anio,$mes_activo);
	//$this->valores=$proeficiencias->find('columns: id,promaquinas_id,fecha,valor');
	endforeach;
}
public function cargarmaquina($id=0,$url='crear')
{
	if($id!=0)
	{
		$maquinas=new Promaquinas();
		$maq=$maquinas->find_first((int)$id);
		Session::set('MAQUINA_ID',$maq->id);
		Session::set('MAQUINA_NOMBRE',$maq->nombre);
		Session::set('MAQUINA_PREFIJO',$maq->prefijo);
		Session::set('MAQUINA_NUMERO',$maq->numero);
		return Redirect::toAction($url);
	}
}
public function crear()
{
	$eficiencias = new Proeficiencias();
	$maquinas=new Promaquinas();
	$this->detalles=$eficiencias->find('conditions: promaquinas_id='.Session::get('MAQUINA_ID').' AND DATE_FORMAT(fecha,"%Y-%m")="'.date('Y-m').'"','order: fecha,proturnos_id ASC');
}
public function grabar_eficiencia()
{
	if (Input::hasPost('eficiencia')) {
		  $ef = new Proeficiencias(Input::post('eficiencia'));
		  $ef->aclempresas_id=Session::get('EMPRESAS_ID');
		  $ef->promaquinas_id=Session::get('MAQUINA_ID');
		  $ef->aclusuarios_id=Auth::get('id');
		  $ef->estado='1';
		  if ($ef->save())
		  {
			  /*Auditorias*/
				 Aclauditorias::add("Ingreso una eficiencia a la maquina , maquina_id = {Session::get('MAQUINA_ID')}, Proeficiencias->id={$ef->id}",'Proeficiencias');
				/*fin Aurditorias*/
		  Flash::valid('Creado correctamente');
		  Input::delete('eficiencia'); 
			
		  } else {
			  Flash::warning('No se Pudieron Guardar los Datos...!!!');
		  }
	  }
	$maquinas=new Promaquinas();
	$this->ultimo_detalle=$maquinas->getMaxvalor(Session::get('MAQUINA_ID'));
	$eficiencias = new Proeficiencias();
	$this->detalles=$eficiencias->find('conditions: promaquinas_id='.Session::get('MAQUINA_ID').' AND DATE_FORMAT(fecha,"%Y-%m")="'.date('Y-m').'"','order: fecha ASC');
}
public function getdatos(){
	$q=$_GET['q'];
	$obj = new Acldatos();
	/*Tejedores*/
	$results = $obj->find('conditions: placargos_id=13 AND CONCAT_WS(" ",nombre,appaterno,apmaterno,dni) like "%'.$q.'%"');
 	foreach ($results as $value)
		{
			$name=$value->nombre.' '.$value->appaterno.' '.$value->apmaterno.' ('.$value->dni.')';
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $name;
			$json['tejedor'] =$value->nombre.' '.$value->appaterno.' '.$value->apmaterno;
			$this->data[] = $json;
		}	
	}
public function getmaquinas()
{
	View::select("getdatos");
	$q=$_GET['q'];
	$obj = new Tesproductos();
	$results = $obj->getProductos_tipo($q,9);
 	foreach ($results as $value)
		{
			//('.$value->prefijo.'-'.$value->numero.')
			$name=$value->nombre.' '.$value->detalle;
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $name;
			$this->data[] = $json;
		}	
}
/*$id Recibe el id del turno*/
public function anterior_ingreso($maquina_id,$id=0,$datos_id=0)
{
	$maquinas=new Promaquinas();
	$this->maq=$maquinas->find_first((int)$maquina_id);
	$this->ultimo_detalle=$maquinas->getMaxvalor($maquina_id,$id,$datos_id);
	/*VAlidar al grabar con el ingreso anterior*/
}

public function eliminar($id=0)
{
	$eficiencias = new Proeficiencias();
	$efi=$eficiencias->find_first((int)$id);
	$promaquinas_id=$efi->promaquinas_id;
	$eficiencias->delete((int)$id);
	return Redirect::toAction('cargarmaquina/'.$promaquinas_id.'/crear');
}
/*INGRESAR EFICIENCIA PARA CHENILERA*/

public function coneras()
{
	$maquinas=new Promaquinas();
	$this->maquinas = $maquinas->getListado(140);
	$eficiencias = new Proeficiencias();
	$this->eficiencias=$eficiencias->find('conditions: !ISNULL(fecha_inicio)');
	$this->eficiencias_fechas=$eficiencias->find('conditions: !ISNULL(fecha_inicio)','group: fecha_inicio');
}

public function grabar_coneras()
{
	$maquinas=new Promaquinas();
	$this->maquinas =$maqs= $maquinas->getListado(140);
	if(Input::hasPost('grabar'))
    {
		$f_i=Input::post('fecha_inicio');
		$f=Input::post('fecha');
		$h_i=Input::post('hora_inicio');
		$h_f=Input::post('hora_fin');
		foreach($maqs as $m):
		for($i=0;$i<2;$i++){
		$ef = new Proeficiencias(Input::post('eficiencia-'.$i.'-'.$m->id));
		  $ef->aclempresas_id=Session::get('EMPRESAS_ID');
		  $ef->promaquinas_id=$m->id;
		  $ef->aclusuarios_id=Auth::get('id');
		  $ef->fecha_inicio=$f_i;
		  $ef->fecha=$f;
		  $ef->hora_inicio=$h_i;
		  $ef->hora_fin=$h_f;
		  $ef->estado='1';
		  $ef->save();
		}
		endforeach;
		return Redirect::toAction('coneras');
	}
	/*getListado => pide el id del tipo de prosuctos de la lina 9*/
	
}
public function coneras_edit($id)
{
	if (Input::hasPost('eficiencia')) {
		  $ef = new Proeficiencias(Input::post('eficiencia'));
		  if ($ef->save())
		  {
			 Flash::valid('Modificado correctamente');
		    Input::delete('eficiencia'); 
			
		  } else {
			  Flash::warning('No se Pudieron Guardar los Datos...!!!');
		  }
		  
	  return Redirect::toAction('coneras');
	  }
	  $ef = new Proeficiencias();
	  $this->eficiencia=$ef->find_first((int)$id);
}

}


?>