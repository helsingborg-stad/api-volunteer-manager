<?php

namespace VolunteerManager\Helper;

use PluginTestCase\PluginTestCase;
use VolunteerManager\Helper\EmailNotifier as EmailNotifier;

class EmailNotifierTest extends PluginTestCase
{
    public function testSendSuccess()
    {
        $emailService = fn($to, $subject, $message, $headers, $attachments) => true;
        $notifier = new EmailNotifier($emailService);
        $result = $notifier->send('test@example.com', 'sender@example.com', 'message',);
        $this->assertTrue($result);
    }
}

