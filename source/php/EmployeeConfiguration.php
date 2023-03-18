<?php

namespace VolunteerManager;

class EmployeeConfiguration {
    public static function getStatusTerms(): array {
        return [
            [
                'name' => __('New', 'api-volunteer-manager'),
                'slug' => 'new',
                'description' => 'New employee. Employee needs to be processed.'
            ],
            [
                'name' => __('Ongoing', 'api-volunteer-manager'),
                'slug' => 'ongoing',
                'description' => 'Employee under investigation.'
            ],
            [
                'name' => __('Approved', 'api-volunteer-manager'),
                'slug' => 'approved',
                'description' => 'Employee approved for assignments.'
            ],
            [
                'name' => __('Denied', 'api-volunteer-manager'),
                'slug' => 'denied',
                'description' => 'Employee denied. Employee can\'t apply.'
            ]
        ];
    }
}
