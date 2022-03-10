<?php 
Load::models('ubigeo');

class UtilesController extends AppController {

protected function before_filter() {
        		
		if (Input::isAjax() ){
			View::response('view');
        }
    }
	/*
	Busqueda de Ubigeo por Distrito
	*/
	public function index()
	{
		View::template('backend/default');
	}
    public function resultados() 
	{
		$q=$_GET['q'];
		$obj = new Ubigeo();
		$results = $obj->find('conditions: distrito like "%'.$q.'%"');
		foreach ($results as $value)
		{
			$id=$value->departamento."-".$value->provincia."-".$value->distrito;
			$name=$value->departamento." / ".$value->provincia." / ".$value->distrito;
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
		}
    }
	/*
	### Busqueda de un campo Cualquiera como auto Ayuda 
	*/
	public function vercampo($model='', $campo='') 
	{
		$value=$_GET['q'];
		$obj = Load::model($model);
		$this->campo=$campo;
		$this->results = $obj->find('columns: '.$campo,'conditions: '.$campo.' like "%'.					$value.'%"','group: '.$campo);
    }
	public function imprimir(){}
	public function excel()
	{
		View::response('view');
		$this->p=$_POST['datos_a_enviar'];$this->nombre=$_POST['nombre'];
	}
	public function txt_factura()
	{
		$this->p=$_POST['datos_a_enviar'];$this->nombre=$_POST['nombre'];
	}
	public function txt_guia()
	{
		$this->p=$_POST['datos_a_enviar'];$this->nombre=$_POST['nombre'];
	}
	public function descargas($file,$ruta)
	{
		View::response('view');
		$this->f=$file;
		$this->ruta=$ruta;
	}
	public function demo()
	{
		$obj = new ToRtf();
		$this->obj=$obj;
	}
	public function validar()
	{
		$this->validar=0;
		$usuarios=Load::model('aclusuarios');
		$c=md5($_POST['clave']);
		$u=$_POST['usuario'];
		$this->url=$_POST['url'];
		if($usuarios->exists('usuario="'.$u.'" AND clave="'.$c.'" AND (aclroles_id=2 OR aclroles_id=4)'))
		{
			$this->validar=1;
		}
		
	}
	
	public function pdf_publico()
	{
		View::template(NULL);
		View::select('pdf');
		setlocale(LC_ALL,"es_ES");
		$this->fecha=strftime("%A %d de %B del %Y");
		$this->direc='P';
		if(!$_POST['direccion'])
		{
		$this->direc=$_POST['direccion'];
		}
		if(!$_POST['datos_a_enviar']){
		$this->p=$_POST['datos_a_enviar_pdf'];
		}else
		{
		
		$this->p=$_POST['datos_a_enviar'];	
		}
		$this->nombre=$_POST['nombre'];
	}
	public function pdf(){}
	
	public function word()
	{
		View::template('');
		View::select('txt');
		setlocale(LC_ALL,"es_ES");
		$this->fecha=strftime("%A %d de %B del %Y");
		if(!$_POST['datos_a_enviar']){
		$this->p=$_POST['datos_a_enviar_word'];
		}else
		{
		
		$this->p=$_POST['datos_a_enviar'];	
		}
		$this->nombre=$_POST['nombre'];
	}
	public function txt()
	{
		View::template('');		
	}

	public function getSessionMenu()
	{
		/*
		Session::Get('menu_oculto');
		Session::Get('menu_oculto');
		si la session esta vacia se almacena la clase si tiene la clase se coloca vacia*/
		Session::has('id_usuario');
		if(Session::set('menu_oculto')){}
		if(Session::set('menu_oculto')){}
		
		
	}

}
?>