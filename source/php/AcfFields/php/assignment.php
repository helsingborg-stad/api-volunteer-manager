<?php 



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
        ),
        1 => array(
            'key' => 'field_63dcdb011a906',
            'label' => __('Qualifications', 'api-volunteer-manager'),
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
        ),
        4 => array(
            'key' => 'field_63dcff972b04c',
            'label' => __('Number of vacancies', 'api-volunteer-manager'),
            'name' => 'vacancies',
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
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 1,
));
