<?php
namespace Botble\ACL\Notifications;
use Botble\Base\Facades\EmailHandler;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;


class PasswordResetSuccess extends Notification
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $emailHandler = EmailHandler::setModule('acl')
            ->setTemplate('password-reset-success') // custom template name
            ->setType('core') // core module type
            ->addTemplateSettings('acl', config('core.acl.email', []))
            ->setVariableValues([
                'user_name' => $notifiable->name,
                'site_name' => config('app.name'),
            ]);

        return (new MailMessage())
            ->view(['html' => new HtmlString($emailHandler->getContent())])
            ->subject($emailHandler->getSubject());
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
