<?php

namespace App\Notifications;

use App\Helpers\AboutAllUsers;
use App\Helpers\AboutCurrentUser;
use App\Models\Event;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCheckedNotification extends Notification
{
    use Queueable;

    public $task;
    public $event;
    public $aboutCurrentUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, Event $event, AboutCurrentUser $aboutCurrentUser)
    {
        $this->task = $task;
        $this->event = $event;
        $this->aboutCurrentUser = $aboutCurrentUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $user = ($notifiable->id=== $this->aboutCurrentUser->id()) ? 'You' : $this->aboutCurrentUser->name();

        return [
            'task_id' => $this->task->id,
            'event_id' => $this->event->id,
            'message' =>'"' . $user . '" checked the task "' . $this->task->name . '" as finished.'
        ];
    }
}
