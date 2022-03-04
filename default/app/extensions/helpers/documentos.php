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

class Documentos
{
	
	public static function tr($tipo=01,$simbolo)
	{
		switch($tipo)
		{
			/*FACTURAS*/
case '01': 
				$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><input id="codigo-\'+IDnew+\'" name="codigo-\'+IDnew+\'" type="text" readonly="readonly" size="10"><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>';
				$code.='<td class="item-name"><input id="productos_id-\'+IDnew+\'" name="productos_id-\'+IDnew+\'" type="hidden" class="productos_id" readonly="readonly"><span id="ver-name\'+IDnew+\'" style="display:none;"><input id="productoname-\'+IDnew+\'" name="productoname-\'+IDnew+\'" type="text" value="" readonly="readonly"></span><span id="ver\'+IDnew+\'" class="ver"><input id="producto\'+IDnew+\'" name="producto\'+IDnew+\'" type="text" class="producto" placeholder="Producto" size="30" readonly="readonly"></span></td>';
				$code.='<td><input id="tesunidadesmedidas\'+IDnew+\'" name="tesunidadesmedidas\'+IDnew+\'" type="text" value=""><input class="unidad" id="tesunidadesmedidas_id-\'+IDnew+\'" name="tesunidadesmedidas_id-\'+IDnew+\'" type="hidden" value=""></td>';
				$code.='<td> <input id="lote1" name="lote1" type="text" value="" size="10"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><input id="precio-\'+IDnew+\'" type="text"  size="10" class="cost"/></td>';
				$code.='<td><input  size="10" id="cantidad-\'+IDnew+\'" type="text" class="qty"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><span id="total-\'+IDnew+\'" class="price">0.00</span></td>';
				$code.='</tr>';
			;break;
			/*RECIBO POR HONORARIOS*/
case '02': 
				$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><textarea id="concepto\'+IDnew+\'" name="concepto\'+IDnew+\'" rows="5" cols="100"><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>'; 
			
				$code.='<td><span class="simbolo">'.$simbolo.'</span><input id="precio-\'+IDnew+\'" type="text"  size="10" class="cost"/></td>';
				//$code.='<td><input  size="10" id="cantidad-\'+IDnew+\'" type="text" class="qty"/></td>';
				//$code.='<td><span class="simbolo">'.$simbolo.'</span><span id="total-\'+IDnew+\'" class="price">0.00</span></td>';
				$code.='</tr>';
			;break;
			/*BOLETA DE VENTA*/
case '03': 
				$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><input id="codigo-\'+IDnew+\'" name="codigo-\'+IDnew+\'" type="text" readonly="readonly" size="10"><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>';
				$code.='<td class="item-name"><input id="productos_id-\'+IDnew+\'" name="productos_id-\'+IDnew+\'" type="hidden" class="productos_id" readonly="readonly"><span id="ver-name\'+IDnew+\'" style="display:none;"><input id="productoname-\'+IDnew+\'" name="productoname-\'+IDnew+\'" type="text" value="" readonly="readonly"></span><span id="ver\'+IDnew+\'" class="ver"><input id="producto\'+IDnew+\'" name="producto\'+IDnew+\'" type="text" class="producto" placeholder="Producto" size="30" readonly="readonly"></span></td>';
				$code.='<td><input id="tesunidadesmedidas\'+IDnew+\'" name="tesunidadesmedidas\'+IDnew+\'" type="text" value=""><input class="unidad" id="tesunidadesmedidas_id-\'+IDnew+\'" name="tesunidadesmedidas_id-\'+IDnew+\'" type="hidden" value=""></td>';
				$code.='<td> <input id="lote1" name="lote1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="empaque1" name="empaque1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="bobinas1" name="bobinas1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="pesoneto1" name="pesoneto1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="pesobruto1" name="pesobruto1" type="text" value="" size="10"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><input id="precio-\'+IDnew+\'" type="text"  size="10" class="cost"/></td>';
				$code.='<td><input  size="10" id="cantidad-\'+IDnew+\'" type="text" class="qty"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><span id="total-\'+IDnew+\'" class="price">0.00</span></td>';
				$code.='</tr>';
			;break;
			/*LIQUIDACION DE COMPRA*/
case '04': 
				$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><input id="codigo-\'+IDnew+\'" name="codigo-\'+IDnew+\'" type="text" readonly="readonly" size="10"><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>';
				$code.='<td class="item-name"><input id="productos_id-\'+IDnew+\'" name="productos_id-\'+IDnew+\'" type="hidden" class="productos_id" readonly="readonly"><span id="ver-name\'+IDnew+\'" style="display:none;"><input id="productoname-\'+IDnew+\'" name="productoname-\'+IDnew+\'" type="text" value="" readonly="readonly"></span><span id="ver\'+IDnew+\'" class="ver"><input id="producto\'+IDnew+\'" name="producto\'+IDnew+\'" type="text" class="producto" placeholder="Producto" size="30" readonly="readonly"></span></td>';
				$code.='<td><input id="tesunidadesmedidas\'+IDnew+\'" name="tesunidadesmedidas\'+IDnew+\'" type="text" value=""><input class="unidad" id="tesunidadesmedidas_id-\'+IDnew+\'" name="tesunidadesmedidas_id-\'+IDnew+\'" type="hidden" value=""></td>';
				$code.='<td> <input id="lote1" name="lote1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="empaque1" name="empaque1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="bobinas1" name="bobinas1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="pesoneto1" name="pesoneto1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="pesobruto1" name="pesobruto1" type="text" value="" size="10"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><input id="precio-\'+IDnew+\'" type="text"  size="10" class="cost"/></td>';
				$code.='<td><input  size="10" id="cantidad-\'+IDnew+\'" type="text" class="qty"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><span id="total-\'+IDnew+\'" class="price">0.00</span></td>';
				$code.='</tr>';
			;break;
			/*NOTA DE CREDITO*/
case '07': 
				$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><textarea id="concepto\'+IDnew+\'" name="concepto\'+IDnew+\'" rows="5" cols="100"><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>'; 
			
				$code.='<td><span class="simbolo">'.$simbolo.'</span><input id="precio-\'+IDnew+\'" type="text"  size="10" class="cost"/></td>';
				//$code.='<td><input  size="10" id="cantidad-\'+IDnew+\'" type="text" class="qty"/></td>';
				//$code.='<td><span class="simbolo">'.$simbolo.'</span><span id="total-\'+IDnew+\'" class="price">0.00</span></td>';
				$code.='</tr>';
			;break;
			/*NOTA DE DEBITO*/
case '08': 
				$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><textarea id="concepto\'+IDnew+\'" name="concepto\'+IDnew+\'" rows="5" cols="100"><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>'; 
			
				$code.='<td><span class="simbolo">'.$simbolo.'</span><input id="precio-\'+IDnew+\'" type="text"  size="10" class="cost"/></td>';
				//$code.='<td><input  size="10" id="cantidad-\'+IDnew+\'" type="text" class="qty"/></td>';
				//$code.='<td><span class="simbolo">'.$simbolo.'</span><span id="total-\'+IDnew+\'" class="price">0.00</span></td>';
				$code.='</tr>';
			/*GUIA REMISION*/
case '09':
				$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><input id="codigo-\'+IDnew+\'" name="codigo-\'+IDnew+\'" type="text" readonly="readonly" size="10"><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>';
				$code.='<td class="item-name"><input id="productos_id-\'+IDnew+\'" name="productos_id-\'+IDnew+\'" type="hidden" class="productos_id" ><span id="ver-name\'+IDnew+\'" style="display:none;"><input id="productoname-\'+IDnew+\'" name="productoname-\'+IDnew+\'" type="text" value="" ></span><span id="ver\'+IDnew+\'" class="ver"><input id="producto\'+IDnew+\'" name="producto\'+IDnew+\'" type="text" class="producto" placeholder="Producto" size="30"></span></td>';
				$code.='<td><input id="tesunidadesmedidas\'+IDnew+\'" name="tesunidadesmedidas\'+IDnew+\'" type="text" value=""><input class="unidad" id="tesunidadesmedidas_id-\'+IDnew+\'" name="tesunidadesmedidas_id-\'+IDnew+\'" type="hidden" value=""></td>';
				$code.='<td><input id="tescolores\'+IDnew+\'" name="tescolores\'+IDnew+\'" type="text" value=""><input class="color" id="tescolores_id-\'+IDnew+\'" name="tescolores_id-\'+IDnew+\'" type="hidden" value=""></td>';
				$code.='<td> <input id="lote\'+IDnew+\'" class="save" name="lote\'+IDnew+\'" type="text" value="" size="10"/></td>';
				$code.='<td> <input data-id="0" class="save cajas" id="empaque\'+IDnew+\'" name="empaque\'+IDnew+\'" type="text" value="" size="10"/> <div class="flotante" id="cajas-\'+IDnew+\'"></div></td>';
				$code.='<td> <input id="bobinas\'+IDnew+\'" name="bobinas\'+IDnew+\'" type="text" value="" size="10"/></td>';
				$code.='<td> <input id="pesoneto\'+IDnew+\'" class="save" name="pesoneto\'+IDnew+\'" type="text" value="" size="10"/></td>';
				$code.='<td> <input id="pesobruto\'+IDnew+\'" class="save" name="pesobruto\'+IDnew+\'" type="text" value="" size="10"/></td>';
				$code.='</tr>';
			;break;
			/*RECIBO POR ARRENDAMIENTO*/
case '10': 
				$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><input id="codigo-\'+IDnew+\'" name="codigo-\'+IDnew+\'" type="text" readonly="readonly" size="10"><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>';
				$code.='<td class="item-name"><input id="productos_id-\'+IDnew+\'" name="productos_id-\'+IDnew+\'" type="hidden" class="productos_id" readonly="readonly"><span id="ver-name\'+IDnew+\'" style="display:none;"><input id="productoname-\'+IDnew+\'" name="productoname-\'+IDnew+\'" type="text" value="" readonly="readonly"></span><span id="ver\'+IDnew+\'" class="ver"><input id="producto\'+IDnew+\'" name="producto\'+IDnew+\'" type="text" class="producto" placeholder="Producto" size="30" readonly="readonly"></span></td>';
				$code.='<td><input id="tesunidadesmedidas\'+IDnew+\'" name="tesunidadesmedidas\'+IDnew+\'" type="text" value=""><input class="unidad" id="tesunidadesmedidas_id-\'+IDnew+\'" name="tesunidadesmedidas_id-\'+IDnew+\'" type="hidden" value=""></td>';
				$code.='<td> <input id="lote1" name="lote1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="empaque1" name="empaque1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="bobinas1" name="bobinas1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="pesoneto1" name="pesoneto1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="pesobruto1" name="pesobruto1" type="text" value="" size="10"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><input id="precio-\'+IDnew+\'" type="text"  size="10" class="cost"/></td>';
				$code.='<td><input  size="10" id="cantidad-\'+IDnew+\'" type="text" class="qty"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><span id="total-\'+IDnew+\'" class="price">0.00</span></td>';
				$code.='</tr>';
			/*TICKET DE MAQUINA REGISTRADOR*/
case '12': 
				$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><input id="concepto\'+IDnew+\'" name="concepto\'+IDnew+\'" type="text" value=""/><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>';
				$code.='<td class="item-name"><input id="productos_id-\'+IDnew+\'" name="productos_id-\'+IDnew+\'" type="hidden" class="productos_id" readonly="readonly"><span id="ver-name\'+IDnew+\'" style="display:none;"><input id="productoname-\'+IDnew+\'" name="productoname-\'+IDnew+\'" type="text" value="" readonly="readonly"></span><span id="ver\'+IDnew+\'" class="ver"><input id="producto\'+IDnew+\'" name="producto\'+IDnew+\'" type="text" class="producto" placeholder="Producto" size="30" readonly="readonly"></span></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><input id="precio-\'+IDnew+\'" type="text"  size="10" class="cost"/></td>';
				$code.='<td><input  size="10" id="cantidad-\'+IDnew+\'" type="text" class="qty"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><span id="total-\'+IDnew+\'" class="price">0.00</span></td>';
				$code.='</tr>';
			;break;
			/*RECIBO POR SERVICIOS*/
case '14': 
				$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><input id="concepto\'+IDnew+\'" name="concepto\'+IDnew+\'" type="text" value=""/><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>'; 
			
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><input id="precio-\'+IDnew+\'" type="text"  size="10" class="cost"/></td>';
				$code.='<td><input  size="10" id="cantidad-\'+IDnew+\'" type="text" class="qty"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><span id="total-\'+IDnew+\'" class="price">0.00</span></td>';
				$code.='</tr>';
			;break;
			case NULL: 
				$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><input id="codigo-\'+IDnew+\'" name="codigo-\'+IDnew+\'" type="text" readonly="readonly" size="10"><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>';
				$code.='<td class="item-name"><input id="productos_id-\'+IDnew+\'" name="productos_id-\'+IDnew+\'" type="hidden" class="productos_id" readonly="readonly"><span id="ver-name\'+IDnew+\'" style="display:none;"><input id="productoname-\'+IDnew+\'" name="productoname-\'+IDnew+\'" type="text" value="" readonly="readonly"></span><span id="ver\'+IDnew+\'" class="ver"><input id="producto\'+IDnew+\'" name="producto\'+IDnew+\'" type="text" class="producto" placeholder="Producto" size="30" readonly="readonly"></span></td>';
				$code.='<td><input id="tesunidadesmedidas\'+IDnew+\'" name="tesunidadesmedidas\'+IDnew+\'" type="text" value=""><input class="unidad" id="tesunidadesmedidas_id-\'+IDnew+\'" name="tesunidadesmedidas_id-\'+IDnew+\'" type="hidden" value=""></td>';
				$code.='<td> <input id="lote1" name="lote1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="empaque1" name="empaque1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="bobinas1" name="bobinas1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="pesoneto1" name="pesoneto1" type="text" value="" size="10"/></td>';
				//$code.='<td> <input id="pesobruto1" name="pesobruto1" type="text" value="" size="10"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><input id="precio-\'+IDnew+\'" type="text"  size="10" class="cost"/></td>';
				$code.='<td><input  size="10" id="cantidad-\'+IDnew+\'" type="text" class="qty"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><span id="total-\'+IDnew+\'" class="price">0.00</span></td>';
				$code.='</tr>';
			;break;
			default:
			$code='<tr id="\'+IDnew+\'" class="item-row">';
				$code.='<td><div class="delete-wpr"><input id="codigo-\'+IDnew+\'" name="codigo-\'+IDnew+\'" type="text" readonly="readonly" size="10"><a id="del-\'+IDnew+\'" class="delete" href="javascript:;" title="Elimar registro" data-id="0" style="">X</a></div></td>';
				$code.='<td class="item-name"><input id="productos_id-\'+IDnew+\'" name="productos_id-\'+IDnew+\'" type="hidden" class="productos_id" readonly="readonly"><span id="ver-name\'+IDnew+\'" style="display:none;"><input id="productoname-\'+IDnew+\'" name="productoname-\'+IDnew+\'" type="text" value="" readonly="readonly"></span><span id="ver\'+IDnew+\'" class="ver"><input id="producto\'+IDnew+\'" name="producto\'+IDnew+\'" type="text" class="producto" placeholder="Producto" size="30" readonly="readonly"></span></td>';
				$code.='<td><input id="tesunidadesmedidas\'+IDnew+\'" name="tesunidadesmedidas\'+IDnew+\'" type="text" value=""><input class="unidad" id="tesunidadesmedidas_id-\'+IDnew+\'" name="tesunidadesmedidas_id-\'+IDnew+\'" type="hidden" value=""></td>';
				$code.='<td> <input id="lote1" name="lote1" type="text" value="" size="10"/></td>';
				$code.='<td> <input id="empaque1" name="empaque1" type="text" value="" size="10"/></td>';
				$code.='<td> <input id="bobinas1" name="bobinas1" type="text" value="" size="10"/></td>';
				$code.='<td> <input id="pesoneto1" name="pesoneto1" type="text" value="" size="10"/></td>';
				$code.='<td> <input id="pesobruto1" name="pesobruto1" type="text" value="" size="10"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><input id="precio-\'+IDnew+\'" type="text"  size="10" class="cost"/></td>';
				$code.='<td><input  size="10" id="cantidad-\'+IDnew+\'" type="text" class="qty"/></td>';
				$code.='<td><span class="simbolo"><?php echo $simbolo;?></span><span id="total-\'+IDnew+\'" class="price">0.00</span></td>';
				$code.='</tr>';
			break;
		}
		return $code;
	}
/*
###
Detalle de los ingresos
*/
public static function doc($detalles=NULL,$tipo=01,$DETALLE,$simbolo,$igv,$subt,$subtotal,$pedido=NULL)
	{
		switch($tipo)
		{
			/*FACTURAS*/
case '01':
			?>
       <tbody id="factura-<?php echo $tipo?>">
            <tr>
		      <th>Codigo</th>
		      <th>Producto</th>
		      <th>Medida</th>
              <th>Lote</th>
		      <th>Costo Unit</th>
		      <th>Cantidad</th>
		      <th>Precio</th>
		  </tr>
          <?php
		   echo $pedido;
		   if(!empty($pedido)){$servico=substr($pedido,0,2);}else{$servico=$pedido;}
		   if($servico!='TI'){
			if($DETALLE)
			{
				$n=0;
				$subtotal=0;
				foreach($detalles as $item)
				{
						  $n++;
						  $subtotal=$subtotal+$item->importe;
				?>
                      <?php echo Form::hidden('tescolores_id-'.$n,'class="tescolores_id"',$item->tescolores_id)?>
                  <tr id="<?php echo $n?>" class="item-row">
				   <td>
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-'.$n,'size="10" readonly="readonly"',$item->getTesproductos()->codigo)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						  </div>
					</td>
					  <td class="item-name">
						  <?php echo Form::hidden('productos_id-'.$n,'class="productos_id" readonly="readonly"',$item->tesproductos_id)?>
						  <span id="ver-name<?php echo $n?>" style="display:none;"><?php echo Form::text('productoname-'.$n,' size="30" readonly="readonly"')?></span>
						  <span id="ver<?php echo $n;?>" class="ver"><?php echo Form::text('producto'.$n,'class="producto" placeholder="Producto" readonly="readonly"',$item->getTesproductos()->nombre)?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-'.$n,'readonly="readonly"',$item->tesunidadesmedidas_id)?> <?php echo Form::text('tesunidadesmedidas'.$n,'class="unidad" size="10"',$item->getTesunidadesmedidas()->nombre)?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="10"',$item->lote)?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php 
					  //echo $item->preciounitario.'precio//';
					  if(empty($item->preciounitario))$precio_unitario=0000;else $precio_unitario=$item->preciounitario;
					   echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"',number_format($precio_unitario,2,'.',''))?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="0.0"',$item->cantidad);?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price"><?php if(!empty($item->importe))echo number_format($item->importe,2,'.','');else echo "00.00";?></span></td>
				  </tr>
				  
				  <?php
					  }/*FIN FOREACH*/
					if($n==0)
						{  $n=1; ?>
						  <tr id="1" class="item-row">
							 <td class="item-name">
								  <div class="delete-wpr">
								  <?php echo Form::text('codigo-1','class="productos_id" size="10" readonly="readonly"')?>
								  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
								  </div>
							</td>
							  <td class="item-name">
								 <?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
								  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
								  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
							  </td>
							  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="5"')?></td>
							  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-1','size="5" class="cost" placeholder="0.0"')?></td>
							  <td><?php echo Form::text('cantidad-1',' class="qty" size="5" placeholder="0.0"');?></td>
							  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-1' class="price">0.00</span></td>
						  </tr>
				  <?php
						}?>
				  <?php 
				  }else{
					 $n=1; ?>
				  <tr id="1" class="item-row">
					 <td class="item-name">
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-1','class="productos_id" readonly="readonly"')?>
						  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
					  <td class="item-name"><?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
						  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
						  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="5"')?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-1','class="cost" placeholder="0.0"')?></td>
					  <td><?php echo Form::text('cantidad-1',' class="qty" placeholder="0.0"');?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-1' class="price">0.00</span></td>
				  </tr>
			<?php 
				}  
			?>
            <tr id="hiderow">
		    <td colspan="12"><a id="addrow" href="javascript:;" title="Agregar mas registros">Agragar Mas</a></td>
		    </tr>
            <?php 
			
		}else{
		
		/*PARA FACTURAS DE SERVICIO*/
			
		$n=0;
		$pesoneto=0;
		if($DETALLE)
		{
			echo " servicio";
			foreach($detalles as $im)
			{
				if($im->tesproductos_id!=0)
				{
					$pesoneto=$pesoneto+$im->pesoneto;
				?>
                <tr>
                <td colspan="12">
				<?php 
				if(!empty($im->tesproductos_id))
				{
				echo $im->getTesproductos()->detalle; 
				echo $im->getTesproductos()->nombre;
				?> - <?php 
				echo $im->getTesproductos()->codigo?> Numero de rollo
				<?Php if(!empty($im->prorollos_id)){echo $im->getProrollos()->numero?> <?php echo $im->getProrollos()->numeroventa;}
				}?></td>
                </tr>
				<?php 
				}
			}
				
				$subtotal=0;
			foreach($detalles as $item)
			{
				if($item->tesproductos_id==0)
				{
					$n++;
					$subtotal=$subtotal+$item->importe;		  
				?>
                <?php echo Form::hidden('tescolores_id-'.$n,'class="tescolores_id"',$item->tescolores_id)?>
                  <tr id="<?php echo $n?>" class="item-row">
				  <td class="item-name" colspan="2">
				 <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						factura de servicio<?php echo Form::hidden('productos_id-'.$n,'class="productos_id" readonly="readonly"',0)?>
						<?php echo Form::textArea('pro_descripcion-'.$n,'class="grabar_form" style="margin-left: 0px; margin-right: 0px; width: 100%;"',$item->concepto)?>  
						 
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-'.$n,'readonly="readonly"',$item->tesunidadesmedidas_id)?> <?php echo Form::text('tesunidadesmedidas'.$n,'class="unidad" size="10"',$item->getTesunidadesmedidas()->nombre)?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="10"',$item->lote)?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php 
					  //echo $item->preciounitario.'precio//';
					  if(empty($item->preciounitario))$precio_unitario=0000;else $precio_unitario=$item->preciounitario;
					   echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"',number_format($precio_unitario,2,'.',''))?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="0.0"',$item->cantidad);?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price"><?php if(!empty($item->importe))echo number_format($item->importe,2,'.','');else echo "00.00";?></span></td>
				  </tr>
          <?Php }
			}
		}
				if($n==0){
					$n=1?>
           <tr id="1" class="item-row">
					 <td class="item-name" colspan="2">
						 <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
					 <?php echo Form::hidden('productos_id-'.$n,'class="productos_id" readonly="readonly"',0)?>
						<?php echo Form::textArea('pro_descripcion-'.$n,'class="grabar_form" style="margin-left: 0px; margin-right: 0px; width: 100%;"')?>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-1','',2)?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="5"')?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-1','class="cost" placeholder="0.0"')?></td>
					  <td><?php echo Form::text('cantidad-1',' class="qty" placeholder="0.0"',$pesoneto);?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-1' class="price">0.00</span></td>
				  </tr>
            <?Php 
				}
			}?>
          </tbody>
  </table>
          <table width="100%" class="meta">
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Sub Total : </strong></td>
		      <td width="15%" class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="subtotal"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Valor Venta : </strong></td>
		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid_total','size="10"',number_format($subt,2,'.',''));?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>IGV : </strong></td>

		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid','placeholder="0.00"')?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance"><strong>Total a Pagar : <?Php echo $subtotal.'+'.$igv?></strong> </td>
		      <td class="total-value balance"><span class="simbolo"><?php echo $simbolo?></span><span class="due"><?php echo number_format($subtotal+$igv,2,'.','')?></span></td>
		  </tr>
			<?php
			; break;
			/*RECIBO POR HONORARIOS*/
