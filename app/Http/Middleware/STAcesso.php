<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class STAcesso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Obtém o usuário logado
            $user = Auth::user();

            // Se o STAcesso for 0, desloga o usuário e redireciona com mensagem de erro
            if ($user->STAcesso == 0) {
                Auth::logout();

                // Invalida e regenera a sessão para segurança
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'STAcesso' => 'Seu acesso foi bloqueado. Entre em contato com o administrador.',
                ]);
            }
        }
        
        return $next($request);
    }
}
