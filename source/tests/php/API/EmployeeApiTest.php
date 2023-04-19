<?php

namespace php\API;

use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Brain\Monkey\Functions;
use Mockery;
use PluginTestCase\PluginTestCase;
use VolunteerManager\API\Api;
use VolunteerManager\API\EmployeeApi;
use VolunteerManager\Employee\EmployeeApiValidatorInterface;

class EmployeeApiTest extends PluginTestCase
{
    private EmployeeApi $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new EmployeeApi();
    }

    /**
     * @throws ExpectationArgsRequired
     */
    public function testRegisterPostEndpoint()
    {
        $endpoint = '/test-endpoint/';
        $callback = function () {
            return 'Test callback';
        };
        $validator = Mockery::mock(EmployeeApiValidatorInterface::class);
        $namespace = 'volunteer-manager/v1';

        Functions\expect('register_rest_route')
            ->once()
            ->with(
                $namespace,
                $endpoint,
                Mockery::on(function ($arg) use ($callback) {
                    return $arg['methods'] === 'POST'
                        && is_callable($arg['callback'])
                        && $arg['callback']() === $callback()
                        && is_callable($arg['permission_callback']);
                })
            );

        $this->api->registerPostEndpoint($endpoint, $callback, $validator, $namespace);
    }
}
