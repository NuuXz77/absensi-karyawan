<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleBasedUrlRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $currentPath = $request->path();
            
            // Jika user mengakses root URL "/"
            if ($currentPath === '/') {
                if ($user->role === 'admin') {
                    return redirect('/admin/dashboard');
                } elseif ($user->role === 'karyawan') {
                    return redirect('/dashboard');
                }
            }
            
            // Jika admin mengakses URL tanpa /admin prefix
            if ($user->role === 'admin' && !str_starts_with($currentPath, 'admin/')) {
                // Kecuali untuk login, logout, dan asset
                if (!in_array($currentPath, ['login', 'logout']) && !str_starts_with($currentPath, 'livewire/')) {
                    return redirect('/admin/' . $currentPath);
                }
            }
            
            // Jika karyawan mengakses URL dengan /admin prefix
            if ($user->role === 'karyawan' && str_starts_with($currentPath, 'admin/')) {
                // Redirect ke URL tanpa /admin
                $newPath = substr($currentPath, 6); // Hapus 'admin/'
                return redirect('/' . $newPath);
            }
        }
        
        return $next($request);
    }
}
