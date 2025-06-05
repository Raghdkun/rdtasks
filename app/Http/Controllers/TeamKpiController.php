<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\TaskRating;
use App\Models\ClientRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeamKpiController extends AppBaseController
{
    /**
     * Display the Team KPI report
     */
    public function index(Request $request)
    {
        $users = User::orderBy('name')->get();
        $kpiData = $this->getKpiData($request);
        
        return view('team_kpi.index', compact('users', 'kpiData'));
    }

    /**
     * Get KPI data based on filters
     */
    private function getKpiData(Request $request)
    {
        $query = User::select('users.*')
            ->leftJoin('task_assignees', 'users.id', '=', 'task_assignees.user_id')
            ->leftJoin('tasks', 'task_assignees.task_id', '=', 'tasks.id')
            ->leftJoin('task_ratings', 'tasks.id', '=', 'task_ratings.task_id')
            ->leftJoin('client_ratings', 'tasks.id', '=', 'client_ratings.task_id')
            ->where('tasks.status', 1) // Only completed tasks
            ->whereNotNull('tasks.completed_on');

        // Apply user filter
        if ($request->filled('user_id')) {
            $query->where('users.id', $request->user_id);
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->where('tasks.completed_on', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('tasks.completed_on', '<=', $request->date_to);
        }

        $users = $query->groupBy('users.id')->get();

        $kpiData = [];
        foreach ($users as $user) {
            $taskRatingsQuery = TaskRating::select(
                    DB::raw('AVG((code_quality + delivery_output + time_score + collaboration + complexity_urgency) / 5) as avg_rating'),
                    DB::raw('COUNT(*) as total_ratings')
                )
                ->join('tasks', 'task_ratings.task_id', '=', 'tasks.id')
                ->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
                ->where('task_assignees.user_id', $user->id)
                ->where('tasks.status', 1)
                ->whereNotNull('tasks.completed_on');

            $clientRatingsQuery = ClientRating::select(
                    DB::raw('AVG(rating) as avg_rating'),
                    DB::raw('COUNT(*) as total_ratings')
                )
                ->join('tasks', 'client_ratings.task_id', '=', 'tasks.id')
                ->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
                ->where('task_assignees.user_id', $user->id)
                ->where('tasks.status', 1)
                ->whereNotNull('tasks.completed_on');

            // Apply date filters to rating queries
            if ($request->filled('date_from')) {
                $taskRatingsQuery->where('tasks.completed_on', '>=', $request->date_from);
                $clientRatingsQuery->where('tasks.completed_on', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $taskRatingsQuery->where('tasks.completed_on', '<=', $request->date_to);
                $clientRatingsQuery->where('tasks.completed_on', '<=', $request->date_to);
            }

            $taskRatingData = $taskRatingsQuery->first();
            $clientRatingData = $clientRatingsQuery->first();

            // Calculate percentages (Task Rating: max 10 = 50%, Client Rating: max 50 = 50%)
            $taskPercentage = $taskRatingData->avg_rating ? ($taskRatingData->avg_rating / 10) * 50 : 0;
            $clientPercentage = $clientRatingData->avg_rating ? ($clientRatingData->avg_rating / 50) * 50 : 0;
            $totalPercentage = $taskPercentage + $clientPercentage;

            $kpiData[] = [
                'user' => $user,
                'task_avg_rating' => round($taskRatingData->avg_rating ?? 0, 2),
                'task_total_ratings' => $taskRatingData->total_ratings ?? 0,
                'task_percentage' => round($taskPercentage, 2),
                'client_avg_rating' => round($clientRatingData->avg_rating ?? 0, 2),
                'client_total_ratings' => $clientRatingData->total_ratings ?? 0,
                'client_percentage' => round($clientPercentage, 2),
                'total_percentage' => round($totalPercentage, 2),
                'performance_grade' => $this->getPerformanceGrade($totalPercentage)
            ];
        }

        // Sort by total percentage descending
        usort($kpiData, function($a, $b) {
            return $b['total_percentage'] <=> $a['total_percentage'];
        });

        return $kpiData;
    }

    /**
     * Get performance grade based on percentage
     */
    private function getPerformanceGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        return 'D';
    }

    /**
     * Export KPI data to CSV
     */
    public function export(Request $request)
    {
        $kpiData = $this->getKpiData($request);
        
        $filename = 'team_kpi_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($kpiData) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'User Name',
                'Task Avg Rating',
                'Task Total Ratings', 
                'Task Percentage (50% max)',
                'Client Avg Rating',
                'Client Total Ratings',
                'Client Percentage (50% max)',
                'Total Percentage',
                'Performance Grade'
            ]);

            // CSV Data
            foreach ($kpiData as $data) {
                fputcsv($file, [
                    $data['user']->name,
                    $data['task_avg_rating'],
                    $data['task_total_ratings'],
                    $data['task_percentage'] . '%',
                    $data['client_avg_rating'],
                    $data['client_total_ratings'],
                    $data['client_percentage'] . '%',
                    $data['total_percentage'] . '%',
                    $data['performance_grade']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}