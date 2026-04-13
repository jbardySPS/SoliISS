<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // DB2 iSeries: la conexión tiene schema=SOLIISS como default,
    // por lo que la tabla se crea en SOLIISS.SOLICITUDES.
    public function up(): void
    {
        Schema::create('SOLICITUDES', function (Blueprint $table) {
            $table->bigIncrements('sol_id');

            // Solicitante que creó la solicitud
            $table->unsignedBigInteger('sol_usr_id');

            // Datos de la solicitud
            $table->string('sol_tipo', 60);
            $table->string('sol_descripcion', 2000);
            $table->string('sol_prioridad', 10)->default('media');  // baja | media | alta

            // Estado del ciclo de vida
            // borrador → pendiente → aprobada → en_proceso → completada
            //                     → rechazada
            // borrador/pendiente → cancelada
            $table->string('sol_estado', 20)->default('borrador');

            // Revisión del supervisor
            $table->unsignedBigInteger('sol_supervisor_id')->nullable();
            $table->string('sol_obs_supervisor', 1000)->nullable();

            // Procesamiento del operador
            $table->unsignedBigInteger('sol_operador_id')->nullable();
            $table->string('sol_obs_operador', 1000)->nullable();

            // Timestamps manuales (nombres con prefijo sol_)
            $table->timestamp('sol_creado')->nullable();
            $table->timestamp('sol_actualizado')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('SOLICITUDES');
    }
};
