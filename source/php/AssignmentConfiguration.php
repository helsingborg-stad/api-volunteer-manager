<?php

namespace VolunteerManager;

class AssignmentConfiguration
{
    public static function getStatusTerms(): array
    {
        return [
            [
                'name' => __('Approved', 'api-volunteer-manager'),
                'slug' => 'approved',
                'description' => __('Approved assignment', 'api-volunteer-manager'),
                'color' => '#EEE'
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
                'color' => '#EEE'
            ],
            [
                'name' => __('Recurring', 'api-volunteer-manager'),
                'slug' => 'recurring',
                'description' => __('Recurring assignment', 'api-volunteer-manager'),
                'color' => '#8224e3'
            ]
        ];
    }
}
