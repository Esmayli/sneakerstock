<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $data = Validator::make([
            'name' => env('ADMIN_NAME', 'Administrator'),
            'email' => env('ADMIN_EMAIL'),
            'password' => env('ADMIN_PASSWORD'),
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', Password::default()],
        ])->validate();

        $this->call(RoleSeeder::class);

        $user = User::updateOrCreate(
            ['email' => Str::lower(trim($data['email']))],
            [
                'name' => trim($data['name']),
                'password' => $data['password'],
            ],
        );

        $user->syncRoles(['admin']);
        $this->command?->info('Administrator account is ready.');
    }
}
