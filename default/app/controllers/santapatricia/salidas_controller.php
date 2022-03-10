<?php 
View::template('spatricia/default');
Load::models('tesdocumentos','tessalidas','tesdetallesalidas','tesproductos','aclempresas','tesdatos','tesunidadesmedidas','testipocambios','testipoproductos','teslineaproductos','subcuentas','tescolores','promovimientos','proplegadores','protransportes','protransportistas','prodetalletransportes');
class SalidasController extends AdminController
{
	protected function before_filter()
	{
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
		
	/*
	GRABAR LA SALIDA
	*/
	public function grabar($val=0)
	{
		if(isset($_POST['cliente_id'])){
		if ($val==1)
		{
			$SAL=new Tessalidas();
			if($_POST['id']=='' || $_POST['id']==0){
        	$salidas = new Tessalidas();
			$salidas->id=$_POST['id'];
			}else
			{
			$salidas=$SAL->find_first((int)$_POST['id']);
			}
			if(isset($_POST['serie']))$salidas->serie=$_POST['serie'];
			if(isset($_POST['numero']))$salidas->numero=$_POST['numero'];
			$salidas->tesdocumentos_id=(int)Session::get('DOC_ID');
			$salidas->aclusuarios_id=Auth::get('id');
			if(isset($_POST['cliente_id']))$salidas->tesdatos_id=$_POST['cliente_id'];
			if(isset($_POST['monedas']))$salidas->tesmonedas_id=$_POST['monedas'];
			if(isset($_POST['femision']))$salidas->femision=$_POST['femision'];
			if(isset($_POST['fvencimiento']))$salidas->fvencimiento=$_POST['fvencimiento']; else $salidas->fvencimiento=date("Y-m-d",strtotime($_POST['femision']."+ 30 days"));
			$salidas->fregistro=date('Y-m-d');
			if(isset($_POST['npedido']))$salidas->npedido=$_POST['npedido'];
			if(isset($_POST['numeroguia']))$salidas->numeroguia=$_POST['numeroguia'];
			if(isset($_POST['numerofactura']))$salidas->numerofactura=$_POST['numerofactura'];
			if(isset($_POST['ordendecompra']))$salidas->ordendecompra=$_POST['ordendecompra'];
			if(isset($_POST['finiciotraslado']))$salidas->finiciotraslado=$_POST['finiciotraslado'];
			if(isset($_POST['movimiento']))$salidas->movimiento=$_POST['movimiento'];
			if(isset($_POST['glosa']))$salidas->glosa=$_POST['glosa'];
			if(isset($_POST['totalconigv']))$salidas->totalconigv=$_POST['totalconigv'];
			if(isset($_POST['cuantagastos']))$salidas->cuantagastos=$_POST['cuantagastos'];
			if(isset($_POST['cuentaporpagar']))$salidas->cuentaporpagar=$_POST['cuentaporpagar'];
			if(isset($_POST['igv']))$salidas->igv=$_POST['igv'];
			if(isset($_POST['totalenletras']))$salidas->totalenletras=$_POST['totalenletras'];
			if(isset($_POST['codigo']))$salidas->codigo=$_POST['codigo'];
			if(isset($_POST['direccion']))$salidas->direccion_entrega=$_POST['direccion'];
			if(isset($_POST['tesdatosdirecciones_id']))$salidas->tesdatosdirecciones_id=$_POST['tesdatosdirecciones_id'];
			$salidas->estado=1;
			$salidas->userid=Auth::get('id');
			$salidas->activo='1';
			$salidas->testipocambios_id=Session::get('CAMBIO_ID');
			$salidas->userid=Auth::get('id');
			$salidas->aclusuarios_id=Auth::get('id');
			$salidas->aclempresas_id=Session::get("EMPRESAS_ID");
            if($salidas->save())
			{
				/* inicio 
				cuardar en las tablas dependientes a tessalidas como son : teschequessalidas, tesletrassalidas*/
				/*graba el detalle del transportista en caso de guias*/
				if($_POST['protransportes_id']!='' && $_POST['protransportistas_id']!=''){
				$detalleT= new Prodetalletransportes();
				if(!$detalleT->exists('tessalidas_id='.$salidas->id.' AND protransportes_id='.$_POST['protransportes_id'].' AND protransportistas_id='.$_POST['protransportistas_id'])){
				$detalleT->tessalidas_id=$salidas->id;
				$detalleT->protransportes_id=$_POST['protransportes_id'];
				$detalleT->protransportistas_id=$_POST['protransportistas_id'];
				$detalleT->numero=$salidas->numero;
				$detalleT->serie=$salidas->serie;
				$detalleT->fechatraslado=$salidas->finiciotraslado;
				$detalleT->estado='1';
				$detalleT->userid=Auth::get('id');
				$detalleT->save();
				}
				}
				/* fin */
				Session::set("SALIDA_ID",$salidas->id);
            	//Flash::valid('fue agregada Exitosamente...!!!');
                Aclauditorias::add("Creo una Nuevo Salida ".Session::get('DOC_NOMBRE')."-{$salidas->numero} al sistema");
                return Redirect::toAction('respuesta/'.$salidas->id);
				unset($_POST);
             } else {
                 Flash::warning('No se Pudieron Guardar los Datos...!!!');
				  return Redirect::toAction('respuesta/0');
             }
         }
		}else
		{
			return Redirect::toAction('respuesta/9');
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
			$DET=new Tesdetallesalidas();
			if(!$DET->exists('tesproductos_id='.$_POST['productos_id'].' AND tessalidas_id='.Session::get('SALIDA_ID')))
			{
				$detalle= new Tesdetallesalidas();
			}else{
				$detalle= $DET->find_first((int)$_POST['id_detalle']);
			}
			$detalle->tessalidas_id=Session::get('SALIDA_ID');
			if(isset($_POST['tesunidadesmedidas_id']))$detalle->tesunidadesmedidas_id=$_POST['tesunidadesmedidas_id'];
			if(isset($_POST['colores_id']))$detalle->tescolores_id=$_POST['colores_id'];
			if(isset($_POST['productos_id']))$detalle->tesproductos_id=$_POST['productos_id'];
			if(isset($_POST['cantidad']))$detalle->cantidad=$this->definido($_POST['cantidad']);
			if(isset($_POST['empaque']))$detalle->empaque=$this->definido($_POST['empaque']);
			if(isset($_POST['bobinas']))$detalle->bobinas=$this->definido($_POST['bobinas']);
			if(isset($_POST['pesoneto']))$detalle->pesoneto=$this->definido($_POST['pesoneto']);
			if(isset($_POST['pesobruto']))$detalle->pesobruto=$this->definido($_POST['pesobruto']);
			if(isset($_POST['precio']))$detalle->preciounitario=$_POST['precio'];
			if(isset($_POST['total']))$detalle->importe=$_POST['total'];
			$detalle->userid=Auth::get('id');
			$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
			$detalle->estado=1;
			if($detalle->save()){
				
				switch($detalle->getTesproductos()->testipoproductos_id)
				{
					/*PARA GRABAR EL MOV del plegador*/
					case '37': 
						$mov=new Promovimientos();
						$mov->update_all('estado=1','conditions: proplegadores_id='.$detalle->getTesproductos()->getProplegadores()->id);
						if($mov->exists('proplegadores_id='.$detalle->getTesproductos()->getProplegadores()->id.' AND tessalidas_id='.Session::get('SALIDA_ID')))
						{
						$MOV=new Promovimientos();
						$mov=$MOV->find_first('proplegadores_id='.$detalle->getTesproductos()->getProplegadores()->id.' AND tessalidas_id='.Session::get('SALIDA_ID'));
						}else
						{
						$mov=new Promovimientos();
						}
						/* COLOR DE URDIDO*/
						$gl=explode(',',$detalle->getTessalidas()->glosa);
						foreach($gl as $id=>$value)
						{
							if(!empty($value)){
							$cl=explode(':',$value);
							$det[trim($cl[0])]=$cl[1];
							}
						}
						/**/
						$mov->proplegadores_id=$detalle->getTesproductos()->getProplegadores()->id;
						$mov->observacion='Salida de plegador';
						$mov->tessalidas_id=Session::get('SALIDA_ID');
						$mov->descripcion=$detalle->getTessalidas()->glosa;
						if(array_key_exists('COLOR',$det))$mov->colorurdido=$det['COLOR'];
						$mov->tesingresos_id='0';
						$mov->estadomov='proceso';
						$mov->estado='1';
						$mov->activo='1';
						$mov->userid=Auth::get('id');
							if($mov->save())
							{
								$plegador= new Proplegadores();
								$pl=$plegador->find_first((int)$detalle->getTesproductos()->getProplegadores()->id);
								$pl->estadoplegador_id=4;
								$pl->save();
							}
						;break;
					default: break;
				}
				$this->id=$detalle->id;
				unset($_POST);
			}else
			{
				$this->id=0;
			}
		}
		
	}
	
	/*
	# @serie LA serie de la guia de remision.
	# @numero el numero de la guia de remision. 
	#
	*/
	public function facturaguia($serie=0,$numero=0)
	{
		
		$this->DETALLE=TRUE;
		if($serie==0 && $numero==0)
		{
		$salidas= new Tessalidas();
		$guia=$salidas->find_first(Session::get('SALIDA_ID'));
		$guia->update('numeroguia: ');
		$guia->save();
		$this->DETALLE=FALSE;
		$detalles= new Tesdetallesalidas();
		$detalles->delete('tesingresos_id='.Session::get('SALIDA_ID'));
		$this->monedas=$guia->tesmonedas_id;
		}else
		{
		$salidas= new Tessalidas();
		$guia=$salidas->find_first('columns: id,tesmonedas_id','conditions: tesdocumentos_id=15 AND aclempresas_id='.Session::get('EMPRESAS_ID').' AND serie="'.$serie.'" AND numero="'.$numero.'"');
		$this->monedas=$guia->tesmonedas_id;
		$m=$guia->tesmonedas_id;
		$detalles= new Tesdetallesalidas();
		$det_guia=$detalles->find('conditions: tesingresos_id='.$guia->id);
			foreach($det_guia as $d)
			{
				$detalle= new Tesdetallesalidas();
				$detalle->tessalidas_id=Session::get('SALIDA_ID');
				$detalle->tesunidadesmedidas_id=$d->tesunidadesmedidas_id;
				$detalle->tescolores_id=$d->tescolores_id;
				$detalle->tesproductos_id=$d->tesproductos_id;
				$detalle->descripcion=$d->descripcion;
				$detalle->cantidad=$d->pesoneto;
				$detalle->empaque=$d->empaque;
				$detalle->bobinas=$d->bobinas;
				$detalle->pesoneto=$d->pesoneto;
				$detalle->pesobruto=$d->pesobruto;
				switch ($m)
					{
						case 1: //moneda es SOLES
								$precio=$d->getTesproductos()->precio;
						 		break;
						case 2:  //moneda es Dolares
								$precio=$d->getTesproductos()->preciod;
						 		break;
						case 4:  //moneda es SOLES
								$precio=$d->getTesproductos()->precio;
						 		break;
						case 5:  //moneda es Dolares
								$precio=$d->getTesproductos()->preciod;
						 		break;
						case 0:  //moneda es SOLES
								$precio=$d->getTesproductos()->precio;
						 		break;
					}
				$detalle->preciounitario=$precio;
				$detalle->importe=$d->pesoneto*$precio;
				$detalle->userid=Auth::get('id');
				$detalle->aclempresas_id=Session::get('EMPRESAS_ID');
				$detalle->estado=1;
				$detalle->save();
			}
			$this->detalles=$detalles->find('conditions: tessalidas_id='.Session::get('SALIDAS_ID'));
		}
	}
	
	public function eliminarDetalle($val=0)
	{
		View::select('respuesta');
		$this->id=0;
		if($val!=0)
		{
			$DET=new Tesdetallesalidas();
			$DET->delete($val);
		}
	}
		
	public function respuesta($id=0)
	{
		View::template(null);
		$this->id=$id;
	}
	
	public function producto()
	{
		$this->data=[];
		$inventario_id=0;
		if(Session::has('SALIDAS_ID'))
		{
			$inventario_id=Session::get('SALIDA_ID');
		}
		$q=$_GET['q'];
		$inventario= new Tesdetallesalidas();
		$obj = new Tesproductos();
		$results = $obj->find('conditions: estado=1 and aclempresas_id='.Session::get('EMPRESAS_ID').' and CONCAT_WS(" ",nombre,detalle,codigo) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			if(!$inventario->exists('tessalidas_id='.$inventario_id.' AND tesproductos_id='.$value->id)){
			$ID=array();
			foreach($value->fields as $field)
			{
				$ID[$field]=$value->$field;
			}
			
			//$name=$value->nombre;
			switch($value->testipoproductos_id)
			{
				case 37: $opcional=' - '.$value->getProplegadores()->numero;$ID['PESO']=$value->getProplegadores()->peso;break;
				default: 
				$opcional='';
				break;
			}
			$id=$ID;
			$n=$value->detalle.' ('.$value->codigo.$opcional.')';
			$name=$this->clear($this->htmlcode($n));
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name;
			$this->data[] = $json;
			}
		}
	}
	public function medidas()
	{	$this->data=[];
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
	public function cuentasG()
	{	$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj=new Subcuentas();
		$results = $obj->find('conditions: CONCAT_WS(" ",codigo,descripcion) like "'.$q.'%"');
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =$value->codigo;
			$json['name'] = $value->codigo.'<span style="font-size:9px;">('.$value->descripcion.')</span>';
			$this->data[] = $json;
		}
	}
	public function cuentasP()
	{$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj=new Subcuentas();
		$results = $obj->find('conditions: CONCAT_WS(" ",codigo,descripcion) like "'.$q.'%"');
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =$value->codigo;
			$json['name'] = $value->codigo.'<span style="font-size:9px;">('.$value->descripcion.')</span>';
			$this->data[] = $json;
		}
	}
	
	public function buscarcliente() 
	{$this->data=[];
		$q=$_GET['q'];
		$obj = new Tesdatos();
		$results = $obj->getDatos_c($q);
		foreach ($results as $value)
		{
			$mas='(';
			if(!empty($value->id_proveedor)){$id=$value->id_proveedor;$mas.='Proveedor';}
			if(!empty($value->id_proveedor) && !empty($value->id_cliente))$mas.=' y ';
			if(!empty($value->id_cliente)){$id=$value->id_cliente;$mas.='Cliente';}
			$mas.=')';
			$name=$value->name;
			$json = array();
			$json['id'] =$value->id_cliente;
			$json['name'] = $name.$mas;
			if(!empty($value->id_cliente))$id_c=$value->id_cliente; else $id_c=0;
			$json['id_cliente'] = $id_c;			
			if(!empty($value->id_proveedor))$id_p=$value->id_proveedor; else $id_p=0;
			$json['id_proveedor'] = $id_p;
			$this->data[] = $json;
		}
		
		
    }
	public function buscarproveedor() 
	{
		View::select('buscarcliente');
		$this->data=[];
		$q=$_GET['q'];
		$obj = new Tesdatos();
		$results = $obj->getDatos_p($q);
		foreach ($results as $value)
		{
			$mas='(';
			if(!empty($value->id_proveedor)){$id=$value->id_proveedor;$mas.='Proveedor';}
			if(!empty($value->id_proveedor) && !empty($value->id_cliente))$mas.=' y ';
			if(!empty($value->id_cliente)){$id=$value->id_cliente;$mas.='Cliente';}
			$mas.=')';
			$name=$value->name;
			$json = array();
			$json['id'] =$value->id_proveedor;
			$json['name'] = $name.$mas;
			if(!empty($value->id_cliente))$id_c=$value->id_cliente; else $id_c=0;
			$json['id_cliente'] = $id_c;			
			if(!empty($value->id_proveedor))$id_p=$value->id_proveedor; else $id_p=0;
			$json['id_proveedor'] = $id_p;
			$this->data[] = $json;
		}
		
		
    }
	public function direcciones_clientes($id,$value=0)
	{
		$this->id=$id;$this->value=$value;
		
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
	private function definido($variable)
	{
		if($variable=='undefined')
		{
			$val='0';
		}else
		{
			$val=$variable;
		}
		return $val;
	}
	
	public function colores()
	{$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj= new Tescolores();
		$results = $obj->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND CONCAT_WS(" ",nombre,codigo) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $value->nombre;
			$this->data[] = $json;
		}
	}
	/*
	# busca las guia para el ingreso de la factura en caso sea nescesario.
	*/
	public function guiadelafactura()
	{
		$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj= new Tessalidas();
		$results = $obj->find('conditions: tesdocumentos_id=15 AND aclempresas_id='.Session::get('EMPRESAS_ID').' AND CONCAT_WS("-",serie,numero) like "'.$q.'%"');
		foreach ($results as $value)
		{	
		/*
		#valida la existencia de dicho serie-numero en otro ingreso anterior
		*/
			if(!$obj->exists('numeroguia="'.$value->serie.'-'.$value->numero.'"')){
			$json = array();
			$json['id'] =$value->serie.'-'.$value->numero;
			$json['name'] = $value->serie.'-'.$value->numero;
			$this->data[] = $json;
			}
		}
	}
	
	/*
	BUSQUEDA DEL TRANSPORTE Y EL TRANSPORTISTA PARA LAS GUIAS DE SALIDA DE SANTA PATRICIA
	
	*/
	public function transportes()
	{$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj= new Protransportes();
		$campos = 'protransportes.' . join(',protransportes.', $obj->fields).' ,p.nombre,p.codigo';
		$results = $obj->find('columns: '.$campos,'join: INNER JOIN tesproductos as p ON p.id=protransportes.tesproductos_id','conditions: p.aclempresas_id='.Session::get('EMPRESAS_ID').' AND CONCAT_WS(" ",p.nombre,p.codigo,protransportes.marca,protransportes.modelo,protransportes.numeroplaca) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = (string)$value->nombre.' '.$value->marca.' '.$value->modelo.' '.$value->numeroplaca;
			$this->data[] = $json;
		}
	}
	
	public function transportistas()
	{$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj= new Protransportistas();
		$campos = 'protransportistas.' . join(',protransportistas.', $obj->fields).'';
		$results = $obj->find('columns: '.$campos,'join: INNER JOIN acldatos d ON d.id=protransportistas.acldatos_id','conditions: d.aclempresas_id='.Session::get('EMPRESAS_ID').' AND CONCAT_WS(" ",protransportistas.nombre,protransportistas.numerobrevete) like "%'.$q.'%"');
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $value->nombre.' ('.$value->numerobrevete.')';
			$this->data[] = $json;
		}
	}

public function buscardoc()
{
	$this->data=[];
		View::select('producto');
		$q=$_GET['q'];
		$obj= new Tessalidas();
		$results = $obj->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND CONCAT_WS("-",serie,numero,totalconigv,npedido) like "'.$q.'%"');
		foreach ($results as $value)
		{
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $value->serie.'-'.$value->numero.' T: '.$value->totalconigv.' Pedido: '.$value->npedido;
			$this->data[] = $json;
		}
}
	
}

?>