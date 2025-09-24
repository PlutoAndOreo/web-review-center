<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
        $credentials = $request->validated();
       
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $admin = Auth::guard('admin')->user();

            if ($admin->role === 'super_admin') {
                return redirect()->intended(route('dashboard'));

            }    
        }
        throw ValidationException::withMessages([
            'email' => __('The provided credentials are incorrect.'),
        ]);
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
