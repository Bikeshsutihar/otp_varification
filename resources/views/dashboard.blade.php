<x-layouts.app :title="'Dashboard'">
    <div class="w-full max-w-2xl rounded-2xl bg-white p-8 shadow-2xl shadow-brand-900/10 ring-1 ring-brand-50">
        <div class="mb-6 flex items-center gap-4">
            <div class="grid h-16 w-16 place-items-center rounded-2xl bg-brand-600 text-2xl font-bold text-white">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-brand-900">Welcome, {{ $user->name }} 👋</h1>
                <p class="text-sm text-brand-700/70">{{ $user->email }}</p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-brand-200 bg-brand-50 p-5">
                <i class="fa-solid fa-circle-check mb-2 text-brand-600"></i>
                <p class="text-sm font-semibold text-brand-900">Email verified</p>
                <p class="text-xs text-brand-700/70">
                    Verified {{ $user->email_verified_at?->diffForHumans() }}
                </p>
            </div>
            <div class="rounded-xl border border-brand-200 bg-brand-50 p-5">
                <i class="fa-solid fa-shield-halved mb-2 text-brand-600"></i>
                <p class="text-sm font-semibold text-brand-900">Account secured</p>
                <p class="text-xs text-brand-700/70">Protected by OTP verification</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="btn-secondary">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>
</x-layouts.app>
