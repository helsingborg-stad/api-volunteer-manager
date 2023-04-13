<?php

namespace VolunteerManager;

use VolunteerManager\App;

use Brain\Monkey\Functions;
use Mockery;

class AppTest extends \PluginTestCase\PluginTestCase
{
    public function testAddHooks()
    {
        $app = new App();
        self::assertNotFalse(has_action('admin_enqueue_scripts', [$app, 'enqueueStyles']));
        self::assertNotFalse(has_action('admin_enqueue_scripts', [$app, 'enqueueScripts']));
        self::assertNotFalse(has_action('plugins_loaded', [$app, 'init']));
        self::assertNotFalse(has_action('after_setup_theme', [$app, 'themeSupport']));
        self::assertNotFalse(has_filter('acf/fields/google_map/api', [$app, 'setGoogleApiKey']));
    }

    public function testEnqueueStyles()
    {
        Functions\expect('wp_register_style')->once();
        Functions\expect('wp_enqueue_style')->once()->with('api-volunteer-manager-css');

        $app = new App();

        $app->enqueueStyles();
    }

    public function testEnqueueScripts()
    {
        Functions\expect('wp_register_script')->once();
        Functions\expect('wp_enqueue_script')->once()->with('api-volunteer-manager-js');

        $app = new App();

        $app->enqueueScripts();
    }
}
