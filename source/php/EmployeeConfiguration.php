<?php

namespace VolunteerManager;

class EmployeeConfiguration {
    public static function getStatusTerms(): array
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
