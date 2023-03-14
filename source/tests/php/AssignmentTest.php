<?php

namespace php;

use Brain\Monkey\Functions;
use PluginTestCase\PluginTestCase;
use VolunteerManager\Assignment;
use VolunteerManager\Notification\NotificationHandler;

class AssignmentTest extends PluginTestCase
{
    private $assignment;
    private object $post;

    public function setUp(): void
    {
        parent::setUp();

        $this->post = new \stdClass();
        $this->post->ID = 123;

        $emailServiceMock = $this->getMockBuilder('VolunteerManager\Notification\EmailNotificationSender')
            ->disableOriginalConstructor()
            ->getMock();
        $notificationHandler = new NotificationHandler([], $emailServiceMock);
        $this->assignment = new Assignment($notificationHandler);
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

    public function testPopulateNotificationWithSubmitter()
    {
        $args = [
            'to' => '',
            'from' => 'from@email.com',
            'message' => [
                'subject' => 'subject',
                'content' => 'content',
            ]
        ];
        $expectedResult = $args;
        $expectedResult['to'] = 'foo@bar.com';
        Functions\when('get_post_meta')->justReturn('foo@bar.com');
        $actualResult = $this->assignment->populateNotificationWithSubmitter($args, 123);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testPopulateNotificationSenderWithEmailAndName()
    {
        $args = [
            'to' => 'to@email.com',
            'from' => '',
            'message' => [
                'subject' => 'subject',
                'content' => 'content',
            ]
        ];
        $expectedResult = $args;
        $expectedResult['from'] = 'Foo Bar <foo@bar.com>';
        Functions\when('get_field')->justReturn(['email' => 'foo@bar.com', 'name' => 'Foo Bar']);
        $actualResult = $this->assignment->populateNotificationSender($args, 1);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testPopulateNotificationSenderWithEmail()
    {
        $args = [
            'to' => 'to@email.com',
            'from' => '',
            'message' => [
                'subject' => 'subject',
                'content' => 'content',
            ]
        ];
        $expectedResult = $args;
        $expectedResult['from'] = 'foo@bar.com';
        Functions\when('get_field')->justReturn(['email' => 'foo@bar.com', 'name' => null]);
        $actualResult = $this->assignment->populateNotificationSender($args, 1);
        $this->assertEquals($expectedResult, $actualResult);
    }
}