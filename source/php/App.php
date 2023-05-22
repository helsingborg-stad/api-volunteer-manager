<?php

namespace VolunteerManager;

use VolunteerManager\API\Api;
use VolunteerManager\Notification\EmailNotificationSender;
use VolunteerManager\Notification\LoggingNotificationSender;
use VolunteerManager\Notification\NotificationHandler;
use VolunteerManager\Notification\NotificationsConfig;
use VolunteerManager\PostType\Application\Application;
use VolunteerManager\PostType\Application\ApplicationConfiguration;
use VolunteerManager\PostType\Application\ApplicationNotificationFilters;
use VolunteerManager\PostType\Assignment\Assignment;
use VolunteerManager\PostType\Assignment\AssignmentConfiguration;
use VolunteerManager\PostType\Assignment\AssignmentNotificationFilters;
use VolunteerManager\PostType\Employee\Employee;
use VolunteerManager\PostType\Employee\EmployeeApiManager;
use VolunteerManager\PostType\Employee\EmployeeApiValidator;
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

        //Post types
        $assignment = new Assignment(...array_values(AssignmentConfiguration::getPostTypeArgs()));
        $assignment->addHooks();
        $assignmentNotifications = new AssignmentNotificationFilters();
        $assignmentNotifications->addHooks();

        $employee = new Employee(...array_values(EmployeeConfiguration::getPostTypeArgs()));
        $employee->addHooks();
        $employeeNotifications = new EmployeeNotificationFilters();
        $employeeNotifications->addHooks();

        $employeeApiManager = new EmployeeApiManager();
        $employeeApiManager->addHooks();

        $application = new Application(...array_values(ApplicationConfiguration::getPostTypeArgs()));
        $application->addHooks();
        $applicationNotifications = new ApplicationNotificationFilters();
        $applicationNotifications->addHooks();
    }
}
