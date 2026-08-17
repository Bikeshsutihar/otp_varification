<x-layouts.app :title="'Create account'">
    <div class="auth-card">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                <i class="fa-solid fa-user-plus text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-brand-900">Create your account</h1>
            <p class="mt-1 text-sm text-brand-700/70">We'll email you a verification code to confirm it's you.</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="space-y-5" data-loading-text="Creating account...">
            @csrf

            <div>
                <label for="name" class="form-label">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                       class="form-input @error('name') !border-red-400 @enderror" placeholder="Jane Doe">
                @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="form-label">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                       class="form-input @error('email') !border-red-400 @enderror" placeholder="you@example.com">
                @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" required
                       class="form-input @error('password') !border-red-400 @enderror" placeholder="At least 8 characters">
                @error('password') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="form-label">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="form-input" placeholder="Re-enter your password">
            </div>

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-paper-plane"></i> Create account &amp; send code
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-brand-700/80">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Sign in</a>
        </p>
    </div>
</x-layouts.app>
