<?php

namespace VolunteerManager\Employee;

use VolunteerManager\API\Api;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class EmployeeApiManager
{
    private EmployeeApiValidator $validator;

    public function __construct(IEmployeeApiValidator $validator)
    {
        $this->validator = $validator;
    }

    public function registerPostEndpoint()
    {
        (new Api())->registerPostEndpoint(
            'employee',
            array($this, 'registerEmployee'),
            $this->validator
        );
    }

    /**
     * Callback function to handle the employee registration POST request
     *
     * @param WP_REST_Request $request
     * @return WP_Error|WP_REST_Response
     */
    public function registerEmployee(WP_REST_Request $request)
    {
        $param_keys = [
            'first_name',
            'surname',
            'national_identity_number',
            'email',
        ];

        $params = [];
        foreach ($param_keys as $key) {
            $params[$key] = $request->get_param($key);
        }

        // TODO: Handle all params

        // Loop through the params and return WP_Error and status code 400 if a param is empty
        $validation_result = $this->validator->validate_required_params($params);
        if (is_wp_error($validation_result)) {
            return $validation_result;
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

        foreach ($param_keys as $key) {
            update_post_meta($employeePostId, $key, $params[$key]);
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
