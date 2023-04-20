<?php

namespace php;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\PostType\Assignment\Assignment;

class AssignmentTest extends PluginTestCase
{
    private $assignment;
    private object $post;

    public function setUp(): void
    {
        parent::setUp();

        $mockAssignmentArgs =
            [
                'slug' => 'assignment',
                'namePlural' => 'assignments',
                'nameSingular' => 'assignment',
            ];

        $this->post = new \stdClass();
        $this->post->ID = 123;
        $this->assignment = new Assignment(...$mockAssignmentArgs);
    }

    public function testRenderSubmitterData(): void
    {
        $args = [
            'args' => [
                'submittedByEmail' => 'foo@bar.com',
                'submittedByPhone' => '123-456-7890',
                'submittedByFirstName' => 'Foo',
                'submittedBySurname' => 'Bar',
            ],
        ];
        $expectedOutput = '<p>Contact details of the person who submitted the assignment.</p><p><strong>Name:</strong> Foo Bar</p><p><strong>Email:</strong> <a href="mailto:foo@bar.com">foo@bar.com</a></p><p><strong>Phone:</strong> 123-456-7890</p>';

        ob_start();
        $this->assignment->renderSubmitterData($this->post, $args);
        $output = ob_get_clean();

        $this->assertEquals($expectedOutput, $output);
    }

    public function testRegisterSubmitterMetaBoxWithExistingMetaValue()
    {
        Functions\expect('get_post_meta')->times(4)->andReturn('foo@bar.se', '1234567', 'Foo', 'Bar');
        Functions\expect('add_meta_box')->once()
            ->with(
                'submitter-info',
                'Submitted by',
                array($this->assignment, 'renderSubmitterData'),
                array('assignment'),
                'normal',
                'low',
                array(
                    'submittedByEmail' => 'foo@bar.se',
                    'submittedByPhone' => '1234567',
                    'submittedByFirstName' => 'Foo',
                    'submittedBySurname' => 'Bar',
                )
            );
        $this->assignment->registerSubmitterMetaBox('assignment', $this->post);
    }

    public function testRegisterSubmitterMetaBoxWithMissingExistingMetaValue()
    {
        Functions\when('get_post_meta')->justReturn(null);
        Functions\expect('add_meta_box')->never()->withAnyArgs();
        $this->assignment->registerSubmitterMetaBox('assignment', $this->post);
    }
}