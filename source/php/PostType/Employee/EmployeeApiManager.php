<?php

namespace VolunteerManager\PostType\Employee;

use VolunteerManager\API\FormatRequest;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class EmployeeApiManager
{
    public function addHooks()
    {
        add_action('rest_api_init', array($this, 'registerPostEndpoint'));
    }

    public function registerPostEndpoint()
    {
        register_rest_route(
            'wp/v2',
            'employee',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'handlePostRequest'),
                'permission_callback' => function () {
                    return true;
                },
            )
        );
    }

    public function handlePostRequest(WP_REST_Request $request)
    {
        // TODO: Remove once JWT is implemented.
        $validated_params_array = $request->get_params();

        $format_request = new FormatRequest();
        $unique_params = new ValidateUniqueParams($format_request);
        $required_params = new RequiredEmployeeParams($unique_params);

        $validated_params = $required_params->formatRestRequest($validated_params_array);
        if (is_wp_error($validated_params)) {
            return $validated_params;
        }

        return $this->registerEmployee($validated_params);
    }

    /**
     * Callback function to handle the employee registration POST request
     *
     * @param array $request
     * @return WP_REST_Response
     */
    public function registerEmployee(array $request)
    {
        // TODO: Handle all params

        // Create the employee post
        $employeePostId = wp_insert_post(
            array(
                'post_title' => $request['first_name'] . ' ' . $request['surname'],
                'post_type' => 'employee',
                'post_status' => 'pending',
                'post_date_gmt' => current_time('mysql', true),
                'post_modified_gmt' => current_time('mysql', true)
            )
        );

        foreach ($request as $key => $value) {
            update_post_meta($employeePostId, $key, $value);
        }

        $new_status_term = get_term_by('slug', 'new', 'employee-registration-status');
        if ($new_status_term) {
            wp_set_post_terms($employeePostId, array($new_status_term->term_id), 'employee-registration-status');
        }

        return WPResponseFactory::wp_rest_response(
            __('Employee created', AVM_TEXT_DOMAIN),
            $employeePostId
        );
    }
}
