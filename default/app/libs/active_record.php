<?php
/**
 * @see KumbiaActiveRecord
 */
require_once CORE_PATH.'libs/kumbia_active_record/kumbia_active_record.php';

/**
 * ActiveRecord
 *
 * Clase padre ActiveRecord para añadir tus métodos propios
 *
 * @category Kumbia
 * @package Db
 * @subpackage ActiveRecord
 */
abstract class ActiveRecord extends KumbiaActiveRecord
{
    //    public $debug = true;
//    public $logger = true;

	// Para Activar un registro (se muestra el registro)
    public function activar() {
        $this->activo = '1';
        return $this->update();
    }
	// Para desactivar un registro (se muestra el registro)
    public function desactivar() {
        $this->activo = '0';
        return $this->update();
    }
	/*Para Cambiar el estado a los regitros (en el caso especifico de Borrar / el registro no se muestra en los listados)*/
	public function borrar() {
        $this->estado = '3';
        return $this->update();
    }

    /**
     * Elimina caracteres que podrian ayudar a ejecutar
     * un ataque de Inyeccion SQL
     *
     * Copia de la funcion en KumbiaActiveRecord pero esta no quita los
     * espacios en blanco, para poder crear alias en postgres
     *
     * @param string $sql_item
     */
    public static function sql_sanizite($sql_item) {
        $sql_item = trim($sql_item);
        if ($sql_item !== '' && $sql_item !== null) {
            $sql_match = preg_replace('/\s+/', '', $sql_item);
            if (!preg_match('/^[a-zA-Z_0-9\,\(\)\.\*]+$/', $sql_match)) {
                throw new KumbiaException("Se esta tratando de ejecutar una operacion maliciosa!");
            }
        }
        return $sql_item;
    }


}
