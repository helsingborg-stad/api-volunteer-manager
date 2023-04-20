<?php

namespace VolunteerManager;

use VolunteerManager\API\Api;
use VolunteerManager\Helper\CacheBust;
use VolunteerManager\Notification\EmailNotificationSender;
use VolunteerManager\Notification\LoggingNotificationSender;
use VolunteerManager\Notification\NotificationHandler;
use VolunteerManager\Notification\NotificationsConfig;
use VolunteerManager\PostType\Application\Application;
use VolunteerManager\PostType\Application\ApplicationConfiguration;
use VolunteerManager\PostType\Assignment\Assignment;
use VolunteerManager\PostType\Assignment\AssignmentConfiguration;
use VolunteerManager\PostType\Assignment\AssignmentNotifications;
use VolunteerManager\PostType\Employee\Employee;
use VolunteerManager\PostType\Employee\EmployeeApiManager;
use VolunteerManager\PostType\Employee\EmployeeApiValidator;
use VolunteerManager\PostType\Employee\EmployeeConfiguration;

class App
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('plugins_loaded', array($this, 'init'));
        add_action('after_setup_theme', array($this, 'themeSupport'));
    }

    public function init()
    {
        $emailSender = new EmailNotificationSender('wp_mail');
        $loggingEmailSender = new LoggingNotificationSender($emailSender);
        $notificationsHandler = new NotificationHandler(NotificationsConfig::$notifications, $loggingEmailSender);
        $notificationsHandler->addHooks();

        //General
        new Api();
        $admin = new Admin();
        $admin->addHooks();

        //Post types
        $assignment = new Assignment(...array_values(AssignmentConfiguration::getPostTypeArgs()));
        $assignment->addHooks();
        $assignmentNotifications = new AssignmentNotifications();
        $assignmentNotifications->addHooks();

        $employee = new Employee(...array_values(EmployeeConfiguration::getPostTypeArgs()));
        $employee->addHooks();

        $employeeApiManager = new EmployeeApiManager(new EmployeeApiValidator());
        $employeeApiManager->addHooks();

        $application = new Application(...array_values(ApplicationConfiguration::getPostTypeArgs()));
        $application->addHooks();
    }

    /**
     * Enqueue required style
     * @return void
     */
    public function enqueueStyles()
    {
        wp_register_style(
            'api-volunteer-manager-css',
            VOLUNTEER_MANAGER_URL . '/dist/' .
            (new CacheBust())->name('css/api-volunteer-manager.css')
        );

        wp_enqueue_style('api-volunteer-manager-css');
    }

    /**
     * Enqueue required scripts
     * @return void
     */
    public function enqueueScripts()
    {
        wp_register_script(
            'api-volunteer-manager-js',
            VOLUNTEER_MANAGER_URL . '/dist/' .
            (new CacheBust())->name('js/api-volunteer-manager.js')
        );

        wp_enqueue_script('api-volunteer-manager-js');
    }
    
    /**
     * Add theme support
     */
    public function themeSupport()
    {
        add_theme_support('post-thumbnails');
    }
}
