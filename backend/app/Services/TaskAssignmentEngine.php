<?php
namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TaskAssignmentEngine 
{
    public function getEligibleUsersQuery(array $rules)
    {
        $query = User::query()->where('role', 'user');

        if (!empty($rules['department'])) {
            $query->where('department', $rules['department']);
        }
        if (isset($rules['min_experience'])) {
            $query->where('experience', '>=', $rules['min_experience']);
        }
        if (!empty($rules['location'])) {
            $query->where('location', $rules['location']);
        }
        if (isset($rules['max_active_tasks'])) {
            $query->where('active_tasks_count', '<', $rules['max_active_tasks']);
        }

        return $query;
    }

    public function assignTask(Task $task): bool
    {
        $rules = $task->assignment_rules ?? [];
        
      
        $candidate = $this->getEligibleUsersQuery($rules)
            ->orderBy('active_tasks_count', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        if (!$candidate) {
            return false; 
        }

        DB::transaction(function () use ($task, $candidate) {
            if ($task->assigned_to && $task->assigned_to !== $candidate->id) {
                User::where('id', $task->assigned_to)->decrement('active_tasks_count');
            }

            $task->update(['assigned_to' => $candidate->id]);
            User::where('id', $candidate->id)->increment('active_tasks_count');
            
           
            Cache::forget("user_tasks_{$candidate->id}");
        });

        return true;
    }
}