<?php

namespace VolunteerManager\PostType\Application;

use VolunteerManager\Helper\Icon as Icon;

class ApplicationConfiguration
{
    public static function getPostTypeArgs(): array
    {
        return [
            'slug' => 'application',
            'namePlural' => __('applications', AVM_TEXT_DOMAIN),
            'nameSingular' => __('application', AVM_TEXT_DOMAIN),
            'args' => [
                'description' => __('Applications', AVM_TEXT_DOMAIN),
                'menu_icon' => Icon::get('person'),
                'public' => false,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'exclude_from_search' => true,
                'supports' => false,
            ]
        ];
    }

    public static function getStatusTerms(): array
    {
        return [
            [
                'name' => __('Pending', AVM_TEXT_DOMAIN),
                'slug' => 'pending',
                'description' => 'Application is pending.',
                'color' => '#dd9933'
            ],
            [
                'name' => __('Approved', AVM_TEXT_DOMAIN),
                'slug' => 'approved',
                'description' => 'Application is approved.',
                'color' => '#81d742'
            ],
            [
                'name' => __('Approved with condition', AVM_TEXT_DOMAIN),
                'slug' => 'approved_with_condition',
                'description' => 'Application is approved, with condition.',
                'color' => '#1e73be'
            ],
            [
                'name' => __('Closed', AVM_TEXT_DOMAIN),
                'slug' => 'closed',
                'description' => 'Application is closed.',
                'color' => '#708090'
            ],
            [
                'name' => __('Denied', AVM_TEXT_DOMAIN),
                'slug' => 'denied',
                'description' => 'Application is denied.',
                'color' => '#dd3333'
            ],
        ];
    }
}
