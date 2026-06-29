<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install · Navanari</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans py-10 px-4">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-rose-100 via-cream to-rose-50"></div>
    <div class="pointer-events-none absolute -top-20 -right-10 h-72 w-72 rounded-full bg-rose-300/30 blur-3xl animate-float"></div>

    <div class="mx-auto max-w-2xl animate-scale-in">
        <div class="text-center mb-8">
            <span class="text-4xl font-serif font-bold text-gradient">Navanari</span>
            <p class="mt-2 text-ink/50">Welcome! Let's get your store set up — this takes about a minute.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 px-5 py-4 text-sm text-rose-700 ring-1 ring-rose-100">
                @foreach($errors->all() as $error)<p>⚠️ {{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('install') }}" class="space-y-6">
            @csrf

            <div class="card p-6 space-y-5">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-600 text-white text-sm font-bold">1</span>
                    <h2 class="font-semibold text-ink">Database connection</h2>
                </div>
                <p class="text-sm text-ink/50 -mt-2">From hPanel → MySQL Databases. These may already be filled in for you.</p>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Database Host</label>
                        <input type="text" name="db_host" value="{{ old('db_host', $defaults['db_host']) }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Port</label>
                        <input type="text" name="db_port" value="{{ old('db_port', $defaults['db_port']) }}" class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Database Name</label>
                        <input type="text" name="db_database" value="{{ old('db_database', $defaults['db_database']) }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Database Username</label>
                        <input type="text" name="db_username" value="{{ old('db_username', $defaults['db_username']) }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Database Password</label>
                        <input type="text" name="db_password" value="{{ old('db_password', $defaults['db_password']) }}" class="input">
                    </div>
                </div>
            </div>

            <div class="card p-6 space-y-5">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-600 text-white text-sm font-bold">2</span>
                    <h2 class="font-semibold text-ink">Your admin account</h2>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="label">Your Name</label>
                        <input type="text" name="admin_name" value="{{ old('admin_name', 'Navanari Admin') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Admin Email</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="you@email.com" required class="input">
                    </div>
                    <div>
                        <label class="label">Admin Password</label>
                        <input type="text" name="admin_password" value="{{ old('admin_password') }}" placeholder="choose a password" required class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">WhatsApp Number <span class="text-ink/40 text-xs">(intl format, no +)</span></label>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" placeholder="91XXXXXXXXXX" class="input">
                    </div>
                </div>
                <label class="flex items-center gap-2.5 text-sm">
                    <input type="checkbox" name="with_demo" value="1" {{ old('with_demo', true) ? 'checked' : '' }} class="rounded border-rose-300 text-rose-600 focus:ring-rose-300">
                    Load demo products & categories (recommended — you can delete them later)
                </label>
            </div>

            <button class="btn-primary w-full !py-4 text-base shadow-glow"><x-icon name="sparkles" class="h-5 w-5" /> Install Navanari</button>
        </form>

        <p class="text-center mt-5 text-xs text-ink/40">This installer disables itself automatically once setup is complete.</p>
    </div>
</body>
</html>
