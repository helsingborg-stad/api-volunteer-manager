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

        $this->post = new \stdClass();
        $this->post->ID = 123;
        $this->assignment = new Assignment();
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
}