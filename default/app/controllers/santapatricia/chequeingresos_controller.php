<?php 
/* CHEQUES id 35*/
View::template('spatricia/default');
Load::models('tesdocumentos','tesingresos','tesdetalleingresos','tesproductos','aclempresas','tesdatos','subcuentas','teschequesingresos','tessalidas');
class ChequeingresosController extends AppController
{
	protected function before_filter()
	{
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
		
	public function index($Y='',$m='')
	{
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		$ingresos= new Tesingresos();
		$this->cheques = $ingresos->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND estado=1 AND tesdocumentos_id=35 AND DATE_FORMAT(femision, "%Y-%m")="'.$anio.'-'.$mes_activo.'"','order: numero ASC');
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)35);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
	}
	
public function sinregistrar()
	{
		$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$cheques = new Teschequesingresos();
		$this->cheques=$cheques->find('conditions: ISNULL(estadocheque) AND aclempresas_id='.Session::get('EMPRESAS_ID'));
	}
	
/*registar el cheque*/
public function registrar($id)
	{
		if (Input::hasPost('cheques')) {
           
			$che= new Teschequesingresos(Input::post('cheques'));
			$che->estadocheque='Pendiente';
			if($che->save())
			{
	 /*Auditorias*/ 
	 Aclauditorias::add("El cheque fue Registrado Total={$che->monto},numero={$che->numercheque},Teschequesingresos->id={$che->id}",'Teschequesingresos'); 
	/*fin Aurditorias*/
				$ingresos= new Tesingresos(Input::post('ingresos'));
				$ingresos->numero=$che->numercheque;
				$ingresos->totalconigv=$che->monto;
				$ingresos->userid=Auth::get('id');
				$ingresos->save();
				Flash::valid('El cheque fue Registrado');
	 /*Auditorias*/ 
	 Aclauditorias::add("Se creo el ingreso tipo(Cheque), Total={$ingresos->totalconigv},numero={$ingresos->numercheque},Tesingresos->id={$ingresos->id}",'Tesingresos'); 
	/*fin Aurditorias*/
				return Redirect::toAction('');
			}
      }
		$cheques = new Teschequesingresos();
		$this->cheques=$cheques->find_first((int)$id);
		$ingresos= new Tesingresos();
		$this->ingresos = $ingresos->find_first((int)$this->cheques->tesingresos_id);
	}
/*Accion para Realizar el cobro del cheque graba la fecha del click autamaticamente */
public function cobrar($id)
	{
		$che= new Teschequesingresos();
		$che->find_first((int)$id);
		$che->fecha_cobro=date("Y-n-d H:i:s");
		$che->estadocheque='COBRADO';
		$che->userid=Auth::get('id');
	 /*Auditorias*/ 
	 Aclauditorias::add("El cheque fue Cobrado Total={$che->monto},numero={$che->numercheque},Teschequesingresos->id={$che->id}",'Teschequesingresos'); 
	/*fin Aurditorias*/
		if($che->save())
		{
				$ingresos= new Tesingresos();
				$ingresos->find_first((int)$che->tesingresos_id);
				$ingresos->estadoingreso='COBRADO';
				$ingresos->userid=Auth::get('id');
				$ingresos->save();
				Flash::valid('El cheque fue Cobrado');
		return Redirect::toAction('pendientes');
		}
		
	}
public function stornar($id)
{
	 $cheques = new Teschequesingresos();
	 $cheques->find_first((int)$id);
	 $ingresos= new Tesingresos();
	 $ingresos->find_first((int)$cheques->tesingresos_id);
	 $salidas = new Tessalidas();
	 $salidas->aclusuarios_id=Auth::get('id');
	 $salidas->tesmonedas_id=$ingresos->tesmonedas_id;
	 $salidas->tesdatos_id=$ingresos->tesdatos_id;
	 $salidas->tesdocumentos_id=41;
	 $salidas->testipocambios_id=$ingresos->testipocambios_id;
	 $salidas->codigo=$ingresos->codigo;
	 $salidas->numero=$ingresos->numero;
	 $salidas->femision=$ingresos->femision;
	 $salidas->fvencimiento=$ingresos->fvencimiento;
	 $salidas->fregistro=$ingresos->fregistro;
	 $salidas->totalconigv=$ingresos->totalconigv;
	 $salidas->movimiento=$ingresos->movimiento;
	 $salidas->estado='1';
	 $salidas->estadosalida='Pendiente';
	 $salidas->userid=Auth::get('id');
	 $salidas->aclempresas_id=$ingresos->aclempresas_id;	
	 $salidas->tescuentascorrientes_id=$ingresos->tescuentascorrientes_id;
	 if($salidas->save()){
		 /*Auditorias*/
		 Aclauditorias::add("Genrando una salida tipo cheque estornado ,Total={$salidas->totalconigv},numero={$salidas->numero},Tessalidas->id={$salidas->id}",'Tessalidas');
		 /*fin Aurditorias*/
		 
		$cheques->fecha_cobro=date("Y-n-d H:i:s");
		$cheques->estadocheque='EXTORNADO';
		$cheques->tessalidas_id=$salidas->id;
		$cheques->userid=Auth::get('id');
		/*Auditorias*/
		Aclauditorias::add("El cheque fue EXTORNADO Total={$cheques->monto},numero={$cheques->numercheque},Teschequesingresos->id={$cheques->id}",'Teschequesingresos');
		/*fin Aurditorias*/
	
		if($cheques->save())
		{
				$ingresos->estadoingreso='EXTORNADO';
				$ingresos->userid=Auth::get('id');
				$ingresos->save();
				Flash::valid('El cheque fue Estornado');
		return Redirect::toAction('pendientes');
		}
	 }
}
public function pendientes()
	{
	  	$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$cheques = new Teschequesingresos();
		$this->cheques=$cheques->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND estadocheque="Pendiente"');
	}
public function extornados()
	{
	  	$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$cheques = new Teschequesingresos();
		$this->cheques=$cheques->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND estadocheque="EXTORNADO"');
	}
	
}

?>