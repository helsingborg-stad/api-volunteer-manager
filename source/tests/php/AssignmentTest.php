<?php

namespace php;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Assignment;

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
            ],
        ];
        $expectedOutput = '<p>Contact details of the person who submitted the assignment.</p><p><strong>Email:</strong> <a href="mailto:foo@bar.com">foo@bar.com</a></p><p><strong>Phone:</strong> 123-456-7890</p>';

        ob_start();
        $this->assignment->renderSubmitterData($this->post, $args);
        $output = ob_get_clean();

        $this->assertEquals($expectedOutput, $output);
    }

    public function testRegisterSubmitterMetaBoxWithExistingMetaValue()
    {
        Functions\when('get_post_meta')->justReturn('meta_value');
        Functions\expect('add_meta_box')->once()
            ->with(
                'submitter-info',
                'Submitted by',
                array($this->assignment, 'renderSubmitterData'),
                array('assignment'),
                'normal',
                'low',
                array(
                    'submittedByEmail' => 'meta_value',
                    'submittedByPhone' => 'meta_value'
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

    /**
     * @dataProvider notificationReceiverProvider
     */
    public function testPopulateNotificationReceiverWithSubmitter($args, $getPostMetaResult, $expectedResult)
    {
        Functions\when('get_post_meta')->justReturn($getPostMetaResult);
        $this->assertEquals(
            $expectedResult,
            $this->assignment->populateNotificationReceiverWithSubmitter($args, $this->post->ID)
        );
    }

    public function notificationReceiverProvider(): array
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
     * @dataProvider notificationSenderProvider
     */
    public function testPopulateNotificationSender($args, $getFieldResult, $expectedResult)
    {
        Functions\when('get_field')->justReturn($getFieldResult);
        $this->assertEquals($expectedResult, $this->assignment->populateNotificationSender($args, $this->post->ID));
    }

    public function notificationSenderProvider(): array
    {
        return [
            [
                ['to' => 'foo@bar.receiver', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                ['email' => 'foo@bar.email', 'name' => 'Foo Bar'],
                ['to' => 'foo@bar.receiver', 'from' => 'Foo Bar <foo@bar.email>', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
            [
                ['to' => 'foo@bar.receiver', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                ['email' => 'foo@bar.email', 'name' => null],
                ['to' => 'foo@bar.receiver', 'from' => 'foo@bar.email', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ],
            [
                ['to' => 'foo@bar.receiver', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']],
                ['email' => null, 'name' => null],
                ['to' => 'foo@bar.receiver', 'from' => '', 'message' => ['subject' => 'Subject', 'content' => 'Content']]
            ]
        ];
    }

    public function testRegisterApplicationsMetaBox()
    {
        Functions\expect('add_meta_box')->once()
            ->with(
                'assignment_employees',
                'Employees',
                array($this->assignment, 'renderEmployeesList'),
                array('assignment'),
                'normal',
                'low',
                array(
                    'applications' => [],
                )
            );
        Functions\when('get_posts')->justReturn([]);
        $this->assignment->registerApplicationsMetaBox('', $this->post);
    }

    /**
     * @throws \Brain\Monkey\Expectation\Exception\ExpectationArgsRequired
     */
    public function testGetApplications()
    {
        Functions\expect('get_posts')->once()
            ->with(
                [
                    'post_type' => 'application',
                    'orderby' => 'post_date',
                    'order' => 'ASC',
                    'posts_per_page' => -1,
                    'suppress_filters' => true,
                    'meta_query' => [
                        [
                            'key' => 'application_assignment',
                            'value' => $this->post->ID,
                            'compare' => '='
                        ]
                    ]
                ])->andReturn([]);
        $this->assignment->getApplications($this->post);
    }

    public function testRenderEmployeesListWithEmptyArgs()
    {
        $post = new \stdClass();
        $args = array('args' => array('applications' => array()));
        Functions\Expect('get_field')->never();
        Functions\Expect('get_the_date')->never();

        ob_start();
        $this->assignment->renderEmployeesList($post, $args);
        $output = ob_get_clean();

        $this->assertStringContainsString('No employees found.', $output);
    }

    public function testRenderEmployeesListWithArgs()
    {
        $applications = [(object)['ID' => 1]];
        $args = array('args' => array('applications' => $applications));
        $employee = (object)['ID' => 99, 'post_title' => 'Foo Bar'];
        $status = (object)['term_id' => 10, 'taxonomy' => 'tax', 'slug' => 'tax', 'name' => 'Tax'];

        Functions\Expect('get_field')->times(4)->andReturn($employee, $status, '#ddd', '#fff');
        Functions\Expect('get_the_date')->times(1)->andReturn('2000-01-01');
        Functions\Expect('get_edit_post_link')->times(2)->andReturn('https://url.com/1/edit.php', 'https://url.com/2/edit.php');
        Functions\Expect('get_delete_post_link')->once()->andReturn('https://url.com/1/delete.php');

        ob_start();
        $this->assignment->renderEmployeesList($this->post, $args);
        $output = ob_get_clean();
        $output = str_replace("\n", "", $output);
        $output = preg_replace('/\s*(<[^>]*>)\s*/', '$1', $output);

        $expected = '<table><tr><th>Name</th><th>Date</th><th>Status</th><th></th></tr><tr><td class="employee_name"><a href="https://url.com/1/edit.php">Foo Bar</a></td><td>2000-01-01</td><td><span style="background: #ddd; color: #fff;" class="term-pill term-pill-tax">Tax</span></td><td class="actions"><a href="https://url.com/2/edit.php">Edit</a><a href="https://url.com/1/delete.php" class="delete">Delete</a></td></tr></table>';
        $this->assertEquals($expected, $output);
    }
}