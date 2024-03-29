<?php

namespace php;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Admin;

class AdminTest extends PluginTestCase
{
    private $admin;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = new Admin();
    }

    public function testAddHooks()
    {
        $this->admin->addHooks();
        self::assertNotFalse(has_action('acf/init', [$this->admin, 'addOptionsPage']));
        self::assertNotFalse(has_filter('get_sample_permalink_html', [$this->admin, 'replacePermalink']));
        self::assertNotFalse(has_action('admin_enqueue_scripts', [$this->admin, 'enqueueStyles']));
        self::assertNotFalse(has_action('admin_enqueue_scripts', [$this->admin, 'enqueueScripts']));
        self::assertNotFalse(has_action('after_setup_theme', [$this->admin, 'themeSupport']));
    }

    public function testReplacePermalink()
    {
        $post = new \stdClass();
        $post->ID = 99;
        $post->post_type = 'post_type';
        Functions\when('home_url')->justReturn('https://home.url');
        self::assertEquals(
            '<strong>API-url:</strong> <a href="https://home.url/json/wp/v2/post_type/99" target="_blank">https://home.url/json/wp/v2/post_type/99</a>',
            $this->admin->replacePermalink(
                'return', $post->ID, 'new_title', 'new_slug', $post
            )
        );
    }

    public function testAddOptionsPage()
    {
        Functions\expect('acf_add_options_sub_page')->once()->withAnyArgs();
        $this->admin->addOptionsPage();
    }

    public function testEnqueueStyles()
    {
        Functions\expect('wp_register_style')->once();
        Functions\expect('wp_enqueue_style')->once()->with('api-volunteer-manager-css');
        $this->admin->enqueueStyles();
    }

    public function testEnqueueScripts()
    {
        Functions\expect('wp_register_script')->once();
        Functions\expect('wp_enqueue_script')->once()->with('api-volunteer-manager-js');
        $this->admin->enqueueScripts();
    }
}