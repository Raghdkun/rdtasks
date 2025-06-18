<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskRating;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Exception;

class TaskRatingController extends AppBaseController
{
    /**
     * Display a listing of completed tasks for rating.
     *
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        // Build the query for completed tasks
        $query = Task::with(['project', 'taskAssignee', 'rating', 'rating.ratedBy', 'rating.editedBy'])
            ->where('status', 1)
            ->whereNotNull('completed_on');

        // Apply filters
        if ($request->filled('task_name')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->task_name . '%')
                  ->orWhereHas('project', function($projectQuery) use ($request) {
                      $projectQuery->where('name', 'like', '%' . $request->task_name . '%');
                  });
            });
        }

        if ($request->filled('assignee_name')) {
            $query->whereHas('taskAssignee', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->assignee_name . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('completed_on', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('completed_on', '<=', $request->date_to);
        }

        if ($request->filled('rating_status')) {
            switch ($request->rating_status) {
                case 'rated':
                    $query->whereHas('rating');
                    break;
                case 'pending':
                    $query->whereDoesntHave('rating');
                    break;
                case 'edited':
                    $query->whereHas('rating', function($q) {
                        $q->where('is_edited', true);
                    });
                    break;
            }
        }

        // Handle CSV export
        if ($request->get('export') === 'csv') {
            $tasks = $query->orderBy('completed_on', 'desc')->get();
            return $this->exportToCsv($tasks);
        }

        // Regular pagination for web view
        $completedTasks = $query->orderBy('completed_on', 'desc')->paginate(20);

        return view('task_ratings.index', compact('completedTasks'));
    }

    /**
     * Export tasks to CSV
     */
    private function exportToCsv($tasks)
    {
        $filename = 'task-ratings-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ];
    
        $callback = function() use ($tasks) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Task Number',
                'Task Title', 
                'Project',
                'Assignees',
                'Completed On',
                'Average Rating',
                'Code Quality',
                'Delivery Output',
                'Time Score',
                'Collaboration',
                'Complexity Urgency',
                'Comments',
                'Status',
                'Rated By',
                'Rated On',
                'Last Edited By',
                'Last Edited On'
            ]);
    
            // Add data rows
            foreach ($tasks as $task) {
                $assignees = $task->taskAssignee->pluck('name')->implode(', ');
                $rating = $task->rating;
                
                fputcsv($file, [
                    $task->task_number,
                    $task->title,
                    $task->project->name ?? 'N/A',
                    $assignees ?: 'No assignee',
                    $task->completed_on ? $task->completed_on->format('Y-m-d') : 'N/A',
                    $rating ? number_format($rating->average_rating, 2) : 'Not rated',
                    $rating ? $rating->code_quality : 'N/A',
                    $rating ? $rating->delivery_output : 'N/A', 
                    $rating ? $rating->time_score : 'N/A',
                    $rating ? $rating->collaboration : 'N/A',
                    $rating ? $rating->complexity_urgency : 'N/A',
                    $rating ? $rating->comments : 'N/A',
                    $rating ? ($rating->is_edited ? 'Edited' : 'Rated') : 'Pending',
                    $rating ? ($rating->ratedBy->name ?? 'Unknown') : 'N/A',
                    $rating ? $rating->created_at->format('Y-m-d H:i:s') : 'N/A',
                    $rating && $rating->is_edited ? ($rating->editedBy->name ?? 'Unknown') : 'N/A',
                    $rating && $rating->is_edited ? $rating->edited_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            
            fclose($file);
        };
    
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for rating a specific task.
     *
     * @param Task $task
     * @return Factory|View
     */
    public function create(Task $task)
    {
        // Check if task is completed
        if ($task->status != 1 || !$task->completed_on) {
            return redirect()->route('task-ratings.index')
                ->with('error', 'Only completed tasks can be rated.');
        }

        // Check if already rated
        if ($task->rating) {
            return redirect()->route('task-ratings.edit', $task)
                ->with('info', 'This task has already been rated. You can edit the existing rating.');
        }

        $criteria = TaskRating::getRatingCriteria();
        
        return view('task_ratings.create', compact('task', 'criteria'));
    }

    /**
     * Store a newly created rating in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     */
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'code_quality' => 'required|integer|min:1|max:5',
            'delivery_output' => 'required|integer|min:1|max:5', 
            'time_score' => 'required|integer|min:1|max:5',
            'collaboration' => 'required|integer|min:1|max:5',
            'complexity_urgency' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000'
        ]);

        // Check if task is completed
        if ($task->status != 1 || !$task->completed_on) {
            return $this->sendError('Only completed tasks can be rated.');
        }

        // Check if already rated
        if ($task->rating) {
            return $this->sendError('This task has already been rated.');
        }

        DB::beginTransaction();
        try {
            $rating = TaskRating::create([
                'task_id' => $task->id,
                'rated_by' => Auth::id(),
                'code_quality' => $request->code_quality,
                'delivery_output' => $request->delivery_output,
                'time_score' => $request->time_score,
                'collaboration' => $request->collaboration,
                'complexity_urgency' => $request->complexity_urgency,
                'comments' => $request->comments
            ]);

            DB::commit();

            return $this->sendResponse($rating, 'Task rated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred while saving the rating.');
        }
    }

    /**
     * Show the form for editing the specified rating.
     *
     * @param Task $task
     * @return Factory|View
     */
    public function edit(Task $task)
    {
        $rating = $task->rating;
        
        if (!$rating) {
            return redirect()->route('task-ratings.create', $task)
                ->with('info', 'This task has not been rated yet.');
        }

        $criteria = TaskRating::getRatingCriteria();
        
        return view('task_ratings.edit', compact('task', 'rating', 'criteria'));
    }

    /**
     * Update the specified rating in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'code_quality' => 'required|integer|min:1|max:5',
            'delivery_output' => 'required|integer|min:1|max:5',
            'time_score' => 'required|integer|min:1|max:5',
            'collaboration' => 'required|integer|min:1|max:5',
            'complexity_urgency' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000'
        ]);

        $rating = $task->rating;
        
        if (!$rating) {
            return $this->sendError('This task has not been rated yet.');
        }

        DB::beginTransaction();
        try {
            $rating->update([
                'code_quality' => $request->code_quality,
                'delivery_output' => $request->delivery_output,
                'time_score' => $request->time_score,
                'collaboration' => $request->collaboration,
                'complexity_urgency' => $request->complexity_urgency,
                'comments' => $request->comments,
                'edited_by' => Auth::id(),
                'edited_at' => now()
            ]);

            DB::commit();

            return $this->sendResponse($rating->fresh(), 'Task rating updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred while updating the rating.');
        }
    }

    /**
     * Display the specified rating.
     *
     * @param Task $task
     * @return Factory|View
     */
    public function show(Task $task)
    {
        $rating = $task->rating;
        
        if (!$rating) {
            return redirect()->route('task-ratings.index')
                ->with('error', 'This task has not been rated yet.');
        }

        $criteria = TaskRating::getRatingCriteria();
        
        return view('task_ratings.show', compact('task', 'rating', 'criteria'));
    }

    /**
     * Get popup content for task rating
     */
    public function getPopupContent(Task $task)
    {
        $task->load(['rating.ratedBy', 'clientRatings.client', 'clientRatings.ratedBy', 'project']);
        
        $content = view('task_ratings.popup_content', compact('task'))->render();
        $actions = view('task_ratings.popup_actions', compact('task'))->render();
        
        return response()->json([
            'content' => $content,
            'actions' => $actions
        ]);
    }
}