<?php
class Aclauditorias extends ActiveRecord {

    public function initialize() {
        //relaciones
        $this->belongs_to('aclusuarios');
    }

    public function crear_filtro($filtro) {
        $condiciones = array();
        if (!empty($filtro['tabla_afectada'])) {

            $condiciones[] = "tabla_afectada LIKE '%{$filtro['tabla_afectada']}%'";
        }
        if (!empty($filtro['fecha_at'])) {
            $filtro['fecha_at'] = date('Y-m-d', strtotime($filtro['fecha_at']));
            $condiciones[] = "fecha_at LIKE '%{$filtro['fecha_at']}%'";
        }
        if (!empty($filtro['accion_realizada'])) {

            $condiciones[] = "accion_realizada LIKE '%{$filtro['accion_realizada']}%'";
        }
        return sizeof($condiciones) ? join(' AND ', $condiciones) : 'TRUE';
    }

    public function auditorias_por_usuario($id_usuario, $pagina = 1, $filtro = NULL) {
        $condiciones = 'TRUE';
        if ($filtro) {
            $condiciones = $this->crear_filtro($filtro);
        }
        $id_usuario = Filter::get($id_usuario, 'int');
        $where = "aclusuarios_id = '{$id_usuario}' AND {$condiciones}";
        return $this->paginate("per_page: 25","page: $pagina", "conditions: $where", "order: id desc");
    }

    public function tablas_afectadas() {
        $tablas = array('Seleccione');
        foreach ($this->distinct('tabla_afectada') as $e) {
            $tablas["$e"] = $e;
        }
        return $tablas;
    }

}

