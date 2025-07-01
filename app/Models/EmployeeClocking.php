<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class EmployeeClocking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'clock_in_time',
        'clock_out_time',
        'total_work_minutes',
        'total_break_minutes',
        'break_sessions',
        'status',
        'work_date',
        'daily_work_hours',
        'notes'
    ];

    protected $casts = [
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
        'work_date' => 'date',
        'break_sessions' => 'array',
        'total_work_minutes' => 'integer',
        'total_break_minutes' => 'integer',
        'daily_work_hours' => 'integer'
    ];

    // Status constants
    const STATUS_CLOCKED_IN = 'clocked_in';
    const STATUS_ON_BREAK = 'on_break';
    const STATUS_CLOCKED_OUT = 'clocked_out';

    /**
     * Get the user that owns the clocking record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get current active break session
     */
    public function getCurrentBreakSession()
    {
        $breakSessions = $this->break_sessions ?? [];
        foreach ($breakSessions as $session) {
            if (!isset($session['end_time'])) {
                return $session;
            }
        }
        return null;
    }

    /**
     * Add a new break session
     */
    public function startBreak()
    {
        $breakSessions = $this->break_sessions ?? [];
        $breakSessions[] = [
            'start_time' => now()->toDateTimeString(),
            'end_time' => null
        ];
        $this->break_sessions = $breakSessions;
        $this->status = self::STATUS_ON_BREAK;
        $this->save();
    }

    /**
     * End current break session
     */
    public function endBreak()
    {
        $breakSessions = $this->break_sessions ?? [];
        for ($i = count($breakSessions) - 1; $i >= 0; $i--) {
            if (!isset($breakSessions[$i]['end_time'])) {
                $breakSessions[$i]['end_time'] = now()->toDateTimeString();
                
                // Calculate break duration
                $startTime = Carbon::parse($breakSessions[$i]['start_time']);
                $endTime = Carbon::parse($breakSessions[$i]['end_time']);
                $breakDuration = $endTime->diffInMinutes($startTime);
                
                $this->total_break_minutes = ($this->total_break_minutes ?? 0) + $breakDuration;
                break;
            }
        }
        
        $this->break_sessions = $breakSessions;
        $this->status = self::STATUS_CLOCKED_IN;
        
        // Recalculate work time after updating break time
        $this->calculateWorkTime();
        $this->save();
    }

    /**
     * Calculate total work time excluding breaks
     */
    public function calculateWorkTime()
    {
        if (!$this->clock_in_time) {
            return 0;
        }
        
        if (!$this->clock_out_time) {
            $endTime = now();
        } else {
            $endTime = $this->clock_out_time;
        }
        
        $totalMinutes = $this->clock_in_time->diffInMinutes($endTime);
        $this->total_work_minutes = max(0, $totalMinutes - ($this->total_break_minutes ?? 0));
        
        return $this->total_work_minutes;
    }
    
    /**
     * Calculate and save work time
     */
    public function calculateAndSaveWorkTime()
    {
        $this->calculateWorkTime();
        $this->save();
        return $this->total_work_minutes;
    }

    /**
     * Format time in hours and minutes
     */
    public function getFormattedWorkTime()
    {
        $hours = floor($this->total_work_minutes / 60);
        $minutes = $this->total_work_minutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Format break time in hours and minutes
     */
    public function getFormattedBreakTime()
    {
        $hours = floor($this->total_break_minutes / 60);
        $minutes = $this->total_break_minutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Scope for today's records
     */
    public function scopeToday($query)
    {
        return $query->whereDate('work_date', today());
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}