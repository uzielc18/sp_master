<?php
class Protipoplegadores extends ActiveRecord {

    public function initialize() {
        //validaciones
		$this->has_many('proplegadores');
    }
	public function before_validation_on_create() {
        $this->validates_uniqueness_of('numero', 'message: Ya hay un Plegador con el <b>mismo Número</b>');
    }
	public function plconmovimientos()
	{
		return $this->find_all_by_sql('SELECT proplegadores . * , COUNT( promovimientos.id ) AS n
										FROM proplegadores
										INNER JOIN promovimientos ON promovimientos.proplegadores_id = proplegadores.id
										WHERE proplegadores.estado =  "1"
										AND proplegadores.activo =  "1"
										GROUP BY proplegadores.id
										ORDER BY promovimientos.id, n DESC ');
	}
}
?>