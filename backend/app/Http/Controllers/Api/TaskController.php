<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Jobs\EvaluateTaskAssignmentJob;
use App\Services\TaskAssignmentEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{

     /**
     * Helper check to enforce Admin role.
     */
    private function checkAdmin(Request $request)
    {
     if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Only administrators can create, update, or delete tasks.'
            ], 403);
        }

        return null;
    }

 public function index(Request $request)
    {
        $user = $request->user();

        
        $query = Task::with('assignee');

        if ($user->role !== 'admin') {
            $query->where('assigned_to', $user->id);
        }

        return response()->json($query->paginate(50));
    }

    public function store(Request $request)
    {
        if ($forbidden = $this->checkAdmin($request)) {
            return $forbidden;
        }
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'assignment_rules' => 'required|array',
            'due_date' => 'nullable|date'
        ]);

        $task = Task::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        // Push task evaluation to background Redis Queue
        EvaluateTaskAssignmentJob::dispatch($task);

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        return response()->json($task->load('assignee'));
    }

    public function update(Request $request, Task $task)
    {
       if ($forbidden = $this->checkAdmin($request)) {
            return $forbidden;
        }
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'assignment_rules' => 'sometimes|array',
            'status' => 'sometimes|in:todo,in_progress,done'
        ]);

        $rulesUpdated = isset($validated['assignment_rules']);
        $task->update($validated);

        if ($rulesUpdated) {
            EvaluateTaskAssignmentJob::dispatch($task); // Recomputes on rule change
        }

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        if ($forbidden = $this->checkAdmin($request)) {
            return $forbidden;
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }

    // Engine Endpoint: GET /tasks/{id}/eligible-users
    public function eligibleUsers(Task $task, TaskAssignmentEngine $engine)
    {
        $users = $engine->getEligibleUsersQuery($task->assignment_rules)->get();
        return response()->json($users);
    }

    // High Performance Endpoint: GET /my-eligible-tasks (<200ms caching)
    public function myEligibleTasks(Request $request)
    {
        $userId = $request->user()->id;
    
        $tasks = Cache::remember("user_tasks_{$userId}", 300, function () use ($userId) {
            return Task::where('assigned_to', $userId)->get();
        });

        return response()->json($tasks);
    }
}