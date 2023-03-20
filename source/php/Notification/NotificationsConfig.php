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
                'subject' => 'Assignment approved subject',
                'content' => 'Assignment approved message',
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
                'subject' => 'Assignment denied subject',
                'content' => 'Assignment denied message',
            ],
            'rule' => [
                'key' => 'source',
                'value' => '',
                'operator' => 'NOT_EQUAL'
            ]
        ],
        "Message to volunteer when a new application is created" => [
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
        "Message to admin when a new application is created" => [
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