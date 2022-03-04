<?php
/*
01 FACTURAS
02 RECIBO POR HONORARIOS
03 BOLETA DE VENTA
04 LIQUIDACION DE COMPRA
07 NOTA DE CREDITO 
08 NOTA DE DEBITO 
09 GUIA REMISION 
10 RECIBO POR ARRENDAMIENTO
12 TICKET DE MAQUINA REGISTRADOR
14 RECIBO POR SERVICIOS
#######
*/

class Scripthead
{
	public static function head($tipo,$simbolo,$totalconigv=0,$igv=0)
	{
		if(empty($totalconigv))$totalconigv=0;
		if(empty($igv))$igv=0;
		
		switch($tipo)
		{
		/*FACTURAS*/
case '01': ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
        <th width="110" valign="top" align="left" class="meta-head"></th>
        <td width="236" valign="top" align="left"><?php //echo Form::text('codigo','size="5" class="grabar_form"')?></td>
        <th width="116" valign="top" align="left" class="meta-head"></th>
        <td width="333" valign="top" align="left"><?php echo Form::hidden('npedido','size="5" class="grabar_form"')?></td>
      </tr>
      <tr>
        <th width="110" valign="top" align="left" class="meta-head">Orden Compra</th>
        <td width="236" valign="top" align="left"><?php echo Form::text('ordendecompra','size="5" class="grabar_form"')?></td>
        <th width="110" valign="top" align="left" class="meta-head">Numero de Guia (fac)</th>
        <td width="236" valign="top" align="left"><?php echo Form::text('numeroguia','size="5" class="grabar_form" class="grabar_form"' )?></td>
      </tr>
      <tr>
      	<th align="left">movimiento</th>
        <td align="left"><?php echo Form::select('movimiento',array("FA"=>'FACTURA ADELANTO',"CP"=>'COMPRA'),'','CP')?></td>
        <th align="left" valign="top" class="meta-head">Monto Total</th>
        <td  valign="top"align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo number_format($totalconigv,2,'.','')?></span></td>
       </tr>
      <?php
	  ; break;
	  /*RECIBO POR HONORARIOS*/
case '02': ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
     	<th align="left">movimiento</th>
        <td align="left"><?php echo Form::text('movimiento','size="5"')?></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo number_format($totalconigv+$igv,2,'.','')?></span></td>
       </tr>
      <?php
	  ; break;
	  /*BOLETA DE VENTA*/
case '03': ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
        <th width="110" align="left" class="meta-head">Codigo</th>
        <td width="236" align="left"><?php echo Form::text('codigo','size="5"')?></td>
        <th width="116" align="left" class="meta-head">Pedido</th>
        <td width="333" align="left"><?php echo Form::text('npedido','size="5"')?></td>
      </tr>
      <tr>
        <th width="110" align="left" class="meta-head">Orden Compra</th>
        <td width="236" align="left"><?php echo Form::text('ordendecompra','size="5"')?></td>
        <th width="110" align="left" class="meta-head">Numero de Guia (fac)</th>
        <td width="236" align="left"><?php echo Form::text('numeroguia','size="5"')?></td>
      </tr>
      <tr>
        <th width="116" align="left" class="meta-head">Factura Referencia(Guias)</th>
        <td width="333" align="left"><?php echo Form::text('numerofactura','size="5"')?></td>
        <th width="116" align="left" class="meta-head">Fecha de Traslado(Guias)</th>
        <td width="333" align="left"><?php echo Calendar::selector('finiciotraslado');?></td>
      </tr>
      <tr>
     	<th align="left">movimiento</th>
        <td align="left"><?php echo Form::select('movimiento',array("FA"=>'FACTURA ADELANTO',"CP"=>'COMPRA'),'','CP')?></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo number_format($totalconigv+$igv,2,'.','')?></span></td>
       </tr>
      <?php
	  ; break;
	  /*LIQUIDACION DE COMPRA*/
case '04': ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
        <th width="110" align="left" class="meta-head">Codigo</th>
        <td width="236" align="left"><?php echo Form::text('codigo','size="5"')?></td>
        <th width="116" align="left" class="meta-head">Pedido</th>
        <td width="333" align="left"><?php echo Form::text('npedido','size="5"')?></td>
      </tr>
      <tr>
        <th width="110" align="left" class="meta-head">Orden Compra</th>
        <td width="236" align="left"><?php echo Form::text('ordendecompra','size="5"')?></td>
        <th width="110" align="left" class="meta-head">Numero de Guia (fac)</th>
        <td width="236" align="left"><?php echo Form::text('numeroguia','size="5"')?></td>
      </tr>
      <tr>
        <th width="116" align="left" class="meta-head">Factura Referencia(Guias)</th>
        <td width="333" align="left"><?php echo Form::text('numerofactura','size="5"')?></td>
        <th width="116" align="left" class="meta-head">Fecha de Traslado(Guias)</th>
        <td width="333" align="left"><?php echo Calendar::selector('finiciotraslado');?></td>
      </tr>
      <tr>
     	<th align="left"></th>
        <td align="left"><?php //echo Form::select('movimiento',array("AB"=>"ABONO","FA"=>'FACTURA ADELANTO',"VT"=>'VENTA',"NC"=>"NOTA DE CREDITO"))?></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo number_format($totalconigv+$igv,2,'.','')?></span></td>
       </tr>
      <?php
	  ; break;
	  /*NOTA DE CREDITO*/
case '07': ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
     	<th align="left"></th>
        <td align="left"><?php //echo Form::select('movimiento',array("AB"=>"ABONO","FA"=>'FACTURA ADELANTO',"VT"=>'VENTA',"NC"=>"NOTA DE CREDITO"))?></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo number_format($totalconigv+$igv,2,'.','')?></span></td>
       </tr>
      <?php
	  ; break;
	  /*NOTA DE DEBITO*/
case '08': ?>
	 	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
     	<th align="left"></th>
        <td align="left"><?php //echo Form::select('movimiento',array("AB"=>"ABONO","FA"=>'FACTURA ADELANTO',"VT"=>'VENTA',"NC"=>"NOTA DE CREDITO"))?></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo number_format($totalconigv+$igv,2,'.','')?></span></td>
       </tr>
      <?php
	  ; break;
	  /*GUIA REMISION*/
case '09';?>
      <tr>
        <th width="110" align="left" class="meta-head"></th>
        <td width="236" align="left"><?php //echo Form::text('codigo','size="5" class="grabar_form"')?></td>
        <th width="116" align="left" class="meta-head">Pedido</th>
        <td width="333" align="left"><?php echo Form::text('npedido','size="5"')?></td>
      </tr>
      <tr>
        <th width="110" align="left" class="meta-head">Orden Compra</th>
        <td width="236" align="left"><?php echo Form::text('ordendecompra','size="5" class="grabar_form"')?></td>
        <th align="left"></th>
        <td align="left"><?php //echo Form::select('movimiento',array("AB"=>"ABONO","FA"=>'FACTURA ADELANTO',"VT"=>'VENTA',"NC"=>"NOTA DE CREDITO"))?></td>
      </tr>
      <tr>
        <th width="116" align="left" class="meta-head">Factura Referencia(Guias)</th>
        <td width="333" align="left"><?php echo Form::text('numerofactura','size="5" class="grabar_form"')?></td>
        <th width="116" align="left" class="meta-head">Fecha de Traslado(Guias)</th>
        <td width="333" align="left"><?php echo Calendar::selector('finiciotraslado');?></td>
      </tr>
      <?php
	  ; break;
	  /*RECIBO POR ARRENDAMIENTO*/
case '10': ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
        <th width="110" align="left" class="meta-head">Codigo</th>
        <td width="236" align="left"><?php echo Form::text('codigo','size="5"')?></td>
        <th width="116" align="left" class="meta-head">Pedido</th>
        <td width="333" align="left"><?php echo Form::text('npedido','size="5"')?></td>
      </tr>
      <tr>
        <th width="110" align="left" class="meta-head">Orden Compra</th>
        <td width="236" align="left"><?php echo Form::text('ordendecompra','size="5"')?></td>
        <th width="110" align="left" class="meta-head">Numero de Guia (fac)</th>
        <td width="236" align="left"><?php echo Form::text('numeroguia','size="5"')?></td>
      </tr>
      <tr>
        <th width="116" align="left" class="meta-head">Factura Referencia(Guias)</th>
        <td width="333" align="left"><?php echo Form::text('numerofactura','size="5"')?></td>
        <th width="116" align="left" class="meta-head">Fecha de Traslado(Guias)</th>
        <td width="333" align="left"><?php echo Calendar::selector('finiciotraslado');?></td>
      </tr>
      <tr>
     	<th align="left"></th>
        <td align="left"><?php //echo Form::text('movimiento','size="5"')?></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo number_format($totalconigv+$igv,2,'.','')?></span></td>
       </tr>
      <?php
	  ; break;
	  /*TICKET DE MAQUINA REGISTRADOR*/
case '12': ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
        <th align="left"></th>
        <td align="left"><?php //echo Form::select('movimiento',array("AB"=>"ABONO","FA"=>'FACTURA ADELANTO',"VT"=>'VENTA',"NC"=>"NOTA DE CREDITO"))?></td>
        <th width="116" align="left" class="meta-head"></th>
        <td width="333" align="left"><?php //echo Calendar::selector('fvencimiento');?></td>
      </tr>
      <tr>
     	<th></th>
        <td></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo number_format($totalconigv,2,'.','')?></span></td>
       </tr>
      <?php
	  ; break;
	  /*RECIBO POR SERVICIOS*/
case '14': ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr><?php /*?>
      <tr>
        <th width="116" align="left" class="meta-head">Factura Referencia(Guias)</th>
        <td width="333" align="left"><?php echo Form::text('numerofactura','size="5"')?></td>
        <th width="116" align="left" class="meta-head">Fecha de Traslado(Guias)</th>
        <td width="333" align="left"><?php echo Calendar::selector('finiciotraslado');?></td>
      </tr><?php */?>
      <tr>
     	<th align="left">Moviemiento</th>
        <td align="left"><?php echo Form::select('movimiento',array("FA"=>'FACTURA ADELANTO',"CP"=>'COMPRA'),'','CP')?></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo number_format($totalconigv+$igv,2,'.','')?></span></td>
       </tr>
      <?php
	  ; break;
/* CHEQUES */
case '104': ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
        <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
        <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
        <th width="116" align="left" class="meta-head">Referencia del Girador</th>
        <td width="333" align="left"><?php echo Form::text('numerofactura','size="10" class="grabar_form"')?></td>
        <th width="116" align="left" class="meta-head">Fecha  de cobro</th>
        <td width="333" align="left"><?php echo Calendar::selector('fvencimiento');?></td>
      </tr>
      <tr>
     	<th align="left">Banco</th>
        <td align="left"><?php echo Form::dbSelect('tesbancos_id','nombre',array('tesbancos','find','conditions: aclempresas_id='.Session::get('EMPRESAS_ID')));?></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo Form::text('importe','',number_format($totalconigv,2,'.',''))?></span></td>
       </tr>
      <?php
	  ; break;
	  /* LETRAS */
case '101': ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head"><?php /*C Gastos*/?></th>
        <td width="302" align="left" valign="top"><?php //echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
        <th width="116" align="left" class="meta-head">Referencia del Girador</th>
        <td width="333" align="left"><?php echo Form::text('numerofactura','size="10" class="grabar_form"')?></td>
        <th width="116" align="left" class="meta-head">Fecha  de Vencimiento</th>
        <td width="333" align="left"><?php echo Calendar::selector('fvencimiento');?></td>
      </tr>
      <tr>
     	<th align="left">Movimiento</th>
        <td align="left"><?php echo Form::select('movimiento',array("LA"=>'LETRA ADELANTO',"CP"=>'COMPRA'),'')?></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo Form::text('importe','class="grabar_form"',number_format($totalconigv,2,'.',''))?></span></td>
       </tr>
      <?php
	  ; break;
	  case NULL: ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
        <th width="110" align="left" class="meta-head">Codigo</th>
        <td width="236" align="left"><?php echo Form::text('codigo','size="5"')?></td>
        <th width="116" align="left" class="meta-head">Pedido</th>
        <td width="333" align="left"><?php echo Form::text('npedido','size="5"')?></td>
      </tr>
      <tr>
        <th width="110" align="left" class="meta-head">Orden Compra</th>
        <td width="236" align="left"><?php echo Form::text('ordendecompra','size="5"')?></td>
        <th width="110" align="left" class="meta-head">Numero de Guia (fac)</th>
        <td width="236" align="left"><?php echo Form::text('numeroguia','size="5"')?></td>
      </tr>
      <tr>
        <th width="116" align="left" class="meta-head">Factura Referencia(Guias)</th>
        <td width="333" align="left"><?php echo Form::text('numerofactura','size="5"')?></td>
        <th width="116" align="left" class="meta-head">Fecha de Traslado(Guias)</th>
        <td width="333" align="left"><?php echo Calendar::selector('finiciotraslado');?></td>
      </tr>
      <tr>
     	<th align="left">movimiento</th>
        <td align="left"><?php echo Form::select('movimiento',array("AB"=>"ABONO","FA"=>'FACTURA ADELANTO',"VT"=>'VENTA',"NC"=>"NOTA DE CREDITO"))?></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo number_format($totalconigv+$igv,2,'.','')?></span></td>
       </tr>
      <?php
	  ; break;
	  default: ?>
	  <tr>
        <th width="56" align="left" valign="top" class="meta-head">C Gastos</th>
        <td width="302" align="left" valign="top"><?php echo Form::text('cuantagastos')?></td>
          
          <th width="69" align="left" valign="top" class="meta-head">C por Pagar:</th>
          <td width="255" align="left" valign="top"><?php echo Form::text('cuentaporpagar')?></td>
      </tr>
      <tr>
        <th width="110" align="left" class="meta-head">Codigo</th>
        <td width="236" align="left"><?php echo Form::text('codigo','size="5"')?></td>
        <th width="116" align="left" class="meta-head">Pedido</th>
        <td width="333" align="left"><?php echo Form::text('npedido','size="5"')?></td>
      </tr>
      <tr>
        <th width="110" align="left" class="meta-head">Orden Compra</th>
        <td width="236" align="left"><?php echo Form::text('ordendecompra','size="5"')?></td>
        <th width="110" align="left" class="meta-head">Numero de Guia (fac)</th>
        <td width="236" align="left"><?php echo Form::text('numeroguia','size="5"')?></td>
      </tr>
      <tr>
        <th width="116" align="left" class="meta-head">Factura Referencia(Guias)</th>
        <td width="333" align="left"><?php echo Form::text('numerofactura','size="5"')?></td>
        <th width="116" align="left" class="meta-head">Fecha de Traslado(Guias)</th>
        <td width="333" align="left"><?php echo Calendar::selector('finiciotraslado');?></td>
      </tr>
      <tr>
     	<th align="left">movimiento</th>
        <td align="left"><?php echo Form::select('movimiento',array("AB"=>"ABONO","FA"=>'FACTURA ADELANTO',"VT"=>'VENTA',"NC"=>"NOTA DE CREDITO"))?></td>
        <th align="left" class="meta-head">Monto Total</th>
        <td align="left"><span class="simbolo"><?php echo $simbolo?></span></span><span class="due"><?php echo number_format($totalconigv+$igv,2,'.','')?></span></td>
       </tr>
      <?php
	  ; break;
	  }

	}
}