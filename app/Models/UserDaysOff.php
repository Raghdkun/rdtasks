<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserDaysOff extends Model
{
    use HasFactory;

    protected $table = 'user_days_off';

    protected $fillable = [
        'user_id',
        'week_start_date',
        'selected_days',
        'status',
        'notes',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'selected_days' => 'array',
        'week_start_date' => 'date',
        'approved_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getSelectedDaysNamesAttribute(): array
    {
        $weekDays = DaysOffSetting::getWeekDays();
        return collect($this->selected_days)->map(function($day) use ($weekDays) {
            return $weekDays[$day] ?? '';
        })->toArray();
    }

    public function scopeForCurrentWeek($query)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        return $query->where('week_start_date', $startOfWeek->format('Y-m-d'));
    }
}