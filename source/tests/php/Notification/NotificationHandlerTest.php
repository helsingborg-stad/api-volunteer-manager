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
        parent::setUp();
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

    /**
     * @dataProvider notificationProvider
     */
    public function testFindMatchingEvents(array $notifications, array $searchCriteria, array $expectedMatches): void
    {
        $this->assertEquals(
            $expectedMatches,
            $this->notificationHandler->findMatchingEvents($notifications, $searchCriteria)
        );
    }

    public function notificationProvider(): array
    {
        return [
            [
                [
                    ["oldValue" => 1, "newValue" => 2],
                    ["oldValue" => 3, "newValue" => 4],
                    ["oldValue" => 5, "newValue" => 6],
                ],
                [
                    ["oldValue" => 1, "newValue" => 2],
                    ["oldValue" => 5, "newValue" => 6],
                ],
                [
                    ["oldValue" => 1, "newValue" => 2],
                    ["oldValue" => 5, "newValue" => 6],
                ],
            ],
            [
                [
                    ["oldValue" => "A", "newValue" => "B"],
                    ["oldValue" => "C", "newValue" => "D"],
                    ["oldValue" => "E", "newValue" => "F"],
                ],
                [
                    ["oldValue" => "C", "newValue" => "D"],
                    ["oldValue" => "E", "newValue" => "F"],
                ],
                [
                    ["oldValue" => "C", "newValue" => "D"],
                    ["oldValue" => "E", "newValue" => "F"],
                ],
            ],
        ];
    }

    public function testCombineOldAndNewValues(): void
    {
        $oldValues = [1, 2, 3];
        $newValues = [4, 5, 6];

        $expectedCombinedValues = [
            ["oldValue" => 1, "newValue" => 4],
            ["oldValue" => 2, "newValue" => 5],
            ["oldValue" => 3, "newValue" => 6],
        ];

        $this->assertEquals(
            $expectedCombinedValues,
            $this->notificationHandler->combineOldAndNewValues($oldValues, $newValues)
        );
    }

}