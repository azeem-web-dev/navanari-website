<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdmin extends Command
{
    protected $signature = 'navanari:make-admin
                            {email : Admin email address}
                            {--name=Administrator : Display name}
                            {--password= : Password (will prompt if omitted)}';

    protected $description = 'Create or promote a Navanari admin user';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->option('password') ?: $this->secret('Choose a password');

        if (! $password) {
            $this->error('A password is required.');
            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $this->option('name'),
                'password' => Hash::make($password),
                'is_admin' => true,
            ],
        );

        $this->info("Admin ready: {$user->email}");
        $this->line('Sign in at: '.url('/login'));

        return self::SUCCESS;
    }
}
