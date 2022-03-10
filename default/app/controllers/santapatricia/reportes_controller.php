<?php 
View::template('spatricia/default');
Load::models('aclempresas','tesdatos','tessalidasinternas','tesletrassalidasinternas','tessalidas','tesletrassalidas','tesletrasingresos','tesdocumentos','tesingresos','tesdetalleingresos','prorollos','tesdetracciones','proeficiencias','tesvendedores','tesdetallesalidas','tesdetallesalidasinternas','tesproductos','testipoproductos','tesabonos','tesreportes','tesabonos','teschequessalidas','tesfacturasadelantos','tesvauchers','teschequesingresos');

class ReportesController extends AdminController
{
	
	protected function before_filter(){
        if (Input::isAjax()){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
public function index(){}

public function reportesemanal($id=0,$fa=''){
	$f=date("Y-m-d");
	$this->id=$id;
	$fecha=$this->fecha=$f;
	if($fa!=''){
		$this->fecha=$fecha=$fa;
		}else{
		$fecha=$this->fecha=$f;
		}
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$salidas= new Tessalidas();
	$this->ventas=$salidas->getVentasALLDatos($id,$fecha);
	$this->dato=FALSE;
	$vendedores=new Tesvendedores();
	$this->vendedores=$vendedores->getAV();
	if($id!=0){
	$tesdatos= new Tesdatos();
	$this->dato=$tesdatos->find_first($id);
	}	
}
public function reportesemanal_uno($id,$fa=''){
	$f=date("Y-m-d");
	$this->id=$id;
	$fecha=$this->fecha=$f;
	if($fa!=''){
		$this->fecha=$fecha=$fa;
		}else{
		$fecha=$this->fecha=$f;
		}
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$salidas= new Tessalidas();
	$this->ventas=$salidas->getVentasALLDatos($id,$fecha);
	$vendedores=new Tesvendedores();
	$this->vendedores=$vendedores->getAV();
	$this->dato=FALSE;
	if($id!=0){
	$tesdatos= new Tesdatos();
	$this->dato=$tesdatos->find_first($id);
	}	
}

public function reportediario(){}

public function ver_reportediario($fecha)
{
/*getVentas,getSaldos,getVenta,getAbonos,getAdelanto_f*/
	$reportes= new Tesreportes();
	$this->mostrar=0;
	if($reportes->exists('fecha="'.$fecha.'"')){
	$this->mostrar=1;
	View::select('ver_reportediario_guardado');
	$this->reporte=$reportes->find_first('fecha="'.$fecha.'"');
	}
	$fecha_2=date("Y-m-d", strtotime("$fecha -1 day"));
	$this->SALDOS=FALSE;
	$this->fecha_2=$fecha_2;
	if($reportes->exists('fecha="'.$fecha_2.'"')){
	$this->SALDOS=$reportes->find_first('fecha="'.$fecha_2.'"');
	}
	$this->fecha_D=$fecha;
	$salidas= new Tessalidas();
	$this->SAL=$salidas;
	$vendedores=new Tesvendedores();
	$this->vendedores=$vendedores->find('conditions: activo!=0 AND aclempresas_id='.Session::get('EMPRESAS_ID'));
	
	$this->saldos=$salidas->getSaldos($fecha);
	$this->ventas=$salidas->getVentas($fecha);
	$this->venta=$salidas->getVenta($fecha);
	$this->abonos=$salidas->getAbonos($fecha);
	$this->adelantos=$salidas->getAdelanto_f($fecha);
	$this->cobranzas=$salidas->getAbonosporTipo($fecha);
	$this->abonos_dia=$salidas->getAbonosDia($fecha);
	$this->abonos_mes=$salidas->getAbonosMes($fecha);
	$this->letras_canceladas=$salidas->getLetrasAcumuladas($fecha);
	$this->aceptadas_dia=$salidas->getAbonosLetraDia($fecha);
	$this->aceptadas_mes=$salidas->getAbonosLetraMes($fecha);
	$this->detraccion_mes=$salidas->getAbonosDetraccionMes($fecha);
	$this->retencion_mes=$salidas->getAbonosRetencionMes($fecha);
	
	$anio=$this->anio=Session::get('ANIO');	
	$mes_activo=$this->mes_activo=date('m');
	if(($mes_activo-1)<10)$mes_activo_a='0'.($mes_activo-1);else $mes_activo_a=($mes_activo-1);
	
	$this->fecha=$anio.'-'.($mes_activo);
	$this->fecha_a=$anio.'-'.($mes_activo_a);
$salidas= new Tessalidas();	
$this->salidas=$salidas->find("conditions: aclempresas_id=".Session::get("EMPRESAS_ID")." AND (tesdocumentos_id=7 OR tesdocumentos_id=13 OR tesdocumentos_id=14) AND estadosalida!='ANULADO' AND DATE_FORMAT(femision,'%Y-%m')='".$anio.'-'.($mes_activo)."'",'order: femision,numero ASC');	
$this->salidas_anteriores=$salidas->find("conditions: aclempresas_id=".Session::get("EMPRESAS_ID")." AND (tesdocumentos_id=7 OR tesdocumentos_id=13 OR tesdocumentos_id=14) AND estadosalida!='ANULADO' AND DATE_FORMAT(femision,'%Y-%m')='".$anio.'-'.$mes_activo_a."'",'order: femision,numero ASC');
$ingresos= new Tesingresos();
$this->ingresos=$ingresos->find("conditions: aclempresas_id=".Session::get("EMPRESAS_ID")." AND (tesdocumentos_id=7 OR tesdocumentos_id=13 OR tesdocumentos_id=14) AND DATE_FORMAT(femision,'%Y-%m')='".$anio.'-'.$mes_activo."'","order: femision ASC");
$this->ingresos_anteriores=$ingresos->find("conditions: aclempresas_id=".Session::get("EMPRESAS_ID")." AND (tesdocumentos_id=7 OR tesdocumentos_id=13 OR tesdocumentos_id=14) AND DATE_FORMAT(femision,'%Y-%m')='".$anio.'-'.$mes_activo_a."'","order: femision ASC"); 
/*retencion de la fecha*/
$abonos=new Tesabonos();
$this->retencion=$abonos->sum("total","conditions: tesformadepagosabonos_id=14 AND aclempresas_id=".Session::get("EMPRESAS_ID")." AND date_format(fecha,'%Y-%m')='".$anio.'-'.$mes_activo."'");
$this->retencion_a=$abonos->sum("total","conditions: tesformadepagosabonos_id=14 AND aclempresas_id=".Session::get("EMPRESAS_ID")." AND date_format(fecha,'%Y-%m')='".$anio.'-'.$mes_activo_a."'");
}
public function salvar_reportes($c=0)
{
	$this->data='';
	View::select('tesdatos');
	$reportes= new Tesreportes();
	if($_POST['id']=='' || $_POST['id']==0)
	{
		$reportes= new Tesreportes();
		$reportes->aclusuarios_id=Auth::get('id');
		$reportes->id=$_POST['id'];
	}else
	{
		$reportes=$ING->find_first((int)$_POST['id']);
	}
	if(isset($_POST['nombre']))$reportes->nombre=$_POST['nombre'];
	if(isset($_POST['cuerpo']))$reportes->cuerpo=$_POST['cuerpo'];
	if(isset($_POST['cuerpo']))$reportes->fecha=$_POST['fecha'];
	if(isset($_POST['monto_uno']))$reportes->monto_uno=$_POST['monto_uno'];
	if(isset($_POST['monto_dos']))$reportes->monto_dos=$_POST['monto_dos'];
	if(isset($_POST['monto_tres']))$reportes->monto_tres=$_POST['monto_tres'];
	if(isset($_POST['monto_cuatro']))$reportes->monto_cuatro=$_POST['monto_cuatro'];
	if(isset($_POST['igv_mes_ant']))$reportes->igv_mes_ant=$_POST['igv_mes_ant'];
	if($reportes->save())
	{
		$this->data='OK';
	}
	
}
public function ver_reportediario_guardado()
{
	
}
public function ventasmes($Y='',$m=''){
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas= new Tessalidas();
		$this->salidas=$salidas->find("conditions: aclempresas_id=".Session::get("EMPRESAS_ID")." AND (tesdocumentos_id=7 OR tesdocumentos_id=13 OR tesdocumentos_id=14) AND estadosalida!='ANULADO' AND DATE_FORMAT(femision,'%Y-%m')='".$anio.'-'.$mes_activo."'",'order: femision,numero ASC');
	}
	
public function ventas_anual($Y='',$c='total',$d='DESC'){
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas= new Tessalidas();
		$this->ventas=$salidas->getVentas_anual($anio,$c,$d);
	}
public function ventas_anual_detalle($anio,$id){
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas= new Tessalidas();
		$this->ventas=$salidas->find("conditions: tesdatos_id=$id AND DATE_FORMAT(femision,'%Y')=$anio AND aclempresas_id=".Session::get("EMPRESAS_ID"));
		$internas= new Tessalidasinternas();
		$this->internas=$internas->find("conditions: tesdatos_id=$id AND DATE_FORMAT(femision,'%Y')=$anio AND aclempresas_id=".Session::get("EMPRESAS_ID"));
	}

public function comprasmes($Y='',$m='')
{
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$ingresos= new Tesingresos();
		$this->ingresos=$ingresos->find("conditions: aclempresas_id=".Session::get("EMPRESAS_ID")." AND (tesdocumentos_id=7 OR tesdocumentos_id=13 OR tesdocumentos_id=14) AND DATE_FORMAT(femision,'%Y-%m')='".$anio.'-'.$mes_activo."' order by femision ASC");
}

public function impuestosmes($Y='',$m='')
{
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$salidas=$this->R= new Tessalidas();
	$this->salidas=$salidas->getTotalesall(' AND DATE_FORMAT(s.femision,"%Y")='.$anio);
	$ingresos= new Tesingresos();
	$this->ingresos=$ingresos->getTotalesall(' AND DATE_FORMAT(s.femision,"%Y")='.$anio);
}
public function impuestosmes_resumen($Y='',$m='')
{
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
	if(($mes_activo-1)<10)$mes_activo_a='0'.($mes_activo-1);else $mes_activo_a=($mes_activo-1);
	
	$this->fecha=$anio.'-'.($mes_activo);
	$this->fecha_a=$anio.'-'.($mes_activo_a);
	
$salidas= new Tessalidas();	
$this->salidas=$salidas->find("conditions: aclempresas_id=".Session::get("EMPRESAS_ID")." AND (tesdocumentos_id=7 OR tesdocumentos_id=13 OR tesdocumentos_id=14) AND estadosalida!='ANULADO' AND DATE_FORMAT(femision,'%Y-%m')='".$anio.'-'.($mes_activo)."'",'order: femision,numero ASC');	
$this->salidas_anteriores=$salidas->find("conditions: aclempresas_id=".Session::get("EMPRESAS_ID")." AND (tesdocumentos_id=7 OR tesdocumentos_id=13 OR tesdocumentos_id=14) AND estadosalida!='ANULADO' AND DATE_FORMAT(femision,'%Y-%m')='".$anio.'-'.$mes_activo_a."'",'order: femision,numero ASC');
$ingresos= new Tesingresos();
$this->ingresos=$ingresos->find("conditions: aclempresas_id=".Session::get("EMPRESAS_ID")." AND (tesdocumentos_id=7 OR tesdocumentos_id=13 OR tesdocumentos_id=14) AND DATE_FORMAT(femision,'%Y-%m')='".$anio.'-'.$mes_activo."'","order: femision ASC");
$this->ingresos_anteriores=$ingresos->find("conditions: aclempresas_id=".Session::get("EMPRESAS_ID")." AND (tesdocumentos_id=7 OR tesdocumentos_id=13 OR tesdocumentos_id=14) AND DATE_FORMAT(femision,'%Y-%m')='".$anio.'-'.$mes_activo_a."'","order: femision ASC"); 
/*retencion de la fecha*/
$abonos=new Tesabonos();
$this->retencion=$abonos->sum("total","conditions: tesformadepagosabonos_id=14 AND aclempresas_id=".Session::get("EMPRESAS_ID")." AND date_format(fecha,'%Y-%m')='".$anio.'-'.$mes_activo."'");
$this->retencion_a=$abonos->sum("total","conditions: tesformadepagosabonos_id=14 AND aclempresas_id=".Session::get("EMPRESAS_ID")." AND date_format(fecha,'%Y-%m')='".$anio.'-'.$mes_activo_a."'");
}

public function hilourdido()
{
		$ING=new Tesingresos();
		$this->INGR=$ING;
		$this->nombre='Hilo en Urdido';
		$this->DET=$DETALLES= new Tesdetalleingresos();
		$DETSALIDAS= new Tesdetallesalidas();
		$DETSALIDASINTERNAS= new Tesdetallesalidasinternas();
		$empresas= new Aclempresas();
		Session::get("INVENTARIO_ID");
		Session::set("GROUP",'lote');
		
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$this->inventario=$ING->find((int)Session::get("INVENTARIO_ID"));
		$this->detalles=$DETALLES->getDetallesProductos_ubicacion(3,Session::get('GROUP'),0,2);
		$this->salidas=$DETSALIDAS->getUrdidos(3);
		$this->internas=$DETSALIDASINTERNAS->getUrdidos(3);
		$this->titulo = 'Ver Inventario Por Producto';
}

public function listado_rollos($e='',$ancho='')
{
	/*se modifico para que los listado ya no depeddan de los proceos  los procesos solo se usaran para saber cuanto rollos estan tienen procesos y cuantos son cortaod en almacen */

	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$rollos= new Prorollos();
	$this->rollos=array();
	$this->ancho=$ancho;
	/*selector de metros*/
	//$this->metros=$rollos->getMetros();
	if($e!=''){
	$this->rollos=$rollos->getRollosporestado($e,$ancho);
	}
	$this->e=$e;
}
public function listado_rollos_venta($id)
{
	$prorollos=new Prorollos();
	$productos = new Tesproductos();
	$this->producto=$productos->find_first($id);
	$this->rollos_venta=$prorollos->getVentas_rollos($id);
	$this->rollos_sinprocesos=$prorollos->getSinProcesos($id);
	$this->rollos_tintoreria=$prorollos->getRollos_tintoreria($id);
}
/*
recibe el id del cliente;
*/
public function venta_por_cliente($id='')
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$salidas= new Tessalidas();
	$this->ventas=$salidas->getVentasDatos($id);
	$this->dato=FALSE;
	if($id!=0){
	$tesdatos= new Tesdatos();
	$this->dato=$tesdatos->find_first($id);
	}
}
public function venta_por_cliente_uno($id='')
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$salidas= new Tessalidas();
	$this->ventas=$salidas->getVentasDatos($id);
	$this->dato=FALSE;
	if($id!=0){
	$tesdatos= new Tesdatos();
	$this->dato=$tesdatos->find_first($id);
	}
}
public function compra_por_proveedor($id=0)
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$ingresos= new Tesingresos();
	$this->compras=$ingresos->getComprasDatos($id);
	$this->dato=FALSE;
	if($id!=0){
	$tesdatos= new Tesdatos();
	$this->dato=$tesdatos->find_first($id);
	}
}
public function compra_por_proveedor_uno($id=0)
{
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$ingresos= new Tesingresos();
	$this->compras=$ingresos->getComprasDatos($id);
	$this->dato=FALSE;
	if($id!=0){
	$tesdatos= new Tesdatos();
	$this->dato=$tesdatos->find_first($id);
	}
}

