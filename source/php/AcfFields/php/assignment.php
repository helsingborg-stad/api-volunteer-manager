<?php 


if (function_exists('acf_add_local_field_group')) {

    acf_add_local_field_group(array(
    'key' => 'group_63dce32c807e2',
    'title' => __('Assignment', 'api-volunteer-manager'),
    'fields' => array(
        0 => array(
            'key' => 'field_63dcd749463a4',
            'label' => __('Description', 'api-volunteer-manager'),
            'name' => 'description',
            'aria-label' => '',
            'type' => 'textarea',
            'instructions' => __('Describe the assignment.', 'api-volunteer-manager'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'rows' => '',
            'placeholder' => '',
            'new_lines' => '',
            'acfe_textarea_code' => 0,
        ),
        1 => array(
            'key' => 'field_63dcdb011a906',
            'label' => __('Qualification', 'api-volunteer-manager'),
            'name' => 'qualifications',
            'aria-label' => '',
            'type' => 'textarea',
            'instructions' => __('What is required of the volunteer (e.g., education, access to a car, language).', 'api-volunteer-manager'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'rows' => '',
            'placeholder' => '',
            'new_lines' => '',
            'acfe_textarea_code' => 0,
        ),
        2 => array(
            'key' => 'field_63dcdea1ab055',
            'label' => __('Schedule', 'api-volunteer-manager'),
            'name' => 'schedule',
            'aria-label' => '',
            'type' => 'textarea',
            'instructions' => __('Schedule for when and how often the assignment occurs.', 'api-volunteer-manager'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'rows' => '',
            'placeholder' => '',
            'new_lines' => '',
            'acfe_textarea_code' => 0,
        ),
        3 => array(
            'key' => 'field_63dcfaa623558',
            'label' => __('Benefits', 'api-volunteer-manager'),
            'name' => 'benefits',
            'aria-label' => '',
            'type' => 'textarea',
            'instructions' => __('E.g., bus card, lunch.', 'api-volunteer-manager'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'rows' => '',
            'placeholder' => '',
            'new_lines' => '',
            'acfe_textarea_code' => 0,
        ),
        4 => array(
            'key' => 'field_63dcff972b04c',
            'label' => __('Number of spots', 'api-volunteer-manager'),
            'name' => 'number_of_available_spots',
            'aria-label' => '',
            'type' => 'number',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'min' => '',
            'max' => '',
            'placeholder' => '',
            'step' => '',
            'prepend' => '',
            'append' => '',
        ),
        5 => array(
            'key' => 'field_67160ee8351cc',
            'label' => __('End date', 'api-volunteer-manager'),
            'name' => 'end_date',
            'aria-label' => '',
            'type' => 'date_picker',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'display_format' => 'Y-m-d',
            'return_format' => 'Y-m-d',
            'first_day' => 1,
            'allow_in_bindings' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'assignment',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'acf_after_title',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 1,
    'acfe_display_title' => '',
    'acfe_autosync' => '',
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));

}