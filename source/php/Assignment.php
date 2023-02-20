<?php

namespace VolunteerManager;

use \VolunteerManager\Entity\Taxonomy as Taxonomy;
use \VolunteerManager\Entity\PostType as PostType;
use \VolunteerManager\Entity\Filter as Filter;

use \VolunteerManager\Helper\MetaBox as MetaBox;
use \VolunteerManager\Helper\Icon as Icon;
use \VolunteerManager\Helper\Admin\UI as AdminUI;
use \VolunteerManager\Components\EditPostStatusButtons\EditPostStatusButtonFactory as EditPostStatusButtonFactory;
use \VolunteerManager\Helper\Admin\UrlBuilder as UrlBuilder;

class Assignment
{
    public static $postTypeSlug;
    public static $statusTaxonomySlug;

    public function __construct()
    {
        //Main post type
        self::$postTypeSlug = $this->postType();

        //Taxonomy
        self::$statusTaxonomySlug = $this->taxonomyStatus();

        add_action('admin_post_update_post_status', array($this, 'updatePostStatus'));
    }

    /**
     * Create post type
     * @return void
     */
    public function postType(): string
    {
        // Create posttype
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
            'public',
            __('Public', 'api-volunteer-manager'),
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
