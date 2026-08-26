<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tipos de Cargo
        DB::table('tipos_cargo')->insert([
            ['nombre' => 'ADMINISTRATIVO', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'INSTRUCTOR', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'TRABAJADOR OFICIAL', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Tipos de Vinculación
        DB::table('tipos_vinculacion')->insert([
            ['nombre' => 'CARRERA ADMINISTRATIVA', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'PROVISIONAL', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'LIBRE NOMBRAMIENTO Y REMOCIÓN', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'CONTRATISTA (APOYO)', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Centros de Formación
        DB::table('centros_formacion')->insert([
            ['nombre' => 'CENTRO INDUSTRIAL DE MANTENIMIENTO INTEGRAL (CIMI)', 'municipio' => 'Girón', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'CENTRO DE SERVICIOS EMPRESARIALES Y TURÍSTICOS (CSET)', 'municipio' => 'Bucaramanga', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'CENTRO DE ATENCIÓN AL SECTOR AGROPECUARIO (CASA)', 'municipio' => 'Piedecuesta', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'CENTRO AGROTURÍSTICO', 'municipio' => 'San Gil', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'CENTRO AGROEMPRESARIAL Y TURÍSTICO DE LOS ANDES', 'municipio' => 'Málaga', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'SEDE REGIONAL SANTANDER - DIRECCIÓN', 'municipio' => 'Bucaramanga', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Funcionario de prueba
        DB::table('funcionarios_perfil')->insert([
            'cedula' => '1098765432',
            'tipo_cargo_id' => 1,
            'tipo_vinculacion_id' => 1,
            'centro_formacion_id' => 1,
            'nombres' => 'Funcionario',
            'apellidos' => 'de Prueba',
            'genero' => 'MASCULINO',
            'fecha_nacimiento' => '1990-05-15',
            'email' => 'prueba@sena.edu.co',
            'telefono' => '3001234567',
            'direccion_residencia' => 'Calle 10 # 5-20',
            'eps' => 'Sanitas',
            'fondo_pension' => 'Porvenir',
            'talla_camisa' => 'M',
            'talla_pantalon' => '32',
            'talla_calzado' => '40',
            'es_padre_madre' => false,
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}