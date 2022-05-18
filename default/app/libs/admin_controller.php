<?php
/**
 * @see Controller nuevo controller
 */
require_once CORE_PATH . 'kumbia/controller.php';

/**
 * Controlador para proteger los controladores que heredan
 * Para empezar a crear una convención de seguridad y módulos
 *
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los métodos aquí definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 */

View::template('admin');
abstract class AdminController extends Controller
{
    public $estados=array('0'=>'Sin uso','1'=>'Activo','2'=>'Inactivo','3'=>'Papelera','9'=>'Privado');
    public $meses=array('01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Ago','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic');
    public $data=NULL;

    final protected function initialize()
    {
        //Código de auth y permisos
        //Será libre, pero añadiremos uno por defecto en breve
        //Posiblemente se cree una clase abstracta con lo que debe tener por defecto
        $modulo = Router::get('module');
		if($modulo=='santapatricia')
		{
			Session::set("EMPRESAS_ID",1);
			Session::set("EMPRESAS_NOMBRE",'TEXTIL SANTA PATRICIA');
			Session::set('INVENTARIO_ID',Session::get('INVENTARIO_ID_SP'));
		
		}
		if($modulo=='santacarmela')
		{
			Session::set("EMPRESAS_ID",2);
			Session::set("EMPRESAS_NOMBRE",'SANTA CARMELA');			
			Session::set('INVENTARIO_ID',Session::get('INVENTARIO_ID_SC'));
		}
		if($modulo=='admin')
		{
			Session::set("EMPRESAS_ID",9);
			Session::set("EMPRESAS_NOMBRE",'Admin');			
			Session::set('INVENTARIO_ID',Session::get('INVENTARIO_ID_SC'));
		}
        if (MyAuth::es_valido())
		{
			//View::template('backend/backend');
            $acl = new MyAcl();
            if (!$acl->check())
			{
				if ($acl->limiteDeIntentosPasado())
				{
                    $acl->resetearIntentos();
                    return $this->intentos_pasados();
                }
                Flash::error('No posees privilegios para acceder a <b>' . Router::get('route') . '</b>');
                View::select(NULL);
                return FALSE;
            }else
			{
                $acl->resetearIntentos();
            }	
			
        } elseif (Input::hasPost('login') && Input::hasPost('clave')) {
            if(MyAuth::autenticar(Input::post('login'), Input::post('clave'))) {
                Flash::info('Bienvenido al Sistema <b>' . Auth::get('nombres') . '</b>');
				return Redirect::to();
            } else {
                Flash::warning('Datos de Acceso Invalidos');
                View::select(NULL, 'logueo');
                return FALSE;
            }
        } else {
            View::select(NULL, 'logueo');
            return FALSE;
        }
    }

    public function logout() {
        MyAuth::cerrar_sesion();
    }

    final protected function finalize()
    {
        
    }

}
