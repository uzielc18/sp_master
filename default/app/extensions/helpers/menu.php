<?php

class Menu
{
	/**
	 * Constante que define que solo va a mostrar los
	 * Items del menu para app
	 */
	const APP = 1;

	/**
	 * Constante que define que solo va a mostrar los
	 * Items del menu para el backend
	 */
	const BACKEND = 2;
	/**
	 * Constante que define que solo va a mostrar los
	 * Items del menu para el santa patricia
	 */
	const SPATRICIA = 0;
	/**
	 * Constante que define que solo va a mostrar los
	 * Items del menu para el santa carmela
	 */
	const SCARMELA = 4;
	/**
	 * Id del rol del usuario logueado actualmente
	 *
	 * @var int 
	 */


	protected static $_id_rol = NULL;

	public static function render($id_rol, $entorno = self::BACKEND, $ubicacion, $attr = '')
	{
		self::$_id_rol = $id_rol;
		$rL = new Aclmenus();
		$registros = $rL->obtener_menu_por_rol($id_rol, $entorno, $ubicacion);
		$html = '';
		//$html.='<pre>'.print_r($registros).'</pre>';
		if ($registros) {
			$html .= '<ul ' . $attr . '>' . PHP_EOL;
			foreach ($registros as $key => $e) {
				$html .= self::generarItems($key, $e, $entorno, $ubicacion);
			}
			$html .= '</ul>' . PHP_EOL;
		}
		return $html;
	}

	protected static function generarItems($key, $objeto_menu, $entorno, $ubicacion)
	{
		$sub_menu = $objeto_menu->get_sub_menus(self::$_id_rol, $entorno, $ubicacion);
		//$class = 'dropdown menu_' . str_replace('/', '_', $objeto_menu->url);
		$class = '';
		if ($ubicacion == 1) $class = 'nav-item';
		if ($ubicacion == 3) $class = 'nav-item';
		$span_clas = ' class="nav-icon ' . $objeto_menu->clases . '"';

		$html = '';
		if ($ubicacion == 1 and $key == 0) {
			$html .= '<li class="nav-item">
						<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
					</li>';
			$span_clas = '';
		}
		if ($sub_menu) {
			$caret = '<i class="right fas fa-angle-left"></i>';
			$a_class = ' class="nav-link"';
			if ($ubicacion == 1) {
				$caret = '';
				$a_class = 'id="dropdownSubMenu' . $key . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle"';
				$span_clas = '';

				$html .= "<li class='{$class} dropdown'>" .
					'<a href="#"' . $a_class . '>' . '' . $objeto_menu->nombre . $caret . '</a>' . PHP_EOL;
			} else {

				$html .= "<li class='{$class} dropdown'>" .
					'<a href="#"' . $a_class . '>' . '<i' . $span_clas . '></i><p>' . $objeto_menu->nombre . $caret . '</p></a>' . PHP_EOL;
			}
		} else {
			$html = '';

			$html .= "<li class='{$class}'>" .
				Html::link($objeto_menu->url, '<i' . $span_clas . '></i> <p>' . $objeto_menu->nombre . '</p>', 'class="nav-link"') . PHP_EOL;
		}
		if ($sub_menu) {
			$clas = '';
			if ($ubicacion == 1) {
				$clas = ' aria-labelledby="dropdownSubMenu' . $key . '" class="dropdown-menu border-0 shadow"';
			}
			if ($ubicacion == 3) {
				$clas = ' class="nav nav-treeview"';
			}
			$html .= '<ul' . $clas . '>' . PHP_EOL;
			foreach ($sub_menu as $e) {
				$html .= self::generarItems(1, $e, $entorno, $ubicacion);
			}
			$html .= '</ul>' . PHP_EOL;
		}
		return $html . "</li>" . PHP_EOL;
	}

	protected static function es_activa($url)
	{
		$url_actual = substr(Router::get('route'), 1);
		return (strpos($url, $url_actual) !== false || strpos($url, "$url_actual/index") !== false);
	}

