<?php

namespace VolunteerManager;

use \VolunteerManager\Entity\PostType as PostType;
use \VolunteerManager\Helper\Icon as Icon;

class Employee extends PostType
{
    private static $postTypeSlug;

    public function __construct()
    {
        self::$postTypeSlug = $this->postType();
    }

    /**
     * Create post type
     * @return void
     */
    public function postType() : string
    {
        // Create posttype
        $postType = new PostType(
            _x('Employees', 'Post type plural', 'api-volunteer-manager'),
            _x('Employee', 'Post type singular', 'api-volunteer-manager'),
            'employee',
            array(
                'description'          =>   __('Employees', 'api-volunteer-manager'),
                'menu_icon'            =>   Icon::get('person'),
                'publicly_queriable'   =>   true,
                'show_ui'              =>   true,
                'show_in_nav_menus'    =>   true,
                'has_archive'          =>   true,
                'rewrite'              =>   array(
                    'slug'       =>   __('employee', 'api-volunteer-manager'),
                    'with_front' =>   false
                ),
                'hierarchical'          =>  false,
                'exclude_from_search'   =>  true,
                'taxonomies'            =>  array(),
                'supports'              =>  array('title'),
                'show_in_rest'          => true
            )
        );

        return $postType->slug;
    }
}
