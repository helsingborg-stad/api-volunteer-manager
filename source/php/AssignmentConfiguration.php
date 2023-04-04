<?php

namespace VolunteerManager;

use VolunteerManager\Helper\Icon as Icon;

class AssignmentConfiguration
{
    public static function getPostTypeArgs(): array
    {
        return [
            'slug' => 'assignment',
            'namePlural' => 'assignments',
            'nameSingular' => 'assignment',
            'args' => [
                'description' => __('Assignments', AVM_TEXT_DOMAIN),
                'menu_icon' => Icon::get('person'),
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'has_archive' => true,
                'hierarchical' => false,
                'exclude_from_search' => true,
                'supports' => array('title', 'revisions'),
                'show_in_rest' => true
            ]
        ];
    }

    public static function getStatusTerms(): array
    {
        return [
            [
                'name' => __('Approved', 'api-volunteer-manager'),
                'slug' => 'approved',
                'description' => __('Approved assignment', 'api-volunteer-manager'),
                'color' => '#1e73be'
            ],
            [
                'name' => __('Ongoing', 'api-volunteer-manager'),
                'slug' => 'ongoing',
                'description' => __('Ongoing assignment', 'api-volunteer-manager'),
                'color' => '#81D742'
            ],
            [
                'name' => __('Pending', 'api-volunteer-manager'),
                'slug' => 'pending',
                'description' => __('Pending assignment', 'api-volunteer-manager'),
                'color' => '#dd9933'
            ],
            [
                'name' => __('Denied', 'api-volunteer-manager'),
                'slug' => 'denied',
                'description' => __('Denied assignment', 'api-volunteer-manager'),
                'color' => '#dd3333'
            ]
        ];
    }
}
