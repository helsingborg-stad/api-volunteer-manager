<?php

namespace VolunteerManager\Entity;

class CustomFieldFilter
{
    /**
     * Adds a custom meta filter dropdown based on the field type.
     *
     * @param string $fieldKey The key of the custom field.
     * @param string $dropdownTitle The title of the dropdown.
     */
    public function addCustomMetaFilterDropdown(string $fieldKey, string $dropdownTitle)
    {
        $field = acf_get_field($fieldKey);

        if ($field) {
            $selectedValue = $_GET[$fieldKey] ?? '';

            // Render the dropdown based on the field type
            if ($field['type'] === 'true_false') {
                $this->renderTrueFalseDropdown($fieldKey, $dropdownTitle, $selectedValue);
            } elseif ($field['type'] === 'select') {
                $this->renderSelectDropdown($fieldKey, $dropdownTitle, $field['choices'], $selectedValue);
            }
        }
    }

    public function addCustomAssignmentFilterDropdown(string $dropdownTitle)
    {
        $assignments = get_posts([
            'post_type' => 'assignment',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'suppress_filters' => true,
        ]);

        if ($assignments) {
            $selectedAssignment = $_GET['assignment_id'] ?? '';

            $this->renderPostDropdown('assignment_id', $dropdownTitle, $assignments, $selectedAssignment);
        }
    }

    /**
     * Renders the dropdown.
     *
     * @param string $name The name of the select element.
     * @param array $options The options for the dropdown.
     * @param string $selectedValue The currently selected value.
     * @param string $dropdownTitle The title of the dropdown.
     */
    private function renderDropdown(string $name, array $options, string $selectedValue, string $dropdownTitle)
    {
        echo '<select name="' . $name . '">';
        echo '<option value="">' . $dropdownTitle . '</option>';
        foreach ($options as $value => $label) {
            echo '<option value="' . $value . '" ' . selected($selectedValue, (string)$value, false) . '>' . $label . '</option>';
        }
        echo '</select>';
    }

    /**
     * Renders the dropdown for the posts.
     *
     * @param string $postType The post type.
     * @param string $dropdownTitle The title of the dropdown.
     * @param array $posts The posts to be listed in the dropdown.
     * @param string $selectedValue The currently selected value.
     */
    private function renderPostDropdown(string $postType, string $dropdownTitle, array $posts, string $selectedValue)
    {
        $options = [];
        foreach ($posts as $post) {
            $options[$post->ID] = $post->post_title;
        }

        $this->renderDropdown($postType, $options, $selectedValue, $dropdownTitle);
    }

    /**
     * Renders the dropdown for the select field type.
     *
     * @param string $fieldKey The key of the custom field.
     * @param string $dropdownTitle The title of the dropdown.
     * @param array $choices The choices for the dropdown.
     * @param string $selectedValue The currently selected value.
     */
    private function renderSelectDropdown(string $fieldKey, string $dropdownTitle, array $choices, string $selectedValue)
    {
        $this->renderDropdown($fieldKey, $choices, $selectedValue, $dropdownTitle);
    }

    /**
     * Renders the dropdown for the true/false field type.
     *
     * @param string $fieldKey The key of the custom field.
     * @param string $dropdownTitle The title of the dropdown.
     * @param string $selectedValue The currently selected value.
     */
    private function renderTrueFalseDropdown(string $fieldKey, string $dropdownTitle, string $selectedValue)
    {
        echo '<select name="' . $fieldKey . '">';
        echo '<option value="">' . $dropdownTitle . '</option>';
        echo '<option value="1" ' . selected($selectedValue, '1', false) . '>' . __('Yes', AVM_TEXT_DOMAIN) . '</option>';
        echo '<option value="0" ' . selected($selectedValue, '0', false) . '>' . __('No', AVM_TEXT_DOMAIN) . '</option>';
        echo '</select>';
    }

    public function applyCustomFilter($query, $fieldKey, $callback = null)
    {
        global $pagenow;

        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ('edit.php' === $pagenow && isset($_GET[$fieldKey])) {
            $meta_value = sanitize_text_field($_GET[$fieldKey]);
            if ($meta_value !== '') {
                $meta_query = array(
                    'key' => $fieldKey,
                    'value' => $meta_value,
                );

                call_user_func($callback, $meta_query, $query);
            }
        }
    }

    public function applyCustomMetaFilter($query, $fieldKey)
    {
        $this->applyCustomFilter($query, $fieldKey, function ($meta_query, $query) {
            $query->query_vars['meta_query'][] = $meta_query;
        });
    }

    public function applyCustomAssignmentFilter($query, $fieldKey)
    {
        $this->applyCustomFilter($query, $fieldKey, function ($meta_value, $query) {
            $employees = $this->getApplicationEmployees($meta_value['value']);
            $query->query_vars['post__in'] = $employees;
        });
    }

    private function getApplicationEmployees(int $assignmentId): array
    {
        $applicationPosts = get_posts([
            'post_type' => 'application',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'suppress_filters' => false,
            'meta_query' => [
                [
                    'key' => 'application_assignment',
                    'value' => $assignmentId,
                    'compare' => '=',
                ]
            ]
        ]);

        $employees = [];
        foreach ($applicationPosts as $applicationPost) {
            $employee = get_post_meta($applicationPost->ID, 'application_employee', true);
            if ($employee) {
                $employees[] = $employee;
            }
        }

        return $employees;
    }
}
