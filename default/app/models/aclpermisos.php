<?php
class Aclpermisos extends ActiveRecord {

//    public $debug = true;

    public function initialize() {
        $this->belongs_to('aclroles','aclrecursos');
    }

    public function obtener_privilegios() {
        $privilegios = array();
        foreach ($this->find() as $e) {
            $privilegios["{$e->aclroles_id}-{$e->aclrecursos_id}"] = $e->id;
        }
        return $privilegios;
    }

    public function eliminarTodos() {
        return $this->delete_all();
    }

    /**
     * Elimina todos los registros por id ejemplo 
     * 
     * @example
     * 
     * eliminarPorIds("1,2,3,4");
     * 
     * elimina los registros con id 1,2,3 y 4
     *
     * @param string $ids
     * @return boolean 
     */
    public function eliminarPorIds($ids) {
        if (!empty($ids)) {
            $ids = str_replace('"', "'", Util::encomillar($ids));
            return $this->delete_all("id IN ($ids)");
        }else{
            return true;
        }
    }

    public function guardar($rol, $recurso) {
        if ($this->existe($rol, $recurso))
            return true;
        return $this->create(array(
            'aclroles_id' => $rol,
            'aclrecursos_id' => $recurso
        ));
    }

    public function editarPrivilegios($privilegios, $privilegios_a_eliminar) {
        $this->begin();
        //elimino todo de la bd
        if (!$this->eliminarPorIds($privilegios_a_eliminar)) {
            $this->rollback();
            return false;
        }
        foreach ((array) $privilegios as $e) {
            $data = explode('/', $e); //el formato es 1/4 = rol/recurso
            if (!$this->guardar($data[0], $data[1])) {
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    public function existe($rol, $recurso) {
        return $this->exists("aclroles_id = '$rol' AND aclrecursos_id = '$recurso'");
    }

}

