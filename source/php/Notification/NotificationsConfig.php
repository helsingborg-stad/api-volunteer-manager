<?php

namespace VolunteerManager\Notification;

class NotificationsConfig
{
    public static function getNotifications()
    {
        return [
            "Message to submitter when externally created assignments is approved" => [
                'key' => 'external_assignment_approved',
                'taxonomy' => 'assignment-status',
                'oldValue' => 'pending',
                'newValue' => 'approved',
                'message' => [
                    'subject' => __('Your assignment "%s" is approved', AVM_TEXT_DOMAIN),
                    'content' => __('Hello %s,<br><br>It\'s great that you want to involve more people from Helsingborg! Your assignment "%s" has now been approved for publication on <a href="https://helsingborg.se">helsingborg.se</a> and will be visible to many engaged Helsingborg residents. If you would like to make changes, update something, or remove the assignment, please send an email to engagemang@helsingborg.se.<br><br>Good luck!<br><br>Best regards,<br>Engagemang Helsingborg', AVM_TEXT_DOMAIN),
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to submitter when externally created assignments is denied" => [
                'key' => 'external_assignment_denied',
                'taxonomy' => 'assignment-status',
                'oldValue' => 'pending',
                'newValue' => 'denied',
                'message' => [
                    'subject' => __('Your assignment "%s" is denied', AVM_TEXT_DOMAIN),
                    'content' => __('Hello %s,<br><br>Thank you for wanting to register an assignment for engaged residents of Helsingborg. The assignment "%s" has been processed by an administrator and unfortunately it cannot be published. Please contact Engagemang Helsingborg for more information at engagemang@helsingborg.se.<br><br>Best regards,<br>Engagemang Helsingborg', AVM_TEXT_DOMAIN),
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to the volunteer when a new volunteer is created" => [
                'key' => 'external_volunteer_new',
                'taxonomy' => 'employee-registration-status',
                'oldValue' => '',
                'newValue' => 'new',
                'message' => [
                    'subject' => __('Your application is received', AVM_TEXT_DOMAIN),
                    'content' => __('Hello %s,<br><br>We are pleased to inform you that your application to become a volunteer has been received and we are grateful for your interest in helping to make a positive difference in our city. Thank you for taking the time to get involved with others! You have taken a step towards making Helsingborg a better place and we are grateful for your willingness to help.<br><br>We also want to inform you that your application has been registered in our system and will be processed shortly. Until then, feel free to contact us if you have any questions or concerns.<br><br>Thanks again for your commitment!<br><br>Sincerely,<br>Engagemang Helsingborg', AVM_TEXT_DOMAIN),
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to admin when a new volunteer is created" => [
                'key' => 'admin_external_volunteer_new',
                'taxonomy' => 'employee-registration-status',
                'oldValue' => '',
                'newValue' => 'new',
                'message' => [
                    'subject' => __('A new application is created', AVM_TEXT_DOMAIN),
                    'content' => __('Hello,<br><br>A new application has been created in our system and is ready to be processed. The application concerns a new volunteer who wants to help make a positive difference in Helsingborg.<br><br>We kindly ask you to take a look at the application and to process it as soon as possible. We want to make sure that every volunteer who is interested in helping to improve our city gets a chance to do so.<br><br>Sincerely,<br><br>Engagemang Helsingborg', AVM_TEXT_DOMAIN),
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to admin when a new application for an assignment is created" => [
                'key' => 'admin_external_application_new',
                'taxonomy' => 'application-status',
                'oldValue' => '',
                'newValue' => 'pending',
                'message' => [
                    'subject' => __('A new application to assignment is created', AVM_TEXT_DOMAIN),
                    'content' => __('Hello,<br><br>A new application for the volunteer assignment "%s" has been created in our system and is ready to be processed.<br>%s<br><br>Sincerely,<br><br>Engagement Helsingborg', AVM_TEXT_DOMAIN),
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ]
        ];
    }
}