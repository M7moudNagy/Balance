<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SessionReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    public function via($notifiable)
    {
        return ['mail']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تذكير بجلسة العلاج')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('عندك جلسة مع دكتور/مريض اليوم الساعة: ' . $this->session->time)
            ->line('رابط الجلسة: ' . $this->session->platform_link)
            ->line('تاريخ الجلسة: ' . $this->session->date->format('Y-m-d'))
            ->line('يرجى الدخول قبل الموعد بـ 5 دقائق.')
            ->salutation('مع تحيات فريق Balance');
    }

    public function toArray($notifiable)
    {
        return [
            'session_id' => $this->session->id,
            'date' => $this->session->date,
            'time' => $this->session->time,
            'link' => $this->session->platform_link,
        ];
    }
}
