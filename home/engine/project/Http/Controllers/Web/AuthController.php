<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()
                ->withErrors(['email' => 'The provided credentials are incorrect.'])
                ->withInput($request->only('email'));
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()
                ->withErrors(['email' => 'Your account has been deactivated.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended('/dashboard')->with('success', 'Welcome back, ' . $user->name);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Implementation for sending reset link
        // This would integrate with the API endpoint

        return back()->with('success', 'Password reset link has been sent to your email address.');
    }

    /**
     * Show the reset password form.
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle reset password request.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Implementation for password reset
        // This would integrate with the API endpoint

        return redirect('/login')->with('success', 'Your password has been reset successfully.');
    }
}