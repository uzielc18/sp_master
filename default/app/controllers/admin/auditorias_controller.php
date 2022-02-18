<?php
Load::models('aclauditorias', 'aclusuarios');
View::template('backend/backend');
class AuditoriasController extends AdminController {
 	protected function before_filter() {
        if ( Input::isAjax() ){
			View::response('view');
            //View::select(NULL, NULL);
        }
    }
    public function index($pag= 1) {
        try {
            Session::delete('filtro_auditorias_usuario');
            $usr = new Aclusuarios();
            $this->usuarios = $usr->obtener_usuarios_con_num_acciones($pag);
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function usuario($id, $pagina = 1) {
        $this->url = "admin/auditorias/usuario/$id";
        try {
            $usr = new Aclusuarios();
            $aud = new Aclauditorias();
            $this->auditorias = $aud->auditorias_por_usuario($id, $pagina);
            if (!$this->auditorias->items) {
                Flash::info('Este usuario no ha realizado ninguna acción en el sistema...!!!');
                return Router::redirect();
            }
            $this->usuario = $usr->find_first($id);
            $this->tablas_afectadas = $aud->tablas_afectadas();
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function resultados_usuario($id,$pagina = 1) {
        $this->url = "admin/auditorias/resultados_usuario/$id";
        try {
            if (Input::hasPost('filtro')) {
                Session::set('filtro_auditorias_usuario', Input::post('filtro'));
            }
            $usr = new Aclusuarios();
            $aud = new Aclauditorias();
            $this->usuario = $usr->find_first($id);
            $this->tablas_afectadas = $aud->tablas_afectadas();
            $this->filtro = Session::get('filtro_auditorias_usuario');
            $this->auditorias = $aud->auditorias_por_usuario($id, $pagina, $this->filtro);
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        View::select('usuario');
    }

}
