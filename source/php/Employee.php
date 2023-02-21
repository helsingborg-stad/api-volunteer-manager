<?php

namespace VolunteerManager;

use \VolunteerManager\Entity\PostType as PostType;
use VolunteerManager\Entity\Taxonomy;
use \VolunteerManager\Helper\Icon as Icon;

class Employee
{
    private static PostType $postType;

    public function __construct()
    {
        self::$postType = $this->postType();
        $this->addPostTypeTableColumn(self::$postType);

        $this->registerEmployeeStatusTaxonomy(self::$postType);
    }

    public function addHooks()
    {
        add_action('init', array($this, 'createEmployeeStatusTerms'));
    }


    /**
     * Create post type
     * @return PostType
     */
    public function postType(): PostType
    {
        // Create post type
        return new PostType(
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
                'show_in_rest'          =>  true
            )
        );
    }

    /**
     * Add table column to post type
     *
     * @param PostType $postType
     * @return void
     */
    public function addPostTypeTableColumn(PostType $postType)
    {
        $postType->addTableColumn(
            'registration_status',
            __('Registration status', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                echo "-";
            }
        );
    }

    private function registerEmployeeStatusTaxonomy($postType) : void
    {
        $categories = new Taxonomy(
            'Registration statuses',
            'Registration status',
            'employee-registration-status',
            array($postType->slug),
            array (
                'hierarchical' => false,
                // 'show_ui' => false
            )
        );

    }

    public function createEmployeeStatusTerms()
    {
        $insert_result = wp_insert_term(
            'p_insert_test',
            'employee-registration-status'
        );
    }
}
