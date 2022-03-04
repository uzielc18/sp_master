<?php
/**
 * @see KumbiaView
 */
require_once CORE_PATH . 'kumbia/kumbia_view.php';

/**
 * Esta clase permite extender o modificar la clase ViewBase de Kumbiaphp.
 *
 * @category KumbiaPHP
 * @package View
 */
class View extends KumbiaView
{
    public static function renderMenu($entorno,$ubicacion=1,$attr='class="nav"') {  /* ED: añadi lo que antes antes agrego uziel, la variable ",$attr=''" */
        if (Auth::is_valid()) {
            echo $var = Menu::render(Auth::get('aclroles_id'),$entorno,$ubicacion,$attr); /* ED: variable ",$attr=''" de paso añadida en la carga del render() */
        } else {
            return '';
        }
    }

    public static function excepcion(KumbiaException $e) {
        Flash::warning('Lo sentimos, Ha Ocurrido un Error...!!!');
        if (Config::get('config.application.log_exception') || !PRODUCTION) {
            Flash::error($e->getMessage());
        }
        if (!PRODUCTION) {
            Flash::error($e->getTraceAsString());
        }
        Logger::critical($e);
        Flash::info('Si el problema persiste por favor informe al Administrador del Sistema...!!!');
    }


}
