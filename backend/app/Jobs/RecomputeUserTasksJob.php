<?php
namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskAssignmentEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class RecomputeUserTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(public User $user) {}

    public function handle(TaskAssignmentEngine $engine): void
    {
        
        $unassignedTasks = Task::whereNull('assigned_to')->get();

        foreach ($unassignedTasks as $task) {
            $engine->assignTask($task);
        }
    }
}