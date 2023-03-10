<?php

namespace VolunteerManager;

use VolunteerManager\Components\EditPostStatusButtons\EditPostStatusButtonFactory as EditPostStatusButtonFactory;
use VolunteerManager\Entity\Filter as Filter;
use VolunteerManager\Entity\PostType as PostType;
use VolunteerManager\Entity\Taxonomy as Taxonomy;
use VolunteerManager\Helper\Admin\UI as AdminUI;
use VolunteerManager\Helper\Admin\UrlBuilder as UrlBuilder;
use VolunteerManager\Helper\Icon as Icon;
use VolunteerManager\Helper\MetaBox as MetaBox;
use VolunteerManager\Notification\NotificationHandlerInterface;

class Assignment
{
    private NotificationHandlerInterface $notificationHandler;
    public static string $postTypeSlug;
    public static string $statusTaxonomySlug;

    public function __construct($notificationHandler)
    {
        $this->notificationHandler = $notificationHandler;
        //Main post type
        self::$postTypeSlug = $this->postType();
        //Taxonomy
        self::$statusTaxonomySlug = $this->taxonomyStatus();
    }

    public function addHooks()
    {
        add_action('admin_post_update_post_status', array($this, 'updatePostStatus'));
        add_action('set_object_terms', array($this, 'scheduleTermNotifications'), 10, 6);
        add_action('add_meta_boxes', array($this, 'registerSubmitterMetaBox'), 10, 2);

        add_filter('avm_notification', array($this, 'populateNotificationSender'), 10, 1);
        add_filter('avm_assignment_approved_notification', array($this, 'populateNotificationReceiver'), 10, 2);
        add_filter('avm_assignment_denied_notification', array($this, 'populateNotificationReceiver'), 10, 2);
    }

    /**
     * Populates notifications with sender email address
     * @param array $args
     * @return array
     */
    public function populateNotificationSender(array $args): array
    {
        $args['from'] = 'no-reply@helsingborg.se';
        return $args;
    }

    /**
     * Populate notification with receiver email address
     * @param array $args
     * @param int   $postId
     * @return array
     */
    public function populateNotificationReceiver(array $args, int $postId): array
    {
        // TODO: Set correct email key
        $receiver = get_field('contact_email', $postId);
        $args['to'] = $receiver ?? '';
        return $args;
    }

    /**
     * Registers notification events when status taxonomy changes
     * @param int    $objectId
     * @param array  $terms
     * @param array  $newIds
     * @param string $taxonomy
     * @param bool   $append
     * @param array  $oldIds
     * @return void
     */
    public function scheduleTermNotifications(int $objectId, array $terms, array $newIds, string $taxonomy, bool $append, array $oldIds): void
    {
        if (empty($this->notificationHandler->getNotifications(self::$postTypeSlug, $taxonomy))) {
            return;
        }
        $this->notificationHandler->scheduleNotificationsForTermUpdates($newIds, $oldIds, self::$postTypeSlug, $taxonomy, $objectId);
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

        $postType->addTableColumn(
            'visibility',
            __('Visibility', 'api-volunteer-manager'),
            false,
            function ($column, $postId) {
                $postStatus = get_post_status($postId);
                $editButton = EditPostStatusButtonFactory::create($postId, $postStatus, new UrlBuilder());
                echo $editButton->getHtml();
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
        $paged = filter_input(INPUT_GET, 'paged', FILTER_SANITIZE_STRING);
        $nonce = filter_input(INPUT_GET, 'nonce', FILTER_SANITIZE_STRING);
        $postId = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_STRING);
        $postStatus = filter_input(INPUT_GET, 'post_status', FILTER_SANITIZE_STRING);

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
     * @return string
     */
    public function taxonomyStatus(): string
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

        //Return taxonomy slug
        return $categories->slug;
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
}
