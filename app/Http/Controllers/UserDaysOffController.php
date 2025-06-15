<?php

namespace App\Http\Controllers;

use App\Models\DaysOffSetting;
use App\Models\UserDaysOff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UserDaysOffController extends AppBaseController
{
    public function index(): View
    {
        $settings = DaysOffSetting::where('is_active', true)->latest()->first();
        $weekDays = DaysOffSetting::getWeekDays();
        
        // Get current week's selection
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentSelection = UserDaysOff::where('user_id', Auth::id())
            ->where('week_start_date', $currentWeekStart->format('Y-m-d'))
            ->first();
            
        // Get user's history
        $userHistory = UserDaysOff::where('user_id', Auth::id())
            ->with('approver')
            ->orderBy('week_start_date', 'desc')
            ->paginate(10);
        
        return view('user_days_off.index', compact('settings', 'weekDays', 'currentSelection', 'userHistory', 'currentWeekStart'));
    }

    public function store(Request $request): JsonResponse
    {
        $settings = DaysOffSetting::where('is_active', true)->latest()->first();
        
        if (!$settings) {
            return $this->sendError('Days off settings not configured.');
        }

        $request->validate([
            'selected_days' => 'required|array|min:1|max:' . $settings->days_per_week,
            'selected_days.*' => 'integer|min:1|max:7',
            'week_start_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if selection already exists for this week
        $existingSelection = UserDaysOff::where('user_id', Auth::id())
            ->where('week_start_date', $request->week_start_date)
            ->first();

        if ($existingSelection) {
            $existingSelection->update([
                'selected_days' => $request->selected_days,
                'notes' => $request->notes,
                'status' => 'pending'
            ]);
            $selection = $existingSelection;
        } else {
            $selection = UserDaysOff::create([
                'user_id' => Auth::id(),
                'week_start_date' => $request->week_start_date,
                'selected_days' => $request->selected_days,
                'notes' => $request->notes
            ]);
        }

        return $this->sendResponse($selection, 'Days off selection saved successfully.');
    }

    public function adminIndex(): View
    {
        $pendingRequests = UserDaysOff::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $allRequests = UserDaysOff::with(['user', 'approver'])
            ->orderBy('week_start_date', 'desc')
            ->paginate(20);
        
        return view('days_off_admin.index', compact('pendingRequests', 'allRequests'));
    }

    public function approve(Request $request, UserDaysOff $userDaysOff): JsonResponse
    {
        $userDaysOff->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return $this->sendResponse($userDaysOff, 'Days off request approved successfully.');
    }

    public function reject(Request $request, UserDaysOff $userDaysOff): JsonResponse
    {
        $userDaysOff->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return $this->sendResponse($userDaysOff, 'Days off request rejected.');
    }

    public function edit(UserDaysOff $userDaysOff): View
    {
        $settings = DaysOffSetting::where('is_active', true)->latest()->first();
        $weekDays = DaysOffSetting::getWeekDays();
        
        return view('days_off_admin.edit', compact('userDaysOff', 'settings', 'weekDays'));
    }
    
    public function update(Request $request, UserDaysOff $userDaysOff): JsonResponse
    {
        $settings = DaysOffSetting::where('is_active', true)->latest()->first();
        
        if (!$settings) {
            return $this->sendError('Days off settings not configured.');
        }

        $request->validate([
            'selected_days' => 'required|array|min:1|max:' . $settings->days_per_week,
            'selected_days.*' => 'integer|min:1|max:7',
            'notes' => 'nullable|string|max:500'
        ]);

        $userDaysOff->update([
            'selected_days' => $request->selected_days,
            'notes' => $request->notes,
            'updated_at' => now()
        ]);

        return $this->sendResponse($userDaysOff, 'Days off request updated successfully.');
    }
    
    public function publicIndex(): View
    {
        $currentWeek = Carbon::now()->startOfWeek();
        $nextWeek = Carbon::now()->addWeek()->startOfWeek();
        
        // Get current and next week's approved requests
        $currentWeekRequests = UserDaysOff::with('user')
            ->where('status', 'approved')
            ->where('week_start_date', $currentWeek->format('Y-m-d'))
            ->get();
            
        $nextWeekRequests = UserDaysOff::with('user')
            ->where('status', 'approved')
            ->where('week_start_date', $nextWeek->format('Y-m-d'))
            ->get();
        
        $weekDays = DaysOffSetting::getWeekDays();
        
        return view('days_off_public.index', compact(
            'currentWeekRequests', 
            'nextWeekRequests', 
            'currentWeek', 
            'nextWeek', 
            'weekDays'
        ));
    }
}