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
    public function testPopulateNotificationWithSubmitter($args, $expectedResult, $getFieldResult)
    {
        Functions\when('get_field')->justReturn($getFieldResult);
        $this->assertEquals(
            $expectedResult,
            $this->employee->populateNotificationWithReceiver($args, $this->post->ID)
        );
    }

    public function populateNotificationReceiverProvider(): array
    {
        return [
            "Existing email address" => [
                [
                    'to' => '',
                    'from' => 'from@email.com',
                    'message' => [
                        'subject' => 'subject',
                        'content' => 'content',
                    ]
                ],
                [
                    'to' => 'foo@bar.com',
                    'from' => 'from@email.com',
                    'message' => [
                        'subject' => 'subject',
                        'content' => 'content',
                    ]
                ],
                'foo@bar.com'
            ],
            "Missing email address" => [
                [
                    'to' => '',
                    'from' => 'from@email.com',
                    'message' => [
                        'subject' => 'subject',
                        'content' => 'content',
                    ]
                ],
                [
                    'to' => '',
                    'from' => 'from@email.com',
                    'message' => [
                        'subject' => 'subject',
                        'content' => 'content',
                    ]
                ],
                null
            ],
        ];
    }
}
