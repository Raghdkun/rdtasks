<?php

namespace App\Console\Commands;

use App\Models\EmployeeClocking;
use Illuminate\Console\Command;
use Carbon\Carbon;

class FixClockingData extends Command
{
    protected $signature = 'clocking:fix-data';
    protected $description = 'Fix incorrect work time and break time calculations in employee clocking records';

    public function handle()
    {
        $this->info('Starting to fix clocking data...');
        
        $clockings = EmployeeClocking::whereNotNull('clock_out_time')->get();
        $fixed = 0;
        
        foreach ($clockings as $clocking) {
            $originalWorkMinutes = $clocking->total_work_minutes;
            
            // Recalculate break time from break sessions
            $totalBreakMinutes = 0;
            if ($clocking->break_sessions) {
                foreach ($clocking->break_sessions as $session) {
                    if (isset($session['start_time']) && isset($session['end_time'])) {
                        $startTime = Carbon::parse($session['start_time']);
                        $endTime = Carbon::parse($session['end_time']);
                        $totalBreakMinutes += $endTime->diffInMinutes($startTime);
                    }
                }
            }
            
            // Recalculate work time
            $totalMinutes = $clocking->clock_in_time->diffInMinutes($clocking->clock_out_time);
            $correctWorkMinutes = max(0, $totalMinutes - $totalBreakMinutes);
            
            if ($originalWorkMinutes !== $correctWorkMinutes || $clocking->total_break_minutes !== $totalBreakMinutes) {
                $clocking->total_break_minutes = $totalBreakMinutes;
                $clocking->total_work_minutes = $correctWorkMinutes;
                $clocking->save();
                $fixed++;
                
                $this->line("Fixed record ID {$clocking->id}: Work time {$originalWorkMinutes} -> {$correctWorkMinutes} minutes");
            }
        }
        
        $this->info("Fixed {$fixed} clocking records.");
        return 0;
    }
}