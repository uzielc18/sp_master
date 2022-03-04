<?php
Load::models('testipocambios'); 
class Sp{

public static function cambio(){
	?>
	<script type="text/javascript">
    $(document).ready(function()
	{
		$("#cambio").blur(function(){
			var fecha='<?php echo date('Y-m-d')?>';
			var compra=$(this).val();
			var id=$(this).attr('data-id'); 
			var dataString ='fecha='+fecha+'&compra='+compra+'&id='+id;
			//alert(dataString);
	  $.ajax({
            type:"POST",
            url:"/admin/testipocambios/guardarhoy/",
            data:dataString,
            beforeSend:function(){
              $('#cargando').show();
            },
            success:function(response){
				$('#cargando').hide();
                $('#Cambio').removeAttr('class');
				$('#Cambio span').html('Dolar: ');
				//alert(response);
				$('#cambio').attr('data-id',response);
				$("#myModal").modal("hide");		
            }
 
         });
			});	
	});
    </script>
   	<?php
	$cambios= new Testipocambios();
	$hoy= date('Y-m-d');
	
	if($cambios->exists('fecha="'.$hoy.'"')){
	$cc=$cambios->find_first('fecha="'.$hoy.'"');
	$texto='Dolar:';
	$class='';
	$id=$cc->id;
	$code='';
	$code.='<div id="Cambio"'.$class.'><span>'.$texto.'</span><input id="cambio" type="text" size="5" placeholder="00.00" value="'.$cc->compra.'" data-id="'.$id.'" />
	<div id="cargando" style="display:none;"><img src="/img/spin.gif" /></div>
	</div>';
	}else
	{
	$cc=$cambios->find_first('order: fecha DESC');
	//$class=' class="dolar_nuevo"';
	$texto='';
	
	$id=0;
	$code='
	<button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-lg">
	Ingrese El valor de Hoy:
  	</button>
	<div class="modal fade" id="modal-lg" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Large Modal</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body"> Para ver el cambio del Dia haga Clik en <a href="http://www.sunat.gob.pe/cl-at-ittipcam/tcS01Alias" target="hola"> Tipo de Cambio</a>
			<div id="Cambio"><input id="cambio" type="text" size="5" placeholder="00.00" value="'.$cc->compra.'" data-id="'.$id.'" />
			<div id="cargando" style="display:none;"><img src="/img/spin.gif" /></div>
			</div>
			<iframe name="hola" style="width: 100%;
			min-height: 540px;"></iframe>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save changes</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
	';

	$code.='<script type="text/javascript">
</script>'
	;
	}
	return $code;
}

public static function getArray($params){
        $data = array();
		$params =explode(',',$params);
        foreach($params as $p) {
            if(is_string($p)) {
                $match = explode(':',$p);
                if(isset($match[1])) {
                    $data[$match[0]] = $match[1];
                } else {
                    $data[] = $p;
                }
            } else {
                $data[] = $p;
            }
        }
        return $data;
}

public static function formatFecha($format='%A %d de %B del %Y',$fecha)
{	
	$fecha = $fecha; 
    $lenguage = 'es_ES.UTF-8';
	putenv("LANG=$lenguage");
	setlocale(LC_ALL, $lenguage);
    return strftime($format,strtotime($fecha)); 
}

public static function getMes($fecha)
{	
	$fecha = $fecha; 
    $lenguage = 'es_ES.UTF-8';
	putenv("LANG=$lenguage");
	setlocale(LC_ALL, $lenguage);
	$meses=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
	return $meses[strftime("%m",strtotime($fecha))];
}

public static function Ndias($fecha_mayor,$fecha_menor)
{
	/*function diasEntreFechas($fechainicio, $fechafin){
    return ((strtotime($fechafin)-strtotime($fechainicio))/86400);
	}*/
	$cambios= new Testipocambios();
	$n=$cambios->find_by_sql("SELECT DATEDIFF('".$fecha_mayor."','".$fecha_menor."' ) as ndias");
	return $n->ndias;
}

public static function sumarDias($fecha,$ndias)
{
	return date("Y-m-d", strtotime($fecha." +".$ndias." day"));
	/*$Fecha = mktime(0, 0, 0, date("m")+1, date("d"), date("Y"));
	date("m/d/Y", $Fecha);*/
}

public static function imprimir($nombre='Reporte',$direccion='P',$val=array())
{
$code='<div class="report-actions">';
if(!empty($val))
{
	if(array_key_exists('W',$val)) $code.='<a class="word botonword">&nbsp;</a>&nbsp;';
	if(array_key_exists('P',$val)) $code.='<a href="javascrips:;" class="print">&nbsp;</a>&nbsp;';
	if(array_key_exists('PDF',$val)) $code.='<a href="javascript:;" class="pdf botonpdf">&nbsp;</a>&nbsp;';
	if(array_key_exists('EX',$val)) $code.='<a href="javascript:;" class="excel botonExcel">&nbsp;</a>';
	/*jquerys*/
	$code.='
	<script type="text/javascript">
	$(document).ready(function()
	{';
	if(array_key_exists('PDF',$val))
	{
		$code.='$(".botonpdf").click(function(event)
	{
		alert("Generar PDF");
		$("#datos_a_enviar_pdf").val( $("<div>").append( $("div.page").eq(0).clone()).html());
		$("#FormularioExportacionPdf").submit();
	});';
	}
	if(array_key_exists('W',$val))
	{
		 $code.='
		$(".botonword").click(function(event)
		{
			alert("Generar WORD");
			//alert($("<div>").append( $(".page").eq(0).clone()).html());
			$("#datos_a_enviar_word").val( $("<div>").append( $("div.page").eq(0).clone()).html());
			$("#Formularioword").submit();
		});
		';
	}
	if(array_key_exists('P',$val))
	{
		$code.='$(".print").click(function() {
		$("div.page").printArea();
		return false;
	});';
	}
	if(array_key_exists('EX',$val))
	{
		$code.='$(".botonExcel").click(function(event)
	{
		alert("Generar EXCEL");
		$("#datos_a_enviar").val($("<div>").append( $("#exportar_excel").eq(0).clone()).html());
		$("#Formularioexcel").submit();
	});';
	}
	$code.='});
</script>';
/*fin de jquerys*/
/*INICIO FORM*/
	if(array_key_exists('PDF',$val))
	{
		$code.='<form action="/utiles/pdf_publico" method="post" target="_blank" id="FormularioExportacionPdf">
<input type="hidden" id="datos_a_enviar_pdf" name="datos_a_enviar_pdf"/>
<input type="hidden" id="nombre" name="nombre" value="'.$nombre.date("dmY").'"/>
<input type="hidden" id="direccion" name="direccion" value="'.$direccion.'"/>
</form>';
	}
	if(array_key_exists('W',$val))
	{
		 $code.='<form action="/utiles/word" method="post" target="_blank" id="Formularioword">
<input type="hidden" id="datos_a_enviar_word" name="datos_a_enviar_word"/>
<input type="hidden" id="nombre" name="nombre" value="'.$nombre.' '.date("dmY").'"/>
<input type="hidden" id="direccion" name="direccion" value="'.$direccion.'"/>
</form>';
	}
	
