<?php

namespace php\PostType\Employee;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\PostType\Employee\EmployeeNotifications;

class EmployeeNotificationsTest extends PluginTestCase
{
    private EmployeeNotifications $employeeNotification;
    private object $post;

    public function setUp(): void
    {
        parent::setUp();

        $this->post = new \stdClass();
        $this->post->ID = 99;

        $this->employeeNotification = new EmployeeNotifications();
    }

    /**
     * @dataProvider populateNotificationReceiverWithAdminProvider
     */
    public function testPopulateNotificationReceiverWithAdmin($args, $getFieldResult, $expectedResult)
    {
        Functions\when('get_field')->justReturn($getFieldResult);
        $this->assertEquals(
            $expectedResult,
            $this->employeeNotification->populateNotificationReceiverWithAdmin($args, $this->post->ID)
        );
    }

    public function populateNotificationReceiverWithAdminProvider(): array
    {
        return [
            [
                ['to' => '', 'from' => '', 'subject' => 'Subject', 'content' => 'Content'],
                [['email' => 'foo@admin.bar']],
                ['to' => 'foo@admin.bar', 'from' => '', 'subject' => 'Subject', 'content' => 'Content']
            ],
            [
                ['to' => '', 'from' => '', 'Subject', 'content' => 'Content'],
                [['email' => 'foo@admin.bar'], ['email' => 'bar@admin.foo']],
                ['to' => 'foo@admin.bar,bar@admin.foo', 'from' => '', 'Subject', 'content' => 'Content'],
            ],
            [
                ['to' => '', 'from' => '', 'Subject', 'content' => 'Content'],
                [['email' => 'foo@admin.bar'], ['unknown' => 'unknown']],
                ['to' => 'foo@admin.bar', 'from' => '', 'Subject', 'content' => 'Content'],
            ],
            [
                ['to' => '', 'from' => '', 'Subject', 'content' => 'Content'],
                [],
                ['to' => '', 'from' => '', 'Subject', 'content' => 'Content'],
            ],
            [
                ['to' => '', 'from' => '', 'Subject', 'content' => 'Content'],
                null,
                ['to' => '', 'from' => '', 'Subject', 'content' => 'Content'],
            ],
        ];
    }

    /**
     * @dataProvider notificationReceiverWithSubmitterProvider
     */
    public function testPopulateNotificationReceiverWithSubmitter($args, $getPostMetaResult, $expectedResult)
    {
        Functions\when('get_field')->justReturn($getPostMetaResult);
        $this->assertEquals(
            $expectedResult,
            $this->employeeNotification->populateNotificationReceiverWithSubmitter($args, $this->post->ID)
        );
    }

    public function notificationReceiverWithSubmitterProvider(): array
    {
        return [
            [
                ['to' => '', 'from' => '', 'subject' => 'Subject', 'content' => 'Content'],
                'foo@email.bar',
                ['to' => 'foo@email.bar', 'from' => '', 'subject' => 'Subject', 'content' => 'Content']
            ],
            [
                ['to' => '', 'from' => '', 'subject' => 'Subject', 'content' => 'Content'],
                null,
                ['to' => '', 'from' => '', 'subject' => 'Subject', 'content' => 'Content']
            ],
        ];
    }

    public function testPopulateNotificationWithContent()
    {
        $args = ['to' => '', 'from' => '', 'subject' => 'Subject example', 'content' => 'Lorem ipsum %s sit amet'];
        $expectedResult = ['to' => '', 'from' => '', 'subject' => 'Subject example', 'content' => 'Lorem ipsum dolor sit amet'];
        Functions\when('get_field')->justReturn('dolor');
        $this->assertEquals(
            $expectedResult,
            $this->employeeNotification->populateNotificationWithContent($args, $this->post->ID)
        );
    }
}