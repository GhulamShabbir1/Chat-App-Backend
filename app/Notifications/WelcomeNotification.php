<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Our Chat Application!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Welcome to our chat application! We\'re excited to have you join our community.')
            ->line('You can now create workspaces, join teams, and start chatting with your colleagues.')
            ->action('Get Started', url('/dashboard'))
            ->line('Thank you for choosing our platform!')
            ->salutation('Best regards, The Chat App Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Welcome to Our Chat App!',
            'message' => 'Your account has been created successfully. Welcome aboard!',
            'action_url' => '/dashboard',
            'action_text' => 'Get Started',
        ];
    }
}
