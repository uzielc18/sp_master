<?php echo Tag::js('jquery/jquery.tokeninput');
Tag::css('token-input');?><div class="card">
    <h1><em>Crear Salida<span><?php echo Session::get('DOC_NOMBRE')?></span></em></h1>
</div>
<div id="mensajes_flash" >        
    <?php View::content() ?>
</div>
<div class="card-body">
<div id="ver_resultado"></div>
<?php echo Form::open(NULL,'POST','class="form-horizontal"')?>
  <div class="control-group">
    <label class="control-label" for="salida_serie">Serie - Numero</label>
    <div class="controls">
      <?php echo Form::text('salida.serie','size="4" readonly="readonly"');?>-<?php echo Form::text('salida.numero','size="10"  readonly="readonly"');?>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="salida_tesmonedas_id">Moneda:</label>
    <div class="controls">
      <?php echo Form::dbSelect('salida.tesmonedas_id','simbolo,nombre',array("tesmonedas",'find','conditions: aclempresas_id='.Session::get('EMPRESAS_ID')));?>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="salida_hilodestino">Destino:</label>
    <div class="controls">
      <?php echo Form::dbSelect('salida.hilodestino','nombre,abr',array("hilodestino",'find'));?>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="CL">Proveedor</label>
    <div class="controls">
    	<input id="CL" />
      <?php echo Form::hidden('salida.tesdatos_id')?>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="salida_direccion_entrega">Direccion de Entrega</label>
    <div class="controls">
      <?php echo Form::text('salida.direccion_entrega','class="span10"');//finiciotraslado?>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="salida_direccion_entrega">Fecha de Traslado</label>
    <div class="controls">
      <?php echo Calendar::selector('salida.finiciotraslado');//finiciotraslado?>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputEmail">Conductor:</label>
    <div class="controls">
      <?php echo Form::text('prodetalletransportes.protransportistas_id')?>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputEmail">Transporte</label>
    <div class="controls">
      <?php echo Form::text('prodetalletransportes.protransportes_id')?>
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
       <button type="submit" class="btn btn-xs">Generar</button>
    </div>
  </div>
<?php echo Form::close();?>
</div>
<script type="text/javascript">
$(document).ready(function()
{
	$("#CL").tokenInput("/<?php echo $module_name?>/salidas/buscarproveedor", 
		{
			tokenLimit: 1,
			minChars: 2,
	
			onAdd: function (item){
			   var VAL=item.id;
			   $("#salida_tesdatos_id").val(VAL);
			   var DIR=item.name.split('(');
			   $("#salida_direccion_entrega").val(DIR[1]);
			   //$("#BC").hide();
			   //GrabarCliente();
			},
			onDelete: function (item) {
			}
		});
		
		/*transporte*/
		$("#prodetalletransportes_protransportes_id").tokenInput("/<?php echo $module_name?>/salidas/transportes", 
		{
			tokenLimit: 1,
			minChars: 2,
		});
		
		/*transportistas*/
		
		$("#prodetalletransportes_protransportistas_id").tokenInput("/<?php echo $module_name?>/salidas/transportistas", 
		{
			tokenLimit: 1,
			minChars: 2,
	
			onAdd: function (item){
			   var VAL=item.id;
			   $("#salida_tesdatos_id").val(VAL);
			   var DIR=item.name.split('(');
			   $("#salida_direccion_entrega").val(DIR[1]);
			   //$("#BC").hide();
			   //GrabarCliente();
			},
			onDelete: function (item) {
			}
		});
});
</script>
Transportista:	
Nombre:	
Domicilio:	
Ruc:	
Placa:	