case '02':
			?><tr>
		      
              <th width="42%">Concepto</th>
		      <th width="18%">Precio</th>
		  </tr>
          <?php
			if($DETALLE)
			{
				$n=0;
				$subtotal=0;
				foreach($detalles as $item){
						  $n++;
						  $subtotal=$subtotal+$item->importe;
					  ?>
				  <tr id="<?php echo $n?>" class="item-row">
				   <td>
						  <div class="delete-wpr">
						  <?php echo Form::textArea('concepto'.$n,'rows="5" cols="100"',$item->concepto)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						  </div>
					</td>
                    <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"',number_format($item->preciounitario,2,'.',''))?></td>
					  </tr>
				  
				  <?php
					  }/*FIN FOREACH*/
					if($n==0)
						{  $n=1; ?>
					<tr id="1" class="item-row">
						<td>
						  <div class="delete-wpr">
						  <?php echo Form::textArea('concepto'.$n,'rows="5" cols="100"',$item->concepto)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
                    <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"')?></td>
				</tr>
				  <?php
						}?>
				  <?php 
				  }else{
					 $n=1; ?>
				  <tr id="1" class="item-row">
					 <td>
						  <div class="delete-wpr">
						  <?php echo Form::textArea('concepto'.$n,'rows="5" cols="100"')?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
                    <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost grabar" placeholder="0.0"')?></td>
				</tr>
			<?php }  
			?>
            <?php /*?><tr id="hiderow">
		    <td colspan="12"><a id="addrow" href="javascript:;" title="Agregar mas registros">Agragar Mas</a></td>
		  </tr><?php */?>
  </table>
          <table width="100%" class="meta">
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Total Por Honorarios: </strong></td>
		      <td width="10%" class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="subtotal"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Total Por Honorarios: </strong></td>
		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid_total','size="10"',number_format($subtotal,2,'.',''));?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Retencion (8%): </strong></td>

		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid','placeholder="0.00" readonly="readonly"')?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance"><strong>Total Neto Recibido : </strong> </td>
		      <td class="total-value balance"><span class="simbolo"><?php echo $simbolo?></span><span class="due"><?php echo number_format($subtotal-$igv,2,'.','')?></span></td>
		  </tr>
			<?php
			
			; break;
			/*BOLETA DE VENTA*/
