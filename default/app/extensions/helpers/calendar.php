<?php 
class Calendar{
	/*
	pensado en el selctor de fechas
	
	@modelo = recibe el nombre del modelo el cual se antepodra al nombre de cada slector ejemplo modelo.'anio' etc.
	@fecha = fecha de la base de dato el campo de la fecha YYYY-mm-dd
	///////////////////////////// 
	para mas adleante se porevee que tendra que validar los meses que selecciona es decir los dias por meses 
	*/
	
	public static function selector($field, $attrs = NULL, $value = NULL)
	  {
		  $vy='';
		  $vm='';
		  $meses=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
		  $years=array();
		  
		  $y=date('Y')+5;
		  for($i=$y;$i>=1920;$i--)
		  {
			  $years[$i]=$i;
		  }
		  $field_id=str_replace('.', '_', $field);
$code='
<script type="text/javascript">
	$(document).ready(function()
	{				  
					dias=new Array(12);
					  dias[1]=31;
					  dias[2]=28;
					  dias[3]=31;
					  dias[4]=30;
					  dias[5]=31;
					  dias[6]=30;
					  dias[7]=31;
					  dias[8]=31;
					  dias[9]=30;
					  dias[10]=31;
					  dias[11]=30;
					  dias[12]=31;
		
		var f=$("#'.$field_id.'").val();
		if(f!="")
			{
				var F=f.split("-");
			  	var anio=F[0]+"";
			  	var mes=F[1]+"";
			  	var dia=F[2]+"";
				selectDias(diasDeMes(mes,anio),dia);
			}else
			{
				
				var anio='.date('Y').';
				var mes='.date('m').';
				var dia='.date('d').';
				selectDias(diasDeMes(mes,anio),dia);
				$("#'.$field_id.'").val(anio+"-"+mes+"-"+dia);
			}
			
			function diasDeMes(mes,anio)
			{ 
			  //alert(mes);			  
			  if(mes=="08"){var mes=8;}
			  if(mes=="09"){var mes=9;}
			  var mes = parseInt(mes);
			  var ultimo=0; 
			  if (mes==2){ 
			  var fecha=new Date(anio,2,29) 
			  var vermes=fecha.getMonth(); 
			  if(vermes==mes){ultimo=29} 
			  }
			  //(mes);			  
			  if(ultimo==0){ultimo=dias[mes]} 
			  //alert(ultimo);			  
			  return ultimo;
		    } 
					
			var MESES= new Array();
			var ANIOS= new Array();
			
			function selectDias(d,dia)
			{
				$(\'#days'.$field_id.'\').find("option").remove();
				var i=1;
				var i_d;
				for (i=1;i<=d;i++)
				{
					i_d=i;
					if(i<10){i_d="0"+i;}
					if(i==dia)
					{
						$(\'#days'.$field_id.'\').append($(\'<option selected="selected"></option>\').attr(\'value\',i_d ).text(i));
					}else
					{
						$(\'#days'.$field_id.'\').append($(\'<option></option>\').attr(\'value\',i_d ).text(i));
					}
				}
			}
					';
				foreach($meses as $clave=>$v){
					$code.='MESES["'.$clave.'"] = "'.$v.'";';
					}
				 for($i=$y;$i>=1920;$i--)
		  			{
			 		$code.='ANIOS['.$i.'] = "'.$i.'";';
					"$('#year".$field_id."').append($('<option></option>').attr('value', '".$i."' ).text('".$i."'));";
		 			 }				
					$code.='
					for(var i in MESES)
					{
						if(i==mes)
						{
							$(\'#mont'.$field_id.'\').append($(\'<option selected="selected"></option>\').attr(\'value\',i ).text(MESES[i]));
						}else
						{
							$(\'#mont'.$field_id.'\').append($(\'<option></option>\').attr(\'value\',i ).text(MESES[i]));
						}
					}
					for(var i in ANIOS)
					{
						if(i==anio)
						{
							$(\'#year'.$field_id.'\').append($(\'<option selected="selected"></option>\').attr(\'value\',i ).text(ANIOS[i]));
						}else
						{
							$(\'#year'.$field_id.'\').append($(\'<option></option>\').attr(\'value\',i ).text(ANIOS[i]));
						}
					}
					
					$("#mont'.$field_id.'").change(function(){
						var d = $("#days'.$field_id.'").val();
						var m=$(this).val();
						var a=$("#year'.$field_id.'").val();
						
						var fecha=a+"-"+m+"-"+d;
						$("#'.$field_id.'").val(fecha);
						selectDias(diasDeMes(m,a),d);
						});
					$("#year'.$field_id.'").change(function(){
						var d = $("#days'.$field_id.'").val();
						var a = $(this).val();
						var m = $("#mont'.$field_id.'").val();
						
						var fecha=a+"-"+m+"-"+d;
						$("#'.$field_id.'").val(fecha);
						});
					$("#days'.$field_id.'").change(function(){
						var d = $(this).val();
						var m = $("#mont'.$field_id.'").val();
						var a=$("#year'.$field_id.'").val();
						
						var fecha=a+"-"+m+"-"+d;
						$("#'.$field_id.'").val(fecha);
						});				
		
				});
				
		  </script>
		  <select id="days'.$field_id.'" name="days'.$field_id.'"'.$attrs.'></select>/
		  <select id="mont'.$field_id.'" name="mont'.$field_id.'"'.$attrs.'></select>/
		  <select id="year'.$field_id.'" name="year'.$field_id.'"'.$attrs.'></select>	  
		  ';
		  $code.=Form::hidden($field,$attrs,$value);
		  return $code;
	  }
	public static function text($field, $attrs = NULL, $value = NULL){
          static $i = false; 
          $code   =   ''; 
          if($i == false){
                  $i = true; 
                  $code   =    Tag::css('themes/base/jquery.ui.all');
                  $code   .=   Tag::js('jquery/ui/jquery.ui.core');
                  $code   .=   Tag::js('jquery/ui/jquery.ui.datepicker');
          }
          $code   .=   Form::text($field, $attrs, $value); 
          $field  =   str_replace('.', '_', $field); 
          $code   .=  "<script type=\"text/javascript\"> 
                      $(function() { 
                          $(\"#" . $field . "\").datepicker({ 
                          altFormat: 'dd/mm/yy', 
                          autoSize: true, 
                          dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'], 
                          dayNamesMin: ['Dom', 'Lu', 'Ma', 'Mi', 'Je', 'Vi', 'Sa'], 
                          firstDay: 1, 
                          monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'], 
                          monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'], 
                          dateFormat: 'yy-mm-dd', 
                          changeMonth: true, 
                          changeYear: true}); 
                      }); 
                      </script>"; 
          return $code; 
      } 
}
?>