<?php
class Promaquinas extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('proubicacionmaquina','proproduccion','proeficiencias');
		$this->belongs_to('tesproductos');
    }
	public function before_validation_on_create(){
        $this->validates_uniqueness_of('tesproductos_id','message: Este id <b>ya existe</b> en el sistema');
    }
	public function getLados($id)
	{
		/*/"SELECT  p.detalle as detalle, m.id as m_id,m.nombre as maquina, u.* FROM tesproductos as p, (proubicacionmaquina as u LEFT JOIN promaquinas as m ON m.id=u.promaquinas_id)
WHERE m.tesproductos_id=p.id AND  u.promaquinas_id=m.id AND m.activo='1' AND m.estado='1' ORDER BY m_id"*/
		return $this->find_all_by_sql("SELECT p.detalle AS detalle, m.id AS m_id, m.nombre AS maquina,m.imagen as imagen, u . *  FROM tesproductos AS p, promaquinas as m LEFT JOIN proubicacionmaquina as u ON m.id=u.promaquinas_id WHERE m.tesproductos_id = p.id AND p.testipoproductos_id=".$id." AND m.aclempresas_id=".Session::get('EMPRESAS_ID')." ORDER BY m_id,u.nombre ASC");
	}
	/*$id el id de la maquina*/
	public function getMaxvalor($id=0,$turno_id=0,$datos_id=0)
	{
		//echo "SELECT IFNULL(max(valor),0) as valor FROM `proeficiencias` WHERE proturnos_id=".$turno_id." AND promaquinas_id=".$id." AND acldatos_id=".$datos_id;
		$v=$this->find_by_sql("SELECT IFNULL(max(id),0) as id FROM `proeficiencias` WHERE promaquinas_id=".$id." AND acldatos_id=".$datos_id); 
		$valor=$this->find_by_sql("SELECT id,valor FROM proeficiencias WHERE id=".(int)$v->id);
		return (int)$valor->valor;
	}
	/*Valida si ys ingreso la eficiencia para ayer*/
	public function validarP()
	{
		$hoy=date("Y-m-d"); // tu sabrás como la obtienes, solo asegurate que tenga este formato 
		$dias= 1; // los días a restar 
		$ayer= date("Y-m-d", strtotime("$hoy -$dias day"));
		$v=$this->find_by_sql("SELECT count(id) as ids FROM `proeficiencias` WHERE fecha='".$ayer."' AND promaquinas_id=".$this->id);
		return $v->ids;
	}
	/*LISTADO DE LA MAQUINAS POR TIPO DE PRODUCTOS*/
	public function getListado($id)
	{
		/*36=>Tejeduria,37=>Plegadores,140=>Hilatura,141=>Urdido,142=>Control de Calidad(Maquinas Revisadoras),
		  143=>Maquinas Estampadoras,144=>Maquinas Anudadoras-Compresoras,163=>Enconado Maquinas para enconar.*/
		  
		  return $this->find_all_by_sql("SELECT m.* FROM  promaquinas as m INNER JOIN tesproductos as p ON p.id=m.tesproductos_id AND p.testipoproductos_id=".$id);
	}
	
}
?>