<?php

class InventariosController extends AdminController
{
	public $documento=27;
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	public function index()
	{
		Session::delete('FAMILIA_ID');
		$tipoAlmacenes= new Testipoalmacenes();
		$this->almacenes=$tipoAlmacenes->find();
	}
	function cargarlistado($id=0)
	{
		if($id!=0)
		{
			Session::set('FAMILIA_ID',$id);
			return Redirect::toAction('listado/0');
		}else
		{
			return Redirect::toAction('');
		}
	}
	public function listado($id=0)
	{
		if($id==0)$this->id=$id=Session::get('FAMILIA_ID');
		if($id!=0){
		$this->id=$id;
		$ingresos= new Tesingresos();
		$this->INGR=$ingresos;
		$this->documento;
		$this->inventarios= $ingresos->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND estado=1 AND tesdocumentos_id='.$this->documento);
		/*inventarios por tipo de producto*/
		$tipoporductos= new Testipoproductos();
		$LN= new Teslineaproductos();
		$this->lineas=$LN->find('conditions: testipoalmacenes_id='.$id.' AND aclempresas_id='.Session::get('EMPRESAS_ID'));
		$this->TP= $tipoporductos;
		}else
		{
			return Redirect::toAction('cargarlistado');
		}
	}
	public function editar($linea_id=0,$tipos_id=0,$page=1)
	{
		if(Session::has('INVENTARIO_ID')){}else{Session::set('INVENTARIO_ID',Session::get('INVENTARIO_ID_SP'));}
	//try {
		
		$detalles=new Tesdetalleingresos();
		$this->DETALLE=FALSE;
		$LN= new Teslineaproductos();
		$tipos= new Testipoproductos();
		$productos = new Tesproductos();
		$this->linea=$LN->find_first('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND id='.(int)$linea_id);
		if($tipos_id!=0)
		{
			$con=' and testipoproductos_id='.$tipos_id;
			$this->tipo=$tipos->find_first((int)$tipos_id);
		}else
		{
			$tps=$tipos->find('conditions: teslineaproductos_id='.$linea_id);
			$arrayT=array();
			foreach($tps as $tp):
			 $arrayT[] = "testipoproductos_id= '$tp->id'";
			endforeach;
			$con=' and ('.join(' OR ', $arrayT).')';
			$this->tipo=0;
		}
		$this->tipos_id=$tipos_id;
		$this->linea_id=$linea_id;
		
		//$this->productos=$productos->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND estado=1 and activo=1'.$con,'order: nombre ASC');
		
		$this->productos=$productos->paginate('page: '.$page,'per_page: 30','conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND estado=1 and activo=1'.$con,'order: codigo_ant ASC');
		$this->monedas=0;
		$ING=new Tesingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->titulo = 'Crear Inventario INICAL';
		$this->numero=$ING->generarNumero($this->documento);
		$this->cliente_id="";
		$this->cliente="";
		if(Session::has('INVENTARIO_ID')){
		if($ING->exists("id=".(int)Session::get('INVENTARIO_ID'))){
		$this->DETALLE=TRUE;
		/*
		Cargar los detalles
		*/
		$inventario=$ING->find_first((int)Session::get('INVENTARIO_ID'));
		
		$this->detalles=$detalles;
		$this->id=$inventario->id;
		$this->numero=$inventario->numero;
		$this->glosa=$inventario->glosa;
		$this->serie=$inventario->serie;
		$this->numero=$inventario->numero;
		$this->codigo=$inventario->codigo;//fvencimiento
		$this->femision=$inventario->femision;
		$this->fvencimiento=$inventario->fvencimiento;
		$this->monedas=$inventario->tesmonedas_id;
		$this->cliente_id=$inventario->tesdatos_id;
		$this->totalconigv=$inventario->totalconigv;
		$this->igv=$inventario->igv;
		$this->paid=$inventario->igv;
		
		}else{
		$this->DETALLE=FALSE;
		
		$this->numero=$ING->generarNumero($this->documento);
		$this->detalles=$detalles;
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->monedas=0;
		$this->cliente_id="";
		$this->cliente="";
		$this->id='';
		$this->igv=00.00;
		$this->totalconigv=00.00;
		}
		}
	/*} catch (KumbiaException $e)
	{
       View::excepcion($e);
    }*/
	}
	/* funcion valida para duplicar el detalle del inventario */
	public function duplicar($id=0,$n=0)
	{
		View::template(null);
		$this->n=$n;
		$this->id=$id;
		$this->monedas=1;
		if($id!=0)
		{
			$detalles=new Tesdetalleingresos();
			$det=$detalles->find_first((int)$id);
			$newdetalle=new Tesdetalleingresos();
			foreach($newdetalle->fields as $field)
			{
				if($field!='id')$newdetalle->{$field}=$det->{$field};
			}
			$newdetalle->save();
			$this->det=$detalles->find_first((int)$newdetalle->id);
			$this->monedas=$this->det->getTesingresos()->tesmonedas_id;
			$prod=new Tesproductos();
			$this->item=$prod->find_first((int)$det->tesproductos_id);
		}
		
	}
	
