<?php

namespace VolunteerManager\Notification;

class NotificationsConfig
{
    public static array $notifications = [
        [
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
        [
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
        [
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
        ]
    ];
}