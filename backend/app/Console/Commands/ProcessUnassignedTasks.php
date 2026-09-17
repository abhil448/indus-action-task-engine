<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Services\TaskAssignmentEngine;

class ProcessUnassignedTasks extends Command
{
    protected $signature = 'tasks:process-unassigned';
    protected $description = 'Periodically evaluate unassigned tasks for eligible user';

    public function handle(TaskAssignmentEngine $engine)
    {
        $unassignedTasks = Task::whereNull('assigned_to')->get();

        foreach ($unassignedTasks as $task) {
            $assigned = $engine->assignTask($task);
            if ($assigned) {
                $this->info("Task ID {$task->id} successfully assigned.");
            }
        }
    }
}