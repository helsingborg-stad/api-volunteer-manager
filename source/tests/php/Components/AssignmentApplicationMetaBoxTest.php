<?php

namespace php\Components;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Components\ApplicationMetaBox\AssignmentApplicationMetaBox;

class AssignmentApplicationMetaBoxTest extends PluginTestCase
{
    private $applicationMetaBox;
    private object $post;

    public function setUp(): void
    {
        parent::setUp();

        $this->post = (object)['ID' => 123, 'post_type' => 'example_post_type'];

        $this->applicationMetaBox = $this->getMockForAbstractClass(
            AssignmentApplicationMetaBox::class,
            [$this->post, 'Title', 'key']
        );
    }

    public function testGetApplicationRow()
    {
        $application = (object)['ID' => 1];
        $employee = (object)['ID' => 99, 'post_title' => 'Foo Bar'];
        $status = (object)['term_id' => 10, 'taxonomy' => 'tax', 'slug' => 'tax', 'name' => 'Tax'];

        Functions\Expect('get_field')->times(4)->andReturn($employee, $status, '#ddd', '#fff');
        Functions\Expect('get_the_date')->times(1)->andReturn('1970-01-01');
        Functions\Expect('get_edit_post_link')->times(2)->andReturn('https://url.com/1/edit.php', 'https://url.com/2/edit.php');
        Functions\Expect('get_delete_post_link')->once()->andReturn('https://url.com/1/delete.php');

        $result = $this->applicationMetaBox->getApplicationRow($application);
        $result = str_replace("\n", "", preg_replace('/\s*(<[^>]*>)\s*/', '$1', $result));

        $expected = '<tr><td class="title"><a href="https://url.com/1/edit.php">Foo Bar</a></td><td>1970-01-01</td><td><span style="background: #ddd; color: #fff;" class="term-pill term-pill-tax">Tax</span></td><td class="actions"><a href="https://url.com/2/edit.php">Edit</a><a href="https://url.com/1/delete.php" class="delete">Delete</a></td></tr>';

        $this->assertEquals($expected, $result);
    }
}