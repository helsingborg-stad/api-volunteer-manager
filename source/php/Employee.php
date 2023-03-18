<?php

namespace VolunteerManager;

use VolunteerManager\Entity\ITerm;
use \VolunteerManager\Entity\PostType as PostType;
use \VolunteerManager\Entity\Taxonomy as Taxonomy;
use \VolunteerManager\Helper\Icon as Icon;

class Employee
{
    private static PostType $postType;

    private Taxonomy $employeeTaxonomy;

    public function __construct()
    {
        self::$postType = $this->postType();
        $this->addPostTypeTableColumn(self::$postType);
    }

    public function addHooks()
    {
        add_action('init', array($this, 'registerStatusTaxonomy'));
        add_action('init', array($this, 'insertEmploymentStatusTerms'));
        add_filter('avm_external_volunteer_new_notification', array($this, 'populateNotificationReceiverWithSubmitter'), 10, 2);
        add_filter('avm_admin_external_volunteer_new_notification', array($this, 'populateNotificationReceiverWithAdmin'), 10, 2);
    }

    /**
     * Populate notification receiver with submitter email address
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateNotificationReceiverWithSubmitter(array $args, int $postId): array
    {
        $receiver = get_field('email', $postId);
        $args['to'] = $receiver ?? '';
        return $args;
    }

    /**
     * Populate notification receiver with admin email addresses
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateNotificationReceiverWithAdmin(array $args, int $postId): array
    {
        $receivers = get_field('notification_receivers', 'option') ?? [];
        $emailArray = array_column($receivers, 'email');
        $emailsString = implode(',', $emailArray);
        $args['to'] = $emailsString;
        return $args;
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

        $postType->addTableColumn(
            'submitted_from',
            __('Submitted from', AVM_TEXT_DOMAIN),
            false,
            function ($column, $postId) {
                echo get_post_meta($postId, 'source', true);
            }
        );
    }

    /**
     * Create terms for the employee status taxonomy
     *
     * @param $postType
     * @return void
     */
    public function registerStatusTaxonomy() : void
    {
        $this->employeeTaxonomy = new Taxonomy(
            'Registration statuses',
            'Registration status',
            'employee-registration-status',
            // array($postType->slug),
            array(self::$postType->slug),
            array (
                'hierarchical' => false,
                'show_ui' => true
            )
        );
    }

    /**
     * Insert terms for the employee status taxonomy
     *
     */
    public function insertEmploymentStatusTerms()
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

        return $this->employeeTaxonomy->insertTerms($term_items);
    }
}
