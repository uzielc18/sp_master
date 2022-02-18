<?php
class Aclmenus extends ActiveRecord {
//    public $debug = true;
//    public $logger = true;

    /**
     * Constante que define que el menu es visible desde app
     */
    const VISIBILIDAD_APP = 1;

    /**
     * Constante que define que el menu es visible desde el backend
     */
    const VISIBILIDAD_BACKEND = 2;
	/**
     * Constante que define que el menu es visible desde el santapatricia
     */
    const VISIBILIDAD_SPATRICIA = 0;
	/**
     * Constante que define que el menu es visible desde el satnacarmela
     */
    const VISIBILIDAD_SCARMELA = 4;

    /*
     * Constante que define que el menu es visible desde cualquier lado
     */
    const VISIBILIDAD_TODAS = 3;

    public function initialize() {
        $this->has_many('aclmenus');
        //validaciones
        $this->validates_presence_of('aclrecursos_id', 'message: Debe seleccionar un <b>Recurso</b> al cual se va acceder');
        $this->validates_presence_of('nombre', 'message: Debe escribir el <b>Texto a Mostrar</b> en el menu');
        $this->validates_presence_of('url', 'message: Debe escribir la <b>URL</b> en el menu');
    }

    public function before_validation_on_create() {
        $this->validates_uniqueness_of('nombre', 'message: Ya hay un menu con el <b>mismo Nombre</b>');
    }

    public function obtener_menu_por_rol($id_rol, $entorno, $ubicacion) {
        $select = 'm.' . join(',m.', $this->fields) . ',re.recurso';
        $from = 'aclmenus as m';
        $joins = "INNER JOIN aclpermisos AS rr ON m.aclrecursos_id = rr.aclrecursos_id ";
        $joins .= " AND ( " . $this->obtener_condicion_roles_padres($id_rol) . " ) ";
        $joins .= 'INNER JOIN aclrecursos AS re ON re.activo = 1 AND re.id = rr.aclrecursos_id ';
        $condiciones = " (m.aclmenus_id is NULL OR m.aclmenus_id = 0 ) AND m.activo =1 ";
		$condiciones.= " AND ubicacion='".$ubicacion."'";
        $condiciones .= " AND visible_en IN ('3','$entorno') ";
        $orden = 'm.posicion';
        $agrupar_por = 'm.' . join(',m.', $this->fields) . ',re.recurso';
        return $this->find_all_by_sql("SELECT $select FROM $from $joins WHERE $condiciones GROUP BY $agrupar_por ORDER BY $orden");
    }

    public function get_sub_menus($id_rol, $entorno, $ubicacion) {
        $campos = 'aclmenus.' . join(',aclmenus.', $this->fields) . ',r.recurso';
        $join = 'INNER JOIN aclrecursos as r ON r.id = aclmenus.aclrecursos_id AND r.activo = 1 ';
        $join .= 'INNER JOIN aclpermisos as rr ON r.id = rr.aclrecursos_id ';
        $join .= ' AND (rr.aclroles_id = \'' . $id_rol . '\' OR ' . $this->obtener_condicion_roles_padres($id_rol) . ')';
        $condiciones = "aclmenus.aclmenus_id = '{$this->id}' AND aclmenus.activo = 1 ";
		$condiciones.= " AND ubicacion='".$ubicacion."'";
        $condiciones .= " AND visible_en IN ('3','$entorno') ";
        $agrupar_por = 'aclmenus.' . join(',aclmenus.', $this->fields) . ',r.recurso';
        return $this->find($condiciones, "join: $join", "columns: $campos", 'order: aclmenus.posicion', "group: $agrupar_por");
    }

    public function menus_paginados($pagina,$visible=3) {
        $cols = 'aclmenus.' . join(',aclmenus.', $this->fields) . ",r.recurso,m2.nombre as padre";
        $joins = 'INNER JOIN aclrecursos as r ON r.id = aclrecursos_id ';
        $joins .= 'LEFT JOIN aclmenus as m2 ON m2.id = aclmenus.aclmenus_id ';
        return $this->paginate("conditions: aclmenus.visible_en='".$visible."'","page: $pagina", "per_page: 20", "columns: $cols","join: $joins",'order: aclmenus.aclmenus_id,aclmenus.visible_en,aclmenus.posicion');
    }

    public function before_save() {
        $this->posicion = !empty($this->posicion) ? $this->posicion : '100';
    }

    protected function obtener_condicion_roles_padres($id_rol) {
        $rol = Load::model('aclroles')->find_first($id_rol);
        $roles = array("rr.aclroles_id = '{$rol->id}'");
        if ($rol->padres) {
            foreach (explode(',', $rol->padres) as $e) {
                $roles[] = "rr.aclroles_id = '$e'";
            }
        }
        return join(' OR ', $roles);
    }

}
?>