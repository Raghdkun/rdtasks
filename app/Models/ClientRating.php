<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ClientRating
 *
 * @property int $id
 * @property int $task_id
 * @property int $client_id
 * @property int $rated_by
 * @property int $rating
 * @property string|null $comment
 * @property int|null $edited_by
 * @property Carbon|null $edited_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ClientRating extends Model
{
    protected $fillable = [
        'task_id',
        'client_id',
        'rated_by',
        'rating',
        'comment',
        'edited_by',
        'edited_at'
    ];

    protected $casts = [
        'task_id' => 'integer',
        'client_id' => 'integer',
        'rated_by' => 'integer',
        'rating' => 'integer',
        'edited_by' => 'integer',
        'edited_at' => 'datetime',
    ];

    /**
     * Get the task that this rating belongs to
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the client that this rating belongs to
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user who created this rating
     */
    public function ratedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_by');
    }

    /**
     * Get the user who last edited this rating
     */
    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Get rating as percentage
     */
    public function getRatingPercentageAttribute(): float
    {
        return ($this->rating / 50) * 100;
    }

    /**
     * Get rating display with stars
     */
    public function getRatingDisplayAttribute(): string
    {
        $stars = round(($this->rating / 50) * 5);
        return str_repeat('★', $stars) . str_repeat('☆', 5 - $stars) . " ({$this->rating}/50)";
    }
}