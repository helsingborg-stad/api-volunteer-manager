<?php

namespace php\Employee;

use PluginTestCase\PluginTestCase;
use VolunteerManager\Employee\EmployeeApiManager;
use PHPUnit\Framework\TestCase;
use WP_Error;
use Brain\Monkey\Functions;

class EmployeeApiManagerTest extends PluginTestCase
{
    private $employeeApiManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->employeeApiManager = new class extends EmployeeApiManager {

            // Make protected methods available for testing
            public function validate_required_params_test($params)
            {
                return $this->validate_required_params($params);
            }
        };
    }

    /**
     * @dataProvider requiredValidParamsProvider
     */
    public function testValidRequiredParams($params)
    {
        $result = $this->employeeApiManager->validate_required_params_test($params);

        $this->assertTrue($result);
    }

    /**
     * @dataProvider requiredInvalidParamsProvider
     */
    public function testInvalidRequiredParams($params, $expectedResult, $expectedErrorCode, $expectedErrorParam)
    {
        $wp_error_mock = \Mockery::mock('overload:WP_Error');
        $wp_error_mock->shouldReceive('get_error_code')
            ->once()
            ->andReturn($expectedErrorCode);
        $wp_error_mock->shouldReceive('get_error_data')
            ->once()
            ->andReturn($expectedErrorParam);

        $result = $this->employeeApiManager->validate_required_params_test($params);

        $this->assertInstanceOf(WP_Error::class, $result);
        $this->assertEquals($expectedErrorCode, $result->get_error_code());
        $this->assertEquals($expectedErrorParam, $result->get_error_data());
    }

    public function requiredValidParamsProvider(): array
    {
        return [
            'All Params' => [
                [
                    'first_name' => 'John',
                    'surname' => 'Doe',
                    'national_identity_number' => '123456789',
                    'email' => 'john.doe@example.com',
                ],
                true,
            ],
        ];
    }

    public function requiredInvalidParamsProvider(): array
    {
        return [
            'Missing Surname' => [
                [
                    'first_name' => 'John',
                    'surname' => '',
                    'national_identity_number' => '123456789',
                    'email' => 'john.doe@example.com',
                ],
                false,
                'Some error code for registration errors',
                'Some error data',
            ],
        ];
    }

}
