<?php 

class CajasController extends AdminController
{
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
       }
	public function index()
	{
		/*listado de cajas todos los estado los que esta en almacen los que esta fuera de alamacen y su ubicacion 
		los que esta sin ingresar a alamacen*/
		$CA=new Tescajas();
		//$this->enalamcen=$CA->getListado('enalmacen=1');
		//$this->salidas=$CA->getListado('enalmacen=0');
		$this->libres=$CA->getListado('ISNULL(tescajas.enalmacen)');
	}
	
	public function crear($cj=0,$numero_tr=0)
	{
		if($cj!=0)
		{
			$this->numerodetr=$numero_tr;
			$this->id_detalle=$_POST['id_detalle'];
			$CA=new Tescajas();
			if($CA->exists('estado=1 AND tesdetalleingresos_id='.$_POST['id_detalle']))
			{
				$this->cajas=$CA->find('conditions: estado=1 AND tesdetalleingresos_id='.$_POST['id_detalle']);
			}else
			{
				if($_POST['id_detalle']!=0){
				$numerodecajas=(int)$cj;
				for($i=1;$i<=$numerodecajas;$i++)
				{
					$cajas=new Tescajas();
					$cajas->tesdetalleingresos_id=$_POST['id_detalle'];
					$cajas->numerodecaja=$CA->generarNumero();
					$cajas->estado=1;
					$cajas->userid=Auth::get('id');
					$cajas->aclempresas_id=Session::get('EMPRESAS_ID');
					$cajas->save();
					/*Auditorias*/                    
                    Aclauditorias::add("Agrego una caja al sistema numero = {$cajas->numerodecaja},Tescajas->id={$cajas->numerodecaja}",'Tescajas');
					/*fin Aurditorias*/
				}
				return $this->cajas=$CA->find('conditions: estado=1 AND tesdetalleingresos_id='.$_POST['id_detalle']);
				}
			}
			return $this->cajas=$CA->find('conditions: estado=1 AND tesdetalleingresos_id='.$_POST['id_detalle']);
		}
		//return $this->cajas=$CA->find('conditions: tesdetalleingresos_id=0');
	}
	public function crear_cajas($cj=0,$numero_tr=0,$id_detalle=0)
	{
		$detalles=new Tesdetalleingresos();
		$this->detalle=$detalles->find_first((int)$id_detalle);
		if(Input::hasPost('detalle'))
		{
			$detalles=new Tesdetalleingresos(Input::post('detalle'));
			$detalles->save();
			return Redirect::redirect('/santapatricia/ingresos/cargaringreso/'.$detalles->tesingresos_id.'/crear');
		}
		if($cj!=0)
		{
			$this->numero_tr=$numero_tr;
			$this->id_detalle=$id_detalle;
			$CA=new Tescajas();
			if($CA->exists('estado=1 AND tesdetalleingresos_id='.$id_detalle))
			{
				$this->cajas=$CA->find('conditions: estado=1 AND tesdetalleingresos_id='.$id_detalle);
			}else
			{
				$numerodecajas=(int)$cj;
				for($i=1;$i<=$numerodecajas;$i++)
				{
					$cajas=new Tescajas();
					$cajas->tesdetalleingresos_id=$id_detalle;
					$cajas->numerodecaja=$CA->generarNumero();
					$cajas->estado=1;
					$cajas->userid=Auth::get('id');
					$cajas->aclempresas_id=Session::get('EMPRESAS_ID');
					$cajas->save();
					/*Auditorias*/                    
                    Aclauditorias::add("Agrego una caja al sistema numero = {$cajas->numerodecaja},Tescajas->id={$cajas->numerodecaja}",'Tescajas');
					/*fin Aurditorias*/
				}
				return $this->cajas=$CA->find('conditions: estado=1 AND tesdetalleingresos_id='.$id_detalle);
				
			}
			return $this->cajas=$CA->find('conditions: estado=1 AND tesdetalleingresos_id='.$id_detalle);
		}
		
	}
	public function grabar()
	{
		$this->id=0;
		$cajas=new Tescajas();
		$caja=$cajas->find_first((int)$_POST['id']);
		$caja->id=(int)$_POST['id'];
		$caja->tesdetalleingresos_id=$_POST['id_detalle'];
		$caja->tipodecaja=$_POST['tipodecaja'];
		$caja->pesoneto=$_POST['pesoneto'];
		$caja->conos=$_POST['conos'];
		$caja->numeroant=$_POST['numeroant'];
		$caja->lote=$_POST['lote'];
		$caja->tescolores_id=$_POST['tescolores_id'];
		if($caja->save())
		{
			/*Auditorias*/                    
             Aclauditorias::add("Agrego informacion a la caja numero = {$cajas->numerodecaja},Tescajas->id={$cajas->numerodecaja}",'Tescajas');
			/*fin Aurditorias*/
			$val=$caja->TcajasTbobinasTpneto($_POST['id_detalle']);
			$this->id=$val;
		}
		//$this->cj=$caja;
	}
	public function volver()
	{
		$this->id=0;
		$cajas=new Tescajas();
		$caja=$cajas->find_first((int)$_POST['id']);
		$caja->tesdetalleingresos_id=$_POST['id_detalle'];
		$caja->tipodecaja=$_POST['tipodecaja'];
		$caja->pesoneto=$_POST['pesoneto'];
		$caja->conos=$_POST['conos'];
		$caja->numeroant=$_POST['numeroant'];
		if($caja->save())
		{
			$val=$caja->TcajasTbobinasTpneto($_POST['id_detalle']);
			$this->id=$val;
		}
		//$this->cj=$caja;
	}
	public function crearuno()
	{
		View::template(null);
		if(isset($_POST['id_detalle']))
		{
			$CJ=new Tescajas();
			$caja=new Tescajas();
			$caja->tesdetalleingresos_id=$_POST['id_detalle'];
			$caja->numerodecaja=$CJ->generarNumero();
			$caja->estado=1;
			$caja->userid=Auth::get('id');
			$caja->aclempresas_id=Session::get('EMPRESAS_ID');
			if($caja->save())
			{
				$this->caja=$caja->count('estado=1 AND tesdetalleingresos_id='.$_POST['id_detalle']);
				unset($_POST);
			}else
			{
				$this->caja=FALSE;
			}
		}else{
			$this->caja=FALSE;
		}
		$this->caja=$caja;
	}
	public function borrar($id,$cj=0,$numero_tr=0,$id_detalle=0) {
        try {
            $id = Filter::get($id, 'digits');

            $obj  = new Tescajas();

            if (!$obj ->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Caja");
            } else if ($obj ->borrar()) {
                $total=$obj->count('estado=1 AND tesdetalleingresos_id='.$obj->tesdetalleingresos_id);
			/*Auditorias*/                    
             Aclauditorias::add("Eliminno una caja numero = {$cajas->numerodecaja},Tescajas->id={$cajas->numerodecaja}",'Tescajas');
			/*fin Aurditorias*/
                //Aclauditorias::add("EL inventario fue Borrado {$obj->codigo} del sistema", 'tesproductos');
            } else {
                //Flash::warning("No se Pudo Eliminar el Prodcuto <b>{$tesproductos->nombre}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
		$this->id=$total;
        //return Redirect::toAction('listado');
    }

public function borrarsalidacajas($detalle_id,$caja_id)
{
	View::select('index');
	$cajas= new Tescajas();
	$caja = $cajas->find_first((int)$caja_id);
	if($caja){
	$caja->enalmacen='1';
	$caja->save();
	}else{
		Flash::warning("No existe ninguna Caja");
	}
	$detalle=new Tesdetallesalidas();
	$detalle->delete($detalle_id);
	Redirect::redirect('/santapatricia/hilos/agregardetalles');
}
public function grabarsalidacajas($id=0)
{
	if($id!=0)
	{
			$cajas= new Tescajas();
			$caja=$cajas->find_first((int)$id);
			
			$detalle=new Tesdetallesalidas();
			$detalle->tessalidas_id=Session::get('SALIDA_ID');
			$detalle->tesunidadesmedidas_id=$caja->getTesdetalleingresos()->tesunidadesmedidas_id;
			$detalle->tescolores_id=$caja->getTesdetalleingresos()->tescolores_id;
			$detalle->tesproductos_id=$caja->getTesdetalleingresos()->tesproductos_id;
			$CC=explode('-',$caja->getStockCC($id));
			$detalle->cantidad=$CC[0];
			$detalle->empaque='1';
			$detalle->bobinas=$CC[1];
			$detalle->lote=$caja->getTesdetalleingresos()->lote;
			$detalle->pesoneto=$CC[0];
			$detalle->pesobruto=$caja->getTesdetalleingresos()->pesobruto;
			$detalle->tescajas_id=$id;
			$detalle->userid=Auth::get('id');
			$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
			$detalle->estado=1;
			if($detalle->save()){
				Flash::valid("Agregado a guia");
			/*Auditorias*/                    
             Aclauditorias::add("Agrego una caja a la salida id_de_la_caja = {$detalle->tescajas_id},Tesdetallesalidas->id={$detalle->numerodecaja}",'Tesdetallesalidas');
			/*fin Aurditorias*/
			$caja->enalmacen='0';
			$caja->save();
			$this->detalles=$detalle->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
				}
			$this->detalles=$detalle->find('conditions: tessalidas_id='.Session::get('SALIDA_ID'));
			
	}else
	{
		Flash::warning("No existe ninguna Caja");
		return Redirect::toAction('index/');
	}
}	
/*recibe y el id del detalle a a ingresar las cajas en una nota de pedido 
se tiene que recibir el id del detalle y generar la nota de ingreso al alamacen y recien poner el 1 en la cajas 
*/
public function ingreso_caja_almacen($id=0,$tesdatos_id=0)
{
	$CA=new Tescajas();
	$ingresar_cajas=$CA->count('id','conditions: ISNULL(enalmacen)');
	if($ingresar_cajas>0){
		$Npedidos= new Pronotapedidos();
		/*graba el pedido*/
		$nota= new Pronotapedidos();
		$nota->aclusuarios_id=Auth::get('id');
		$nota->tesdatos_id=$tesdatos_id;
		$nota->codigo=$Npedidos->codigo("'salida'");
		$nota->descripcion='Ingreso de cajas al almacen';
		$nota->observacion='Ingreso de cajas al almacen';
		$nota->estadonota='Entregado';
		$nota->estado=1;
		$nota->activo=1;
		$nota->aclempresas_id=Session::get('EMPRESAS_ID');
		$nota->tipo='ingreso';
		$nota->origen='Cajas';
		if($nota->save()){
		/*se graba el detalle del pedido de acuerdo con el tipo de caja o empaque que hay en la base de dato*/
		
			/*Auditorias*/                    
             Aclauditorias::add("Segenera el pedido para el ingreso de cajas al almacen numero = {$nota->codigo},Pronotapedidos->id={$nota->id}",'Pronotapedidos');
			/*fin Aurditorias*/
			
		$productos= new Tesproductos();
		$ps=$productos->find('conditions: testipoproductos_id =3 AND activo="1"');
		foreach($ps as $p): 
			$cond=' ';
		if($id!=0)$cond=' tesdetalleingresos_id='.$id.' AND ';
		$cc=$CA->count('id','conditions:'.$cond.'ISNULL(enalmacen) AND tipodecaja='.$p->id);
		if($cc>0)
		{
		$obj=$OBJ= new Prodetallepedidos();
		$obj->pronotapedidos_id=$nota->id;
		$obj->tesproductos_id=$p->id;
		$obj->tescolores_id='773';
		$obj->descripcion='Ingreso Automatico de Cajas a almacen';
		$obj->cantidad=$cc;
		$obj->tesunidadesmedidas_id='13';/* UNIDAD */
		$obj->precio=$p->precio;
		$obj->activo=1;
		$obj->estado=1;
		$obj->save();
		}
		
		endforeach;
		/*Auditorias*/                    
             Aclauditorias::add("Seagrega el detalle del pedido = {$nota->codigo},Pronotapedidos->id={$nota->id}",'Prodetallepedidos');
			/*fin Aurditorias*/
		$CA->update_all("enalmacen =  '1'","ISNULL(enalmacen)");
		
		}
		Flash::valid("Cajas enviadas a almacen Gracias");
	}else
	{
		Flash::warning("No existe ninguna Caja");
	}
	
	
}

/*Salida Interna de Hilos Para TRAMA*/

public function grabarsalidacajas_interna($id=0)
{
	if($id!=0)
	{
			$cajas= new Tescajas();
			$caja=$cajas->find_first((int)$id);
			
			$detalle=new Tesdetallesalidasinternas();
			$detalle->tessalidasinternas_id=Session::get('SALIDA_ID_I');
			$detalle->tesunidadesmedidas_id=$caja->getTesdetalleingresos()->tesunidadesmedidas_id;
			$detalle->tescolores_id=$caja->getTesdetalleingresos()->tescolores_id;
			$detalle->tesproductos_id=$caja->getTesdetalleingresos()->tesproductos_id;
			$CC=explode('-',$caja->getStockCC($id));
			$detalle->cantidad=$CC[0];
			$detalle->empaque='1';
			$detalle->bobinas=$CC[1];
			$detalle->lote=$caja->getTesdetalleingresos()->lote;
			$detalle->pesoneto=$CC[0];
			$detalle->pesobruto=$caja->getTesdetalleingresos()->pesobruto;
			$detalle->tescajas_id=$id;
			$detalle->userid=Auth::get('id');
			$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
			$detalle->estado=1;
			if($detalle->save()){
			Flash::valid("Agregado a guia");
			$caja->enalmacen='0';
			$caja->save();
			$this->detalles=$detalle->find('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
				}
			$this->detalles=$detalle->find('conditions: tessalidasinternas_id='.Session::get('SALIDA_ID_I'));
			
	}else
	{
		Flash::warning("No existe ninguna Caja");
		return Redirect::toAction('index/');
	}
}	
public function borrarsalidacajas_i($detalle_id,$caja_id)
{
	View::select('index');
	$cajas= new Tescajas();
	$caja = $cajas->find_first((int)$caja_id);
	if($caja){
	$caja->enalmacen='1';
	$caja->save();
	}else{
		Flash::warning("No existe ninguna Caja");
	}
	$detalle=new Tesdetallesalidasinternas();
	$detalle->delete($detalle_id);
	Redirect::redirect('/santapatricia/salidasinternas/agregardetalles');
}

public function actualizar_cajas($tipo=0)
{
	$tipos=new Testipoproductos();
	$this->tipos=$tipos->find('conditions: teslineaproductos_id=3');
	$CAJAS= new Tescajas();
	$this->cajas=$cajas=$CAJAS->ActualizarCajas($tipo);
	//$this->cajas=$cajas=$CAJAS->find('conditions: estado!=9');
	$view='Seleccione tipo';
	$this->id_t_p=$tipo;
	if($tipo!=0){
	foreach($cajas as $c): 
		$S=explode("-",$c->getStockCC($c->id));
	$S[0];
	if($S[0]<1){
	$caja= $CAJAS;
	$caja->find_first($c->id);
	$caja->estado=9;/*Sin Kilos*/
	$caja->enalmacen=9;/*Sin Kilos*/
	$caja->save();
	$view.="Se termino esta caja ".$c->numerodecaja;
	$view.='<br />';
	}
	endforeach;
	/*elimianr las cajas que tengan null conos o pesoneto*/
	$e=$CAJAS->count('conditions: isnull(pesoneto) or isnull(conos)');
	if($e>0)
	{
		$e_cajas=$CAJAS->find('conditions: isnull(pesoneto) or isnull(conos)');
		foreach($e_cajas as $e_c):
            $detalleingreso = new Tesdetalleingresos();
            $caja= new Tescajas();
            $c_validar=$caja->find_first($e_c->id);
            if(!$detalleingreso->exists('id='.$c_validar->tesdetalleingresos_id)){
                $CAJAS->delete($e_c->id);
                $view.='Se elimino esta caja'.$e_c->id.'- ';
            }

		endforeach;
	}
	}
	Flash::info($view);
}
public function reparar_cantidad()
{
	$CAJAS= new Tescajas();
	$sql="SELECT sum(tescajas.pesoneto) as total, tesdetalleingresos_id, d.cantidad,d.pesoneto FROM `tescajas` INNER JOIN tesdetalleingresos as d ON d.id=tescajas.tesdetalleingresos_id AND (d.cantidad=0 or isnull(d.cantidad)) GROUP BY tesdetalleingresos_id";
	$cajas=$CAJAS->find_all_by_sql($sql);
	foreach($cajas as $caja)
	{
		$detalles=new Tesdetalleingresos();
		$detalles->find_first($caja->tesdetalleingresos_id);
		$detalles->cantidad=$caja->total;
		$detalles->pesoneto=$caja->total;
		$detalles->save();
	}
	
	return Redirect::toAction('index/');
	
}
public function reparar_color_lote()
{
	$CAJAS= new Tescajas();
	$sql="SELECT * FROM tescajas WHERE 1=1 GROUP BY tesdetalleingresos_id";
	$cjs=$CAJAS->find('conditions: ISNULL(tescolores_id) GROUP BY tesdetalleingresos_id');
	$ver='';
	foreach($cjs as $cj)
	{
		$detalles=new Tesdetalleingresos();
		if($detalles->exists("id=".(int)$cj->tesdetalleingresos_id)){
		$detalle=$detalles->find_first($cj->tesdetalleingresos_id);
		$cajas=$CAJAS->find('conditions: ISNULL(tescolores_id) AND tesdetalleingresos_id='.$detalle->id);
		foreach($cajas as $caja)
		{
			$update_caja= new Tescajas();
			$update_caja->find_first($caja->id);
			$update_caja->tescolores_id=$detalle->tescolores_id;
			$update_caja->lote=$detalle->lote;
			$update_caja->save();
		}
		}else
		{
		$ver.='detalle_id= caja detalle_id='.$cj->tesdetalleingresos_id.'<br />';	
		}
		
	}
	Flash::info($ver);
	return Redirect::toAction('index/');
}
public function liberar_cajas(){
    $CAJAS= new Tescajas();
}

}
?>