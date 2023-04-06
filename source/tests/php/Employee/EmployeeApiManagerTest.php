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

            public function is_email_in_use_test($email)
            {
                return $this->is_email_in_use($email);
            }

            public function is_national_identity_number_in_use_test($national_identity_number)
            {
                return $this->is_national_identity_number_in_use($national_identity_number);
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

    /**
     * @dataProvider emailInUseDataProvider
     */
    public function testIsEmailInUse($email, $expectedResult)
    {
        Functions\when('get_posts')->justReturn($expectedResult ? ['dummy_post'] : []);

        $result = $this->employeeApiManager->is_email_in_use_test($email);

        $this->assertEquals($expectedResult, $result);
    }

    public function emailInUseDataProvider(): array
    {
        return [
            'Email In Use' => [
                'john.doe@example.com',
                true,
            ],
            'Email Not In Use' => [
                'jane.doe@example.com',
                false,
            ],
        ];
    }

    /**
     * @dataProvider nationalIdentityNumberInUseDataProvider
     */
    public function testIsNationalIdentityNumberInUse($national_identity_number, $expectedResult)
    {
        Functions\when('get_posts')->justReturn($expectedResult ? ['dummy_post'] : []);

        $result = $this->employeeApiManager->is_national_identity_number_in_use_test($national_identity_number);

        $this->assertEquals($expectedResult, $result);
    }

    public function nationalIdentityNumberInUseDataProvider(): array
    {
        return [
            'National Identity Number In Use' => [
                '123456789',
                true,
            ],
            'National Identity Number Not In Use' => [
                '987654321',
                false,
            ],
        ];
    }
}