	if(array_key_exists('EX',$val))
	{
		$code.='<form action="/utiles/excel" method="post" target="_blank" id="Formularioexcel">
<input type="hidden" id="datos_a_enviar" name="datos_a_enviar"/>
<input type="hidden" id="nombre" name="nombre" value="'.$nombre.date("dmY").'"/>
<input type="hidden" id="direccion" name="direccion" value="'.$direccion.'"/>
</form>';
	}
$code.='</div>';
/*FIN DE FORM*/
	echo $code;
}else{

?>
<div class="report-actions">
<a href="javascript:;" class="word botonword">&nbsp;</a>&nbsp;
<a href="javascript:;" class="print">&nbsp;</a>&nbsp;
<a href="javascript:;" class="pdf botonpdf">&nbsp;</a>&nbsp;
<a href="javascript:;" class="excel botonExcel">&nbsp;</a>
<form action="/utiles/pdf_publico" method="post" target="_blank" id="FormularioExportacionPdf">
<input type="hidden" id="datos_a_enviar_pdf" name="datos_a_enviar_pdf"/>
<input type="hidden" id="nombre" name="nombre" value="<?php echo $nombre?><?php echo date("dmY");?>"/>
<input type="hidden" id="direccion" name="direccion" value="<?php echo $direccion?>"/>
</form>
<form action="/utiles/word" method="post" target="_blank" id="Formularioword">
<input type="hidden" id="datos_a_enviar_word" name="datos_a_enviar_word"/>
<input type="hidden" id="nombre" name="nombre" value="<?php echo $nombre?><?php echo date("dmY");?>"/>
<input type="hidden" id="direccion" name="direccion" value="<?php echo $direccion?>"/>
</form>
<form action="/utiles/excel" method="post" target="_blank" id="Formularioexcel">
<input type="hidden" id="datos_a_enviar" name="datos_a_enviar"/>
<input type="hidden" id="nombre" name="nombre" value="<?php echo $nombre?><?php echo date("dmY");?>"/>
<input type="hidden" id="direccion" name="direccion" value="<?php echo $direccion?>"/>
</form>
<script type="text/javascript">
$(document).ready(function()
{
$(".botonpdf").click(function(event)
	{
		alert('Generar PDF');
		//alert($("<div>").append( $("div.page").eq(0).clone()).html());
		$("#datos_a_enviar_pdf").val( $("<div>").append( $("div.page").eq(0).clone()).html());
		$("#FormularioExportacionPdf").submit();
	});
	
$(".botonword").click(function(event)
	{
		alert("Generar WORD");
		//alert($("<div>").append( $(".page").eq(0).clone()).html());
		$("#datos_a_enviar_word").val( $("<div>").append( $("div.page").eq(0).clone()).html());
		$("#Formularioword").submit();
	});
$(".print").click(function() {
		$("div.page").printArea();
		return false;
	});
$(".botonExcel").click(function(event)
	{
		alert("Generar EXCEL");
		//alert($("<div>").append( $("#exportar_excel").eq(0).clone()).html());
		$("#datos_a_enviar").val($("<div>").append( $("#exportar_excel").eq(0).clone()).html());
		$("#Formularioexcel").submit();
	});
});
</script>
</div>
<?php }?>
<?php
}
/*order sin paginacion*/
public static function order_campo($a,$c,$mas='')
{
	$campo=base64_encode($c);
	$action=$a;
	$asc=base64_encode('ASC');
	$desc=base64_encode('DESC');
	$cadena='<span class="span_float">';
	$cadena.=Html::linkAction($action.'/'.$campo.'/'.$asc,'&uarr;');
	$cadena.=Html::linkAction($action.'/'.$campo.'/'.$desc,'&darr;');
	$cadena.='</span>';
	return $cadena;
}
	
}
?>