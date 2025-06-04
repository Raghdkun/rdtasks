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
        // Only show completed tasks (assuming status 1 means completed)
        $completedTasks = Task::with(['project', 'taskAssignee', 'rating', 'rating.ratedBy', 'rating.editedBy'])
            ->where('status', 1) // Adjust this based on your status system
            ->whereNotNull('completed_on')
            ->orderBy('completed_on', 'desc')
            ->paginate(20);

        return view('task_ratings.index', compact('completedTasks'));
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
            'code_quality' => 'required|integer|min:1|max:10',
            'delivery_output' => 'required|integer|min:1|max:10',
            'time_score' => 'required|integer|min:1|max:10',
            'collaboration' => 'required|integer|min:1|max:10',
            'complexity_urgency' => 'required|integer|min:1|max:10',
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
            'code_quality' => 'required|integer|min:1|max:10',
            'delivery_output' => 'required|integer|min:1|max:10',
            'time_score' => 'required|integer|min:1|max:10',
            'collaboration' => 'required|integer|min:1|max:10',
            'complexity_urgency' => 'required|integer|min:1|max:10',
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
}