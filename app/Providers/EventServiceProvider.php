<?php

namespace App\Providers;

use App\Events\TaskCreated;
use App\Listeners\CreateGoogleCalendarEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\TaskDeleted;
use App\Listeners\DeleteGoogleCalendarEvent;
use App\Models\Task;
use App\Events\TaskUpdated;
use App\Listeners\UpdateGoogleCalendarEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // টাস্ক তৈরি হওয়ার পর ক্যালেন্ডার ইভেন্ট তৈরির জন্য এই ম্যাপিং যোগ করা হয়েছে
        TaskCreated::class => [
            CreateGoogleCalendarEvent::class,
        ],

        TaskDeleted::class => [
            DeleteGoogleCalendarEvent::class,
        ],

        TaskUpdated::class => [
            UpdateGoogleCalendarEvent::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}