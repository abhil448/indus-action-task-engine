<?php
namespace App\Jobs;

use App\Models\Task;
use App\Services\TaskAssignmentEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EvaluateTaskAssignmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5; // Max retries before marking as unassigned
    public int $backoff = 60; // Wait 60 seconds 

    public function __construct(public Task $task) {}

    public function handle(TaskAssignmentEngine $engine): void
    {
        $assigned = $engine->assignTask($this->task);

        if (!$assigned) {
            Log::warning("No eligible user found for Task ID {$this->task->id}. Retry scheduled.");

            // Release the job back to queue with a 2 minute delay
            $this->release(120); 
        }
    }
}