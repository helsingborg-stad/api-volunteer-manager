<?php

namespace php;

use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use ReflectionException;
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

        $mockApplicationArgs =
            [
                'slug' => 'employee',
                'namePlural' => 'employees',
                'nameSingular' => 'employee',
            ];
        $this->employee = new Employee(...$mockApplicationArgs);
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

    /**
     * @dataProvider acfSetNotesDefaultDateProvider
     * @throws ReflectionException|ExpectationArgsRequired
     */
    public function testAcfSetNotesDefaultDate($field, $expectedField)
    {
        Functions\expect('add_filter')
            ->once()
            ->with('acf/load_field/name=notes_date_updated', [$this->employee, 'acfSetNotesDefaultDate']);

        $this->employee->addHooks();

        $reflection = new \ReflectionClass(Employee::class);
        $method = $reflection->getMethod('acfSetNotesDefaultDate');
        $method->setAccessible(true);

        $result = $method->invoke($this->employee, $field);
        $this->assertEquals($expectedField, $result);

    }

    public function acfSetNotesDefaultDateProvider(): array
    {
        $currentDate = date('Y-m-d');
        return [
            "Test setting default_value when value is empty" => [
                ['type' => 'date_picker', 'name' => 'notes_date_updated', 'value' => ''],
                ['type' => 'date_picker', 'name' => 'notes_date_updated', 'value' => '', 'default_value' => $currentDate]
            ],
            "Test setting default_value when value is not empty" => [
                ['type' => 'date_picker', 'name' => 'notes_date_updated', 'value' => '2023-03-22'],
                ['type' => 'date_picker', 'name' => 'notes_date_updated', 'value' => '2023-03-22', 'default_value' => $currentDate]
            ],
        ];
    }
}
