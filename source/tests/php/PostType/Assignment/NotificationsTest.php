<?php

namespace php\PostType\Assignment;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\PostType\Assignment\Notifications;

class NotificationsTest extends PluginTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->post = new \stdClass();
        $this->post->ID = 123;
        $this->assignmentNotifications = new Notifications();
    }

    /**
     * @dataProvider notificationReceiverProvider
     */
    public function testPopulateNotificationReceiverWithSubmitter($args, $getPostMetaResult, $expectedResult)
    {
        Functions\when('get_post_meta')->justReturn($getPostMetaResult);
        $this->assertEquals(
            $expectedResult,
            $this->assignmentNotifications->populateNotificationReceiverWithSubmitter($args, $this->post->ID)
        );
    }

    public function notificationReceiverProvider(): array
    {
        return [
            [
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                'foo@email.bar',
                ['to' => 'foo@email.bar', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
            [
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                null,
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
        ];
    }

    /**
     * @dataProvider notificationSenderProvider
     */
    public function testPopulateNotificationSender($args, $getFieldResult, $expectedResult)
    {
        Functions\when('get_field')->justReturn($getFieldResult);
        $this->assertEquals($expectedResult, $this->assignmentNotifications->populateNotificationSender($args, $this->post->ID));
    }

    public function notificationSenderProvider(): array
    {
        return [
            [
                ['to' => 'foo@bar.receiver', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                ['email' => 'foo@bar.email', 'name' => 'Foo Bar'],
                ['to' => 'foo@bar.receiver', 'from' => 'Foo Bar <foo@bar.email>', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
            [
                ['to' => 'foo@bar.receiver', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                ['email' => 'foo@bar.email', 'name' => null],
                ['to' => 'foo@bar.receiver', 'from' => 'foo@bar.email', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
            [
                ['to' => 'foo@bar.receiver', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                ['email' => null, 'name' => null],
                ['to' => 'foo@bar.receiver', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ]
        ];
    }
}