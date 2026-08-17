<nav class="border-b border-brand-200/60 bg-white/70 backdrop-blur">
    <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
        <a href="{{ route('login') }}" class="flex items-center gap-2 text-brand-700">
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-600 text-white">
                <i class="fa-solid fa-shield-halved"></i>
            </span>
            <span class="text-lg font-bold text-brand-900">{{ config('app.name', 'OTP Auth') }}</span>
        </a>

        <div class="flex items-center gap-3 text-sm">
            @auth
                @if (auth()->user()->isOtpVerified())
                    <a href="{{ route('dashboard') }}" class="font-medium text-brand-700 hover:text-brand-600">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-secondary !w-auto px-4 py-2">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </button>
                    </form>
                @else
                    <span class="text-brand-700">{{ auth()->user()->email }}</span>
                @endif
            @else
                <a href="{{ route('login') }}" class="font-medium text-brand-700 hover:text-brand-600">Login</a>
                <a href="{{ route('register') }}" class="btn-primary !w-auto px-4 py-2">
                    <i class="fa-solid fa-user-plus"></i> Sign up
                </a>
            @endauth
        </div>
    </div>
</nav>