case '03':
			?><tr>
		      <th width="11%">Codigo</th>
		      <th width="19%">Producto</th>
		      <th width="12%">Medida</th>
              <th width="24%">Lote</th>
		      <th>Costo Unit</th>
		      <th width="15%">Cantidad</th>
		      <th width="9%">Precio</th>
		  </tr>
          <?php
			if($DETALLE)
			{
				$n=0;
				$subtotal=0;
				foreach($detalles as $item){
						  $n++;
						  $subtotal=$subtotal+$item->importe;
					  ?>
				  <tr id="<?php echo $n?>" class="item-row">
				   <td>
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-'.$n,'size="10" readonly="readonly"',$item->getTesproductos()->codigo)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						  </div>
					</td>
					  <td class="item-name">
						  <?php echo Form::hidden('productos_id-'.$n,'class="productos_id" readonly="readonly"',$item->tesproductos_id)?>
						  <span id="ver-name<?php echo $n?>" style="display:none;"><?php echo Form::text('productoname-'.$n,'readonly="readonly"')?></span>
						  <span id="ver<?php echo $n;?>" class="ver"><?php echo Form::text('producto'.$n,'class="producto" placeholder="Producto" size="30" readonly="readonly"',$item->getTesproductos()->nombre)?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-'.$n,'readonly="readonly"',$item->tesunidadesmedidas_id)?> <?php echo Form::text('tesunidadesmedidas'.$n,'class="unidad" size="10"',$item->getTesunidadesmedidas()->nombre)?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="10"',$item->lote)?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"',number_format($item->preciounitario,2,'.',''))?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="0.0"',$item->cantidad);?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price"><?php echo number_format($item->importe,2,'.','')?></span></td>
				  </tr>
				  
				  <?php
					  }/*FIN FOREACH*/
					if($n==0)
						{  $n=1; ?>
						  <tr id="1" class="item-row">
							 <td class="item-name">
								  <div class="delete-wpr">
								  <?php echo Form::text('codigo-1','class="productos_id" size="10" readonly="readonly"')?>
								  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
								  </div>
							</td>
							  <td class="item-name">
								 <?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
								  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
								  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
							  </td>
							  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="5"')?></td>
							  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-1','size="5" class="cost" placeholder="0.0"')?></td>
							  <td><?php echo Form::text('cantidad-1',' class="qty" size="5" placeholder="0.0"');?></td>
							  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-1' class="price">0.00</span></td>
						  </tr>
				  <?php
						}?>
				  <?php 
				  }else{
					 $n=1; ?>
				  <tr id="1" class="item-row">
					 <td class="item-name">
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-1','class="productos_id" readonly="readonly"')?>
						  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
					  <td class="item-name">
						 <?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
						  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
						  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="5"')?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-1','class="cost" placeholder="0.0"')?></td>
					  <td><?php echo Form::text('cantidad-1',' class="qty" placeholder="0.0"');?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-1' class="price">0.00</span></td>
				  </tr>
			<?php }  
			?>
            <tr id="hiderow">
		    <td colspan="12"><a id="addrow" href="javascript:;" title="Agregar mas registros">Agragar Mas</a></td>
		  </tr>
  </table>
          <table width="100%" class="meta">
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Sub Total : </strong></td>
		      <td width="11%" class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="subtotal"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Valor Venta : </strong></td>
		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid_total','size="10"',number_format($subt,2,'.',''));?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>IGV : </strong></td>

		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid','placeholder="0.00" readonly="readonly"')?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance"><strong>Total a Pagar : </strong> </td>
		      <td class="total-value balance"><span class="simbolo"><?php echo $simbolo?></span><span class="due"><?php echo number_format($subtotal+$igv,2,'.','')?></span></td>
		  </tr>
			<?php
			
			; break;
			/*LIQUIDACION DE COMPRA*/
