<?php

namespace App\Http\Controllers;

use App\Models\DaysOffSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DaysOffSettingController extends AppBaseController
{
    public function index(): View
    {
        $settings = DaysOffSetting::with(['creator', 'updater'])->latest()->first();
        $weekDays = DaysOffSetting::getWeekDays();
        
        return view('days_off_settings.index', compact('settings', 'weekDays'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'days_per_week' => 'required|integer|min:1|max:7',
            'start_week_day' => 'required|integer|min:1|max:7',
            'description' => 'nullable|string|max:1000'
        ]);

        $setting = DaysOffSetting::create([
            'days_per_week' => $request->days_per_week,
            'start_week_day' => $request->start_week_day,
            'description' => $request->description,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id()
        ]);

        return $this->sendResponse($setting, 'Days off settings saved successfully.');
    }

    public function update(Request $request, DaysOffSetting $daysOffSetting): JsonResponse
    {
        $request->validate([
            'days_per_week' => 'required|integer|min:1|max:7',
            'start_week_day' => 'required|integer|min:1|max:7',
            'description' => 'nullable|string|max:1000'
        ]);

        $daysOffSetting->update([
            'days_per_week' => $request->days_per_week,
            'start_week_day' => $request->start_week_day,
            'description' => $request->description,
            'updated_by' => Auth::id()
        ]);

        return $this->sendResponse($daysOffSetting, 'Days off settings updated successfully.');
    }
}