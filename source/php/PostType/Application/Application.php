<?php

namespace VolunteerManager\PostType\Application;

use VolunteerManager\Entity\Filter;
use VolunteerManager\Entity\TermInterface;
use VolunteerManager\Entity\PostType;
use VolunteerManager\Entity\Taxonomy as Taxonomy;
use VolunteerManager\Helper\Admin\UI as AdminUI;
use WP_Error;

class Application extends PostType
{
    private Taxonomy $applicationTaxonomy;

    public function addHooks(): void
    {
        parent::addHooks();

        add_action('init', [$this, 'initTaxonomiesAndTerms']);
        add_action('init', [$this, 'addStatusTableColumn']);
        add_action('acf/save_post', array($this, 'setApplicationPostTitle'));
        add_action('add_meta_boxes', array($this, 'registerEligibilityMetaBox'), 10, 2);
    }

    public function initTaxonomiesAndTerms(): void
    {
        $this->registerStatusTaxonomy();
        $this->insertStatusTerms($this->applicationTaxonomy);
    }

    /**
     * Register status taxonomy
     *
     * @return void
     */
    public function registerStatusTaxonomy(): void
    {
        $this->applicationTaxonomy = new Taxonomy(
            __('Application statuses', 'api-volunteer-manager'),
            __('Application status', 'api-volunteer-manager'),
            'application-status',
            array($this->slug),
            array(
                'hierarchical' => false,
                'show_ui' => false
            )
        );

        $this->applicationTaxonomy->registerTaxonomy();

        //Add filter
        new Filter(
            'application-status',
            'application'
        );
    }

    

    /**
     * Insert status terms
     *
     * @return array|WP_Error
     */
    public function insertStatusTerms(TermInterface $taxonomy)
    {
        return $taxonomy->insertTerms(ApplicationConfiguration::getStatusTerms());
    }

    /**
     * Adds a column with status
     * @return void
     */
    public function addStatusTableColumn(): void
    {
        $this->addTableColumn(
            'status',
            __('Status', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                echo AdminUI::createTaxonomyPills(
                    get_the_terms(
                        $postId,
                        'application-status'
                    )
                );
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
            'title',
            __('Application', 'api-volunteer-manager'),
            true,
            function ($column, $postId) {
                echo get_the_title($postId);
            }
        );
    }

    /**
     * Sets post title
     * @param $postId
     * @return void
     */
    public function setApplicationPostTitle($postId)
    {
        if (get_post_type($postId) !== 'application') {
            return;
        }

        $employee = get_field('application_employee', $postId);
        $assignment = get_field('application_assignment', $postId);

        $postData = array(
            'ID' => $postId,
            'post_title' => trim("{$employee->post_title} - {$assignment->post_title}"),
        );
        wp_update_post($postData);
    }

    /**
     * Register custom meta boxes
     * @return void
     */
    public function registerEligibilityMetaBox($postType, $post)
    {
        $employee = get_field('application_employee', $post->ID);
        $assignment = get_field('application_assignment', $post->ID);

        if (empty($employee) || empty($assignment)) {
            return;
        }

        add_meta_box(
            'application-eligibility',
            __('Eligibility', 'api-volunteer-manager'),
            array($this, 'renderEligibilityMetaBox'),
            array('application'),
            'normal',
            'low',
            ['employee' => $employee, 'assignment' => $assignment]
        );
    }

    /**
     * Displays contact information about the post submitter
     * @param object $post
     * @param array  $args
     * @return void
     */
    public function renderEligibilityMetaBox(object $post, array $args): void
    {
        $employee = $args['args']['employee'];
        $assignment = $args['args']['assignment'];
        $employeeEligibility = $this->getEmployeeEligibilityLevel($args['args']['employee']);
        $assignmentEligibility = $this->getAssignmentEligibilityLevel($args['args']['assignment']);
        $eligibilityClass = $employeeEligibility < $assignmentEligibility ? 'red' : null;
        $classAttr = $eligibilityClass ? "class=\"{$eligibilityClass}\"" : '';
        $content = sprintf(
            __('<p><a href="%s">%s</a>:<br><span>Level %s</span></p><p><a href="%s">%s</a>:<br><span %s>Level %s</span></p>', 'api-volunteer-manager'),
            get_edit_post_link($assignment),
            $assignment->post_title,
            $assignmentEligibility,
            get_edit_post_link($employee),
            $employee->post_title,
            $classAttr,
            $employeeEligibility
        );
        $content .= $employeeEligibility < $assignmentEligibility ? '<p><i>' . __('The employees eligibility level does not match the assignment.', 'api-volunteer-manager') . '</i></p>' : '';
        echo $content;
    }

    /**
     * Calculates an employee's eligibility level
     * @param object $employee An object representing the employee.
     * @return int The eligibility level, which can be either 1 (eligible) or 2 (ineligible).
     */
    private function getEmployeeEligibilityLevel(object $employee): int
    {
        $employeeCrimeRecord = get_field('crime_record_extracted', $employee->ID);
        return $employeeCrimeRecord ? 2 : 1;
    }

    /**
     * Gets the eligibility level for an assignment.
     * @param object $post The post object for the assignment.
     * @return int The eligibility level, which can be either 1 (eligible) or a higher value indicating ineligibility.
     */
    private function getAssignmentEligibilityLevel(object $post): int
    {
        $eligibilityTerms = get_the_terms($post->ID, 'assignment-eligibility');
        return isset($eligibilityTerms[0]) ? (int)$eligibilityTerms[0]->slug : 1;
    }
}