<?php

namespace App\Providers;

use App\Database\DB2Grammar;
use Illuminate\Support\ServiceProvider;

/**
 * Inyecta el grammar DB2 parcheado para compatibilidad con Laravel 13.
 *
 * cooperl/laravel-db2 no actualizó compileTableExists() / compileColumnExists()
 * para la nueva firma de Laravel 13. Este provider reemplaza el grammar
 * después de que la conexión es resuelta, sin tocar vendor/.
 */
class DB2CompatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Reemplazamos el schema grammar de la conexión DB2 con nuestra
        // subclase parcheada. Se ejecuta en cada request/comando antes
        // de que Schema:: sea usado.
        $this->app->resolving('db', function ($db) {
            $db->beforeExecuting(function () {
                // no-op, solo forzamos la resolución del manager
            });
        });

        // Parchamos directamente al arrancar
        $this->app->booted(function () {
            try {
                /** @var \Illuminate\Database\Connection $conn */
                $conn    = app('db')->connection('db2');
                $grammar = new DB2Grammar();
                $grammar->setTablePrefix($conn->getTablePrefix());
                $conn->setSchemaGrammar($grammar);
            } catch (\Throwable) {
                // Si la conexión no está disponible en este contexto, se ignora.
            }
        });
    }
}
