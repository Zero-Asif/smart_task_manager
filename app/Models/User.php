<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\WelcomeAndVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'gender',
        'password',
        'notification_preferences',
        'google_id',
        'google_access_token',
        'google_refresh_token',
        'preferences',
    ];

    protected $hidden = [ 'password', 'remember_token' ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'notification_preferences' => 'array', 
            'password' => 'hashed',
            'preferences' => 'array',
        ];
    }
    
    public function tasks() { return $this->hasMany(Task::class); }

    public function sendEmailVerificationNotification() { $this->notify(new WelcomeAndVerifyEmail); }
}