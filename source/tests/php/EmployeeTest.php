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

//    /**
//     * @throws ExpectationArgsRequired
//     */
//    public function testAddHooks()
//    {
//        Functions\expect('add_action')
//            ->once()
//            ->with('init', [$this->employee, 'initTaxonomiesAndTerms']);
//
//        $this->employee->addHooks();
//    }

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

    /**
     * @throws \Brain\Monkey\Expectation\Exception\ExpectationArgsRequired
     */
    public function testSetPostTitle()
    {
        Functions\when('get_post_type')->justReturn("employee");
        Functions\expect('get_field')
            ->twice()
            ->andReturn('Foo', 'Bar');
        Functions\expect('wp_update_post')->once()->with(['post_title' => 'Foo Bar', 'ID' => $this->post->ID]);
        $this->employee->setPostTitle($this->post->ID);
    }
}
