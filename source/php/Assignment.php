<?php

namespace VolunteerManager;

use \VolunteerManager\Entity\Taxonomy as Taxonomy;
use \VolunteerManager\Entity\PostType as PostType;
use \VolunteerManager\Entity\Filter as Filter;

use \VolunteerManager\Helper\MetaBox as MetaBox;
use \VolunteerManager\Helper\Icon as Icon;
use \VolunteerManager\Helper\Admin\UI as AdminUI;

class Assignment extends PostType
{
    public static $postTypeSlug;
    public static $statusTaxonomySlug;

    public function __construct()
    {
        //Main post type
        self::$postTypeSlug = $this->postType();

        //Taxonomy
        self::$statusTaxonomySlug   = $this->taxonomyStatus();
    }

    /**
     * Create post type
     * @return void
     */
    public function postType() : string
    {
        // Create posttype
        $postType = new PostType(
            _x('Assignments', 'Post type plural', 'api-volunteer-manager'),
            _x('Assignment', 'Post type singular', 'api-volunteer-manager'),
            'assignment',
            array(
                'description'          =>   __('Assignments', 'api-volunteer-manager'),
                'menu_icon'            =>   Icon::get('person'),
                'publicly_queriable'   =>   true,
                'show_ui'              =>   true,
                'show_in_nav_menus'    =>   true,
                'has_archive'          =>   true,
                'rewrite'              =>   array(
                    'slug'       =>   __('assignment', 'api-volunteer-manager'),
                    'with_front' =>   false
                ),
                'hierarchical'          =>  false,
                'exclude_from_search'   =>  true,
                'taxonomies'            =>  array(),
                'supports'              =>  array('title', 'revisions'),
                'show_in_rest'          => true
            )
        );

        $postType->addTableColumn(
            'status',
            __('Status', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                echo AdminUI::createTaxonomyPills(
                    get_the_terms(
                        $postId,
                        self::$statusTaxonomySlug
                    )
                );
            }
        );

        return $postType->slug;
    }

    /**
     * Create status taxonomy
     * @return string
     */
    public function taxonomyStatus() : string
    {
        //Register new taxonomy
        $categories = new Taxonomy(
            __('Statuses', 'api-volunteer-manager'),
            __('Status', 'api-volunteer-manager'),
            'assignment-status',
            array(self::$postTypeSlug),
            array(
                'hierarchical' => false
            )
        );

        //Remove deafult UI
        (new MetaBox)->remove(
            "tagsdiv-assignment-status",
            self::$postTypeSlug
        );

        //Add filter
        new Filter(
            'assignment-status',
            'assignment'
        );

        //Return taxonomy slug
        return $categories->slug;
    }
}
