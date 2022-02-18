<?php 
View::template('backend/backend');
class ReportesController extends ScaffoldController
{
	public $model='tesreportes';	
	public $titulo='Control de Reportes ';
	public $columns  = 'id,nombre,fecha,igv_mes_ant';
}
?>