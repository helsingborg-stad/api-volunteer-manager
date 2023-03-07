<?php

namespace php\Notification;

use PluginTestCase\PluginTestCase;
use VolunteerManager\Notification\NotificationHandler;
use VolunteerManager\Notification\NotificationHandlerInterface;

class NotificationHandlerTest extends PluginTestCase
{
    private NotificationHandlerInterface $notificationHandler;

    public function setUp(): void
    {
        $emailServiceMock = $this->getMockBuilder('VolunteerManager\Notification\EmailNotificationSender')
            ->disableOriginalConstructor()
            ->getMock();
        $this->notificationHandler = new NotificationHandler(self::$config, $emailServiceMock);
    }

    private static array $config = [
        'post_type' => [
            'taxonomy' => [
                [
                    'key' => 'post_approved',
                    'oldValue' => 'foo',
                    'newValue' => 'bar',
                    'message' => [
                        'subject' => 'Subject',
                        'content' => 'Content',
                    ],
                    'rule' => [
                        'key' => 'foo_key',
                        'value' => 'foo',
                        'operator' => 'EQUAL'
                    ]
                ],
            ]
        ]
    ];

    public function testShouldScheduleNotificationWithEmptyRule()
    {
        $eventRule = [];
        $getFieldFn = fn($key, $postId) => 'test_value';
        $result = $this->notificationHandler->shouldScheduleNotification(1, $eventRule, $getFieldFn);
        $this->assertTrue($result);
    }

    public function testShouldScheduleNotificationWithEqualOperator()
    {
        $eventRule = [
            'key' => 'test_key',
            'operator' => 'EQUAL',
            'value' => 'test_value'
        ];
        $getFieldFn = fn($key, $postId) => 'test_value';
        $result = $this->notificationHandler->shouldScheduleNotification(1, $eventRule, $getFieldFn);
        $this->assertTrue($result);
    }

    public function testShouldScheduleNotificationWithNotEqualOperator()
    {
        $eventRule = [
            'key' => 'test_key',
            'operator' => 'NOT_EQUAL',
            'value' => 'test_value'
        ];
        $getFieldFn = fn($key, $postId) => 'test_value';
        $result = $this->notificationHandler->shouldScheduleNotification(1, $eventRule, $getFieldFn);
        $this->assertFalse($result);
    }

    public function testShouldScheduleNotificationWithNotEqualOperatorAndNonMatchingValue()
    {
        $postId = 1;
        $eventRule = [
            'key' => 'test_key',
            'operator' => 'NOT_EQUAL',
            'value' => 'test_value_not_equal'
        ];
        $getFieldFn = fn($key, $postId) => 'test_value';
        $result = $this->notificationHandler->shouldScheduleNotification($postId, $eventRule, $getFieldFn);
        $this->assertTrue($result);
    }

    public function testShouldScheduleNotificationWithInvalidOperator()
    {
        $eventRule = [
            'key' => 'test_key',
            'operator' => 'INVALID_OPERATOR',
            'value' => 'test_value'
        ];
        $getFieldFn = fn($key, $postId) => 'test_value';
        $result = $this->notificationHandler->shouldScheduleNotification(1, $eventRule, $getFieldFn);
        $this->assertFalse($result);
    }

    public function testGetNotifications()
    {
        $result = $this->notificationHandler->getNotifications('post_type', 'taxonomy');
        $this->assertEquals($result, self::$config['post_type']['taxonomy']);

        $result = $this->notificationHandler->getNotifications('post_type', 'unknown_taxonomy');
        $this->assertEquals([], $result);
    }
}