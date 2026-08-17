<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\OtpCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class OtpController extends Controller
{
    /**
     * Show the "enter your code" screen.
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isOtpVerified()) {
            return redirect()->route('dashboard');
        }

        $latest = $user->otpCodes()->where('purpose', 'registration')->latest()->first();

        $throttleKey = 'otp-resend:'.$user->id;
        $remaining = RateLimiter::availableIn($throttleKey);

        return view('auth.verify-otp', [
            'email' => $user->email,
            'expiresAt' => $latest?->expires_at,
            'cooldownSeconds' => RateLimiter::tooManyAttempts($throttleKey, 1) ? $remaining : 0,
        ]);
    }

    /**
     * Verify the submitted OTP code.
     */
    public function verify(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = Auth::user();

        $throttleKey = 'otp-verify:'.$user->id;

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'code' => "Too many attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        RateLimiter::hit($throttleKey, 300);

        $otp = $user->otpCodes()
            ->where('purpose', 'registration')
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $otp || $otp->isExpired()) {
            return back()->withErrors([
                'code' => 'This code has expired. Please request a new one.',
            ]);
        }

        if ($otp->hasExceededAttempts()) {
            return back()->withErrors([
                'code' => 'Too many incorrect attempts. Please request a new code.',
            ]);
        }

        if (! $otp->matches($request->input('code'))) {
            $otp->increment('attempts');

            return back()->withErrors([
                'code' => 'The code you entered is incorrect.',
            ]);
        }

        $otp->forceFill(['verified_at' => now()])->save();
        $user->markOtpVerified();

        RateLimiter::clear($throttleKey);
        $request->session()->forget('otp_user_id');

        return redirect()->route('dashboard')->with('status', 'Your email has been verified. Welcome!');
    }

    /**
     * Resend a fresh OTP code, respecting the resend cooldown.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isOtpVerified()) {
            return redirect()->route('dashboard');
        }

        $throttleKey = 'otp-resend:'.$user->id;

        if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'code' => "Please wait {$seconds} seconds before requesting another code.",
            ]);
        }

        RateLimiter::hit($throttleKey, (int) config('otp.resend_cooldown_seconds'));

        [$otp, $code] = OtpCode::generateFor($user, 'registration', $request->ip());

        Mail::to($user->email)->send(
            new OtpMail($user, $code, (int) config('otp.expires_in_minutes'))
        );

        return back()->with('status', 'A new verification code has been sent to your email.');
    }
}
