<?php

namespace VolunteerManager\PostType\Employee;

use VolunteerManager\Helper\Icon as Icon;

class EmployeeConfiguration
{
    public static function getPostTypeArgs()
    {
        return [
            'slug' => 'employee',
            'namePlural' => __('employees', AVM_TEXT_DOMAIN),
            'nameSingular' => __('employee', AVM_TEXT_DOMAIN),
            'args' => [
                'description' => __('Employees', AVM_TEXT_DOMAIN),
                'menu_icon' => Icon::get('person'),
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'has_archive' => true,
                'hierarchical' => false,
                'exclude_from_search' => true,
                'taxonomies' => array(),
                'supports' => false,
                'show_in_rest' => true]
        ];
    }

    public static function getStatusTerms()
    {
        return [
            [
                'name' => __('New', 'api-volunteer-manager'),
                'slug' => 'new',
                'description' => 'New employee. Employee needs to be processed.',
                'color' => '#eeee22'
            ],
            [
                'name' => __('Ongoing', 'api-volunteer-manager'),
                'slug' => 'ongoing',
                'description' => 'Employee under investigation.',
                'color' => '#81d742'
            ],
            [
                'name' => __('Approved', 'api-volunteer-manager'),
                'slug' => 'approved',
                'description' => 'Employee approved for assignments.',
                'color' => '#1e73be'
            ],
            [
                'name' => __('Denied', 'api-volunteer-manager'),
                'slug' => 'denied',
                'description' => 'Employee denied. Employee can\'t apply.',
                'color' => '#dd3333'
            ]
        ];
    }
}
