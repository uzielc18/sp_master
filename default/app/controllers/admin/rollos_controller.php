<?php 
View::template('backend/backend');
class RollosController extends ScaffoldController
{
	public $model='prorollos';
	public $titulo='Control de rollor ';
	public $columns  = 'id,codigo,numero,numeroventa,maquina_numero,metros,peso,estadorollo';
}
?>