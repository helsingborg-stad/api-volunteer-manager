<?php


namespace php\PostType\Application;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\PostType\Application\ApplicationNotificationFilters;

class ApplicationNotificationFiltersTest extends PluginTestCase
{
    private ApplicationNotificationFilters $applicationNotificationFilters;
    private object $post;

    public function setUp(): void
    {
        parent::setUp();
        $this->post = new \stdClass();
        $this->post->ID = 99;
        $this->applicationNotificationFilters = new ApplicationNotificationFilters();
    }

    public function testAddHooks()
    {
        $this->applicationNotificationFilters->addHooks();
        self::assertNotFalse(has_filter('avm_admin_external_application_new_notification', [$this->applicationNotificationFilters, 'populateNotificationReceiverWithAdmin']));
        self::assertNotFalse(has_filter('avm_admin_external_application_new_notification', [$this->applicationNotificationFilters, 'populateAdminNotificationWithContent']));
        self::assertNotFalse(has_filter('avm_external_application_new_notification', [$this->applicationNotificationFilters, 'populateReceiverWithSubmitter']));
        self::assertNotFalse(has_filter('avm_external_application_new_notification', [$this->applicationNotificationFilters, 'populateApplicationWithContent']));
    }

    public function testPopulateAdminNotificationWithContent()
    {
        $args = ['to' => '', 'from' => '', 'subject' => 'Subject example', 'content' => 'Lorem ipsum %s sit amet %s'];
        $expectedResult = ['to' => '', 'from' => '', 'subject' => 'Subject example', 'content' => 'Lorem ipsum Foo Bar sit amet https://foo.bar'];
        Functions\when('get_field')->justReturn((object)['ID' => 123, 'post_title' => 'Foo Bar']);
        Functions\when('get_edit_post_link')->justReturn('https://foo.bar');
        $this->assertEquals(
            $expectedResult,
            $this->applicationNotificationFilters->populateAdminNotificationWithContent($args, $this->post->ID)
        );
    }

    public function testPopulateReceiverWithSubmitter()
    {
        $args = ['to' => '', 'from' => '', 'subject' => 'Subject example', 'content' => 'Lorem ipsum %s sit amet %s'];
        $expectedResult = ['to' => 'foo@bar.com', 'from' => '', 'subject' => 'Subject example', 'content' => 'Lorem ipsum %s sit amet %s'];
        Functions\expect('get_field')->times(2)->andReturn((object)['ID' => 1], 'foo@bar.com');
        $this->assertEquals(
            $expectedResult,
            $this->applicationNotificationFilters->populateReceiverWithSubmitter($args, $this->post->ID)
        );
    }

    public function testPopulateApplicationWithContent()
    {
        $args = ['to' => '', 'from' => '', 'subject' => 'Subject example', 'content' => 'Lorem ipsum %s sit amet %s'];
        $expectedResult = ['to' => '', 'from' => '', 'subject' => 'Subject example', 'content' => 'Lorem ipsum Foo sit amet Bar'];
        Functions\expect('get_field')->times(3)->andReturn((object)['ID' => 1], 'Foo', (object)['post_title' => 'Bar']);
        $this->assertEquals(
            $expectedResult,
            $this->applicationNotificationFilters->populateApplicationWithContent($args, $this->post->ID)
        );
    }
}