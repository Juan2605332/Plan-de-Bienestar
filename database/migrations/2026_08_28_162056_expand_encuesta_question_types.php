<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('encuesta_preguntas', function (Blueprint $table) {
            $table->string('tipo_pregunta', 40)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encuesta_preguntas', function (Blueprint $table) {
            $table->string('tipo_pregunta', 20)->change();
        });
    }
};
