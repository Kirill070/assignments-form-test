<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'first_name' => 'Ivan',
            'last_name' => 'Petrov',
            'email' => 'ivan.petrov@example.test',
        ]);

        User::factory()->create([
            'first_name' => 'Maria',
            'last_name' => 'Ivanova',
            'email' => 'maria.ivanova@example.test',
        ]);

        User::factory()->create([
            'first_name' => 'Sergey',
            'last_name' => 'Smirnov',
            'email' => 'sergey.smirnov@example.test',
        ]);
    }
}
