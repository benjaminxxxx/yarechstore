<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SupplierMiddleware
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
        
        if($user->role_id != $role_id){
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
