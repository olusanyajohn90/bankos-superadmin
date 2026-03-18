<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::guard('superadmin')->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        $admin = Auth::guard('superadmin')->user();
        if (!$admin->is_active) {
            Auth::guard('superadmin')->logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has been disabled.',
            ]);
        }

        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
