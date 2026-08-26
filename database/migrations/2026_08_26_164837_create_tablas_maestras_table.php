<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('centros_formacion')) {
            Schema::create('centros_formacion', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 150)->unique();
                $table->string('municipio', 100);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tipos_cargo')) {
            Schema::create('tipos_cargo', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100)->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tipos_vinculacion')) {
            Schema::create('tipos_vinculacion', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100)->unique();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_vinculacion');
        Schema::dropIfExists('tipos_cargo');
        Schema::dropIfExists('centros_formacion');
    }
};
