<?php

namespace php\PostType\Assignment;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\PostType\Assignment\Notifications;

class NotificationsTest extends PluginTestCase
{
    private Notifications $assignmentNotifications;
    private object $post;

    public function setUp(): void
    {
        parent::setUp();

        $this->post = (object)['ID' => 123, 'post_title' => 'Some title'];
        $this->assignmentNotifications = new Notifications();
    }

    public function testAddHooks()
    {
        $this->assignmentNotifications->addHooks();
        self::assertNotFalse(has_filter('avm_notification', [$this->assignmentNotifications, 'populateNotificationSender']));
        self::assertNotFalse(has_filter('avm_external_assignment_approved_notification', [$this->assignmentNotifications, 'populateNotificationReceiverWithSubmitter']));
        self::assertNotFalse(has_filter('avm_external_assignment_approved_notification', [$this->assignmentNotifications, 'populateAssignmentApprovedWithMessage']));
        self::assertNotFalse(has_filter('avm_external_assignment_denied_notification', [$this->assignmentNotifications, 'populateNotificationReceiverWithSubmitter']));
        self::assertNotFalse(has_filter('avm_external_assignment_denied_notification', [$this->assignmentNotifications, 'populateAssignmentDeniedWithMessage']));
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

    public function testPopulateAssignmentApprovedWithMessage()
    {
        $args = ['subject' => '', 'content' => ''];
        Functions\expect('get_post')->andReturn($this->post);
        Functions\expect('get_post_meta')->andReturn('Foo');
        $result = $this->assignmentNotifications->populateAssignmentApprovedWithMessage($args, $this->post->ID);
        $this->assertContains('Your assignment "Some title" is approved', $result);
    }

    public function testPopulateAssignmentDeniedWithMessage()
    {
        $args = ['subject' => '', 'content' => ''];
        Functions\expect('get_post')->andReturn($this->post);
        Functions\expect('get_post_meta')->andReturn('Foo');
        $result = $this->assignmentNotifications->populateAssignmentDeniedWithMessage($args, $this->post->ID);
        $this->assertContains('Your assignment "Some title" is denied', $result);
    }
}