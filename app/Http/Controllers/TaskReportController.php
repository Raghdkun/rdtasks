<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

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
        try {
            // Get filter parameters
            $projectId = $request->input('project_id');
            $status = $request->input('status');
            $priority = $request->input('priority');
            $userId = $request->input('user_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Build query with proper relationships
            $query = Task::with(['project', 'createdUser', 'taskAssignee', 'timeEntries'])
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

            // Filter by user if provided
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
                'Assigned To',
                'Estimated Time',
                'Total Hours Spent'
            ];

            $callback = function() use ($tasks, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($tasks as $task) {
                    // Get status text
                    $statusText = '';
                    if (isset($task->status) && is_numeric($task->status)) {
                        $statusArr = Task::$statusArr ?? [];
                        $statusText = isset($statusArr[$task->status]) ? $statusArr[$task->status] : '';
                    } else {
                        $statusText = $task->status;
                    }
                    
                    // Get project name
                    $projectName = '';
                    if (isset($task->project) && $task->project) {
                        $projectName = $task->project->name;
                    }
                    
                    // Get created by
                    $createdBy = '';
                    if (isset($task->createdUser) && $task->createdUser) {
                        $createdBy = $task->createdUser->name;
                    }
                    
                    // Format due date and completed on date properly
                    $dueDate = '';
                    if (!empty($task->due_date)) {
                        $dueDate = date('Y-m-d', strtotime($task->due_date));
                    }
                    
                    $completedOn = '';
                    if (!empty($task->completed_on)) {
                        $completedOn = date('Y-m-d', strtotime($task->completed_on));
                    }
                    
                    // Calculate total hours spent
                    $totalHoursSpent = 0;
                    if ($task->timeEntries && $task->timeEntries->count() > 0) {
                        $totalHoursSpent = $task->timeEntries->sum('duration') / 60; // Convert minutes to hours
                    }
                    
                    // Check if task has assignees
                    if ($task->taskAssignee && $task->taskAssignee->count() > 0) {
                        // Create a row for each assignee
                        foreach ($task->taskAssignee as $assignee) {
                            $row = [
                                $task->id ?? '',
                                $task->task_number ?? '',
                                $task->title ?? '',
                                strip_tags($task->description ?? ''),
                                $projectName,
                                $statusText,
                                $task->priority ?? '',
                                $dueDate,
                                $completedOn,
                                $createdBy,
                                $assignee->name ?? '', // Add assignee name
                                $task->estimate_time ?? '',
                                number_format($totalHoursSpent, 2) // Format to 2 decimal places
                            ];

                            fputcsv($file, $row);
                        }
                    } else {
                        // If no assignees, create a row with empty assignee field
                        $row = [
                            $task->id ?? '',
                            $task->task_number ?? '',
                            $task->title ?? '',
                            strip_tags($task->description ?? ''),
                            $projectName,
                            $statusText,
                            $task->priority ?? '',
                            $dueDate,
                            $completedOn,
                            $createdBy,
                            '', // Empty assignee
                            $task->estimate_time ?? '',
                            number_format($totalHoursSpent, 2) // Format to 2 decimal places
                        ];

                        fputcsv($file, $row);
                    }
                }

                fclose($file);
            };

            // Use streamDownload for better compatibility
            return response()->streamDownload($callback, 'tasks_report_' . date('Y-m-d') . '.csv', $headers);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Task report export failed: ' . $e->getMessage());
            
            // Return a friendly error message
            return back()->with('error', 'Failed to export tasks: ' . $e->getMessage());
        }
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