<?php

namespace VolunteerManager\Notification;

class NotificationsConfig
{
    public static array $notifications = [
        'assignment' => [
            'assignment-status' => [
                [
                    'key' => 'assignment_approved',
                    'oldValue' => 'pending',
                    'newValue' => 'approved',
                    'message' => [
                        'subject' => 'Assignment approved subject',
                        'content' => 'Assignment approved message',
                    ],
                    'rule' => [
                        'key' => 'source',
                        'value' => 'internal',
                        'operator' => 'NOT_EQUAL'
                    ]
                ],
                [
                    'key' => 'assignment_denied',
                    'oldValue' => 'pending',
                    'newValue' => 'denied',
                    'message' => [
                        'subject' => 'Assignment denied subject',
                        'content' => 'Assignment denied message',
                    ],
                    'rule' => [
                        'key' => 'source',
                        'value' => 'internal',
                        'operator' => 'NOT_EQUAL'
                    ]
                ]
            ]
        ],
        'volunteer' => [
            // add notifications
        ]
    ];
}