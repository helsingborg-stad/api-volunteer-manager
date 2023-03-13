<?php

namespace php;

use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Employee as Employee;

class EmployeeTest extends PluginTestCase
{
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
