<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_639308fb101ce',
    'title' => __('Employer', 'api-volunteer-manager'),
    'fields' => array(
        0 => array(
            'key' => 'field_639af11ba5ae7',
            'label' => __('Employer', 'api-volunteer-manager'),
            'name' => 'external_employer',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => __('Enable if the employer is external ie. not arranged by the city.', 'api-volunteer-manager'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('If the employer is external.', 'api-volunteer-manager'),
            'default_value' => 1,
            'ui_on_text' => __('External', 'api-volunteer-manager'),
            'ui_off_text' => __('Internal', 'api-volunteer-manager'),
            'ui' => 1,
        ),
        1 => array(
            'key' => 'field_63dce23b8f9ce',
            'label' => __('Name', 'api-volunteer-manager'),
            'name' => 'employer_name',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => __('Name of the company/organisation.', 'api-volunteer-manager'),
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_639af11ba5ae7',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
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
            'key' => 'field_639af218edf0b',
            'label' => __('Contacts', 'api-volunteer-manager'),
            'name' => 'employer_contacts',
            'aria-label' => '',
            'type' => 'repeater',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_639af11ba5ae7',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'layout' => 'table',
            'pagination' => 0,
            'min' => 0,
            'max' => 0,
            'collapsed' => '',
            'button_label' => __('Add Contact', 'api-volunteer-manager'),
            'rows_per_page' => 20,
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_639af23aedf0c',
                    'label' => __('Name', 'api-volunteer-manager'),
                    'name' => 'name',
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
                    'parent_repeater' => 'field_639af218edf0b',
                ),
                1 => array(
                    'key' => 'field_639af248edf0d',
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
                    'parent_repeater' => 'field_639af218edf0b',
                ),
                2 => array(
                    'key' => 'field_639af25aedf0e',
                    'label' => __('Phone', 'api-volunteer-manager'),
                    'name' => 'phone',
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
                    'parent_repeater' => 'field_639af218edf0b',
                ),
            ),
        ),
        3 => array(
            'key' => 'field_639af303f57b8',
            'label' => __('Website', 'api-volunteer-manager'),
            'name' => 'employer_website',
            'aria-label' => '',
            'type' => 'url',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_639af11ba5ae7',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
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
    'label_placement' => 'left',
    'instruction_placement' => 'label',
    'hide_on_screen' => array(
        0 => 'excerpt',
        1 => 'discussion',
        2 => 'comments',
        3 => 'revisions',
        4 => 'slug',
        5 => 'author',
        6 => 'format',
        7 => 'page_attributes',
        8 => 'featured_image',
        9 => 'categories',
        10 => 'tags',
        11 => 'send-trackbacks',
    ),
    'active' => true,
    'description' => 'Metadata',
    'show_in_rest' => 1,
));
}