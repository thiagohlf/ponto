<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveEmployee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se o usuário está autenticado
        if (Auth::check()) {
            $user = Auth::user();
            
            // Verificar se o usuário tem um funcionário associado e se está inativo
            if ($user->employee && !$user->employee->active) {
                // Fazer logout
                Auth::logout();
                
                // Invalidar a sessão
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirecionar para login com mensagem
                return redirect()->route('login')
                    ->with('error', 'Sua conta foi desativada. Entre em contato com o RH.');
            }
        }

        return $next($request);
    }
}
