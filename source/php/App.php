<?php

namespace VolunteerManager;

use VolunteerManager\API\Api;
use VolunteerManager\API\Application\ApplicationApiManager;
use VolunteerManager\API\Application\ApplicationCreator;
use VolunteerManager\API\Assignment\AssignmentApiManager;
use VolunteerManager\API\Assignment\AssignmentCreator;
use VolunteerManager\API\Auth\JWTAuthentication;
use VolunteerManager\API\Employee\EmployeeApiManager;
use VolunteerManager\API\Employee\EmployeeCreator;
use VolunteerManager\Notification\EmailNotificationSender;
use VolunteerManager\Notification\LoggingNotificationSender;
use VolunteerManager\Notification\NotificationHandler;
use VolunteerManager\Notification\NotificationsConfig;
use VolunteerManager\PostType\Application\Application;
use VolunteerManager\PostType\Application\ApplicationConfiguration;
use VolunteerManager\PostType\Application\ApplicationNotificationFilters;
use VolunteerManager\PostType\Assignment\Assignment;
use VolunteerManager\PostType\Assignment\AssignmentConfiguration;
use VolunteerManager\Entity\FieldSetter;
use VolunteerManager\PostType\Assignment\AssignmentNotificationFilters;
use VolunteerManager\PostType\Employee\Employee;
use VolunteerManager\PostType\Employee\EmployeeConfiguration;
use VolunteerManager\PostType\Employee\EmployeeNotificationFilters;

class App
{
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init'));
    }

    public function init()
    {
        $emailSender = new EmailNotificationSender('wp_mail');
        $loggingEmailSender = new LoggingNotificationSender($emailSender);
        $notificationsHandler = new NotificationHandler(NotificationsConfig::getNotifications(), $loggingEmailSender);
        $notificationsHandler->addHooks();

        //General
        new Api();
        $admin = new Admin();
        $admin->addHooks();

        $JWTAuthentication = new JWTAuthentication(defined('VOLUNTEER_MANAGER_JWT_SECRET_KEY') ? VOLUNTEER_MANAGER_JWT_SECRET_KEY : '');

        //Post types
        $assignment = new Assignment(...array_values(AssignmentConfiguration::getPostTypeArgs()));
        $assignment->addHooks();
        $assignmentNotifications = new AssignmentNotificationFilters();
        $assignmentNotifications->addHooks();

        (new AssignmentApiManager(
            new AssignmentCreator(),
            new FieldSetter(),
            $assignment->slug
        ))->addHooks();

        $employee = new Employee(...array_values(EmployeeConfiguration::getPostTypeArgs()));
        $employee->addHooks();
        (new EmployeeApiManager(
            $JWTAuthentication,
            new EmployeeCreator(),
            new FieldSetter(),
            $employee->slug
        ))->addHooks();
        (new EmployeeNotificationFilters())->addHooks();

        $application = new Application(...array_values(ApplicationConfiguration::getPostTypeArgs()));
        $application->addHooks();
        (new ApplicationApiManager(
            $JWTAuthentication,
            new ApplicationCreator(),
            new FieldSetter(),
            $application->slug
        ))->addHooks();
        $applicationNotifications = new ApplicationNotificationFilters();
        $applicationNotifications->addHooks();
    }
}
