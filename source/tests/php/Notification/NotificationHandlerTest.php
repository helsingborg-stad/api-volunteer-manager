<?php

namespace php\Notification;

use PluginTestCase\PluginTestCase;
use VolunteerManager\Notification\NotificationHandler;
use VolunteerManager\Notification\NotificationSenderInterface;

class NotificationHandlerTest extends PluginTestCase
{
    public function testShouldScheduleNotification()
    {
        $sender = new Sender();
        $notificationsHandler = new NotificationHandler([], $sender);

        $postId = 1;
        $rule = [
            'key' => 'test_key',
            'operator' => 'EQUAL',
            'value' => 'test_value'
        ];
        $getFieldFn = fn($key, $postId) => 'test_value';

        // Test case when rule is empty
        $eventRule = [];
        $result = $notificationsHandler->shouldScheduleNotification($postId, $eventRule, $getFieldFn);
        $this->assertTrue($result);

        // Test case when operator is EQUAL and metaValue matches value
        $eventRule = $rule;
        $result = $notificationsHandler->shouldScheduleNotification($postId, $eventRule, $getFieldFn);
        $this->assertTrue($result);

        // Test case when operator is NOT_EQUAL and metaValue match value
        $eventRule['operator'] = 'NOT_EQUAL';
        $result = $notificationsHandler->shouldScheduleNotification($postId, $eventRule, $getFieldFn);
        $this->assertFalse($result);

        // Test case when operator is NOT_EQUAL and metaValue does not match value
        $eventRule['value'] = 'test_value_not_equal';
        $result = $notificationsHandler->shouldScheduleNotification($postId, $eventRule, $getFieldFn);
        $this->assertTrue($result);

        // Test case when operator is unrecognized
        $eventRule['operator'] = 'INVALID_OPERATOR';
        $result = $notificationsHandler->shouldScheduleNotification($postId, $eventRule, $getFieldFn);
        $this->assertFalse($result);
    }
}

class Sender implements NotificationSenderInterface
{
    public function send(string $to, string $from, string $message): bool
    {
        return true;
    }
}