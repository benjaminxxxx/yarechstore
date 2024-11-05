<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckBranchSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $role = Role::where('name', 'Proveedor')->first();
        $role_id = 0;
        if ($role) {
            $role_id = $role->id;
        }
        
        if($user->role_id == $role_id){
            return redirect()->route('supplier.dashboard');
        }
        //dd(Session::has('selected_branch'));
        // Verifica si el usuario no es super administrador
        if ($user->role_id != 1) {
          
            $branches = $user->branches();
            if($branches){
                if($branches->count()==1){
                    $branch = $branches->first();
                    Session::put('selected_branch', $branch->code);
                }
            }
            // Verifica si no hay sucursal seleccionada en la sesión y evita redirección en la ruta de selección de sucursal
            if (!Session::has('selected_branch') && !$request->routeIs('select-branch', 'set-branch')) {
                
                return redirect()->route('select-branch');
            }
        }

        return $next($request);
    }
}
