<?Php
/*tesformadepagosabonos*/
 class Tesformadepagosabonos extends ActiveRecord {

    public function initialize() {
        //relaciones
		$this->has_many('tesabonos');
		$this->has_many('tesletrasingresos');
		//$this->belongs_to('aclusuarios','testipocambios','tesmonedas');
    }	
}
?>
