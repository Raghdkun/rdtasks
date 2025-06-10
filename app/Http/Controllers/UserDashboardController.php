<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\TaskRating;
use App\Models\ClientRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserDashboardController extends AppBaseController
{
    /**
     * Display the User Dashboard with KPI data
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'current_month');
        
        $dashboardData = $this->getUserDashboardData($user, $period);
        
        return view('user_dashboard.index', compact('dashboardData', 'period'));
    }

    /**
     * Get user dashboard data based on period
     */
    private function getUserDashboardData(User $user, $period)
    {
        $dateRange = $this->getDateRange($period);
        
        // Get recent completed tasks
        $recentTasks = $this->getRecentCompletedTasks($user, $dateRange);
        
        // Get KPI data for the user
        $kpiData = $this->getUserKpiData($user, $dateRange);
        
        // Get projects under 50% rate
        $underPerformingProjects = $this->getUnderPerformingProjects($user, $dateRange);
        
        // Get summary statistics
        $summaryStats = $this->getSummaryStats($user, $dateRange, $period);
        
        return [
            'recent_tasks' => $recentTasks,
            'kpi_data' => $kpiData,
            'under_performing_projects' => $underPerformingProjects,
            'summary_stats' => $summaryStats,
        ];
    }

    /**
     * Get date range based on period
     */
    private function getDateRange($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'last_month':
                return [
                    'start' => $now->copy()->subMonth()->startOfMonth(),
                    'end' => $now->copy()->subMonth()->endOfMonth()
                ];
            case 'last_3_months':
                return [
                    'start' => $now->copy()->subMonths(3)->startOfMonth(),
                    'end' => $now->copy()->subMonth()->endOfMonth()
                ];
            case 'last_6_months':
                return [
                    'start' => $now->copy()->subMonths(6)->startOfMonth(),
                    'end' => $now->copy()->subMonth()->endOfMonth()
                ];
            case 'current_month':
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }

    /**
     * Get recent completed tasks for the user
     */
    private function getRecentCompletedTasks(User $user, $dateRange)
    {
        return Task::with(['project', 'rating', 'clientRatings'])
            ->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.user_id', $user->id)
            ->where('tasks.status', 1)
            ->whereNotNull('tasks.completed_on')
            ->whereBetween('tasks.completed_on', [$dateRange['start'], $dateRange['end']])
            ->orderBy('tasks.completed_on', 'desc')
            ->select('tasks.*')
            ->limit(10)
            ->get();
    }

    /**
     * Get user KPI data
     */
    private function getUserKpiData(User $user, $dateRange)
    {
        $taskRatingData = TaskRating::select(
                DB::raw('AVG((code_quality + delivery_output + time_score + collaboration + complexity_urgency) / 5) as avg_rating'),
                DB::raw('COUNT(*) as total_ratings')
            )
            ->join('tasks', 'task_ratings.task_id', '=', 'tasks.id')
            ->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.user_id', $user->id)
            ->where('tasks.status', 1)
            ->whereNotNull('tasks.completed_on')
            ->whereBetween('tasks.completed_on', [$dateRange['start'], $dateRange['end']])
            ->first();

        $clientRatingData = ClientRating::select(
                DB::raw('AVG(rating) as avg_rating'),
                DB::raw('COUNT(*) as total_ratings')
            )
            ->join('tasks', 'client_ratings.task_id', '=', 'tasks.id')
            ->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.user_id', $user->id)
            ->where('tasks.status', 1)
            ->whereNotNull('tasks.completed_on')
            ->whereBetween('tasks.completed_on', [$dateRange['start'], $dateRange['end']])
            ->first();

        // Calculate percentages
        $taskPercentage = $taskRatingData->avg_rating ? ($taskRatingData->avg_rating / 10) * 50 : 0;
        $clientPercentage = $clientRatingData->avg_rating ? ($clientRatingData->avg_rating / 50) * 50 : 0;
        $totalPercentage = $taskPercentage + $clientPercentage;

        return [
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

    /**
     * Get projects under 50% performance rate
     */
    private function getUnderPerformingProjects(User $user, $dateRange)
    {
        $projects = DB::table('projects')
            ->select('projects.*', 
                DB::raw('AVG((task_ratings.code_quality + task_ratings.delivery_output + task_ratings.time_score + task_ratings.collaboration + task_ratings.complexity_urgency) / 5) as avg_task_rating'),
                DB::raw('AVG(client_ratings.rating) as avg_client_rating'),
                DB::raw('COUNT(DISTINCT tasks.id) as total_tasks')
            )
            ->join('tasks', 'projects.id', '=', 'tasks.project_id')
            ->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->leftJoin('task_ratings', 'tasks.id', '=', 'task_ratings.task_id')
            ->leftJoin('client_ratings', 'tasks.id', '=', 'client_ratings.task_id')
            ->where('task_assignees.user_id', $user->id)
            ->where('tasks.status', 1)
            ->whereNotNull('tasks.completed_on')
            ->whereBetween('tasks.completed_on', [$dateRange['start'], $dateRange['end']])
            ->groupBy('projects.id', 'projects.name', 'projects.description', 'projects.created_at', 'projects.updated_at')
            ->havingRaw('(AVG((task_ratings.code_quality + task_ratings.delivery_output + task_ratings.time_score + task_ratings.collaboration + task_ratings.complexity_urgency) / 5) / 10 * 50) + (AVG(client_ratings.rating) / 50 * 50) < 50')
            ->get()
            ->map(function($project) {
                $taskPercentage = $project->avg_task_rating ? ($project->avg_task_rating / 10) * 50 : 0;
                $clientPercentage = $project->avg_client_rating ? ($project->avg_client_rating / 50) * 50 : 0;
                $project->total_percentage = round($taskPercentage + $clientPercentage, 2);
                return $project;
            });

        return $projects;
    }

    /**
     * Calculate incentive data
     */
    private function calculateIncentiveData(User $user, $dateRange)
    {
        $kpiData = $this->getUserKpiData($user, $dateRange);
        
        // Base incentive calculation (you can adjust this logic based on your business rules)
        $baseIncentive = 1000; // Base amount
        $performanceMultiplier = $kpiData['total_percentage'] / 100;
        $totalIncentive = $baseIncentive * $performanceMultiplier;
        
        // Bonus for high performance
        $bonus = 0;
        if ($kpiData['total_percentage'] >= 90) {
            $bonus = $baseIncentive * 0.5; // 50% bonus for A+ performance
        } elseif ($kpiData['total_percentage'] >= 80) {
            $bonus = $baseIncentive * 0.3; // 30% bonus for A performance
        } elseif ($kpiData['total_percentage'] >= 70) {
            $bonus = $baseIncentive * 0.15; // 15% bonus for B+ performance
        }
        
        return [
            'base_incentive' => $baseIncentive,
            'performance_multiplier' => round($performanceMultiplier, 2),
            'performance_incentive' => round($totalIncentive, 2),
            'bonus' => round($bonus, 2),
            'total_incentive' => round($totalIncentive + $bonus, 2),
            'incentive_percentage' => round($kpiData['total_percentage'], 2)
        ];
    }

    /**
     * Get summary statistics
     */
    private function getSummaryStats(User $user, $dateRange)
    {
        $completedTasks = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.user_id', $user->id)
            ->where('tasks.status', 1)
            ->whereNotNull('tasks.completed_on')
            ->whereBetween('tasks.completed_on', [$dateRange['start'], $dateRange['end']])
            ->count();

        $totalProjects = DB::table('projects')
            ->join('tasks', 'projects.id', '=', 'tasks.project_id')
            ->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
            ->where('task_assignees.user_id', $user->id)
            ->where('tasks.status', 1)
            ->whereNotNull('tasks.completed_on')
            ->whereBetween('tasks.completed_on', [$dateRange['start'], $dateRange['end']])
            ->distinct('projects.id')
            ->count();

        return [
            'completed_tasks' => $completedTasks,
            'total_projects' => $totalProjects,
            'period_label' => $this->getPeriodLabel($dateRange)
        ];
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
     * Get period label for display
     */
    private function getPeriodLabel($dateRange)
    {
        return $dateRange['start']->format('M j, Y') . ' - ' . $dateRange['end']->format('M j, Y');
    }
}