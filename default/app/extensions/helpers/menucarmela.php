<?php

class Menucarmela {

public static function menuPedido_Cliente($estado='')
	{
		$code='';
		$class='';
		if($estado=='ing')$class='btn-success';
		$code.=' '.Html::link('santacarmela/ordendecompra/lis_externo/ing','Gerencia General','class="btn '.$class.'"');
		$class='';
		if($estado=='dis')$class='btn-success';
		$code.=' '.Html::link('santacarmela/ordendecompra/lis_externo/dis','Diseño','class="btn '.$class.'"');
		$class='';
		if($estado=='alm')$class='btn-success';
		$code.=' '.Html::link('santacarmela/ordendecompra/lis_externo/alm','Almacen','class="btn '.$class.'"');
		
		
		return $code;
	}
	

public static function menuVentas($action='')
	{
		$code="		
		";
		
		$class='';
		if($action=='index')$class='btn-success';
		 $code.=Html::linkAction("", 'F. Adelantos/Servicio','class="btn '.$class.'"');
		$class='';
		//if($action=='guias')$class='btn-success';
		// $code.=Html::linkAction("guias/15", 'Guias de Remisiòn!','class="btn '.$class.'"');
		// $class='';
		if($action=='Facturacion')$class='btn-success';
		 $code.=Html::linkAction("selector", 'Facturacion!','class="btn '.$class.'"');
		 $class='';
		if($action=='nota_creditos')$class='btn-success';
		 $code.=Html::linkAction("cargar_doc/13/nota_creditos", 'Nota de Credito','class="btn '.$class.'"');
		 $class='';
		if($action=='nota_debitos')$class='btn-success';
		 $code.=Html::linkAction("cargar_doc/14/nota_debitos", 'Nota de Debito','class="btn '.$class.'"');
		 if($action!='index')$code.=Html::linkAction("", 'Volver ','class="btn"');
		
		return $code;
	}
	public static function menuVentasP($action='')
	{
		$code="
		<script type='text/javascript'>
		$(document).ready(function()
{
	/*Menu activo*/
  function setActive() {
  aObj = document.getElementById('active').getElementsByTagName('a');
  for(i=0;i<aObj.length;i++) { 
    if(document.location.href.indexOf(aObj[i].href)>=0) {
		var c=aObj[i].className;
      	if(c=='btn'){
		aObj[i].className=c+' btn-success active';
		}else
		{
		aObj[i].className=c+' active';
		}
		}
 	 }
	}
	window.onload = setActive;
});
		</script>
		";
		$code.='<div id="active">';
		$class='';
		if($action=='index')$class='btn-success';
		 $code.=Html::linkAction("", 'Facturas Emitidas!','class="btn '.$class.'"');
		$class='';
		if($action=='guias')$class='btn-success';
		 $code.=Html::linkAction("salidas/15", 'Guias de Remisiòn!','class="btn '.$class.'"');
		 $class='';
		if($action=='f_salidas')$class='btn-success';
		 $code.=Html::linkAction("f_salidas/7", 'Facturacion!','class="btn '.$class.'"');
		 $class='';
		 if($action=='salidas_internas')$class='btn-success';
		 if(Auth::get('id')==3 || Auth::get('id')==12)$code.=Html::linkAction("salidas_internas/", 'Salidas Internas!','class="btn '.$class.'"');
		 $class='';
		/*if($action=='nota_creditos')$class='btn-success';
		 $code.=Html::linkAction("cargar_doc/13/nota_creditos", 'Notas de Credito','class="btn '.$class.'"');
		 $class='';
		if($action=='nota_debitos')$class='btn-success';
		 $code.=Html::linkAction("cargar_doc/14/nota_debitos", 'Notas de Debito','class="btn '.$class.'"');*/
		 if($action!='index')$code.=Html::linkAction("", 'Volver ','class="btn"');
		 $code.='</div>';
		return $code;
	}
	
	public static function menuReportes($action)
	{

$code='<script type="text/javascript">
$(document).ready(function()
{

$("select#repor").change(function(){
	var U =window.location.pathname.split("/");
	$( ".page-header" ).after("<div style=\"position: absolute;padding-top: 10%;text-align: center;z-index: 1;top: 0;left: 0;right: 0;bottom: 0;\" align=\"center\"><img src=\"/img/ajax-loader1.gif\" /></div>");
    location.href = "/"+U[1]+"/"+U[2]+"/"+$(this).val();;
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
    </optgroup>
    <optgroup label="Ventas y compras">
    <option value="ventasmes">Ventas</option>
    <option value="venta_por_cliente">Facturas por Cobrar</option>
    <option value="comprasmes">Compras</option>
    <option value="compra_por_proveedor">Facturas por Pagar</option>
    </optgroup>
</select>';
	return $code;
}
	
public static function menuGraficos($action)
	{

$code='<script type="text/javascript">
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
	
}
/*$class="";
		//echo $action;
		$code='<div id="active">';
		if($action=='index')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction("", 'Inicio','class="btn '.$class.'"');
		$class='';
		if($action=='reportesemanal')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('reportesemanal','Reporte Semanal','class="btn '.$class.'"');
		$class='';
		if($action=='reportediario')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('reportediario','Reporte Diario','class="btn '.$class.'"');
		$class='';
		if($action=='adelantos')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('adelantos','Reporte de Adelantos','class="btn '.$class.'"');
		$class='';
		if($action=='ventasmes')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('ventasmes','Ventas X mes','class="btn '.$class.'"');
		$class='';
		if($action=='comprasmes')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('comprasmes','Compras X mes','class="btn '.$class.'"');
		$class='';
		if($action=='impuestosmes')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('impuestosmes/','Impuesto X mes','class="btn '.$class.'"');
		$class='';
		if($action=='listado_rollos')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('listado_rollos/','Listado de rollos','class="btn '.$class.'"');
		$class='';
		if($action=='hilourdido')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('hilourdido/','Reporte de urdido','class="btn '.$class.'"');
		$class='';
		if($action=='compra_por_proveedor')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('compra_por_proveedor/','Compra/Proveedor','class="btn '.$class.'"');
		$class='';
		if($action=='venta_por_cliente')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('venta_por_cliente/','Venta/Cliente','class="btn '.$class.'"');
		$class='';
		if($action=='guias_tintoreria')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('guias_tintoreria/','Guias / Tintoreria','class="btn '.$class.'"');
		$class='';
		if($action=='movimientos')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('movimientos/','Movimientos','class="btn '.$class.'"');
		$class='';
		if($action=='detracciones')$class='btn-success';
		$code.='&nbsp;'.Html::linkAction('detracciones/','Detracciones por Mes','class="btn '.$class.'"');
		$class='';
		if($action!='index')$code.='&nbsp;'.Html::linkAction("", 'Volver ','class="btn"');
		//$code.=Html::link('santacarmela/tintoreria/rollos_venta','Rollos para venta','class="btn"') listado_rollos;
		$code.='</div>';*/
?>