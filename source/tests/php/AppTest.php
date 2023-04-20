<?php

namespace VolunteerManager;

use PluginTestCase\PluginTestCase;

class AppTest extends PluginTestCase
{
    public function testAddHooks()
    {
        $app = new App();
        self::assertNotFalse(has_action('plugins_loaded', [$app, 'init']));
    }
}
