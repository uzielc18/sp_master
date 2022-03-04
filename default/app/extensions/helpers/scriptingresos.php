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
############
*/

class Scriptingresos
{
	
public static function script($tipo,$simbolo,$totalconigv,$igv,$cliente_id,$nombre,$cuantagastos,$cuantagastos_name,$cuentaporpagar,$cuentaporpagar_name,$module_name)
	
	{$igv=Session::get('IGV');
		$code='';
switch($tipo)
{
		/*FACTURAS*/
case '01':
//inicio de ready
$code.='$(document).ready(function()
		{	
			bind();  
			$("input").click(function(){
    		$(this).select();
			});';
$code.='$("#paid").blur(function(){update_balance();});
		$("#paid_total").blur(function(){update_balance();});';
   /*
  Para grabar el tr - id buscando la clase :save y mandando todo el contenido del tr:
  */
$code.='$("#addrow").click(function()
		{
			var ID = $(".item-row:last").attr("id");
			grabarDetalle(ID);
			var IDnew=Number(ID)+Number(1);
			var tr=\''.Documentos::tr(Session::get('DOC_CODIGO'),$simbolo).'\';
    		$(".item-row:last").after(tr);
    		if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
			bind();
  		});';
/* ------- FIN contenido del tr: ------- */
$code.='$(".delete").live("click",function()
		{
		  var row =$(this).parents(".item-row")
		  var id=row.find(\'.delete\').attr(\'data-id\');
		  EliminarDetalle(id);
			$(this).parents(\'.item-row\').remove();
		  	update_total();
			if ($(".delete").length < 2) $(".delete").hide();
	    });';
/**/
  
  		//$("#pre_descripcion").blur(function(){$("#observacion").val($(this).val());GRABAR();});

$code.='$("#paid").blur(function(){$("#descuento").val($(this).val());GRABAR();});';
$code.='$("#glosa").change(function(){GRABAR();});';
$code.='$("#femision").change(function(){GRABAR();});';
$code.='$("#numero").blur(function(){GRABAR();});';
$code.='$(".grabar_form").blur(function(){GRABAR();});';
$code.='$("#monedas").change(function()
		{
			GRABAR();
			var op = $("#monedas option:selected").val();
			if(op==1)
			{
			$(".simbolo").html("S/. ");
			}
			if(op==2)
			{
			$(".simbolo").html("$. ");
			}
		});';
/*Busqueda del Cliente*/
$code.='$("#CL").tokenInput("/'.$module_name.'/ingresos/buscarcliente", 
		{
			tokenLimit: 1,
			minChars: 2,';
	if($cliente_id!=0){
	$code.='prePopulate: [ 
						 {id: '.$cliente_id.', name: "'.$nombre.'"},
						 ],';
			}
	$code.='onAdd: function (item){
			   var VAL=item.id;
			   $("#cliente_id").val(VAL);
			   $("#customer-title").val(item.name);
			},
			onDelete: function (item) {
			}
		});';
/*---FIN Busqueda del Cliente*/	
/* Numero de Guia */
$code.='if($("#numeroguia").val()!="")
		{
			$("#numeroguia").tokenInput("/'.$module_name.'/ingresos/guiadelafactura/", 
			{
			tokenLimit: 1,
			minChars: 1,
			prePopulate: [ 
						 {id: $("#numeroguia").val(), name: ""+$("#numeroguia").val()+""},
						],
			onAdd: function (item) {
			   var ID=item.name.split("-");
				$.ajax({
				type:"POST",
				url:"/'.$module_name.'/ingresos/facturaguia/"+ID[0]+"/"+ID[1],
				beforeSend:function(){
				  $("#loading").show();
				},
				success:function(response){
					$("#loading").hide();
					GRABAR();
					window.location = "/'.$module_name.'/ingresos/crear";
				}
				});
			},
			onDelete: function (item)
			{
				$.ajax({
				type:"POST",
				url:"/'.$module_name.'/ingresos/facturaguia/0/0",
				beforeSend:function(){
				  $("#loading").show();
				},
				success:function(response){
					$("#loading").hide();
					window.location = "/'.$module_name.'/ingresos/crear";
				}
				});
				}
			});
		}else{
			$("#numeroguia").tokenInput("/'.$module_name.'/ingresos/guiadelafactura/", 
			{
				
			tokenLimit: 1,
			minChars: 1,
			onAdd: function (item) {
			   var ID=item.name.split("-");
				$.ajax({
				type:"POST",
				url:"/'.$module_name.'/ingresos/facturaguia/"+ID[0]+"/"+ID[1],
				beforeSend:function(){
				  $("#loading").show();
				},
				success:function(response){
					$("#loading").hide();
					GRABAR();
					window.location = "/'.$module_name.'/ingresos/crear";
				}
				});
			},
			onDelete: function (item) {
				$.ajax({
				type:"POST",
				url:"/'.$module_name.'/ingresos/facturaguia/0/0",
				beforeSend:function(){
				  $("#loading").show();
				},
				success:function(response){
					$("#loading").hide();
					window.location = "/'.$module_name.'/ingresos/crear";
				}
				});
				}
			});
		}';
/*fin numero de Guia*/

if(Session::get('DOC_CODIGO')!='09'){
	
/*Busqueda del Cuentas de Gastos*/
$code.='$("#cuantagastos").tokenInput("/'.$module_name.'/ingresos/cuentasG", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuantagastos)){
$code.='prePopulate: [ 
		             {id: '.$cuantagastos.', name: "'.$cuantagastos_name.'"},
					 ],';
		}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   $("#customer-title").val(item.name);
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/*Busqueda del Cuentas Po pagar*/
$code.='$("#cuentaporpagar").tokenInput("/'.$module_name.'/ingresos/cuentasP", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuentaporpagar)){/*cuantagastos*/
$code.='prePopulate: [ 
		             {id: '.$cuentaporpagar.', name: "'.$cuentaporpagar_name.'"},
					 ],';
}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   $("#customer-title").val(item.name);
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/* FINN Busqueda del Cuentas Po pagar*/

$code.='$("#XC").click(function(){
		$("#BC").show();
		$("#cliente_id").val(\'\');
		$("#customer-title").val(\'\');
		});';
}

$code.='});';
$code.='function roundNumber(number,decimals)
		{
  			var newString;// The new rounded number
  			decimals = Number(decimals);
  			if (decimals < 1) 
			{
    			newString = (Math.round(number)).toString();
  			}else
			{
    			var numString = number.toString();
    			if (numString.lastIndexOf(".") == -1)
				{// If there is no decimal point
      				numString += ".";// give it one at the end
    			}
    			var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
    			var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we\'ll end up with
    			var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
    			if (d2 >= 5)
				{// Do we need to round up at all? If not, the string will just be truncated
      				if (d1 == 9 && cutoff > 0)
					{// If the last digit is 9, find a new cutoff point
        				while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          				if (d1 != ".")
						{
            				cutoff -= 1;
            				d1 = Number(numString.substring(cutoff,cutoff+1));
         				} else {
            				cutoff -= 1;
          				}
        			}
      			}
      			d1 += 1;
    		} 
    		if (d1 == 10)
			{
      			numString = numString.substring(0, numString.lastIndexOf("."));
      			var roundedNum = Number(numString) + 1;
      			newString = roundedNum.toString() + \'.\';
    		}else
			{
				newString = numString.substring(0,cutoff) + d1.toString();
		    }
  		}
  		if (newString.lastIndexOf(".") == -1) 
		{// Do this again, to the new string
    		newString += ".";
  		}
  		var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
  		for(var i=0;i<decimals-decs;i++) newString += "0";
  		//var newNumber = Number(newString);// make it a number if you like
		return newString; // Output the result to the form field (change for your purposes)
		}';