public function guias_tintoreria($id=0)
{
	$this->COLOR=Load::model('tescolores');
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$tessalidas= new Tessalidas();
	$this->rollos_tintoreria=$tessalidas->getGuiasT($id);
	
	$this->rollos_reprocesos=$tessalidas->getGuiasR($id);
	$this->dato=FALSE;
	if($id!=0){
	$tesdatos= new Tesdatos();
	$this->dato=$tesdatos->find_first($id);
	}
}

public function facturasadelanto($id=0,$fecha=''){
	$this->fecha=$fecha=date("Y-m-d");
	if($fecha!='')$fecha=$this->fecha=$fecha;
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$salidas= new Tessalidas();
	$this->ventas=$salidas->getAdelantosAp();
	$this->dato=FALSE;
	if($id!=0){
	$tesdatos= new Tesdatos();
	$this->dato=$tesdatos->find_first($id);
	}	
}
public function facturasadelanto_uno($id=0,$fecha='')
{
	$this->fecha=$fecha=date("Y-m-d");
	if($fecha!='')$fecha=$this->fecha=$fecha;
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$salidas= new Tessalidas();
	$this->ventas=$salidas->getAdelantosAp($id);
	$this->dato=FALSE;
	if($id!=0){
	$tesdatos= new Tesdatos();
	$this->dato=$tesdatos->find_first($id);
	}	
}
public function adelantos($view="")
{
	if($view=='sinigv'){View::select('adelantos_sinigv');}
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$salidas= new Tessalidas();
	$this->ventas=$salidas->adelantos();
}
public function adelantos_sinigv(){}
public function movimientos_clientes()
{
$datos=new Tesdatos();
$this->datos=$datos->getDatosConPendientes();
}
public function ver_movimientos_cliente($id_c=0,$id_p=0,$fecha_inicio='',$fecha_fin='')
{
	$this->id=0;
	$this->id_p=$id_p;
	$this->id_c=$id_c;
	if($id_c==0){$id=$id_p;}else{$id=$id_c;}
	$this->id=$id;
	$datos=new Tesdatos();
	$Salidas=new Tessalidas();
	$this->dato=$datos->find_first((int)$id);
	$this->contacto=$datos->dato_id($id);
	$this->fi=$fecha_inicio;
	$this->ff=$fecha_fin;
	$this->detalles_c_s=FALSE;
	$this->detalles_c_d=FALSE;
	if($id_c!=0){
	$this->detalles=$datos->getMovimientos($id_c,$fecha_inicio,$fecha_fin,$id_p);
	$this->letras=$datos->getLetrasPendientes($id_c);
	$this->cargos_s=$datos->getCargos_s($id_c,$id_p)->cargos_s;
	$this->cargos_d=$datos->getCargos_d($id_c,$id_p)->cargos_d;
	$this->abonos_s=$datos->getAbonos_s($id_c,$id_p)->abonos_s;
	$this->abonos_d=$datos->getAbonos_d($id_c,$id_p)->abonos_d;
	
	$this->NC_anulados_s=$datos->getNC_Anulados_s($id_c,$id_p)->resta_abono_s;
	
	$this->NC_anulados_d=$datos->getNC_Anulados_d($id_c,$id_p)->resta_abono_d;
	
	$this->adelantos=$Salidas->getAdelantosClientes($id_c);
	//$this->detalles_c=$datos->getMovimientos($id_c,$fecha_inicio,$fecha_fin,$id_p);
	}
	$this->detalles_p=FALSE;
	if($id_p!=0){
	//$this->detalles_p=$datos->getMovimientos($id_p,$fecha_inicio,$fecha_fin);
	}
}
public function ver_movimientos_cliente_reporte($id_c=0,$id_p=0,$fecha_inicio='',$fecha_fin='',$ultimos=0)
{
	$this->id=0;
	$this->id_p=$id_p;
	$this->id_c=$id_c;
	if($id_c==0){$id=$id_p;}else{$id=$id_c;}
	$this->id=$id;
	$this->ultimos=$ultimos;
	$datos=new Tesdatos();
	$Salidas=new Tessalidas();
	$this->dato=$datos->find_first((int)$id);
	$this->contacto=$datos->dato_id($id);
	$this->fi=$fecha_inicio;
	$this->ff=date("Y-m-d");
	if($id_c!=0){	
	$this->detalles=$datos->getMovimientos($id_c,$fecha_inicio,$fecha_fin,$id_p,$ultimos);
	$this->letras=$datos->getLetrasPendientes($id_c);
	$this->cargos_s=$datos->getCargos_s($id_c,$id_p)->cargos_s;
	$this->cargos_d=$datos->getCargos_d($id_c,$id_p)->cargos_d;
	$this->abonos_s=$datos->getAbonos_s($id_c,$id_p)->abonos_s;
	$this->abonos_d=$datos->getAbonos_d($id_c,$id_p)->abonos_d;	
	
	$this->NC_anulados_s=$datos->getNC_Anulados_s($id_c,$id_p)->resta_abono_s;
	
	$this->NC_anulados_d=$datos->getNC_Anulados_d($id_c,$id_p)->resta_abono_d;
	
	$this->adelantos=$Salidas->getAdelantosClientes($id_c);
	//$this->detalles_c=$datos->getMovimientos($id_c,$fecha_inicio,$fecha_fin,$id_p);
	}
	$this->detalles_p=FALSE;
	if($id_p!=0){
	//$this->detalles_p=$datos->getMovimientos($id_p,$fecha_inicio,$fecha_fin);
	}
}



