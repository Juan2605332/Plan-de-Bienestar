<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('activo')->default(true);
            $table->string('profile_photo_path')->nullable();
            $table->string('profile_color', 16)->default('#1d6b3d');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['activo', 'profile_photo_path', 'profile_color']);
        });
    }
};
