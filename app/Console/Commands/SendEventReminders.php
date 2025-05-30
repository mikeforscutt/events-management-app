<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends notifications to all event attendees about upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('SendEventReminders command is being executed');
        $events = Event::with('attendees.user')
            ->whereBetween('start_time', [now(), now()->addDay()])
            ->get();
        
        $eventCount = $events->count();
        $eventLabel = Str::plural('event', $eventCount);

        $this->info('Sending ' . $eventCount . ' ' . $eventLabel . ' reminders...');

        $events->each(
            fn($event) => $event->attendees->each(
                fn($attendee) => $this->info("Notifying the user {$attendee->user->name} about the event {$event->name}")
            )
        );

        $this->info('Reminder notification sent successfully!');
    }
}
