<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login · {{ setting('site_name', 'Navanari') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans flex items-center justify-center p-4">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-rose-100 via-cream to-rose-50"></div>
    <div class="pointer-events-none absolute -top-20 -right-10 h-72 w-72 rounded-full bg-rose-300/30 blur-3xl animate-float"></div>
    <div class="pointer-events-none absolute -bottom-20 -left-10 h-72 w-72 rounded-full bg-gold/20 blur-3xl"></div>

    <div class="w-full max-w-md animate-scale-in">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="text-4xl font-serif font-bold text-gradient">{{ setting('site_name', 'Navanari') }}</a>
            <p class="mt-2 text-ink/50 text-sm">Admin Panel · please sign in</p>
        </div>

        <div class="card p-8">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="input">
                </div>
                <div>
                    <label class="label">Password</label>
                    <input type="password" name="password" required autocomplete="current-password" class="input">
                </div>
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-ink/60">
                        <input type="checkbox" name="remember" class="rounded border-rose-300 text-rose-600 focus:ring-rose-300">
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-rose-600 hover:underline">Forgot password?</a>
                    @endif
                </div>
                <button class="btn-primary w-full">Sign In</button>
            </form>
        </div>

        <p class="text-center mt-6 text-sm text-ink/40">
            <a href="{{ route('home') }}" class="hover:text-rose-600">← Back to store</a>
        </p>
    </div>
</body>
</html>
