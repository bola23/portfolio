<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates a local administrator from ADMIN_EMAIL / ADMIN_PASSWORD.
 * Skipped in production and when credentials are not configured.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $credentials = config('portfolio.seed_admin');

        if (app()->isProduction() || blank($credentials['email']) || blank($credentials['password'])) {
            $this->command?->warn('Admin user not seeded. Run `php artisan portfolio:make-admin` to create one.');

            return;
        }

        $user = User::query()->firstOrNew(['email' => $credentials['email']]);
        $user->fill(['name' => $credentials['name'], 'password' => $credentials['password']]);
        $user->forceFill(['is_admin' => true, 'email_verified_at' => now()])->save();
    }
}
