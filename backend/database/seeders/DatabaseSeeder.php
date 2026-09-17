<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Default Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@indusaction.org',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'department' => 'IT',
            'experience' => 10,
            'location' => 'Bhubaneswar'
        ]);

        // Sample Workers
        User::factory()->count(100)->create();

        // Sample Tasks
        Task::factory()->count(5)->create();
    }
}