<x-layouts.app :title="'Sign in'">
    <div class="auth-card">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                <i class="fa-solid fa-right-to-bracket text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-brand-900">Welcome back</h1>
            <p class="mt-1 text-sm text-brand-700/70">Sign in to continue to your account.</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5" data-loading-text="Signing in...">
            @csrf

            <div>
                <label for="email" class="form-label">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       class="form-input @error('email') !border-red-400 @enderror" placeholder="you@example.com">
                @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" required
                       class="form-input @error('password') !border-red-400 @enderror" placeholder="Your password">
                @error('password') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-brand-700">
                <input type="checkbox" name="remember" class="rounded border-brand-200 text-brand-600 focus:ring-brand-200">
                Remember me
            </label>

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-right-to-bracket"></i> Sign in
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-brand-700/80">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline">Create one</a>
        </p>
    </div>
</x-layouts.app>
