<x-layouts.app :title="'Verify your email'">
    <div class="auth-card">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                <i class="fa-solid fa-envelope-circle-check text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-brand-900">Verify your email</h1>
            <p class="mt-1 text-sm text-brand-700/70">
                Enter the 6-digit code we sent to<br>
                <span class="font-semibold text-brand-900">{{ $email }}</span>
            </p>
        </div>

        <form method="POST" action="{{ route('otp.verify.store') }}" class="space-y-6" data-loading-text="Verifying...">
            @csrf
            <input type="hidden" name="code" id="otp-hidden-code">

            <div class="flex justify-center gap-2 sm:gap-3">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" inputmode="numeric" maxlength="1" class="otp-input" autocomplete="one-time-code">
                @endfor
            </div>

            @error('code')
                <p class="text-center text-sm text-red-500">{{ $message }}</p>
            @enderror

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-circle-check"></i> Verify code
            </button>
        </form>

        <form method="POST" action="{{ route('otp.resend') }}" class="mt-4">
            @csrf
            <button
                type="submit"
                id="resend-btn"
                data-cooldown-until="{{ now()->addSeconds($cooldownSeconds)->getTimestampMs() }}"
                class="btn-secondary"
            >
                Resend code
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-brand-700/60">
            <i class="fa-regular fa-clock"></i> Code expires
            {{ $expiresAt ? $expiresAt->diffForHumans() : 'soon' }}.
        </p>
    </div>
</x-layouts.app>
