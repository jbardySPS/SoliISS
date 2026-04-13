<?php

namespace App\Database;

use Cooperl\Database\DB2\Schema\Grammars\DB2Grammar as BaseDB2Grammar;

/**
 * Parche de compatibilidad para cooperl/laravel-db2 con Laravel 13.
 *
 * Laravel 13 actualizó Grammar::compileTableExists() y compileColumnExists()
 * para aceptar parámetros ($schema, $table), pero el paquete cooperl no fue
 * actualizado. PHP lanza un Fatal Error al cargar la clase por firma incompatible.
 *
 * Esta subclase corrige las firmas sin modificar vendor/.
 */
class DB2Grammar extends BaseDB2Grammar
{
    /**
     * @param string|null $schema
     * @param string|null $table
     */
    public function compileTableExists($schema = null, $table = null): string
    {
        return 'select * from information_schema.tables where table_schema = upper(?) and table_name = upper(?)';
    }

    /**
     * @param string|null $schema
     * @param string|null $table
     */
    public function compileColumnExists($schema = null, $table = null): string
    {
        return 'select column_name from information_schema.columns where table_schema = upper(?) and table_name = upper(?)';
    }
}
