<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        [$otp, $code] = OtpCode::generateFor($user, 'registration', $request->ip());

        Mail::to($user->email)->send(
            new OtpMail($user, $code, (int) config('otp.expires_in_minutes'))
        );

        // Log the user in but keep them gated by the "otp.verified" middleware
        // until they successfully verify the code.
        Auth::login($user);

        $request->session()->put('otp_user_id', $user->id);

        return redirect()
            ->route('otp.verify')
            ->with('status', 'We\'ve sent a 6-digit verification code to '.$user->email);
    }
}
