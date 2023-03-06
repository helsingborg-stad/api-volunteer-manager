<?php

namespace VolunteerManager\Notification;

interface NotificationSender
{
    public function send(string $to, string $from, string $message): bool;
}

class EmailNotificationSender implements NotificationSender
{
    private $emailService;

    public function __construct(callable $emailService)
    {
        $this->emailService = $emailService;
    }

    public function send(string $to, string $from, string $message, string $subject = '', array $headers = [], array $attachments = []): bool
    {
        $defaultHeaders = array("Content-Type: text/html; charset=UTF-8");
        $defaultHeaders[] = !empty($from) ? "From: {$from}" : '';
        $headers = !empty($headers) ? $headers : $defaultHeaders;
        return call_user_func($this->emailService, $to, $subject, $message, $headers, $attachments);
    }
}

class LoggingNotificationSender implements NotificationSender
{
    private NotificationSender $sender;
    private $logger;

    public function __construct(NotificationSender $sender, ?callable $logger = null)
    {
        $this->sender = $sender;
        $this->logger = $logger ?? fn($message) => error_log($message);

    }

    public function send(string $to, string $from, string $message, string $subject = ''): bool
    {
        $success = $this->sender->send($to, $from, $message, $subject);
        if (!$success) {
            call_user_func($this->logger, "Failed to send notification to {$to}");
        } else {
            call_user_func($this->logger, "Sent notification to \"{$to}\" with message: \"{$message}\"");
        }
        return $success;
    }
}