case '04':
			?><tr>
		      <th width="12%">Codigo</th>
		      <th width="15%">Producto</th>
		      <th width="14%">Medida</th>
              <th width="22%">Lote</th>
		      <th>Costo Unit</th>
		      <th width="15%">Cantidad</th>
		      <th width="11%">Precio</th>
		  </tr>
          <?php
			if($DETALLE)
			{
				$n=0;
				$subtotal=0;
				foreach($detalles as $item){
						  $n++;
						  $subtotal=$subtotal+$item->importe;
					  ?>
				  <tr id="<?php echo $n?>" class="item-row">
				   <td>
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-'.$n,'size="10" readonly="readonly"',$item->getTesproductos()->codigo)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						  </div>
					</td>
					  <td class="item-name">
						  <?php echo Form::hidden('productos_id-'.$n,'class="productos_id" readonly="readonly"',$item->tesproductos_id)?>
						  <span id="ver-name<?php echo $n?>" style="display:none;"><?php echo Form::text('productoname-'.$n,'readonly="readonly"')?></span>
						  <span id="ver<?php echo $n;?>" class="ver"><?php echo Form::text('producto'.$n,'class="producto" placeholder="Producto" size="30" readonly="readonly"',$item->getTesproductos()->nombre)?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-'.$n,'readonly="readonly"',$item->tesunidadesmedidas_id)?> <?php echo Form::text('tesunidadesmedidas'.$n,'class="unidad" size="10"',$item->getTesunidadesmedidas()->nombre)?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="10"',$item->lote)?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"',number_format($item->preciounitario,2,'.',''))?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="0.0"',$item->cantidad);?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price"><?php echo number_format($item->importe,2,'.','')?></span></td>
				  </tr>
				  
				  <?php
					  }/*FIN FOREACH*/
					if($n==0)
						{  $n=1; ?>
						  <tr id="1" class="item-row">
							 <td class="item-name">
								  <div class="delete-wpr">
								  <?php echo Form::text('codigo-1','class="productos_id" size="10" readonly="readonly"')?>
								  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
								  </div>
							</td>
							  <td class="item-name">
								 <?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
								  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
								  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
							  </td>
							  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="5"')?></td>
							  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-1','size="5" class="cost" placeholder="0.0"')?></td>
							  <td><?php echo Form::text('cantidad-1',' class="qty" size="5" placeholder="0.0"');?></td>
							  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-1' class="price">0.00</span></td>
						  </tr>
				  <?php
						}?>
				  <?php 
				  }else{
					 $n=1; ?>
				  <tr id="1" class="item-row">
					 <td class="item-name">
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-1','class="productos_id" readonly="readonly"')?>
						  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
					  <td class="item-name">
						 <?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
						  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
						  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="5"')?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-1','class="cost" placeholder="0.0"')?></td>
					  <td><?php echo Form::text('cantidad-1',' class="qty" placeholder="0.0"');?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-1' class="price">0.00</span></td>
				  </tr>
			<?php }  
			?>
            <tr id="hiderow">
		    <td colspan="12"><a id="addrow" href="javascript:;" title="Agregar mas registros">Agragar Mas</a></td>
		  </tr>
  </table>
          <table width="100%" class="meta">
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Sub Total : </strong></td>
		      <td width="11%" class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="subtotal"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Valor Venta : </strong></td>
		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid_total','size="10"',number_format($subt,2,'.',''));?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>IGV : </strong></td>

		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid','placeholder="0.00" readonly="readonly"')?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance"><strong>Total a Pagar : </strong> </td>
		      <td class="total-value balance"><span class="simbolo"><?php echo $simbolo?></span><span class="due"><?php echo number_format($subtotal+$igv,2,'.','')?></span></td>
		  </tr>
			<?php
			
			; break;
			/*NOTA DE CREDITO*/
