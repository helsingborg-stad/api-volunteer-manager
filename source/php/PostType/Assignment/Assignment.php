<?php

namespace VolunteerManager\PostType\Assignment;

use VolunteerManager\Components\ApplicationMetaBox\ApplicationMetaBox;
use VolunteerManager\Components\EditPostStatusButtons\EditPostStatusButtonFactory as EditPostStatusButtonFactory;
use VolunteerManager\Entity\Filter as Filter;
use VolunteerManager\Entity\PostType;
use VolunteerManager\Entity\Taxonomy as Taxonomy;
use VolunteerManager\Helper\Admin\UI as AdminUI;
use VolunteerManager\Helper\Admin\UrlBuilder as UrlBuilder;
use VolunteerManager\Helper\MetaBox as MetaBox;

class Assignment extends PostType
{
    private Taxonomy $assignmentTaxonomy;
    private Taxonomy $eligibilityTaxonomy;

    public function addHooks(): void
    {
        parent::addHooks();
        add_action('admin_post_update_post_status', array($this, 'updatePostStatus'));
        add_action('add_meta_boxes', array($this, 'registerSubmitterMetaBox'), 10, 2);
        add_action('add_meta_boxes', array($this, 'registerApplicationsMetaBox'), 10, 2);
        add_action('init', array($this, 'registerStatusTaxonomy'));
        add_action('init', array($this, 'registerCategoryTaxonomy'));
        add_action('init', array($this, 'insertAssignmentStatusTerms'));
        add_action('init', array($this, 'registerEligibilityTaxonomy'));
        add_action('init', array($this, 'insertAssignmentEligibilityTerms'));
        add_action('init', array($this, 'addPostTypeTableColumn'));
    }

    /**
     * Adds custom table columns to the specified post type's admin list table.
     *
     * @return void
     */
    public function addPostTypeTableColumn(): void
    {
        $this->addTableColumn(
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

        $this->addTableColumn(
            'visibility',
            __('Visibility', AVM_TEXT_DOMAIN),
            false,
            function ($column, $postId) {
                $postStatus = get_post_status($postId);
                $editButton = EditPostStatusButtonFactory::create($postId, $postStatus, new UrlBuilder());
                echo $editButton->getHtml();
            }
        );

        $this->addTableColumn(
            'submitted_from',
            __('Submitted from', AVM_TEXT_DOMAIN),
            false,
            function ($column, $postId) {
                echo get_post_meta($postId, 'source', true);
            }
        );
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
            'post_type' => $this->postType->slug,
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
        $this->assignmentTaxonomy = new Taxonomy(
            __('Statuses', 'api-volunteer-manager'),
            __('Status', 'api-volunteer-manager'),
            'assignment-status',
            array($this->slug),
            array(
                'hierarchical' => false,
                'show_ui' => false
            )
        );

        $this->assignmentTaxonomy->registerTaxonomy();

        //Remove default UI
        (new MetaBox)->remove(
            "tagsdiv-assignment-status",
            $this->slug,
        );

        //Add filter
        new Filter(
            'assignment-status',
            'assignment'
        );
    }

    /**
     * Create category taxonomy
     */
    public function registerCategoryTaxonomy()
    {
        $categoryTaxonomy = new Taxonomy(
            __('Categories', 'api-volunteer-manager'),
            __('Category', 'api-volunteer-manager'),
            'assignment-category',
            array($this->slug),
            array(
                'hierarchical' => false,
                'show_ui' => true
            )
        );

        $categoryTaxonomy->registerTaxonomy();

        //Remove default UI
        (new MetaBox)->remove(
            "tagsdiv-assignment-category",
            $this->slug,
        );

        //Add filter
        new Filter(
            'assignment-category',
            'assignment'
        );
    }

    /**
     * Create eligibility taxonomy
     */
    public function registerEligibilityTaxonomy()
    {
        $this->eligibilityTaxonomy = new Taxonomy(
            __('Eligibility', 'api-volunteer-manager'),
            __('Eligibility', 'api-volunteer-manager'),
            'assignment-eligibility',
            array($this->slug),
            array(
                'hierarchical' => false,
                'show_ui' => false
            )
        );

        $this->eligibilityTaxonomy->registerTaxonomy();

        //Remove default UI
        (new MetaBox)->remove(
            "tagsdiv-assignment-eligibility",
            $this->slug,
        );

        //Add filter
        new Filter(
            'assignment-eligibility',
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
                'submittedByPhone' => get_post_meta($post->ID, 'submitted_by_phone', true) ?? '',
                'submittedByFirstName' => get_post_meta($post->ID, 'submitted_by_first_name', true) ?? '',
                'submittedBySurname' => get_post_meta($post->ID, 'submitted_by_surname', true) ?? '',
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
        $content .= sprintf('<p><strong>%s:</strong> %s %s</p>', __('Name', AVM_TEXT_DOMAIN), $args['args']['submittedByFirstName'], $args['args']['submittedBySurname']);
        $content .= sprintf('<p><strong>%1$s:</strong> <a href="mailto:%2$s">%2$s</a></p>', __('Email', AVM_TEXT_DOMAIN), $args['args']['submittedByEmail']);
        $content .= sprintf('<p><strong>%s:</strong> %s</p>', __('Phone', AVM_TEXT_DOMAIN), $args['args']['submittedByPhone']);
        echo $content;
    }

    /**
     * Register applications meta box
     * @return void
     */
    public function registerApplicationsMetaBox($postType, $post)
    {
        if ($postType !== 'assignment') {
            return;
        }
        $applicationMetaBox = new ApplicationMetaBox(
            $post,
            __('Employees', AVM_TEXT_DOMAIN),
            'application_assignment'
        );
        $applicationMetaBox->register();
    }

    public function insertAssignmentStatusTerms()
    {
        return $this->assignmentTaxonomy->insertTerms(AssignmentConfiguration::getStatusTerms());
    }

    public function insertAssignmentEligibilityTerms()
    {
        return $this->eligibilityTaxonomy->insertTerms(AssignmentConfiguration::getEligibilityTerms());
    }
}
