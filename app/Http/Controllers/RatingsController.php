<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskRating;
use App\Models\ClientRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingsController extends AppBaseController
{
    /**
     * Display a comprehensive ratings page
     */
    public function index(Request $request)
    {
        // Get tasks with both ratings
        $query = Task::with([
            'project',
            'taskAssignee',
            'rating.ratedBy',
            'clientRatings.client',
            'clientRatings.ratedBy'
        ]);

        // Apply filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('has_task_rating')) {
            if ($request->has_task_rating == '1') {
                $query->whereHas('rating');
            } else {
                $query->whereDoesntHave('rating');
            }
        }

        if ($request->filled('has_client_rating')) {
            if ($request->has_client_rating == '1') {
                $query->whereHas('clientRatings');
            } else {
                $query->whereDoesntHave('clientRatings');
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get filter data
        $projects = \App\Models\Project::orderBy('name')->get();
        $statuses = Task::$statusArr;

        // Get statistics
        $stats = [
            'total_tasks' => Task::count(),
            'tasks_with_ratings' => Task::whereHas('rating')->count(),
            'tasks_with_client_ratings' => Task::whereHas('clientRatings')->count(),
            'tasks_with_both_ratings' => Task::whereHas('rating')->whereHas('clientRatings')->count(),
            'avg_task_rating' => TaskRating::avg(DB::raw('(code_quality + delivery_output + time_score + collaboration + complexity_urgency) / 5')),
            'avg_client_rating' => ClientRating::avg('rating')
        ];

        return view('ratings.index', compact('tasks', 'projects', 'statuses', 'stats'));
    }

    /**
     * Get rating status for a task (AJAX)
     */
    public function getTaskRatingStatus(Task $task)
    {
        $hasTaskRating = $task->rating()->exists();
        $hasClientRating = $task->clientRatings()->exists();
        
        return response()->json([
            'has_task_rating' => $hasTaskRating,
            'has_client_rating' => $hasClientRating,
            'task_rating_avg' => $hasTaskRating ? $task->rating->average_rating : null,
            'client_rating_avg' => $hasClientRating ? $task->clientRatings->avg('rating') : null
        ]);
    }
}