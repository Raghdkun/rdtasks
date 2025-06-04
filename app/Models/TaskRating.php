<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\TaskRating
 *
 * @property int $id
 * @property int $task_id
 * @property int $rated_by
 * @property int $code_quality
 * @property int $delivery_output
 * @property int $time_score
 * @property int $collaboration
 * @property int $complexity_urgency
 * @property string|null $comments
 * @property int|null $edited_by
 * @property \Illuminate\Support\Carbon|null $edited_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class TaskRating extends Model
{
    protected $fillable = [
        'task_id',
        'rated_by',
        'code_quality',
        'delivery_output',
        'time_score',
        'collaboration',
        'complexity_urgency',
        'comments',
        'edited_by',
        'edited_at'
    ];

    protected $casts = [
        'task_id' => 'integer',
        'rated_by' => 'integer',
        'code_quality' => 'integer',
        'delivery_output' => 'integer',
        'time_score' => 'integer',
        'collaboration' => 'integer',
        'complexity_urgency' => 'integer',
        'edited_by' => 'integer',
        'edited_at' => 'datetime',
    ];

    /**
     * Get the task that owns the rating.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who rated the task.
     */
    public function ratedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_by');
    }

    /**
     * Get the user who last edited the rating.
     */
    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Calculate the average rating.
     */
    public function getAverageRatingAttribute(): float
    {
        return round((
            $this->code_quality +
            $this->delivery_output +
            $this->time_score +
            $this->collaboration +
            $this->complexity_urgency
        ) / 5, 2);
    }

    /**
     * Get rating criteria labels.
     */
    public static function getRatingCriteria(): array
    {
        return [
            'code_quality' => 'Code/Task Quality',
            'delivery_output' => 'Delivery Output',
            'time_score' => 'Time Score',
            'collaboration' => 'Collaboration',
            'complexity_urgency' => 'Complexity & Urgency'
        ];
    }

    /**
     * Check if the rating has been edited.
     */
    public function getIsEditedAttribute(): bool
    {
        return !is_null($this->edited_by) && !is_null($this->edited_at);
    }
}