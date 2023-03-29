<?php

namespace VolunteerManager;

use VolunteerManager\Helper\Icon as Icon;

class ApplicationConfiguration
{
    public static function getPostTypeArgs(): array
    {
        return [
            'slug' => 'application',
            'namePlural' => 'applications',
            'nameSingular' => 'application',
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
}
