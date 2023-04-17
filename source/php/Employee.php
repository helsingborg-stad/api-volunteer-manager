<?php

namespace VolunteerManager;

use VolunteerManager\Components\ApplicationMetaBox\EmployeeApplicationMetaBox;
use VolunteerManager\Entity\PostType;
use \VolunteerManager\Entity\Taxonomy as Taxonomy;
use VolunteerManager\Helper\Admin\UI as AdminUI;

class Employee extends PostType
{
    private Taxonomy $employeeTaxonomy;

    public function addHooks(): void
    {
        parent::addHooks();
        add_action('init', array($this, 'initTaxonomiesAndTerms'));
        add_action('init', array($this, 'addPostTypeTableColumn'));
        add_action('acf/save_post', array($this, 'setPostTitle'));
        add_action('add_meta_boxes', array($this, 'registerApplicationsMetaBox'), 10, 2);

        add_filter('avm_external_volunteer_new_notification', array($this, 'populateNotificationReceiverWithSubmitter'), 10, 2);
        add_filter('avm_admin_external_volunteer_new_notification', array($this, 'populateNotificationReceiverWithAdmin'), 10, 2);
        add_filter('acf/load_field/name=notes_date_updated', array($this, 'acfSetNotesDefaultDate'));
    }

    public function initTaxonomiesAndTerms()
    {
        $this->registerStatusTaxonomy();
        $this->insertEmploymentStatusTerms();
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
     * @return void
     */
    public function addPostTypeTableColumn(): void
    {
        $this->addTableColumn(
            'registration_status',
            __('Registration status', AVM_TEXT_DOMAIN),
            true,
            function ($column, $postId) {
                echo AdminUI::createTaxonomyPills(
                    get_the_terms(
                        $postId,
                        'employee-registration-status'
                    )
                );
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
     * Create terms for the employee status taxonomy
     *
     * @return void
     */
    public function registerStatusTaxonomy(): void
    {
        $this->employeeTaxonomy = new Taxonomy(
            'Registration statuses',
            'Registration status',
            'employee-registration-status',
            array($this->slug),
            array(
                'hierarchical' => false,
                'show_ui' => false
            )
        );

        $this->employeeTaxonomy->registerTaxonomy();
    }

    /**
     * Insert terms for the employee status taxonomy
     *
     */
    public function insertEmploymentStatusTerms()
    {
        return $this->employeeTaxonomy->insertTerms(EmployeeConfiguration::getStatusTerms());
    }

    /**
     * Update post title with name
     * @param $postId
     * @return void
     */
    public function setPostTitle($postId)
    {
        if (get_post_type($postId) !== 'employee') {
            return;
        }

        $firstName = get_field('first_name', $postId) ?? '';
        $surname = get_field('surname', $postId) ?? '';
        $postData = array(
            'ID' => $postId,
            'post_title' => trim("{$firstName} {$surname}"),
        );
        wp_update_post($postData);
    }

    /**
     * Set the current date as the default value for ACF notes date picker
     *
     * @param $field
     * @return mixed
     */
    public function acfSetNotesDefaultDate($field)
    {
        $field['default_value'] = date('Y-m-d');
        return $field;
    }

    /**
     * Register applications meta box
     * @return void
     */
    public function registerApplicationsMetaBox($postType, $post)
    {
        if ($postType !== 'employee') {
            return;
        }
        $applicationMetaBox = new EmployeeApplicationMetaBox(
            $post,
            __('Assignments', AVM_TEXT_DOMAIN),
            'application_employee'
        );
        $applicationMetaBox->register();
    }
}
