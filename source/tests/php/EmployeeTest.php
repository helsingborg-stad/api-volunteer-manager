<?php

namespace php;

use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Employee as Employee;
use VolunteerManager\Entity\Term;

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
        $termMock = \Mockery::mock(Term::class);

        $employee = new Employee($termMock);

        Functions\expect('add_action')
            ->once()
            ->with('init', [$this->employee, 'insertEmploymentStatusTerms']);

        $this->employee->addHooks();
    }

    /**
     * @dataProvider populateNotificationReceiverWithAdminProvider
     */
    public function testPopulateNotificationReceiverWithAdmin($args, $getFieldResult, $expectedResult)
    {
        Functions\when('get_field')->justReturn($getFieldResult);
        $this->assertEquals(
            $expectedResult,
            $this->employee->populateNotificationReceiverWithAdmin($args, $this->post->ID)
        );
    }

    public function populateNotificationReceiverWithAdminProvider(): array
    {
        return [
            [
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                [['email' => 'foo@admin.bar']],
                ['to' => 'foo@admin.bar', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
            [
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                [['email' => 'foo@admin.bar'], ['email' => 'bar@admin.foo']],
                ['to' => 'foo@admin.bar,bar@admin.foo', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
            [
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                [['email' => 'foo@admin.bar'], ['unknown' => 'unknown']],
                ['to' => 'foo@admin.bar', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
            [
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                [],
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
            [
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                null,
                ['to' => '', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
        ];
    }

    /**
     * @dataProvider notificationReceiverWithSubmitterProvider
     */
    public function testPopulateNotificationReceiverWithSubmitter($args, $getPostMetaResult, $expectedResult)
    {
        Functions\when('get_field')->justReturn($getPostMetaResult);
        $this->assertEquals(
            $expectedResult,
            $this->employee->populateNotificationReceiverWithSubmitter($args, $this->post->ID)
        );
    }

    public function notificationReceiverWithSubmitterProvider(): array
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

//
//    {
//        $termMock = \Mockery::mock(Term::class);
//        $term_items = [
//            [
//                'name' => 'New',
//                'slug' => 'new',
//                'description' => 'New employee. Employee needs to be processed.'
//            ],
//            [
//                'name' => 'Ongoing',
//                'slug' => 'ongoing',
//                'description' => 'Employee under investigation.'
//            ],
//            [
//                'name' => 'Approved',
//                'slug' => 'approved',
//                'description' => 'Employee approved for assignments.'
//            ],
//            [
//                'name' => 'Denied',
//                'slug' => 'denied',
//                'description' => 'Employee denied. Employee can\'t apply.'
//            ]
//        ];
//
//        $termMock->shouldReceive('insertTerms')
//            ->once()
//            ->with($term_items, 'employee-registration-status')
//            ->andReturn(['term_id' => 1, 'term_taxonomy_id' => 1]);
//
//        $employee = new Employee($termMock);
//        $employee->insertEmploymentStatusTerms();
//    }

}
