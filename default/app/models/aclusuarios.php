<?php

class Aclusuarios extends ActiveRecord {

    public function initialize() {
        $min_clave = Config::get('config.application.minimo_clave');
        $this->belongs_to('aclroles','aclempresas');
		$this->has_one('acldatos');
        $this->has_many('aclauditorias','peraccidentes','aclempresas','aclroles','tessalidasinternas','tessalidas','tesproformas','tesproductos','tesordendecompras','tesingresos','protrama','proproduccion','proprocesos','proplegadoresenuso','pronotapedidos','memos','proturnos','proeficiencias','prorollos');
        $this->validates_presence_of('usuario', 'message: Debe escribir un <b>Usuario</b> para el Usuario');
        $this->validates_presence_of('clave', 'message: Debe escribir una <b>Contraseña</b>');
        $this->validates_length_of('clave', 50, $min_clave, "too_short: La Clave debe tener <b>Minimo {$min_clave} caracteres</b>");
        $this->validates_presence_of('clave2', 'message: Debe volver a escribir la <b>Contraseña</b>');
        $this->validates_presence_of('nombres', 'message: Debe escribir su <b>nombre completo</b>');
        $this->validates_presence_of('aclroles_id', 'message: Debe seleccionar un <b>Rol de Usuario</b>');
    }

    public function before_validation_on_create() {
        $this->validates_uniqueness_of('usuario', 'message: El <b>Usuario</b> ya está siendo utilizado');
    }

    public function before_save() {
        if (isset($this->clave2) and $this->clave !== $this->clave2) {
            Flash::error('Las <b>CLaves</b> no Coinciden...!!!');
            return 'cancel';
        } elseif (isset($this->clave2)) {
            $this->clave = md5($this->clave);
        }
    }

    public function obtener_usuarios($pagina = 1) {
        $cols = "aclusuarios.*,aclroles.rol";
        $join = "INNER JOIN aclroles ON aclroles.id = aclroles_id";
        return $this->paginate("page: $pagina", "columns: $cols", "join: $join");
    }

    public function obtener_usuarios_con_num_acciones($pagina = 1) {
        $cols = "aclusuarios.*,aclroles.rol,COUNT(aclauditorias.id) as num_acciones";
        $join = "INNER JOIN aclroles ON aclroles.id = aclusuarios.aclroles_id ";
        $join .= "LEFT JOIN aclauditorias ON aclusuarios.id = aclauditorias.aclusuarios_id";
        $group = 'aclusuarios.' . join(',aclusuarios.', $this->fields);
        $sql = "SELECT $cols FROM $this->source $join GROUP BY $group";
        return $this->paginate_by_sql($sql, "page: $pagina",'per_page: 25');
        //comentada la siguiente linea debido a que el active record lanzaba
        //una advertencia de que el count esta devolviendo mas de 1 registro,
        //esto es por el group by
        //return $this->paginate("page: $pagina", "columns: $cols", "join: $join", "group: $group");
    }

    public function cambiar_clave($datos) {
        if (md5($datos['clave_actual']) != $this->clave) {
            Flash::error('Las <b>CLave Actual</b> es Incorrecta...!!!');
            return false;
        }
        $this->clave = $datos['nueva_clave'];
        $this->clave2 = $datos['nueva_clave2'];
        return $this->update();
    }

}