public function detracciones($Y='',$m='')
{
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	$empresas= new Aclempresas();
	$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
	$detracciones= new Tesdetracciones();
	$this->detracciones=$detracciones->getDetracciones($anio,$mes_activo);
}
/*Para el ingreso del codigo y la fecha cuando no tiene fecha o codigo de detraccion*/
public function codigodetraccion($id)
{
	if(Input::hasPost('detraccion'))
	{	
		$detraccion= new Tesdetracciones(Input::post('detraccion'));
		$detraccion->save();
		return Redirect::toAction('detracciones');
	}
	$detraccion= new Tesdetracciones();
	$this->detraccion=$detraccion->find_first((int)$id);
}
/*busqueda de datos para Clientes*/
public function tesdatos()
{
	/*
	1 Proveedor;
	2 Clientes;
	*/
	   $q=$_GET['q'];
		$obj = new Tesdatos();
		$results = $obj->getDatos_c($q);
		foreach ($results as $value)
		{
			$mas='(';
			if(!empty($value->id_proveedor)){$id=$value->id_proveedor;$mas.='Proveedor';}
			if(!empty($value->id_proveedor) && !empty($value->id_cliente))$mas.=' y ';
			if(!empty($value->id_cliente)){$id=$value->id_cliente;$mas.='Cliente';}
			$mas.=')';
			$name=$value->name;
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name.$mas;
			if(!empty($value->id_cliente))$id_c=$value->id_cliente; else $id_c=0;
			$json['id_cliente'] = $id_c;			
			if(!empty($value->id_proveedor))$id_p=$value->id_proveedor; else $id_p=0;
			$json['id_proveedor'] = $id_p;
			$this->data[] = $json;
		}
}
/*busqueda de datos para Proveedores*/
public function tesdatos_p()
{
	View::select('tesdatos');
	   $q=$_GET['q'];
		$obj = new Tesdatos();
		$results = $obj->getDatos_p($q);
		foreach ($results as $value)
		{
			$mas='(';
			if(!empty($value->id_proveedor)){$id=$value->id_proveedor;$mas.='Proveedor';}
			if(!empty($value->id_proveedor) && !empty($value->id_cliente))$mas.=' y ';
			if(!empty($value->id_cliente)){$id=$value->id_cliente;$mas.='Cliente';}
			$mas.=')';
			$name=$value->name;
			$json = array();
			$json['id'] =$id;
			$json['name'] = $name.$mas;
			if(!empty($value->id_cliente))$id_c=$value->id_cliente; else $id_c=0;
			$json['id_cliente'] = $id_c;			
			if(!empty($value->id_proveedor))$id_p=$value->id_proveedor; else $id_p=0;
			$json['id_proveedor'] = $id_p;
			$this->data[] = $json;
		}
}
public function valorado_muestras($Y='',$m='')
{
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	
	$muestras= new Tessalidasinternas();
	$this->muestras=$muestras->getMuestras($anio,$mes_activo);
	
}