	public function crear($page=1)
	{
	//try {
		$this->DETALLE=FALSE;
		$this->monedas=0;
		$ING=new Tesingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->titulo = 'Crear Inventario INICAL';
		$this->numero=$ING->generarNumero($this->documento);
		$this->observacion='';
		$this->pre_descripcion='';
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->totalconigv=00.00;
		$this->igv=00.00;
		$this->cliente_id="";
		$this->cliente="";
		if(Session::has('INVENTARIO_ID')){
		if($ING->exists("id=".(int)Session::get('INVENTARIO_ID'))){
		$this->DETALLE=TRUE;
		/*
		Cargar los detalles
		*/
		$detalles=new Tesdetalleingresos();
		$inventario=$ING->find_first((int)Session::get('INVENTARIO_ID'));
		
		$this->detalles=$detalles->paginate('page: '.$page,'per_page: 30','conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND tesingresos_id='.$inventario->id,'order: id DESC');
		$this->id=$inventario->id;
		$this->numero=$inventario->numero;
		$this->glosa=$inventario->glosa;
		$this->serie=$inventario->serie;
		$this->numero=$inventario->numero;
		$this->codigo=$inventario->codigo;//fvencimiento
		$this->femision=$inventario->femision;
		$this->fvencimiento=$inventario->fvencimiento;
		$this->monedas=$inventario->tesmonedas_id;
		$this->cliente_id=$inventario->tesdatos_id;
		$this->totalconigv=$inventario->totalconigv;
		$this->igv=$inventario->igv;
		$this->paid=$inventario->igv;
		
		}else{
		$this->DETALLE=FALSE;
		$this->numero=$ING->generarNumero($this->documento);
		$this->subtotal=00.00;
		$this->paid=00.00;
		$this->monedas=0;
		$this->cliente_id="";
		$this->cliente="";
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
	public function cargar($id=0,$url='crear')
	{
		if($id!=0){
		Session::set("INVENTARIO_ID",$id);
		}else
		{
			Session::delete("INVENTARIO_ID");
		}
		return Redirect::toAction($url);
	}
	/*
	GRABAR LA PROFORMA
	###
	*/
	public function grabar($val=0)
	{
		if ($val==1)
		{
			$ING=new Tesingresos();
			if($_POST['id']=='' || $_POST['id']==0){
        	$ingresos = new Tesingresos();
			$ingresos->id=$_POST['id'];
			}else
			{
			$ingresos=$ING->find_first((int)$_POST['id']);
			}
			$ingresos->numero=$_POST['numero'];
			$ingresos->tesdocumentos_id=$this->documento;
			$ingresos->tesdatos_id=1792;
			$ingresos->tesmonedas_id=$_POST['monedas'];
			$ingresos->femision=$_POST['femision'];
			$ingresos->fvencimiento=date("Y-m-d",strtotime($_POST['femision']."+ 90 days"));
			$ingresos->glosa=$_POST['glosa'];
			$ingresos->totalconigv=$_POST['totalconigv'];
			$ingresos->igv=$_POST['igv'];
			$ingresos->serie='0000';
			$ingresos->codigo=$this->documento.'-'.'000'.$_POST['numero'];
			$ingresos->estado=1;
			$ingresos->userid=Auth::get('id');
			$ingresos->activo='1';
			$ingresos->userid=Auth::get('id');
			$ingresos->testipocambios_id=Session::get('CAMBIO_ID');
			$ingresos->aclusuarios_id=Auth::get('id');
			$ingresos->aclempresas_id=Session::get("EMPRESAS_ID");
            if ($ingresos->save())
			{
				Session::set("INVENTARIO_ID",$ingresos->id);
            	//Flash::valid('Efué agregada Exitosamente...!!!');
                Aclauditorias::add("Creo un Inventario INICIAL {$ingresos->glosa} al sistema");
                return Redirect::toAction('respuesta/'.$ingresos->id);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				  return Redirect::toAction('respuesta/0');
             }
         }
	}
	
	/*
	GRABAR DETALLE DEL INVENTARIO
	###
	*/
	public function grabarDetalle($val=0)
	{
		View::select('respuesta');
		$this->id=0;
		if($val!=0)
		{
			$DET=new Tesdetalleingresos();
			if(!$DET->exists('tesproductos_id='.$_POST['productos_id'].' AND tesingresos_id='.Session::get('INVENTARIO_ID')))
			{
				$detalle= new Tesdetalleingresos();
			}else{
				$detalle= $DET->find_first((int)$_POST['id_detalle']);
			}
				$detalle->tesingresos_id=Session::get('INVENTARIO_ID');
				$detalle->tesunidadesmedidas_id=$_POST['tesunidadesmedidas_id'];
				$detalle->tescolores_id=$_POST['tescolores_id'];
				$detalle->tesproductos_id=$_POST['productos_id'];
				$detalle->descripcion=$_POST['pro_descripcion'];
				$detalle->cantidad=$_POST['cantidad'];
				$detalle->lote=$_POST['lote'];
				$detalle->empaque=$_POST['empaque'];
				$detalle->bobinas=$_POST['bobinas'];
				$detalle->pesoneto=$_POST['pesoneto'];
				$detalle->pesobruto=$_POST['pesobruto'];
				$detalle->preciounitario=$_POST['precio'];
				$detalle->importe=$_POST['total'];
				$detalle->userid=Auth::get('id');
				$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
				$detalle->estado=1;
				if($detalle->save())
				{
				$monedas='0';
				$monedas=$detalle->getTesingresos()->getTesmonedas()->id;
				$cambios=new Testipocambios();
				$cam=$cambios->find_first($detalle->getTesingresos()->getCambio($detalle->getTesingresos()->femision));
						switch ($monedas)
					{
						case '1': //moneda es SOLES
								$precioS=$detalle->preciounitario;
								$precioD=($detalle->preciounitario/$cam->compra);
						 		break;
						case '2':  //moneda es Dolares
								$precioD=$detalle->preciounitario;
								$precioS=($detalle->preciounitario*$cam->compra);
						 		break;
						case '4':  //moneda es SOLES
								$precioS=$detalle->preciounitario;
								$precioD=($detalle->preciounitario/$cam->compra);
						 		break;
						case '5':  //moneda es Dolares
								$precioD=$detalle->preciounitario;
								$precioS=($detalle->preciounitario/$cam->compra);
						 		break;
						case '0':  //moneda es SOLES
								$precioS=$detalle->preciounitario;
								$precioD=($detalle->preciounitario/$cam->compra);
						 		break;
					}
						
					$producto = new Tesproductos();
					$pr=$producto->find_first((int)$detalle->tesproductos_id);
					$pr->precio=$precioS;
					$pr->preciod=$precioD;
					$pr->stokminimo=$_POST['stokminimo'];
					$pr->stokmaximo=$_POST['stokmaximo'];
					$pr->ubicacion_almacen=$_POST['ubicacion'];
					$pr->save();
					$this->id=$detalle->id;
			
				}else
				{
					$this->id=0;
				}
		}
		
	}
	
	public function eliminarDetalle($val=0)
	{
		View::select('respuesta');
		$this->id=0;
		if($val!=0)
		{
			$DET=new Tesdetalleingresos();
			$DET->delete($val);
		}
	}
		
	public function respuesta($id=0)
	{
		View::template(null);
		$this->id=$id;
	}
   	
	public function borrar($id) {
        try {
            $id = Filter::get($id, 'digits');

            $obj  = new Tesingresos();

            if (!$obj ->find_first($id)) { //si no existe
                Flash::warning("No existe ninguna Producto");
            } else if ($obj ->borrar()) {
                Flash::valid("EL inventario fue Borrado <b>{$obj->codigo}</b> fué Eliminado...!!!");
                Aclauditorias::add("EL inventario fue Borrado {$obj->codigo} del sistema", 'tesproductos');
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
		$inventario_id=0;
		if(Session::has('INVENTARIO_ID'))
		{
			$inventario_id=Session::get('INVENTARIO_ID');
		}
		$q=$_GET['q'];
		$inventario= new Tesdetalleingresos();
		$obj = new Tesproductos();
		$results = $obj->find('conditions: estado=1 and aclempresas_id='.Session::get('EMPRESAS_ID').' and CONCAT_WS(" ",nombre,detalle,codigo) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			if(!$inventario->exists('tesingresos_id='.$inventario_id.' AND tesproductos_id='.$value->id)){
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
	
	public function cargarInventario($id=0,$tipo=0,$group='tesproductos_id')
	{
		if($id!=0){
		Session::set("INVENTARIO_ID",$id);
		Session::set("TIPOPRODUCTO_ID",$tipo);
		Session::set("GROUP",$group);
		$TipoProducto= new Testipoproductos();
		$tipo=$TipoProducto->find_first((int)$tipo);
		Session::set('NOMBRE_INVENTARIO',$tipo->getTeslineaproductos()->nombre.' '.$tipo->nombre);
		if($group=='tesproductos_id')return Redirect::toAction('inventario_producto');
		if($group=='tescolores_id')return Redirect::toAction('inventario_producto_color');
		if($group=='lote')return Redirect::toAction('inventario_producto_color_lote');
		if($group=='Movimientos')return Redirect::toAction('inventario_movimientos');
		if($group=='detalle')return Redirect::toAction('inventario_producto');
		}else
		{
		Session::delete("INVENTARIO_ID");
		Session::delete("TIPOPRODUCTO_ID");
		Session::delete('NOMBRE_INVENTARIO');
		Session::delete('GROUP');
		return Redirect::toAction('');
		}
		
	}
	public function inventario_producto()
	{
		$ING=new Tesingresos();
		$this->INGR=$ING;
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->inventario=$ING->find((int)Session::get("INVENTARIO_ID"));
		$this->detalles=$DETALLES->getDetallesProductos(Session::get("TIPOPRODUCTO_ID"),$gr='tesproductos_id');
		$this->titulo = 'Ver Inventario Por Producto';
	}
	
	public function inventario_producto_detalle($producto=0,$gr='d.lote')
	{
		$this->gr=$gr;
		$ING=new Tesingresos();$this->INGR=$ING;
		$this->INV= Load::model('inventario');
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		$productos= new Tesproductos();
		$this->inventario=$ING->find((int)Session::get("INVENTARIO_ID"));
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->item=$productos->find_first($producto);
		$this->titulo = 'Ver Inventario Por Producto, Color y Lote';
	}
	public function inventario_producto_color()
	{
		$ING=new Tesingresos();
		$this->INGR=$ING;
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->inventario=$ING->find_first((int)Session::get("INVENTARIO_ID"));
		$this->detalles=$DETALLES->getDetallesProductos(Session::get("TIPOPRODUCTO_ID"),Session::get("GROUP"));
		$this->titulo = 'Ver Inventario Por Producto y Color';
	}
	
	public function inventario_producto_color_lote()
	{
		$ING=new Tesingresos();
		$this->INGR=$ING;
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->inventario=$ING->find((int)Session::get("INVENTARIO_ID"));
		$this->detalles=$DETALLES->getDetallesProductos(Session::get("TIPOPRODUCTO_ID"),Session::get("GROUP"));
		$this->titulo = 'Ver Inventario Por Producto, Color y Lote';
	}
	
	public function inventario_producto_uno($id)
	{
		$ING=new Tesingresos();
		$this->INGR=$ING;
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->inventario=$ING->find((int)Session::get("INVENTARIO_ID"));
		$this->detalles=$DETALLES->getDetallesProductos(Session::get("TIPOPRODUCTO_ID"),Session::get("GROUP"),$id);
		$this->titulo = 'Ver Inventario de un solo producto';
	}
	public function inventario_producto_color_uno($id,$color_id=0)
	{
		$this->id_p=$id;
		$ING=new Tesingresos();
		$this->INGR=$ING;
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->inventario=$ING->find((int)Session::get("INVENTARIO_ID"));
		$adicional='';
		if($color_id!=0){$adicional=' td.tescolores_id='.$color_id.' AND ';}
		$this->detalles=$DETALLES->getDetallesProductos(Session::get("TIPOPRODUCTO_ID"),Session::get("GROUP"),$id,$adicional);
	}
	public function inventario_producto_color_lote_uno($id,$lote='')
	{
		$this->id_p=$id;
		$ING=new Tesingresos();
		$this->INGR=$ING;
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->inventario=$ING->find((int)Session::get("INVENTARIO_ID"));
		$adicional='';
		if($lote!=0){$adicional=' td.lote="'.$lote.'" AND ';}
		$this->detalles=$DETALLES->getDetallesProductos(Session::get("TIPOPRODUCTO_ID"),Session::get("GROUP"),$id,$adicional);
	}
	/*Vista para el inventario Incial*/
public function verinventario()
	{
		$ING=new Tesingresos();
		$this->inven=$ING->find_first((int)Session::get("INVENTARIO_ID"));
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->detalles=$DETALLES->getDetallesI(Session::get("INVENTARIO_ID"));
		
	}
/*KARDEX */
public function inventario_movimientos()
	{
		Session::set('contador',0);
		$ING=new Tesingresos();
		$this->INGR=$ING;
		
		$PRODUCTOS= new Tesproductos();
		$this->PP=$PRODUCTOS->getProductos_json(Session::get("TIPOPRODUCTO_ID"));
		$this->CC=$PRODUCTOS->getColores_json();
		$this->LL=$PRODUCTOS->getLotes_json(Session::get("TIPOPRODUCTO_ID"));
		
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->inventario=$ING->find_first((int)Session::get("INVENTARIO_ID"));
		

	}
public function kardex($i=0)
	{
		$a=Session::get('contador');
		$a++;
		Session::set('contador',$a);
		
		$this->clp=(string)'p'.$a.'imprimir';
		$this->cld=(string)'d'.$a.'contenedor';
		$producto=Input::post('local_producto');
		$color=Input::post('local_color');
		$lote=$this->lote=Input::post('local_lote');
		$fecha_inicio=$this->fecha_inicio=Input::post('fecha_inicio');
		$fecha_fin=$this->fecha_fin=Input::post('fecha_fin');
		$PR=new Tesproductos();
		$CL=new Tescolores();
		if(!empty($producto))
		{
			$p=$PR->find_first('columns: detalle,prefijo','conditions: id='.(int)Input::post('local_producto'));
			$this->producto=$p->detalle;
		}else
		{$this->producto='';}
		if(!empty($color))
		{
			$c=$CL->find_first('columns: nombre','conditions: id='.(int)Input::post('local_color'));
			$this->color=$c->nombre;
			
		}else
		{$this->color='';}
			
		
		if(!empty($fecha_inicio) && !empty($fecha_inicio)){
		$DETALLES= new Tesdetalleingresos();
		$this->detalles=$DETALLES->getKardex($producto,$color,$lote,$fecha_inicio,$fecha_fin);
		
		if(!empty($lote) && !empty($color) && !empty($producto))
		{
			$this->saldo_anterios=$DETALLES->getSaldo($producto,$color,$lote,$fecha_inicio);
		}else
		{
			View::select('vermovimientos');
		}
		}else
		{
		$this->detalles=FALSE;
		Flash::warning("Debe escojer el rango de fechas Gracias");
		}
	}
	public function vermovimientos()
	{
		
	}
	public function inventario_producto_resumen($linea_id,$group='',$anio='')/*recibe el id de la linea productos*/
	{
		$this->anio=Session::get('ANIO');
		if($anio!='')$this->anio=$anio;
		Session::set('contador',0);
		Session::set("GROUP",$group);
		Session::set("INVENTARIO_ID",1);
		$LN= new Teslineaproductos();
		$this->linea=$LN->find_first('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND id='.(int)$linea_id);
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	}
	public function inventario_producto_resumen_mes($id)
	{
		$a=Session::get('contador');
		$a++;
		Session::set('contador',$a);
		$this->clp=(string)'p'.$a.'imprimir';
		$this->cld=(string)'d'.$a.'contenedor';
		$producto=Input::post('local_producto');
		$fecha=$this->fecha=$_POST['fecha'];
		$LN= new Teslineaproductos();
		$this->ln=$LN->find_first('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND id='.(int)$id);
		$ING=new Tesingresos();
		$this->INGR=$ING;
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->inventario=$ING->find((int)Session::get("INVENTARIO_ID"));
		$this->detalles=$DETALLES->getDetallesProductosResumen($id,'tesproductos_id',$fecha);
		$this->titulo = 'Ver Inventario Por Producto';
		

	}
	public function inventario_producto_resumen_anio($id,$anio)
	{
		$fecha=$this->fecha=$anio.'-01-01';
		$fecha_fin=$this->fecha_fin=$anio.'-12-31';
		$LN= new Teslineaproductos();
		$this->ln=$LN->find_first('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND id='.(int)$id);
		$ING=new Tesingresos();
		$this->INGR=$ING;
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->inventario=$ING->find((int)Session::get("INVENTARIO_ID"));
		$this->detalles=$DETALLES->getDetallesProductosResumen($id,'tesproductos_id',$fecha);
		$this->titulo = 'Ver Inventario Por Producto';
		

	}
	public function inventario_tipo_ingreso($id,$ubicacion,$nombre='')
	{
		$ING=new Tesingresos();
		$this->INGR=$ING;
		$this->nombre=$nombre;
		$DETALLES= new Tesdetalleingresos();
		$empresas= new Aclempresas();
		Session::set("INVENTARIO_ID",'1');
		Session::set("GROUP",base64_encode('lote'));
		$LN= new Teslineaproductos();
		$this->ln=$LN->find_first('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND id='.(int)$id);
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->inventario=$ING->find((int)Session::get("INVENTARIO_ID"));
		$this->detalles=$DETALLES->getDetallesProductos_ubicacion($this->ln->id,base64_decode(Session::get("GROUP")),0,$ubicacion);
		$this->titulo = 'Ver Inventario Por Producto';
	}

public function reporte_cajas($id=0)
{
	$tipos=new Testipoproductos();
	$this->tipos=$tipos->find('conditions: teslineaproductos_id=3');
	$cajas=new Tescajas();
	//$this->cajas= array();
	$this->id=$id;
	$this->cajas=$cajas->getStokdecajas($id,'t.detalle,cl.nombre,d.lote,c.numerodecaja ASC');
}
public function ver_reporte_cajas()
{
	
}
public function modificar()
{
	if(Auth::get('id')==3 || Auth::get('id')==2)
	{
		
		Session::set("INVENTARIO_ID",1);
		
	}else
	{
	Flash::warning("No tiene Accesos a esta Accion");	
	return Redirect::toAction('');
	}
	
}
public function modificar_grabar($id=0)
{
	$productos = new Tesproductos();
	$this->id=$id;
	$this->producto=$productos->find_first($id);
	$ING=new Tesingresos();
	$this->inventario=$ING->find_first((int)Session::get("INVENTARIO_ID"));
	$detalles= new Tesdetalleingresos();
	$this->detalles=$detalles->find('conditions: tesproductos_id='.$id.' AND tesingresos_id='.Session::get("INVENTARIO_ID"));
}

public function producto_modificar()
	{
		
		View::select('producto');
		$q=$_GET['q'];
		$inventario= new Tesdetalleingresos();
		$obj = new Tesproductos();
		$results = $obj->find('conditions: estado=1 and aclempresas_id='.Session::get('EMPRESAS_ID').' and CONCAT_WS(" ",nombre,detalle,codigo,codigo_ant) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			
			$id=$value->id;
			//$name=$value->nombre;
			$n=$value->nombre." - (".$value->codigo.')'.$value->detalle.' ['.$value->codigo_ant.']';
			$name=$this->clear($this->htmlcode($n));
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
		}
	}

}


?>