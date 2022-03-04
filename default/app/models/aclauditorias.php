<?php
class Aclauditorias extends ActiveRecord {

    public function initialize() {
        //relaciones
        $this->belongs_to('aclusuarios');
    }
    
    static public function add($accion_realizada, $tabla_afectada = NULL) {
        try {
            if (MyAuth::es_valido() &&
                Config::get('config.application.guardar_auditorias') == true) {
				$auditoria = new Aclauditorias();
				if(!$auditoria->exists('aclusuarios_id='.Auth::get('id').' AND accion_realizada="'.$accion_realizada.'"')){
                $auditoria->aclusuarios_id = Auth::get('id');
                $auditoria->accion_realizada = strip_tags($accion_realizada);
                $auditoria->tabla_afectada = strtoupper(strip_tags($tabla_afectada));
				$auditoria->created=time();
                $auditoria->save();
				}else
				{
				}
				
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
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

