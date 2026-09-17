<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $departments = ['Finance', 'HR', 'IT', 'Operations'];
        $locations = ['Bhubaneswar', 'Cuttack', 'Puri', 'Rourkela', 'Sambalpur'];

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            // 'email_verified_at' => now(), <-- REMOVE OR COMMENT THIS OUT
            // 'remember_token' => Str::random(10), <-- REMOVE OR COMMENT THIS OUT
            'role' => 'user',
            'department' => fake()->randomElement($departments),
            'experience' => fake()->numberBetween(1, 15),
            'location' => fake()->randomElement($locations),
            'active_tasks_count' => 0,
        ];
    }
}