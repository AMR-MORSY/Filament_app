<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * The email a new staff member receives instead of "Reset Password Notification",
 * which reads oddly to someone who has never had an account.
 *
 * The link is built by ResetPassword::createUrlUsing() in AppServiceProvider, so
 * this class does not override resetUrl(). That keeps one source of truth for the
 * URL and, more importantly, keeps it panel-independent: a staff member whose
 * role changes between invitation and reset can still set their password.
 *
 * Deliberately NOT ShouldQueue, unlike Filament ResetPassword: the queue runs on
 * the database connection here, so a queued invitation would sit unsent until a
 * worker happened to be running, while the admin was told it had gone.
 */
class StaffInvitation extends BaseNotification
{
    public function toMail($notifiable): MailMessage
    {
        $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Your Rowan Clinic account is ready')
            ->greeting("Hello {$notifiable->name},")
            ->line('An account has been created for you on the Rowan Clinic staff system.')
            ->line('Choose your own password below. Nobody else knows it, not even whoever set the account up.')
            ->action('Set your password', $this->resetUrl($notifiable))
            ->line("This link expires in {$minutes} minutes. If it has run out, ask an administrator to send you a new invitation.")
            ->salutation('Rowan Clinic');
    }
}
