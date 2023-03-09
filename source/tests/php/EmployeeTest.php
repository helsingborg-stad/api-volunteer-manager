<?php

namespace php;

use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Employee as Employee;

class EmployeeTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Functions\when('_x')->returnArg();
    }

    /**
     * @throws ExpectationArgsRequired
     */
    public function testAddHooks()
    {
        $employee = new Employee();

        Functions\expect('add_action')
            ->once()
            ->with('init', [$employee, 'insertEmploymentStatusTerms']);

        $employee->addHooks();
    }
}
