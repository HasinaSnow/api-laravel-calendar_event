<?php

namespace App\Notifications;

use App\Helpers\AboutCurrentUser;
use App\Http\Requests\TaskAttributeRequest;
use App\Models\Event;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAttributedUpdatedNotification extends Notification
{
    use Queueable;

    private $task;
    private $event;
    private $aboutCurrentUser;
    private $taskAttributeRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, Event $event, TaskAttributeRequest $taskAttributeRequest, AboutCurrentUser $aboutCurrentUser)
    {
        $this->task = $task;
        $this->event = $event;
        $this->taskAttributeRequest = $taskAttributeRequest;
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
        $user = ($notifiable->id === $this->aboutCurrentUser->id()) ? 'You' : $this->aboutCurrentUser->name();
        $message = '"' . $user . '" updated the task assignement "' . $this->task->name . '" to "' . User::find($this->taskAttributeRequest->attribute_to)->name . '"  for the event ' . $this->event->date . '.';

        return [
            'task_id' => $this->task->id,
            'event_id' => $this->event->id,
            'message' => $message
        ];
    }
}
