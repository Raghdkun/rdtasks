<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TaskReportController extends AppBaseController
{
    /**
     * Export tasks as CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCsv(Request $request)
    {
        // Get filter parameters
        $projectId = $request->input('project_id');
        $status = $request->input('status');
        $priority = $request->input('priority');
        $userId = $request->input('user_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Build query
        $query = Task::with(['project', 'taskAssignee', 'createdUser'])
            ->select('tasks.*');

        // Apply filters
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        if ($userId) {
            $query->whereHas('taskAssignee', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Get tasks
        $tasks = $query->get();

        // Prepare CSV data
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tasks_report_' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'Task ID', 
            'Task Number', 
            'Title', 
            'Description', 
            'Project', 
            'Status', 
            'Priority',
            'Due Date', 
            'Completed On', 
            'Created By', 
            'Assignees',
            'Estimated Time',
            'Total Hours Spent'
        ];

        $callback = function() use ($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $assignees = $task->taskAssignee->pluck('name')->implode(', ');
                $statusText = $task->status_text;
                
                $row = [
                    $task->id,
                    $task->prefix_task_number,
                    $task->title,
                    strip_tags($task->description),
                    $task->project->name,
                    $statusText,
                    $task->priority,
                    $task->due_date ? $task->due_date->format('Y-m-d') : '',
                    $task->completed_on ? $task->completed_on->format('Y-m-d') : '',
                    $task->createdUser ? $task->createdUser->name : '',
                    $assignees,
                    $task->estimate_time,
                    $task->task_total_hours
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Display task report form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $projects = Project::pluck('name', 'id')->toArray();
        $users = User::where('is_active', true)->pluck('name', 'id')->toArray();
        $statuses = Task::$statusArr;
        $priorities = Task::PRIORITY;

        return view('task_reports.index', compact('projects', 'users', 'statuses', 'priorities'));
    }
}