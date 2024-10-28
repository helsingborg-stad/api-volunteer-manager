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
        add_action('before_delete_post', array($this, 'deleteRelatedApplications'));
        add_action('set_object_terms', array($this, 'draftOnStatusCompleted'), 10, 6);
        add_action('init', array($this, 'updateStatusbyEndDate'));
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
            __('Status', 'api-volunteer-manager'),
            false,
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
            __('Visibility', 'api-volunteer-manager'),
            false,
            function ($column, $postId) {
                $postStatus = get_post_status($postId);
                $editButton = EditPostStatusButtonFactory::create($postId, $postStatus, new UrlBuilder());
                echo $editButton->getHtml();
            }
        );

        $this->addTableColumn(
            'submitted_from',
            __('Submitted from', 'api-volunteer-manager'),
            false,
            function ($column, $postId) {
                echo get_post_meta($postId, 'source', true);
            }
        );

        $this->addTableColumn(
            'eligibility',
            __('Eligibility', 'api-volunteer-manager'),
            false,
            function ($column, $postId) {
                echo (get_the_terms($postId, 'assignment-eligibility'))[0]->name ?? '';
            }
        );

        $this->addTableColumn(
            'title',
            __('Assignment', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                echo get_the_title($postId);
            }
        );

        $this->addTableColumn(
            'end_date',
            __('End date', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                echo get_field('end_date',$postId);
            }
        );

        $this->addTableColumn(
            'date',
            __('Date', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                echo get_the_date($postId);
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
            'post_type' => $this->slug,
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
            __('Submitted by', 'api-volunteer-manager'),
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
        $content = sprintf('<p>%s</p>', __('Contact details of the person who submitted the assignment.', 'api-volunteer-manager'));
        $content .= sprintf('<p><strong>%s:</strong> %s %s</p>', __('Name', 'api-volunteer-manager'), $args['args']['submittedByFirstName'], $args['args']['submittedBySurname']);
        $content .= sprintf('<p><strong>%1$s:</strong> <a href="mailto:%2$s">%2$s</a></p>', __('Email', 'api-volunteer-manager'), $args['args']['submittedByEmail']);
        $content .= sprintf('<p><strong>%s:</strong> %s</p>', __('Phone', 'api-volunteer-manager'), $args['args']['submittedByPhone']);
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
            __('Employees', 'api-volunteer-manager'),
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

    /**
     * Handles the transition of an assignment post to a 'draft' status when the status changes to 'completed'.
     * If the new term of the post is 'completed' and the old term is not 'completed', the post status is changed to
     * 'draft'.
     *
     */
    function draftOnStatusCompleted($object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids)
    {
        if ('assignment-status' !== $taxonomy) {
            return;
        }

        $completedTerm = get_term_by('slug', 'completed', 'assignment-status');

        // If the new term is 'completed' and the old term is not 'completed', draft the post.
        if (in_array($completedTerm->term_id, $tt_ids) && !in_array($completedTerm->term_id, $old_tt_ids)) {
            wp_update_post(array(
                'ID' => $object_id,
                'post_status' => 'draft'
            ));
        }
    }

    /**
     * Permanently delete applications related to the assignment
     *
     * @param int $postId
     * @return void
     */
    public function deleteRelatedApplications(int $postId)
    {
        $postType = get_post_type($postId);
        if (
            !wp_is_post_revision($postId) &&
            !wp_is_post_autosave($postId) &&
            $postType === 'assignment'
        ) {
            $args = array(
                'post_type' => 'application',
                'post_per_page' => '-1',
                'post_status' => 'any',
                'meta_query' => array(
                    array(
                        'key' => 'application_assignment',
                        'value' => $postId,
                        'compare' => '='
                    )
                )
            );

            $relatedApplications = get_posts($args);
            foreach ($relatedApplications as $application) {
                wp_delete_post($application->ID, true);
            }
        }
    }

    /**
     * Updates status by end date
     */
    
     public function updateStatusbyEndDate(){
        // Hämta poster med end date
        $posts = get_posts(array(
            'post_type' => 'assignment',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'end_date',
                    'type' => 'DATE',
                    'compare' => '<',
                    'value' => date('Y-m-d')
                )
            )
        ));
        // Uppdatera status om det nått end date
        foreach($posts as $post){
            wp_set_post_terms($post->ID, 'completed', 'assignment-status', false);
        }
     }
}
