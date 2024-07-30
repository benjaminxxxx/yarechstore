<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            'name' => 'INVERSIONES YARECH S.R.L.',
            'ruc' => '20611263300',
            'address' => 'venta al por menor de articulo de ferreteria',
            'logo' => '',
            'sol_user' => '',
            'sol_pass' => '',
            'cert_path' => '',
            'client_id' => '',
            'client_secret' => '',
            'production' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $companyId = 1; // Ajusta este valor al ID de la empresa que deseas usar

        DB::table('branches')->insert([
            'code' => Str::random(15), // Puedes ajustar este valor según tus necesidades
            'company_id' => $companyId,
            'name' => 'Tienda Principal',
            'address' => 'La Aguadita',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
