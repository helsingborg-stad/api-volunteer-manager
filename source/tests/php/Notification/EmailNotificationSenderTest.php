<?php

namespace php\Notification;

use PluginTestCase\PluginTestCase;
use VolunteerManager\Notification\EmailNotificationSender;

class EmailNotificationSenderTest extends PluginTestCase
{
    public function testSendSuccess()
    {
        $emailService = fn($to, $subject, $message, $headers, $attachments) => true;
        $notifier = new EmailNotificationSender($emailService);
        $result = $notifier->send('test@example.com', 'sender@example.com', 'message',);
        $this->assertTrue($result);
    }
}