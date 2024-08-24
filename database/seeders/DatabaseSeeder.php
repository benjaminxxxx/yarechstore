<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\UserBranch;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $roles = [
            ['name' => 'Super Administrador'],
            ['name' => 'Administrador'],
            ['name' => 'Gerente de Sucursal'],
            ['name' => 'Cajero'],
            ['name' => 'Almacenero'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
        $superAdminRole = Role::where('name', 'Super Administrador')->first();
    
        User::create([
            'name' => 'Benjamin Unitek',
            'code'=>Str::random(15),
            'email' => 'benjamin_unitek@hotmail.com',
            'password' => bcrypt('12345678'), // Encriptar la contraseña
            'role_id' => $superAdminRole->id
        ]);
        $ventas = User::create([
            'name' => 'Ventas',
            'code'=>Str::random(15),
            'email' => 'ventas@hotmail.com',
            'password' => bcrypt('12345678'), // Encriptar la contraseña
            'role_id' => 3
        ]);

        

        $this->call([
            CompanySeeder::class,
            CategoriesTableSeeder::class,
            UnitsSeeder::class,
            BrandsSeeder::class
        ]);

        UserBranch::create([
            'user_id'=>$ventas->id,
            'branch_id'=>1
        ]);

        $documentTypes = [
            ['code' => '0', 'name' => 'Doc. Trib. No Dom. Sin RUC', 'short_name' => 'No Dom. Sin RUC'],
            ['code' => '1', 'name' => 'Doc. Nacional de Identidad', 'short_name' => 'DNI'],
            ['code' => '4', 'name' => 'Carnet de Extranjería', 'short_name' => 'Carnet Extranjería'],
            ['code' => '6', 'name' => 'Registro Único de Contribuyentes', 'short_name' => 'RUC'],
            ['code' => '7', 'name' => 'Pasaporte', 'short_name' => 'Pasaporte'],
            ['code' => 'A', 'name' => 'Ced. Diplomática de Identidad', 'short_name' => 'Ced. Diplomática'],
            ['code' => 'B', 'name' => 'Doc. Identidad País Residencia-No.D', 'short_name' => 'Doc. País Residencia'],
            ['code' => 'C', 'name' => 'Tax Identification Number - TIN – Doc Trib PP.NN', 'short_name' => 'TIN'],
            ['code' => 'D', 'name' => 'Identification Number - IN – Doc Trib PP. JJ', 'short_name' => 'IN'],
            ['code' => 'E', 'name' => 'TAM - Tarjeta Andina de Migración', 'short_name' => 'TAM'],
        ];
    
        foreach ($documentTypes as $type) {
            \DB::table('document_sunat_types')->insert($type);
        }
    }
}
