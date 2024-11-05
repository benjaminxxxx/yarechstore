<?php

namespace App\Livewire;

use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoginProveedor extends Component
{
    public $email;
    public $password;
    public $remember_me;
    public function render()
    {
        return view('livewire.login-proveedor');
    }
    public function iniciarSesion(){

        $role = Role::where('name', 'Proveedor')->first();

        if (!$role) {
            // Si no existe el rol, mostrar un mensaje de error
            session()->flash('error', 'El rol "Proveedor" no existe. Contacte al administrador.');
            return;
        }

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
            'role_id' => $role->id,  // Verificar que el usuario tenga el rol de Proveedor
        ];

        if (Auth::attempt($credentials, $this->remember_me)) {
            // Si la autenticación es exitosa, redirigir al dashboard
            return redirect()->route('dashboard');
        } else {
            // Si la autenticación falla, mostrar un mensaje de error
            session()->flash('error', 'Credenciales incorrectas o el usuario no tiene el rol de Proveedor.');
        }
    }
}
