<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User 1',
            'email' => 'user1@example.com',
        ]);

        User::factory()->create([
            'name' => 'Test User 2',
            'email' => 'user2@example.com',
        ]);

        User::factory()->create([
            'name' => 'Test User 3',
            'email' => 'user3@example.com',
        ]);
    }
}
