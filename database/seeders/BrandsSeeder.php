<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Sodimac', 'description' => 'Líder en el mercado de ferretería en Perú.'],
            ['name' => 'Promart', 'description' => 'Ofrecen una amplia variedad de productos para la construcción y remodelación.'],
            ['name' => 'Ferre Total', 'description' => 'Especialistas en ferretería y herramientas.'],
            ['name' => 'Cromantic', 'description' => 'Famosos por sus herramientas y accesorios de alta calidad.'],
            ['name' => 'Truper', 'description' => 'Reconocida por su extensa gama de herramientas y equipos de ferretería.'],
            ['name' => 'Asta', 'description' => 'Proveedores de productos para la construcción y remodelación.'],
            ['name' => 'Bauker', 'description' => 'Marcas destacadas en herramientas eléctricas y manuales.'],
            ['name' => 'Stanley', 'description' => 'Conocida por su durabilidad y confiabilidad en herramientas.'],
            ['name' => 'Bosch', 'description' => 'Innovadores en herramientas eléctricas y equipos de ferretería.'],
            ['name' => 'Makita', 'description' => 'Famosa por sus herramientas eléctricas profesionales.'],
            ['name' => 'PAVCO', 'description' => 'Líder en la fabricación de tubos y conexiones para el sector de la construcción.'],
            ['name' => 'Hilti', 'description' => 'Especialistas en herramientas y equipos para la construcción.'],
            ['name' => 'DeWalt', 'description' => 'Conocida por sus herramientas eléctricas y manuales de alta calidad.'],
            ['name' => 'Milwaukee', 'description' => 'Famosa por su tecnología innovadora en herramientas y equipos.'],
            ['name' => 'Caterpillar', 'description' => 'Reconocida por su equipo de construcción y maquinaria pesada.'],
            ['name' => 'Makita', 'description' => 'Marca japonesa conocida por sus herramientas eléctricas y equipos.'],
            ['name' => 'Bosch', 'description' => 'Marca alemana famosa por sus herramientas eléctricas y equipos de ferretería.'],
            ['name' => 'Hitachi', 'description' => 'Famosa por sus herramientas eléctricas y maquinaria.'],
            ['name' => 'Festool', 'description' => 'Conocida por sus herramientas de alta precisión y durabilidad.'],
            ['name' => 'Ryobi', 'description' => 'Marca japonesa conocida por sus herramientas eléctricas y de jardinería.'],
        ];

        DB::table('brands')->insert($brands);
    }
}
