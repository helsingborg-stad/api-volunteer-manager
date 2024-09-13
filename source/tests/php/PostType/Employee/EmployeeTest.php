<?php

namespace php\PostType\Employee;

use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use ReflectionException;
use VolunteerManager\PostType\Employee\Employee as Employee;

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

    public function testAddTableColumn()
    {
        $this->employee->addPostTypeTableColumn();
        $actual = $this->employee->tableColumns;

        $this->assertArrayHasKey('registration_status', $actual);
        $this->assertArrayHasKey('submitted_from', $actual);
        $this->assertArrayHasKey('title', $actual);
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
