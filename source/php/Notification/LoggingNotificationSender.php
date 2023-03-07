<?php

namespace VolunteerManager\Notification;

class LoggingNotificationSender implements NotificationSenderInterface
{
    private NotificationSenderInterface $sender;
    private $logger;

    public function __construct(NotificationSenderInterface $sender, ?callable $logger = null)
    {
        $this->sender = $sender;
        $this->logger = $logger ?? fn($message) => error_log($message);
    }

    public function send(string $to, string $from, string $message, string $subject = ''): bool
    {
        $success = $this->sender->send($to, $from, $message, $subject);
        if (!$success) {
            call_user_func($this->logger, "Failed to send notification to \"{$to}\"");
        } else {
            call_user_func($this->logger, "Sent notification to \"{$to}\" with message: \"{$message}\"");
        }
        return $success;
    }
}
