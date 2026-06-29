<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Default admin (local development convenience).
        User::updateOrCreate(
            ['email' => 'admin@navanari.test'],
            [
                'name' => 'Navanari Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ],
        );

        $this->call([
            SettingsSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
