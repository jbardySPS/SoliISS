<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla SOLIISS.SOLICITUDES junto con sus tablas de lookup.
     *
     * Esta estructura fue definida en Fase 0 y ya puede existir en producción.
     * El método up() es idempotente: verifica antes de crear.
     */
    public function up(): void
    {
        // ── Tipos de solicitud ──────────────────────────────────────────────
        if (! Schema::hasTable('TIPOS_SOL')) {
            Schema::create('TIPOS_SOL', function (Blueprint $table) {
                $table->smallInteger('tipo_id')->primary();
                $table->string('tipo_nombre', 100);
                $table->string('tipo_desc', 500)->nullable();
                $table->string('tipo_icono', 60)->nullable();
            });
        }

        // ── Estados de solicitud ────────────────────────────────────────────
        if (! Schema::hasTable('ESTADOS_SOL')) {
            Schema::create('ESTADOS_SOL', function (Blueprint $table) {
                $table->smallInteger('est_id')->primary();
                $table->string('est_nombre', 50);
                $table->string('est_color', 20)->nullable();
                $table->smallInteger('est_orden')->default(0);
            });
        }

        // ── Áreas destino ───────────────────────────────────────────────────
        if (! Schema::hasTable('AREAS_DEST')) {
            Schema::create('AREAS_DEST', function (Blueprint $table) {
                $table->smallInteger('area_id')->primary();
                $table->string('area_nombre', 100);
                $table->string('area_desc', 500)->nullable();
            });
        }

        // ── Solicitudes ─────────────────────────────────────────────────────
        if (! Schema::hasTable('SOLICITUDES')) {
            Schema::create('SOLICITUDES', function (Blueprint $table) {
                $table->increments('sol_id');
                $table->string('sol_numero', 20);
                $table->smallInteger('sol_tipo_id');
                $table->smallInteger('sol_area_dest_id');
                $table->string('sol_titulo', 200);
                $table->string('sol_descripcion', 4000);
                $table->smallInteger('sol_prioridad')->default(2); // 1=baja 2=media 3=alta
                $table->smallInteger('sol_estado_id')->default(1); // 1=borrador
                $table->string('sol_sistema', 100)->nullable();
                $table->unsignedInteger('sol_usr_solicita');
                $table->unsignedInteger('sol_usr_supervisor');
                $table->unsignedInteger('sol_usr_asignado')->nullable();
                $table->string('sol_resolucion', 4000)->nullable();
                $table->timestamp('sol_fecha_creacion');
                $table->timestamp('sol_fecha_envio')->nullable();
                $table->timestamp('sol_fecha_aprobacion')->nullable();
                $table->timestamp('sol_fecha_cierre')->nullable();
                $table->timestamp('sol_actualizado');
            });
        }

        // ── Historial de cambios de estado ──────────────────────────────────
        if (! Schema::hasTable('HISTORIAL_ESTADOS')) {
            Schema::create('HISTORIAL_ESTADOS', function (Blueprint $table) {
                $table->increments('his_id');
                $table->unsignedInteger('his_sol_id');
                $table->smallInteger('his_est_anterior')->nullable();
                $table->smallInteger('his_est_nuevo');
                $table->unsignedInteger('his_usr_id');
                $table->string('his_comentario', 1000)->nullable();
                $table->timestamp('his_fecha');
            });
        }

        // ── Comentarios ─────────────────────────────────────────────────────
        if (! Schema::hasTable('COMENTARIOS')) {
            Schema::create('COMENTARIOS', function (Blueprint $table) {
                $table->increments('com_id');
                $table->unsignedInteger('com_sol_id');
                $table->unsignedInteger('com_usr_id');
                $table->string('com_texto', 2000);
                $table->smallInteger('com_interno')->default(0); // 0=público 1=interno
                $table->timestamp('com_fecha');
            });
        }

        // ── Adjuntos ────────────────────────────────────────────────────────
        if (! Schema::hasTable('ADJUNTOS')) {
            Schema::create('ADJUNTOS', function (Blueprint $table) {
                $table->increments('adj_id');
                $table->unsignedInteger('adj_sol_id');
                $table->unsignedInteger('adj_usr_id');
                $table->string('adj_nombre_orig', 255);
                $table->string('adj_nombre_disk', 255);
                $table->string('adj_mime_type', 100);
                $table->integer('adj_tamano_kb');
                $table->timestamp('adj_fecha');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ADJUNTOS');
        Schema::dropIfExists('COMENTARIOS');
        Schema::dropIfExists('HISTORIAL_ESTADOS');
        Schema::dropIfExists('SOLICITUDES');
        Schema::dropIfExists('AREAS_DEST');
        Schema::dropIfExists('ESTADOS_SOL');
        Schema::dropIfExists('TIPOS_SOL');
    }
};
