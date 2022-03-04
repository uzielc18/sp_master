<?php 
 class Search{ 
 /*  --------------------------------------------------------------
  ### En la vista donde quiees este helper debe llmar al jquey del token-input ###
  $field = nombre del campo
  $controller_action = (controllador/accion) en donde se mostrara un listado de las respuestas!!
 $attr = attributos en array
 $value = valor por defecto al cargar por primera vez.
 $model = modelo (nombre de la tabla que va a buscar)
 --------------------------------------------------------------
 */
  public static function text($field,$controller_action,$attrs = NULL,$value = NULL){ 
 static $i = false; 
  $code   =   ''; 
         if($i == false){ 
                $i = true; 
                $code   =    Tag::css('autocomplete/autocomplete');
				$code.=Tag::js('jquery/jquery.autocomplete');
          } 
		  
		 $field=   str_replace('.', '_', $field);
         $code.=Form::text($field, $attrs, $value);
         $code   .= "<script type='text/javascript'>
$(document).ready(function(){
$('#".$field."').autocomplete('".PUBLIC_PATH."$controller_action');
});
</script>";

return $code; 
     } 
 } 
?>
