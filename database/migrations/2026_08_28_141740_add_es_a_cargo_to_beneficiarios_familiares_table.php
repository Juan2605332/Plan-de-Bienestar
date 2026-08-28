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
        Schema::table('beneficiarios_familiares', function (Blueprint $table) {
            $table->boolean('es_a_cargo')->default(false)->after('genero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiarios_familiares', function (Blueprint $table) {
            $table->dropColumn('es_a_cargo');
        });
    }
};
