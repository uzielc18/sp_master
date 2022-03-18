<?php

class ProduccionController extends AdminController
{
	protected function before_filter()
	{
		if (Input::isAjax()) {
			View::template(null);
			//View::select(NULL, NULL);
		}
	}
	public function index()
	{
		Session::delete('MAQUINA_ID');
		Session::delete('MAQUINA_NOMBRE');
		Session::delete('MAQUINA_PREFIJO');
		Session::delete('MAQUINA_NUMERO');
		$maquinas = new Promaquinas();
		/*getListado => pide el id del tipo de prosuctos de la lina 9*/
		$this->maquinas = $maquinas->getListado(36);
		$this->PR = new Proproduccion();
	}
	public function cargarmaquina($id = 0)
	{
		if ($id != 0) {
			$maquinas = new Promaquinas();
			$maq = $maquinas->find_first((int)$id);
			Session::set('MAQUINA_ID', $maq->id);
			Session::set('MAQUINA_NOMBRE', $maq->nombre);
			Session::set('MAQUINA_PREFIJO', $maq->prefijo);
			Session::set('MAQUINA_NUMERO', $maq->numero);
			Flash::valid('Ingrese los datos para la maquina ' . $maq->numero . '!!');
			return Redirect::toAction('listado');
		} else {
			Flash::warning('No hay niguna maquina con ese numero!!');
			return Redirect::toAction('');
		}
	}
	public function listado($Y = '', $m = '')
	{
		$anio = $this->anio = Session::get('ANIO');
		if ($Y != '') $anio = $this->anio = $Y;
		$mes_activo = $this->mes_activo = date('m');
		if ($m != '') $mes_activo = $this->mes_activo = $m;
		Session::delete('PRODUCCION_ID');
		$this->NTP = new Pronotapedidos();
		$produccion = new Proproduccion();
		$maquinas = new Promaquinas();
		$this->maquina = $maquinas->find_first(Session::get('MAQUINA_ID'));
		$this->hojas = $produccion->findHojas((int)Session::get('MAQUINA_ID'), $anio, $mes_activo);



		$GUIA = new Tesingresos();
		$this->guias = $GUIA->find('conditions: tesingresos.tesdocumentos_id=15 AND pr="PL" AND produccion="0" OR (estadoingreso="Pendiente-2" AND produccion="0") AND aclempresas_id=' . Session::get('EMPRESAS_ID'));
		$this->DP = new Prodetalleproduccion();
	}
	public function cargarhoja($id = 0, $url = '')
	{
		Session::set('PRODUCCION_ID', $id);
		if ($id != 0) {
			$produccion = new Proproduccion();
			$pro = $produccion->find_first((int)$id);
			Session::set('PRODUCCION_ID', $pro->id);
		}
		return Redirect::toAction($url);
	}
	public function hojaderuta()
	{
		$produccion = new Proproduccion();
		$Puso = new Proplegadoresenuso();
		$this->estado = 0;
		$this->id = 0;
		$this->titulo = '';
		$this->guianumero = '';
		$this->peso = '';
		$this->metros = '';
		$this->serviciode = '';
		$this->numerohilos = '';
		$this->colores = '';
		$metrosu = 0;
		$this->metrostotal = $this->metros - $metrosu;
		$this->fecha = date('Y-m-d');
		$this->plegadoresuso = FALSE;
		$this->pro = FALSE;
		if (Session::get('PRODUCCION_ID') != 0) {
			$producciondetalle = new Prodetalleproduccion();
			$pro = $produccion->find_first((int)Session::get('PRODUCCION_ID'));
			$this->estado = $pro->estado;
			$this->id = $pro->id;
			$this->titulo = $pro->titulo;
			$this->codigo = $pro->codigo;
			$this->guianumero = $pro->guianumero;
			$this->peso = $pro->peso;
			$this->metros = $pro->metros;
			$this->serviciode = $pro->serviciode;
			$this->numerohilos = $pro->numerohilos;
			$this->colores = $pro->colores;
			$this->fecha = $pro->fecha;
			$this->pro = $pro;
			$this->plegadoresuso = $Puso->find('conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			$this->detalles = $producciondetalle->find('conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			$metrosu = $producciondetalle->sum('metroprogramados', 'conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			$this->metrostotal = $this->metros - $metrosu;
		}
	}
	/*Agregar mas detalles*/
	public function agregardetalle()
	{
		$produccion = new Proproduccion();
		$Puso = new Proplegadoresenuso();
		$this->estado = 0;
		$this->id = 0;
		$this->titulo = '';
		$this->guianumero = '';
		$this->peso = '';
		$this->metros = '';
		$this->serviciode = '';
		$this->numerohilos = '';
		$this->colores = '';
		$metrosu = 0;
		$this->metrostotal = $this->metros - $metrosu;
		$this->fecha = date('Y-m-d');
		$this->plegadoresuso = FALSE;
		if (Session::get('PRODUCCION_ID') != 0) {
			$producciondetalle = new Prodetalleproduccion();
			$pro = $produccion->find_first((int)Session::get('PRODUCCION_ID'));
			$this->pro = $pro;
			$this->estado = $pro->estado;
			$this->id = $pro->id;
			$this->titulo = $pro->titulo;
			$this->codigo = $pro->codigo;
			$this->guianumero = $pro->guianumero;
			$this->peso = $pro->peso;
			$this->metros = $pro->metros;
			$this->serviciode = $pro->serviciode;
			$this->numerohilos = $pro->numerohilos;
			$this->colores = $pro->colores;
			$this->fecha = $pro->fecha;
			$this->plegadoresuso = $Puso->find('conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			$this->detalles = $producciondetalle->find('conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'), 'order: orden, id ASC');
			$metrosu = $producciondetalle->sum('metroprogramados', 'conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			$this->metrostotal = $this->metros - $metrosu;
		}
	}

	public function grabar($id = 0)
	{
		View::select('plegadoruso');
		if ($id != 0) {
			if ($_POST['id'] == 0) {
				$produccion = new Proproduccion();
			} else {
				$PRO = new Proproduccion();
				$produccion = $PRO->find_first((int)$_POST['id']);
			}
			$produccion->aclusuarios_id = Auth::get('id');
			$produccion->promaquinas_id = Session::get('MAQUINA_ID');
			$produccion->titulo = $_POST['titulo'];
			$produccion->codigo = time() . '-' . Auth::get('id');
			$produccion->guianumero = $_POST['guianumero'];
			$produccion->peso = $_POST['peso'];
			$produccion->metros = $_POST['metros'];
			$produccion->serviciode = $_POST['serviciode'];
			$produccion->numerohilos = $_POST['numerohilos'];
			$produccion->colores = $_POST['colores'];
			$produccion->fecha = $_POST['fecha'];
			$produccion->estadoproduccion = 'PROGRAMACION';
			$produccion->userid = Auth::get('id');
			$produccion->save();
			$this->id = $produccion->id;
			Session::set('PRODUCCION_ID', $produccion->id);
		} else {
			$this->id = 0;
		}
	}
	/*
#
# Terminar la creacion primera de la hoja de ruta
#
*/
	public function terminar($id = 0, $estado = 0)
	{
		$PRO = new Proproduccion();
		$produccion = $PRO->find_first((int)$id);
		$produccion->estado = $estado;
		$produccion->save();
		$this->id = $produccion->id;
		Session::set('PRODUCCION_ID', $produccion->id);
		return Redirect::toAction('hojaderuta');
	}
	public function plegadores()
	{
		$this->data = '';
		$q = $_GET['q'];
		$obj = new Proplegadores();
		$mov = new Promovimientos();
		$campo = "proplegadores." . join(',proplegadores.', $obj->fields);
		$results = $obj->find('conditions: proplegadores.estado=1 AND proplegadores.estadoplegador_id=5 AND CONCAT_WS(" ",proplegadores.modelo,proplegadores.numero) like "%' . $q . '%"');
		foreach ($results as $value) {
			$mov->find_first('conditions: proplegadores_id=' . $value->id . ' AND estado=1');
			$json = array();
			$json['id'] = $value->id . '-' . $mov->colorurdido;
			$json['name'] = $value->modelo . '-' . $value->numero;
			$this->data[] = $json;
		}
	}

	public function plegadoruso($id = 0, $produccion)
	{
		$this->id = 0;
		if ($id != 0) {
			$proplegadoresenuso = new Proplegadoresenuso();
			$proplegadoresenuso->aclusuarios_id = (int)Auth::get('id');
			$proplegadoresenuso->proplegadores_id = (int)$id;
			$proplegadoresenuso->proproduccion_id = (int)$produccion;
			$proplegadoresenuso->estado = '1';
			$proplegadoresenuso->userid = Auth::get('id');
			$proplegadoresenuso->promovimientos_id = 0;
			if ($proplegadoresenuso->save()) {
				$plegador = new Proplegadores();
				$pl = $plegador->find_first((int)$proplegadoresenuso->proplegadores_id);
				$pl->estadoplegador_id = 2;
				$pl->save();
				$this->id = $proplegadoresenuso->id . '-' . $proplegadoresenuso->proplegadores_id;
			}
		}
	}
	/*
# Borrar todos los plegadores de la produccion en mencion
# @ id el id del plegador;
# @ id_produccion el id de la produccion;
*/
	public function borrarplegadoruso($id, $id_produccion)
	{
		View::select('plegadoruso');
		$this->id = 0;
		if ($id != 0) {
			$proplegadoresenuso = new Proplegadoresenuso();
			if ($proplegadoresenuso->exists('proplegadores_id=' . (int)$id . ' AND proproduccion_id=' . $id_produccion)) {
				$pluso = $proplegadoresenuso->find_first('conditions: proplegadores_id=' . (int)$id . ' AND proproduccion_id=' . $id_produccion);
				$plegador = new Proplegadores();
				$pl = $plegador->find_first((int)$pluso->proplegadores_id);
				$pl->estadoplegador_id = 5;
				$pl->save();
				if ($proplegadoresenuso->delete('proplegadores_id=' . (int)$id . ' AND proproduccion_id=' . $id_produccion)) {
					$this->id = $proplegadoresenuso->id . '-' . $proplegadoresenuso->proplegadores_id;
				} else {
					$this->id = 'NO SE BORRO NADA';
				}
			}
		}
	}
	public function producto($id = 0)
	{
		try{

		}catch(Exception $e){
			var_dump($e);
		}
		$this->data = [];
		View::select('plegadores');
		$q = $_GET['q'];
		$obj = new Tesproductos();
		$results = $obj->getProductos_tipo($q, $id);
		foreach ($results as $value) {
			/*if(!$detalleProduccion->exists('proproduccion_id='.$produccion_id.' AND tesproductos_id='.$value->id)){*/
			$id = $value->id;
			$n = $value->detalle;
			//$name=$value->nombre;
			//$n=$value->nombre." - (".$value->tipo_fibra.' '.$value->detalle.')';
			if ($id != 0) {
				$n = $value->nombre . ' ' . $value->nombre_corto . ' ' . $value->detalle . " - (" . $value->tipo_fibra . ') - ' . $value->codigo . ' - ' . $value->codigo_ant;
			}
			$name = $this->clear($this->htmlcode($n));
			$json = array();
			$json['id'] = $id;
			$json['name'] = $name;
			$this->data[] = $json;
			/*}*/
		}
	}
	public function ordenescompra()
	{

		View::select('plegadores');
		$this->data = '';
		$q = $_GET['q'];
		$obj = Load::model('tesordendecompras');
		$results = $obj->find('conditions: origenorden="externo" AND estado=1 AND CONCAT_WS(" ",codigo,numero_interno) like "%' . $q . '%"');
		foreach ($results as $value) {
			//$mov->find_first('conditions: proplegadores_id='.$value->id.' AND estado=1');
			$json = array();
			$json['id'] = $value->id;
			$json['name'] = $value->codigo . " (" . $value->numero_interno . ")";
			$this->data[] = $json;
		}
	}
	public function grabardetalle($id = 0)
	{
		$PRO = new Prodetalleproduccion();
		if ($id != 0) {
			$detalleproduccion = new Prodetalleproduccion();
			$detalleproduccion->tesproductos_id = $_POST['tesproductos_id'];
			$detalleproduccion->proproduccion_id = Session::get('PRODUCCION_ID');
			$detalleproduccion->tescolores_id = $_POST['tescolores_id'];
			$detalleproduccion->metroprogramados = $_POST['metroprogramados'];
			$detalleproduccion->observaciones = $_POST['observaciones'];
			$detalleproduccion->tesordendecompras_id = $_POST['tesordendecompras_id'];
			$detalleproduccion->corte = $_POST['corte'];
			$detalleproduccion->orden = $PRO->getOrden(Session::get('PRODUCCION_ID'));
			//corte
			$detalleproduccion->estado = '1';
			$detalleproduccion->userid = Auth::get('id');
			if ($detalleproduccion->save() && !empty($_POST['tesordendecompras_id'])) {
				$trama = $_POST['trama'];
				$colores = $_POST['tescolores_id_trama'];
				$this->grabartrama($detalleproduccion->id, $trama, $colores);
				$this->detalles = $PRO->find('conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			} else {
				Flash::error('DEBE INGRESAR UNA ORDER DE COMPRA');
				$this->detalles = $PRO->find('conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			}
		} else {
			$this->detalles = $PRO->find('conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'), 'order: orden,id ASC');
		}
		/*VALIDA EL UNVIO DE LA ORDEN DE COMPRA*/
	}
	public function borrardetalle($id = 0)
	{
		$PRO = new Prodetalleproduccion();
		$TRA = new Protrama();
		$ped = $TRA->find_first('prodetalleproduccion_id=' . (int)$id);
		/*buscar el id del detalle para tener el id del pedido y borrar */
		$detalle_pedido_id = $ped->prodetallepedidos_id;


		$TRA->delete('prodetalleproduccion_id=' . (int)$id);
		$PRO->delete((int)$id);
		return Redirect::toAction('grabardetalle/0');
	}
	/*
# @id el id del detalle de produccion.
#
*/
	protected function grabartrama($id = 0, $trama = '', $colores = '')
	{
		$trama_array = explode(',', $trama);
		$color_array = explode(',', $colores);
		foreach ($trama_array as $i => $value) {
			$protrama = new Protrama();
			$protrama->tesproductos_id = $value;
			$protrama->aclusuarios_id = Auth::get('id');
			$protrama->prodetalleproduccion_id = $id;
			$protrama->tescolores_id = $color_array[$i];
			$protrama->cantidadprogramada = 0;
			$protrama->cantidaduso = 0;
			$protrama->userid = Auth::get('id');
			$protrama->estado = '1';
			$protrama->save();
		}
		return TRUE;
	}
	/*
#
# Finalizar la hoja de ruta Y crear el pedido de almacen para trama 
#
*/
	public function finalizar($id = 0, $estado = 'PROGRAMACION')
	{
		if ($id != 0) {
			$obj = new Proproduccion();
			$produccion = $obj->find_first((int)$id);
			$produccion->update('estadoproduccion: ' . $estado);
			if ($produccion->save()) {
				/* Cuando la maquina esta en produccion ya no pueden volver a crea una hoja de ruta pero si editar o crear mas rollos*/
				$maquinas = new Promaquinas();
				$maquina = $maquinas->find_first($produccion->promaquinas_id);
				$maquina->estadomaquina = 'Produccion';
				$maquina->save();
				$detalleproduccion = new Prodetalleproduccion();
				/*para la ceracion de la hoja de ruta 
			#### en esta version ####*/
				/*$dp=$detalleproduccion->count('conditions: proproduccion_id='.$id);  
		if($dp>0)
		{
			$detallesP=$detalleproduccion->find('conditions: proproduccion_id='.$id);
			//valida la existencia de de trama nueva para pedido en trama por producion
			if($obj->existsNewtrama($id))
			{
				$NT=new Pronotapedidos();
				//valida la existencia de una nota de pedido en estado diferente al Entregado
				if($NT->exists('referencia="proproduccion_id-'.$id.'" AND estadonota="Editable"'))
				{
					$nota=$NT->find_first('conditions: referencia="proproduccion_id-'.$id.'"');
				}else
				{
					$nota=new Pronotapedidos();
					$nota->aclusuarios_id=Auth::get('id');
			 		$nota->codigo=$nota->codigo('salida');
			 		$nota->descripcion='Generacion de nota de pedido de la hoja de ruta';
			 		$nota->observacion=$produccion->titulo.' ';
			 		$nota->estadonota='Sin enviar';
			 		$nota->estado=1;
			 		$nota->activo=1;
			 		$nota->tipo='salida';
			 		$nota->origen='Produccion';
			 		$nota->referencia='proproduccion_id-'.$id;
			 		$nota->aclempresas_id=Session::get('EMPRESAS_ID');
			 		$nota->save();
				}
				Session::set('NOTA_ID',$nota->id);
				Session::set('tipo_nota','salida');
				foreach($detallesP as $detp):
				$trama= new Protrama();
				$tramas=$trama->find('conditions: prodetalleproduccion_id='.$detp->id);
				//crear el detalle de la nota de salida y actualizar la trama con la id del detalle de la nota de pedido
				foreach($tramas as $tr):
						if(empty($tr->prodetallepedidos_id))
						{
							$detalles=new Prodetallepedidos();
							$detalles->pronotapedidos_id=$nota->id;
							$detalles->tesproductos_id=$tr->tesproductos_id;
							$detalles->descripcion='';
							$detalles->cantidad=00;
							$detalles->tesunidadesmedidas_id=10;//Kilos
							$detalles->activo=1;
							if($detalles->save())
							{
								$tr->prodetallepedidos_id=(int)$detalles->id;
								$tr->save();
							}
						}
					endforeach;
				endforeach;
				
				Flash::valid('Igresar los detalles de la nota de pedido');
				return Redirect::redirect('/santapatricia/notadepedido/crear');
			}
		 }else
		 {
			Flash::error('Esta hoja de ruta no Tiene detalle por favor verifique los detalles!!');
			return Redirect::toAction('hojaderuta');
		 }*/

				Flash::valid('Hoja de ruta del plegador: lista para la Produccion');
				return Redirect::toAction('listado');
			} else {
				Flash::error('No se puede grabar (Disculpe las molestias)');
				return Redirect::toAction('hojaderuta');
			}
		} else {
			Flash::error('No se puede cambiar el esatdo de esta Hoja de ruta del plegador');
			return Redirect::toAction('listado');
		}
	}

	public function corte()
	{
		if (Session::get('PRODUCCION_ID') != 0) {
			$produccion = new Proproduccion();
			$this->NTP = new Pronotapedidos();
			$Puso = new Proplegadoresenuso();
			/*crea el ingreso de plegador*/
			$Puso->getIinicioFin('I', Session::get('PRODUCCION_ID'));
			$producciondetalle = new Prodetalleproduccion();
			$pro = $produccion->find_first((int)Session::get('PRODUCCION_ID'));
			$this->titulo = $pro->titulo;
			$this->codigo = $pro->codigo;
			$this->guianumero = $pro->guianumero;
			$this->peso = $pro->peso;
			$this->metros = $pro->metros;
			$this->serviciode = $pro->serviciode;
			$this->numerohilos = $pro->numerohilos;
			$this->colores = $pro->colores;
			$this->fecha = $pro->fecha;
			$this->plegadoresuso = $Puso->find('conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			$this->detalles = $producciondetalle->find('conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'), 'order: orden, id ASC');
			$metrosu = $producciondetalle->sum('metroprogramados', 'conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			$this->metrostotal = $this->metros - $metrosu;
		}
	}

	public function generarollos()
	{
		$fecha = Input::post("fechadecorte");
		$numero = Input::post("numero");
		$metrosrevisados = Input::post("mrevisados");
		$id = Input::post('id');
		$detalleP = new Prodetalleproduccion();
		$detalle = $detalleP->find((int)$id);

		if (!empty($fecha) && !empty($numero)) {

			for ($i = 0; $i < $numero; $i++) {
				$prorollos = new Prorollos();
				$prorollos->prodetalleproduccion_id = $detalle->id;
				$prorollos->tesproductos_id = $detalle->tesproductos_id;
				$prorollos->codigo = $prorollos->codigo($id);
				$prorollos->numero = $prorollos->numero();
				$prorollos->maquina_numero = $detalle->getProproduccion()->getPromaquinas()->numero;
				$prorollos->fechacorte = $fecha;
				$prorollos->color = $detalle->getTescolores()->nombre;
				$prorollos->tescolores_id = $detalle->tescolores_id;
				$prorollos->tesordendecompras_id = $detalle->tesordendecompras_id;
				$prorollos->estadorollo = 'Sin Revisar';
				$prorollos->estado = 1;
				$prorollos->userid = Auth::get('id');
				$prorollos->lote = $detalle->getProproduccion()->codigo;
				$prorollos->enalmacen = '0';
				$prorollos->save();
			}
			$detalle->update('fechadecorte: ' . $fecha);
			$detalle->update('metrosrevisados: ' . $metrosrevisados);
			$detalle->save();
			Flash::info('Ingrese los datos del rollo');
			return Redirect::toAction('rollos/' . $id);
		} else {
			Flash::error('No se puede crear los rollos!!!! ' . "<br /> Por favor intente nuevamente, verificando la informacion enviada");
			return Redirect::toAction('corte');
		}
	}

	public function rollos($id = 0)
	{
		if ($id != 0) {
			$prorollos = new Prorollos();
			$this->prorollos = $prorollos->find('conditions: prodetalleproduccion_id=' . $id);
			$this->id = $id;
		} else {
			return Redirect::toAction('corte');
		}
	}

	public function grabarrollo($id)
	{
		$this->id = $id;
		$this->ok = 'No grabo';
		$a = 1;
		$b = 2;
		$prorollos = new Prorollos();
		if (Input::hasPost('prorollos' . $a)) {
			$this->ok = '';
			$post = Input::post('prorollos' . $a);
			$id_rollo = $post = $post['id'];
			if ($prorollos->save(Input::post('prorollos' . $a))) {
				$this->ok .= 'Desa editar la informacion enviada?';
				$this->id = $prorollos->prodetalleproduccion_id;
				$this->n = 0;
			} else {
				$this->rollo = $prorollos->find_first((int)$id_rollo);
				$this->n = $a;
			}
		}
		if (Input::hasPost('prorollos' . $b)) {

			$this->ok = '';
			$post = Input::post('prorollos' . $b);
			$id_rollo = $post = $post['id'];
			if ($prorollos->save(Input::post('prorollos' . $b))) {
				$this->ok .= 'Desa editar la informacion enviada?';
				$this->id = $prorollos->prodetalleproduccion_id;
				$this->n = 0;
			} else {
				$this->rollo = $prorollos->find_first((int)$id_rollo);
				$this->n = $b;
			}
		}
	}
	public function buscarguia()
	{
		$this->data = '';
		View::select('plegadores');
		$q = $_GET['q'];
		$obj = Load::model('tesingresos');
		$produccion = new Proproduccion();
		$results = $obj->find('conditions: pr="PL" AND tesdocumentos_id=15 AND aclempresas_id=' . Session::get('EMPRESAS_ID') . ' AND CONCAT_WS("-",serie,numero) like "' . $q . '%"');
		foreach ($results as $value) {
			/*
	  #valida la existencia de serie-numero en otra PRODUCCION ANTERIOR anterior
	  */
			if (!$produccion->exists('guianumero="' . $value->serie . '-' . $value->numero . '"')) {
				$pesoneto = $this->getPesoNeto($value);
				$empresa = $value->getTesdatos()->razonsocial;
				$gl = explode(',', $value->glosa);
				$det = array();
				$this->gl = $gl;
				foreach ($gl as $id => $v) {
					if (!empty($v)) {
						$cl = explode(':', $v);
						$det[trim($cl[0])] = $cl[1];
					}
				}
				if (array_key_exists('HILOS', $det)) $titulo_hilo = $det['HILOS'];
				if (array_key_exists('COLOR', $det)) $color = $det['COLOR'];
				if (array_key_exists('ANCHO', $det)) $ancho = $det['ANCHO'];
				if (array_key_exists('#HILOS', $det)) $nhilos = $det['#HILOS'];
				if (array_key_exists('METROS', $det)) $metros = $det['METROS'];
				if (array_key_exists('PLEGADORES', $det)) $plegadores = $det['PLEGADORES'];
				if (array_key_exists('T.KILOS', $det)) $totalkilos = $det['T.KILOS'];
				$json = array();
				$json['id'] = $value->serie . '-' . $value->numero . '-' . $titulo_hilo . '-' . $empresa . '-' . $nhilos . '-' . $metros . '-' . $pesoneto;
				$json['name'] = $value->serie . '-' . $value->numero;
				$this->data[] = $json;
			}
		}
	}

	/*
generar la hoja de ruta desde una guia
#
#@ id= el id de ingreso guia de remision tesdocumentos_id=15 AND ;
#

*/

	public function generarhojaderuta($id = 0)
	{
		$GUIA = new Tesingresos();
		$DETGUIA = new Tesdetalleingresos();
		$PRO = new Proproduccion();
		$DETPRO = new Prodetalleproduccion();
		/*Valida el ingreso de un POST o el ingreso por GET*/
		if (Input::hasPost('guias')) {
			$values = Input::post('guias');
			$val_id = $values['ids'];
			$VAL = explode(',', $values['ids']);
		} else {
			$VAL[0] = $id;
			$val_id = $id;
		}
		$guiaingresos = $GUIA->find_first((int)$VAL[0]);
		$detalleguia = $DETGUIA->find('conditions: tesingresos_id=' . (int)$VAL[0]);

		if (array_key_exists(1, $VAL)) {
			$guiaingresos_m = $GUIA->find_first((int)$VAL[1]);
			$detalleguia_m = $DETGUIA->find('conditions: tesingresos_id=' . (int)$VAL[1]);
			/*Encontrar los detalles de la hoja de ruta*/
			$gl = explode(',', $guiaingresos_m->glosa);
			$det = array();
			$this->gl = $gl;
			foreach ($gl as $id => $value) {
				if (!empty($value)) {
					$cl = explode(':', $value);
					$det[trim($cl[0])] = $cl[1];
				}
			}
			if (array_key_exists('ANCHO', $det)) $ancho_m = $det['ANCHO'];
			if (array_key_exists('#HILOS', $det)) $nhilos_m = $det['#HILOS'];
			if (array_key_exists('METROS', $det)) $metros_m = $det['METROS'];
			if (array_key_exists('PLEGADORES', $det)) $plegadores_m = $det['PLEGADORES'];
			if (array_key_exists('T.KILOS', $det)) $totalkilos_m = $det['T.KILOS'];
		}
		/*Encontrar los detalles de la hoja de ruta*/
		$gl = explode(',', $guiaingresos->glosa);
		$det = array();
		$this->gl = $gl;
		foreach ($gl as $id => $value) {
			if (!empty($value)) {
				$cl = explode(':', $value);
				$det[trim($cl[0])] = $cl[1];
			}
		}
		if (array_key_exists('HILOS', $det)) $titulo_hilo = $det['HILOS'];
		if (array_key_exists('COLOR', $det)) $color = $det['COLOR'];
		if (array_key_exists('ANCHO', $det)) $ancho = $det['ANCHO'];
		if (array_key_exists('#HILOS', $det)) $nhilos = $det['#HILOS'];
		if (array_key_exists('METROS', $det)) $metros = $det['METROS'];
		if (array_key_exists('PLEGADORES', $det)) $plegadores = $det['PLEGADORES'];
		if (array_key_exists('T.KILOS', $det)) $totalkilos = $det['T.KILOS'];
		/*buscar si ya hay hojas de produccion para esta guia*/
		if ($guiaingresos->estadoingreso == 'Pendiente-2') {
			$factor = $totalkilos / $metros;
			$pr = new Proproduccion();
			$p = $pr->totalUsados($guiaingresos->id);
			$totalkilos = ((float)($metros - $p) * (float)$factor);
			$metros = $metros - $p;
			//Session::set('mostrar','Peso='.$totalkilos.' Metros='.$metros);
		}
		/*Grabar hoja de ruta*/
		$produccion = new Proproduccion();
		$produccion->aclusuarios_id = Auth::get('id');
		$produccion->promaquinas_id = Session::get('MAQUINA_ID');
		$produccion->titulo = $titulo_hilo;
		$produccion->codigo = time() . '-' . Auth::get('id');
		$produccion->guianumero = $guiaingresos->serie . '-' . $guiaingresos->numero;
		if (array_key_exists(1, $VAL)) {
			$produccion->guianumero_m = $guiaingresos_m->serie . '-' . $guiaingresos_m->numero;
			$produccion->peso_m = $totalkilos_m;
			$produccion->metros_m = $metros_m;
			$produccion->numerohilos_m = $nhilos_m;
		}
		$produccion->guia_id = $val_id;
		$produccion->peso = $totalkilos;
		$produccion->metros = $metros;
		$produccion->serviciode = $guiaingresos->getTesdatos()->razonsocial;
		$produccion->numerohilos = $nhilos;
		$produccion->colores = $color;
		$produccion->fecha = date("Y-m-d");
		$produccion->estadoproduccion = 'PROGRAMACION';
		$produccion->userid = Auth::get('id');
		if ($produccion->save()) {
			foreach ($detalleguia as $det) {
				$proplegadoresenuso = new Proplegadoresenuso();
				$proplegadoresenuso->aclusuarios_id = (int)Auth::get('id');
				$proplegadoresenuso->proplegadores_id = (int)$det->getTesproductos()->getProplegadores()->id;
				$proplegadoresenuso->proproduccion_id = (int)$produccion->id;
				$proplegadoresenuso->estado = '1';
				$proplegadoresenuso->userid = Auth::get('id');
				$proplegadoresenuso->promovimientos_id = 0;
				if ($proplegadoresenuso->save()) {
					$plegador = new Proplegadores();
					$pl = $plegador->find_first((int)$proplegadoresenuso->proplegadores_id);
					$pl->estadoplegador_id = 2;
					$pl->save();
				}
			}
			$guiaingresos->produccion = '1';
			$guiaingresos->save();
			if (array_key_exists(1, $VAL)) {
				foreach ($detalleguia_m as $det) {
					$proplegadoresenuso = new Proplegadoresenuso();
					$proplegadoresenuso->aclusuarios_id = (int)Auth::get('id');
					$proplegadoresenuso->proplegadores_id = (int)$det->getTesproductos()->getProplegadores()->id;
					$proplegadoresenuso->proproduccion_id = (int)$produccion->id;
					$proplegadoresenuso->estado = '1';
					$proplegadoresenuso->userid = Auth::get('id');
					$proplegadoresenuso->promovimientos_id = 0;
					if ($proplegadoresenuso->save()) {
						$plegador = new Proplegadores();
						$pl = $plegador->find_first((int)$proplegadoresenuso->proplegadores_id);
						$pl->estadoplegador_id = 2;
						$pl->save();
					}
				}
				$guiaingresos_m->produccion = '1';
				$guiaingresos_m->save();
			}
		}
		return Redirect::toAction('cargarhoja/' . $produccion->id . '/hojaderuta');
	}
	public function verhoja()
	{
		if (Session::get('PRODUCCION_ID') != 0) {
			$produccion = new Proproduccion();
			$Puso = new Proplegadoresenuso();
			$producciondetalle = new Prodetalleproduccion();
			$pro = $produccion->find_first((int)Session::get('PRODUCCION_ID'));
			$this->titulo = $pro->titulo;
			$this->codigo = $pro->codigo;
			$this->guianumero = $pro->guianumero;
			$this->peso = $pro->peso;
			$this->metros = $pro->metros;
			$this->serviciode = $pro->serviciode;
			$this->numerohilos = $pro->numerohilos;
			$this->colores = $pro->colores;
			$this->fecha = $pro->fecha;
			$this->plegadoresuso = $Puso->find('conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			$this->detalles = $producciondetalle->find('conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			$metrosu = $producciondetalle->sum('metroprogramados', 'conditions: proproduccion_id=' . Session::get('PRODUCCION_ID'));
			$this->metrostotal = $this->metros - $metrosu;
		}
	}



	/*
#
# Finalizar la hoja de ruta y su produccion.
# Cambiar de estado a los plegadores en uso.
# id el id de la produccion IMPORTANTE + no se trabaja con la session para no poder generar ningun error+
# buscar los plegadores en uso y cambiarlos de estado a 1 en piso
#
*/
	public function cambiarestado($id = 0, $estado = '', $action = '')
	{
		$produccion = new Proproduccion();
		$prod = $produccion->find_first((int)$id);
		$values = Input::post('guias');
		$guia_id = $prod->guia_id;
		$VAL = explode(',', $guia_id);
		$prod->estadoproduccion = $estado;
		$prod->save();
		if ($estado == 'TERMINADO') {
			$Puso = new Proplegadoresenuso();
			$Puso->getIinicioFin('F', $id);
			$Puso->getPlegadorenpiso($id);
			$M = Load::model('promaquinas');
			$maquina = $M->find_first((int)$prod->promaquinas_id);
			$maquina->estadomaquina = '';
			$maquina->save();
			/*Buscar eh ingresar al merma*/
			$merma = new Promerma();
			if (!$merma->exists('proproduccion_id=' . $id)) {
				$merma->proproduccion_id = $id;
				//prodetalleproduccion_id
				$merma->promaquinas_id = $prod->promaquinas_id;
				$merma->metros = $Puso->getMerma($id);
				//ancho
				//prorollos_id
				//numero
				$merma->estado = '1';
				$merma->userid = Auth::get('id');
				$merma->save();
			}
			$ingresos = new Tesingresos();
			$ingreso = $ingresos->find_first($VAL[0]);
			$ingreso->produccion = "1";
			$ingreso->save();
			if (array_key_exists(1, $VAL)) {
				$ingresos = new Tesingresos();
				$ingreso = $ingresos->find_first($VAL[1]);
				$ingreso->produccion = "1";
				$ingreso->save();
			}
		}
		return Redirect::toAction($action);
	}

	public function terminarproduccion($id = 0)
	{
		$produccion_id = Session::get('PRODUCCION_ID');
		if ($id == $produccion_id) {
			$produccion = new Proproduccion();
			$pp = $produccion->find_first((int)$produccion_id);
			$detallesP = new Prodetalleproduccion();
			$dds = $detallesP->find('conditions: ISNULL( fechadecorte ) AND proproduccion_id=' . $pp->id);
			$id_del_pedido = 0;
			$cod = '';
			foreach ($dds as $d) :
				/*Busca la trama para este detalle para la eliminacion corespondiente*/
				$protramas = new Protrama();
				$tramas = $protramas->find('conditions: prodetalleproduccion_id=' . $d->id);
				$cod .= ' tiene detalle sin terminar de cortar';
				foreach ($tramas as $tr) :
					/*Busca el detalle de pedido generado por la trama*/
					$detalles = new Prodetallepedidos();
					$pedido_detalles = $detalles->find_first((int)$tr->prodetallepedidos_id);
					$id_del_pedido = $pedido_detalles->pronotapedidos_id;
					$C_T = Load::model('procajastrama');
					$C_T->delete('prodetallepedidos_id=' . $tr->prodetallepedidos_id);
					$detalles->delete((int)$tr->prodetallepedidos_id);
					/*elimina la trama */
					$protramas->delete((int)$tr->id);
					$cod .= ' detalles y trama fueron eliminados';
				endforeach;
				/*Eliminar el Pedido Si no hay mas detalles*/
				if ($id_del_pedido != 0) {
					$NT = new Pronotapedidos();
					if ($NT->exists("id=" . (int)$id_del_pedido)) {
						$p = $NT->find_first((int)$id_del_pedido);
						$detalles = new Prodetallepedidos();
						if (!$detalles->exists('pronotapedidos_id=' . $p->id)) {
							$NT->delete((int)$p->id);
							$cod .= ' el pedido fue eliminado ' . $p->id;
						} else {
							$cod .= ' el pedido aun tiene otros detalles ' . $p->id;
						}
					}
				}
				/*elimina */
				$detallesP->delete((int)$d->id);
			endforeach;
			/*Volver la guia Pendiente-2*/
			$VAL = explode(',', $pp->guia_id);
			if (array_key_exists(1, $VAL)) {
				$ingresos = new Tesingresos();
				$ingreso = $ingresos->find_first($VAL[1]);
				$ingreso->produccion = "0";
				$ingreso->save();
				Flash::valid('La produccion fue ' . $pp->estadoproduccion . ' la guia ' . $ingreso->serie . '-' . $ingreso->numero . ' esta ahora ' . $ingreso->estadoingreso . ' !!!' . $cod);
			}
			$ingresos = new Tesingresos();
			$ingreso = $ingresos->find_first((int)$VAL[0]);
			$ingreso->produccion = "0";
			$ingreso->save();
			$pp->estadoproduccion = "TERMINADO";
			$pp->save();
			$M = Load::model('promaquinas');
			$maquina = $M->find_first((int)$pp->promaquinas_id);
			$maquina->estadomaquina = '';
			$maquina->save();
			Flash::valid('La produccion fue ' . $pp->estadoproduccion . ' la guia ' . $ingreso->serie . '-' . $ingreso->numero . ' esta ahora ' . $ingreso->estadoingreso . ' !!!' . $cod);
			return Redirect::toAction('listado');
		} else {
			Flash::error('Por favor cerrar la otras ventanas que esten abierta del sistema. !!!');
			return Redirect::toAction('listado');
		}
	}
	/* 
Eliminar la hoja de ruta cuando no tenga rollos existentes
//id de produccion 
*/
	public function eliminar($guia_id = 0)
	{
		$id = (int)Session::get('PRODUCCION_ID');
		$produccion = new Proproduccion();
		$detalleP = new Prodetalleproduccion();
		$proplegadoresenuso = new Proplegadoresenuso();
		$cambio = '';
		if ($id != 0) {
			$proplegadoresenuso->delete('proproduccion_id=' . $id);
			if ($guia_id != 0) {
				$VAL = explode(',', $guia_id);
				if (array_key_exists(1, $VAL)) {
					$ingresos = new Tesingresos();
					$ingreso = $ingresos->find_first($VAL[1]);
					$ingreso->produccion = "0";
					$ingreso->save();
				}
				$ingresos = new Tesingresos();
				$ingreso = $ingresos->find_first($VAL[0]);
				$ingreso->produccion = "0";
				if ($ingreso->save()) {/*/$cambio='Si cambio'.$ingreso->id;*/
				}
			}
		}
		if ($detalleP->exists('proproduccion_id=' . $id)) {
			$detalleP->delete('proproduccion_id=' . $id);
		}
		if ($produccion->exists('id=' . (int)$id)) {
			if ($produccion->delete((int)$id)) {
				Flash::valid('Hoja de ruta sin grabar ' . $cambio);
				return Redirect::toAction('listado');
			} else {
				Flash::info('No se puede borrar la hoja de ruta');
				return Redirect::toAction('hojaderuta');
			}
		}
		return Redirect::toAction('listado');
	}



	public function grabarorden($ids = '')
	{
		$val = explode(',', $ids);
		$this->val = $val;
		$n = 0;
		foreach ($val as $item => $value) {
			$n++;
			$detalles = new Prodetalleproduccion();
			$detalles->find_first((int)$value);
			$detalles->orden = $n;
			$detalles->save();
		}
	}

	/*Funciones protegidas*/

	/*
	# @id= id del ingreso
	*/
	protected function getPesoNeto($value)
	{
		$pesototal = 0;
		//$pesotara=0;
		foreach ($value->getTesdetalleingresos() as $de) {
			$pesototal = $pesototal + $de->pesoneto;
			//$pesotara=$pesotara+$de->getTesproductos()->getProplegadores()->peso;
		}
		return $pesototal; //-$pesotara;
	}

	protected function htmlcode($text)
	{
		$stvarno = array("<", ">");
		$zamjenjeno = array("&lt;", "&gt;");
		$final = str_replace($stvarno, $zamjenjeno, $text);
		return $final;
	}
	protected function clear($text)
	{
		$final = stripslashes(stripslashes($text));
		return $final;
	}
	protected function definido($variable)
	{
		if ($variable == 'undefined') {
			$val = '0';
		} else {
			$val = $variable;
		}
		return $val;
	}
}
