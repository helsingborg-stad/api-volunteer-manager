<?php

namespace php\API;

use PluginTestCase\PluginTestCase;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Response;
use Brain\Monkey\Functions;

/**
 * Run tests in separate processes and disable global state preservation
 * to avoid conflicts with WP_Error.
 * https://docs.mockery.io/en/latest/cookbook/mocking_hard_dependencies.html
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class WPResponseFactoryTest extends PluginTestCase
{

    public function testWpErrorResponse()
    {
        $error_code = 'error_code';
        $message = 'Error message';
        $param = ['param'];

        $wp_error_mock = \Mockery::mock('overload:WP_Error');
        $wp_error_mock->shouldReceive('get_error_code')
            ->once()
            ->andReturn($error_code);
        $wp_error_mock->shouldReceive('get_error_message')
            ->once()
            ->andReturn($message);
        $wp_error_mock->shouldReceive('get_error_data')
            ->once()
            ->andReturn(['status' => 400, 'param' => $param]);

        $wp_error_response = WPResponseFactory::wp_error_response($error_code, $message, $param);

        $this->assertInstanceOf(WP_Error::class, $wp_error_response);
        $this->assertEquals($error_code, $wp_error_response->get_error_code());
        $this->assertEquals($message, $wp_error_response->get_error_message());
        $this->assertEquals(['status' => 400, 'param' => $param], $wp_error_response->get_error_data());
    }

    public function testWpRestResponse()
    {
        $message = 'Success message';
        $employee_id = 1;
        $status = 200;

        $wp_rest_response_mock = \Mockery::mock('overload:WP_REST_Response');
        $wp_rest_response_mock->shouldReceive('get_status')
            ->once()
            ->andReturn($status);

        $wp_rest_response = WPResponseFactory::wp_rest_response($message, ['employee_id' => $employee_id]);
        $this->assertEquals($status, $wp_rest_response->get_status());
    }
}
