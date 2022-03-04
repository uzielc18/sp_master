<?php
Load::models('aclauditorias');

class Acciones {

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

}

