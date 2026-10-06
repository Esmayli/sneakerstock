<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        if (filled(env('ADMIN_EMAIL')) && filled(env('ADMIN_PASSWORD'))) {
            $this->call(AdminUserSeeder::class);
        } else {
            $this->call(RoleSeeder::class);
        }

        Category::factory(5)->create();
        User::factory(9)->create();
        $this->call(SneakerInventorySeeder::class);
    }
}
