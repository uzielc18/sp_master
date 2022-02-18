<?php

Load::models('aclconfiguraciones','testipocambios','tesingresos');
class MyAuth {

    protected static $_clave_sesion = PUBLIC_PATH;

    public static function autenticar($user, $pass) {
        Session::set(self::$_clave_sesion . '_sesion_activa', false);
        $pass = md5($pass);
        $auth = new Auth('class: aclusuarios',
                        'usuario: ' . $user,
                        'clave: ' . $pass,
                        "activo: 1");
        if ($auth->authenticate()) {
			Session::set("EMPRESAS_ID",0);
			/*cargar en session el igv y el tipo de cambio actual(id) y monto*/
			$confs=new Aclconfiguraciones();
			$confd=$confs->find();
			foreach($confd as $c)
			{
				Session::set($c->variable,$c->valor);
			}
			$cambio= new Testipocambios();
			$cam=$cambio->find_first('conditions: activo=1');
			Session::set("CAMBIO_ID",$cam->id);
			Session::set("CAMBIO_MONTO",$cam->compra);
			Session::set("time_session",time());
            Session::set(self::$_clave_sesion . '_sesion_activa', true);
        }
        return self::es_valido();
    }

    public static function es_valido() {
        return Session::get(self::$_clave_sesion . '_sesion_activa');
    }

    public static function cerrar_sesion() {
        Auth::destroy_identity();
        Session::delete(self::$_clave_sesion . '_sesion_activa');
		/*PARA CERRAR LAS SESSIONES ACTIVAS*/
		session_destroy();
		$parametros_cookies = session_get_cookie_params(); 
		setcookie(session_name(),0,1,$parametros_cookies["path"]);
    }

}

