<?php
class Aclrecursos extends ActiveRecord {


    public function initialize() {
        //validaciones
		$this->has_many('aclmenus','aclpermisos');
        $this->validates_presence_of('controlador', 'message: Debe escribir un <b>Controlador</b>');
        $this->validates_presence_of('descripcion', 'message: Debe escribir una <b>Descripción</b>');
    }

    public function before_validation_on_create() {
        $this->validates_uniqueness_of('recurso', 'message: Este Recurso <b>ya existe</b> en el sistema');
    }

    public function obtener_recursos_por_rol($id_rol) {
        $cols = 'aclrecursos.recurso';
        $joins = 'INNER JOIN aclpermisos as r ON r.aclrecursos_id = aclrecursos.id';
        $where = "r.aclroles_id = '$id_rol'";
        return $this->find("columns: $cols", "join: $joins", "$where");
    }

    public function before_validation() {
        if (empty($this->recurso)) {
            $this->recurso = !empty($this->modulo) ? "$this->modulo/" : '';
            $this->recurso .= "$this->controlador/";
            $this->recurso .= ! empty($this->accion) ? "$this->accion" : '*';
        }
    }

    public function obtener_recursos_nuevos($pagina = 1) {
        $recursos = LectorRecursos::obtenerRecursos();
        foreach ($recursos as $index => $re) {
            if ($this->exists('recurso = \'' . $re['recurso'] . '\'')) {
                unset($recursos[$index]);
            }
        }
        $recursos = LectorRecursos::paginar($recursos, $pagina, 100);
        $this->recursos_nuevos = $recursos->items;
        array_unshift($this->recursos_nuevos, null);
        return $recursos;
    }

    public function guardar_nuevos() {
        $recursos_a_guardar = array();
        $recursos_chequeados = Input::post('check');
        $descripciones = Input::post('descripcion');
        $activos = Input::post('activo');
        if ($recursos_chequeados) {
            foreach ($recursos_chequeados as $valor) {
                if (empty($descripciones[$valor])) {
                    Flash::error('Existen Recursos Seleccionados que no tienen especificada una Descripción');
                    return FALSE;
                }
                $data = null;
                $data = $this->recursos_nuevos[$valor];
                $data['descripcion'] = $descripciones[$valor];
                $data['activo'] = $activos[$valor];
                $recursos_a_guardar[] = $data;
            }
        } else {
            return false;
        }
        $this->begin();
        foreach ($recursos_a_guardar as $e) {
            if (!$this->save($e)) {
                $this->rollback();
                return FALSE;
            }
        }
        $this->commit();
        return TRUE;
    }

}
