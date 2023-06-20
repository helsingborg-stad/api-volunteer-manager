<?php

namespace VolunteerManager\PostType\Employee;

use VolunteerManager\Components\ApplicationMetaBox\ApplicationMetaBox;
use VolunteerManager\Entity\Filter as Filter;
use VolunteerManager\Entity\PostType as PostType;
use VolunteerManager\Entity\Taxonomy as Taxonomy;
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
        add_action('before_delete_post', array($this, 'deleteRelatedApplications'));

        add_filter('acf/load_field/name=notes_date_updated', array($this, 'acfSetNotesDefaultDate'));

        add_action('restrict_manage_posts', [$this, 'addMetaFilterDropdown']);
        add_filter('pre_get_posts', [$this, 'applyMetaFilters']);
    }

    public function addMetaFilterDropdown()
    {
        global $typenow;

        if ('employee' === $typenow) {
            $this->addCustomMetaFilterDropdown('swedish_language_proficiency', __('Language proficiency', AVM_TEXT_DOMAIN));
        }
    }

    public function addCustomMetaFilterDropdown($fieldKey, $dropdownTitle)
    {
        $field = acf_get_field($fieldKey);

        if ($field) {
            $selected_value = $_GET[$fieldKey] ?? '';

            echo '<select name="' . $fieldKey . '">';
            echo '<option value="">' . $dropdownTitle . '</option>';

            foreach ($field['choices'] as $value => $label) {
                echo '<option value="' . $value . '" ' . selected($selected_value, $value, false) . '>' . $label . '</option>';
            }

            echo '</select>';
        }
    }

    public function applyMetaFilters($query)
    {
        global $typenow;

        if ('employee' === $typenow) {
            $this->applyCustomMetaFilter($query, 'swedish_language_proficiency');
        }
    }

    public function applyCustomMetaFilter($query, $fieldKey)
    {
        global $pagenow;

        if ('edit.php' === $pagenow && isset($_GET[$fieldKey])) {
            $meta_value = sanitize_text_field($_GET[$fieldKey]);

            if (!empty($meta_value)) {
                $query->query_vars['meta_key'] = $fieldKey;
                $query->query_vars['meta_value'] = $meta_value;
            }
        }
    }

    public function initTaxonomiesAndTerms()
    {
        $this->registerStatusTaxonomy();
        $this->insertEmploymentStatusTerms();
    }

    /**
     * @return void
     */
    public function addPostTypeTableColumn()
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
    public function registerStatusTaxonomy()
    {
        $this->employeeTaxonomy = new Taxonomy(
            __('Statuses', AVM_TEXT_DOMAIN),
            __('Status', AVM_TEXT_DOMAIN),
            'employee-registration-status',
            array($this->slug),
            array(
                'hierarchical' => false,
                'show_ui' => false
            )
        );

        $this->employeeTaxonomy->registerTaxonomy();

        new Filter(
            'employee-registration-status',
            'employee'
        );
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
        $applicationMetaBox = new ApplicationMetaBox(
            $post,
            __('Assignments', AVM_TEXT_DOMAIN),
            'application_employee'
        );
        $applicationMetaBox->register();
    }

    /**
     * Permanently deletes applications related to the employee
     * @param $postId
     * @return void
     */
    public function deleteRelatedApplications($postId)
    {
        $postType = get_post_type($postId);
        if (!wp_is_post_revision($postId) && !wp_is_post_autosave($postId) && $postType === 'employee') {
            $args = array(
                'post_status' => 'any',
                'meta_key' => 'application_employee',
                'meta_value' => $postId,
                'post_type' => 'application',
                'posts_per_page' => -1,
                'fields' => 'ids',
            );

            $relatedApplications = get_posts($args);
            foreach ($relatedApplications as $application) {
                wp_delete_post($application, true);
            }
        }
    }
}
