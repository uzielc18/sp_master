<?Php

class VentasController extends AdminController
{
	protected function before_filter()
	{
		if (Input::isAjax()) {
			View::template(null);
			//View::select(NULL, NULL);
		}
	}
	public function index($Y = '', $m = '')
	{

		setlocale(LC_ALL, "es_ES");
		$anio = $this->anio = Session::get('ANIO');
		if ($Y != '') $anio = $this->anio = $Y;
		$mes_activo = $this->mes_activo = date('m');
		if ($m != '') $mes_activo = $this->mes_activo = $m;
		$salidas = new Tessalidas();
		$this->ventas = $salidas->find('conditions: aclempresas_id=' . Session::get("EMPRESAS_ID") . ' AND (serie="F001" OR serie="001" OR serie = "F002") AND estado=1 AND (npedido like "TE%" OR npedido like "PA%") AND tesdocumentos_id=7 AND DATE_FORMAT(femision, "%Y-%m")="' . $anio . '-' . $mes_activo . '"', 'order: numero ASC');
		Session::delete("SALIDA_ID");
		Session::delete('DOC_ID');
		Session::delete('DOC_NOMBRE');
		Session::delete('DOC_ABR');
		Session::delete('DOC_CODIGO');
	}
	public function cargarsalida($id = 0, $url = 'agregardetalles')
	{
		if ($id != 0) {
			Session::set("SALIDA_ID", $id);
		} else {
			Session::delete("SALIDA_ID");
		}
		return Redirect::toAction($url);
	}
	public function guias($id)
	{
		if ($id == 7) return Redirect::toAction('facturas');
		$this->titulo = 'Guias sin factura';
		$this->url = 'crearsalidas';
		$documentos = new Tesdocumentos();
		$doc = $documentos->find_first((int)$id);
		Session::set('DOC_ID', $doc->id);
		Session::set('DOC_NOMBRE', $doc->nombre);
		Session::set('DOC_ABR', $doc->abr);
		Session::set('DOC_CODIGO', $doc->codigo);
		/*
		serie 001
		salida de de rollos para venta
		*/
		$salidas = new Tessalidas();
		$this->salidas = $salidas->find('conditions: aclempresas_id=' . Session::get("EMPRESAS_ID") . ' AND (serie="F001" OR serie="001" OR serie = "F002") AND (estadosalida="Editable" OR estadosalida="PENDIENTE")AND estadosalida!="Anulado" AND npedido like "TE%" AND estado=1 AND tesdocumentos_id=' . Session::get('DOC_ID'), 'order: numero DESC limit 15');
		Session::delete("SALIDA_ID");
	}
	public function guias_mes($Y = '', $m = '')
	{
		setlocale(LC_ALL, "es_ES");
		View::select('facturas');
		$anio = $this->anio = Session::get('ANIO');
		if ($Y != '') $anio = $this->anio = $Y;
		$mes_activo = $this->mes_activo = date('m');
		if ($m != '') $mes_activo = $this->mes_activo = $m;
		$this->titulo = 'Guias del mes';
		$this->url = 'crearsalidas';
		$documentos = new Tesdocumentos();
		$id = 15;
		$doc = $documentos->find_first((int)$id);
		Session::set('DOC_ID', $doc->id);
		Session::set('DOC_NOMBRE', $doc->nombre);
		Session::set('DOC_ABR', $doc->abr);
		Session::set('DOC_CODIGO', $doc->codigo);
		/*
		serie 001
		salida de de rollos para venta
		*/
		$salidas = new Tessalidas();
		//$this->salidas= $salidas->find('conditions: DATE_FORMAT(femision,"%Y-%m")="'.date('Y-m').'" AND aclempresas_id='.Session::get("EMPRESAS_ID").' AND serie="001" AND (npedido like "TE%" OR npedido like "PA%") AND tesdocumentos_id='.Session::get('DOC_ID'),'order: numero DESC'); /*AND (npedido like "TE%" OR npedido like "PA%")*/
		$this->salidas = $salidas->find('conditions: aclempresas_id=' . Session::get("EMPRESAS_ID") . ' AND (serie="F001" OR serie="001" OR serie = "F002") AND tesdocumentos_id=' . Session::get('DOC_ID') . ' AND DATE_FORMAT(femision, "%Y-%m")="' . $anio . '-' . $mes_activo . '"', 'order: numero DESC');
		Session::delete("SALIDA_ID");
		$this->guias = false;
	}

