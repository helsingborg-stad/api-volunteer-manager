<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_639b06c19d21f',
    'title' => __('Signup', 'api-volunteer-manager'),
    'fields' => array(
        0 => array(
            'key' => 'field_639af11ba5ae7',
            'label' => __('Internal assignment', 'api-volunteer-manager'),
            'name' => 'internal_assignment',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => __('Toggle to indicate whether the assignment is managed internally by the city or externally by a third party.', 'api-volunteer-manager'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'default_value' => 1,
            'ui_on_text' => __('Internal', 'api-volunteer-manager'),
            'ui_off_text' => __('External', 'api-volunteer-manager'),
            'ui' => 1,
        ),
        1 => array(
            'key' => 'field_639b06c1a6e7a',
            'label' => __('Signup methods', 'api-volunteer-manager'),
            'name' => 'signup_methods',
            'aria-label' => '',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_639af11ba5ae7',
                        'operator' => '!=',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'link' => __('Link', 'api-volunteer-manager'),
                'email' => __('Email', 'api-volunteer-manager'),
                'phone' => __('Phone', 'api-volunteer-manager'),
            ),
            'default_value' => array(
            ),
            'return_format' => 'value',
            'allow_custom' => 0,
            'layout' => 'horizontal',
            'toggle' => 1,
            'save_custom' => 0,
        ),
        2 => array(
            'key' => 'field_639b0713a6e7b',
            'label' => __('Email', 'api-volunteer-manager'),
            'name' => 'signup_email',
            'aria-label' => '',
            'type' => 'email',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_639b06c1a6e7a',
                        'operator' => '==',
                        'value' => 'email',
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
            'prepend' => '',
            'append' => '',
        ),
        3 => array(
            'key' => 'field_639b0727a6e7c',
            'label' => __('Phone', 'api-volunteer-manager'),
            'name' => 'signup_phone',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_639b06c1a6e7a',
                        'operator' => '==',
                        'value' => 'phone',
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
        4 => array(
            'key' => 'field_639b072ea6e7d',
            'label' => __('Link', 'api-volunteer-manager'),
            'name' => 'signup_link',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_639b06c1a6e7a',
                        'operator' => '==',
                        'value' => 'link',
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
    'acfe_display_title' => '',
    'acfe_autosync' => '',
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}