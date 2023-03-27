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
