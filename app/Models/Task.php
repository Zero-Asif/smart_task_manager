<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $google_event_id
 * @property string $title
 * @property string|null $description
 * @property string $due_date
 * @property string $priority
 * @property int $is_completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereGoogleEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withoutTrashed()
 * @mixin \Eloquent
 */
class Task extends Model
{
    use HasFactory, SoftDeletes, LogsActivity; // LogsActivity trait যোগ করুন

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'is_completed',
        'user_id',
        'google_event_id',
    ];

    // Activity Log-এর জন্য কনফিগারেশন
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'priority', 'due_date', 'is_completed']) // কোন কোন ফিল্ডের পরিবর্তন লগ করা হবে
            ->logOnlyDirty() // শুধুমাত্র পরিবর্তিত ডেটা লগ করবে
            ->setDescriptionForEvent(fn(string $eventName) => "Task has been {$eventName}") // লগের বিবরণ
            ->dontSubmitEmptyLogs(); // যদি কোনো পরিবর্তন না হয়, তাহলে লগ তৈরি করবে না
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}