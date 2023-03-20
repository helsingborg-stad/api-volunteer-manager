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
        "Some test" => [
            'key' => 'post_approved',
            'taxonomy' => 'custom_taxonomy',
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
        "Other test" => [
            'key' => 'other',
            'taxonomy' => 'other_taxonomy',
            'oldValue' => 'old',
            'newValue' => 'new',
            'message' => [
                'subject' => 'Subject',
                'content' => 'Content',
            ],
            'rule' => [
                'key' => 'foo_key',
                'value' => 'bar',
                'operator' => 'NOT_EQUAL'
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

    /**
     * @dataProvider getNotificationsProvider
     */
    public function testGetTaxonomyNotifications($expected)
    {
        $actual = $this->notificationHandler->getNotificationsByTaxonomy(self::$config, 'custom_taxonomy');
        $this->assertEquals($expected, $actual);
    }

    public function getNotificationsProvider(): array
    {
        return [[[[
            'key' => 'post_approved',
            'taxonomy' => 'custom_taxonomy',
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
        ]]]];
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

    /**
     * @dataProvider oldAndNewValuesProvider
     */
    public function testCombineOldAndNewValues($expected, $oldValues, $newValues): void
    {
        $this->assertEquals(
            $expected,
            $this->notificationHandler->combineOldAndNewValues($oldValues, $newValues)
        );
    }

    public function oldAndNewValuesProvider(): array
    {
        return [
            "Existing new and old values" => [
                [
                    ["oldValue" => 1, "newValue" => 4],
                    ["oldValue" => 2, "newValue" => 5],
                    ["oldValue" => 3, "newValue" => 6],
                ],
                [1, 2, 3],
                [4, 5, 6]
            ],
            "Empty old value" => [
                [["oldValue" => null, "newValue" => 1]], [], [1]
            ],
            "Empty new value" => [
                [], [1], []
            ],
        ];
    }


}