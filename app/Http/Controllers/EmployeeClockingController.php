<?php

namespace App\Http\Controllers;

use App\Models\EmployeeClocking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use Auth;

class EmployeeClockingController extends AppBaseController
{
    /**
     * Display the clocking interface
     */
    public function index(): View
    {
        $user = Auth::user();
        $todayClocking = EmployeeClocking::forUser($user->id)
            ->today()
            ->first();

        return view('employee_clocking.index', compact('todayClocking'));
    }

    /**
     * Clock in for the day
     */
    public function clockIn(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Check if already clocked in today
        $existingClocking = EmployeeClocking::forUser($user->id)
            ->today()
            ->first();

        if ($existingClocking) {
            return $this->sendError('You have already clocked in today.');
        }

        $clocking = EmployeeClocking::create([
            'user_id' => $user->id,
            'clock_in_time' => now(),
            'work_date' => today(),
            'status' => EmployeeClocking::STATUS_CLOCKED_IN,
            'daily_work_hours' => $request->input('daily_work_hours', 420) // Default 7 hours
        ]);

        return $this->sendResponse($clocking, 'Clocked in successfully.');
    }

    /**
     * Start break
     */
    public function startBreak(Request $request): JsonResponse
    {
        $user = Auth::user();
        $clocking = EmployeeClocking::forUser($user->id)
            ->today()
            ->where('status', '!=', EmployeeClocking::STATUS_CLOCKED_OUT)
            ->first();

        if (!$clocking) {
            return $this->sendError('No active clocking session found.');
        }

        if ($clocking->status === EmployeeClocking::STATUS_ON_BREAK) {
            return $this->sendError('You are already on break.');
        }

        $clocking->startBreak();

        return $this->sendResponse($clocking, 'Break started successfully.');
    }

    /**
     * End break and continue working
     */
    public function endBreak(Request $request): JsonResponse
    {
        $user = Auth::user();
        $clocking = EmployeeClocking::forUser($user->id)
            ->today()
            ->where('status', EmployeeClocking::STATUS_ON_BREAK)
            ->first();

        if (!$clocking) {
            return $this->sendError('No active break session found.');
        }

        $clocking->endBreak();

        return $this->sendResponse($clocking, 'Break ended. Continue working.');
    }

    /**
     * Clock out for the day
     */
    public function clockOut(Request $request): JsonResponse
    {
        $user = Auth::user();
        $clocking = EmployeeClocking::forUser($user->id)
            ->today()
            ->where('status', '!=', EmployeeClocking::STATUS_CLOCKED_OUT)
            ->first();

        if (!$clocking) {
            return $this->sendError('No active clocking session found.');
        }

        // End any active break session
        if ($clocking->status === EmployeeClocking::STATUS_ON_BREAK) {
            $clocking->endBreak();
        }

        $clocking->clock_out_time = now();
        $clocking->status = EmployeeClocking::STATUS_CLOCKED_OUT;
        $clocking->notes = $request->input('notes');
        
        // Calculate and save work time
        $clocking->calculateAndSaveWorkTime();
        
        return $this->sendResponse($clocking, 'Clocked out successfully.');
    }

    /**
     * Get current clocking status
     */
    public function getCurrentStatus(): JsonResponse
    {
        $user = Auth::user();
        $clocking = EmployeeClocking::forUser($user->id)
            ->today()
            ->first();

        if (!$clocking) {
            return $this->sendResponse([
                'status' => 'not_clocked_in',
                'clocking' => null
            ], 'No clocking session found.');
        }

        // Calculate current work time
        $clocking->calculateWorkTime();

        return $this->sendResponse([
            'status' => $clocking->status,
            'clocking' => $clocking,
            'current_break' => $clocking->getCurrentBreakSession(),
            'formatted_work_time' => $clocking->getFormattedWorkTime(),
            'formatted_break_time' => $clocking->getFormattedBreakTime()
        ], 'Current status retrieved.');
    }

