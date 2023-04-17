<?php

namespace php\Components;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Components\ApplicationMetaBox\ApplicationMetaBox;

class ApplicationMetaBoxTest extends PluginTestCase
{
    private $applicationMetaBox;
    private object $post;

    public function setUp(): void
    {
        parent::setUp();

        $this->post = (object)['ID' => 123, 'post_type' => 'example_post_type'];

        $this->applicationMetaBox = $this->getMockForAbstractClass(
            ApplicationMetaBox::class,
            [$this->post, 'Title', 'key']
        );
    }

    public function testRegister()
    {
        Functions\expect('add_meta_box')->once();
        Functions\when('get_posts')->justReturn([]);
        $this->applicationMetaBox->register();
    }

    /**
     * @throws \Brain\Monkey\Expectation\Exception\ExpectationArgsRequired
     */
    public function testGetApplications()
    {
        Functions\expect('get_posts')->once()
            ->with(
                [
                    'post_type' => 'application',
                    'post_status' => 'any',
                    'orderby' => 'post_date',
                    'order' => 'ASC',
                    'posts_per_page' => -1,
                    'suppress_filters' => true,
                    'meta_query' => [
                        [
                            'key' => 'key',
                            'value' => $this->post->ID,
                            'compare' => '='
                        ]
                    ]
                ])->andReturn([]);
        $this->applicationMetaBox->getApplications();
    }

    public function testRenderWithEmptyApplications()
    {
        Functions\Expect('get_field')->never();
        Functions\Expect('get_the_date')->never();

        ob_start();
        $this->applicationMetaBox->render([]);
        $output = ob_get_clean();

        $this->assertStringContainsString('No applications found.', $output);
    }

    public function testRenderWithApplications()
    {
        $applications = [(object)['ID' => 1]];

        ob_start();
        $this->applicationMetaBox->render($applications);
        $output = ob_get_clean();
        $output = str_replace("\n", "", preg_replace('/\s*(<[^>]*>)\s*/', '$1', $output));

        $expected = '<table><tr><th>Name</th><th>Date</th><th>Status</th><th></th></tr></table>';
        $this->assertEquals($expected, $output);
    }
}