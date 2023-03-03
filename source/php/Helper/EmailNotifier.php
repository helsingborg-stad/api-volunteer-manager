<?php

namespace VolunteerManager\Helper;

interface NotifierInterface
{
    public function send(string $to, string $from, string $message): bool;
}

class EmailNotifier implements NotifierInterface
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