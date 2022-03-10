<?php 
View::template('spatricia/default');
Load::models('aclempresas','tesdatos','tessalidasinternas','tesletrassalidasinternas','tessalidas','tesletrassalidas','tesdocumentos','tesingresos','tesdetalleingresos','prorollos','tesdetracciones','proeficiencias','tesabonos');

class GraficosController extends AdminController
{
	
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
public function index(){}


/*GRAFICOS*/

public function graficos_venta($Y='',$m='')
{
	
$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first(Session::get("EMPRESAS_ID"));
		$salidas= new Tessalidas();
		$this->valores=$salidas->getTotalesall();
		$salidasinternas= new Tessalidasinternas();
		$this->guias=$salidasinternas->getVentas();
		$abonos= new Tesabonos();
		$this->abonos=$abonos->getAbonos();
		$this->abonos_i=$abonos->getAbonos_i();
}

public function graficos_eficiencia($Y='',$m='')
{
	$anio=$this->anio=Session::get('ANIO');
	if($Y!='')$anio=$this->anio=$Y;
	$mes_activo=$this->mes_activo=date('m');
	if($m!='')$mes_activo=$this->mes_activo=$m;
	$proeficiencias=new Proeficiencias();
	$n=0;
	foreach($acldatos=$proeficiencias->find('columns: id,acldatos_id','group: acldatos_id') as $t):
	$this->vals=count($acldatos);
	$n++;
	$a=$t->acldatos_id;
	$this->{'id'.$n}=$a;
	$this->{'nombre'.$n}=$t->getAcldatos()->nombre.' '.$t->getAcldatos()->appaterno;
	$this->{'maquinas'.$n}=$proeficiencias->getEfi($t->acldatos_id,'p.id,p.promaquinas_id,p.fecha,p.valor,p.acldatos_id',' GROUP BY p.promaquinas_id',' ORDER BY p.promaquinas_id ASC');
	$this->{'fechas'.$n}=$proeficiencias->getEfi($t->acldatos_id,'p.id,p.promaquinas_id,p.fecha,p.valor,p.acldatos_id',' GROUP BY s.id',' ORDER BY p.fecha, p.proturnos_id, p.promaquinas_id ASC',$anio,$mes_activo);
	$this->valores=$proeficiencias->find('columns: id,promaquinas_id,fecha,valor');
	endforeach;
}

}

?>