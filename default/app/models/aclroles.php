<?php
class Aclroles extends ActiveRecord {

    public function initialize() {
        //relaciones
        $this->has_and_belongs_to_many('aclrecursos', 'model: aclrecursos', 'fk: aclrecursos_id', 'through: aclpermisos', 'key: aclroles_id');
        
        //validaciones
        $this->validates_presence_of('rol','message: Debe escribir el <b>Nombre del Rol</b>');
        
    }

    public function before_validation_on_create(){
        $this->validates_uniqueness_of('rol','message: Este Rol <b>ya existe</b> en el sistema');
    }

    public function getRecursos(){
        $columnas = "r.*";
        $join = "INNER JOIN aclpermisos as rr ON rr.aclroles_id = aclroles.id ";
        $join .= "INNER JOIN aclrecursos as r ON rr.aclrecursos_id = r.id ";
        $where = "aclroles.id = '$this->id'";
        return $this->find($where, "columns: $columnas" , "join: $join");
    }

}

