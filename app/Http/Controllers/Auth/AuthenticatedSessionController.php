<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = \App\Models\User::where('email', $request->email)->first();
        $Org = \App\Models\Organizacao::find($user->id_org);
        // Verifica se o STAcesso é 0
        if ($user && $user->STAcesso == 0) {
            return redirect()->back()->withErrors([
                'STAcesso' => 'Seu acesso está bloqueado. Entre em contato com o administrador.',
            ]);
        }
        // Verifica se o contrato esta ativado;
        if ($Org->STContrato == 0) {
            return redirect()->back()->withErrors([
                'STAcesso' => 'O Acesso do município está bloqueado',
            ]);
        }

        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
