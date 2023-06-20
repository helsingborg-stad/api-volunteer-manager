<?php

namespace VolunteerManager\Entity;

class MetaFilter
{
    /**
     * Adds a custom meta filter dropdown based on the field type.
     *
     * @param string $fieldKey      The key of the custom field.
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

    /**
     * Renders the dropdown for the true/false field type.
     *
     * @param string $fieldKey      The key of the custom field.
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

    /**
     * Renders the dropdown for the select field type.
     *
     * @param string $fieldKey      The key of the custom field.
     * @param string $dropdownTitle The title of the dropdown.
     * @param array  $choices       The choices for the dropdown.
     * @param string $selectedValue The currently selected value.
     */
    private function renderSelectDropdown(string $fieldKey, string $dropdownTitle, array $choices, string $selectedValue)
    {
        echo '<select name="' . $fieldKey . '">';
        echo '<option value="">' . $dropdownTitle . '</option>';
        foreach ($choices as $value => $label) {
            echo '<option value="' . $value . '" ' . selected($selectedValue, $value, false) . '>' . $label . '</option>';
        }
        echo '</select>';
    }

    /**
     * Apply custom meta filters
     * @param $query
     * @param $fieldKey
     * @return void
     */
    public function applyCustomMetaFilter($query, $fieldKey)
    {
        global $pagenow;

        if ('edit.php' === $pagenow && isset($_GET[$fieldKey])) {
            $meta_value = sanitize_text_field($_GET[$fieldKey]);
            if ($meta_value !== '') {
                $meta_query = array(
                    'key' => $fieldKey,
                    'value' => $meta_value,
                );
                $query->query_vars['meta_query'][] = $meta_query;
            }
        }
    }
}