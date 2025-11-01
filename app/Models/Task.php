<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * @property-read \App\Models\User|null $user
 */
class Task extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status', 
        'due_date',
        'is_completed',
        'user_id',
        'google_event_id',
        'parent_id', 
    ];

    /**
     * Activity Log-এর জন্য কনফিগারেশন।
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'priority', 'status', 'due_date', 'is_completed'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Task has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }
    
    /**
     * Get the user that owns the task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- নতুন রিলেশনশিপ যোগ করা হয়েছে ---

    /**
     * Get the parent task that this sub-task belongs to.
     */
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Get all of the sub-tasks for the task.
     */
    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }
}
