<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_642168f6b8c75',
    'title' => __('Application', 'api-volunteer-manager'),
    'fields' => array(
        0 => array(
            'key' => 'field_642168f7ce2da',
            'label' => __('Employee', 'api-volunteer-manager'),
            'name' => 'application_employee',
            'aria-label' => '',
            'type' => 'post_object',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'post_type' => array(
                0 => 'employee',
            ),
            'taxonomy' => '',
            'return_format' => 'object',
            'multiple' => 0,
            'save_custom' => 0,
            'save_post_status' => 'publish',
            'acfe_bidirectional' => array(
                'acfe_bidirectional_enabled' => '0',
            ),
            'allow_null' => 0,
            'ui' => 1,
            'save_post_type' => '',
        ),
        1 => array(
            'key' => 'field_64216a0e847ec',
            'label' => __('Assignment', 'api-volunteer-manager'),
            'name' => 'application_assignment',
            'aria-label' => '',
            'type' => 'post_object',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'post_type' => array(
                0 => 'assignment',
            ),
            'taxonomy' => '',
            'return_format' => 'object',
            'multiple' => 0,
            'save_custom' => 0,
            'save_post_status' => 'publish',
            'acfe_bidirectional' => array(
                'acfe_bidirectional_enabled' => '0',
            ),
            'allow_null' => 0,
            'ui' => 1,
            'save_post_type' => '',
        ),
        2 => array(
            'key' => 'field_64216a974ad23',
            'label' => __('Status', 'api-volunteer-manager'),
            'name' => 'application_status',
            'aria-label' => '',
            'type' => 'taxonomy',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'taxonomy' => 'application-status',
            'add_term' => 0,
            'save_terms' => 1,
            'load_terms' => 1,
            'return_format' => 'object',
            'field_type' => 'select',
            'allow_null' => 0,
            'acfe_bidirectional' => array(
                'acfe_bidirectional_enabled' => '0',
            ),
            'multiple' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'application',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'left',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    'acfe_display_title' => '',
    'acfe_autosync' => array(
        0 => 'php',
        1 => 'json',
    ),
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}