/*devuelve los saldos de los hilos en el controlador reporte*/
public function hilos($id=6)
{
	View::select('hilos_ver');
	$this->id=$id;
	$productos=new Tesproductos(); 
	$testipos= new Testipoproductos();
	$this->tipos=$testipos->find('conditions: teslineaproductos_id=3');
	$salidas = new Tessalidas();
	$this->hilos=$salidas->getSaldoHilos($id);
	
	$this->PR=$productos;
}
public function hilos_ver()
{
}

/*reporte para ver la compra de hilos por año en meses*/
public function hiloanual($id='inicio',$personal_id=0,$Y='')
{
	$this->id=$id;
	$this->personal_id=$personal_id;
	if($personal_id!=0)
	{
		$personal= new Tesdatos();
		$this->proveedor=$personal->find_first((int)$personal_id);
	}
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$testipos= new Testipoproductos();
	$this->tipos=$testipos->find('conditions: teslineaproductos_id=3');
	$ingresos = new Tesingresos();
	if($id!='inicio'){
	$this->hilos=$ingresos->getHilosAnual($id,$personal_id,$anio);
	}else
	{
		$this->hilos='';
	}
	$this->IN=$ingresos;
	$this->DIN= new Tesproductos();
}

/*Reporte de codigo de barras*/

