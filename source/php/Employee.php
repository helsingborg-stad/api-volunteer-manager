<?php

namespace VolunteerManager;

use \VolunteerManager\Entity\PostType as PostType;
use \VolunteerManager\Entity\Taxonomy as Taxonomy;
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
        add_action('init', array($this, 'insertEmploymentStatusTerms'));
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
     * @param PostType $postType
     * @return void
     */
    private function addPostTypeTableColumn(PostType $postType): void
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

    /**
     * Create terms for the employee status taxonomy
     *
     * @param $postType
     * @return void
     */
    private function registerEmployeeStatusTaxonomy($postType) : void
    {
        new Taxonomy(
            'Registration statuses',
            'Registration status',
            'employee-registration-status',
            array($postType->slug),
            array (
                'hierarchical' => false,
                'show_ui' => false
            )
        );
    }

    /**
     * Insert terms for the employee status taxonomy
     *
     * @return void
     */
    public function insertEmploymentStatusTerms(): void
    {
        $term_items = [
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

        foreach ($term_items as $term) {
            if (!term_exists($term['name'], 'employee-registration-status'))
            {
                $result = wp_insert_term(
                    $term['name'],
                    'employee-registration-status',
                    [
                        'slug' => $term['slug'],
                        'description' => $term['description'],
                    ]
                );
            }
        }
    }
}