case '07':
			?><tr>
		      
              <th width="42%">Concepto</th>
		      <th width="18%">Precio</th>
		  </tr>
          <?php
			if($DETALLE)
			{
				$n=0;
				$subtotal=0;
				foreach($detalles as $item){
						  $n++;
						  $subtotal=$subtotal+$item->importe;
					  ?>
				  <tr id="<?php echo $n?>" class="item-row">
				   <td>
						  <div class="delete-wpr">
						  <?php echo Form::textArea('concepto'.$n,'rows="5" cols="100"',$item->concepto)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						  </div>
					</td>
                    <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"',number_format($item->preciounitario,2,'.',''))?></td>
					  </tr>
				  
				  <?php
					  }/*FIN FOREACH*/
					if($n==0)
						{  $n=1; ?>
					<tr id="1" class="item-row">
						<td>
						  <div class="delete-wpr">
						  <?php echo Form::textArea('concepto'.$n,'rows="5" cols="100"')?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
                    <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"')?></td>
				</tr>
				  <?php
						}?>
				  <?php 
				  }else{
					 $n=1; ?>
				  <tr id="1" class="item-row">
					 <td>
						  <div class="delete-wpr">
						  <?php echo Form::textArea('concepto'.$n,'rows="5" cols="100"')?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
                    <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost grabar" placeholder="0.0"')?></td>
				</tr>
			<?php }  
			?>
            <?php /*?><tr id="hiderow">
		    <td colspan="12"><a id="addrow" href="javascript:;" title="Agregar mas registros">Agragar Mas</a></td>
		  </tr><?php */?>
  </table>
          <table width="100%" class="meta">
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Total: </strong></td>
		      <td width="10%" class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="subtotal"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Total: </strong></td>
		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid_total','size="10"',number_format($subtotal,2,'.',''));?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>IGV: </strong></td>

		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid','placeholder="0.00" readonly="readonly"')?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance"><strong>Total Neto Recibido : </strong> </td>
		      <td class="total-value balance"><span class="simbolo"><?php echo $simbolo?></span><span class="due"><?php echo number_format($subtotal+$igv,2,'.','')?></span></td>
		  </tr>
			<?php
			
			; break;
			/*NOTA DE DEBITO*/
case '08':
			?><tr>
		      
              <th width="42%">Concepto</th>
		      <th width="18%">Precio</th>
		  </tr>
          <?php
			if($DETALLE)
			{
				$n=0;
				$subtotal=0;
				foreach($detalles as $item){
						  $n++;
						  $subtotal=$subtotal+$item->importe;
					  ?>
				  <tr id="<?php echo $n?>" class="item-row">
				   <td>
						  <div class="delete-wpr">
						  <?php echo Form::textArea('concepto'.$n,'rows="5" cols="100"',$item->concepto)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						  </div>
					</td>
                    <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"',number_format($item->preciounitario,2,'.',''))?></td>
					  </tr>
				  
				  <?php
					  }/*FIN FOREACH*/
					if($n==0)
						{  $n=1; ?>
					<tr id="1" class="item-row">
						<td>
						  <div class="delete-wpr">
						  <?php echo Form::textArea('concepto'.$n,'rows="5" cols="100"')?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
                    <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"')?></td>
				</tr>
				  <?php
						}?>
				  <?php 
				  }else{
					 $n=1; ?>
				  <tr id="1" class="item-row">
					 <td>
						  <div class="delete-wpr">
						  <?php echo Form::textArea('concepto'.$n,'rows="5" cols="100"')?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
                    <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost grabar" placeholder="0.0"')?></td>
				</tr>
			<?php }  
			?>
            <?php /*?><tr id="hiderow">
		    <td colspan="12"><a id="addrow" href="javascript:;" title="Agregar mas registros">Agragar Mas</a></td>
		  </tr><?php */?>
  </table>
          <table width="100%" class="meta">
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Total: </strong></td>
		      <td width="10%" class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="subtotal"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Total: </strong></td>
		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid_total','size="10"',number_format($subtotal,2,'.',''));?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>IGV: </strong></td>

		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid','placeholder="0.00" readonly="readonly"')?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance"><strong>Total Neto Recibido : </strong> </td>
		      <td class="total-value balance"><span class="simbolo"><?php echo $simbolo?></span><span class="due"><?php echo number_format($subtotal+$igv,2,'.','')?></span></td>
		  </tr>
			<?php
			;break;
			/*GUIA REMISION*/
