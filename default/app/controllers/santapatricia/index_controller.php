<?php

class IndexController extends AdminController {
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
			
        }
    }
    public function index()
	{
		$paneles= new Proproduccion();
		$this->producciones=$paneles->panel_produccion();
		//$this->ventas=$paneles->panel_ventas();
		$this->ventas=$paneles->panel_semanal(date("Y-m-d"),7);
		$this->ventasg=$paneles->panel_semanal(date("Y-m-d"),15);
		$this->rollos=$paneles->panel_rollos();
		$this->ordenes=$paneles->panel_orden();
		$this->compras=$paneles->panel_ingresos();
		
		$fecha = new DateTime();
		$fecha->modify('first day of this month');
		$f1=$fecha->format('Y-m-d');
		
		$fecha2 = new DateTime();
		$fecha2->modify('last day of this month');
		$f2=$fecha2->format('Y-m-d');
	}
	
    public function bienvenida()
	{
		
    }
	public function produccion()
	{
		$paneles= new Proproduccion();
		$this->producciones=$paneles->panel_produccion();
	}
	public function ventas()
	{
		$paneles= new Proproduccion();
		$this->ventas=$paneles->panel_ventas();
	}
	public function semana_facturas($fecha)
	{
		$this->fecha=$fecha;
		$paneles= new Proproduccion();
		$this->ventas=$paneles->panel_semanal($fecha,7);
	}
	public function semana_guias($fecha)
	{
		$this->fecha=$fecha;
		$paneles= new Proproduccion();
		$this->ventas=$paneles->panel_semanal($fecha,15);
	}
	public function rollos()
	{
		$paneles= new Proproduccion();
		$this->rollos=$paneles->panel_rollos();
	}
}
