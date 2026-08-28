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
        Schema::table('eventos', function (Blueprint $table) {
            $table->unsignedTinyInteger('edad_minima')->nullable()->after('requiere_ser_padre_madre');
            $table->unsignedTinyInteger('edad_maxima')->nullable()->after('edad_minima');
            $table->boolean('requiere_familiar_a_cargo')->default(false)->after('edad_maxima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['edad_minima', 'edad_maxima', 'requiere_familiar_a_cargo']);
        });
    }
};
