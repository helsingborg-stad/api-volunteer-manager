<?php

namespace php\PostType\Employee;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\PostType\Employee\EmployeeApiValidator;
use WP_Error;

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
     * @dataProvider emailInUseDataProvider
     */
    public function testIfEmailUnique($email, $expectedResult)
    {
        Functions\when('get_posts')->justReturn($expectedResult ? [] : ['dummy_found_post']);

        $result = $this->employeeApiValidator->is_email_unique($email);

        $this->assertEquals($expectedResult, $result);
    }

    public function emailInUseDataProvider(): array
    {
        return [
            'Email is unique' => [
                'john.doe@example.com',
                true,
            ],
            'Email not unique' => [
                'jane.doe@example.com',
                false,
            ],
        ];
    }

    /**
     * @dataProvider nationalIdentityNumberInUseDataProvider
     */
    public function testIfNationalIdentityNumberUnique($national_identity_number, $expectedResult)
    {
        Functions\when('get_posts')->justReturn($expectedResult ? [] : ['dummy_post']);

        $result = $this->employeeApiValidator->is_national_identity_unique($national_identity_number);

        $this->assertEquals($expectedResult, $result);
    }

    public function nationalIdentityNumberInUseDataProvider(): array
    {
        return [
            'National Identity Number unique' => [
                '123456789',
                true,
            ],
            'National Identity Number not unique' => [
                '987654321',
                false,
            ],
        ];
    }

}
