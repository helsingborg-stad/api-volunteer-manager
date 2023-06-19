<?php

namespace php\PostType\Assignment;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\PostType\Assignment\AssignmentNotificationFilters;

class AssignmentNotificationFiltersTest extends PluginTestCase
{
    private AssignmentNotificationFilters $assignmentNotifications;
    private object $post;

    public function setUp(): void
    {
        parent::setUp();
        $this->post = (object)['ID' => 123, 'post_title' => 'Some title'];
        $this->assignmentNotifications = new AssignmentNotificationFilters();
    }

    public function testAddHooks()
    {
        $this->assignmentNotifications->addHooks();
        self::assertNotFalse(has_filter('avm_notification', [$this->assignmentNotifications, 'populateNotificationSender']));
        self::assertNotFalse(has_filter('avm_external_assignment_approved_notification', [$this->assignmentNotifications, 'populateNotificationReceiverWithSubmitter']));
        self::assertNotFalse(has_filter('avm_external_assignment_approved_notification', [$this->assignmentNotifications, 'populateStatusNotificationWithContent']));
        self::assertNotFalse(has_filter('avm_external_assignment_denied_notification', [$this->assignmentNotifications, 'populateNotificationReceiverWithSubmitter']));
        self::assertNotFalse(has_filter('avm_external_assignment_denied_notification', [$this->assignmentNotifications, 'populateStatusNotificationWithContent']));
        self::assertNotFalse(has_filter('avm_admin_external_assignment_new_notification', [$this->assignmentNotifications, 'populateNotificationReceiverWithAdmin']));
        self::assertNotFalse(has_filter('avm_admin_external_assignment_new_notification', [$this->assignmentNotifications, 'populateAdminNotificationWithContent']));
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

    public function testPopulateStatusNotificationWithContent()
    {
        $args = ['subject' => 'Your assignment "%s" is approved', 'content' => 'Your assignment "%s" is approved'];
        Functions\expect('get_post')->andReturn($this->post);
        Functions\expect('get_post_meta')->andReturn('Foo');
        $result = $this->assignmentNotifications->populateStatusNotificationWithContent($args, $this->post->ID);
        $this->assertContains('Your assignment "Some title" is approved', $result);
    }

    public function testPopulateAdminNotificationWithContent()
    {
        $args = ['to' => '', 'from' => '', 'subject' => 'Subject example', 'content' => 'Lorem ipsum %s sit amet %s'];
        $expectedResult = ['to' => '', 'from' => '', 'subject' => 'Subject example', 'content' => 'Lorem ipsum Foo Bar sit amet https://foo.bar'];
        Functions\when('get_post')->justReturn((object)['ID' => 123, 'post_title' => 'Foo Bar']);
        Functions\when('get_admin_url')->justReturn('https://foo.bar');
        $this->assertEquals(
            $expectedResult,
            $this->assignmentNotifications->populateAdminNotificationWithContent($args, $this->post->ID)
        );
    }
}