	public static function menuPedido_Cliente($estado = '')
	{
		$code = "";
		$class = '';
		if ($estado == 'ing') $class = 'btn-success';
		$code .= ' ' . Html::link('santapatricia/ordendecompra/lis_externo/ing', 'Gerencia General', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($estado == 'dis') $class = 'btn-success';
		$code .= ' ' . Html::link('santapatricia/ordendecompra/lis_externo/dis', 'Diseño', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($estado == 'alm') $class = 'btn-success';
		$code .= ' ' . Html::link('santapatricia/ordendecompra/lis_externo/alm', 'Almacen', 'class="btn btn-xs ' . $class . '"');
		$code .= ' ' . Html::link('santapatricia/ordendecompra/reportes', 'Buscar pedidos anteriores', 'class="btn btn-xs ' . $class . '"');


		return $code;
	}


	public static function menuProduccion($action = '')
	{
		$code = "
		
		";
		$code .= '<div id="active">';
		$class = '';
		if ($action == 'index') $class = 'btn-success';
		$code .= Html::link('santapatricia/revisiones/', 'Rollos, cortados de Mq.', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'control') $class = 'btn-success';
		$code .= Html::link('santapatricia/revisiones/control', 'Rollos en proceso de REVISION', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'telacruda') $class = 'btn-success';
		$code .= Html::link('santapatricia/revisiones/telacruda', 'Rollos, sin procesos', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'Rollos') $class = 'btn-success';
		$code .= Html::link('santapatricia/rollos/', 'Ingreso de Rollos (INVENTARIO)', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'listado_articulo') $class = 'btn-success';
		$code .= Html::link('santapatricia/revisiones/listado_articulo', 'Listado por Articulo', 'class="btn btn-xs ' . $class . '"');
		$code .= '</div>';
		return $code;
	}
	public static function menuControl($action = '')
	{
		$code = "";
		$class = '';
		//echo $action;
		$code .= '';
		if ($action == 'listado') $class = 'btn-success';
		$code .= Html::link("santapatricia/rollos/listado", 'Ingresar Rollos', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'salidas') $class = 'btn-success';
		$code .= Html::link("santapatricia/tintoreria/salidas", 'Guias Generadas', 'class="btn btn-xs ' . $class . '"');
		$class = '';

		if ($action == 'control') $class = 'btn-success';
		$code .= Html::link('santapatricia/tintoreria/control', 'Rollos C.C./V', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'rollos_tintoreria') $class = 'btn-success';
		$code .= Html::link('santapatricia/tintoreria/rollos_tintoreria', 'Rollos en Tintoreria', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'rollos_reprocesos') $class = 'btn-success';
		$code .= Html::link('santapatricia/tintoreria/rollos_reprocesos', 'Rollos en Reproceso', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'rollos_venta') $class = 'btn-success';
		$code .= Html::link('santapatricia/tintoreria/rollos_venta', 'Rollos P/V', 'class="btn btn-xs ' . $class . '"');
		$code .= '';
		return $code;
	}
	public static function menuLetras($action)
	{
		$code = "";
		$class = '';
		//echo $action;
		$code .= '';
		if ($action == 'index') $class = 'btn-success';
		$code .= Html::linkAction("", 'Emitidas', 'class="btn btn-xs ' . $class . '"');
		$class = '';

		if ($action == 'aceptadas') $class = 'btn-success';
		$code .= Html::linkAction("aceptadas", 'Aceptadas', 'class="btn btn-xs ' . $class . '"');
		$class = '';

		if ($action == 'letrasbancos') $class = 'btn-success';
		$code .= Html::linkAction('letrasbancos', 'Con Nro. Unico', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'letras_pro') $class = 'btn-success';
		$code .= Html::linkAction('letras_pro', 'Vencidas', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'letras_protestadas') $class = 'btn-success';
		$code .= Html::linkAction('letras_protestadas', 'Protestadas', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'letrasporfecha') $class = 'btn-success';
		$code .= Html::linkAction('letrasporfecha', 'Pendientes', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'letrasmes') $class = 'btn-success';
		$code .= Html::linkAction('letrasmes/' . date('Y/m'), 'Vcto por Mes', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'crear') $class = 'btn-success';
		$code .= Html::linkAction('crear/', 'Crear', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'internas') $class = 'btn-success';
		$code .= Html::linkAction('internas', 'Detalles', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'letraspagadas') $class = 'btn-success';
		$code .= Html::linkAction('letraspagadas/' . date('Y/m'), 'Pagado', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		$code .= Html::linkAction("", 'Volver ', 'class="btn"');
		//$code.=Html::link('santapatricia/tintoreria/rollos_venta','Rollos para venta','class="btn"');
		$code .= '';
		return $code;
	}
	public static function menuVentas($action = '')
	{
		$code = "";
		$code .= '';
		$class = '';
		if ($action == 'index') $class = 'btn-success';
		$code .= Html::linkAction("", 'Facturas Enitidas!', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'selector' or $action == 'facturas' or $action == 'factura_adelanto' or $action == 'factura_adelanto_detalle' or $action == 'versalida_adelanto') $class = 'btn-success';
		$code .= Html::linkAction("selector", 'Facturacion!', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'listado_servicio' AND Session::get('DOC_ID')=='7') $class = 'btn-success';
		$code .= Html::linkAction("cargar_doc/7/listado_servicio", 'Facturas de servicio', 'class="btn btn-xs ' . $class . '" title="Crear Factura de Servicio"');
		$class = '';
		if($action == 'listado_servicio' AND Session::get('DOC_ID')=='15') $class = 'btn-success';
		$code .=Html::linkAction("cargar_doc/15/listado_servicio", 'Guias por servicios','class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'guias_mes') $class = 'btn-success';
		$code .= Html::linkAction("guias_mes", "Guias Emitidas", "class='btn btn-xs " . $class . "'");
		$class = '';
		if ($action == 'guias') $class = 'btn-success';
		$code .= Html::linkAction("guias/15", 'Guias de Remisiòn!', 'class="btn btn-xs ' . $class . '"');
		$class = '';

		if ($action == 'nota_creditos') $class = 'btn-success';
		$code .= Html::linkAction("cargar_doc/13/nota_creditos", 'Nota de Credito', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'nota_debitos') $class = 'btn-success';
		$code .= Html::linkAction("cargar_doc/14/nota_debitos", 'Nota de Debito', 'class="btn btn-xs ' . $class . '"');
		if ($action != 'index') $code .= Html::linkAction("", 'Volver ', 'class="btn btn-xs"');
		$code .= '';
		return $code;
	}
	public static function menuVentasP($action = '')
	{
		$code = "";
		$code .= '';
		$class = '';
		if ($action == 'index') $class = 'btn-success';
		$code .= Html::linkAction("", 'Facturas Emitidas!', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'guias') $class = 'btn-success';
		$code .= Html::linkAction("salidas/15", 'Guias de Remisiòn!', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'f_salidas') $class = 'btn-success';
		$code .= Html::linkAction("f_salidas/7", 'Facturacion!', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'salidas_internas') $class = 'btn-success';
		/*if(Auth::get('id')==3 || Auth::get('id')==12)*/
		$code .= Html::linkAction("salidas_internas/", 'Salidas Internas!', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		/*if(Session::get('EMPRESAS_ID')==2){
		if($action=='nota_creditos')$class='btn-success';
		 $code.=Html::linkAction("cargar_doc/13/nota_creditos", 'Notas de Credito','class="btn btn-xs '.$class.'"');
		 $class='';
		if($action=='nota_debitos')$class='btn-success';
		 $code.=Html::linkAction("cargar_doc/14/nota_debitos", 'Notas de Debito','class="btn btn-xs '.$class.'"');
		}*/
		if ($action != 'index') $code .= Html::linkAction("", 'Volver ', 'class="btn"');
		$code .= '';
		return $code;
	}
	public static function menuReportes($action)
	{

		$code = '<script type="text/javascript">
$(document).ready(function()
{
$("select#repor").change(function(){
	$( ".page-header" ).after("<div style=\"position: absolute;padding-top: 10%;text-align: center;z-index: 1;top: 0;left: 0;right: 0;bottom: 0;\" align=\"center\"><img src=\"/img/ajax-loader1.gif\" /></div>");
	var U =window.location.pathname.split("/");
    location.href = "/"+U[1]+"/"+U[2]+"/"+$(this).val();
});
});
</script>
 <select id="repor">
 	<optgroup label="Consultas">
    <option value="">Inicio</option>
    <option value="movimientos_clientes">Ultimos Movimientos (Clientes)</option>
    <option value="reportesemanal">Reporte Semanal (Clientes)</option>
    <option value="reportediario">Reporte Diario</option>
    <option value="adelantos">Adelantos</option>
    <option value="impuestosmes">Impuestos por Mes</option>
    <option value="detracciones">Detraciones Mensuales!</option>
    <option value="valorado_muestras">Valorado de Muestras</option>
    <option value="reporte_letras_clientes">Letras Emitidas</option>
    <option value="reporte_letras_proveedores">Letras Aceptadas</option>
	</optgroup>
    <optgroup label="Ventas y compras">
    <option value="ventasmes">Ventas</option>
    <option value="venta_por_cliente">Facturas por Cobrar</option>
    <option value="comprasmes">Compras</option>
    <option value="compra_por_proveedor">Facturas por Pagar</option>
    </optgroup>';
		if (Session::get('EMPRESAS_ID') == 1) $code .= '
    <optgroup label="Varios ">
    <option value="guias_tintoreria">Guias en Tintoreria</option>
    <option value="listado_rollos">Rollos</option>
    <option value="hilourdido">Reporte de Urdido</option>
    <option value="hilos">Reporte de hilos</option>
    <option value="hiloanual">Compra de hilos por año</option>
    <option value="hilosemanal">Stok de hilos por semana</option>
    <option value="codigodebarras">Codigo de Barras de los rollos</option>
    </optgroup>';
		$code .= '
</select>';
		return $code;
	}

	public static function menuGraficos($action)
	{

		$code = '<script type="text/javascript">
$(document).ready(function()
{

$("select#repor").change(function(){
	var U =window.location.pathname.split("/");
	
    location.href = "/"+U[1]+"/"+U[2]+"/"+$(this).val();;
});
});
</script>
 <select id="repor">
 	<option value="">Inicio</option>
    <optgroup label="Ventas y compras">
    <option value="graficos_venta">Ventas Totales</option>
    </optgroup>
    <optgroup label="Varios ">
    <option value="graficos_eficiencia">Eficiencia</option>
    </optgroup>
</select>';
		return $code;
	}
	public static function menuCheques()
	{
		$code = "";

		$code .= ' ' . Html::linkAction('sinregistrar', 'Recepcionados', 'class="btn"');
		$code .= ' ' . Html::linkAction('pendientes', 'En Cartera', 'class="btn"');
		$code .= ' ' . Html::linkAction('extornados', 'Extornados', 'class="btn"');
		$code .= ' ' . Html::linkAction('index', 'Listado por Mes', 'class="btn"');



		return $code;
	}
	public static function menuCheques_emitidos()
	{
		$code = "";
		$code .= ' ' . Html::linkAction('index', 'Listado por Mes', 'class="btn"');
		$code .= ' ' . Html::linkAction('sinregistrar', 'Cheques Emitidos', 'class="btn"');
		$code .= ' ' . Html::linkAction('pendientes', 'Cheques Pendientes de Cobro', 'class="btn"');
		$code .= ' ' . Html::linkAction('chequesanulados', 'Cheques Anulados', 'class="btn"');



		return $code;
	}
	/*Menu de eficiencia*/
	public static function efe($action = '')
	{

		$code = '';
		$code .= '';
		$class = '';
		if ($action == 'index') $class = 'btn-success';
		$code .= Html::linkAction("", 'Por tejedor!', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'maquinas') $class = 'btn-success';
		$code .= Html::linkAction("maquinas", 'Por maquinas!', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'turnos') $class = 'btn-success';
		$code .= Html::linkAction("turnos", 'Por turnos!', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'produccion') $class = 'btn-success';
		$code .= Html::linkAction("produccion", 'Produccion!', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action == 'eficiencia') $class = 'btn-success';
		$code .= Html::linkAction("eficiencia", 'Eficiencia', 'class="btn btn-xs ' . $class . '"');
		$class = '';
		if ($action != 'index') {
			$code .= Html::linkAction("", 'Volver ', 'class="btn"');
		} else {
			$code .= Html::link("santapatricia/maquinas", 'Volver ', 'class="btn"');
		}
		$code .= '';
		return $code;
	}
}
