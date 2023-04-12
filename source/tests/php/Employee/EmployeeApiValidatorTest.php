<?php

namespace php\Employee;

use VolunteerManager\Employee\EmployeeApiManager;
use VolunteerManager\Employee\EmployeeApiValidator;
use PHPUnit\Framework\TestCase;

use PluginTestCase\PluginTestCase;
use WP_Error;
use Brain\Monkey\Functions;

/**
 * Run tests in separate processes and disable global state preservation
 * to avoid conflicts with WP_Error.
 * https://docs.mockery.io/en/latest/cookbook/mocking_hard_dependencies.html
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class EmployeeApiValidatorTest extends PluginTestCase
{
    private EmployeeApiValidator $employeeApiValidator;

    public function setUp(): void
    {
        parent::setUp();

        $this->employeeApiValidator = new EmployeeApiValidator();
    }

    /**
     * @dataProvider requiredValidParamsProvider
     */
    public function testValidRequiredParams($params)
    {
        $result = $this->employeeApiValidator->validate_required_params($params);

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

        $result = $this->employeeApiValidator->validate_required_params($params);

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

        $result = $this->employeeApiValidator->is_email_in_use($email);

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

        $result = $this->employeeApiValidator->is_national_identity_number_in_use($national_identity_number);

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
