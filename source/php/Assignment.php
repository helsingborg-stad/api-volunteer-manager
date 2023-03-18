<?php

namespace VolunteerManager;

use VolunteerManager\Components\EditPostStatusButtons\EditPostStatusButtonFactory as EditPostStatusButtonFactory;
use VolunteerManager\Entity\Filter as Filter;
use VolunteerManager\Entity\ITerm;
use VolunteerManager\Entity\PostType as PostType;
use VolunteerManager\Entity\Taxonomy as Taxonomy;
use VolunteerManager\Helper\Admin\UI as AdminUI;
use VolunteerManager\Helper\Admin\UrlBuilder as UrlBuilder;
use VolunteerManager\Helper\Icon as Icon;
use VolunteerManager\Helper\MetaBox as MetaBox;

class Assignment
{
    public static string $postTypeSlug;

    public function __construct()
    {
        //Main post type
        self::$postTypeSlug = $this->postType();
    }



    public function addHooks()
    {
        add_action('admin_post_update_post_status', array($this, 'updatePostStatus'));
        add_action('add_meta_boxes', array($this, 'registerSubmitterMetaBox'), 10, 2);
        add_action('init', array($this, 'registerStatusTaxonomy'));

        add_filter('avm_notification', array($this, 'populateNotificationSender'), 10, 1);
        add_filter('avm_external_assignment_approved_notification', array($this, 'populateNotificationReceiverWithSubmitter'), 10, 2);
        add_filter('avm_external_assignment_denied_notification', array($this, 'populateNotificationReceiverWithSubmitter'), 10, 2);
    }

    /**
     * Populates notifications with sender email address
     * @param array $args
     * @return array
     */
    public function populateNotificationSender(array $args): array
    {
        $senderOption = get_field('notification_sender', 'option');
        $senderEmail = $senderOption['email'] ?? '';
        $sender = $senderOption['name'] ? "{$senderOption['name']} <{$senderEmail}>" : $senderEmail;
        $args['from'] = $sender;
        return $args;
    }

    /**
     * Populate notification with receiver email address
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateNotificationReceiverWithSubmitter(array $args, int $postId): array
    {
        $receiver = get_post_meta($postId, 'submitted_by_email', true);
        $args['to'] = $receiver ?? '';
        return $args;
    }

    /**
     * Create post type
     * @return void
     */
    public function postType(): string
    {
        // Create post type
        $postType = new PostType(
            _x('Assignments', 'Post type plural', 'api-volunteer-manager'),
            _x('Assignment', 'Post type singular', 'api-volunteer-manager'),
            'assignment',
            array(
                'description' => __('Assignments', 'api-volunteer-manager'),
                'menu_icon' => Icon::get('person'),
                'publicly_queriable' => true,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'has_archive' => true,
                'rewrite' => array(
                    'slug' => __('assignment', 'api-volunteer-manager'),
                    'with_front' => false
                ),
                'hierarchical' => false,
                'exclude_from_search' => true,
                'taxonomies' => array(),
                'supports' => array('title', 'revisions'),
                'show_in_rest' => true
            )
        );

        $postType->addTableColumn(
            'status',
            __('Status', AVM_TEXT_DOMAIN),
            true,
            function ($column, $postId) {
                echo AdminUI::createTaxonomyPills(
                    get_the_terms(
                        $postId,
                        'assignment-status'
                    )
                );
            }
        );

        $postType->addTableColumn(
            'visibility',
            __('Visibility', AVM_TEXT_DOMAIN),
            false,
            function ($column, $postId) {
                $postStatus = get_post_status($postId);
                $editButton = EditPostStatusButtonFactory::create($postId, $postStatus, new UrlBuilder());
                echo $editButton->getHtml();
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

        return $postType->slug;
    }

    /**
     * Update post status
     * @return void
     */
    public function updatePostStatus()
    {
        $paged = filter_input(INPUT_GET, 'paged',);
        $nonce = filter_input(INPUT_GET, 'nonce',);
        $postId = filter_input(INPUT_GET, 'post_id');
        $postStatus = filter_input(INPUT_GET, 'post_status');

        $queryString = http_build_query(array(
            'post_type' => self::$postTypeSlug,
            'paged' => $paged,
        ));

        $redirectUrl = admin_url('edit.php') . '?' . $queryString;

        if (!wp_verify_nonce($nonce, 'edit_post_status')) {
            wp_redirect($redirectUrl);
        }

        $post = get_post($postId, 'ARRAY_A');
        $post['post_status'] = $postStatus;
        wp_update_post($post);

        wp_redirect($redirectUrl);
        exit();
    }

    /**
     * Create status taxonomy
     */
    public function registerStatusTaxonomy()
    {
        //Register new taxonomy
        $status = new Taxonomy(
            __('Statuses', 'api-volunteer-manager'),
            __('Status', 'api-volunteer-manager'),
            'assignment-status',
            array(self::$postTypeSlug),
            array(
                'hierarchical' => false
            )
        );

        $status->registerTaxonomy();

        //Remove default UI
        (new MetaBox)->remove(
            "tagsdiv-assignment-status",
            self::$postTypeSlug
        );

        //Add filter
        new Filter(
            'assignment-status',
            'assignment'
        );
    }

    /**
     * Register custom meta boxes
     * @return void
     */
    public function registerSubmitterMetaBox($postType, $post)
    {
        if (!$submittedByEmail = get_post_meta($post->ID, 'submitted_by_email', true)) {
            return;
        }
        add_meta_box(
            'submitter-info',
            __('Submitted by', AVM_TEXT_DOMAIN),
            array($this, 'renderSubmitterData'),
            array('assignment'),
            'normal',
            'low',
            array(
                'submittedByEmail' => $submittedByEmail,
                'submittedByPhone' => get_post_meta($post->ID, 'submitted_by_phone', true) ?? null
            )
        );
    }

    /**
     * Displays contact information about the post submitter
     * @param object $post
     * @param array  $args
     * @return void
     */
    public function renderSubmitterData(object $post, array $args): void
    {
        $content = sprintf('<p>%s</p>', __('Contact details of the person who submitted the assignment.', AVM_TEXT_DOMAIN));
        $content .= $args['args']['submittedByEmail'] ? sprintf('<p><strong>%1$s:</strong> <a href="mailto:%2$s">%2$s</a></p>', __('Email', AVM_TEXT_DOMAIN), $args['args']['submittedByEmail']) : '';
        $content .= $args['args']['submittedByPhone'] ? sprintf('<p><strong>%s:</strong> %s</p>', __('Phone', AVM_TEXT_DOMAIN), $args['args']['submittedByPhone']) : '';
        echo $content;
    }

    public function insertAssignmentStatusTerms(): void
    {
        $term_items = [
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
