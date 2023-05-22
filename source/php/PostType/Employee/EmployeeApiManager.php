<?php

namespace VolunteerManager\PostType\Employee;

use VolunteerManager\API\FormatRequest;
use VolunteerManager\API\WPResponseFactory;
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
        $format_request = new FormatRequest();
        $unique_params = new ValidateUniqueParams($format_request);
        $required_params = new RequiredEmployeeParams($unique_params);

        $validated_params = $required_params->formatRestRequest($request);
        if (is_wp_error($validated_params)) {
            return $validated_params;
        }

        return $this->registerEmployee($request);
    }

    /**
     * Callback function to handle the employee registration POST request
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function registerEmployee(WP_REST_Request $request): WP_REST_Response
    {
        $request_decoded_token = $request->get_param('decoded_token');
        $decode_token_params = [
            'first_name',
            'surname',
            'national_identity_number',
        ];
        $request_params = [
            'email',
        ];

        $params = [];
        foreach ($decode_token_params as $param) {
            $params[$param] = $request_decoded_token[$param];
        }
        foreach ($request_params as $param) {
            $params[$param] = $request->get_param($param);
        }

        // Create the employee post
        $employeePostId = wp_insert_post(
            array(
                'post_title' => $params['first_name'] . ' ' . $params['surname'],
                'post_type' => 'employee',
                'post_status' => 'pending',
                'post_date_gmt' => current_time('mysql', true),
                'post_modified_gmt' => current_time('mysql', true)
            )
        );

        foreach ($params as $key => $value) {
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
