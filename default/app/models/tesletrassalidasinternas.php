<?php
class Tesletrassalidasinternas extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->belongs_to('tessalidasinternas');
    }
	public function getLetrasAdelantos()
	{
		return $this->find_all_by_sql("SELECT s.id as salidas_id,s.estadosalida,s.total,l.id as letra_id,l.monto,d.id,d.tesletrassalidasinternas_id,round(sum(d.monto),2),
round(l.monto,2)-ROUND(ifnull(sum(d.monto),0),2),l.estado FROM tessalidasinternas as s, tesletrassalidasinternas as l left JOIN tesaplicacionletrainterna as d ON d.tesletrassalidasinternas_id=l.id
WHERE s.id=l.tessalidasinternas_id
GROUP BY l.id");
	}
	public function getLetraAdelanto($id=0)
	{
		$sql="SELECT s.id as salidas_id,s.estadosalida,s.total,l.id as letra_id,l.monto,d.id,d.tesletrassalidasinternas_id,round(sum(d.monto),2),
round(l.monto,2)-ROUND(ifnull(sum(d.monto),0),2) as resta,l.estado FROM tessalidasinternas as s, tesletrassalidasinternas as l INNER JOIN tesaplicacionletrainterna as d ON d.tesletrassalidasinternas_id=l.id AND l.id=".$id."
WHERE s.id=l.tessalidasinternas_id
GROUP BY l.id";
		$letra=$this->find_by_sql($sql);
		if(!empty($letra->resta))
		{
		if($letra->resta<=0.20)
		{
			$L=$this->find_first($letra->letra_id);
			$L->estado=9;
			if($L->save())
			{
				Flash::valid("La letra se termino por que ya no tenia saldo");
			}
			
		}else
		{
			$L=$this->find_first($letra->letra_id);
			$L->estado=1;
			if($L->save())
			{
				Flash::info("Se volvio a calcular las aplicaciones y esta letra aun tiene saldo");
			}
		}
		}else
		{
			Flash::info("La letra no tiene niguna aplicacion");
		}
	}
	
}
?>