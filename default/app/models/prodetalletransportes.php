<?php
class Prodetalletransportes extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('tessalidas','protransportes','protransportistas');
		
    }
	public function detalles($id,$origen)
	{
		
		$cond=' AND protransportes_id='.$id.'';
		if($origen!='Transporte'){$cond=' AND protransportistas_id='.$id.'';}
		return $this->find_all_by_sql('SELECT d.id as id, s.id as salidas_id, pp.nombre as chofer , CONCAT_WS("-",pt.modelo,pt.numeroplaca) as carro, d.fechatraslado as fecha, CONCAT_WS("-",s.serie, s.numero) as numero , td.razonsocial as cliente, s.direccion_entrega as destino
				FROM tessalidas as s, tesdatos as td, prodetalletransportes as d 
				LEFT JOIN protransportes as pt 
				ON d.protransportes_id=pt.id 
				LEFT JOIN protransportistas as pp  
				ON d.protransportistas_id=pp.id 
				WHERE s.tesdatos_id=td.id AND d.tessalidas_id=s.id'.$cond.' ORDER BY fecha');
	}
}
?>