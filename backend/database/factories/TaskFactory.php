<?php
namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $departments = ['Finance', 'HR', 'IT', 'Operations'];
        $locations = ['Bhubaneswar', 'Cuttack', 'Puri', 'Rourkela', 'Sambalpur'];

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['todo', 'in_progress', 'done']), //
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']), //
            
            // Dynamic rule-based json object
            'assignment_rules' => [
                'department' => fake()->randomElement($departments),
                'min_experience' => fake()->numberBetween(1, 5),
                'location' => fake()->randomElement($locations),
                'max_active_tasks' => fake()->numberBetween(3, 10),
            ],
            
            'assigned_to' => null, // Left null initially so the background queue handles engine assignment
            'created_by' => User::whereIn('role', ['admin', 'manager'])->inRandomOrder()->first()?->id 
                           ?? User::factory()->create(['role' => 'admin'])->id,
            'due_date' => fake()->dateTimeBetween('now', '+1 month'), 
        ];
    }
}