case '09': ?>
		<tr>
		      <th width="9%">Codigo</th>
		      <th width="25%">Producto</th>
		      <th width="14%">Medida</th>
		      <th>Color</th>
              <th>Lote</th>
		      <th width="5%">C/B</th>
		      <th width="10%">Bobinas</th>
              <th width="5%">PN/Cant</th>
		      <th width="9%">P/B</th>
		  </tr>
          <?php
			if($DETALLE)
			{
				$n=0;
				$subtotal=0;
				foreach($detalles as $item){
						  $n++;
						  $subtotal=$subtotal+$item->importe;
					  ?>
				  <tr id="<?php echo $n?>" class="item-row">
				   <td>
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-'.$n,'size="10" readonly="readonly"',$item->getTesproductos()->codigo)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						  </div>
					</td>
					  <td class="item-name">
						  <?php echo Form::hidden('productos_id-'.$n,'class="productos_id"',$item->tesproductos_id)?>
						  <span id="ver-name<?php echo $n?>" style="display:none;"><?php echo Form::text('productoname-'.$n,'class="producto"')?></span>
						  <span id="ver<?php echo $n;?>" class="ver"><?php echo Form::text('producto'.$n,'class="producto" placeholder="Producto" size="30" ',$item->getTesproductos()->nombre)?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-'.$n,'readonly="readonly"',$item->tesunidadesmedidas_id)?> <?php echo Form::text('tesunidadesmedidas'.$n,'class="unidad" size="10"',$item->getTesunidadesmedidas()->nombre)?></td>
					  <td><?php echo Form::hidden('tescolores_id-'.$n,'readonly="readonly"',$item->tescolores_id)?> <?php echo Form::text('tescolores'.$n,'class="color" size="10"',$item->getTescolores()->nombre)?></td>
					  <td> <?php echo Form::text('lote'.$n,'class="save" size="10"',$item->lote)?></td>
					  <td> <?php echo Form::text('empaque'.$n,'class="save cajas" data-id="'.$item->id.'" size="10"',$item->empaque)?><div class="flotante" id="cajas-<?php echo $n?>"></div></td>
					  <td> <?php echo Form::text('bobinas'.$n,'class="save" size="10"',$item->bobinas)?></td>
					  <td> <?php echo Form::text('pesoneto'.$n,'class="save" size="10"',$item->pesoneto)?></td>
					  <td> <?php echo Form::text('pesobruto'.$n,'class="save" size="10"',$item->pesobruto)?></td>
				  </tr>
				  
				  <?php
					  }/*FIN FOREACH*/
					if($n==0)
						{  $n=1; ?>
						  <tr id="1" class="item-row">
							 <td class="item-name">
								  <div class="delete-wpr">
								  <?php echo Form::text('codigo-1','class="productos_id" size="10" readonly="readonly"')?>
								  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
								  </div>
							</td>
							  <td class="item-name">
								 <?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
								  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
								  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
							  </td>
							  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td width="17%"><?php echo Form::hidden('tescolores_id-'.$n,'readonly="readonly"')?> <?php echo Form::text('tescolores'.$n,'class="color" size="10"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'class="save" size="10"')?></td>
					  <td> <?php echo Form::text('empaque'.$n,'data-id="0" class="save cajas" size="10"')?><div class="flotante" id="cajas-<?php echo $n?>"></div></td>
					  <td> <?php echo Form::text('bobinas'.$n,'class="save" size="5" ')?></td>
					  <td> <?php echo Form::text('pesoneto'.$n,'class="save" size="5"')?></td>
					  <td> <?php echo Form::text('pesobruto'.$n,'class="save" size="5"')?></td>
							 
						  </tr>
				  <?php
						}?>
				  <?php 
				  }else{
					 $n=1; ?>
				  <tr id="1" class="item-row">
					 <td>
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-1','class="productos_id" readonly="readonly"')?>
						  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
					  <td>
						 <?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
						  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
						  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td><?php echo Form::hidden('tescolores_id-'.$n,'readonly="readonly"')?> <?php echo Form::text('tescolores'.$n,'class="color" size="10"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'class="save" size="5"')?></td>
					  <td> <?php echo Form::text('empaque'.$n,'data-id="0" size="5" class="save cajas"')?><div class="flotante" id="cajas-<?php echo $n?>"></div></td>
					  <td> <?php echo Form::text('bobinas'.$n,'class="save" size="5"')?></td>
					  <td> <?php echo Form::text('pesoneto'.$n,'class="save" size="5"')?></td>
					  <td> <?php echo Form::text('pesobruto'.$n,'class="save" size="5"')?></td>
					  
				  </tr>
			<?php }  
			?>
            <tr id="hiderow">
		    <td colspan="12"><a id="addrow" href="javascript:;" title="Agregar mas registros">Agragar Mas</a></td>
		  </tr>
  </table>
          <table width="100%" class="meta">
		  <?php /*?><tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Sub Total : </strong></td>
		      <td width="24%" class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="subtotal"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Valor Venta : </strong></td>
		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="paid_total"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>IGV : </strong></td>

		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid','placeholder="0.00" readonly="readonly"')?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance"><strong>Total a Pagar : </strong> </td>
		      <td class="total-value balance"><span class="simbolo"><?php echo $simbolo?></span><span class="due"><?php echo number_format($subtotal+$igv,2,'.','')?></span></td>
		  </tr><?php */?>
			<?php ;break;
			/*RECIBO POR ARRENDAMIENTO*/
case '10':
			?><tr>
		      <th width="9%">Codigo</th>
		      <th width="15%">Producto</th>
		      <th width="12%">Medida</th>
              <th width="6%">Lote</th>
		      <th width="11%">Empaque</th>
		      <th width="10%">Bobinas</th>
              <th width="5%">P/N</th>
		      <th width="5%">P/B</th>
		      <th width="8%">Costo Unit</th>
		      <th width="11%">Cantidad</th>
		      <th width="8%">Precio</th>
		  </tr>
          <?php
			if($DETALLE)
			{
				$n=0;
				$subtotal=0;
				foreach($detalles as $item){
						  $n++;
						  $subtotal=$subtotal+$item->importe;
					  ?>
				  <tr id="<?php echo $n?>" class="item-row">
				   <td>
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-'.$n,'size="10" readonly="readonly"',$item->getTesproductos()->codigo)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						  </div>
					</td>
					  <td class="item-name">
						  <?php echo Form::hidden('productos_id-'.$n,'class="productos_id" readonly="readonly"',$item->tesproductos_id)?>
						  <span id="ver-name<?php echo $n?>" style="display:none;"><?php echo Form::text('productoname-'.$n,'readonly="readonly"')?></span>
						  <span id="ver<?php echo $n;?>" class="ver"><?php echo Form::text('producto'.$n,'class="producto" placeholder="Producto" size="30" readonly="readonly"',$item->getTesproductos()->nombre)?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-'.$n,'readonly="readonly"',$item->tesunidadesmedidas_id)?> <?php echo Form::text('tesunidadesmedidas'.$n,'class="unidad" size="10"',$item->getTesunidadesmedidas()->nombre)?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="10"',$item->lote)?></td>
					  <td> <?php echo Form::text('empaque'.$n,'size="10"',$item->empaque)?></td>
					  <td> <?php echo Form::text('bobinas'.$n,'size="10"',$item->bobinas)?></td>
					  <td> <?php echo Form::text('pesoneto'.$n,'size="10"',$item->pesoneto)?></td>
					  <td> <?php echo Form::text('pesobruto'.$n,'size="10"',$item->pesobruto)?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"',number_format($item->preciounitario,2,'.',''))?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="0.0"',$item->cantidad);?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price"><?php echo number_format($item->importe,2,'.','')?></span></td>
				  </tr>
				  
				  <?php
					  }/*FIN FOREACH*/
					if($n==0)
						{  $n=1; ?>
						  <tr id="1" class="item-row">
							 <td class="item-name">
								  <div class="delete-wpr">
								  <?php echo Form::text('codigo-1','class="productos_id" size="10" readonly="readonly"')?>
								  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
								  </div>
							</td>
							  <td class="item-name">
								 <?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
								  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
								  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
							  </td>
							  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('empaque'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('bobinas'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('pesoneto'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('pesobruto'.$n,'size="5"')?></td>
							  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-1','size="5" class="cost" placeholder="0.0"')?></td>
							  <td><?php echo Form::text('cantidad-1',' class="qty" size="5" placeholder="0.0"');?></td>
							  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-1' class="price">0.00</span></td>
						  </tr>
				  <?php
						}?>
				  <?php 
				  }else{
					 $n=1; ?>
				  <tr id="1" class="item-row">
					 <td class="item-name">
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-1','class="productos_id" readonly="readonly"')?>
						  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
					  <td class="item-name">
						 <?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
						  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
						  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('empaque'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('bobinas'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('pesoneto'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('pesobruto'.$n,'size="5"')?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-1','class="cost" placeholder="0.0"')?></td>
					  <td><?php echo Form::text('cantidad-1',' class="qty" placeholder="0.0"');?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-1' class="price">0.00</span></td>
				  </tr>
			<?php }  
			?>
            <tr id="hiderow">
		    <td colspan="12"><a id="addrow" href="javascript:;" title="Agregar mas registros">Agragar Mas</a></td>
		  </tr>
  </table>
          <table width="100%" class="meta">
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Sub Total : </strong></td>
		      <td width="15%" class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="subtotal"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Valor Venta : </strong></td>
		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="paid_total"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>IGV : </strong></td>

		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid','placeholder="0.00" readonly="readonly"')?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance"><strong>Total a Pagar : </strong> </td>
		      <td class="total-value balance"><span class="simbolo"><?php echo $simbolo?></span><span class="due"><?php echo number_format($subtotal+$igv,2,'.','')?></span></td>
		  </tr>
			<?php ;break;
			/*TICKET DE MAQUINA REGISTRADOR*/
case '12':
			?><tr>
		      
              <th width="34%">Concepto</th>
              <th>Producto</th>
		      <th width="18%">Importe sin IGV</th>
		      <th width="19%">Cantidad</th>
		      <th width="14%">Precio</th>
		  </tr>
          <?php
			if($DETALLE)
			{
				$n=0;
				$subtotal=0;
				foreach($detalles as $item){
						  $n++;
						  $subtotal=$subtotal+$item->importe;
					  ?>
				  <tr id="<?php echo $n?>" class="item-row">
				   <td>
						  <div class="delete-wpr">
						  <?php echo Form::text('concepto'.$n,'size="50"',$item->concepto)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						  </div>
					</td>
                    <td class="item-name">
						  <?php echo Form::hidden('productos_id-'.$n,'class="productos_id" readonly="readonly"',$item->tesproductos_id)?>
						  <span id="ver-name<?php echo $n?>" style="display:none;"><?php echo Form::text('productoname-'.$n,' size="30" readonly="readonly"')?></span>
						  <span id="ver<?php echo $n;?>" class="ver"><?php 
						  if(!empty($item->getTesproductos()->nombre))echo Form::text('producto'.$n,'class="producto" placeholder="Producto" readonly="readonly"',$item->getTesproductos()->nombre); else echo " - ";?></span>
					  </td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"',number_format($item->preciounitario,2,'.',''))?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="1"',$item->cantidad);?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price"><?php echo number_format($item->importe,2,'.','')?></span></td>
				  </tr>
				  
				  <?php
					  }/*FIN FOREACH*/
					if($n==0)
						{  $n=1; ?>
						  <tr id="1" class="item-row">
						<td>
						  <div class="delete-wpr">
						  <?php echo Form::text('concepto'.$n,'size="50"')?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
                    <td class="item-name">
						  <?php echo Form::hidden('productos_id-'.$n,'class="productos_id" readonly="readonly"')?>
						  <span id="ver-name<?php echo $n?>" style="display:none;"><?php echo Form::text('productoname-'.$n,' size="30" readonly="readonly"')?></span>
						  <span id="ver<?php echo $n;?>" class="ver"><?php echo Form::text('producto'.$n,'class="producto" placeholder="Producto"')?></span>
					  </td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"')?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="1"');?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price">0.00</span></td>
						  </tr>
				  <?php
						}?>
				  <?php 
				  }else{
					 $n=1; ?>
				  <tr id="1" class="item-row">
					 <td>
						  <div class="delete-wpr">
						  <?php echo Form::text('concepto'.$n,'size="50"')?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
                    <td class="item-name">
						  <?php echo Form::hidden('productos_id-'.$n,'class="productos_id" readonly="readonly"')?>
						  <span id="ver-name<?php echo $n?>" style="display:none;"><?php echo Form::text('productoname-'.$n,' size="30" readonly="readonly"')?></span>
						  <span id="ver<?php echo $n;?>" class="ver"><?php echo Form::text('producto'.$n,'class="producto" placeholder="Producto"')?></span>
					  </td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"')?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="1"');?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price">0.00</span></td>
				  </tr>
			<?php }  
			?>
            <tr id="hiderow">
		    <td colspan="12"><a id="addrow" href="javascript:;" title="Agregar mas registros">Agragar Mas</a></td>
		  </tr>
  </table>
          <table width="100%" class="meta">
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Sub Total : </strong></td>
		      <td width="12%" class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="subtotal"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Valor Venta : </strong></td>
		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="paid_total"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>IGV : </strong></td>

		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid','placeholder="0.00" readonly="readonly"')?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance"><strong>Total a Pagar : </strong> </td>
		      <td class="total-value balance"><span class="simbolo"><?php echo $simbolo?></span><span class="due"><?php echo number_format($subtotal+$igv,2,'.','')?></span></td>
		  </tr>
			<?php ;break;
			/*RECIBO POR SERVICIOS*/
case '14':
			?><tr>
		      
              <th width="37%">Concepto</th>
		      <th width="18%">Importe sin IGV</th>
		      <th width="19%">Cantidad</th>
		      <th width="14%">Precio</th>
		  </tr>
          <?php
			if($DETALLE)
			{
				$n=0;
				$subtotal=0;
				foreach($detalles as $item){
						  $n++;
						  $subtotal=$subtotal+$item->importe;
					  ?>
				  <tr id="<?php echo $n?>" class="item-row">
				   <td>
						  <div class="delete-wpr">
						  <?php echo Form::text('concepto'.$n,'size="100"',$item->concepto)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						  </div>
					</td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"',number_format($item->preciounitario,2,'.',''))?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="1"',$item->cantidad);?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price"><?php echo number_format($item->importe,2,'.','')?></span></td>
				  </tr>
				  
				  <?php
					  }/*FIN FOREACH*/
					if($n==0)
						{  $n=1; ?>
						  <tr id="1" class="item-row">
						<td>
						  <div class="delete-wpr">
						  <?php echo Form::text('concepto'.$n,'size="100"')?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"')?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="1"');?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price">0.00</span></td>
						  </tr>
				  <?php
						}?>
				  <?php 
				  }else{
					 $n=1; ?>
				  <tr id="1" class="item-row">
					 <td>
						  <div class="delete-wpr">
						  <?php echo Form::text('concepto'.$n,'size="100"')?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"')?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="1"');?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price">0.00</span></td>
				  </tr>
			<?php }  
			?>
            <tr id="hiderow">
		    <td colspan="12"><a id="addrow" href="javascript:;" title="Agregar mas registros">Agragar Mas</a></td>
		  </tr>
  </table>
          <table width="100%" class="meta">
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Sub Total : </strong></td>
		      <td width="11%" class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="subtotal"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Valor Venta : </strong></td>
		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="paid_total"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>IGV : </strong></td>

		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid','placeholder="0.00" readonly="readonly"')?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance"><strong>Total a Pagar : </strong> </td>
		      <td class="total-value balance"><span class="simbolo"><?php echo $simbolo?></span><span class="due"><?php echo number_format($subtotal+$igv,2,'.','')?></span></td>
		  </tr>
			<?php ;break;
			/*RECIBO POR SERVICIOS*/
case '101':
			?>
         <tr>
         <td></td>
         </tr>
			<?php ;break;
/*###  CHEQUE  ###*/
case '104':
			?>
         <tr>
         <td></td>
         </tr>
			<?php ;break;
		
			/*ORIGINAL*/
			default:
			?><tr>
		      <th width="8%">Codigo</th>
		      <th width="17%">Producto</th>
		      <th width="10%">Medida</th>
              <th width="7%">Lote</th>
		      <th width="11%">Empaque</th>
		      <th width="10%">Bobinas</th>
              <th width="5%">P/N</th>
		      <th width="5%">P/B</th>
		      <th width="8%">Costo Unit</th>
		      <th width="11%">Cantidad</th>
		      <th width="8%">Precio</th>
		  </tr>
          <?php
			if($DETALLE)
			{
				$n=0;
				$subtotal=0;
				foreach($detalles as $item){
						  $n++;
						  $subtotal=$subtotal+$item->importe;
					  ?>
				  <tr id="<?php echo $n?>" class="item-row">
				   <td>
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-'.$n,'size="10" readonly="readonly"',$item->getTesproductos()->codigo)?>
						  <a id="del-<?php echo $n?>" class="delete" href="javascript:;" title="Elimar registro" data-id="<?php echo $item->id?>">X</a>
						  </div>
					</td>
					  <td class="item-name">
						  <?php echo Form::hidden('productos_id-'.$n,'class="productos_id" readonly="readonly"',$item->tesproductos_id)?>
						  <span id="ver-name<?php echo $n?>" style="display:none;"><?php echo Form::text('productoname-'.$n,'readonly="readonly"')?></span>
						  <span id="ver<?php echo $n;?>" class="ver"><?php echo Form::text('producto'.$n,'class="producto" placeholder="Producto" size="30" readonly="readonly"',$item->getTesproductos()->nombre)?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-'.$n,'readonly="readonly"',$item->tesunidadesmedidas_id)?> <?php echo Form::text('tesunidadesmedidas'.$n,'class="unidad" size="10"',$item->getTesunidadesmedidas()->nombre)?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="10"',$item->lote)?></td>
					  <td> <?php echo Form::text('empaque'.$n,'size="10"',$item->empaque)?></td>
					  <td> <?php echo Form::text('bobinas'.$n,'size="10"',$item->bobinas)?></td>
					  <td> <?php echo Form::text('pesoneto'.$n,'size="10"',$item->pesoneto)?></td>
					  <td> <?php echo Form::text('pesobruto'.$n,'size="10"',$item->pesobruto)?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-'.$n,'size="10" class="cost" placeholder="0.0"',number_format($item->preciounitario,2,'.',''))?></td>
					  <td><?php echo Form::text('cantidad-'.$n,' class="qty" size="10" placeholder="0.0"',$item->cantidad);?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-<?php echo $n?>' class="price"><?php echo number_format($item->importe,2,'.','')?></span></td>
				  </tr>
				  
				  <?php
					  }/*FIN FOREACH*/
					if($n==0)
						{  $n=1; ?>
						  <tr id="1" class="item-row">
							 <td class="item-name">
								  <div class="delete-wpr">
								  <?php echo Form::text('codigo-1','class="productos_id" size="10" readonly="readonly"')?>
								  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
								  </div>
							</td>
							  <td class="item-name">
								 <?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
								  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
								  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
							  </td>
							  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('empaque'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('bobinas'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('pesoneto'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('pesobruto'.$n,'size="5"')?></td>
							  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-1','size="5" class="cost" placeholder="0.0"')?></td>
							  <td><?php echo Form::text('cantidad-1',' class="qty" size="5" placeholder="0.0"');?></td>
							  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-1' class="price">0.00</span></td>
						  </tr>
				  <?php
						}?>
				  <?php 
				  }else{
					 $n=1; ?>
				  <tr id="1" class="item-row">
					 <td class="item-name">
						  <div class="delete-wpr">
						  <?php echo Form::text('codigo-1','class="productos_id" readonly="readonly"')?>
						  <a id="del-1" class="delete" href="javascript:;" title="Elimar registro" data-id="0">X</a>
						  </div>
					</td>
					  <td class="item-name">
						 <?php echo Form::hidden('productos_id-1','class="productos_id" readonly="readonly"')?>
						  <span id="ver-name1" style="display:none;"><?php echo Form::text('productoname-1','readonly="readonly"')?></span>
						  <span id="ver1" class="ver"><?php echo Form::text('producto1','class="producto" placeholder="Producto" size="30"')?></span>
					  </td>
					  <td><?php echo Form::hidden('tesunidadesmedidas_id-1')?> <?php echo Form::text('tesunidadesmedidas1','class="unidad"')?></td>
					  <td> <?php echo Form::text('lote'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('empaque'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('bobinas'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('pesoneto'.$n,'size="5"')?></td>
					  <td> <?php echo Form::text('pesobruto'.$n,'size="5"')?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('precio-1','class="cost" placeholder="0.0"')?></td>
					  <td><?php echo Form::text('cantidad-1',' class="qty" placeholder="0.0"');?></td>
					  <td><span class="simbolo"><?php echo $simbolo?></span><span id='total-1' class="price">0.00</span></td></tr>
			<?php } 
			?><tr id="hiderow">
		    <td colspan="12"><a id="addrow" href="javascript:;" title="Agregar mas registros">Agragar Mas</a></td>
		  </tr>
  </table>
          <table class="meta">
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Sub Total : </strong></td>
		      <td width="24%" class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="subtotal"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>Valor Venta : </strong></td>
		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><span id="paid_total"><?php echo number_format($subtotal,2,'.','')?></span></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line"><strong>IGV : </strong></td>

		      <td class="total-value"><span class="simbolo"><?php echo $simbolo?></span><?php echo Form::text('paid','placeholder="0.00" readonly="readonly"')?></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance"><strong>Total a Pagar : </strong> </td>
		      <td class="total-value balance"><span class="simbolo"><?php echo $simbolo?></span><span class="due"><?php echo number_format($subtotal+$igv,2,'.','')?></span></td>
		  </tr>
			<?php
			;break;
		}
	}
}
?>