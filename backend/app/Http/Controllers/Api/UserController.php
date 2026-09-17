<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\RecomputeUserTasksJob;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'department' => 'sometimes|string',
            'experience' => 'sometimes|integer',
            'location' => 'sometimes|string',
        ]);

        $user->update($validated);

        // Story 3: Trigger async task engine recalculation when user profile updates
        RecomputeUserTasksJob::dispatch($user);

        return response()->json(['message' => 'Profile updated & evaluation queued.', 'user' => $user]);
    }
}