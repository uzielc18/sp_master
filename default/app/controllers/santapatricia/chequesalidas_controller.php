<?php 

class ChequesalidasController extends AdminController
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
		//Flash::valid('listado');
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		$salidas= new Tessalidas();
		$this->cheques = $salidas->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND estado=1 AND tesdocumentos_id=35 AND DATE_FORMAT(femision, "%Y-%m")="'.$anio.'-'.$mes_activo.'"','order: femision DESC');
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)35);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
	}

	public function sinregistrar($Y='',$m='')
	{
		$tescuentas=new Tescuentascorrientes();
		$this->cuentas=$tescuentas->find("conditions: aclempresas_id=".Session::get('EMPRESAS_ID'));
		
		$anio=$this->anio=Session::get('ANIO');
		if($Y!='')$anio=$this->anio=$Y;
		$mes_activo=$this->mes_activo=date('m');
		if($m!='')$mes_activo=$this->mes_activo=$m;
		$this->SAL=$salidas= new Tessalidas();
		
		$this->cheques = $salidas->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND estado=1 AND tesdocumentos_id=35 AND (ISNULL(estadosalida) or estadosalida="Pagado")','order: femision DESC');
		$documentos= new Tesdocumentos();
		$doc= $documentos->find_first((int)35);
		Session::set('DOC_ID',$doc->id);
		Session::set('DOC_NOMBRE',$doc->nombre);
		Session::set('DOC_ABR',$doc->abr);
		Session::set('DOC_CODIGO',$doc->codigo);
	}	
	public function registrar($id)
	{
		if (Input::hasPost('cheques')) {
           
			$salidas= new Tessalidas(Input::post('salidas'));
			$salidas->userid=Auth::get('id');
			$salidas->estadosalida='Pendiente';
			$salidas->aclempresas_id=Session::get('EMPRESAS_ID');
			$salidas->save();
			if($salidas->save())
			{
				
				$che= new Teschequessalidas(Input::post('cheques'));
			    $che->tessalidas_id=$salidas->id;
			    $che->aclusuarios_id=Auth::get('id');
			    $che->numerodecheque=$salidas->numero;
				$che->fecha_cobro=$salidas->fvencimiento;
				$che->estadocheque='Pendiente';
				$che->monto=$salidas->totalconigv;
				$che->estado=$salidas->estado;
				$che->userid=$salidas->userid;
				$che->aclempresas_id=$salidas->aclempresas_id;
				$che->save();
		/*Auditorias*/
		Aclauditorias::add("El cheque fue Registrado Total={$che->monto},numero={$che->numercheque},Teschequessalidas->id={$che->id}",'Teschequessalidas');
		/*fin Aurditorias*/
				Flash::valid('El cheque fue Registrado');
				return Redirect::toAction('sinregistrar');
			}
      }
		$salidas= new Tessalidas();
		$this->salidas = $salidas->find_first((int)$id);
		
		$cheques = new Teschequessalidas();
		if($cheques->exists("tessalidas_id=".$id))
		{
			$this->cheques=$cheques->find_first('tessalidas_id='.(int)$id);
		}
	}
/*Accion para Realizar el cobro del cheque graba la fecha del click autamaticamente */
	public function cobrar($id)
	{
		$che= new Teschequessalidas();
		$che->find_first((int)$id);
		$che->fecha_cobro=date("Y-n-d H:i:s");
		$che->estadocheque='COBRADO';
		$che->userid=Auth::get('id');
		if($che->save())
		{
		/*Auditorias*/
		Aclauditorias::add("El cheque fue Cobrado Total={$che->monto},numero={$che->numercheque},Teschequessalidas->id={$che->id}",'Teschequessalidas');
		/*fin Aurditorias*/
				$ingresos= new Tessalidas();
				$ingresos->find_first((int)$che->tessalidas_id);
				$ingresos->estadosalida='COBRADO';
				$ingresos->userid=Auth::get('id');
				$ingresos->save();
				Flash::valid('El cheque fue Cobrado');
		return Redirect::toAction('pendientes');
		}
		
	}
	public function pendientes()
	{
	  	$empresas= new Aclempresas();
		$this->empresa=$empresas->find_first((int)Session::get('EMPRESAS_ID'));
		$cheques = new Teschequessalidas();
		$this->cheques=$cheques->find('conditions: aclempresas_id='.Session::get('EMPRESAS_ID').' AND estadocheque="Pendiente"');
	}
	public function chequesanulados($id=0){
		if (Input::hasPost('cheques'))
		{
			$Cheques= new Teschequessalidas(Input::post('cheques'));
			$Cheques->tessalidas_id=0;
			$Cheques->aclusuarios_id=Auth::get('id');
			$Cheques->estadocheque='ANULADO';
			$Cheques->aclempresas_id=Session::get('EMPRESAS_ID');
			$Cheques->save();
			return Redirect::toAction('chequesanulados');
		}
		$Cheques = new Teschequessalidas();
		if($id!=0){
			$this->cheques=$Cheques->find_first($id);
		}
		$this->cheques_anulados=$Cheques->find('estadocheque="ANULADO"');

	}
	
	
}

?>