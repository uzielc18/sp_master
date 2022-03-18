<?php
Load::models('testipoproductos');

class Tesproductos extends ActiveRecord
{

	public function initialize()
	{
		//relaciones
		$this->has_many('tesdetallesalidasinternas', 'tesdetallesalidas', 'tesdetalleproformas', 'tesdetalleingresos', 'prorollos', 'prodetalleproduccion', 'prodetallepedidos');
		$this->belongs_to('aclusuarios', 'testipoproductos', 'aclempresas', 'hilosistema', 'hilofibras', 'hiloacabados');
		$this->has_one('protransportes', 'prorepuestos', 'proplegadores', 'promaquinas');
		$this->validates_presence_of('codigo', 'message: Debe escribir un <b>Codigo </b> para el Producto');
		$this->validates_presence_of('nombre', 'message: Debe escribir un <b>Nombre </b> para el Producto');
		$this->validates_presence_of('precio', 'message: Debe escribir un <b>Precio </b> para el Producto');
		$this->validates_presence_of('testipoproductos_id', 'message: Debe seleccionar<b> una linea y un tipo </b> para el Producto');
		$this->validates_presence_of('precio', 'message: Debe escribir un <b>Precio </b> para el Producto');
	}
	public function before_validation_on_create()
	{
		$this->validates_uniqueness_of('codigo', 'message: El <b>Codigo</b> ya está siendo utilizado');
	}
	public function before_save()
	{
		$numero = $this->testipoproductos_id;
		$sigla = $this->getTestipoproductos()->abr;
		$prefijo = $sigla . "-" . $numero;
		$this->prefijo = $prefijo;
	}
	public function getHilos($id)
	{
		$hilo = '';
		if ($id != 0) {
			$hilo = '(' . $this->obtener_tipo_productos($id) . ') AND ';
		}
		return $this->find('conditions: ' . $hilo . 'estado=1 and aclempresas_id=' . Session::get('EMPRESAS_ID') . '');
	}

	public function getTelas($id)
	{
		$hilo = '';
		if ($id != 0) {
			$hilo = '(' . $this->obtener_tipo_productos($id) . ') AND ';
		}
		$hilo . 'estado=1 and aclempresas_id=' . Session::get('EMPRESAS_ID');
		return $this->find('conditions: ' . $hilo . 'estado=1 and aclempresas_id=' . Session::get('EMPRESAS_ID') . '');
	}