    /**
     * Display clocking reports
     */
    public function reports(Request $request): View
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $clockings = EmployeeClocking::forUser($user->id)
            ->whereBetween('work_date', [$startDate, $endDate])
            ->orderBy('work_date', 'desc')
            ->paginate(15);

        return view('employee_clocking.reports', compact('clockings', 'startDate', 'endDate'));
    }

    /**
     * Admin view for all employee clockings
     */
    public function adminReports(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $userId = $request->input('user_id');

        $query = EmployeeClocking::with('user')
            ->whereBetween('work_date', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $clockings = $query->orderBy('work_date', 'desc')
            ->orderBy('clock_in_time', 'desc')
            ->paginate(20);

        $users = \App\Models\User::active()->get();

        // Calculate statistics
        $statisticsQuery = EmployeeClocking::with('user')
            ->whereBetween('work_date', [$startDate, $endDate]);
        
        if ($userId) {
            $statisticsQuery->where('user_id', $userId);
        }
        
        $allClockings = $statisticsQuery->get();
        
        $statistics = [
            'total_employees' => $allClockings->pluck('user_id')->unique()->count(),
            'total_work_hours' => round($allClockings->sum('total_work_minutes') / 60, 1),
            'total_break_hours' => round($allClockings->sum('total_break_minutes') / 60, 1),
            'total_days' => $allClockings->pluck('work_date')->unique()->count()
        ];

        return view('employee_clocking.admin_reports', compact('clockings', 'users', 'startDate', 'endDate', 'userId', 'statistics'));
    }

    /**
     * Show form for editing a clocking record
     */
    public function edit($id)
    {
        $clocking = EmployeeClocking::with('user')->findOrFail($id);
        
        // If it's an AJAX request, return JSON data
        if (request()->ajax() || request()->expectsJson()) {
            return $this->sendResponse([
                'id' => $clocking->id,
                'user_id' => $clocking->user_id,
                'work_date' => $clocking->work_date->format('Y-m-d'),
                'clock_in_time' => $clocking->clock_in_time->format('Y-m-d H:i:s'),
                'clock_out_time' => $clocking->clock_out_time ? $clocking->clock_out_time->format('Y-m-d H:i:s') : null,
                'daily_work_hours' => $clocking->daily_work_hours,
                'notes' => $clocking->notes
            ], 'Clocking data retrieved successfully.');
        }
        
        // For regular requests, return the view
        $users = \App\Models\User::active()->get();
        return view('employee_clocking.edit', compact('clocking', 'users'));
    }

    /**
     * Update a clocking record
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'work_date' => 'required|date',
            'clock_in_time' => 'required|date_format:H:i',
            'clock_out_time' => 'nullable|date_format:H:i|after:clock_in_time',
            'daily_work_hours' => 'required|integer|min:60|max:720',
            'notes' => 'nullable|string|max:500'
        ]);

        $clocking = EmployeeClocking::findOrFail($id);
        
        // Combine date and time
        $workDate = $request->work_date;
        $clockInTime = Carbon::createFromFormat('Y-m-d H:i', $workDate . ' ' . $request->clock_in_time);
        $clockOutTime = $request->clock_out_time ? 
            Carbon::createFromFormat('Y-m-d H:i', $workDate . ' ' . $request->clock_out_time) : null;

        $clocking->update([
            'user_id' => $request->user_id,
            'work_date' => $workDate,
            'clock_in_time' => $clockInTime,
            'clock_out_time' => $clockOutTime,
            'daily_work_hours' => $request->daily_work_hours,
            'notes' => $request->notes,
            'status' => $clockOutTime ? EmployeeClocking::STATUS_CLOCKED_OUT : $clocking->status
        ]);

        // Recalculate work time if clocked out
        if ($clockOutTime) {
            $clocking->calculateWorkTime();
        }

        return $this->sendResponse($clocking, 'Clocking record updated successfully.');
    }

    /**
     * Delete a clocking record
     */
    public function destroy($id): JsonResponse
    {
        $clocking = EmployeeClocking::findOrFail($id);
        $clocking->delete();

        return $this->sendResponse([], 'Clocking record deleted successfully.');
    }
}