<?php

namespace PluginTestCase;

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Brain\Monkey;
use Brain\Monkey\Functions;

class PluginTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Setup which calls \WP_Mock setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();

        Functions\stubs(
            [
                '__' => null,
                '_e' => null,
                '_n' => null,
                '_x' => null,
            ]
        );

        if (!defined('AVM_TEXT_DOMAIN')) {
            define('AVM_TEXT_DOMAIN', 'avm-text-domain');
        }
    }

    /**
     * Teardown which calls \WP_Mock tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
