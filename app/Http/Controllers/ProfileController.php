<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfileController extends AppBaseController
{
    /**
     * Display the user profile page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to view your profile.');
        }
        
        $profileData = $this->getUserProfileData($user);
        
        return view('profile.index', compact('user', 'profileData'));
    }
    
    /**
     * Display another user's profile (public view)
     */
    public function show(User $user)
    {
        // Check if user exists (Laravel route model binding should handle this, but let's be safe)
        if (!$user) {
            abort(404, 'User not found');
        }
        
        $profileData = $this->getUserProfileData($user);
        
        return view('profile.show', compact('user', 'profileData'));
    }
    
    /**
     * Get comprehensive profile data for a user
     */
    private function getUserProfileData($user)
    {
        // Additional safety check
        if (!$user) {
            return [
                'task_stats' => [
                    'total_tasks' => 0,
                    'completed_tasks' => 0,
                    'in_progress_tasks' => 0,
                    'overdue_tasks' => 0,
                ],
                'time_stats' => [
                    'total_hours' => 0,
                    'this_month_hours' => 0,
                    'avg_daily_hours' => 0,
                ],
                'recent_tasks' => collect(),
                'achievements' => [],
                'monthly_data' => [],
            ];
        }
        
        // Get status IDs first
        $completedStatus = \App\Models\Status::where('name', 'Completed')->first();
        $inProgressStatus = \App\Models\Status::where('name', 'In Progress')->first();
        
        $completedStatusId = $completedStatus ? $completedStatus->status : null;
        $inProgressStatusId = $inProgressStatus ? $inProgressStatus->status : null;
        
        // Task Statistics with null checks
        $taskStats = [
            'total_tasks' => $user->assignedTasks()->count(),
            'completed_tasks' => $completedStatusId ? $user->assignedTasks()->where('status', $completedStatusId)->count() : 0,
            'in_progress_tasks' => $inProgressStatusId ? $user->assignedTasks()->where('status', $inProgressStatusId)->count() : 0,
            'overdue_tasks' => $user->assignedTasks()->where('due_date', '<', Carbon::now())
                ->where('status', '!=', $completedStatusId)->count(),
        ];
        
        // Time Tracking Statistics
        $timeStats = [
            'total_hours' => $user->timeEntries()->sum('duration') ?? 0,
            'this_month_hours' => $user->timeEntries()
                ->whereMonth('start_time', Carbon::now()->month)
                ->whereYear('start_time', Carbon::now()->year)
                ->sum('duration') ?? 0,
            'avg_daily_hours' => $user->timeEntries()
                ->whereMonth('start_time', Carbon::now()->month)
                ->avg('duration') ?? 0,
        ];
        
        // Recent Activities
        $recentTasks = $user->assignedTasks()
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        
        // Achievement Calculations
        $achievements = $this->calculateAchievements($user, $taskStats, $timeStats);
        
        // Monthly Performance Data
        $monthlyData = $this->getMonthlyPerformanceData($user);
        
        return [
            'task_stats' => $taskStats,
            'time_stats' => $timeStats,
            'recent_tasks' => $recentTasks,
            'achievements' => $achievements,
            'monthly_data' => $monthlyData,
        ];
    }
    
    /**
     * Calculate user achievements and badges
     */
    private function calculateAchievements(User $user, array $taskStats, array $timeStats)
    {
        $achievements = [];
        
        // Task-based achievements
        if ($taskStats['completed_tasks'] >= 100) {
            $achievements[] = ['name' => 'Task Master', 'icon' => 'fas fa-trophy', 'color' => 'gold'];
        } elseif ($taskStats['completed_tasks'] >= 50) {
            $achievements[] = ['name' => 'Task Expert', 'icon' => 'fas fa-medal', 'color' => 'silver'];
        } elseif ($taskStats['completed_tasks'] >= 10) {
            $achievements[] = ['name' => 'Task Achiever', 'icon' => 'fas fa-award', 'color' => 'bronze'];
        }
        
        // Time-based achievements
        if ($timeStats['total_hours'] >= 1000) {
            $achievements[] = ['name' => 'Time Champion', 'icon' => 'fas fa-clock', 'color' => 'gold'];
        } elseif ($timeStats['total_hours'] >= 500) {
            $achievements[] = ['name' => 'Time Tracker', 'icon' => 'fas fa-stopwatch', 'color' => 'silver'];
        }
        
        return $achievements;
    }
    
    /**
     * Get monthly performance data for charts
     */
    /**
     * Get monthly performance data for insights
     */
    private function getMonthlyPerformanceData(User $user)
    {
        $monthlyInsights = [];
        
        // Get completed status ID
        $completedStatusId = \App\Models\Status::where('name', 'Completed')->first()->status ?? null;
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $tasksCompleted = $user->assignedTasks()
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where('status', $completedStatusId)
                ->count();
                
            $hoursLogged = $user->timeEntries()
                ->whereMonth('start_time', $date->month)
                ->whereYear('start_time', $date->year)
                ->sum('duration') ?? 0;
                
            $avgHoursPerTask = $tasksCompleted > 0 ? round($hoursLogged / $tasksCompleted, 1) : 0;
            
            $monthlyInsights[] = [
                'month' => $date->format('M Y'),
                'month_short' => $date->format('M'),
                'tasks_completed' => $tasksCompleted,
                'hours_logged' => round($hoursLogged, 1),
                'avg_hours_per_task' => $avgHoursPerTask,
                'productivity_score' => $this->calculateProductivityScore($tasksCompleted, $hoursLogged)
            ];
        }
        
        return $monthlyInsights;
    }
    
    /**
     * Calculate productivity score based on tasks and hours
     */
    private function calculateProductivityScore($tasks, $hours)
    {
        if ($hours == 0) return 0;
        
        // Simple productivity formula: tasks per hour * 10 (scaled for display)
        $score = ($tasks / max($hours, 1)) * 10;
        return min(round($score, 1), 10); // Cap at 10
    }
}