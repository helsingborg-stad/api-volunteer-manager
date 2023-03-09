<?php

namespace VolunteerManager\Notification;

interface NotificationSenderInterface
{
    public function send(string $to, string $from, string $message): bool;
}