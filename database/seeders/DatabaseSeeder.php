<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
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

        $this->call([
            CompanySeeder::class,
            CategoriesTableSeeder::class,
            UnitsSeeder::class,
            BrandsSeeder::class
        ]);
    }
}
