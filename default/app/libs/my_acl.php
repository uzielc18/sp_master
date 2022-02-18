<?php

class MyAcl {

    /**
     *
     * @var SimpleAcl
     */
    static protected $_acl = null;

    /**
     * arreglo con los templates para cada usuario
     *
     * @var array 
     */
    protected $_templates = array();

    /**
     * Recurso al que se esta intentando acceder
     *
     * @var string 
     */
    protected $_recurso_actual = NULL;

    public function __construct() {

        self::$_acl = Acl2::factory();

        $roles = Load::model('aclroles')->find('activo = 1');
		
        $this->_establecerRoles($roles);
    }

    protected function _establecerRoles($roles) {
        foreach ($roles as $e) {
            if ($e->activo) {
                //var_dump($e->rol);
                self::$_acl->user($e->id, array($e->id));
                self::$_acl->parents($e->id, explode(',', $e->padres));
                $this->_establecerTemplate($e->id, $e->plantilla);
                $this->_establecerRecursos($e->id, $e->getRecursos());
            }
        }
    }

    protected function _establecerRecursos($rol, $recursos) {
        $urls = array();
        foreach ($recursos as $e) {
            if ($e->activo) {
                $urls[] = $e->recurso;
            }
        }
        //var_dump($urls);
        self::$_acl->allow($rol, $urls);
    }

    protected function _establecerTemplate($rol, $template) {
        if (!empty($template)) {
            $this->_templates["$rol"] = $template;
        }
    }

    public function check() {

        $usuario = Auth::get('aclroles_id');
        $modulo = Router::get('module');
		
        $controlador = Router::get('controller');
        $accion = Router::get('action'); 

        if (isset($this->_templates["$usuario"])) {
            View::template("{$this->_templates["$usuario"]}");
        }
        if ($modulo) {
            $recurso1 = "$modulo/$controlador/$accion";
            $recurso2 = "$modulo/$controlador/*";  //por si tiene acceso a todas las acciones
            $recurso3 = "$modulo/*/*";  //por si tiene acceso a todos los controladores
            $recurso4 = "*";  //por si tiene acceso a todo el sistema
        } else {
            $recurso1 = "$controlador/$accion";
            $recurso2 = "$controlador/*"; //por si tiene acceso a todas las acciones
            $recurso3 = "$modulo/*/*";  //por si tiene acceso a todos los controladores
            $recurso4 = "*";  //por si tiene acceso a todo el sistema
        }
        return self::$_acl->check($recurso1, $usuario) ||
                self::$_acl->check($recurso2, $usuario) ||
                self::$_acl->check($recurso3, $usuario) ||
                self::$_acl->check($recurso4, $usuario);
    }

    public function limiteDeIntentosPasado() {
        if (Session::has('intentos_acceso')) {
            $intentos = Session::get('intentos_acceso') + 1;
            Session::set('intentos_acceso', $intentos);
            $max_intentos = Config::get('config.application.intentos_acceso');
            return $intentos >= $max_intentos;
        } else {
            Session::set('intentos_acceso', 0);
        }
        return false;
    }

    public function resetearIntentos() {
        Session::set('intentos_acceso', 0);
    }
    
}