public function codigodebarras($estado='',$page=1)
{
	$this->e=$estado;
	$this->titulo='Rollos en '.$estado;
	$prorollos=new Prorollos();
	//$this->rollos=$prorollos->getRollosporestado("VENTA");
	//$this->rollos=$prorollos->getVentas();
	$this->rollos=$prorollos->paginate('conditions: estadorollo="'.$estado.'"',"page: $page", 'order: tesproductos_id DESC');
	
}
/*
#
# Hilo Senanal
#
*/
public function hilosemanal($semana=0,$Y='',$tipo=6)
{
	$anio=$this->anio=Session::get('ANIO');
	$this->tipo_id=$tipo;
	if($Y!='')$anio=$this->anio=$Y;
	$this->s=date('W');
	if($semana!=0)$this->s=$semana;
	$testipos= new Testipoproductos();
	$this->tipos=$testipos->find('conditions: teslineaproductos_id=3');
	$ingresos = new Tesingresos();
	$this->hilos=$ingresos->getDetalles_hilo_semanal($tipo);
	$this->DIN= new Tesdetalleingresos();
}

public function movimientos_proveedores()
{
	
}
public function ver_movimientos_proveedores($id_c=0,$id_p=0,$fecha_inicio='',$fecha_fin='')
{
	
}
public function reporte_letras_clientes($p_id=0,$c_id=0,$e_s='Registrado',$e_i='Por Pagar',$Y='')
{
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$this->p_id=$p_id;
	$this->c_id=$c_id;
	$this->e_s=$e_s;
	$this->e_i=$e_i;
	$letras_salidas= new Tesletrassalidas();
	$letras_ingresos= new Tesletrasingresos();
	$this->vacio=0;
	if($p_id!=0 or $c_id!=0){
	$this->e_letra_salidas=$letras_salidas->find('columns: estadoletras','conditions: 1=1 group by estadoletras');
	$this->e_letra_ingresos=$letras_ingresos->find('columns: estadodeletra','conditions: 1=1 group by estadodeletra');
	$datos= new Tesdatos();
	$this->dato=$datos->find_first($c_id);
	$this->vacio=1;
	$this->l_salidas=$letras_salidas->get_letras($c_id,$anio,$e_s);/*Letras emitidas a los clientes*/
	$this->l_ingresos=$letras_ingresos->get_letras($p_id,$anio,$e_i);/*Letras aceptadas a proveedores*/
	}
}
public function reporte_letras_proveedores($p_id=0,$c_id=0,$e_s='Registrado',$e_i='Por Pagar',$Y='')
{
	
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$this->p_id=$p_id;
	$this->c_id=$c_id;
	$this->e_s=$e_s;
	$this->e_i=$e_i;
	
	$letras_salidas= new Tesletrassalidas();
	$letras_ingresos= new Tesingresos();
	$this->vacio=0;
	if($p_id!=0 or $c_id!=0){
		
	$this->e_letra_salidas=$letras_salidas->find('columns: estadoletras','conditions: 1=1 group by estadoletras');
	$this->e_letra_ingresos=$letras_ingresos->find('columns: estadoingreso','conditions: tesingresos.tesdocumentos_id!=15 AND tesingresos.tesdocumentos_id!=7 group by estadoingreso');
	
	$datos= new Tesdatos();
	$this->dato=$datos->find_first($p_id);
	$this->vacio=1;
	$this->l_salidas=$letras_salidas->get_letras($c_id,$anio,$e_s);/*Letras emitidas a los clientes*/
	$this->l_ingresos=$letras_ingresos->get_obligaciones($p_id,$anio,$e_i);/*Letras aceptadas a proveedores*/
	}
}
/* PAGADO SI HAY UN ERROR */
public function pagar($id=0,$origen)
{
	View::select('tesdatos');
	$this->data='OK';
	if($id!=0)
	{
		if($origen=='ruc'){
		$salidas = new Tessalidas();
		$salidas->find_first($id);
		$salidas->estadosalida='PAGADO';
		if($salidas->update())
		{
			return Redirect::toAction('reportesemanal');
		}
		}
		if($origen=='interna'){
		$salidas = new Tessalidasinternas();
		$salidas->find_first($id);
		$salidas->estadosalida='PAGADO';
		if($salidas->update())
		{
			return Redirect::toAction('reportesemanal');
		}
		}
	}
	
}


