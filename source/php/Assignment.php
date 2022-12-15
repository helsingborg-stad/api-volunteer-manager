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
    public static $categoryTaxonomySlug;
    public static $typeTaxonomySlug;
    public static $priorityTaxonomySlug;
    public static $statusTaxonomySlug;
    public static $sprintTaxonomySlug;

    public function __construct()
    {

        //Main post type
        self::$postTypeSlug = $this->postType();

        //Taxonomy
        self::$categoryTaxonomySlug = $this->taxonomyCategory();
        self::$typeTaxonomySlug     = $this->taxonomyType();
        self::$priorityTaxonomySlug = $this->taxonomyPriority();
        self::$statusTaxonomySlug   = $this->taxonomyStatus();
        self::$sprintTaxonomySlug   = $this->taxonomySprint();
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
                'supports'              =>  array('title', 'revisions', 'editor'),
                'show_in_rest'          => true
            )
        );

        //Priority in list
        $postType->addTableColumn(
            'priority',
            __('Priority', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                echo AdminUI::createTaxonomyPills(
                    get_the_terms(
                        $postId, 
                        self::$priorityTaxonomySlug
                    )
                );
            }
        );





/*

        //Category in list
        $postType->addTableColumn(
            'category',
            __('Categories', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                $i = 0;
                $categories = get_the_terms($postId, self::$categoryTaxonomySlug);

                if (empty($categories)) {
                    echo '<span class="todo-term-pill">' . __("Uncategorized", 'api-volunteer-manager') . '</span>';
                } else {
                    foreach ((array)$categories as $category) {
                        echo isset($category->name) ? '<span style="background: ' . $this->taxonomyColor($category->term_id, self::$categoryTaxonomySlug). ';" class="todo-term-pill '. $category->slug  .'">' . $category->name . '</span>': '';
                    }
                }
            }
        );

        //Type in list
        $postType->addTableColumn(
            'type',
            __('Type', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                $i = 0;
                $types = get_the_terms($postId, self::$typeTaxonomySlug);

                if (empty($types)) {
                    echo '<span class="todo-term-pill">' . __("Undefined", 'api-volunteer-manager') . '</span>';
                } else {
                    foreach ((array)$types as $type) {
                        echo isset($type->name) ? '<span style="background: ' . $this->taxonomyColor($type->term_id, self::$typeTaxonomySlug). ';" class="todo-term-pill '. $type->slug  .'">' . $type->name . '</span>': '';
                    }
                }
            }
        );

        //Customer in list
        $postType->addTableColumn(
            'customer',
            __('Customer', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                $customers = get_field('ticket_customer', $postId, true);
                if (isset($customers['ID']) && is_numeric($customers['ID'])) {
                    $customers = array($customers);
                }
                if (!empty($customers)) {
                    echo implode(', ', array_map(function ($customer) {
                      return $customer['user_firstname'] . ' ' . $customer['user_lastname'];
                    }, $customers));
                } else {
                    _e('No customer', 'api-volunteer-manager');
                }
            }
        );

        //Customer in list
        $postType->addTableColumn(
            'contact',
            __('Support contact', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                $customer = get_field('ticket_support_contact', $postId, true);
                echo !empty($customer) ? $customer['user_firstname'] . " " . $customer['user_lastname'] : __('No contact', 'api-volunteer-manager');
            }
        );

        //Status in list
        $postType->addTableColumn(
            'status',
            __('Task status', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                $i = 0;
                $statuses = get_the_terms($postId, self::$statusTaxonomySlug);

                if (empty($statuses)) {
                    echo '<span class="todo-term-pill">' . __("Pending", 'api-volunteer-manager') . '</span>';
                } else {
                    foreach ((array)$statuses as $status) {
                        echo isset($status->name) ? '<span style="background: ' . $this->taxonomyColor($status->term_id, self::$statusTaxonomySlug). ';" class="todo-term-pill '. $status->slug  .'">' . $status->name . '</span>': '';
                    }
                }
            }
        );

        


        */
        
        return $postType->slug;
    }

    /**
     * Create priority taxonomy
     * @return string
     */
    public function taxonomyPriority() : string
    {
        //Register new taxonomy
        $categories = new Taxonomy(
            __('Priorities', 'api-volunteer-manager'),
            __('Priority', 'api-volunteer-manager'),
            'todo-priority',
            array(self::$postTypeSlug),
            array(
                'hierarchical' => false
            )
        );

        //Remove deafult UI
        /*(new MetaBox)->remove(
            "tagsdiv-todo-priority", 
            self::$postTypeSlug
        );*/ 

        //Add filter
        new Filter(
            'todo-priority',
            'assignment'
        );

        //Return taxonomy slug
        return $categories->slug;
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
            'todo-status',
            array(self::$postTypeSlug),
            array(
                'hierarchical' => false
            )
        );

        //Remove deafult UI
        (new MetaBox)->remove(
            "tagsdiv-todo-status", 
            self::$postTypeSlug
        ); 

        //Add filter
        new Filter(
            'todo-status',
            'assignment'
        );

        //Return taxonomy slug
        return $categories->slug;
    }

    /**
     * Create category taxonomy
     * @return string
     */
    public function taxonomyCategory() : string
    {
        //Register new taxonomy
        $categories = new Taxonomy(
            __('Categories', 'api-volunteer-manager'),
            __('Category', 'api-volunteer-manager'),
            'todo-category',
            array(self::$postTypeSlug),
            array(
                'hierarchical' => true
            )
        );

        //Add filter
        new Filter(
            'todo-category',
            'assignment'
        );

        //Return taxonomy slug
        return $categories->slug;
    }

    /**
     * Create type taxonomy
     * @return string
     */
    public function taxonomyType() : string
    {
        //Register new taxonomy
        $categories = new Taxonomy(
            __('Types', 'api-volunteer-manager'),
            __('Type', 'api-volunteer-manager'),
            'todo-type',
            array(self::$postTypeSlug),
            array(
                'hierarchical' => false
            )
        );

        //Remove deafult UI
        (new MetaBox)->remove(
            "tagsdiv-todo-type", 
            self::$postTypeSlug
        ); 

        //Add filter
        new Filter(
            'todo-type',
            'assignment'
        );

        //Return taxonomy slug
        return $categories->slug;
    }

    /**
     * Create category taxonomy
     * @return string
     */
    public function taxonomySprint() : string
    {
        //Register new taxonomy
        $categories = new Taxonomy(
            __('Sprints', 'api-volunteer-manager'),
            __('Sprint', 'api-volunteer-manager'),
            'todo-sprint',
            array(self::$postTypeSlug),
            array(
                'hierarchical' => true
            )
        );

        //Remove deafult UI
        (new MetaBox)->remove(
            "todo-sprintdiv", 
            self::$postTypeSlug
        ); 


        //Add filter
        new Filter(
            'todo-sprint',
            'assignment'
        );

        //Return taxonomy slug
        return $categories->slug;
    }
}
