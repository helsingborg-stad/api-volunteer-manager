<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_639b042622dd1',
    'title' => __('Employee', 'api-volunteer-manager'),
    'fields' => array(
        0 => array(
            'key' => 'field_63e61cfeacd0e',
            'label' => __('First name', 'api-volunteer-manager'),
            'name' => 'first_name',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
        1 => array(
            'key' => 'field_63e61ca6acd0d',
            'label' => __('Surname', 'api-volunteer-manager'),
            'name' => 'surname',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
        2 => array(
            'key' => 'field_63ea315cdc228',
            'label' => __('Swedish national identity number', 'api-volunteer-manager'),
            'name' => 'national_identity_number',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
        3 => array(
            'key' => 'field_639b0426d0ba5',
            'label' => __('Email', 'api-volunteer-manager'),
            'name' => 'email',
            'aria-label' => '',
            'type' => 'email',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
        4 => array(
            'key' => 'field_639b04e97810f',
            'label' => __('Phone number', 'api-volunteer-manager'),
            'name' => 'phone_number',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
        5 => array(
            'key' => 'field_63ea300a56bf7',
            'label' => __('Employment status', 'api-volunteer-manager'),
            'name' => 'employment_status',
            'aria-label' => '',
            'type' => 'acfe_taxonomy_terms',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'taxonomy' => array(
                0 => 'employee-registration-status',
            ),
            'allow_terms' => array(
                0 => 'all_employee-registration-status',
            ),
            'allow_level' => '',
            'field_type' => 'select',
            'default_value' => array(
                0 => __('new', 'api-volunteer-manager'),
            ),
            'return_format' => 'id',
            'ui' => 0,
            'allow_null' => 0,
            'multiple' => 0,
            'save_terms' => 1,
            'load_terms' => 1,
            'choices' => array(
            ),
            'ajax' => 0,
            'placeholder' => '',
            'search_placeholder' => '',
            'layout' => '',
            'toggle' => 0,
            'allow_custom' => 0,
            'other_choice' => 0,
        ),
        6 => array(
            'key' => 'field_63e65b89379a0',
            'label' => __('Crime record extracts', 'api-volunteer-manager'),
            'name' => 'crime_record_extracted',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('Indicates wheter requests for criminal record extracts have been made.', 'api-volunteer-manager'),
            'default_value' => 0,
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
        7 => array(
            'key' => 'field_63e65d2b379a1',
            'label' => __('Crime record extracted at date', 'api-volunteer-manager'),
            'name' => 'crime_record_extracted_date',
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
            'display_format' => 'd/m/Y',
            'return_format' => 'd/m/Y',
            'first_day' => 1,
        ),
        8 => array(
            'key' => 'field_63ea311bdc227',
            'label' => __('Registration date', 'api-volunteer-manager'),
            'name' => 'registration_date',
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
        ),
        9 => array(
            'key' => 'field_63e65e0d379a2',
            'label' => __('Swedish Language Proficiency', 'api-volunteer-manager'),
            'name' => 'swedish_language_proficiency',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'Fluent' => __('Fluent', 'api-volunteer-manager'),
                'Basic' => __('Basic', 'api-volunteer-manager'),
                'Lacks language proficiency' => __('Lacks language proficiency', 'api-volunteer-manager'),
            ),
            'default_value' => false,
            'return_format' => 'value',
            'multiple' => 0,
            'allow_null' => 0,
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
        ),
        10 => array(
            'key' => 'field_63e61d69acd0f',
            'label' => __('Newsletter', 'api-volunteer-manager'),
            'name' => 'newsletter',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => __('Indicates if employee subscribes to newsletter.', 'api-volunteer-manager'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'default_value' => 0,
            'ui' => 0,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
        11 => array(
            'key' => 'field_641c54b9b6d8b',
            'label' => __('Notes about employee', 'api-volunteer-manager'),
            'name' => 'notes_about_employee',
            'aria-label' => '',
            'type' => 'repeater',
            'instructions' => __('This field can be used for taking notes about the employee.', 'api-volunteer-manager'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'acfe_repeater_stylised_button' => 1,
            'layout' => 'table',
            'pagination' => 0,
            'min' => 0,
            'max' => 0,
            'collapsed' => '',
            'button_label' => __('Add Row', 'api-volunteer-manager'),
            'rows_per_page' => 20,
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_641c5531b6d8c',
                    'label' => __('Notes', 'api-volunteer-manager'),
                    'name' => 'notes',
                    'aria-label' => '',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'acfe_textarea_code' => 0,
                    'maxlength' => '',
                    'rows' => '',
                    'placeholder' => '',
                    'new_lines' => '',
                    'parent_repeater' => 'field_641c54b9b6d8b',
                ),
                1 => array(
                    'key' => 'field_641c5611b6d8d',
                    'label' => __('Date updated', 'api-volunteer-manager'),
                    'name' => 'notes_date_updated',
                    'aria-label' => '',
                    'type' => 'date_picker',
                    'instructions' => __('Used for taking track of when the note was taken.', 'api-volunteer-manager'),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'display_format' => 'Y-m-d',
                    'return_format' => 'd/m/Y',
                    'first_day' => 1,
                    'default_value' => __('2023-03-23', 'api-volunteer-manager'),
                    'parent_repeater' => 'field_641c54b9b6d8b',
                ),
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'employee',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
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