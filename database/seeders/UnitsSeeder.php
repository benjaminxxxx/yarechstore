<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Unidad'],
            ['name' => 'Paquete'],
            ['name' => 'Caja'],
            ['name' => 'Lata'],
            ['name' => 'Botella'],
            ['name' => 'Metro'],
            ['name' => 'Kilogramo'],
            ['name' => 'Litro'],
            ['name' => 'Caja de 12'],
            ['name' => 'Rollo'],
            ['name' => 'Juego'],
            ['name' => 'Pieza'],
            ['name' => 'Bote'],
            ['name' => 'Pallet'],
            ['name' => 'Set'],
            ['name' => 'Bolsa'],
        ];

        DB::table('units')->insert($units);
    }
}
