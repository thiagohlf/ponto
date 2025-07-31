<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRegistrationEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se o registro está habilitado
        if (!config('app.registration_enabled', true)) {
            // Se não estiver habilitado, redirecionar para login com mensagem
            return redirect()->route('login')->with('error', 'O registro de novos usuários está temporariamente desabilitado. Entre em contato com o administrador.');
        }

        return $next($request);
    }
}