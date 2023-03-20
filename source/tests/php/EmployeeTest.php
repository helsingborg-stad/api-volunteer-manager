<?php

namespace php;

use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Employee as Employee;

class EmployeeTest extends PluginTestCase
{

    private $employee;
    private object $post;

    public function setUp(): void
    {
        parent::setUp();

        $this->post = new \stdClass();
        $this->post->ID = 99;

        $this->employee = new Employee();
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

    /**
     * @dataProvider populateNotificationReceiverProvider
     */
    public function testPopulateNotificationWithSubmitter($args, $getFieldResult, $expectedResult)
    {
        Functions\when('get_field')->justReturn($getFieldResult);
        $this->assertEquals($expectedResult, $this->employee->populateNotificationWithReceiver($args, $this->post->ID));
    }

    public function populateNotificationReceiverProvider(): array
    {
        return [
            [
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                'foo@email.bar',
                ['to' => 'foo@email.bar', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
            [
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                null,
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
        ];
    }
}