/*FIN DE REDONDEO*/
$code.='function update_total()
		{
  			var total = 0.00;
  			$(\'.price\').each(function(i)
  			{
    			price = $(this).html();
				if(!isNaN(price)) total =parseFloat(total)+parseFloat(price);
   			});
			//alert(total);
			total = roundNumber(total,2);
			//alert(total);
			var igv= total*'.$igv.';
			var totalconigv=(parseFloat(total)+parseFloat(igv));
			$("#paid").val(igv.toFixed(2));
			$("#igv").val(igv.toFixed(2));
			$("#totalconigv").val(totalconigv);
			$(\'#subtotal\').html(total);
			$(\'#paid_total\').val(total);
			update_balance();
		}';

$code.='function update_balance()
{
    var igv= parseFloat( $("#paid_total").val())*'.$igv.';
	//alert($("#paid_total").val());
	//alert(igv);
	var igv=roundNumber(igv,2);
	var totalconigv=(parseFloat($("#paid_total").val())+parseFloat(igv));
	//alert("despues de sumar "+totalconigv);
	var due=roundNumber(totalconigv,2);
	//alert("despues de round "+due);
	var IGV=igv;
	$("#paid").val(IGV);
	$("#igv").val(IGV);
	$("#totalconigv").val(due);
	$(\'.due\').html(due);
	$("#moneda_en_letras").html(covertirNumLetras(due));
	$("#totalenletras").val(covertirNumLetras(due));
	GRABAR();
}';

$code.='function update_price(ID)
{
  var I=ID.split(\'-\');
  var row = $(this).parents(\'.item-row\');
  var price = (parseFloat($("#precio-"+I[1]).val())*parseFloat($("#cantidad-"+I[1]).val()));
  price = price.toFixed(2);
  isNaN(price) ? $("#total-"+I[1]).html("N/A") : $("#total-"+I[1]).html(price);
  update_total();
  grabarDetalle(I[1]);
}';

$code.='function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function()
  {
	 var ID = $(this).attr("id");
	 update_price(ID);
  });
  $(".qty").blur(function(){var ID = $(this).attr("id");
	  update_price(ID);
	 });
  $(".producto").click(function(){buscarProducto();});
  $(".unidad").click(function(){buscarUnidad();});
}';

/*BUSCAR PRODUCTO*/
	/*  */		
$code.='function buscarProducto()
{
	var row = $(this).parents(\'.item-row:last\');
	var ID = $(\'.item-row:last\').attr(\'id\');
	$("#productos_id-"+ID).val();
	if($("#productos_id-"+ID).val()!=\'\'){
	$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{
		tokenLimit: 1,
		minChars: 1,
		
		prePopulate: [ 
		             {id: $("#productos_id-"+ID).val(), name: ""+$("#producto"+ID).val()+""},
					],
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		    row.find(\'.productos_id\').val(\'\');
		    row.find(\'.pro_descripcion\').val(\'\');
		    row.find(\'.cost\').val(\'\');
		    row.find(\'.qty\').val(\'\');
		    var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}else
	{
		$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{			
		tokenLimit: 1,
		minChars: 1,
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(\'\');
		   row.find(\'.pro_descripcion\').val(\'\');
		   row.find(\'.cost\').val(\'\');
		   row.find(\'.qty\').val(\'\');
		   var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}
}';
/*BUSCAR PRODUCTO*/
	/*  */		
$code.='function buscarUnidad()
{
	var row = $(this).parents(\'.item-row:last\');
	var ID = $(\'.item-row:last\').attr(\'id\');
	if($("#tesunidadesmedidas"+ID).val()!=\'\'){
	$("#tesunidadesmedidas"+ID).tokenInput("/'.$module_name.'/ingresos/medidas/", 
		{			
		tokenLimit: 1,
		minChars: 1,
		prePopulate: [ 
		             {id: $("#tesunidadesmedidas_id-"+ID).val(), name: ""+$("#tesunidadesmedidas"+ID).val()+""},
					],
		onAdd: function (item) {
		   var VAL=item.id;
		   $("#tesunidadesmedidas_id-"+ID).val(item.id);
		   $("#tesunidadesmedidas"+ID).val(item.name);
		   grabarDetalle(ID);
		},
		onDelete: function (item) {
		  $("#tesunidadesmedidas_id-"+ID).val(\'\');
		   $("#tesunidadesmedidas"+ID).val(\'\');
		}
	});
	}else
	{
	$("#tesunidadesmedidas"+ID).tokenInput("/'.$module_name.'/ingresos/medidas/", 
		{
			
		tokenLimit: 1,
		minChars: 1,
		onAdd: function (item) {
		   var VAL=item.id;
		   $("#tesunidadesmedidas_id-"+ID).val(item.id);
		   $("#tesunidadesmedidas"+ID).val(item.name);
		   grabarDetalle(ID);
		},
		onDelete: function (item) {
		  $("#tesunidadesmedidas_id-"+ID).val(\'\');
		   $("#tesunidadesmedidas"+ID).val(\'\');
		}
	});
	}
}';

/*envio dew Formulario PROFORMAS*/
$code.='function GRABAR()
{
	//alert($("#inventarios").serialize());
     $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabar/1",
            data:$("#inventarios").serialize(),
            beforeSend:function(){
              $("#loading").show();
          	},
            success:function(response){
                $("#loading").hide();
				$("#id").val(response);
				//alert(response);
            }
 
          	});
 
      };';

/*GRABAR DETALLE DE PROFORMAS*/

$code.='function grabarDetalle(tr_id) 
{
  //alert(tr_id);
  var producto_id=$(\'#productos_id-\'+tr_id).val();
  var pro_descripcion=$(\'#pro_descripcion-\'+tr_id).val();
  var costo=isNaN($(\'#precio-\'+tr_id).val())? 0 : $(\'#precio-\'+tr_id).val();
  var cantidad=isNaN($(\'#cantidad-\'+tr_id).val())? 0 : $(\'#cantidad-\'+tr_id).val();
  var lote=$(\'#lote\'+tr_id).val();
  var empaque=isNaN($(\'#empaque\'+tr_id).val())? 0 : $(\'#empaque\'+tr_id).val();
  var bobinas=isNaN($(\'#bobinas\'+tr_id).val())? 0 : $(\'#bobinas\'+tr_id).val();
  var pesoneto=isNaN($(\'#pesoneto\'+tr_id).val())? 0 : $(\'#pesoneto\'+tr_id).val();
  var pesobruto=isNaN($(\'#pesobruto\'+tr_id).val())? 0 : $(\'#pesobruto\'+tr_id).val();
  var price = isNaN($(\'#total-\'+tr_id).html())? 0 : $(\'#total-\'+tr_id).html();
  var id_detalle=$(\'#del-\'+tr_id).attr("data-id");
  var unidadmedida_id=$(\'#tesunidadesmedidas_id-\'+tr_id).val();
  var colores_id=$(\'#tescolores_id-\'+tr_id).val();
  var total=isNaN(price)? 0 : price;
 //alert(\'ID del PRODUCTO\'+producto_id);
 //alert(\'id del detalle es 0 cuando es nuevo--->\'+id_detalle);
// alert(\'id del tr que se graba-->\'+tr_id);
  
  if(unidadmedida_id!=\'\')
  {
	  var dataString =\'id_detalle=\'+id_detalle+\'&tesunidadesmedidas_id=\'+ unidadmedida_id+\'&productos_id=\'+ producto_id+\'&pro_descripcion=\'+pro_descripcion+\'&cantidad=\'+cantidad+\'&precio=\'+costo+\'&total=\'+total+\'&lote=\'+lote+\'&empaque=\'+empaque+\'&bobinas=\'+bobinas+\'&pesoneto=\'+pesoneto+\'&pesobruto=\'+pesobruto+\'&colores_id=\'+ colores_id;
	 //alert(dataString);
	  $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabarDetalle/1",
            data:dataString,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
                $("#loading").hide();
				//alert(\'id del detalle grabado\'+response);
				$(\'#del-\'+tr_id).attr("data-id", response);
				$(\'#empaque\'+tr_id).attr("data-id", response);
            }
 
         });
  }
  
}';
$code.='function EliminarDetalle(id)
{
	if(id!=0){
	$.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/eliminarDetalle/"+id,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
				alert(\'Se elimino de la base de dato\');
            }
 
         });
	}else
	{
		return false;
	}
}';
;
 break;
/*RECIBO POR HONORARIOS*/
case '02': 
$code.='$(document).ready(function()
		{	
			bind();  
			$("input").click(function(){
    		$(this).select();
			});';
$code.='$("#paid").blur(function(){update_balance();});
		$("#paid_total").blur(function(){update_balance();});';
   /*
  Para grabar el tr - id buscando la clase :save y mandando todo el contenido del tr:
  */
$code.='$("#addrow").click(function()
		{
			var ID = $(".item-row:last").attr("id");
			//alert(ID);
			grabarDetalle(ID);
			var IDnew=Number(ID)+Number(1);
			var tr=\''.Documentos::tr(Session::get('DOC_CODIGO'),$simbolo).'\';
    		$(".item-row:last").after(tr);
    		if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
			bind();
  		});';
/* ------- FIN contenido del tr: ------- */
$code.='$(".delete").live("click",function()
		{
		  var row =$(this).parents(".item-row")
		  var id=row.find(\'.delete\').attr(\'data-id\');
		  EliminarDetalle(id);
			$(this).parents(\'.item-row\').remove();
		  	update_total();
			if ($(".delete").length < 2) $(".delete").hide();
	    });';
/**/
  
  		//$("#pre_descripcion").blur(function(){$("#observacion").val($(this).val());GRABAR();});

$code.='$("#paid").blur(function(){$("#descuento").val($(this).val());GRABAR();});';
$code.='$("#glosa").change(function(){GRABAR();});';
$code.='$("#femision").change(function(){GRABAR();});';
$code.='$("#numero").blur(function(){GRABAR();});';
$code.='$(".grabar_form").blur(function(){GRABAR();});';
$code.='$("#monedas").change(function()
		{
			GRABAR();
			var op = $("#monedas option:selected").val();
			if(op==1)
			{
			$(".simbolo").html("S/. ");
			}
			if(op==2)
			{
			$(".simbolo").html("$. ");
			}
		});';
/*Busqueda del Cliente*/
$code.='$("#CL").tokenInput("/'.$module_name.'/ingresos/buscarcliente", 
		{
			tokenLimit: 1,
			minChars: 2,';
	if($cliente_id!=0){
	$code.='prePopulate: [ 
						 {id: '.$cliente_id.', name: "'.$nombre.'"},
						 ],';
			}
	$code.='onAdd: function (item){
			   var VAL=item.id;
			   $("#cliente_id").val(VAL);
			   $("#customer-title").val(item.name);
			   //$("#BC").hide();
			   //GrabarCliente();
			},
			onDelete: function (item) {
			}
		});';

if(Session::get('DOC_CODIGO')!='09'){
	
/*Busqueda del Cuentas de Gastos*/
$code.='$("#cuantagastos").tokenInput("/'.$module_name.'/ingresos/cuentasG", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuantagastos)){
$code.='prePopulate: [ 
		             {id: '.$cuantagastos.', name: "'.$cuantagastos_name.'"},
					 ],';
		}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   //$("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/*Busqueda del Cuentas Po pagar*/
$code.='$("#cuentaporpagar").tokenInput("/'.$module_name.'/ingresos/cuentasP", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuentaporpagar)){/*cuantagastos*/
$code.='prePopulate: [ 
		             {id: '.$cuentaporpagar.', name: "'.$cuentaporpagar_name.'"},
					 ],';
}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   //$("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/* FINN Busqueda del Cuentas Po pagar*/

$code.='$("#XC").click(function(){
		$("#BC").show();
		$("#cliente_id").val(\'\');
		$("#customer-title").val(\'\');
		//BorrarCliente()
		});';
}

$code.='});';
$code.='function roundNumber(number,decimals)
		{
  			var newString;// The new rounded number
  			decimals = Number(decimals);
  			if (decimals < 1) 
			{
    			newString = (Math.round(number)).toString();
  			}else
			{
    			var numString = number.toString();
    			if (numString.lastIndexOf(".") == -1)
				{// If there is no decimal point
      				numString += ".";// give it one at the end
    			}
    			var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
    			var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we\'ll end up with
    			var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
    			if (d2 >= 5)
				{// Do we need to round up at all? If not, the string will just be truncated
      				if (d1 == 9 && cutoff > 0)
					{// If the last digit is 9, find a new cutoff point
        				while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          				if (d1 != ".")
						{
            				cutoff -= 1;
            				d1 = Number(numString.substring(cutoff,cutoff+1));
         				} else {
            				cutoff -= 1;
          				}
        			}
      			}
      			d1 += 1;
    		} 
    		if (d1 == 10)
			{
      			numString = numString.substring(0, numString.lastIndexOf("."));
      			var roundedNum = Number(numString) + 1;
      			newString = roundedNum.toString() + \'.\';
    		}else
			{
				newString = numString.substring(0,cutoff) + d1.toString();
		    }
  		}
  		if (newString.lastIndexOf(".") == -1) 
		{// Do this again, to the new string
    		newString += ".";
  		}
  		var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
  		for(var i=0;i<decimals-decs;i++) newString += "0";
  		//var newNumber = Number(newString);// make it a number if you like
		return newString; // Output the result to the form field (change for your purposes)
		}';
/*FIN DE REDONDEO*/
$code.='function update_total()
		{
  			//alert("UPDATE TOTAL");
			var total = 0.00;
  			$(\'.cost\').each(function(i)
  			{
    			price = $(this).val();
    			///alert (\'precio: \'+price+\' ->>>>\'+i);
				if(!isNaN(price)) total =parseFloat(total)+parseFloat(price);
  				 ///alert (\'el total es : \'+total+\' ->>>>\');
   			});
			total = roundNumber(total,2);
			//alert (\'el total es : \'+total+\' ->>>>\');
			if(total>1500){
			var igv= total*0.08;
			//alert("es mayor");
			}else{
				var igv= 0;
				
			//alert("es menor");
				}
			var totalconigv=(parseFloat(total)-parseFloat(igv));
			//alert(\'el igv es: \'+igv);
			$("#paid").val(igv.toFixed(2));
			$("#igv").val(igv.toFixed(2));
			$("#totalconigv").val(totalconigv);
			$(\'#subtotal\').html(total);
			$(\'#paid_total\').val(total);
			//alert(total+"sin descuento");
			//alert(totalconigv+"CON descuento");
			update_balance();
		}';

$code.='function update_balance()
{
  //alert ($("#paid").val());
  var total= parseFloat( $("#paid_total").val());
  if(total>1500){
			var igv= total*0.08;
			//alert("es mayor");
			}else{
				var igv= 0;
				
			//alert("es menor");
				}
	var totalconigv=(parseFloat($("#paid_total").val())-parseFloat(igv));
	var due=roundNumber(totalconigv,2);
	var IGV=roundNumber(igv,2);
	$("#paid").val(IGV);
	$("#igv").val(IGV);
	$("#totalconigv").val(due);
  $("#totalconigv").val(due);
  //alert (\'===: \'+due);
  //alert(totalconigv);
  var en_letras=covertirNumLetras(totalconigv)
	$("#moneda_en_letras").html(en_letras);
	$("#totalenletras").val(en_letras);
	$(\'.due\').html(due);
	GRABAR();
}';

$code.='function update_price(ID)
{
  //var ID = $(this).attr(\'id\');
  var I=ID.split(\'-\');
  //alert (\'id del tr que se manda a grabar->>>\'+ID);
  var row = $(this).parents(\'.item-row\');
  //alert($("#precio-"+I[1]).val()+\'-----------------1\');
  var price = (parseFloat($("#precio-"+I[1]).val())*1);
  price = roundNumber(price,2);
 //alert(price);
  isNaN(price) ? $("#total-"+I[1]).html("N/A") : $("#total-"+I[1]).html(price);
  update_total();
  grabarDetalle(I[1]);
}';

$code.='function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function()
  {
	//alert("cost");
	 var ID = $(this).attr("id");
	 update_price(ID);
  });
  $(".qty").blur(function(){var ID = $(this).attr("id");
	  update_price(ID);
	 });
	 /*$(".price").blur(function(){var ID = $(this).attr("id");
	  update_price(ID);
	 });*/
}';

/*BUSCAR PRODUCTO*/
	/*  */		

/*envio dew Formulario PROFORMAS*/
$code.='function GRABAR()
{
     $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabar/1",
            data:$("#inventarios").serialize(),
            beforeSend:function(){
              $("#loading").show();
          	},
            success:function(response){
                $("#loading").hide();
				$("#id").val(response);
				//alert(response);
            }
 
          	});
 
      };';

/*GRABAR DETALLE DE PROFORMAS*/

$code.='function grabarDetalle(tr_id) 
{
//alert("antes de grabar"+tr_id);
  var producto_id=0;
  var concepto=$(\'#concepto\'+tr_id).val();
  var costo=isNaN($(\'#precio-\'+tr_id).val())? 0 : $(\'#precio-\'+tr_id).val();
  var cantidad=isNaN($(\'#cantidad-\'+tr_id).val())? 0 : $(\'#cantidad-\'+tr_id).val();
  var lote=$(\'#lote\'+tr_id).val();
  var empaque=isNaN($(\'#empaque\'+tr_id).val())? 0 : $(\'#empaque\'+tr_id).val();
  var bobinas=isNaN($(\'#bobinas\'+tr_id).val())? 0 : $(\'#bobinas\'+tr_id).val();
  var pesoneto=isNaN($(\'#pesoneto\'+tr_id).val())? 0 : $(\'#pesoneto\'+tr_id).val();
  var pesobruto=isNaN($(\'#pesobruto\'+tr_id).val())? 0 : $(\'#pesobruto\'+tr_id).val();
  var price = costo;
  var id_detalle=$(\'#del-\'+tr_id).attr("data-id");
  var unidadmedida_id=0;
  var colores_id=0;
  var total=isNaN(price)? 0 : price;
//alert(\'ID del PRODUCTO\'+producto_id);
//alert(\'id del detalle es 0 cuando es nuevo--->\'+id_detalle);
//alert(\'id del tr que se graba-->\'+tr_id);
  
  var dataString =\'id_detalle=\'+id_detalle+\'&tesunidadesmedidas_id=\'+ unidadmedida_id+\'&productos_id=\'+ producto_id+\'&cantidad=\'+cantidad+\'&precio=\'+costo+\'&total=\'+total+\'&lote=\'+lote+\'&concepto=\'+concepto+\'&bobinas=\'+bobinas+\'&pesoneto=\'+pesoneto+\'&pesobruto=\'+pesobruto+\'&colores_id=\'+ colores_id;
	//alert(dataString);
	  $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabarDetalle/1",
            data:dataString,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
                $("#loading").hide();
				//alert(\'id del detalle grabado\'+response);
				$(\'#del-\'+tr_id).attr("data-id", response);
				$(\'#empaque\'+tr_id).attr("data-id", response);
            }
 
         });
   
}';
$code.='function EliminarDetalle(id)
{
	if(id!=0){
	$.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/eliminarDetalle/"+id,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
				alert(\'Se elimino de la base de dato\');
            }
 
         });
	}else
	{
		return false;
	}
}';
	  ; break;
/*BOLETA DE VENTA*/
case '03': 
$code='
function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function(){update_price();});
  $(".qty").blur(function(){update_price();});
  $(".producto").click(function(){buscarProducto();});
  $(".unidad").click(function(){buscarUnidad();});
  $(".grabar_form").blur(function(){GRABAR();});
}';
	  ; break;
/*LIQUIDACION DE COMPRA*/
case '04': 
$code='
function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function(){update_price();});
  $(".qty").blur(function(){update_price();});
  $(".producto").click(function(){buscarProducto();});
  $(".unidad").click(function(){buscarUnidad();});
  $(".grabar_form").blur(function(){GRABAR();});
}';
	  ; break;
/*NOTA DE CREDITO*/
case '07':  
$code.='$(document).ready(function()
		{	
			bind();  
			$("input").click(function(){
    		$(this).select();
			});';
$code.='$("#paid").blur(function(){update_balance();});
		$("#paid_total").blur(function(){update_balance();});';
   /*
  Para grabar el tr - id buscando la clase :save y mandando todo el contenido del tr:
  */
$code.='$("#addrow").click(function()
		{
			var ID = $(".item-row:last").attr("id");
			//alert(ID);
			grabarDetalle(ID);
			var IDnew=Number(ID)+Number(1);
			var tr=\''.Documentos::tr(Session::get('DOC_CODIGO'),$simbolo).'\';
    		$(".item-row:last").after(tr);
    		if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
			bind();
  		});';
/* ------- FIN contenido del tr: ------- */
$code.='$(".delete").live("click",function()
		{
		  var row =$(this).parents(".item-row")
		  var id=row.find(\'.delete\').attr(\'data-id\');
		  EliminarDetalle(id);
			$(this).parents(\'.item-row\').remove();
		  	update_total();
			if ($(".delete").length < 2) $(".delete").hide();
	    });';
/**/
  
  		//$("#pre_descripcion").blur(function(){$("#observacion").val($(this).val());GRABAR();});

$code.='$("#paid").blur(function(){$("#descuento").val($(this).val());GRABAR();});';
$code.='$("#glosa").change(function(){GRABAR();});';
$code.='$("#femision").change(function(){GRABAR();});';
$code.='$("#numero").blur(function(){GRABAR();});';
$code.='$(".grabar_form").blur(function(){GRABAR();});';
$code.='$("#monedas").change(function()
		{
			GRABAR();
			var op = $("#monedas option:selected").val();
			if(op==1)
			{
			$(".simbolo").html("S/. ");
			}
			if(op==2)
			{
			$(".simbolo").html("$. ");
			}
		});';
/*Busqueda del Cliente*/
$code.='$("#CL").tokenInput("/'.$module_name.'/ingresos/buscarcliente", 
		{
			tokenLimit: 1,
			minChars: 2,';
	if($cliente_id!=0){
	$code.='prePopulate: [ 
						 {id: '.$cliente_id.', name: "'.$nombre.'"},
						 ],';
			}
	$code.='onAdd: function (item){
			   var VAL=item.id;
			   $("#cliente_id").val(VAL);
			   $("#customer-title").val(item.name);
			   //$("#BC").hide();
			   //GrabarCliente();
			},
			onDelete: function (item) {
			}
		});';

if(Session::get('DOC_CODIGO')!='09'){
	
/*Busqueda del Cuentas de Gastos*/
$code.='$("#cuantagastos").tokenInput("/'.$module_name.'/ingresos/cuentasG", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuantagastos)){
$code.='prePopulate: [ 
		             {id: '.$cuantagastos.', name: "'.$cuantagastos_name.'"},
					 ],';
		}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   //$("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/*Busqueda del Cuentas Po pagar*/
$code.='$("#cuentaporpagar").tokenInput("/'.$module_name.'/ingresos/cuentasP", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuentaporpagar)){/*cuantagastos*/
$code.='prePopulate: [ 
		             {id: '.$cuentaporpagar.', name: "'.$cuentaporpagar_name.'"},
					 ],';
}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   //$("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/* FINN Busqueda del Cuentas Po pagar*/

$code.='$("#XC").click(function(){
		$("#BC").show();
		$("#cliente_id").val(\'\');
		$("#customer-title").val(\'\');
		//BorrarCliente()
		});';
}

$code.='});';
$code.='function roundNumber(number,decimals)
		{
  			var newString;// The new rounded number
  			decimals = Number(decimals);
  			if (decimals < 1) 
			{
    			newString = (Math.round(number)).toString();
  			}else
			{
    			var numString = number.toString();
    			if (numString.lastIndexOf(".") == -1)
				{// If there is no decimal point
      				numString += ".";// give it one at the end
    			}
    			var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
    			var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we\'ll end up with
    			var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
    			if (d2 >= 5)
				{// Do we need to round up at all? If not, the string will just be truncated
      				if (d1 == 9 && cutoff > 0)
					{// If the last digit is 9, find a new cutoff point
        				while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          				if (d1 != ".")
						{
            				cutoff -= 1;
            				d1 = Number(numString.substring(cutoff,cutoff+1));
         				} else {
            				cutoff -= 1;
          				}
        			}
      			}
      			d1 += 1;
    		} 
    		if (d1 == 10)
			{
      			numString = numString.substring(0, numString.lastIndexOf("."));
      			var roundedNum = Number(numString) + 1;
      			newString = roundedNum.toString() + \'.\';
    		}else
			{
				newString = numString.substring(0,cutoff) + d1.toString();
		    }
  		}
  		if (newString.lastIndexOf(".") == -1) 
		{// Do this again, to the new string
    		newString += ".";
  		}
  		var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
  		for(var i=0;i<decimals-decs;i++) newString += "0";
  		//var newNumber = Number(newString);// make it a number if you like
		return newString; // Output the result to the form field (change for your purposes)
		}';
/*FIN DE REDONDEO*/
$code.='function update_total()
		{
  			//alert("UPDATE TOTAL");
			var total = 0.00;
  			$(\'.cost\').each(function(i)
  			{
    			price = $(this).val();
    			///alert (\'precio: \'+price+\' ->>>>\'+i);
				if(!isNaN(price)) total =parseFloat(total)+parseFloat(price);
  				 ///alert (\'el total es : \'+total+\' ->>>>\');
   			});
			total = roundNumber(total,2);
			//alert (\'el total es : \'+total+\' ->>>>\');
			
			var igv= total*'.$igv.';
			
			var totalconigv=(parseFloat(total)+parseFloat(igv));
			//alert(\'el igv es: \'+igv);
			$("#paid").val(igv.toFixed(2));
			$("#igv").val(igv.toFixed(2));
			$("#totalconigv").val(totalconigv);
			$(\'#subtotal\').html(total);
			$(\'#paid_total\').val(total);
			//alert(total+"sin descuento");
			//alert(totalconigv+"CON descuento");
			update_balance();
		}';

$code.='function update_balance()
{
  //alert ($("#paid").val());
  var total= parseFloat( $("#paid_total").val());
 	var igv= total*'.$igv.';
		
	var totalconigv=(parseFloat($("#paid_total").val())+parseFloat(igv));
	var due=roundNumber(totalconigv,2);
	var IGV=roundNumber(igv,2);
	$("#paid").val(IGV);
	$("#igv").val(IGV);
	$("#totalconigv").val(due);
  $("#totalconigv").val(due);
  //alert (\'===: \'+due);
  //alert(totalconigv);
  var en_letras=covertirNumLetras(totalconigv)
	$("#moneda_en_letras").html(en_letras);
	$("#totalenletras").val(en_letras);
	$(\'.due\').html(due);
	GRABAR();
}';

$code.='function update_price(ID)
{
  //var ID = $(this).attr(\'id\');
  var I=ID.split(\'-\');
  //alert (\'id del tr que se manda a grabar->>>\'+ID);
  var row = $(this).parents(\'.item-row\');
  //alert($("#precio-"+I[1]).val()+\'-----------------1\');
  var price = (parseFloat($("#precio-"+I[1]).val())*1);
  price = roundNumber(price,2);
 //alert(price);
  isNaN(price) ? $("#total-"+I[1]).html("N/A") : $("#total-"+I[1]).html(price);
  update_total();
  grabarDetalle(I[1]);
}';

$code.='function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function()
  {
	//alert("cost");
	 var ID = $(this).attr("id");
	 update_price(ID);
  });
  $(".qty").blur(function(){var ID = $(this).attr("id");
	  update_price(ID);
	 });
	 /*$(".price").blur(function(){var ID = $(this).attr("id");
	  update_price(ID);
	 });*/
}';

/*BUSCAR PRODUCTO*/
	/*  */		
$code.='function buscarProducto()
{
	var row = $(this).parents(\'.item-row:last\');
	var ID = $(\'.item-row:last\').attr(\'id\');
	$("#productos_id-"+ID).val();
	if($("#productos_id-"+ID).val()!=\'\'){
	$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{
		tokenLimit: 1,
		minChars: 1,
		
		prePopulate: [ 
		             {id: $("#productos_id-"+ID).val(), name: ""+$("#producto"+ID).val()+""},
					],
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(\'\');
		   row.find(\'.pro_descripcion\').val(\'\');
		   row.find(\'.cost\').val(\'\');
		   row.find(\'.qty\').val(\'\');
		   var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}else
	{
		$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{			
		tokenLimit: 1,
		minChars: 1,
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(\'\');
		   row.find(\'.pro_descripcion\').val(\'\');
		   row.find(\'.cost\').val(\'\');
		   row.find(\'.qty\').val(\'\');
		   var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}
}';
/*BUSCAR PRODUCTO*/
	/*  */			

/*envio dew Formulario PROFORMAS*/
$code.='function GRABAR()
{
     $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabar/1",
            data:$("#inventarios").serialize(),
            beforeSend:function(){
              $("#loading").show();
          	},
            success:function(response){
                $("#loading").hide();
				$("#id").val(response);
				//alert(response);
            }
 
          	});
 
      };';

/*GRABAR DETALLE DE PROFORMAS*/

$code.='function grabarDetalle(tr_id) 
{
//alert("antes de grabar"+tr_id);
  var producto_id=0;
  var concepto=$(\'#concepto\'+tr_id).val();
  var costo=isNaN($(\'#precio-\'+tr_id).val())? 0 : $(\'#precio-\'+tr_id).val();
  var cantidad=isNaN($(\'#cantidad-\'+tr_id).val())? 0 : $(\'#cantidad-\'+tr_id).val();
  var lote=$(\'#lote\'+tr_id).val();
  var empaque=isNaN($(\'#empaque\'+tr_id).val())? 0 : $(\'#empaque\'+tr_id).val();
  var bobinas=isNaN($(\'#bobinas\'+tr_id).val())? 0 : $(\'#bobinas\'+tr_id).val();
  var pesoneto=isNaN($(\'#pesoneto\'+tr_id).val())? 0 : $(\'#pesoneto\'+tr_id).val();
  var pesobruto=isNaN($(\'#pesobruto\'+tr_id).val())? 0 : $(\'#pesobruto\'+tr_id).val();
  var price = costo;
  var id_detalle=$(\'#del-\'+tr_id).attr("data-id");
  var unidadmedida_id=0;
  var colores_id=0;
  var total=isNaN(price)? 0 : price;
//alert(\'ID del PRODUCTO\'+producto_id);
//alert(\'id del detalle es 0 cuando es nuevo--->\'+id_detalle);
//alert(\'id del tr que se graba-->\'+tr_id);
  
  var dataString =\'id_detalle=\'+id_detalle+\'&tesunidadesmedidas_id=\'+ unidadmedida_id+\'&productos_id=\'+ producto_id+\'&cantidad=\'+cantidad+\'&precio=\'+costo+\'&total=\'+total+\'&lote=\'+lote+\'&concepto=\'+concepto+\'&bobinas=\'+bobinas+\'&pesoneto=\'+pesoneto+\'&pesobruto=\'+pesobruto+\'&colores_id=\'+ colores_id;
	//alert(dataString);
	  $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabarDetalle/1",
            data:dataString,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
                $("#loading").hide();
				//alert(\'id del detalle grabado\'+response);
				$(\'#del-\'+tr_id).attr("data-id", response);
				$(\'#empaque\'+tr_id).attr("data-id", response);
            }
 
         });
   
}';
$code.='function EliminarDetalle(id)
{
	if(id!=0){
	$.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/eliminarDetalle/"+id,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
				alert(\'Se elimino de la base de dato\');
            }
 
         });
	}else
	{
		return false;
	}
}';
	  ; break;
/*NOTA DE DEBITO*/
case '08': 
$code.='$(document).ready(function()
		{	
			bind();  
			$("input").click(function(){
    		$(this).select();
			});';
$code.='$("#paid").blur(function(){update_balance();});
		$("#paid_total").blur(function(){update_balance();});';
   /*
  Para grabar el tr - id buscando la clase :save y mandando todo el contenido del tr:
  */
$code.='$("#addrow").click(function()
		{
			var ID = $(".item-row:last").attr("id");
			//alert(ID);
			grabarDetalle(ID);
			var IDnew=Number(ID)+Number(1);
			var tr=\''.Documentos::tr(Session::get('DOC_CODIGO'),$simbolo).'\';
    		$(".item-row:last").after(tr);
    		if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
			bind();
  		});';
/* ------- FIN contenido del tr: ------- */
$code.='$(".delete").live("click",function()
		{
		  var row =$(this).parents(".item-row")
		  var id=row.find(\'.delete\').attr(\'data-id\');
		  EliminarDetalle(id);
			$(this).parents(\'.item-row\').remove();
		  	update_total();
			if ($(".delete").length < 2) $(".delete").hide();
	    });';
/**/
  
  		//$("#pre_descripcion").blur(function(){$("#observacion").val($(this).val());GRABAR();});

$code.='$("#paid").blur(function(){$("#descuento").val($(this).val());GRABAR();});';
$code.='$("#glosa").change(function(){GRABAR();});';
$code.='$("#femision").change(function(){GRABAR();});';
$code.='$("#numero").blur(function(){GRABAR();});';
$code.='$(".grabar_form").blur(function(){GRABAR();});';
$code.='$("#monedas").change(function()
		{
			GRABAR();
			var op = $("#monedas option:selected").val();
			if(op==1)
			{
			$(".simbolo").html("S/. ");
			}
			if(op==2)
			{
			$(".simbolo").html("$. ");
			}
		});';
/*Busqueda del Cliente*/
$code.='$("#CL").tokenInput("/'.$module_name.'/ingresos/buscarcliente", 
		{
			tokenLimit: 1,
			minChars: 2,';
	if($cliente_id!=0){
	$code.='prePopulate: [ 
						 {id: '.$cliente_id.', name: "'.$nombre.'"},
						 ],';
			}
	$code.='onAdd: function (item){
			   var VAL=item.id;
			   $("#cliente_id").val(VAL);
			   $("#customer-title").val(item.name);
			   //$("#BC").hide();
			   //GrabarCliente();
			},
			onDelete: function (item) {
			}
		});';

if(Session::get('DOC_CODIGO')!='09'){
	
/*Busqueda del Cuentas de Gastos*/
$code.='$("#cuantagastos").tokenInput("/'.$module_name.'/ingresos/cuentasG", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuantagastos)){
$code.='prePopulate: [ 
		             {id: '.$cuantagastos.', name: "'.$cuantagastos_name.'"},
					 ],';
		}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   //$("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/*Busqueda del Cuentas Po pagar*/
$code.='$("#cuentaporpagar").tokenInput("/'.$module_name.'/ingresos/cuentasP", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuentaporpagar)){/*cuantagastos*/
$code.='prePopulate: [ 
		             {id: '.$cuentaporpagar.', name: "'.$cuentaporpagar_name.'"},
					 ],';
}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   //$("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/* FINN Busqueda del Cuentas Po pagar*/

$code.='$("#XC").click(function(){
		$("#BC").show();
		$("#cliente_id").val(\'\');
		$("#customer-title").val(\'\');
		//BorrarCliente()
		});';
}

$code.='});';
$code.='function update_total()
		{
  			//alert("UPDATE TOTAL");
			var total = 0.00;
  			$(\'.cost\').each(function(i)
  			{
    			price = $(this).val();
    			///alert (\'precio: \'+price+\' ->>>>\'+i);
				if(!isNaN(price)) total =parseFloat(total)+parseFloat(price);
  				 ///alert (\'el total es : \'+total+\' ->>>>\');
   			});
			total = total.toFixed(2);
			//alert (\'el total es : \'+total+\' ->>>>\');
			
			var igv= total*'.$igv.';
			
			var totalconigv=(parseFloat(total)+parseFloat(igv));
			//alert(\'el igv es: \'+igv);
			$("#paid").val(igv.toFixed(2));
			$("#igv").val(igv.toFixed(2));
			$("#totalconigv").val(totalconigv);
			$(\'#subtotal\').html(total);
			$(\'#paid_total\').val(total);
			//alert(total+"sin descuento");
			//alert(totalconigv+"CON descuento");
			update_balance();
		}';

$code.='function update_balance()
{
 // alert ($("#paid").val());
  var total= parseFloat( $("#paid_total").val());
 	var igv= total*'.$igv.';
		
	var totalconigv=(parseFloat($("#paid_total").val())+parseFloat(igv));
	var due=totalconigv.toFixed(2);
	var IGV=igv.toFixed(2);
	$("#paid").val(IGV);
	$("#igv").val(IGV);
	$("#totalconigv").val(due);
  $("#totalconigv").val(due);
  //alert (\'===: \'+due);
  //alert(totalconigv);
  var en_letras=covertirNumLetras(totalconigv)
	$("#moneda_en_letras").html(en_letras);
	$("#totalenletras").val(en_letras);
	$(\'.due\').html(due);
	GRABAR();
}';

$code.='function update_price(ID)
{
  //var ID = $(this).attr(\'id\');
  var I=ID.split(\'-\');
  //alert (\'id del tr que se manda a grabar->>>\'+ID);
  var row = $(this).parents(\'.item-row\');
  //alert($("#precio-"+I[1]).val()+\'-----------------1\');
  var price = (parseFloat($("#precio-"+I[1]).val())*1);
  price = price.toFixed(2);
 //alert(price);
  isNaN(price) ? $("#total-"+I[1]).html("N/A") : $("#total-"+I[1]).html(price);
  update_total();
  grabarDetalle(I[1]);
}';

$code.='function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function()
  {
	//alert("cost");
	 var ID = $(this).attr("id");
	 update_price(ID);
  });
  $(".qty").blur(function(){var ID = $(this).attr("id");
	  update_price(ID);
	 });
	 /*$(".price").blur(function(){var ID = $(this).attr("id");
	  update_price(ID);
	 });*/
}';

/*BUSCAR PRODUCTO*/
	/*  */		
$code.='function buscarProducto()
{
	var row = $(this).parents(\'.item-row:last\');
	var ID = $(\'.item-row:last\').attr(\'id\');
	$("#productos_id-"+ID).val();
	if($("#productos_id-"+ID).val()!=\'\'){
	$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{
		tokenLimit: 1,
		minChars: 1,
		
		prePopulate: [ 
		             {id: $("#productos_id-"+ID).val(), name: ""+$("#producto"+ID).val()+""},
					],
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(\'\');
		   row.find(\'.pro_descripcion\').val(\'\');
		   row.find(\'.cost\').val(\'\');
		   row.find(\'.qty\').val(\'\');
		   var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}else
	{
		$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{			
		tokenLimit: 1,
		minChars: 1,
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(\'\');
		   row.find(\'.pro_descripcion\').val(\'\');
		   row.find(\'.cost\').val(\'\');
		   row.find(\'.qty\').val(\'\');
		   var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}
}';
/*BUSCAR PRODUCTO*/
	/*  */			

/*envio dew Formulario PROFORMAS*/
$code.='function GRABAR()
{
     $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabar/1",
            data:$("#inventarios").serialize(),
            beforeSend:function(){
              $("#loading").show();
          	},
            success:function(response){
                $("#loading").hide();
				$("#id").val(response);
				//alert(response);
            }
 
          	});
 
      };';

/*GRABAR DETALLE DE PROFORMAS*/

$code.='function grabarDetalle(tr_id) 
{
//alert("antes de grabar"+tr_id);
  var producto_id=0;
  var concepto=$(\'#concepto\'+tr_id).val();
  var costo=isNaN($(\'#precio-\'+tr_id).val())? 0 : $(\'#precio-\'+tr_id).val();
  var cantidad=isNaN($(\'#cantidad-\'+tr_id).val())? 0 : $(\'#cantidad-\'+tr_id).val();
  var lote=$(\'#lote\'+tr_id).val();
  var empaque=isNaN($(\'#empaque\'+tr_id).val())? 0 : $(\'#empaque\'+tr_id).val();
  var bobinas=isNaN($(\'#bobinas\'+tr_id).val())? 0 : $(\'#bobinas\'+tr_id).val();
  var pesoneto=isNaN($(\'#pesoneto\'+tr_id).val())? 0 : $(\'#pesoneto\'+tr_id).val();
  var pesobruto=isNaN($(\'#pesobruto\'+tr_id).val())? 0 : $(\'#pesobruto\'+tr_id).val();
  var price = costo;
  var id_detalle=$(\'#del-\'+tr_id).attr("data-id");
  var unidadmedida_id=0;
  var colores_id=0;
  var total=isNaN(price)? 0 : price;
//alert(\'ID del PRODUCTO\'+producto_id);
//alert(\'id del detalle es 0 cuando es nuevo--->\'+id_detalle);
//alert(\'id del tr que se graba-->\'+tr_id);
  
  var dataString =\'id_detalle=\'+id_detalle+\'&tesunidadesmedidas_id=\'+ unidadmedida_id+\'&productos_id=\'+ producto_id+\'&cantidad=\'+cantidad+\'&precio=\'+costo+\'&total=\'+total+\'&lote=\'+lote+\'&concepto=\'+concepto+\'&bobinas=\'+bobinas+\'&pesoneto=\'+pesoneto+\'&pesobruto=\'+pesobruto+\'&colores_id=\'+ colores_id;
	//alert(dataString);
	  $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabarDetalle/1",
            data:dataString,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
                $("#loading").hide();
				//alert(\'id del detalle grabado\'+response);
				$(\'#del-\'+tr_id).attr("data-id", response);
				$(\'#empaque\'+tr_id).attr("data-id", response);
            }
 
         });
   
}';
$code.='function EliminarDetalle(id)
{
	if(id!=0){
	$.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/eliminarDetalle/"+id,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
				alert(\'Se elimino de la base de dato\');
            }
 
         });
	}else
	{
		return false;
	}
}';
	  ; break;
/*GUIA REMISION*/
case '09':
$code.='
$(document).ready(function()
		{	
			bind();  
			$("input").click(function(){
    		$(this).select();
			});';
$code.='$("#paid").blur(function(){update_balance();});
		$("#paid_total").blur(function(){update_balance();});';
   /*
  Para grabar el tr - id buscando la clase :save y mandando todo el contenido del tr:
  */
$code.='$("#addrow").click(function()
		{
			var ID = $(".item-row:last").attr("id");
			//alert(ID);
			grabarDetalle(ID);
			var IDnew=Number(ID)+Number(1);
			var tr=\''.Documentos::tr(Session::get('DOC_CODIGO'),$simbolo).'\';
    		$(".item-row:last").after(tr);
    		if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
			bind();
  		});';
/* ------- FIN contenido del tr: ------- */
$code.='$(".delete").live("click",function()
		{
		  var row =$(this).parents(".item-row")
		  var id=row.find(\'.delete\').attr(\'data-id\');
		  EliminarDetalle(id);
			$(this).parents(\'.item-row\').remove();
		  	update_total();
			if ($(".delete").length < 2) $(".delete").hide();
	    });';
/**/
  
  		//$("#pre_descripcion").blur(function(){$("#observacion").val($(this).val());GRABAR();});

$code.='$("#paid").blur(function(){$("#descuento").val($(this).val());GRABAR();});';
$code.='$("#glosa").change(function(){GRABAR();});';
$code.='$("#femision").change(function(){GRABAR();});'; 
$code.='$("#numero").blur(function(){GRABAR();});';
$code.='$(".grabar_form").blur(function(){GRABAR();});';
$code.='$("#monedas").change(function()
		{
			GRABAR();
			var op = $("#monedas option:selected").val();
			if(op==1)
			{
			$(".simbolo").html("S/. ");
			}
			if(op==2)
			{
			$(".simbolo").html("$. ");
			}
		});';
/*Busqueda del Cliente*/
$code.='$("#CL").tokenInput("/'.$module_name.'/ingresos/buscarcliente", 
		{
			tokenLimit: 1,
			minChars: 2,';
	//echo $cliente_id; 
	if($cliente_id!=0){
	$code.='prePopulate: [ 
						 {id: '.$cliente_id.', name: "'.$nombre.'"},
						 ],';
			}
	$code.='onAdd: function (item){
			   var VAL=item.id;
			   $("#cliente_id").val(VAL);
			   $("#customer-title").val(item.name);
			   //$("#BC").hide();
			   //GrabarCliente();
			},
			onDelete: function (item) {
			}
		});';
$code.='});';
/*fin de Documento ready*/
$code.='function roundNumber(number,decimals)
		{
  			var newString;// The new rounded number
  			decimals = Number(decimals);
  			if (decimals < 1) 
			{
    			newString = (Math.round(number)).toString();
  			}else
			{
    			var numString = number.toString();
    			if (numString.lastIndexOf(".") == -1)
				{// If there is no decimal point
      				numString += ".";// give it one at the end
    			}
    			var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
    			var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we\'ll end up with
    			var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
    			if (d2 >= 5)
				{// Do we need to round up at all? If not, the string will just be truncated
      				if (d1 == 9 && cutoff > 0)
					{// If the last digit is 9, find a new cutoff point
        				while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          				if (d1 != ".")
						{
            				cutoff -= 1;
            				d1 = Number(numString.substring(cutoff,cutoff+1));
         				} else {
            				cutoff -= 1;
          				}
        			}
      			}
      			d1 += 1;
    		} 
    		if (d1 == 10)
			{
      			numString = numString.substring(0, numString.lastIndexOf("."));
      			var roundedNum = Number(numString) + 1;
      			newString = roundedNum.toString() + \'.\';
    		}else
			{
				newString = numString.substring(0,cutoff) + d1.toString();
		    }
  		}
  		if (newString.lastIndexOf(".") == -1) 
		{// Do this again, to the new string
    		newString += ".";
  		}
  		var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
  		for(var i=0;i<decimals-decs;i++) newString += "0";
  		//var newNumber = Number(newString);// make it a number if you like
		return newString; // Output the result to the form field (change for your purposes)
		}';
/*FIN DE REDONDEO*/
$code.='function update_total()
		{
  			var total = 0.00;
  			$(\'.price\').each(function(i)
  			{
    			price = $(this).html();
    			//alert (\'precio: \'+price+\' ->>>>\'+i);
				if(!isNaN(price)) total =parseFloat(total)+parseFloat(price);
  				// alert (\'el total es : \'+total+\' ->>>>\');
   			});
			total = roundNumber(total,2);
			//alert (\'el total es : \'+total+\' ->>>>\');
			var igv= total*'.$igv.';
			var totalconigv=(parseFloat(total)+parseFloat(igv));
			//alert(\'el igv es: \'+igv);
			$("#paid").val(igv.toFixed(2));
			$("#igv").val(igv.toFixed(2));
			$("#totalconigv").val(totalconigv);
			$(\'#subtotal\').html(total);
			$(\'#paid_total\').val(total);
			update_balance();
		}';

$code.='function update_balance()
{
 // alert ($("#paid").val());
  var igv= parseFloat( $("#paid_total").val())*'.$igv.';
	var totalconigv=(parseFloat($("#paid_total").val())+parseFloat(igv));
	var due=roundNumber(totalconigv,2);
	var IGV=roundNumber(igv,2);
	$("#paid").val(IGV);
	$("#igv").val(IGV);
	$("#totalconigv").val(due);
  $("#totalconigv").val(due);
  //alert (\'===: \'+due);
	$(\'.due\').html(due);
	GRABAR();
}';

$code.='function update_price(ID)
{
  //var ID = $(this).attr(\'id\');
  var I=ID.split(\'-\');
  //alert (\'id del tr que se manda a grabar->>>\'+ID);
  var row = $(this).parents(\'.item-row\');
  //alert(row.find(\'.cost\').val());
  //var price = row.find(\'.cost\').val() * row.find(\'.qty\').val();
  //alert($("#precio-"+I[1]).val()+\'-----------------\'+$("#cantidad-"+I[1]).val());
  var price = (parseFloat($("#precio-"+I[1]).val())*parseFloat($("#cantidad-"+I[1]).val()));
  price = roundNumber(price,2);
  //alert(price);
  isNaN(price) ? $("#total-"+I[1]).html("N/A") : $("#total-"+I[1]).html(price);
  update_total();
  grabarDetalle(I[1]);
}';
$code.='
function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function(){update_price();});
  $(".qty").blur(function(){update_price();});
  $(".producto").click(buscarProducto());
  $(".unidad").click(buscarUnidad());
  $(".color").click(buscarColor());
  $(".caja").blur(Mostrar());
  $(".save").blur(function(){Detalle($(this).attr("id"));});
}';

/*BUSCAR PRODUCTO*/
	/*  */		
$code.='function buscarProducto()
{
	var row = $(this).parents(\'.item-row:last\');
	var ID = $(\'.item-row:last\').attr(\'id\');
	$("#productos_id-"+ID).val();
	if($("#productos_id-"+ID).val()!=\'\'){
	$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{
		tokenLimit: 1,
		minChars: 1,
		
		prePopulate: [ 
		             {id: $("#productos_id-"+ID).val(), name: ""+$("#producto"+ID).val()+""},
					],
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(\'\');
		   row.find(\'.pro_descripcion\').val(\'\');
		   row.find(\'.cost\').val(\'\');
		   row.find(\'.qty\').val(\'\');
		   var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}else
	{
		$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{			
		tokenLimit: 1,
		minChars: 1,
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(\'\');
		   row.find(\'.pro_descripcion\').val(\'\');
		   row.find(\'.cost\').val(\'\');
		   row.find(\'.qty\').val(\'\');
		   var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}
}';
/*BUSCAR PRODUCTO*/
	/*  */		
$code.='function buscarUnidad()
{
	var row = $(this).parents(\'.item-row:last\');
	var ID = $(\'.item-row:last\').attr(\'id\');
	//alert($("#tesunidadesmedidas"+ID).val());
	if($("#tesunidadesmedidas"+ID).val()!=\'\'){
	$("#tesunidadesmedidas"+ID).tokenInput("/'.$module_name.'/ingresos/medidas/", 
		{			
		tokenLimit: 1,
		minChars: 1,
		prePopulate: [ 
		             {id: $("#tesunidadesmedidas_id-"+ID).val(), name: ""+$("#tesunidadesmedidas"+ID).val()+""},
					],
		onAdd: function (item) {
		   var VAL=item.id;
		   $("#tesunidadesmedidas_id-"+ID).val(item.id);
		   $("#tesunidadesmedidas"+ID).val(item.name);
		   grabarDetalle(ID);
		},
		onDelete: function (item) {
		  $("#tesunidadesmedidas_id-"+ID).val(\'\');
		   $("#tesunidadesmedidas"+ID).val(\'\');
		}
	});
	}else
	{
	$("#tesunidadesmedidas"+ID).tokenInput("/'.$module_name.'/ingresos/medidas/", 
		{
			
		tokenLimit: 1,
		minChars: 1,
		onAdd: function (item) {
		   var VAL=item.id;
		   $("#tesunidadesmedidas_id-"+ID).val(item.id);
		   $("#tesunidadesmedidas"+ID).val(item.name);
		   grabarDetalle(ID);
		},
		onDelete: function (item) {
		  $("#tesunidadesmedidas_id-"+ID).val(\'\');
		   $("#tesunidadesmedidas"+ID).val(\'\');
		}
	});
	}
}';

/*envio dew Formulario PROFORMAS*/
$code.='function GRABAR()
{
     if($("#cliente_id").val()){
	 $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabar/1",
            data:$("#inventarios").serialize(),
            beforeSend:function(){
              $("#loading").show();
          	},
            success:function(response){
                $("#loading").hide();
				$("#id").val(response);
				//alert(response);
            }
 
          	});
	 }else
	 {
		 return FALSE;
	}
 
      };';

/*GRABAR DETALLE DE PROFORMAS*/

$code.='function grabarDetalle(tr_id) 
{
  //alert(tr_id);
  var producto_id=$(\'#productos_id-\'+tr_id).val();
  var pro_descripcion=$(\'#pro_descripcion-\'+tr_id).val();
  var costo=isNaN($(\'#precio-\'+tr_id).val())? 0 : $(\'#precio-\'+tr_id).val();
  //var cantidad=isNaN($(\'#cantidad-\'+tr_id).val())? 0 : $(\'#cantidad-\'+tr_id).val();
  var lote=($(\'#lote\'+tr_id).val());
 //alert(lote);
  var empaque=isNaN($(\'#empaque\'+tr_id).val())? 0 : $(\'#empaque\'+tr_id).val();
  var bobinas=isNaN($(\'#bobinas\'+tr_id).val())? 0 : $(\'#bobinas\'+tr_id).val();
  var pesoneto=isNaN($(\'#pesoneto\'+tr_id).val())? 0 : $(\'#pesoneto\'+tr_id).val();
  var pesobruto=isNaN($(\'#pesobruto\'+tr_id).val())? 0 : $(\'#pesobruto\'+tr_id).val();
  var price = isNaN($(\'#total-\'+tr_id).html())? 0 : $(\'#total-\'+tr_id).html();
  var id_detalle=$(\'#del-\'+tr_id).attr("data-id");
  var unidadmedida_id=$(\'#tesunidadesmedidas_id-\'+tr_id).val();
  var colores_id=$(\'#tescolores_id-\'+tr_id).val();
  var total=isNaN(price)? 0 : price;
  var cantidad=pesoneto;
 //alert(\'ID del PRODUCTO\'+producto_id);
 //alert(\'id del detalle es 0 cuando es nuevo--->\'+id_detalle);
// alert(\'id del tr que se graba-->\'+tr_id);
  
  if(producto_id!=\'\' && unidadmedida_id!=\'\' && colores_id!=\'\')
  {
	  var dataString =\'id_detalle=\'+id_detalle+\'&tesunidadesmedidas_id=\'+ unidadmedida_id+\'&productos_id=\'+ producto_id+\'&pro_descripcion=\'+pro_descripcion+\'&cantidad=\'+cantidad+\'&precio=\'+costo+\'&total=\'+total+\'&lote=\'+lote+\'&empaque=\'+empaque+\'&bobinas=\'+bobinas+\'&pesoneto=\'+pesoneto+\'&pesobruto=\'+pesobruto+\'&colores_id=\'+ colores_id;
	 //alert(dataString);
	  $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabarDetalle/1",
            data:dataString,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
                $("#loading").hide();
				//alert(\'id del detalle grabado\'+response);
				$(\'#del-\'+tr_id).attr("data-id", response);
				$(\'#empaque\'+tr_id).attr("data-id", response);
            }
 
         });
  }
  
}';
$code.='function EliminarDetalle(id)
{
	if(id!=0){
	$.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/eliminarDetalle/"+id,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
				alert(\'Se elimino de la base de dato\');
            }
 
         });
	}else
	{
		return false;
	}
}';


$code.='function Detalle(id){
	  if(id){
	  var idletras=id;
	  //alert(id);
	  var ID=idletras.split("");
	  var n=idletras.length;
	  grabarDetalle(ID[n-1]);
	  }else
	  {
		  return FALSE;
	  }
}';
/*cajas*/
$code.='function Mostrar(){
$(".cajas").blur(function(e){
	  //e.preventDefault();
	  var idletras=$(this).attr("id");
	  var ID=idletras.split("");
	  var n=idletras.length;
	  grabarDetalle(ID[n-1]);
	  var numerocajas=$(this).val();
	  var id_detalle=$(this).attr("data-id");
	  var dataString ="id_detalle="+id_detalle;
	  //alert(numerocajas);
	  if(numerocajas!="")
	  {';
/*if(Auth::get('id')!=3){	  
$code.='$.ajax({
            type:"POST",
            url:"/'.$module_name.'/cajas/crear/"+numerocajas+"/"+ID[n-1],
            data:dataString,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
                $("#loading").hide();
				$("#cajas-"+ID[n-1]).html(response);
				$("#cajas-"+ID[n-1]).show();
				$("#cajas-"+ID[n-1]).dialog({
					title: "Ingrese las Etiquetas de las Cajas",
					width: 600,
					height: 460,
					modal: true,
					buttons: 
					{
					Ok: function() {
					$( this ).dialog( "close" );
					}
					}
				});
				
            }
 
         });';
}*/
/*if(Auth::get('id')==3)
{*/
$code.='if(confirm("usted esta ingresando "+numerocajas+" cajas")){
		document.location = "/'.$module_name.'/cajas/crear_cajas/"+numerocajas+"/"+ID[n-1]+"/"+id_detalle;
		}else{
		return FALSE;
		}';
/*}*/
$code.='		 
	  }
	});
}';
/*Buscar COLOR*/

$code.='function buscarColor()
{
	var row = $(this).parents(".item-row:last");
	var ID = $(".item-row:last").attr("id");
	//alert($("#tesunidadesmedidas"+ID).val());
	if($("#tescolores"+ID).val()!=""){
	$("#tescolores"+ID).tokenInput("/'.$module_name.'/ingresos/colores/", 
		{
			
		tokenLimit: 1,
		minChars: 1,
		prePopulate: [ 
		             {id: $("#tescolores_id-"+ID).val(), name: ""+$("#tescolores"+ID).val()+""},
					],
		onAdd: function (item) {
		   var VAL=item.id;
		   $("#tescolores_id-"+ID).val(item.id);
		   $("#tescolores"+ID).val(item.name);
		   grabarDetalle(ID);
		},
		onDelete: function (item) {
		  $("#tescolores_id-"+ID).val("");
		   $("#tescolores"+ID).val("");
		}
	});
	}else
	{
	$("#tescolores"+ID).tokenInput("/'.$module_name.'/ingresos/colores/", 
		{
			
		tokenLimit: 1,
		minChars: 1,
		onAdd: function (item) {
		   var VAL=item.id;
		   $("#tescolores_id-"+ID).val(item.id);
		   $("#tescolores"+ID).val(item.name);
		   grabarDetalle(ID);
		},
		onDelete: function (item) {
		  $("#tescolores_id-"+ID).val("");
		   $("#tescolores"+ID).val("");
		}
	});
	}	
}';
	  ; break;
/*RECIBO POR ARRENDAMIENTO*/
case '10':  
$code='
function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function(){update_price();});
  $(".qty").blur(function(){update_price();});
  $(".producto").click(function(){buscarProducto();});
  $(".unidad").click(function(){buscarUnidad();});
}';
	  ; break;
/*TICKET DE MAQUINA REGISTRADOR*/
case '12': 
$code.='$(document).ready(function()
		{	
			bind();  
			$("input").click(function(){
    		$(this).select();
			});';
$code.='$("#paid").blur(function(){update_balance();});
		$("#paid_total").blur(function(){update_balance();});';
   /*
  Para grabar el tr - id buscando la clase :save y mandando todo el contenido del tr:
  */
$code.='$("#addrow").click(function()
		{
			var ID = $(".item-row:last").attr("id");
			//alert(ID);
			grabarDetalle(ID);
			var IDnew=Number(ID)+Number(1);
			var tr=\''.Documentos::tr(Session::get('DOC_CODIGO'),$simbolo).'\';
    		$(".item-row:last").after(tr);
    		if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
			bind();
  		});';
/* ------- FIN contenido del tr: ------- */
$code.='$(".delete").live("click",function()
		{
		  var row =$(this).parents(".item-row")
		  var id=row.find(\'.delete\').attr(\'data-id\');
		  EliminarDetalle(id);
			$(this).parents(\'.item-row\').remove();
		  	update_total();
			if ($(".delete").length < 2) $(".delete").hide();
	    });';
/**/
  
  		//$("#pre_descripcion").blur(function(){$("#observacion").val($(this).val());GRABAR();});

$code.='$("#paid").blur(function(){$("#descuento").val($(this).val());GRABAR();});';
$code.='$("#glosa").change(function(){GRABAR();});';
$code.='$("#femision").change(function(){GRABAR();});';
$code.='$("#numero").blur(function(){GRABAR();});';
$code.='$(".grabar_form").blur(function(){GRABAR();});';
$code.='$("#monedas").change(function()
		{
			GRABAR();
			var op = $("#monedas option:selected").val();
			if(op==1)
			{
			$(".simbolo").html("S/. ");
			}
			if(op==2)
			{
			$(".simbolo").html("$. ");
			}
		});';
/*Busqueda del Cliente*/
$code.='$("#CL").tokenInput("/'.$module_name.'/ingresos/buscarcliente", 
		{
			tokenLimit: 1,
			minChars: 2,';
	if($cliente_id!=0){
	$code.='prePopulate: [ 
						 {id: '.$cliente_id.', name: "'.$nombre.'"},
						 ],';
			}
	$code.='onAdd: function (item){
			   var VAL=item.id;
			   $("#cliente_id").val(VAL);
			   $("#customer-title").val(item.name);
			   //$("#BC").hide();
			   //GrabarCliente();
			},
			onDelete: function (item) {
			}
		});';

if(Session::get('DOC_CODIGO')!='09'){
	
/*Busqueda del Cuentas de Gastos*/
$code.='$("#cuantagastos").tokenInput("/'.$module_name.'/ingresos/cuentasG", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuantagastos)){
$code.='prePopulate: [ 
		             {id: '.$cuantagastos.', name: "'.$cuantagastos_name.'"},
					 ],';
		}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   //$("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/*Busqueda del Cuentas Po pagar*/
$code.='$("#cuentaporpagar").tokenInput("/'.$module_name.'/ingresos/cuentasP", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuentaporpagar)){/*cuantagastos*/
$code.='prePopulate: [ 
		             {id: '.$cuentaporpagar.', name: "'.$cuentaporpagar_name.'"},
					 ],';
}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   //$("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/* FINN Busqueda del Cuentas Po pagar*/

$code.='$("#XC").click(function(){
		$("#BC").show();
		$("#cliente_id").val(\'\');
		$("#customer-title").val(\'\');
		//BorrarCliente()
		});';
}

$code.='});';
$code.='function roundNumber(number,decimals)
		{
  			var newString;// The new rounded number
  			decimals = Number(decimals);
  			if (decimals < 1) 
			{
    			newString = (Math.round(number)).toString();
  			}else
			{
    			var numString = number.toString();
    			if (numString.lastIndexOf(".") == -1)
				{// If there is no decimal point
      				numString += ".";// give it one at the end
    			}
    			var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
    			var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we\'ll end up with
    			var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
    			if (d2 >= 5)
				{// Do we need to round up at all? If not, the string will just be truncated
      				if (d1 == 9 && cutoff > 0)
					{// If the last digit is 9, find a new cutoff point
        				while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          				if (d1 != ".")
						{
            				cutoff -= 1;
            				d1 = Number(numString.substring(cutoff,cutoff+1));
         				} else {
            				cutoff -= 1;
          				}
        			}
      			}
      			d1 += 1;
    		} 
    		if (d1 == 10)
			{
      			numString = numString.substring(0, numString.lastIndexOf("."));
      			var roundedNum = Number(numString) + 1;
      			newString = roundedNum.toString() + \'.\';
    		}else
			{
				newString = numString.substring(0,cutoff) + d1.toString();
		    }
  		}
  		if (newString.lastIndexOf(".") == -1) 
		{// Do this again, to the new string
    		newString += ".";
  		}
  		var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
  		for(var i=0;i<decimals-decs;i++) newString += "0";
  		//var newNumber = Number(newString);// make it a number if you like
		return newString; // Output the result to the form field (change for your purposes)
		}';
/*FIN DE REDONDEO*/
$code.='function update_total()
		{
  			var total = 0.00;
  			$(\'.price\').each(function(i)
  			{
    			price = $(this).html();
    			//alert (\'precio: \'+price+\' ->>>>\'+i);
				if(!isNaN(price)) total =parseFloat(total)+parseFloat(price);
  				// alert (\'el total es : \'+total+\' ->>>>\');
   			});
			total = roundNumber(total,2);
			//alert (\'el total es : \'+total+\' ->>>>\');
			var igv= total*'.$igv.';
			var totalconigv=(parseFloat(total)+parseFloat(igv));
			//alert(\'el igv es: \'+igv);
			$("#paid").val(igv.toFixed(2));
			$("#igv").val(igv.toFixed(2));
			$("#totalconigv").val(totalconigv);
			$(\'#subtotal\').html(total);
			$(\'#paid_total\').val(total);
			update_balance();
		}';

$code.='function update_balance()
{
 // alert ($("#paid").val());
  var igv= parseFloat( $("#paid_total").val())*'.$igv.';
	var totalconigv=(parseFloat($("#paid_total").val())+parseFloat(igv));
	var due=roundNumber(totalconigv,2);
	var IGV=roundNumber(igv,2);
	$("#paid").val(IGV);
	$("#igv").val(IGV);
	$("#totalconigv").val(due);
  $("#totalconigv").val(due);
  //alert (\'===: \'+due);
	$(\'.due\').html(due);
	GRABAR();
}';

$code.='function update_price(ID)
{
  //var ID = $(this).attr(\'id\');
  var I=ID.split(\'-\');
  //alert (\'id del tr que se manda a grabar->>>\'+ID);
  var row = $(this).parents(\'.item-row\');
  //alert($("#precio-"+I[1]).val()+\'-----------------1\');
  var price = (parseFloat($("#precio-"+I[1]).val())*1);
  price = roundNumber(price,2);
 // alert(price);
  isNaN(price) ? $("#total-"+I[1]).html("N/A") : $("#total-"+I[1]).html(price);
  update_total();
  grabarDetalle(I[1]);
}';

$code.='function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function()
  {
	 var ID = $(this).attr("id");
	 update_price(ID);
  });
  $(".qty").blur(function(){var ID = $(this).attr("id");
	  update_price(ID);
	 });
	  $(".producto").click(function(){buscarProducto();});
}';

/*BUSCAR PRODUCTO*/
	/*  */		
$code.='function buscarProducto()
{
	var row = $(this).parents(\'.item-row:last\');
	var ID = $(\'.item-row:last\').attr(\'id\');
	$("#productos_id-"+ID).val();
	if($("#productos_id-"+ID).val()!=\'\'){
	$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{
		tokenLimit: 1,
		minChars: 1,
		
		prePopulate: [ 
		             {id: $("#productos_id-"+ID).val(), name: ""+$("#producto"+ID).val()+""},
					],
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(\'\');
		   row.find(\'.pro_descripcion\').val(\'\');
		   row.find(\'.cost\').val(\'\');
		   row.find(\'.qty\').val(\'\');
		   var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}else
	{
		$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{			
		tokenLimit: 1,
		minChars: 1,
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(\'\');
		   row.find(\'.pro_descripcion\').val(\'\');
		   row.find(\'.cost\').val(\'\');
		   row.find(\'.qty\').val(\'\');
		   var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}
}';
/*BUSCAR PRODUCTO*/
	/*  */			

/*envio dew Formulario PROFORMAS*/
$code.='function GRABAR()
{
     $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabar/1",
            data:$("#inventarios").serialize(),
            beforeSend:function(){
              $("#loading").show();
          	},
            success:function(response){
                $("#loading").hide();
				$("#id").val(response);
				//alert(response);
            }
 
          	});
 
      };';

/*GRABAR DETALLE DE PROFORMAS*/

$code.='function grabarDetalle(tr_id) 
{
//alert("antes de grabar"+tr_id);
  var producto_id=$(\'#productos_id-\'+tr_id).val();;
  var concepto=$(\'#concepto\'+tr_id).val();
  var costo=isNaN($(\'#precio-\'+tr_id).val())? 0 : $(\'#precio-\'+tr_id).val();
  var cantidad=isNaN($(\'#cantidad-\'+tr_id).val())? 0 : $(\'#cantidad-\'+tr_id).val();
  var lote=$(\'#lote\'+tr_id).val();
  var empaque=isNaN($(\'#empaque\'+tr_id).val())? 0 : $(\'#empaque\'+tr_id).val();
  var bobinas=isNaN($(\'#bobinas\'+tr_id).val())? 0 : $(\'#bobinas\'+tr_id).val();
  var pesoneto=isNaN($(\'#pesoneto\'+tr_id).val())? 0 : $(\'#pesoneto\'+tr_id).val();
  var pesobruto=isNaN($(\'#pesobruto\'+tr_id).val())? 0 : $(\'#pesobruto\'+tr_id).val();
  var price = isNaN($(\'#total-\'+tr_id).html())? 0 : $(\'#total-\'+tr_id).html();
  var id_detalle=$(\'#del-\'+tr_id).attr("data-id");
  var unidadmedida_id=0;
  var colores_id=0;
  var total=isNaN(price)? 0 : price;
//alert(\'ID del PRODUCTO\'+producto_id);
//alert(\'id del detalle es 0 cuando es nuevo--->\'+id_detalle);
//alert(\'id del tr que se graba-->\'+tr_id);
  
  var dataString =\'id_detalle=\'+id_detalle+\'&tesunidadesmedidas_id=\'+ unidadmedida_id+\'&productos_id=\'+ producto_id+\'&cantidad=\'+cantidad+\'&precio=\'+costo+\'&total=\'+total+\'&lote=\'+lote+\'&concepto=\'+concepto+\'&bobinas=\'+bobinas+\'&pesoneto=\'+pesoneto+\'&pesobruto=\'+pesobruto+\'&colores_id=\'+ colores_id;
	// alert(dataString);
	  $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabarDetalle/1",
            data:dataString,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
                $("#loading").hide();
				//alert(\'id del detalle grabado\'+response);
				$(\'#del-\'+tr_id).attr("data-id", response);
				$(\'#empaque\'+tr_id).attr("data-id", response);
            }
 
         });
   
}';
$code.='function EliminarDetalle(id)
{
	if(id!=0){
	$.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/eliminarDetalle/"+id,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
				alert(\'Se elimino de la base de dato\');
            }
 
         });
	}else
	{
		return false;
	}
}';
	  ; break;
/*RECIBO POR SERVICIOS*/
case '14': 
$code.='$(document).ready(function()
		{	
			bind();  
			$("input").click(function(){
    		$(this).select();
			});';
$code.='$("#paid").blur(function(){update_balance();});
		$("#paid_total").blur(function(){update_balance();});';
   /*
  Para grabar el tr - id buscando la clase :save y mandando todo el contenido del tr:
  */
$code.='$("#addrow").click(function()
		{
			var ID = $(".item-row:last").attr("id");
			//alert(ID);
			grabarDetalle(ID);
			var IDnew=Number(ID)+Number(1);
			var tr=\''.Documentos::tr(Session::get('DOC_CODIGO'),$simbolo).'\';
    		$(".item-row:last").after(tr);
    		if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
			bind();
  		});';
/* ------- FIN contenido del tr: ------- */
$code.='$(".delete").live("click",function()
		{
		  var row =$(this).parents(".item-row")
		  var id=row.find(\'.delete\').attr(\'data-id\');
		  EliminarDetalle(id);
			$(this).parents(\'.item-row\').remove();
		  	update_total();
			if ($(".delete").length < 2) $(".delete").hide();
	    });';
/**/
  
  		//$("#pre_descripcion").blur(function(){$("#observacion").val($(this).val());GRABAR();});

$code.='$("#paid").blur(function(){$("#descuento").val($(this).val());GRABAR();});';
$code.='$("#glosa").change(function(){GRABAR();});';
$code.='$("#femision").change(function(){GRABAR();});';
$code.='$("#numero").blur(function(){GRABAR();});';
$code.='$(".grabar_form").blur(function(){GRABAR();});';
$code.='$("#monedas").change(function()
		{
			GRABAR();
			var op = $("#monedas option:selected").val();
			if(op==1)
			{
			$(".simbolo").html("S/. ");
			}
			if(op==2)
			{
			$(".simbolo").html("$. ");
			}
		});';
/*Busqueda del Cliente*/
$code.='$("#CL").tokenInput("/'.$module_name.'/ingresos/buscarcliente", 
		{
			tokenLimit: 1,
			minChars: 2,';
	if($cliente_id!=0){
	$code.='prePopulate: [ 
						 {id: '.$cliente_id.', name: "'.$nombre.'"},
						 ],';
			}
	$code.='onAdd: function (item){
			   var VAL=item.id;
			   $("#cliente_id").val(VAL);
			   $("#customer-title").val(item.name);
			   //$("#BC").hide();
			   //GrabarCliente();
			},
			onDelete: function (item) {
			}
		});';

if(Session::get('DOC_CODIGO')!='09'){
	
/*Busqueda del Cuentas de Gastos*/
$code.='$("#cuantagastos").tokenInput("/'.$module_name.'/ingresos/cuentasG", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuantagastos)){
$code.='prePopulate: [ 
		             {id: '.$cuantagastos.', name: "'.$cuantagastos_name.'"},
					 ],';
		}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   //$("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/*Busqueda del Cuentas Po pagar*/
$code.='$("#cuentaporpagar").tokenInput("/'.$module_name.'/ingresos/cuentasP", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuentaporpagar)){/*cuantagastos*/
$code.='prePopulate: [ 
		             {id: '.$cuentaporpagar.', name: "'.$cuentaporpagar_name.'"},
					 ],';
}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   //$("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/* FINN Busqueda del Cuentas Po pagar*/

$code.='$("#XC").click(function(){
		$("#BC").show();
		$("#cliente_id").val(\'\');
		$("#customer-title").val(\'\');
		//BorrarCliente()
		});';
}

$code.='});';
$code.='function roundNumber(number,decimals)
		{
  			var newString;// The new rounded number
  			decimals = Number(decimals);
  			if (decimals < 1) 
			{
    			newString = (Math.round(number)).toString();
  			}else
			{
    			var numString = number.toString();
    			if (numString.lastIndexOf(".") == -1)
				{// If there is no decimal point
      				numString += ".";// give it one at the end
    			}
    			var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
    			var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we\'ll end up with
    			var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
    			if (d2 >= 5)
				{// Do we need to round up at all? If not, the string will just be truncated
      				if (d1 == 9 && cutoff > 0)
					{// If the last digit is 9, find a new cutoff point
        				while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          				if (d1 != ".")
						{
            				cutoff -= 1;
            				d1 = Number(numString.substring(cutoff,cutoff+1));
         				} else {
            				cutoff -= 1;
          				}
        			}
      			}
      			d1 += 1;
    		} 
    		if (d1 == 10)
			{
      			numString = numString.substring(0, numString.lastIndexOf("."));
      			var roundedNum = Number(numString) + 1;
      			newString = roundedNum.toString() + \'.\';
    		}else
			{
				newString = numString.substring(0,cutoff) + d1.toString();
		    }
  		}
  		if (newString.lastIndexOf(".") == -1) 
		{// Do this again, to the new string
    		newString += ".";
  		}
  		var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
  		for(var i=0;i<decimals-decs;i++) newString += "0";
  		//var newNumber = Number(newString);// make it a number if you like
		return newString; // Output the result to the form field (change for your purposes)
		}';
/*FIN DE REDONDEO*/
$code.='function update_total()
		{
  			var total = 0.00;
  			$(\'.price\').each(function(i)
  			{
    			price = $(this).html();
    			//alert (\'precio: \'+price+\' ->>>>\'+i);
				if(!isNaN(price)) total =parseFloat(total)+parseFloat(price);
  				// alert (\'el total es : \'+total+\' ->>>>\');
   			});
			total = roundNumber(total,2);
			//alert (\'el total es : \'+total+\' ->>>>\');
			var igv= total*'.$igv.';
			var totalconigv=(parseFloat(total)+parseFloat(igv));
			//alert(\'el igv es: \'+igv);
			$("#paid").val(igv.toFixed(2));
			$("#igv").val(igv.toFixed(2));
			$("#totalconigv").val(totalconigv);
			$(\'#subtotal\').html(total);
			$(\'#paid_total\').val(total);
			update_balance();
		}';

$code.='function update_balance()
{
 // alert ($("#paid").val());
  var igv= parseFloat( $("#paid_total").val())*'.$igv.';
	var totalconigv=(parseFloat($("#paid_total").val())+parseFloat(igv));
	var due=roundNumber(totalconigv,2);
	var IGV=roundNumber(igv,2);
	$("#paid").val(IGV);
	$("#igv").val(IGV);
	$("#totalconigv").val(due);
  $("#totalconigv").val(due);
  //alert (\'===: \'+due);
	$(\'.due\').html(due);
	$("#moneda_en_letras").html(covertirNumLetras(due));
	$("#totalenletras").val(covertirNumLetras(due));
	GRABAR();
}';

$code.='function update_price(ID)
{
  //var ID = $(this).attr(\'id\');
  var I=ID.split(\'-\');
  //alert (\'id del tr que se manda a grabar->>>\'+ID);
  var row = $(this).parents(\'.item-row\');
  //alert($("#precio-"+I[1]).val()+\'-----------------1\');
  var price = (parseFloat($("#precio-"+I[1]).val())*1);
  price = roundNumber(price,2);
  //alert(price);
  isNaN(price) ? $("#total-"+I[1]).html("N/A") : $("#total-"+I[1]).html(price);
  update_total();
  grabarDetalle(I[1]);
}';

$code.='function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function()
  {
	 var ID = $(this).attr("id");
	 update_price(ID);
  });
  $(".qty").blur(function(){var ID = $(this).attr("id");
	  update_price(ID);
	 });
}';

/*BUSCAR PRODUCTO*/
	/*  */		

/*envio dew Formulario PROFORMAS*/
$code.='function GRABAR()
{
     $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabar/1",
            data:$("#inventarios").serialize(),
            beforeSend:function(){
              $("#loading").show();
          	},
            success:function(response){
                $("#loading").hide();
				$("#id").val(response);
				//alert(response);
            }
 
          	});
 
      };';

/*GRABAR DETALLE DE PROFORMAS*/

$code.='function grabarDetalle(tr_id) 
{
//alert("antes de grabar"+tr_id);
  var producto_id=0;
  var concepto=$(\'#concepto\'+tr_id).val();
  var costo=isNaN($(\'#precio-\'+tr_id).val())? 0 : $(\'#precio-\'+tr_id).val();
  var cantidad=isNaN($(\'#cantidad-\'+tr_id).val())? 0 : $(\'#cantidad-\'+tr_id).val();
  var lote=$(\'#lote\'+tr_id).val();
  var empaque=isNaN($(\'#empaque\'+tr_id).val())? 0 : $(\'#empaque\'+tr_id).val();
  var bobinas=isNaN($(\'#bobinas\'+tr_id).val())? 0 : $(\'#bobinas\'+tr_id).val();
  var pesoneto=isNaN($(\'#pesoneto\'+tr_id).val())? 0 : $(\'#pesoneto\'+tr_id).val();
  var pesobruto=isNaN($(\'#pesobruto\'+tr_id).val())? 0 : $(\'#pesobruto\'+tr_id).val();
  var price = isNaN($(\'#total-\'+tr_id).html())? 0 : $(\'#total-\'+tr_id).html();
  var id_detalle=$(\'#del-\'+tr_id).attr("data-id");
  var unidadmedida_id=0;
  var colores_id=0;
  var total=isNaN(price)? 0 : price;
//alert(\'ID del PRODUCTO\'+producto_id);
//alert(\'id del detalle es 0 cuando es nuevo--->\'+id_detalle);
//alert(\'id del tr que se graba-->\'+tr_id);
  
  var dataString =\'id_detalle=\'+id_detalle+\'&tesunidadesmedidas_id=\'+ unidadmedida_id+\'&productos_id=\'+ producto_id+\'&cantidad=\'+cantidad+\'&precio=\'+costo+\'&total=\'+total+\'&lote=\'+lote+\'&concepto=\'+concepto+\'&bobinas=\'+bobinas+\'&pesoneto=\'+pesoneto+\'&pesobruto=\'+pesobruto+\'&colores_id=\'+ colores_id;
	 //alert(dataString);
	  $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabarDetalle/1",
            data:dataString,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
                $("#loading").hide();
				//alert(\'id del detalle grabado\'+response);
				$(\'#del-\'+tr_id).attr("data-id", response);
				$(\'#empaque\'+tr_id).attr("data-id", response);
            }
 
         });
   
}';
$code.='function EliminarDetalle(id)
{
	if(id!=0){
	$.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/eliminarDetalle/"+id,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
				alert(\'Se elimino de la base de dato\');
            }
 
         });
	}else
	{
		return false;
	}
}';
	  ; break;
/* LETRAS */
	  case '101':
	  //inicio de ready
$code.='$(document).ready(function()
		{	 
			$(".grabar_form").click(function(){
    		GRABAR();
			});';
 
$code.='$("#importe").blur(function(){$("#totalconigv").val($(this).val());GRABAR();
		$("#moneda_en_letras").html(covertirNumLetras($(this).val()));
		$("#totalenletras").val(covertirNumLetras($(this).val()));
});';
$code.='$("#glosa").change(function(){GRABAR();});';
$code.='$("#femision").change(function(){GRABAR();});';
$code.='$("#numero").blur(function(){GRABAR();});';
$code.='$("#monedas").change(function()
		{
			GRABAR();
			var op = $("#monedas option:selected").val();
			if(op==1)
			{
			$(".simbolo").html("S/. ");
			}
			if(op==2)
			{
			$(".simbolo").html("$. ");
			}
		});';
/*Busqueda del Cliente*/
$code.='$("#CL").tokenInput("/'.$module_name.'/ingresos/buscarcliente", 
		{
			tokenLimit: 1,
			minChars: 2,';
	if($cliente_id!=0){
	$code.='prePopulate: [ 
						 {id: '.$cliente_id.', name: "'.$nombre.'"},
						 ],';
			}
	$code.='onAdd: function (item){
			   var VAL=item.id;
			   $("#cliente_id").val(VAL);
			   $("#customer-title").val(item.name);
			   //$("#BC").hide();
			   GRABAR();
			},
			onDelete: function (item) {
			}
		});';
/*---FIN Busqueda del Cliente*/	

if(Session::get('DOC_CODIGO')!='09'){
	
/*Busqueda del Cuentas de Gastos*/
$code.='$("#cuantagastos").tokenInput("/'.$module_name.'/ingresos/cuentasG", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuantagastos)){
$code.='prePopulate: [ 
		             {id: '.$cuantagastos.', name: "'.$cuantagastos_name.'"},
					 ],';
		}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   $("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   //GrabarCliente();
		},
		onDelete: function (item) {
		}
		});';
/*Busqueda del Cuentas Po pagar*/
$code.='$("#cuentaporpagar").tokenInput("/'.$module_name.'/ingresos/cuentasP", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuentaporpagar)){/*cuantagastos*/
$code.='prePopulate: [ 
		             {id: '.$cuentaporpagar.', name: "'.$cuentaporpagar_name.'"},
					 ],';
}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   GRABAR();
		},
		onDelete: function (item) {
		}
		});';
/* FINN Busqueda del Cuentas Po pagar*/

$code.='$("#XC").click(function(){
		$("#BC").show();
		$("#cliente_id").val(\'\');
		$("#customer-title").val(\'\');
		//BorrarCliente()
		});';
}

$code.='});';
/*FIN DE READY*/
/*envio dew Formulario PROFORMAS*/
$code.='function GRABAR()
{
	 $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabar/1",
            data:$("#inventarios").serialize(),
            beforeSend:function(){
              $("#loading").show();
          	},
            success:function(response){
                $("#loading").hide();
				$("#id").val(response);
            }
 
          	});
 
      };';

; break;
/* CHEUQES */
case '104':
	  //inicio de ready
$code.='$(document).ready(function()
		{	 
			$("input").click(function(){
    		$(this).select();
			});';
 
$code.='$("#importe").blur(function(){$("#totalconigv").val($(this).val());GRABAR();
	$("#moneda_en_letras").html(covertirNumLetras($(this).val()));
	$("#totalenletras").val(covertirNumLetras($(this).val()));
});';
$code.='$("#glosa").change(function(){GRABAR();});';
$code.='$("#femision").change(function(){GRABAR();});';
$code.='$("#numero").blur(function(){GRABAR();});';
$code.='$("#monedas").change(function()
		{
			GRABAR();
			var op = $("#monedas option:selected").val();
			if(op==1)
			{
			$(".simbolo").html("S/. ");
			}
			if(op==2)
			{
			$(".simbolo").html("$. ");
			}
		});';
/*Busqueda del Cliente*/
$code.='$("#CL").tokenInput("/'.$module_name.'/ingresos/buscarcliente", 
		{
			tokenLimit: 1,
			minChars: 2,';
	if($cliente_id!=0){
	$code.='prePopulate: [ 
						 {id: '.$cliente_id.', name: "'.$nombre.'"},
						 ],';
			}
	$code.='onAdd: function (item){
			   var VAL=item.id;
			   $("#cliente_id").val(VAL);
			   $("#customer-title").val(item.name);
			   //$("#BC").hide();
			   //GrabarCliente();
			},
			onDelete: function (item) {
			}
		});';
/*---FIN Busqueda del Cliente*/	

if(Session::get('DOC_CODIGO')!='09'){
	
/*Busqueda del Cuentas de Gastos*/
$code.='$("#cuantagastos").tokenInput("/'.$module_name.'/ingresos/cuentasG", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuantagastos)){
$code.='prePopulate: [ 
		             {id: '.$cuantagastos.', name: "'.$cuantagastos_name.'"},
					 ],';
		}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   $("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   //GrabarCliente();
		},
		onDelete: function (item) {
		}
		});';
/*Busqueda del Cuentas Po pagar*/
$code.='$("#cuentaporpagar").tokenInput("/'.$module_name.'/ingresos/cuentasP", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuentaporpagar)){/*cuantagastos*/
$code.='prePopulate: [ 
		             {id: '.$cuentaporpagar.', name: "'.$cuentaporpagar_name.'"},
					 ],';
}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   $("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   //GrabarCliente();
		},
		onDelete: function (item) {
		}
		});';
/* FINN Busqueda del Cuentas Po pagar*/

$code.='$("#XC").click(function(){
		$("#BC").show();
		$("#cliente_id").val(\'\');
		$("#customer-title").val(\'\');
		//BorrarCliente()
		});';
}

$code.='});';
/*FIN DE READY*/
/*envio dew Formulario PROFORMAS*/
$code.='function GRABAR()
{
     $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabar/1",
            data:$("#inventarios").serialize(),
            beforeSend:function(){
              $("#loading").show();
          	},
            success:function(response){
                $("#loading").hide();
				$("#id").val(response);
				//alert(response);
            }
 
          	});
 
      };';

; break;

	  case NULL: 
	  $code='
function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function(){update_price();});
  $(".qty").blur(function(){update_price();});
  $(".producto").click(function(){buscarProducto();});
  $(".unidad").click(function(){buscarUnidad();});
}';
	  ; break;
	  default: 
	  //inicio de ready
$code.='$(document).ready(function()
		{	
			bind();  
			$("input").click(function(){
    		$(this).select();
			});';
$code.='$("#paid").blur(function(){update_balance();});
		$("#paid_total").blur(function(){update_balance();});';
   /*
  Para grabar el tr - id buscando la clase :save y mandando todo el contenido del tr:
  */
$code.='$("#addrow").click(function()
		{
			var ID = $(".item-row:last").attr("id");
			//alert(ID);
			grabarDetalle(ID);
			var IDnew=Number(ID)+Number(1);
			var tr=\''.Documentos::tr(Session::get('DOC_CODIGO'),$simbolo).'\';
    		$(".item-row:last").after(tr);
    		if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
			bind();
  		});';
/* ------- FIN contenido del tr: ------- */
$code.='$(".delete").live("click",function()
		{
		  var row =$(this).parents(".item-row")
		  var id=row.find(\'.delete\').attr(\'data-id\');
		  EliminarDetalle(id);
			$(this).parents(\'.item-row\').remove();
		  	update_total();
			if ($(".delete").length < 2) $(".delete").hide();
	    });';
/**/
  
  		//$("#pre_descripcion").blur(function(){$("#observacion").val($(this).val());GRABAR();});

$code.='$("#paid").blur(function(){$("#descuento").val($(this).val());GRABAR();});';
$code.='$("#glosa").change(function(){GRABAR();});';
$code.='$("#femision").change(function(){GRABAR();});';
$code.='$("#numero").blur(function(){GRABAR();});';
$code.='$("#monedas").change(function()
		{
			GRABAR();
			var op = $("#monedas option:selected").val();
			if(op==1)
			{
			$(".simbolo").html("S/. ");
			}
			if(op==2)
			{
			$(".simbolo").html("$. ");
			}
		});';
/*Busqueda del Cliente*/
$code.='$("#CL").tokenInput("/'.$module_name.'/ingresos/buscarcliente", 
		{
			tokenLimit: 1,
			minChars: 2,';
	if($cliente_id!=0){
	$code.='prePopulate: [ 
						 {id: '.$cliente_id.', name: "'.$nombre.'"},
						 ],';
			}
	$code.='onAdd: function (item){
			   var VAL=item.id;
			   $("#cliente_id").val(VAL);
			   $("#customer-title").val(item.name);
			   //$("#BC").hide();
			   //GrabarCliente();
			},
			onDelete: function (item) {
			}
		});';
/*---FIN Busqueda del Cliente*/	
/* Numero de Guia */
$code.='if($("#numeroguia").val()!="")
		{
			$("#numeroguia").tokenInput("/'.$module_name.'/ingresos/guiadelafactura/", 
			{
			tokenLimit: 1,
			minChars: 1,
			prePopulate: [ 
						 {id: $("#numeroguia").val(), name: ""+$("#numeroguia").val()+""},
						],
			onAdd: function (item) {
			   var ID=item.name.split("-");
				$.ajax({
				type:"POST",
				url:"/'.$module_name.'/ingresos/facturaguia/"+ID[0]+"/"+ID[1],
				beforeSend:function(){
				  $("#loading").show();
				},
				success:function(response){
					$("#factura-01").html("");
					$("#loading").hide();
					//alert(response);
					$("#factura-01").html(response);
				}
				});
			},
			onDelete: function (item)
			{
				$.ajax({
				type:"POST",
				url:"/'.$module_name.'/ingresos/facturaguia/0/0",
				beforeSend:function(){
				  $("#loading").show();
				},
				success:function(response){
					
					$("#factura-01").html("");
					$("#loading").hide();
					$("#factura-01").html(response);
				}
				});
				}
			});
		}else{
			$("#numeroguia").tokenInput("/'.$module_name.'/ingresos/guiadelafactura/", 
			{
				
			tokenLimit: 1,
			minChars: 1,
			onAdd: function (item) {
			   var ID=item.name.split("-");
				$.ajax({
				type:"POST",
				url:"/'.$module_name.'/ingresos/facturaguia/"+ID[0]+"/"+ID[1],
				beforeSend:function(){
				  $("#loading").show();
				},
				success:function(response){
					$("#factura-01").html("");
					$("#loading").hide();
					//alert(response);
					$("#factura-01").html(response);
				}
				});
			},
			onDelete: function (item) {
				$.ajax({
				type:"POST",
				url:"/'.$module_name.'/ingresos/facturaguia/0/0",
				beforeSend:function(){
				  $("#loading").show();
				},
				success:function(response){
					$("#factura-01").html("");
					$("#loading").hide();
					$("#factura-01").html(response);
				}
				});
				}
			});
		}';
/*fin numero de Guia*/

if(Session::get('DOC_CODIGO')!='09'){
	
/*Busqueda del Cuentas de Gastos*/
$code.='$("#cuantagastos").tokenInput("/'.$module_name.'/ingresos/cuentasG", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuantagastos)){
$code.='prePopulate: [ 
		             {id: '.$cuantagastos.', name: "'.$cuantagastos_name.'"},
					 ],';
		}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   $("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		},
		onDelete: function (item) {
		}
		});';
/*Busqueda del Cuentas Po pagar*/
$code.='$("#cuentaporpagar").tokenInput("/'.$module_name.'/ingresos/cuentasP", 
		{
		tokenLimit: 1,
		minChars: 2,';
if(!empty($cuentaporpagar)){/*cuantagastos*/
$code.='prePopulate: [ 
		             {id: '.$cuentaporpagar.', name: "'.$cuentaporpagar_name.'"},
					 ],';
}
$code.='onAdd: function (item) {
		   var VAL=item.id;
		   $("#cliente_id").val(VAL);
		   $("#customer-title").val(item.name);
		   //$("#BC").hide();
		   //GrabarCliente();
		},
		onDelete: function (item) {
		}
		});';
/* FINN Busqueda del Cuentas Po pagar*/

$code.='$("#XC").click(function(){
		$("#BC").show();
		$("#cliente_id").val(\'\');
		$("#customer-title").val(\'\');
		//BorrarCliente()
		});';
}

$code.='});';
$code.='function roundNumber(number,decimals)
		{
  			var newString;// The new rounded number
  			decimals = Number(decimals);
  			if (decimals < 1) 
			{
    			newString = (Math.round(number)).toString();
  			}else
			{
    			var numString = number.toString();
    			if (numString.lastIndexOf(".") == -1)
				{// If there is no decimal point
      				numString += ".";// give it one at the end
    			}
    			var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
    			var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we\'ll end up with
    			var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
    			if (d2 >= 5)
				{// Do we need to round up at all? If not, the string will just be truncated
      				if (d1 == 9 && cutoff > 0)
					{// If the last digit is 9, find a new cutoff point
        				while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          				if (d1 != ".")
						{
            				cutoff -= 1;
            				d1 = Number(numString.substring(cutoff,cutoff+1));
         				} else {
            				cutoff -= 1;
          				}
        			}
      			}
      			d1 += 1;
    		} 
    		if (d1 == 10)
			{
      			numString = numString.substring(0, numString.lastIndexOf("."));
      			var roundedNum = Number(numString) + 1;
      			newString = roundedNum.toString() + \'.\';
    		}else
			{
				newString = numString.substring(0,cutoff) + d1.toString();
		    }
  		}
  		if (newString.lastIndexOf(".") == -1) 
		{// Do this again, to the new string
    		newString += ".";
  		}
  		var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
  		for(var i=0;i<decimals-decs;i++) newString += "0";
  		//var newNumber = Number(newString);// make it a number if you like
		return newString; // Output the result to the form field (change for your purposes)
		}';
/*FIN DE REDONDEO*/
$code.='function update_total()
		{
  			var total = 0.00;
  			$(\'.price\').each(function(i)
  			{
    			price = $(this).html();
    			//alert (\'precio: \'+price+\' ->>>>\'+i);
				if(!isNaN(price)) total =parseFloat(total)+parseFloat(price);
  				// alert (\'el total es : \'+total+\' ->>>>\');
   			});
			total = roundNumber(total,2);
			//alert (\'el total es : \'+total+\' ->>>>\');
			var igv= total*'.$igv.';
			var totalconigv=(parseFloat(total)+parseFloat(igv));
			//alert(\'el igv es: \'+igv);
			$("#paid").val(igv.toFixed(2));
			$("#igv").val(igv.toFixed(2));
			$("#totalconigv").val(totalconigv);
			$(\'#subtotal\').html(total);
			$(\'#paid_total\').val(total);
			update_balance();
		}';

$code.='function update_balance()
{
 // alert ($("#paid").val());
  var igv= parseFloat( $("#paid_total").val())*'.$igv.';
	var totalconigv=(parseFloat($("#paid_total").val())+parseFloat(igv));
	var due=roundNumber(totalconigv,2);
	var IGV=roundNumber(igv,2);
	$("#paid").val(IGV);
	$("#igv").val(IGV);
	$("#totalconigv").val(due);
  $("#totalconigv").val(due);
  //alert (\'===: \'+due);
	$(\'.due\').html(due);
	GRABAR();
}';

$code.='function update_price(ID)
{
  //var ID = $(this).attr(\'id\');
  var I=ID.split(\'-\');
  //alert (\'id del tr que se manda a grabar->>>\'+ID);
  var row = $(this).parents(\'.item-row\');
  //alert(row.find(\'.cost\').val());
  //var price = row.find(\'.cost\').val() * row.find(\'.qty\').val();
  //alert($("#precio-"+I[1]).val()+\'-----------------\'+$("#cantidad-"+I[1]).val());
  var price = (parseFloat($("#precio-"+I[1]).val())*parseFloat($("#cantidad-"+I[1]).val()));
  price = roundNumber(price,2);
  //alert(price);
  isNaN(price) ? $("#total-"+I[1]).html("N/A") : $("#total-"+I[1]).html(price);
  update_total();
  grabarDetalle(I[1]);
}';

$code.='function bind(){
  if ($(".delete").length > 1) $(".delete").show();else $(".delete").hide();
  $(".cost").blur(function()
  {
	 var ID = $(this).attr("id");
	 //alert(ID);
	 update_price(ID);
  });
  $(".qty").blur(function(){update_price();});
  $(".producto").click(function(){buscarProducto();});
  $(".unidad").click(function(){buscarUnidad();});
}';

/*BUSCAR PRODUCTO*/
	/*  */		
$code.='function buscarProducto()
{
	var row = $(this).parents(\'.item-row:last\');
	var ID = $(\'.item-row:last\').attr(\'id\');
	$("#productos_id-"+ID).val();
	if($("#productos_id-"+ID).val()!=\'\'){
	$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{
		tokenLimit: 1,
		minChars: 1,
		
		prePopulate: [ 
		             {id: $("#productos_id-"+ID).val(), name: ""+$("#producto"+ID).val()+""},
					],
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(\'\');
		   row.find(\'.pro_descripcion\').val(\'\');
		   row.find(\'.cost\').val(\'\');
		   row.find(\'.qty\').val(\'\');
		   var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}else
	{
		$("#producto"+ID).tokenInput("/'.$module_name.'/ingresos/producto/", 
		{			
		tokenLimit: 1,
		minChars: 1,
		onAdd: function (item) {
		   var VAL=item.id;
		   var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(VAL[\'id\']);
		   row.find(\'.pro_descripcion\').val(VAL[\'detalle\']);
		   row.find(\'.cost\').val(VAL[\'precio\']);
		   row.find(\'.delete\').attr("data-id",0);
		   $(\'#codigo-\'+ID).val(VAL[\'codigo\']);
		   $(\'#ver\'+ID).hide();
		   $(\'#ver-name\'+ID).show();
		   $(\'#productoname-\'+ID).val(VAL[\'detalle\']);
		},
		onDelete: function (item) {
			var row =$(this).parents(\'.item-row\')
		   row.find(\'.productos_id\').val(\'\');
		   row.find(\'.pro_descripcion\').val(\'\');
		   row.find(\'.cost\').val(\'\');
		   row.find(\'.qty\').val(\'\');
		   var id=row.find(\'.delete\').attr(\'data-id\');
		  	EliminarDetalle(id);
		}
	});
	}
}';
/*BUSCAR PRODUCTO*/
	/*  */		
$code.='function buscarUnidad()
{
	var row = $(this).parents(\'.item-row:last\');
	var ID = $(\'.item-row:last\').attr(\'id\');
	//alert($("#tesunidadesmedidas"+ID).val());
	if($("#tesunidadesmedidas"+ID).val()!=\'\'){
	$("#tesunidadesmedidas"+ID).tokenInput("/'.$module_name.'/ingresos/medidas/", 
		{			
		tokenLimit: 1,
		minChars: 1,
		prePopulate: [ 
		             {id: $("#tesunidadesmedidas_id-"+ID).val(), name: ""+$("#tesunidadesmedidas"+ID).val()+""},
					],
		onAdd: function (item) {
		   var VAL=item.id;
		   $("#tesunidadesmedidas_id-"+ID).val(item.id);
		   $("#tesunidadesmedidas"+ID).val(item.name);
		   grabarDetalle(ID);
		},
		onDelete: function (item) {
		  $("#tesunidadesmedidas_id-"+ID).val(\'\');
		   $("#tesunidadesmedidas"+ID).val(\'\');
		}
	});
	}else
	{
	$("#tesunidadesmedidas"+ID).tokenInput("/'.$module_name.'/ingresos/medidas/", 
		{
			
		tokenLimit: 1,
		minChars: 1,
		onAdd: function (item) {
		   var VAL=item.id;
		   $("#tesunidadesmedidas_id-"+ID).val(item.id);
		   $("#tesunidadesmedidas"+ID).val(item.name);
		   grabarDetalle(ID);
		},
		onDelete: function (item) {
		  $("#tesunidadesmedidas_id-"+ID).val(\'\');
		   $("#tesunidadesmedidas"+ID).val(\'\');
		}
	});
	}
}';

/*envio dew Formulario PROFORMAS*/
$code.='function GRABAR()
{
     $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabar/1",
            data:$("#inventarios").serialize(),
            beforeSend:function(){
              $("#loading").show();
          	},
            success:function(response){
                $("#loading").hide();
				$("#id").val(response);
				//alert(response);
            }
 
          	});
 
      };';

/*GRABAR DETALLE DE PROFORMAS*/

$code.='function grabarDetalle(tr_id) 
{
  //alert(tr_id);
  var producto_id=$(\'#productos_id-\'+tr_id).val();
  var pro_descripcion=$(\'#pro_descripcion-\'+tr_id).val();
  var costo=isNaN($(\'#precio-\'+tr_id).val())? 0 : $(\'#precio-\'+tr_id).val();
  var cantidad=isNaN($(\'#cantidad-\'+tr_id).val())? 0 : $(\'#cantidad-\'+tr_id).val();
  var lote=$(\'#lote\'+tr_id).val();
  var empaque=isNaN($(\'#empaque\'+tr_id).val())? 0 : $(\'#empaque\'+tr_id).val();
  var bobinas=isNaN($(\'#bobinas\'+tr_id).val())? 0 : $(\'#bobinas\'+tr_id).val();
  var pesoneto=isNaN($(\'#pesoneto\'+tr_id).val())? 0 : $(\'#pesoneto\'+tr_id).val();
  var pesobruto=isNaN($(\'#pesobruto\'+tr_id).val())? 0 : $(\'#pesobruto\'+tr_id).val();
  var price = isNaN($(\'#total-\'+tr_id).html())? 0 : $(\'#total-\'+tr_id).html();
  var id_detalle=$(\'#del-\'+tr_id).attr("data-id");
  var unidadmedida_id=$(\'#tesunidadesmedidas_id-\'+tr_id).val();
  var colores_id=$(\'#tescolores_id-\'+tr_id).val();
  var total=isNaN(price)? 0 : price;
 //alert(\'ID del PRODUCTO\'+producto_id);
 //alert(\'id del detalle es 0 cuando es nuevo--->\'+id_detalle);
// alert(\'id del tr que se graba-->\'+tr_id);
  
  if(producto_id!=\'\' && unidadmedida_id!=\'\')
  {
	  var dataString =\'id_detalle=\'+id_detalle+\'&tesunidadesmedidas_id=\'+ unidadmedida_id+\'&productos_id=\'+ producto_id+\'&pro_descripcion=\'+pro_descripcion+\'&cantidad=\'+cantidad+\'&precio=\'+costo+\'&total=\'+total+\'&lote=\'+lote+\'&empaque=\'+empaque+\'&bobinas=\'+bobinas+\'&pesoneto=\'+pesoneto+\'&pesobruto=\'+pesobruto+\'&colores_id=\'+ colores_id;
	 //alert(dataString);
	  $.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/grabarDetalle/1",
            data:dataString,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
                $("#loading").hide();
				//alert(\'id del detalle grabado\'+response);
				$(\'#del-\'+tr_id).attr("data-id", response);
				$(\'#empaque\'+tr_id).attr("data-id", response);
            }
 
         });
  }
  
}';
$code.='function EliminarDetalle(id)
{
	if(id!=0){
	$.ajax({
            type:"POST",
            url:"/'.$module_name.'/ingresos/eliminarDetalle/"+id,
            beforeSend:function(){
              $("#loading").show();
            },
            success:function(response){
				alert(\'Se elimino de la base de dato\');
            }
 
         });
	}else
	{
		return false;
	}
}';
	  ; break;
	  }
	return $code;
		
	}
}
?>