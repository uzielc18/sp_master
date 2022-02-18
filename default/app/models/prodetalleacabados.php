<?php
class Prodetalleacabados extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->belongs_to('proprocesos','proacabados');
    }
	public function getAcabos($id,$tipo)
	{
		return $this->find_all_by_sql('SELECT a.nombre as nombre, a.tipo_acabado FROM prodetalleacabados  as p, proacabados as a WHERE a.id=p.proacabados_id AND a.tipo_acabado='.$tipo.' AND p.proprocesos_id='.$id);
	}
}
?>