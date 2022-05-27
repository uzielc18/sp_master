<?php
class Calendar
{
	public static function selector($field, $attrs = NULL, $value = NULL)
	{
		$vy = '';
		$vm = '';
		$meses = array('01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
		$years = array();

		$y = date('Y') + 5;
		for ($i = $y; $i >= 1920; $i--) {
			$years[$i] = $i;
		}
		$field_id = str_replace('.', '_', $field);
		$code = '
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
		
		var f=$("#' . $field_id . '").val();
		if(f!="")
			{
				var F=f.split("-");
			  	var anio=F[0]+"";
			  	var mes=F[1]+"";
			  	var dia=F[2]+"";
				selectDias(diasDeMes(mes,anio),dia);
			}else
			{
				
				var anio=' . date('Y') . ';
				var mes=' . date('m') . ';
				var dia=' . date('d') . ';
				selectDias(diasDeMes(mes,anio),dia);
				$("#' . $field_id . '").val(anio+"-"+mes+"-"+dia);
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
				$(\'#days' . $field_id . '\').find("option").remove();
				var i=1;
				var i_d;
				for (i=1;i<=d;i++)
				{
					i_d=i;
					if(i<10){i_d="0"+i;}
					if(i==dia)
					{
						$(\'#days' . $field_id . '\').append($(\'<option selected="selected"></option>\').attr(\'value\',i_d ).text(i));
					}else
					{
						$(\'#days' . $field_id . '\').append($(\'<option></option>\').attr(\'value\',i_d ).text(i));
					}
				}
			}
					';
		foreach ($meses as $clave => $v) {
			$code .= 'MESES["' . $clave . '"] = "' . $v . '";';
		}
		for ($i = $y; $i >= 1920; $i--) {
			$code .= 'ANIOS[' . $i . '] = "' . $i . '";';
			"$('#year" . $field_id . "').append($('<option></option>').attr('value', '" . $i . "' ).text('" . $i . "'));";
		}
		$code .= '
					for(var i in MESES)
					{
						if(i==mes)
						{
							$(\'#mont' . $field_id . '\').append($(\'<option selected="selected"></option>\').attr(\'value\',i ).text(MESES[i]));
						}else
						{
							$(\'#mont' . $field_id . '\').append($(\'<option></option>\').attr(\'value\',i ).text(MESES[i]));
						}
					}
					for(var i in ANIOS)
					{
						if(i==anio)
						{
							$(\'#year' . $field_id . '\').append($(\'<option selected="selected"></option>\').attr(\'value\',i ).text(ANIOS[i]));
						}else
						{
							$(\'#year' . $field_id . '\').append($(\'<option></option>\').attr(\'value\',i ).text(ANIOS[i]));
						}
					}
					
					$("#mont' . $field_id . '").change(function(){
						var d = $("#days' . $field_id . '").val();
						var m=$(this).val();
						var a=$("#year' . $field_id . '").val();
						
						var fecha=a+"-"+m+"-"+d;
						$("#' . $field_id . '").val(fecha);
						selectDias(diasDeMes(m,a),d);
						});
					$("#year' . $field_id . '").change(function(){
						var d = $("#days' . $field_id . '").val();
						var a = $(this).val();
						var m = $("#mont' . $field_id . '").val();
						
						var fecha=a+"-"+m+"-"+d;
						$("#' . $field_id . '").val(fecha);
						});
					$("#days' . $field_id . '").change(function(){
						var d = $(this).val();
						var m = $("#mont' . $field_id . '").val();
						var a=$("#year' . $field_id . '").val();
						
						var fecha=a+"-"+m+"-"+d;
						$("#' . $field_id . '").val(fecha);
						});				
		
				});
				
		  </script>
		<div class="row">			
			<div class="col-sm-4">
				<select id="days' . $field_id . '" name="days' . $field_id . '"' . $attrs . '></select>
			</div>
			<div class="col-sm-4">
				<select id="mont' . $field_id . '" name="mont' . $field_id . '"' . $attrs . '></select>
			</div>
			<div class="col-sm-4">
				<select id="year' . $field_id . '" name="year' . $field_id . '"' . $attrs . '></select>
			</div>
		  </div>  
		  ';
		$code .= Form::hidden($field, $attrs, $value);
		return $code;
	}
	public static function text($field, $attrs = NULL, $value = NULL)
	{
		$code   =   '';
		$code   .=   Form::text($field, $attrs, $value);
		$field  =   str_replace('.', '_', $field);
		$code   .=  "<script type=\"text/javascript\"> 
                      $(function() { 
                          $(\"#" . $field . "\").datepicker();({ 
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
	public static function getMeses($anio,$meses,$mes_activo){
		$code='<div class="row">
			<ul class="pagination pagination-sm">
				<li class="page-item"><a href="#" class="page-link">Año
						<select id="anio">';
							
							for ($i = 2011; $i <= Session::get('ANIO'); $i++) {
								$selec = '';
								if ($anio == $i) {
									$selec = ' selected="selected"';
								}
								$code.='<option value="' . $i . '" ' . $selec . '>' . $i . '</option>';
							}
		$code.='
						</select>
					</a>
				</li>';
			$mes_actual = date('Y-m');
			foreach ($meses as $clave => $mes) :
				if ($anio . '-' . $clave <= $mes_actual) {
					$class = '';
					if ($mes_activo == $clave) $class = ' active';
					$code.='<li class="page-item ' . $class . '"><a class="page-link enviar' . $class . '" id="mes-' . $mes . '" href="javascript:;" data-id="' . $anio . '-' . $clave . '-01">' . $mes . '</a></li>';
					
				}
			endforeach;
			$code.='</ul>
			
		</div>';

		return $code;
	}
}
