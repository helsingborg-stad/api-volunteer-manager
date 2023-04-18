<?php

namespace VolunteerManager\Notification;

class NotificationsConfig
{
    public static array $notifications = [
        "Message to submitter when externally created assignments is approved" => [
            'key' => 'external_assignment_approved',
            'taxonomy' => 'assignment-status',
            'oldValue' => 'pending',
            'newValue' => 'approved',
            'message' => [
                'subject' => 'Your assignment "%s" is approved',
                'content' => 'Hello %s,<br><br>It\'s great that you want to involve more people from Helsingborg! Your assignment "%s" has now been approved for publication on <a href="https://helsingborg.se">helsingborg.se</a> and will be visible to many engaged Helsingborg residents. If you would like to make changes, update something, or remove the assignment, please send an email to engagemang@helsingborg.se.<br><br>Good luck!<br><br>Best regards,<br>Engagemang Helsingborg',
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
                'subject' => 'Your assignment "%s" is denied',
                'content' => 'Hello %s,<br><br>Thank you for wanting to register an assignment for engaged residents of Helsingborg. The assignment "%s" has been processed by an administrator and unfortunately it cannot be published. Please contact Engagemang Helsingborg for more information at engagemang@helsingborg.se.<br><br>Best regards,<br>Engagemang Helsingborg',
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
                'subject' => 'Application received',
                'content' => 'Your application has been received. More info here...',
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
                'subject' => 'Application created',
                'content' => 'A new application has been created. More info here...',
            ],
            'rule' => [
                'key' => 'source',
                'value' => '',
                'operator' => 'NOT_EQUAL'
            ]
        ]
    ];
}