	/*genera el codigo del producto a razon del codigo anterior recibe el tipo de producto que es*/
	public function generacodigo()
	{
		$maximo = $this->maximum("codigo");
		if (empty($maximo)) {
			$maximo = 1;
		} else {
			$maximo = $maximo + 1;
		}
		switch ($maximo) {
			case $maximo < 10:
				$maximo = "000000" . $maximo;
				break;
			case $maximo < 100:
				$maximo = "00000" . $maximo;
				break;
			case $maximo < 1000:
				$maximo = "0000" . $maximo;
				break;
			case $maximo < 10000:
				$maximo = "000" . $maximo;
				break;
			case $maximo < 100000:
				$maximo = "00" . $maximo;
				break;
			case $maximo < 1000000:
				$maximo = "0" . $maximo;
				break;
			default:
				$maximo = $maximo;
		}
		return $maximo;
	}
	/*
	esta Funciion se usra solo en casos extremo para la actualizacion de los codigos de los productos si los productos exedieran mas de 1000000
	*/
	public function actualizarP()
	{
		$val = $this->find('conditions: codigo >=008670
AND codigo <=009460');
		foreach ($val as $v) :
			/*$maximo=$v->id;
		switch($maximo)
		{
			case $maximo<10: $nuevoCodigo="000000".$v->id; break;
			case $maximo<100: $nuevoCodigo="00000".$v->id; break;
			case $maximo<1000: $nuevoCodigo="0000".$v->id; break;
			case $maximo<10000: $nuevoCodigo="000".$v->id; break;
			case $maximo<100000: $nuevoCodigo="00".$v->id; break;
			case $maximo<1000000: $nuevoCodigo="0".$v->id; break;
		}*/
			$producto = $this->find_first((int)$v->id);
			$producto->codigo = '0' . $v->codigo;
			if (!$producto->update()) {
				return FALSE;
				break;
			}
		endforeach;
	}
	/*recibe el id del producto 
# id int 
*/
	public function getStockInicial($id = 0, $inventario)
	{
		$ingresos = Load::model('tesdetalleingresos');
		return $ingresos->sum('cantidad', 'conditions: tesdetalleingresos.tesingresos_id=' . $inventario . ' AND tesdetalleingresos.tesproductos_id=' . $id, 'join: INNER JOIN tesingresos as i ON i.id = ' . $inventario . ' AND i.tesdocumentos_id=27');
	}
	public function getStockInicialColor($id = 0, $inventario, $color_id)
	{
		$ingresos = Load::model('tesdetalleingresos');
		return $ingresos->sum('cantidad', 'conditions: tesdetalleingresos.tescolores_id=' . $color_id . ' AND tesdetalleingresos.tesingresos_id=' . $inventario . ' AND tesdetalleingresos.tesproductos_id=' . $id, 'join: INNER JOIN tesingresos as i ON i.id = ' . $inventario . ' AND i.tesdocumentos_id=27');
	}
	public function getStockInicialLote($id = 0, $inventario, $lote)
	{
		$ingresos = Load::model('tesdetalleingresos');
		return $ingresos->sum('cantidad', 'conditions: tesdetalleingresos.lote="' . $lote . '" AND tesdetalleingresos.tesingresos_id=' . $inventario . ' AND tesdetalleingresos.tesproductos_id=' . $id, 'join: INNER JOIN tesingresos as i ON i.id = ' . $inventario . ' AND i.tesdocumentos_id=27');
	}
	/*recibe el id del producto 
# id int 
recibe el tipo de stock ya sea total,salidas,ingresos
*/
	public function getStock($id = 0)
	{
		$i = $this->getTotalingresos($id);
		$s = $this->getTotalsalidas($id);
		$TOTAL = ((float)$i - (float)$s);
		/*if($id==7408){
	echo (int)$i.' ingresos<br />';
	echo (int)$s.' salidas<br />';
	}*/
		return $TOTAL;
	}
	public function getStockColor($id, $color_id)
	{
		$i = $this->getTotalingresos($id, $color_id);
		$s = $this->getTotalsalidas($id, $color_id);
		/*echo $id.'=>'.$i.'===='.$s;
	echo "<br />";*/
		$TOTAL = ((float)$i - (float)$s);

		return $TOTAL;
	}
	public function getStockLote($id, $color_id, $lote, $calidad = '')
	{
		$i = $this->getTotalingresos($id, $color_id, $lote, $calidad);
		$s = $this->getTotalsalidas($id, $color_id, $lote, $calidad);
		$TOTAL = ((float)$i - (float)$s);

		return $TOTAL;
	}

	public function getTotalingresos($id = 0, $color_id = 0, $lote = '', $calidad = '')
	{
		$fecha = date("Y-m-d");
		$a = $this->getIngresos_f($id, $color_id, $lote, $fecha, $calidad);
		$b = $this->getIngresos_g($id, $color_id, $lote, $fecha, $calidad);
		$c = $this->getIngresos_p($id, $color_id, $lote, $fecha, $calidad);
		return $a + $c + $b;
	}
	public function getIngresos_g($id = 0, $color_id = 0, $lote = '', $fecha = '', $calidad = '')
	{
		$t = 0;
		if ($id != 0) {
			$ingresos = Load::model('tesdetalleingresos');
			if ($color_id == 0) {
				$cond = 'tesdetalleingresos.tesproductos_id=' . $id;
			}
			if ($color_id != 0) {
				$cond = 'tesdetalleingresos.tescolores_id=' . $color_id;
				if ($id != 0) $cond .= ' AND tesdetalleingresos.tesproductos_id=' . $id;
			}
			if ($lote != '') {
				$cond = 'tesdetalleingresos.lote="' . $lote . '"';
				if ($color_id != 0) $cond .= ' AND tesdetalleingresos.tescolores_id=' . $color_id;
				if ($id != 0) $cond .= ' AND tesdetalleingresos.tesproductos_id=' . $id;
			}
			if ($calidad != '') {
				$cond = 'tesdetalleingresos.sc_calidades_id="' . $calidad . '"';
				if ($color_id != 0) $cond .= ' AND tesdetalleingresos.tescolores_id=' . $color_id;
				if ($id != 0) $cond .= ' AND tesdetalleingresos.tesproductos_id=' . $id;
			}

			/*echo 'aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond;*/
			$join = 'INNER JOIN tesingresos as i ON i.id = tesdetalleingresos.tesingresos_id AND (i.estadoingreso="PAGADO" OR i.estadoingreso="Pendiente" OR i.estadoingreso="INGRESO-CH") AND i.aclempresas_id=' . Session::get("EMPRESAS_ID");

			$di_g = $ingresos->sum('tesdetalleingresos.cantidad', 'conditions: !ISNULL(tesdetalleingresos.cantidad) 
AND tesdetalleingresos.cantidad >0 AND (ISNULL(tesdetalleingresos.prorollos_id) OR tesdetalleingresos.prorollos_id=0) AND ' . $cond, 'join: INNER JOIN tesingresos as i ON i.id = tesdetalleingresos.tesingresos_id AND (i.estadoingreso="PAGADO" OR i.estadoingreso="Pendiente" OR i.estadoingreso="INGRESO-CH" or i.estadoingreso="Con factura") AND i.aclempresas_id=' . Session::get("EMPRESAS_ID") . ' AND (i.tesdocumentos_id=15 OR i.tesdocumentos_id=27) AND i.femision<="' . $fecha . '"');
			/* PARA LAS TELAS SIN CORTE  AND r.estadorollo!="TERMINADO"*/
			$isc = $ingresos->find_by_sql('SELECT SUM( tesdetalleingresos.cantidad ) AS tsc FROM prorollos AS r, tesdetalleingresos INNER JOIN tesingresos AS i ON i.id = tesdetalleingresos.tesingresos_id AND (i.estadoingreso="Pendiente" OR i.estadoingreso="TERMINADO" OR i.estadoingreso="PAGADO") AND (i.tesdocumentos_id=15 OR i.tesdocumentos_id=27) AND i.femision<="' . $fecha . '" AND i.aclempresas_id=' . Session::get('EMPRESAS_ID') . ' WHERE (!ISNULL(tesdetalleingresos.prorollos_id)) AND r.id = tesdetalleingresos.prorollos_id AND ISNULL(r.prorollos_id) AND ' . $cond);
			//echo $isc->total.'inventario telas sin corte<br />';
			/* PARA LAS TELAS CON CORTE*/
			$icc = $ingresos->find_by_sql('SELECT SUM( tesdetalleingresos.cantidad ) AS tcc FROM prorollos AS r, tesdetalleingresos INNER JOIN tesingresos AS i ON i.id = tesdetalleingresos.tesingresos_id AND(i.estadoingreso =  "Pendiente" OR i.estadoingreso =  "TERMINADO" OR i.estadoingreso =  "PAGADO") AND (i.tesdocumentos_id=15 OR i.tesdocumentos_id=27) AND i.femision<="' . $fecha . '" AND i.aclempresas_id=' . Session::get('EMPRESAS_ID') . ' WHERE (!ISNULL(tesdetalleingresos.prorollos_id)) AND r.id = tesdetalleingresos.prorollos_id AND !ISNULL(r.prorollos_id) AND ' . $cond);

			(float)$t = (float)$di_g + $isc->tsc + (float)$icc->tcc;
		}
		//echo ''.$t.' Ingresos <br />';
		return $total = number_format((float)$t, 2, '.', '');
	}

	public function getIngresos_f($id = 0, $color_id = 0, $lote = '', $fecha = '', $calidad = '')
	{
		$t = 0;
		if ($id != 0) {
			$ingresos = Load::model('tesdetalleingresos');
			if ($color_id == 0) {
				$cond = 'tesdetalleingresos.tesproductos_id=' . $id;
			}
			if ($color_id != 0) {
				$cond = 'tesdetalleingresos.tescolores_id=' . $color_id;
				if ($id != 0) $cond .= ' AND tesdetalleingresos.tesproductos_id=' . $id;
			}
			if ($lote != '') {
				$cond = 'tesdetalleingresos.lote="' . $lote . '"';
				if ($color_id != 0) $cond .= ' AND tesdetalleingresos.tescolores_id=' . $color_id;
				if ($id != 0) $cond .= ' AND tesdetalleingresos.tesproductos_id=' . $id;
			}
			if ($calidad != '') {
				$cond = 'tesdetalleingresos.sc_calidades_id="' . $calidad . '"';
				if ($color_id != 0) $cond .= ' AND tesdetalleingresos.tescolores_id=' . $color_id;
				if ($id != 0) $cond .= ' AND tesdetalleingresos.tesproductos_id=' . $id;
			}

			/*echo 'aclempresas_id='.Session::get("EMPRESAS_ID").' AND '.$cond;*/
			$join = 'INNER JOIN tesingresos as i ON i.id = tesdetalleingresos.tesingresos_id AND (i.estadoingreso="PAGADO" OR i.estadoingreso="Pendiente" OR i.estadoingreso="INGRESO-CH") AND i.aclempresas_id=' . Session::get("EMPRESAS_ID");
			$di_f = 0;
			$di_f = $ingresos->sum('tesdetalleingresos.cantidad', 'conditions: !ISNULL(tesdetalleingresos.cantidad) 
AND tesdetalleingresos.cantidad >0 AND (ISNULL(tesdetalleingresos.prorollos_id) OR tesdetalleingresos.prorollos_id=0) AND tesdetalleingresos.aclempresas_id=' . Session::get("EMPRESAS_ID") . ' AND ' . $cond, 'join: ' . $join . ' AND tesdocumentos_id=7 AND isnull(numeroguia) AND i.femision<="' . $fecha . '"');

			(float)$t = (float)$di_f;
		}
		//echo ''.$t.' Ingresos <br />';
		return $total = number_format((float)$t, 2, '.', '');
	}
	public function getIngresos_p($id = 0, $color_id = 0, $lote = '', $fecha = '', $calidad = '')
	{
		$t = 0;
		if ($id != 0) {
			$pedidos = Load::model('pronotapedidos');
			if ($color_id == 0) {
				$condp = 'prodetallepedidos.tesproductos_id=' . $id;
			}
			if ($color_id != 0) {
				$condp = 'prodetallepedidos.tescolores_id=' . $color_id;
				if ($id != 0) $condp .= ' AND prodetallepedidos.tesproductos_id=' . $id;
			}
			if ($lote != '') {
				$condp = 'prodetallepedidos.lote="' . $lote . '"';
				if ($color_id != 0) $condp .= ' AND prodetallepedidos.tescolores_id=' . $color_id;
				if ($id != 0) $condp .= ' AND prodetallepedidos.tesproductos_id=' . $id;
			}
			if ($calidad != '') {
				$condp = 'prodetallepedidos.sc_calidades_id="' . $calidad . '"';
				if ($color_id != 0) $condp .= ' AND prodetallepedidos.tescolores_id=' . $color_id;
				if ($id != 0) $condp .= ' AND prodetallepedidos.tesproductos_id=' . $id;
			}
			/*peidos sin rollos*/
			$pi = $pedidos->find_by_sql(
				'SELECT SUM(prodetallepedidos.cantidad) as t FROM pronotapedidos INNER JOIN prodetallepedidos ON pronotapedidos.id=prodetallepedidos.pronotapedidos_id AND pronotapedidos.aclempresas_id=' . Session::get("EMPRESAS_ID") . ' WHERE (pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado") AND pronotapedidos.aclempresas_id=' . Session::get('EMPRESAS_ID') . ' AND (ISNULL(prodetallepedidos.prorollos_id) OR prodetallepedidos.prorollos_id="0") AND pronotapedidos.fecha_at<="' . $fecha . '" AND pronotapedidos.tipo="ingreso" AND ' . $condp . ' GROUP BY prodetallepedidos.tesproductos_id'
			);
			/*PEDIDO SIN CORTE*/
			$pisc = $pedidos->find_by_sql(
				'SELECT SUM(prodetallepedidos.cantidad) as ts FROM prorollos as r, pronotapedidos INNER JOIN prodetallepedidos ON pronotapedidos.id=prodetallepedidos.pronotapedidos_id WHERE (pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado") AND pronotapedidos.fecha_at<="' . $fecha . '" AND !ISNULL(prodetallepedidos.prorollos_id) AND pronotapedidos.aclempresas_id=' . Session::get('EMPRESAS_ID') . ' AND r.id=prodetallepedidos.prorollos_id AND ISNULL(r.prorollos_id) AND pronotapedidos.tipo="ingreso" AND ' . $condp . ' GROUP BY prodetallepedidos.tesproductos_id'
			);
			/*PEDIDO CON CORTE*/
			$picc = $pedidos->find_by_sql('SELECT SUM(prodetallepedidos.cantidad) as tc FROM prorollos as r, pronotapedidos INNER JOIN prodetallepedidos ON pronotapedidos.id=prodetallepedidos.pronotapedidos_id WHERE (pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado") AND pronotapedidos.fecha_at<="' . $fecha . '" AND !ISNULL(prodetallepedidos.prorollos_id) AND pronotapedidos.aclempresas_id=' . Session::get('EMPRESAS_ID') . ' AND r.id=prodetallepedidos.prorollos_id AND !ISNULL(r.prorollos_id) AND pronotapedidos.tipo="ingreso" AND ' . $condp . ' GROUP BY prodetallepedidos.tesproductos_id');

			(float)$t = $pi->t + (float)$pisc->ts + (float)$picc->tc;
		}
		//echo ''.$t.' Ingresos <br />';
		return $total = number_format((float)$t, 2, '.', '');
	}

	public function getTotalsalidas($id = 0, $color_id = 0, $lote = '', $fecha = '', $calidad = '')
	{
		$fecha = date("Y-m-d");
		$a = $this->getSalidas_g($id, $color_id, $lote, $fecha, $calidad);
		$b = $this->getSalidas_p($id, $color_id, $lote, $fecha, $calidad);
		$c = $this->getAutosalidas($id, $color_id, $lote, $fecha, $calidad);
		$d = $this->getSalidas_internas($id, $color_id, $lote, $fecha, $calidad);
		$t = $a + $b + $c + $d;
		/*if($color_id==1){
	echo $color_id." SALIDAS G: ".$a;
	echo " <br />P: ".$b;
	echo " <br />A: ".$c;
	echo " <br />I: ".$d;
	echo "<br />";
	}*/
		return $total = number_format((float)$t, 2, '.', '');
	}

	public function getSalidas_g($id = 0, $color_id = 0, $lote = '', $fecha = '', $calidad = '')
	{
		$t = 0;
		if ($id != 0) {
			$salidas = Load::model('tesdetallesalidas');
			if ($color_id == 0) {
				$cond = 'tesdetallesalidas.tesproductos_id=' . $id;
			}
			if ($color_id != 0) {
				$cond = 'tesdetallesalidas.tescolores_id=' . $color_id;
				if ($id != 0) $cond .= ' AND tesdetallesalidas.tesproductos_id=' . $id;
			}
			if ($lote != '') {
				$cond = 'tesdetallesalidas.lote="' . $lote . '"';
				if ($id != 0) $cond .= ' AND tesdetallesalidas.tesproductos_id=' . $id;
				if ($color_id != 0) $cond .= ' AND tesdetallesalidas.tescolores_id=' . $color_id;
			}
			if ($calidad != '') {
				$cond = 'tesdetallesalidas.sc_calidades_id="' . $calidad . '"';
				if ($id != 0) $cond .= ' AND tesdetallesalidas.tesproductos_id=' . $id;
				if ($color_id != 0) $cond .= ' AND tesdetallesalidas.tescolores_id=' . $color_id;
			}
			$join_S = 'INNER JOIN tessalidas as s ON s.id=tesdetallesalidas.tessalidas_id AND (s.estadosalida="Pendiente" or s.estadosalida="TERMINADO" or s.estadosalida="PAGADO" or s.estadosalida="Pagado" or s.estadosalida="Enviado") AND s.tesdocumentos_id=15 AND s.aclempresas_id=' . Session::get('EMPRESAS_ID');
			/*salidas de todo menos de telas*/
			$di = $salidas->sum('tesdetallesalidas.cantidad', 'conditions: ISNULL(tesdetallesalidas.prorollos_id) AND tesdetallesalidas.aclempresas_id=' . Session::get("EMPRESAS_ID") . ' AND ' . $cond, 'join: ' . $join_S . ' AND s.femision<="' . $fecha . '"');
			/*PARA LAS TELAS SIN CORTE  AND r.estadorollo!="TERMINADO"*/
			$tsc = $salidas->find_by_sql('SELECT SUM( tesdetallesalidas.cantidad ) AS tsc FROM prorollos AS r, tesdetallesalidas INNER JOIN tessalidas AS s ON s.id = tesdetallesalidas.tessalidas_id AND s.estadosalida!="Editable" AND s.estadosalida!="Con factura" AND s.tesdocumentos_id=15 AND s.aclempresas_id=' . Session::get('EMPRESAS_ID') . ' AND s.femision<="' . $fecha . '" WHERE !ISNULL(tesdetallesalidas.prorollos_id) AND r.id = tesdetallesalidas.prorollos_id AND ISNULL(r.prorollos_id) AND ' . $cond);
			//echo "telasincorte ".$tsc->tsc.'<br />';
			/* PARA LAS TELAS CON CORTE*/
			$tcc = $salidas->find_by_sql('SELECT SUM(tesdetallesalidas.cantidad) AS tcc FROM prorollos AS r, tesdetallesalidas INNER JOIN tessalidas AS s ON s.id = tesdetallesalidas.tessalidas_id AND s.estadosalida!="Editable" AND s.estadosalida!="Con factura"  AND s.tesdocumentos_id=15 AND s.aclempresas_id=' . Session::get('EMPRESAS_ID') . ' AND s.femision<="' . $fecha . '" WHERE !ISNULL(tesdetallesalidas.prorollos_id) AND r.id = tesdetallesalidas.prorollos_id AND !ISNULL(r.prorollos_id) AND ' . $cond);
			/*FIN PARA TELAS*/
			$t = (float)$di + (float)$tsc->tsc + (float)$tcc->tcc;
		}
		return $total = number_format((float)$t, 2, '.', '');
	}
	public function getSalidas_p($id = 0, $color_id = 0, $lote = '', $fecha = '', $calidad = '')
	{
		$t = 0;
		if ($id != 0) {
			$pedidos = Load::model('pronotapedidos');
			if ($color_id == 0) {
				$condp = 'prodetallepedidos.tesproductos_id=' . $id;
			}
			if ($color_id != 0) {
				$condp = 'prodetallepedidos.tescolores_id=' . $color_id;
				if ($id != 0) $condp .= ' AND prodetallepedidos.tesproductos_id=' . $id;
			}
			if ($lote != '') {
				$condp = 'prodetallepedidos.lote="' . $lote . '"';
				if ($id != 0) $condp .= ' AND prodetallepedidos.tesproductos_id=' . $id;
				if ($color_id != 0) $condp .= ' AND prodetallepedidos.tescolores_id=' . $color_id;
			}
			if ($calidad != '') {
				$condp = 'prodetallepedidos.sc_calidades_id="' . $calidad . '"';
				if ($id != 0) $condp .= ' AND prodetallepedidos.tesproductos_id=' . $id;
				if ($color_id != 0) $condp .= ' AND prodetallepedidos.tescolores_id=' . $color_id;
			}
			$sql = 'SELECT ifnull(sum(c.peso),0) as total FROM pronotapedidos , procajastrama as c INNER JOIN prodetallepedidos ON prodetallepedidos.id=c.prodetallepedidos_id
WHERE pronotapedidos.id=prodetallepedidos.pronotapedidos_id AND (pronotapedidos.estadonota="Sin enviar" or pronotapedidos.estadonota="Ingresado" or pronotapedidos.estadonota="Entregado" or pronotapedidos.estadonota="Revisado" or pronotapedidos.estadonota="Pendiente") AND pronotapedidos.aclempresas_id=' . Session::get('EMPRESAS_ID') . ' AND pronotapedidos.fecha<="' . $fecha . '" AND prodetallepedidos.tescajas_id>="1" AND ISNULL(prodetallepedidos.prorollos_id) AND pronotapedidos.tipo="salida" AND ' . $condp . ' GROUP BY prodetallepedidos.tesproductos_id';
			$pi = $pedidos->find_by_sql($sql);
			//echo $sql;


			$t = (float)$pi->total;
		}
		/*echo $t;echo "</ br>";*/
		return $total = number_format((float)$t, 2, '.', '');
	}
	public function getAutosalidas($id = 0, $color_id = 0, $lote = '', $fecha = '', $calidad = '')
	{
		$t = 0;
		if ($id != 0) {
			$autosalida = Load::model('tesdetalleingresos');
			if ($color_id == 0) {
				$cond_auto = 'tesdetalleingresos.tesproductos_id=' . $id;
			}
			if ($color_id != 0) {
				$cond_auto = 'tesdetalleingresos.tescolores_id=' . $color_id;
				if ($id != 0) $cond_auto .= ' AND tesdetalleingresos.tesproductos_id=' . $id;/* Esto para las Auto Salidas solo el stock;*/
			}
			if ($lote != '') {
				$cond_auto = 'tesdetalleingresos.lote="' . $lote . '"';
				if ($id != 0) $cond_auto .= ' AND tesdetalleingresos.tesproductos_id=' . $id;
				if ($color_id != 0) $cond_auto .= ' AND tesdetalleingresos.tescolores_id=' . $color_id;
			}
			if ($calidad != '') {
				/* Esto para las Auto Salidas solo el stock;*/
				$cond_auto = 'tesdetalleingresos.sc_calidades_id="' . $calidad . '"';
				if ($id != 0) $cond_auto .= ' AND tesdetalleingresos.tesproductos_id=' . $id;
				if ($color_id != 0) $cond_auto .= ' AND tesdetalleingresos.tescolores_id=' . $color_id;
			}
			$join = 'INNER JOIN tesingresos as i ON i.id = tesdetalleingresos.tesingresos_id AND  i.autosalida="1" ';
			$di_auto = $autosalida->sum('tesdetalleingresos.cantidad', 'conditions: tesdetalleingresos.aclempresas_id=' . Session::get("EMPRESAS_ID") . ' AND ' . $cond_auto, 'join: ' . $join);
			$t = (float)$di_auto;
		}
		/*echo 'SALIDAS  ->'.$di.'<br />';
	echo 'SALIDAS INTERNAS ->'.$sdi.'<br />';
	echo 'cajasTrama='.$pi->total;*/

		return $total = number_format((float)$t, 2, '.', '');
	}
	public function getSalidas_internas($id = 0, $color_id = 0, $lote = '', $fecha = '', $calidad = '')
	{
		$t = 0;
		if ($id != 0) {
			$salidasinternas = Load::model('tesdetallesalidasinternas');
			if ($color_id == 0) {
				$cond_i = 'tesdetallesalidasinternas.tesproductos_id=' . $id;
			}
			if ($color_id != 0) {
				$cond_i = 'tesdetallesalidasinternas.tescolores_id=' . $color_id;
				if ($id != 0) $cond_i .= ' AND tesdetallesalidasinternas.tesproductos_id=' . $id;
			}
			if ($lote != '') {
				$cond_i = 'tesdetallesalidasinternas.lote="' . $lote . '"';
				if ($id != 0) $cond_i .= ' AND tesdetallesalidasinternas.tesproductos_id=' . $id;
				if ($color_id != 0) $cond_i .= ' AND tesdetallesalidasinternas.tescolores_id=' . $color_id;
			}
			if ($calidad != '') {
				$cond_i = 'tesdetallesalidasinternas.sc_calidades_id="' . $calidad . '"';
				if ($id != 0) $cond_i .= ' AND tesdetallesalidasinternas.tesproductos_id=' . $id;
				if ($color_id != 0) $cond_i .= ' AND tesdetallesalidasinternas.tescolores_id=' . $color_id;
			}
			$join_S_I = 'INNER JOIN tessalidasinternas as s_i ON s_i.id=tesdetallesalidasinternas.tessalidasinternas_id AND (s_i.estadosalida="TERMINADO" OR s_i.estadosalida="Pendiente" or s_i.estadosalida="Enviado" or s_i.estadosalida="PAGADO") AND s_i.aclempresas_id=' . Session::get('EMPRESAS_ID');
			$sdi = $salidasinternas->sum('tesdetallesalidasinternas.cantidad', 'conditions: ISNULL(tesdetallesalidasinternas.prorollos_id) AND ' . $cond_i, 'join: ' . $join_S_I . ' AND s_i.femision<="' . $fecha . '"');

			$t = (float)$sdi;
		}
		return $total = number_format((float)$t, 2, '.', '');
	}





	public function getProduccion_sc($id = 0, $color_id = 0, $lotes = '')
	{
		/*PARA SALIDA A PRODUCCION DE productos terminados (Santa Carmela)
	$PRSC=$this->getProduccion_sc($id,$color_id,$lote);*/
		$total = 0;
		$color = '';
		if ($color_id != 0) $color = " AND LOCATE('" . $color_id . "', sc_urdidos.colores_id )";
		$lote = '';
		if ($lotes != '') $lote = " AND LOCATE('" . $lotes . "', sc_totalcajas.lotes)";
		$q = $this->find_by_sql("SELECT IFNULL( SUM( totalkilos ) , 0 ) as total , IFNULL( SUM( totalkilos ) / IF((
LENGTH(colores_id)-LENGTH(REPLACE(colores_id,',','')))=0,1,(
LENGTH(colores_id)-LENGTH(REPLACE(colores_id,',','' )))+1),0) AS pp
FROM sc_urdidos, sc_totalcajas
WHERE sc_urdidos.sc_totalcajas_id = sc_totalcajas.id
AND sc_urdidos.tesproductos_id=" . $id . $color . $lote);

		if ($color_id == 0) {
			$total = $q->total;
		} else {
			$total = $q->pp;
		}

		return $total;
	}



	public function getStockTransformacion($id = 0)
	{
		$t = 0;
		if ($id != 0) {
			$ingresos = Load::model('tesdelleingresos');
			$pedidos = Load::model('prodetallepedidos');
			$di = $ingresos->count('tesproductos_id=' . $id);
			$pi = $pedidos->count('tesproductos_id=' . $id);
			(float)$t = (float)$di + (float)$pi;
		}
		return $total = (float)$t;
	}
	/*
Busqueda de productos por tipo 
recibe la linea de produccion = $id
*/
	public function getProductos_tipo($q, $id)
	{
		$hilo = '';
		if ($id != 0) {
			$hilo = '(' . $this->obtener_tipo_productos($id) . ') AND ';
		}
		return $this->find('conditions: ' . $hilo . 'estado=1 and aclempresas_id=' . Session::get('EMPRESAS_ID') . ' and CONCAT_WS(" ",nombre,detalle,codigo,codigo_ant) like "%' . $q . '%"');
	}
	protected function obtener_tipo_productos($id)/*recibe el id de la familia de productos*/
	{
		$obj = new Testipoproductos();
		$tipos = $obj->find('conditions: estado=1 AND activo=1 AND teslineaproductos_id=' . $id);
		$tipos_pro = array();
		foreach ($tipos as $e) {
			$tipos_pro[] = "testipoproductos_id = '$e->id'";
		}
		return join(' OR ', $tipos_pro);
	}
	/*Productos en Formato json por tipo de productos
# @id = el id del tipo de producto;
*/
	/*getHilos_json recibe el id de la linea de producto*/
	public function getLineap_json($id)
	{
		$array_producto = '';
		$hilo = '';
		if ($id != 0) {
			$hilo = '(' . $this->obtener_tipo_productos($id) . ') AND ';
		}
		$productos = $this->find('conditions: ' . $hilo . 'estado=1 and aclempresas_id=' . Session::get('EMPRESAS_ID'));

		$n = 0;
		foreach ($productos as $producto) :
			$i_coma = ',';
			if ($n == 0) {
				$i_coma = '';
			}
			$n++;
			$valor = preg_replace('/\"/', '', $producto->nombrecorto);
			$array_producto .= $i_coma . '{id: ' . (int)$producto->id . ', name: "' . $valor . '"}';
		endforeach;
		return $array_producto;
	}
	/*LISTA POR TIPO DE DE PRODUCTOS*/
	public function getProductos_json($id)
	{
		$array_producto = '';
		$productos = $this->find('columns: id,detalle,nombrecorto', 'conditions: testipoproductos_id=' . $id . ' AND aclempresas_id=' . Session::get('EMPRESAS_ID'));
		$n = 0;
		foreach ($productos as $producto) :
			$i_coma = ',';
			if ($n == 0) {
				$i_coma = '';
			}
			$n++;
			$valor = preg_replace('/\"/', '', $producto->nombrecorto);
			$array_producto .= $i_coma . '{id: ' . (int)$producto->id . ', name: "' . $valor . '"}';
		endforeach;
		return $array_producto;
	}


	/*Colores en Formato Json para busquedas*/
	public function getColores_json()
	{
		$array_color = '';
		$colores = Load::model('tescolores')->find('conditions: aclempresas_id=' . Session::get('EMPRESAS_ID'));
		$n = 0;
		foreach ($colores as $color) :
			$i_coma = ',';
			if ($n == 0) {
				$i_coma = '';
			}
			$n++;
			$array_color .= $i_coma . '{id: ' . (string)$color->id . ', name: "' . (string)$color->nombre . '"}';
		endforeach;
		return $array_color;
	}
	public function getLotes_json($id)
	{
		//echo'SELECT d.lote as lote FROM tesdetalleingresos as d, tesproductos as p WHERE p.id=d.tesproductos_id AND p.testipoproductos_id='.$id.' AND d.aclempresas_id='.Session::get('EMPRESAS_ID');
		$array_lote = '';
		$lotes = $this->find_all_by_sql('SELECT d.lote as lote,d.tesproductos_id as tesproductos_id FROM tesdetalleingresos as d, tesproductos as p WHERE p.id=d.tesproductos_id AND p.testipoproductos_id=' . $id . ' AND d.aclempresas_id=' . Session::get('EMPRESAS_ID'));
		$n = 0;
		$l = 0;
		foreach ($lotes as $lote) :
			$i_coma = ',';
			if ($n == 0) {
				$i_coma = '';
			}
			$n++;
			if ($l != $lote->lote) $array_lote .= $i_coma . '{id: "' . $lote->lote . '", name: "' . (string)$lote->lote . ' ' . self::getProducto($lote->tesproductos_id) . '"}';
			$l = $lote->lote;
		endforeach;
		return $array_lote;
	}

	public function getLotesXfechas_json()
	{
		//echo'SELECT d.lote as lote FROM tesdetalleingresos as d, tesproductos as p WHERE p.id=d.tesproductos_id AND p.testipoproductos_id='.$id.' AND d.aclempresas_id='.Session::get('EMPRESAS_ID');
		$array_lote = '';
		$lotes = $this->find_all_by_sql('SELECT i.id as idingreso,i.femision as fecha,IFNULL(d.lote,"SIN LOTE") as lote,d.tesproductos_id as tesproductos_id, p.nombrecorto as nombrecorto FROM tesproductos as p ,testipoproductos as tp,teslineaproductos as ln, tesdetalleingresos as d LEFT JOIN tesingresos as i ON i.id=d.tesingresos_id
WHERE p.id=d.tesproductos_id AND d.aclempresas_id=1 AND p.testipoproductos_id=tp.id AND tp.teslineaproductos_id=ln.id AND ln.id=3 GROUP BY d.lote,i.femision,d.tesproductos_id');
		$n = 0;
		foreach ($lotes as $lote) :
			$i_coma = ',';
			if ($n == 0) {
				$i_coma = '';
			}
			$n++;
			$array_lote .= $i_coma . '{id: "' . $lote->lote . '-' . $lote->tesproductos_id . '", name: "Lote=>' . (string)$lote->lote . ' Hilo=>' . (string)$lote->nombrecorto . '(fecha=>' . $lote->fecha . ') "}';

		endforeach;
		return $array_lote;
	}



	private function getProducto($id)
	{
		$p = $this->find_first('columns: nombrecorto', 'conditions: id=' . (int)$id);
		return $p->nombrecorto;
	}
	/*saldo de los productos*/
	public function getSaldo($producto, $color = 0, $lote = 0, $fecha)
	{
		$c = 0;
		$hh = '';
		if ($lote != 0) {
			$ingresos = Load::model('tesdetalleingresos');
			if ($ingresos->exists('tesproductos_id=' . $producto . ' AND tescolores_id=' . $color . ' AND lote=' . $lote)) {
				$ingresos->find_first('columns: preciounitario', 'conditions: tesproductos_id=' . $producto . ' AND tescolores_id=' . $color . ' AND lote=' . $lote);
				$a = $this->getTotalingresos($producto, $color, $lote, $fecha);
				$b = $this->getTotalsalidas($producto, $color, $lote, $fecha);
				$c = (float)$a - (float)$b;
				$hh = $c . '-' . $ingresos->preciounitario;
			} else {
				$hh = $c . '-0';
			}
			return $hh;
		} else {
			$a = $this->getTotalingresos($producto, $color, 0, $fecha);
			$b = $this->getTotalsalidas($producto, $color, 0, $fecha);
			(float)$c = (float)$a - (float)$b;
			return (float)$c;
		}
	}
}
