<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin do painel a partir do ambiente (local: .env; produção: vault).
        // Sem as variáveis, não cria nada — use php artisan make:filament-user.
        if (env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
            User::firstOrCreate(
                ['email' => env('ADMIN_EMAIL')],
                [
                    'name' => env('ADMIN_NAME', 'Admin'),
                    'password' => Hash::make(env('ADMIN_PASSWORD')),
                ],
            );
        }
    }
}