	public function facturas($Y = '', $m = '')
	{
		$anio = $this->anio = Session::get('ANIO');
		if ($Y != '') $anio = $this->anio = $Y;
		$mes_activo = $this->mes_activo = date('m');
		if ($m != '') $mes_activo = $this->mes_activo = $m;
		$this->titulo = 'Facturas del mes ';
		$this->url = 'crearsalidas';
		$documentos = new Tesdocumentos();
		$id = 7;
		$doc = $documentos->find_first((int)$id);
		Session::set('DOC_ID', $doc->id);
		Session::set('DOC_NOMBRE', $doc->nombre);
		Session::set('DOC_ABR', $doc->abr);
		Session::set('DOC_CODIGO', $doc->codigo);
		/*
		serie 001
		salida de de rollos para venta
		*/
		$salidas = new Tessalidas();
		//$this->salidas= $salidas->find('conditions: DATE_FORMAT(femision,"%Y-%m")="'.date('Y-m').'" AND aclempresas_id='.Session::get("EMPRESAS_ID").' AND serie="001" AND (npedido like "TE%" OR npedido like "PA%") AND tesdocumentos_id='.Session::get('DOC_ID'),'order: numero DESC');
		$this->salidas = $salidas->find('conditions: aclempresas_id=' . Session::get("EMPRESAS_ID") . ' AND (serie="F001" OR serie="001" OR serie = "F002") AND (npedido like "TE%" OR npedido like "PA%") AND tesdocumentos_id=' . Session::get('DOC_ID') . ' AND DATE_FORMAT(femision, "%Y-%m")="' . $anio . '-' . $mes_activo . '"', 'order: numero DESC');
		Session::delete("SALIDA_ID");
		$this->guias = $salidas->find('conditions: aclempresas_id=' . Session::get("EMPRESAS_ID") . ' AND (serie="F001" OR serie="001" OR serie = "F002") AND estadosalida="Pendiente" AND npedido like "TE%" AND estado=1 AND tesdocumentos_id=15', 'order: numero ASC');
	}
	public function crearsalidas()
	{
		$SALD = new Tessalidas();
		$CAMB = new Testipocambios();
		$this->salida['serie'] = '001';
		$this->salida['numero'] = $SALD->generarNumero(Session::get('DOC_ID'), '001');
		$this->salida['npedido'] = $SALD->getNumeropedido('TE', '001');
		$this->salida['numerofactura'] = $SALD->generarNumeroFactura('F002');
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id = Session::get('DOC_ID');
			$salidas->fvencimiento = date("Y-m-d", strtotime($salidas->femision . "+ 30 days"));
			$salidas->estadosalida = 'Editable';
			$salidas->estado = 1;
			/*Cuenta cuentagastos cuentaporpagar*/
			$salidas->cuentaporcobrar = '12121';
			$salidas->userid = Auth::get('id');
			$salidas->activo = '1';
			$salidas->testipocambios_id = $CAMB->getCambioFecha($salidas->femision);
			$salidas->userid = Auth::get('id');
			$salidas->aclusuarios_id = Auth::get('id');
			$salidas->aclempresas_id = Session::get("EMPRESAS_ID");
			if ($salidas->save()) {
				$detalleT = new Prodetalletransportes(Input::post('prodetalletransportes'));
				$detalleT->tessalidas_id = $salidas->id;
				$detalleT->numero = $salidas->numero;
				$detalleT->serie = $salidas->serie;
				$detalleT->fechatraslado = $salidas->finiciotraslado;
				$detalleT->estado = '1';
				$detalleT->userid = Auth::get('id');
				$detalleT->save();

				Session::set("SALIDA_ID", $salidas->id);

				//Flash::valid('fue agregada Exitosamente...!!!');
				Aclauditorias::add("Creo una Nuevo Salida " . Session::get('DOC_NOMBRE') . "-{$salidas->numero} al sistema");

				return Redirect::toAction('agregardetalles/');
				unset($_POST);
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				$this->salida = Input::post('salida');
				return Redirect::toAction('crearsalidas/');
			}
		}
	}
	public function agregardetalles()
	{
		$salidas = new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$rollos = new Prorollos();
		$this->rollos = $rollos->find('conditions: (estadorollo="VENTA") AND enalmacen="1"', 'order: codigo,numeroventa ASC');
		$this->salida = $salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles = $detalles->find('conditions: tessalidas_id=' . Session::get('SALIDA_ID'));
		$detalleT = new Prodetalletransportes();
		$this->prodetalletransportes = $detalleT->find_first('conditions: tessalidas_id=' . Session::get('SALIDA_ID'));
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id = Session::get('DOC_ID');
			$salidas->femision = date('Y-m-d');
			$salidas->fvencimiento = date("Y-m-d", strtotime($salidas->femision . "+ 30 days"));
			$salidas->estadosalida = 'Editable';
			$salidas->estado = 1;
			$salidas->userid = Auth::get('id');
			$salidas->activo = '1';
			//$salidas->testipocambios_id=Session::get('CAMBIO_ID');
			$salidas->userid = Auth::get('id');
			$salidas->aclusuarios_id = Auth::get('id');
			$salidas->aclempresas_id = Session::get("EMPRESAS_ID");
			if ($salidas->save()) {

				if (Session::get('DOC_ID') == 15) {
					$detalleT = new Prodetalletransportes(Input::post('prodetalletransportes'));
					$detalleT->tessalidas_id = $salidas->id;
					$detalleT->numero = $salidas->numero;
					$detalleT->serie = $salidas->serie;
					$detalleT->fechatraslado = $salidas->finiciotraslado;
					$detalleT->save();
				}

				Session::set("SALIDA_ID", $salidas->id);

				//Flash::valid('fue agregada Exitosamente...!!!');
				Aclauditorias::add("Edito la Salida " . Session::get('DOC_NOMBRE') . "-{$salidas->numero} al sistema");

				return Redirect::toAction('agregardetalles/');
				unset($_POST);
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				$this->salida = Input::post('salida');
				return Redirect::toAction('agregardetalles/');
			}
		}
	}
	public function versalida()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			if ($salidas->save()) {
				/*validar si tiene letras y si la suma de sus letras es igual a 0*/
				$tesletrassalidas = new Tesletrassalidas();
				if ($tesletrassalidas->exists('factura_id=' . $salidas->id)) {
					$total_letras = $tesletrassalidas->sum('monto', 'conditions: factrua_id=' . $salidas->id);
					if ($total_letras < $salidas->totalconigv) {
						$SALIDAS = new Tessalidas();
						$salida = $SALIDAS->find_first($salida->id);
						$salida->estadosalida = 'ADELANTADO';
						$salida->save();
					}
				}
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida/');
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$salidas = new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$this->salida = $salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles = $detalles->find('conditions: tessalidas_id=' . (int)Session::get("SALIDA_ID"));
		$detalleT = new Prodetalletransportes();
		$this->prodetalletransportes = $detalleT->find_first('conditions: tessalidas_id=' . Session::get('SALIDA_ID'));
		$letras = new Tesletrassalidas();
		$this->letras = $letras->count('estadoletras="Editable" AND factura_id=' . (int)Session::get("SALIDA_ID"));
		$this->l_f = $letras->count('estadoletras="Sin Numero" AND factura_id=' . (int)Session::get("SALIDA_ID"));
		$letrassalida = new Tesletrassalidas();
		$this->lets = $letrassalida->find('conditions: factura_id=' . Session::get('SALIDA_ID'));
		/* Busa las aplicaciones a esta factura */
		$aplicaciones = new Tesaplicacionfacturasadelantos();
		$this->aplicaciones = FALSE;
		if ($aplicaciones->exists('tessalidas_id=' . $this->salida->id)) {

			$this->aplicaciones = $aplicaciones->find('conditions: tessalidas_id=' . $this->salida->id);
		}
	}

	public function versalida_i()
	{

		$detalles = new Tesdetallesalidas();
		if (Session::get('DOC_ID') == 7) {
			View::select('versalidafactventa_i');
			$DETALLES = $detalles->find('columns: tesdetallesalidas.' . join(',tesdetallesalidas.', $detalles->fields) . ',sum(tesdetallesalidas.cantidad) as totalcantidad,sum(tesdetallesalidas.importe) as totalimporte,sum(tesdetallesalidas.descuento) as totaldescuento', 'conditions: tessalidas_id=' . (int)Session::get("SALIDA_ID"), 'group: tesproductos_id');
		}
		if (Session::get('DOC_ID') == 15) {
			View::select('versalidaguiaventa');
			$DETALLES = $detalles->find('conditions: tessalidas_id=' . (int)Session::get("SALIDA_ID"));
		}
		$empresas = new Aclempresas();
		$this->empresa = $empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas = new Tessalidas();
		$this->salida = $salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles = $DETALLES;
		$detalleT = new Prodetalletransportes();
		$this->prodetalletransportes = $detalleT->find_first('conditions: tessalidas_id=' . Session::get('SALIDA_ID'));
		$letrassalida = new Tesletrassalidas();
		$this->lets = $letrassalida->find('conditions: factura_id=' . Session::get('SALIDA_ID'));
		/* Busa las aplicaciones a esta factura */
		$aplicaciones = new Tesaplicacionfacturasadelantos();
		$this->aplicaciones = FALSE;
		if ($aplicaciones->exists('tessalidas_id=' . $this->salida->id)) {

			$this->aplicaciones = $aplicaciones->find('conditions: tessalidas_id=' . $this->salida->id);
		}
	}

	public function generarfactura()
	{
		$documentos = new Tesdocumentos();

		$CAMB = new Testipocambios();
		$doc = $documentos->find_first((int)7);
		Session::set('DOC_ID', $doc->id);
		Session::set('DOC_NOMBRE', $doc->nombre);
		Session::set('DOC_ABR', $doc->abr);
		Session::set('DOC_CODIGO', $doc->codigo);
		if (Input::hasPost('facturas')) {
			$values = Input::post('facturas');
			$VAL = explode(',', $values['ids']);

			$g = 0;
			$guias = '';
			foreach ($VAL as $value => $item) {
				$salidas = new Tessalidas();
				$salida = $salidas->find_first((int)$item);
				$salida->estadosalida = "Con factura";
				$salida->save();
				if ($g == 0) $coma = '';
				else $coma = ',';
				$guias .= $coma . $salida->serie . '-' . $salida->numero;
				$g++;
			}

			$n = 0;
			foreach ($VAL as $value => $item) {
				if ($n == 0) {
					$salidas = new Tessalidas();
					$salida = $salidas->find_first((int)$item);
					$new_salida = new Tessalidas();
					$new_salida->aclusuarios_id	= $salida->aclusuarios_id;
					$new_salida->tesmonedas_id = $salida->tesmonedas_id;
					$new_salida->tesdatos_id = $salida->tesdatos_id;
					$new_salida->direccion_entrega = $salida->direccion_entrega;
					$new_salida->tesdocumentos_id = 7;
					$new_salida->testipocambios_id = $CAMB->getCambioFecha($salidas->femision);
					$new_salida->codigo = $salida->codigo;
					$V = explode('-', $salida->numerofactura);
					$new_salida->serie = $V[0];
					$new_salida->numero = $V[1];
					$new_salida->femision = $salida->femision;
					$new_salida->fvencimiento = $salida->fvencimiento;
					$new_salida->fregistro = $salida->fregistro;
					$new_salida->npedido = $salida->npedido;
					$new_salida->numeroguia = $guias;
					$new_salida->ordendecompra = $salida->ordendecompra;
					$new_salida->glosa = $salida->glosa;
					$new_salida->cuentagastos = $salida->cuentagastos;
					$new_salida->cuentaporpagar = $salida->cuentaporpagar;
					$new_salida->totalconigv = $salida->totalconigv;
					$new_salida->totalenletras = $salida->totalenletras;
					$new_salida->movimiento = $salida->movimiento;
					$new_salida->finiciotraslado = $salida->finiciotraslado;
					$new_salida->estado = $salida->estado;
					$new_salida->estadosalida = 'Editable';
					$new_salida->userid = $salida->userid;
					$new_salida->aclempresas_id = $salida->aclempresas_id;
					$new_salida->tescuentascorrientes_id = $salida->tescuentascorrientes_id;
					$new_salida->hilodestino_id = $salida->hilodestino_id;
					$new_salida->tescondicionespagos_id = $salida->tescondicionespagos_id;
					$new_salida->tesdatosdirecciones_id = $salida->tesdatosdirecciones_id;
					$new_salida->save();
					Session::set("SALIDA_ID", $new_salida->id);
				}
				$n++;
				$DETALLES = new Tesdetallesalidas();
				$detalle = $DETALLES->find('conditions: tessalidas_id=' . (int)$item);
				foreach ($detalle as $det) {
					$new_detalles = new Tesdetallesalidas();

					$new_detalles->tesproductos_id = $det->tesproductos_id;
					$new_detalles->tessalidas_id = $new_salida->id;
					$new_detalles->tesunidadesmedidas_id = $det->tesunidadesmedidas_id;
					$new_detalles->cantidad = $det->cantidad;
					$new_detalles->preciounitario = $det->preciounitario;
					$descuento = (($det->cantidad * $det->preciounitario) * ($det->descuento) / 100);
					$new_detalles->descuento = $det->descuento;
					$new_detalles->importe = ($det->cantidad * $det->preciounitario) - $descuento;
					$new_detalles->lote = $det->lote;
					$new_detalles->empaque = $det->empaque;
					$new_detalles->tescajas_id = $det->tescajas_id;
					$new_detalles->bobinas = $det->bobinas;
					$new_detalles->pesobruto = $det->pesobruto;
					$new_detalles->pesoneto = $det->pesoneto;
					$new_detalles->estado = $det->estado;
					$new_detalles->userid = $det->userid;
					$new_detalles->aclempresas_id = $det->aclempresas_id;
					$new_detalles->tescolores_id = $det->tescolores_id;
					$new_detalles->prorollos_id = $det->prorollos_id;
					$new_detalles->save();
				}
			}
		}
		return Redirect::toAction('venta/');
	}
	public function aplicaradelanto($id, $f_id, $total = 0)
	{
		$modulo = Redirect::get('module');
		$this->controller = Redirect::get('controller');
		$adelantos = new Tesfacturasadelantos();
		$tessalidas = new Tessalidas();
		/*el $t_factura_a es la resta de total de factura adleanto menos la suma de todas las plicaciones */
		$t_factura_a = $adelantos->getTotal_aplicacion($f_id);
		/*el $t_salida_a es el total */
		$t_salida_a = $total; //$tessalidas->getTotal_aplicacion($id);

		$adelanto = $adelantos->find_first($f_id);
		$this->salida = $salida = $tessalidas->find_first($id);

		$aplicacion = new Tesaplicacionfacturasadelantos();
		$aplicacion->tesfacturasadelantos_id = $adelanto->id;
		$aplicacion->tesmonedas_id = $salida->tesmonedas_id;
		$aplicacion->serie = $salida->serie;
		$aplicacion->numero = $salida->numero;
		if ($t_salida_a >= $t_factura_a) $aplicacion->montototal = $t_factura_a;
		else $aplicacion->montototal = $t_salida_a;
		$aplicacion->estado = '1';
		$aplicacion->userid = Auth::get('id');
		$aplicacion->tessalidas_id = $salida->id;
		$aplicacion->aclempresas_id = Session::get("EMPRESAS_ID");
		$aplicacion->save();
		/*Auditorias*/
		//Aclauditorias::add("Creo una nueva letra interna ,Tesletrassalidasinternas->id={$letrassalida->id}",'Tesletrassalidasinternas');
		/*fin Aurditorias*/
		if ($t_factura_a < $t_salida_a) {
			$adelanto->estado = '9';
			$adelanto->save();
		}
		/*PARA LA CANCELACION DE LA FACTURA*/
		if (number_format($this->salida->totalconigv, 2, '.', '') == number_format($aplicacion->montototal, 2, '.', '')) {
			$salida->estadosalida = 'PAGADO';
			$salida->save();
		}


		$DETALLES = new Tesdetallesalidas();
		$this->detalles = $DETALLES->find('conditions: tessalidas_id=' . (int)Session::get('SALIDA_ID'));
		/* mostrar las facturas por adelanto y si monto final */
		$adelantos = new Tesfacturasadelantos();
		$this->adelantos = FALSE;
		if ($adelantos->exists('tesdatos_id=' . $salida->tesdatos_id . ' AND estado=1')) {
			$this->adelantos = $adelantos->getFacturasAdelantos($salida->tesdatos_id);
			//$this->adelantos=$adelantos->find('conditions: tesdatos_id='.$salida->tesdatos_id.' AND estado=1');
		}
		/* Busa las aplicaciones a esta factura */
		$aplicaciones = new Tesaplicacionfacturasadelantos();
		$this->aplicaciones = FALSE;
		if ($aplicaciones->exists('tessalidas_id=' . $salida->id)) {

			$this->aplicaciones = $aplicaciones->find('conditions: tessalidas_id=' . $salida->id);
		}

		$monedas = $salida->tesmonedas_id;
		switch ($monedas) {
			case 1:
				$this->simbolo = 'S/. ';
				$this->moneda_letras = 'NUEVOS SOLES';
				break;
			case 2:
				$this->simbolo = '$. ';
				$this->moneda_letras = 'DOLARES AMERICANOS';
				break;
			case 4:
				$this->simbolo = 'S/. ';
				$this->moneda_letras = 'NUEVOS SOLES';
				break;
			case 5:
				$this->simbolo = '$. ';
				$this->moneda_letras = 'DOLARES AMERICANOS';
				break;
			case 0:
				$this->simbolo = 'S/. ';
				$this->moneda_letras = 'NUEVOS SOLES';
				break;
		}
	}
	public function venta()
	{
		Flash::valid('Esta es la vista previa de la factura !!!');
		$salidas = new Tessalidas();
		$this->salida = $salidas->find_first(Session::get('SALIDA_ID'));
		$DETALLES = new Tesdetallesalidas();
		$this->detalles = $DETALLES->find('conditions: tessalidas_id=' . (int)Session::get('SALIDA_ID'));
		/* mostrar las facturas por adelanto y si monto final */
		$adelantos = new Tesfacturasadelantos();
		$this->adelantos = FALSE;
		if ($adelantos->exists('tesdatos_id=' . $this->salida->tesdatos_id . ' AND estado=1')) {
			$this->adelantos = $adelantos->getFacturasAdelantos($this->salida->tesdatos_id);
		}
		/* Busa las aplicaciones a esta factura */
		$aplicaciones = new Tesaplicacionfacturasadelantos();
		$this->aplicaciones = FALSE;
		if ($aplicaciones->exists('tessalidas_id=' . $this->salida->id)) {

			$this->aplicaciones = $aplicaciones->find('conditions: tessalidas_id=' . $this->salida->id);
		}
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			if ($salidas->save()) {
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida/');
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				return Redirect::toAction('venta/');
			}
		}
	}
	public function eliminar_app($id)
	{
		$aplicaciones = new Tesaplicacionfacturasadelantos();
		$facturas = new Tesfacturasadelantos();
		$app = $aplicaciones->find_first($id);
		$facturas->actulizar_factura($app->tesfacturasadelantos_id);
		$aplicaciones->delete($id);
		return Redirect::toAction('venta/');
	}
	public function limpiar_factura($id)
	{
		View::template(null);
		$aplicaciones = new Tesaplicacionfacturasadelantos();
		//$app=$aplicaciones->find_first($id);
		$facturas = new Tesfacturasadelantos();
		$dapp = '';
		if ($id == 0) $dapp = 'tesfacturasadelantos_id=' . $id . ' AND ';
		if ($aplicaciones->delete($dapp . 'estado=0')) {
			//$facturas->actulizar_factura($app->tesfacturasadelantos_id);
			Flash::valid('Se recalcuraron los totales');
		} else {
			Flash::warning('Los totales esta bien calculados!!!');
		}
		return Redirect::toAction('venta/');
	}
	public function versalidafactventa_w()
	{
		View::template(null);
	}
	public function versalidafactventa_i()
	{
		/* ver la factura a imprimir*/
	}
	public function facturaadelanto()
	{
		/* ver la factura a adelanto*/
	}
	public function versalidafactventa()
	{
	}
	public function versalidaguiaventa()
	{
	}

	public function letrasruc($id = 7)
	{
		$this->titulo = 'Letras del mes ' . date("M");
		$this->url = 'crearsalidas';
		$documentos = new Tesdocumentos();
		$doc = $documentos->find_first((int)$id);
		Session::set('DOC_ID', $doc->id);
		Session::set('DOC_NOMBRE', $doc->nombre);
		Session::set('DOC_ABR', $doc->abr);
		Session::set('DOC_CODIGO', $doc->codigo);
		/*
		serie 001
		salida de de rollos para venta
		*/
		$salidas = new Tessalidas();
		$this->salidas = $salidas->find('conditions: serie="001" AND estadosalida!="TERMINADO" AND npedido like "TE%" AND estado=1 AND tesdocumentos_id=' . Session::get('DOC_ID'), 'order: fecha_at ASC');
		Session::delete("SALIDA_ID");
		$this->guias = $salidas->find('conditions: serie="001" AND estadosalida="Pendiente" AND npedido like "TE%" AND estado=1 AND tesdocumentos_id=15', 'order: fecha_at ASC');
	}
	public function estadocuentas($id = 7)
	{
	}
	/*Notas de debito y credito*/

	public function cargar_doc($id = 0, $url = 'nota_creditos')
	{

		$documentos = new Tesdocumentos();
		$doc = $documentos->find_first((int)$id);
		Session::set('DOC_ID', $doc->id);
		Session::set('DOC_NOMBRE', $doc->nombre);
		Session::set('DOC_ABR', $doc->abr);
		Session::set('DOC_CODIGO', $doc->codigo);
		return Redirect::toAction($url);
	}
	public function crear_nota($id = 0)
	{
		$CAMB = new Testipocambios();
		$npedido = 'ND';
		$this->url = 'nota_debitos';
		if (Session::get('DOC_ID') == 13) $npedido = 'NC';
		$this->url = 'nota_creditos';
		$SALD = new Tessalidas();
		if ($id == 0) {
			$this->salida['serie'] = '001';
			$this->salida['numero'] = $SALD->generarNumero(Session::get('DOC_ID'), '001');
			$this->salida['npedido'] = $SALD->getNumeropedido($npedido, '001');
		} else {
			$this->salida = $SALD->find_first($id);
			$DETALLES = new Tesdetallesalidas();
			$this->detalle = $DETALLES->find_first('conditions: tessalidas_id=' . (int)$id);
		}
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id = Session::get('DOC_ID');
			$salidas->fvencimiento = date("Y-m-d", strtotime($salidas->femision . "+ 30 days"));
			$salidas->estadosalida = 'Editable';
			$salidas->estado = 1;
			$salidas->userid = Auth::get('id');
			$salidas->activo = '1';
			$salidas->testipocambios_id == $CAMB->getCambioFecha($salidas->femision);
			$salidas->userid = Auth::get('id');
			$salidas->aclusuarios_id = Auth::get('id');
			$salidas->aclempresas_id = Session::get("EMPRESAS_ID");
			if ($salidas->save()) {
				Session::set("SALIDA_ID", $salidas->id);

				if (Input::hasPost('detalle')) {
					$detalles = new Tesdetallesalidas(Input::post('detalle'));
					$detalles->tessalidas_id = Session::get('SALIDA_ID');
					$detalles->cantidad = 1;
					$detalles->tesunidadesmedidas_id = '15';
					$detalles->userid = Auth::get('id');
					$detalles->estado = '1';
					$detalles->tescolores_id = '1';
					$detalles->aclempresas_id = Session::get("EMPRESAS_ID");
					$detalles->save();
				}

				//Flash::valid('fue agregada Exitosamente...!!!');
				Aclauditorias::add("Creo una Nuevo Salida " . Session::get('DOC_NOMBRE') . "-{$salidas->numero} al sistema");

				return Redirect::toAction('detalle_nota/');
				unset($_POST);
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				$this->salida = Input::post('salida');
				$this->detalle = Input::post('detalle');
				return Redirect::toAction('crear_nota/');
			}
		}
	}

	public function detalle_nota()
	{
		$salidas = new Tessalidas();
		$this->salida = $salidas->find_first(Session::get('SALIDA_ID'));
		$DETALLES = new Tesdetallesalidas();
		$this->detalles = $DETALLES->find('conditions: tessalidas_id=' . (int)Session::get('SALIDA_ID'));
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			if ($salidas->save()) {
				Flash::valid('La Salida se realizo con exito!!!');
				$url = 'notadebito';
				if (Session::get('DOC_ID') == 13) $url = 'notacredito';
				return Redirect::toAction($url . '/');
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				return Redirect::toAction('detalle_nota/');
			}
		}
	}

	public function nota_creditos()
	{
		$empresas = new Aclempresas();
		$this->empresa = $empresas->find_first(Session::get("EMPRESAS_ID"));
		$tessalidas = new Tessalidas();
		$this->salidas = $tessalidas->find('conditions: aclempresas_id=' . Session::get("EMPRESAS_ID") . ' AND tesdocumentos_id=' . Session::get("DOC_ID"));
	}

	public function nota_debitos()
	{
		$empresas = new Aclempresas();
		$this->empresa = $empresas->find_first(Session::get("EMPRESAS_ID"));
		$tessalidas = new Tessalidas();
		$this->salidas = $tessalidas->find('conditions: aclempresas_id=' . Session::get("EMPRESAS_ID") . ' AND tesdocumentos_id=' . Session::get("DOC_ID"));
	}

	public function notacredito()
	{
		$salidas = new Tessalidas();
		$this->salida = $salidas->find_first(Session::get('SALIDA_ID'));
		$DETALLES = new Tesdetallesalidas();
		$this->detalles = $DETALLES->find('conditions: tessalidas_id=' . (int)Session::get('SALIDA_ID'));
	}

	public function notadebito()
	{
		$salidas = new Tessalidas();
		$this->salida = $salidas->find_first(Session::get('SALIDA_ID'));
		$DETALLES = new Tesdetallesalidas();
		$this->detalles = $DETALLES->find('conditions: tessalidas_id=' . (int)Session::get('SALIDA_ID'));
	}

	/*factura por adelanto crear editar y anular*/

	public function factura_adelanto($id = 0)
	{
		$CAMB = new Testipocambios();
		$SALD = new Tessalidas();
		if ($id == 0) {
			$this->salida['serie'] = 'F002';
			$this->salida['numero'] = $SALD->generarNumero(Session::get('DOC_ID'), 'F002');
			$this->salida['npedido'] = $SALD->getNumeropedido('PA', '001');
		} else {
			$this->salida = $SALD->find_first($id);
			$DETALLES = new Tesdetallesalidas();
			$this->detalle = $DETALLES->find_first('conditions: tessalidas_id=' . (int)$id);
		}
		if (Input::hasPost('salida')) {
			//$campos=Input::post('salida');
			$salidas = new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id = Session::get('DOC_ID');
			$salidas->fvencimiento = date("Y-m-d", strtotime($salidas->femision . "+ 30 days"));
			$salidas->estadosalida = 'Editable';
			if (!empty($_POST['ordendecompra'])) $salidas->ordendecompra = '"' . (string)$_POST['ordendecompra'] . '"';
			$salidas->estado = 1;
			$salidas->userid = Auth::get('id');
			$salidas->activo = '1';
			$salidas->testipocambios_id = $CAMB->getCambioFecha($salidas->femision);
			$salidas->userid = Auth::get('id');
			$salidas->aclusuarios_id = Auth::get('id');
			$salidas->aclempresas_id = Session::get("EMPRESAS_ID");
			if ($salidas->save()) {
				Session::set("SALIDA_ID", $salidas->id);
				$factura = new Tesfacturasadelantos();
				$salida_p = $SALD->find_first((int)$salidas->id);
				if ($factura->exists('tessalidas_id=' . $salida_p->id)) {
					$factura_a = $factura->find_first('tessalidas_id=' . $salida_p->id);
				} else {
					$factura_a = new Tesfacturasadelantos();
				}
				$factura_a->tessalidas_id = $salida_p->id;
				$factura_a->tesmonedas_id = $salida_p->tesmonedas_id;
				$factura_a->tesdatos_id = $salida_p->tesdatos_id;
				$factura_a->serie = $salida_p->serie;
				$factura_a->numero = $salida_p->numero;
				$factura_a->montototal = (float)$salida_p->totalconigv - (float)$salida_p->igv;
				$factura_a->estado = '1';
				$factura_a->userid = Auth::get('id');
				if ($factura_a->save()) {
					//Flash::valid('Factur compelkta... ('.$factura_a->montototal.'---'.$salida_p->totalconigv.') !!!');
				} else { //Flash::warning('No se Pudieron Guardar los Datos...('.$factura_a->montototal.')!!!');
				}
				if (!empty($salidas->ordendecompra)) {
					$orden = new Tesordendecompras();
					$or = $orden->find_first('conditions: codigo=' . (string)$salidas->ordendecompra . '');
					$orden->aclusuarios_id = $or->aclusuarios_id;
					$orden->estado = "0";
					$orden->save();
				}
				if (Input::hasPost('detalle')) {
					$detalles = new Tesdetallesalidas(Input::post('detalle'));
					$detalles->tessalidas_id = Session::get('SALIDA_ID');
					$detalles->cantidad = 1;
					$detalles->tesunidadesmedidas_id = '15';
					$detalles->userid = Auth::get('id');
					$detalles->estado = '1';
					$detalles->tescolores_id = '1';
					$detalles->aclempresas_id = Session::get("EMPRESAS_ID");
					$detalles->save();
				}

				//Flash::valid('fue agregada Exitosamente...!!!');
				Aclauditorias::add("Creo una Nuevo Salida " . Session::get('DOC_NOMBRE') . "-{$salidas->numero} al sistema");

				return Redirect::toAction('factura_adelanto_detalle/');
				unset($_POST);
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				$this->salida = Input::post('salida');
				$this->detalle = Input::post('detalle');
				return Redirect::toAction('factura_adelanto/');
			}
		}
	}

	public function factura_adelanto_detalle()
	{
		$salidas = new Tessalidas();
		$this->salida = $salidas->find_first(Session::get('SALIDA_ID'));
		$DETALLES = new Tesdetallesalidas();
		$this->detalles = $DETALLES->find('conditions: tessalidas_id=' . (int)Session::get('SALIDA_ID'));
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			if ($salidas->save()) {
				$s_a = $salidas->find_first((int)Session::get("SALIDA_ID"));
				Session::get("SALIDA_ID");
				$factura = new Tesfacturasadelantos();
				$factura_a = $factura->find_first('tessalidas_id=' . Session::get("SALIDA_ID"));
				$factura_a->tessalidas_id = $s_a->id;
				$factura_a->tesmonedas_id = $s_a->tesmonedas_id;
				$factura_a->tesdatos_id = $s_a->tesdatos_id;
				$factura_a->serie = $s_a->serie;
				$factura_a->numero = $s_a->numero;
				$factura_a->montototal = (float)$s_a->totalconigv - (float)$s_a->igv;
				$factura_a->estado = '1';
				$factura_a->userid = Auth::get('id');
				if ($factura_a->save()) {
					Flash::valid('La Salida se realizo con exito!!!');
					return Redirect::toAction('versalida_adelanto/');
				} else {
					Flash::warning('No se Pudieron Guardar los Datos...!!!');
					return Redirect::toAction('factura_adelanto_detalle/');
				}
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				return Redirect::toAction('factura_adelanto_detalle/');
			}
		}
	}
	public function versalida_adelanto()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			if ($salidas->save()) {
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida_adelanto/');
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$salidas = new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$this->salida = $salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles = $detalles->find('conditions: tessalidas_id=' . (int)Session::get("SALIDA_ID"));
		$detalleT = new Prodetalletransportes();
		$this->prodetalletransportes = $detalleT->find_first('conditions: tessalidas_id=' . Session::get('SALIDA_ID'));
		$letras = new Tesletrassalidas();
		$this->letras = $letras->count('estadoletras="Editable" AND factura_id=' . (int)Session::get("SALIDA_ID"));
		$this->l_f = $letras->count('estadoletras="Sin Numero" AND factura_id=' . (int)Session::get("SALIDA_ID"));
		$letrassalida = new Tesletrassalidas();
		$this->lets = $letrassalida->find('conditions: factura_id=' . Session::get('SALIDA_ID'));
	}

	public function versalida_f()
	{
		if (Session::get('DOC_ID') == 15) View::select('versalidaguiaventa');
		if (Session::get('DOC_ID') == 7) View::select('facturaadelanto');

		$empresas = new Aclempresas();
		$this->empresa = $empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas = new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$this->salida = $salidas->find_first((int)Session::get('SALIDA_ID'));
		if (Session::get('DOC_ID') == 7) {
			$this->detalles = $detalles->find('columns: tesdetallesalidas.' . join(',tesdetallesalidas.', $detalles->fields) . ',sum(tesdetallesalidas.cantidad) as totalcantidad,sum(tesdetallesalidas.importe) as totalimporte,sum(tesdetallesalidas.descuento) as totaldescuento', 'conditions: tessalidas_id=' . (int)Session::get("SALIDA_ID"), 'GROUP: tescolores_id,tesproductos_id');
		}
		if (Session::get('DOC_ID') == 15) {
			$this->detalles = $detalles->find('conditions: tessalidas_id=' . (int)Session::get("SALIDA_ID"));
		}
		$detalleT = new Prodetalletransportes();
		$this->prodetalletransportes = $detalleT->find_first('conditions: tessalidas_id=' . Session::get('SALIDA_ID'));
		$letrassalida = new Tesletrassalidas();
		$this->lets = $letrassalida->find('conditions: factura_id=' . Session::get('SALIDA_ID'));
	}
	public function listado_servicio($Y = '', $m = '')
	{
		$this->titulo = 'Facturas del mes';
		$this->url = 'crearsalidas';
		$anio = $this->anio = Session::get('ANIO');
		if ($Y != '') $anio = $this->anio = $Y;
		$mes_activo = $this->mes_activo = date('m');
		if ($m != '') $mes_activo = $this->mes_activo = $m;
		/*
	serie 001
	salida de de rollos para venta
	*/
		$salidas = new Tessalidas();
		$this->salidas = $salidas->find('conditions: (serie="F001" OR serie="001" OR serie = "F002") AND estadosalida!="TERMINADO" AND (npedido like "SV%" OR npedido like "SR%") AND estado=1 AND tesdocumentos_id=' . Session::get('DOC_ID') . ' AND aclempresas_id=' . Session::get("EMPRESAS_ID"). ' AND DATE_FORMAT(femision, "%Y-%m")="' . $anio . '-' . $mes_activo . '"', 'order: fecha_at DESC');
		Session::delete("SALIDA_ID");
		$this->guias = $salidas->find('conditions: (serie="F001" OR serie="001" OR serie = "F002") AND estadosalida="Pendiente" AND (npedido like "SV%" OR npedido like "SR%" ) AND estado=1 AND tesdocumentos_id=15 AND aclempresas_id=' . Session::get("EMPRESAS_ID"), 'order: fecha_at DESC');
	}
	public function guia_servicio($id = 0)
	{
		$SALD = new Tessalidas();
		$CAMB = new Testipocambios();
		
		if ($id == 0) {
			$this->salida['serie'] = '001';
			$this->salida['numero'] = $SALD->generarNumero(Session::get('DOC_ID'), '001');
			$this->salida['npedido'] = $SALD->getNumeropedido('SR', '001');
			$this->salida['numerofactura'] = $SALD->generarNumeroFactura('F002');
		} else {
			$this->salida = $SALD->find_first($id);
			$DETALLES = new Tesdetallesalidas();
			$this->detalle = $DETALLES->find_first('conditions: tessalidas_id=' . (int)$id);
		}
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id = Session::get('DOC_ID');
			$salidas->fvencimiento = date("Y-m-d", strtotime($salidas->femision . "+ 30 days"));
			$salidas->estadosalida = 'Editable';
			$salidas->estado = 1;
			$salidas->userid = Auth::get('id');
			$salidas->activo = '1';
			$salidas->testipocambios_id = $CAMB->getCambioFecha($salidas->femision);
			$salidas->userid = Auth::get('id');
			$salidas->aclusuarios_id = Auth::get('id');
			$salidas->aclempresas_id = Session::get("EMPRESAS_ID");
			if ($salidas->save()) {
				$detalleT = new Prodetalletransportes(Input::post('prodetalletransportes'));
				$detalleT->tessalidas_id = $salidas->id;
				$detalleT->numero = $salidas->numero;
				$detalleT->serie = $salidas->serie;
				$detalleT->fechatraslado = $salidas->finiciotraslado;
				$detalleT->estado = '1';
				$detalleT->userid = Auth::get('id');
				$detalleT->save();

				Session::set("SALIDA_ID", $salidas->id);

				//Flash::valid('fue agregada Exitosamente...!!!');
				Aclauditorias::add("Creo una Nuevo Salida " . Session::get('DOC_NOMBRE') . "-{$salidas->numero} al sistema");

				return Redirect::toAction('factura_servicio_detalle/servicio');
				unset($_POST);
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				$this->salida = Input::post('salida');
				return Redirect::toAction('crearsalidas/');
			}
		}
	}
	public function factura_servicio($id = 0)
	{
		$SALD = new Tessalidas();
		$CAMB = new Testipocambios();
		if ($id == 0) {
			$this->salida['serie'] = 'F002';
			$this->salida['numero'] = $SALD->generarNumero(Session::get('DOC_ID'), 'F002');
			$this->salida['npedido'] = $SALD->getNumeropedido('SV', '001');
		} else {
			$this->salida = $SALD->find_first($id);
			$DETALLES = new Tesdetallesalidas();
			$this->detalle = $DETALLES->find_first('conditions: tessalidas_id=' . (int)$id);
		}
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			$salidas->tesdocumentos_id = Session::get('DOC_ID');
			$salidas->fvencimiento = date("Y-m-d", strtotime($salidas->femision . "+ 30 days"));
			$salidas->estadosalida = 'Editable';
			$salidas->estado = 1;
			$salidas->userid = Auth::get('id');
			$salidas->activo = '1';
			$salidas->testipocambios_id = $CAMB->getCambioFecha($salidas->femision);
			$salidas->userid = Auth::get('id');
			$salidas->aclusuarios_id = Auth::get('id');
			$salidas->aclempresas_id = Session::get("EMPRESAS_ID");
			if ($salidas->save()) {
				Session::set("SALIDA_ID", $salidas->id);
				$detalles = new Tesdetallesalidas(Input::post('detalle'));
				$detalles->tessalidas_id = Session::get('SALIDA_ID');
				$detalles->cantidad = 1;
				$detalles->tescolores_id = '773';
				$detalles->tesunidadesmedidas_id = '14';
				$detalles->userid = Auth::get('id');
				$detalles->estado = '1';
				$detalles->tescolores_id = '1';
				$detalles->aclempresas_id = Session::get("EMPRESAS_ID");
				$detalles->save();


				//Flash::valid('fue agregada Exitosamente...!!!');
				Aclauditorias::add("Creo una Nuevo Salida " . Session::get('DOC_NOMBRE') . "-{$salidas->numero} al sistema");

				return Redirect::toAction('factura_servicio_detalle/');
				unset($_POST);
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				$this->salida = Input::post('salida');
				$this->detalle = Input::post('detalle');
				return Redirect::toAction('factura_servicio/');
			}
		}
	}
	public function factura_servicio_detalle($s = 'servicio')
	{
		$this->s = $s;
		$salidas = new Tessalidas();
		$this->salida = $salidas->find_first(Session::get('SALIDA_ID'));
		$DETALLES = new Tesdetallesalidas();
		$this->detalles = $DETALLES->find('conditions: tessalidas_id=' . (int)Session::get('SALIDA_ID'));
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			if ($salidas->save()) {
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida_servicio/');
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
				return Redirect::toAction('factura_servicio_detalle/');
			}
		}
		if (Input::hasPost('detalle')) {
			$detalles = new Tesdetallesalidas(Input::post('detalle'));
			$detalles->tessalidas_id = Session::get('SALIDA_ID');
			$detalles->tescolores_id = '773';
			$detalles->tesunidadesmedidas_id = '14';
			$detalles->cantidad = Input::post('cantidad');
			$detalles->preciounitario = Input::post('preciounitario');
			$detalles->importe = Input::post('preciounitario') * Input::post('cantidad');
			$detalles->userid = Auth::get('id');
			$detalles->estado = '1';
			$detalles->tescolores_id = '1';
			$detalles->aclempresas_id = Session::get("EMPRESAS_ID");
			$detalles->save();
			return Redirect::toAction('factura_servicio_detalle/');
		}
	}
	public function versalida_servicio()
	{
		/*base64_encode($str);
		base64_decode($str);*/
		if (Input::hasPost('salida')) {
			$salidas = new Tessalidas(Input::post('salida'));
			if ($salidas->save()) {
				Flash::valid('La Salida se realizo con exito!!!');
				return Redirect::toAction('versalida_servicio/');
			} else {
				Flash::warning('No se Pudieron Guardar los Datos...!!!');
			}
		}
		$salidas = new Tessalidas();
		$detalles = new Tesdetallesalidas();
		$this->salida = $salidas->find_first((int)Session::get('SALIDA_ID'));
		$this->detalles = $detalles->find('conditions: tessalidas_id=' . (int)Session::get("SALIDA_ID"));
		$detalleT = new Prodetalletransportes();
		$this->prodetalletransportes = $detalleT->find_first('conditions: tessalidas_id=' . Session::get('SALIDA_ID'));
		$letras = new Tesletrassalidas();
		$this->letras = $letras->count('estadoletras="Editable" AND factura_id=' . (int)Session::get("SALIDA_ID"));
		$this->l_f = $letras->count('estadoletras="Sin Numero" AND factura_id=' . (int)Session::get("SALIDA_ID"));
		$letrassalida = new Tesletrassalidas();
		$this->lets = $letrassalida->find('conditions: factura_id=' . Session::get('SALIDA_ID'));
	}

	public function ordencompra()
	{
		try {

			$this->data = [];
			$q = $_GET['q'];
			$obj = new Tesordendecompras();
			$results = $obj->find('conditions: estado=1 AND origenorden="externo" and codigo like "%' . $q . '%" AND aclempresas_id=' . Session::get('EMPRESAS_ID'));
			foreach ($results as $value) {
				$id = (string)$value->codigo;
				$name = $value->getTesdatos()->razonsocial . ' Nº' . $value->codigo;
				$json = array();
				$json['id'] = $id;
				$json['name'] = $name;
				$this->data[] = $json;
			}
		} catch (\Exception $e) {
			var_dump($e);
			exit();
		}
	}
	/*Actualizar el detalle la salida obteniedo la url y redireccionar*/
	public function modificardetalle($url = '')
	{
		if ($url != '') {
			$n = $_POST['numero'];
			if (Input::hasPost('detalle-' . $n)) {
				$detalles = new Tesdetallesalidas(Input::post('detalle-' . $n));
				if ($detalles->save()) {
					$DET = new Tesdetallesalidas();
					$det_des = $DET->find_first($detalles->id);
					/*valida si hay precio unitario*/

					if (empty($det_des->preciounitario)) {
						$DET->preciounitario = $det_des->importe;
						$descuento = (($det_des->importe) * ($det_des->descuento) / 100);
						$DET->importe = ($det_des->importe) - $descuento;
					} else {
						$descuento = (($det_des->cantidad * $det_des->preciounitario) * ($det_des->descuento) / 100);
						$DET->importe = ($det_des->cantidad * $det_des->preciounitario) - $descuento;
					}
					$DET->descuento = ($det_des->descuento) / 100;
					$DET->save();
					Flash::valid('Actualizado!!!');
					return Redirect::toAction($url);
				} else {
					Flash::warning('No se Pudieron Guardar los Datos...!!!');

					return Redirect::toAction($url);
				}
			}
		}
	}


	/*Anular factura*/
	public function anularfacturas($id)
	{
		$salidas = new Tessalidas();
		$salidas->anularfactura($id);

		return Redirect::toAction('facturas/7');
	}
	/*Anular Guia*/
	public function anularguias($id)
	{
		$salidas = new Tessalidas();
		$salidas->anularguia($id);

		return Redirect::toAction('facturas/7');
	}

	public function editar_detalle($id = 0, $url = '')
	{
		if (Input::hasPost('detalle')) {
			$detalles = new Tesdetallesalidas(Input::post('detalle'));
			$detalles->save();
			return Redirect::toAction('venta/');
		}
		$detalles = new Tesdetallesalidas();
		$this->detalle = $detalles->find_first($id);
	}

	public function precios($Y = '')
	{
		$anio = $this->anio = Session::get('ANIO');
		if ($Y != '') $anio = $this->anio = $Y;
	}

	public function cantidades()
	{
	}
	public function cantidades_clientes($id, $a, $b)
	{
		$detalles = new Tesdetallesalidas();
		$tesdatos = new Tesdatos();
		$this->dato = $tesdatos->find_first($id);
		$this->detalles = $detalles->getCantidades(" AND s.tesdatos_id=" . $id, $a, $b);
	}
	public function precios_clientes($id, $Y = '')
	{
		$anio = $this->anio = Session::get('ANIO');
		if ($Y != '') $anio = $this->anio = $Y;
		$detalles = new Tesdetallesalidas();
		$tesdatos = new Tesdatos();
		$this->dato = $tesdatos->find_first($id);

		$this->detalles = $detalles->getPrecios(" AND s.tesdatos_id=" . $id . " and DATE_FORMAT(s.femision,'%Y')='" . $anio . "'", ' ORDER BY fecha,numero ASC');
	}
	public function precios_telas($id, $Y = '')
	{
		$anio = $this->anio = Session::get('ANIO');
		if ($Y != '') $anio = $this->anio = $Y;
		$detalles = new Tesdetallesalidas();
		$tesproductos = new tesproductos();
		$this->producto = $tesproductos->find_first($id);
		$this->detalles = $detalles->getPrecios(" AND d.tesproductos_id=" . $id . " and DATE_FORMAT(s.femision,'%Y')='" . $anio . "'", ' ORDER BY fecha,numero DESC');
	}
	public function selector()
	{
		/*Seleccionar el tipo de maquina*/
		$tipoproductos = new Testipoproductos();
		$this->tipos = $tipoproductos->find('conditions: id!=37 AND teslineaproductos_id=9');
	}
}
