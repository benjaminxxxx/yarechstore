<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Categorías principales
         $categories = [
            ['name' => 'Herramientas', 'parent_id' => null],
            ['name' => 'Materiales de Construcción', 'parent_id' => null],
            ['name' => 'Plomería', 'parent_id' => null],
            ['name' => 'Electricidad', 'parent_id' => null],
            ['name' => 'Ferretería General', 'parent_id' => null],
            ['name' => 'Pinturas y Barnices', 'parent_id' => null],
            ['name' => 'Jardinería', 'parent_id' => null],
            ['name' => 'Seguridad y Protección', 'parent_id' => null],
            ['name' => 'Cierres y Cerrajería', 'parent_id' => null],
            ['name' => 'Accesorios para Autos', 'parent_id' => null],
            ['name' => 'Equipos de Protección Personal', 'parent_id' => null],
            ['name' => 'Reparaciones y Mantenimiento', 'parent_id' => null],
            ['name' => 'Construcción de Interiores', 'parent_id' => null],
            ['name' => 'Suministros de Oficina', 'parent_id' => null],
            ['name' => 'Herramientas Especializadas', 'parent_id' => null],
        ];

        // Insertar categorías principales
        foreach ($categories as $category) {
            $categoryId = DB::table('categories')->insertGetId($category);

            // Agregar subcategorías para cada categoría principal
            switch ($category['name']) {
                case 'Herramientas':
                    DB::table('categories')->insert([
                        ['name' => 'Herramientas Manuales', 'parent_id' => $categoryId],
                        ['name' => 'Herramientas Eléctricas', 'parent_id' => $categoryId],
                        ['name' => 'Herramientas Neumáticas', 'parent_id' => $categoryId],
                        ['name' => 'Herramientas de Medición', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Materiales de Construcción':
                    DB::table('categories')->insert([
                        ['name' => 'Cemento', 'parent_id' => $categoryId],
                        ['name' => 'Ladrillos', 'parent_id' => $categoryId],
                        ['name' => 'Madera', 'parent_id' => $categoryId],
                        ['name' => 'Acero', 'parent_id' => $categoryId],
                        ['name' => 'Placas de Yeso', 'parent_id' => $categoryId],
                        ['name' => 'Aislamiento Térmico', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Plomería':
                    DB::table('categories')->insert([
                        ['name' => 'Tuberías', 'parent_id' => $categoryId],
                        ['name' => 'Accesorios de Plomería', 'parent_id' => $categoryId],
                        ['name' => 'Grifería', 'parent_id' => $categoryId],
                        ['name' => 'Sanitarios', 'parent_id' => $categoryId],
                        ['name' => 'Bombas de Agua', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Electricidad':
                    DB::table('categories')->insert([
                        ['name' => 'Cableado Eléctrico', 'parent_id' => $categoryId],
                        ['name' => 'Interruptores y Enchufes', 'parent_id' => $categoryId],
                        ['name' => 'Luces y Lámparas', 'parent_id' => $categoryId],
                        ['name' => 'Equipos de Protección', 'parent_id' => $categoryId],
                        ['name' => 'Generadores', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Ferretería General':
                    DB::table('categories')->insert([
                        ['name' => 'Tornillos y Clavos', 'parent_id' => $categoryId],
                        ['name' => 'Adhesivos y Selladores', 'parent_id' => $categoryId],
                        ['name' => 'Accesorios de Fijación', 'parent_id' => $categoryId],
                        ['name' => 'Cuerdas y Cadenas', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Pinturas y Barnices':
                    DB::table('categories')->insert([
                        ['name' => 'Pinturas para Interiores', 'parent_id' => $categoryId],
                        ['name' => 'Pinturas para Exteriores', 'parent_id' => $categoryId],
                        ['name' => 'Barnices y Selladores', 'parent_id' => $categoryId],
                        ['name' => 'Brochas y Rodillos', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Jardinería':
                    DB::table('categories')->insert([
                        ['name' => 'Herramientas de Jardinería', 'parent_id' => $categoryId],
                        ['name' => 'Fertilizantes y Suelos', 'parent_id' => $categoryId],
                        ['name' => 'Macetas y Contenedores', 'parent_id' => $categoryId],
                        ['name' => 'Accesorios de Riego', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Seguridad y Protección':
                    DB::table('categories')->insert([
                        ['name' => 'Cerraduras y Candados', 'parent_id' => $categoryId],
                        ['name' => 'Sistemas de Alarma', 'parent_id' => $categoryId],
                        ['name' => 'Cámaras de Seguridad', 'parent_id' => $categoryId],
                        ['name' => 'Equipos de Protección Personal (EPP)', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Cierres y Cerrajería':
                    DB::table('categories')->insert([
                        ['name' => 'Cerraduras de Puertas', 'parent_id' => $categoryId],
                        ['name' => 'Accesorios de Cerrajería', 'parent_id' => $categoryId],
                        ['name' => 'Llaves y Controladores', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Accesorios para Autos':
                    DB::table('categories')->insert([
                        ['name' => 'Herramientas para Autos', 'parent_id' => $categoryId],
                        ['name' => 'Accesorios de Mantenimiento', 'parent_id' => $categoryId],
                        ['name' => 'Equipos de Seguridad para Autos', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Equipos de Protección Personal':
                    DB::table('categories')->insert([
                        ['name' => 'Casco', 'parent_id' => $categoryId],
                        ['name' => 'Guantes', 'parent_id' => $categoryId],
                        ['name' => 'Máscaras de Protección', 'parent_id' => $categoryId],
                        ['name' => 'Ropa de Trabajo', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Reparaciones y Mantenimiento':
                    DB::table('categories')->insert([
                        ['name' => 'Productos de Mantenimiento', 'parent_id' => $categoryId],
                        ['name' => 'Kit de Reparación', 'parent_id' => $categoryId],
                        ['name' => 'Lubricantes', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Construcción de Interiores':
                    DB::table('categories')->insert([
                        ['name' => 'Revestimientos de Paredes', 'parent_id' => $categoryId],
                        ['name' => 'Pisos y Alfombras', 'parent_id' => $categoryId],
                        ['name' => 'Accesorios de Decoración', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Suministros de Oficina':
                    DB::table('categories')->insert([
                        ['name' => 'Materiales de Oficina', 'parent_id' => $categoryId],
                        ['name' => 'Equipos de Oficina', 'parent_id' => $categoryId],
                    ]);
                    break;

                case 'Herramientas Especializadas':
                    DB::table('categories')->insert([
                        ['name' => 'Herramientas para Soldadura', 'parent_id' => $categoryId],
                        ['name' => 'Herramientas de Medición Especializadas', 'parent_id' => $categoryId],
                    ]);
                    break;
            }
        }
    }
}