/*Seguimiento de un documento*/

public function seguir($model='NINGUNO',$id=0)
{
	
	
	if($model=="NINGUNO" && $id!=0)
	{
		if($model=='tessalidas')
		{
			$salidas=new Tessalidas();
			$this->salida=$salidas->find_first($id);
			$abonos= new Tesabonos();
			$this->abono=$abonos->find_first("conditions: tessalidas_id=".$id);
			$letras= new Tesletrassalidas();
			$this->letra=$letras->find_first("conditions: tessalidas_id=".$id);
			$cheques=new Teschequessalidas();
			$this->cheque=$cheques->find_first("conditions: tessalidas_id=".$id);
			$adelantos=new Tesfacturasadelantos();
			$this->adelanto=$adelantos->find_first("conditions: tessalidas_id=".$id);
			/*tesabonos, tesletrassalidas, tessalidas(guias o factura), teschequessalidas,tesfacturasadelantos*/
			
		}
		if($model=='tesingresos')
		{
			$ingresos= new Tesingresos();
			$this->salida=$ingresos->find_first($id);
			$vauchers= new Tesvauchers();
			$this->vaucher=$vauchers->find("conditions: tesingresos_id=".$id);
			$letras= new Tesletrasingresos();
			$this->letra=$letras->find_first("conditions: tesingresos_id=".$id);
			$cheques= new Teschequesingresos();
			$this->cheque=$cheques->find_first("conditions: tesingresos_id=".$id);
			$detraccion= new Tesdetracciones();
			$this->detraccion=$detraccion->find_first("conditions: tesingresos_id=".$id);
			/*tesingresos, tesvauchers, tesletrasingresos, teschequesingresos,tesdetracciones*/
		}
	}
}


}

?>