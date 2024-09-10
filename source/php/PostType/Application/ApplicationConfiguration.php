<?php

namespace VolunteerManager\PostType\Application;

use VolunteerManager\Helper\Icon as Icon;

class ApplicationConfiguration
{
    public static function getPostTypeArgs(): array
    {
        return [
            'slug' => 'application',
            'namePlural' => __('applications', 'api-volunteer-manager'),
            'nameSingular' => __('application', 'api-volunteer-manager'),
            'args' => [
                'description' => __('Applications', 'api-volunteer-manager'),
                'menu_icon' => Icon::get('person'),
                'public' => false,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'exclude_from_search' => true,
                'supports' => false,
                'show_in_rest' => false,
            ]
        ];
    }

    public static function getStatusTerms(): array
    {
        return [
            [
                'name' => __('Pending', 'api-volunteer-manager'),
                'slug' => 'pending',
                'description' => 'Application is pending.',
                'color' => '#dd9933'
            ],
            [
                'name' => __('Approved', 'api-volunteer-manager'),
                'slug' => 'approved',
                'description' => 'Application is approved.',
                'color' => '#81d742'
            ],
            [
                'name' => __('Approved with condition', 'api-volunteer-manager'),
                'slug' => 'approved_with_condition',
                'description' => 'Application is approved, with condition.',
                'color' => '#1e73be'
            ],
            [
                'name' => __('Closed', 'api-volunteer-manager'),
                'slug' => 'closed',
                'description' => 'Application is closed.',
                'color' => '#708090'
            ],
            [
                'name' => __('Denied', 'api-volunteer-manager'),
                'slug' => 'denied',
                'description' => 'Application is denied.',
                'color' => '#dd3333'
            ],
        ];
    }
}
