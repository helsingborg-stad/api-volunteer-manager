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
}