<?php

namespace VolunteerManager\Employee;

use VolunteerManager\Api;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class EmployeeApiManager
{
    // TODO: Set correct endpoint. This is just a placeholder.
    public function registerPostEndpoint()
    {
        (new Api())->registerPostEndpoint(
            'employee',
            array($this, 'registerEmployee')
        );
    }

// TODO: Wrap DB operations (`wp_insert_post` and `update_post_meta`) in transactions.

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

        // Loop through the params and return WP_Error and status code 400, if any of them are empty
        $validation_result = $this->validate_required_params($params);
        if (is_wp_error($validation_result)) {
            return $validation_result;
        }

        // Check if the email address is already in use
        $emailInUse = $this->is_email_in_use($params['email']);
        if ($emailInUse) {
            return WPResponseFactory::wp_error_response(
                'avm_employee_registration_error',
                __('Email address already in use', AVM_TEXT_DOMAIN),
                'email'
            );
        }

        // Check if the national identity number is already in use
        $nationalIdentityNumberInUse = $this->is_national_identity_number_in_use($params['national_identity_number']);
        if ($nationalIdentityNumberInUse) {
            return WPResponseFactory::wp_error_response(
                'avm_employee_registration_error',
                __('National identity number already in use', AVM_TEXT_DOMAIN),
                'national_identity_number'
            );
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
            update_post_meta($employeePostId, 'employment_status', $new_status_term->term_id);
        }

        return WPResponseFactory::wp_rest_response(
            __('Employee created', AVM_TEXT_DOMAIN),
            $employeePostId
        );
    }

    protected function validate_required_params($params)
    {
        foreach ($params as $key => $value) {
            if (empty($value)) {
                return WPResponseFactory::wp_error_response(
                    'avm_employee_registration_error',
                    __('Missing required parameter', AVM_TEXT_DOMAIN),
                    $key
                );
            }
        }

        return true;
    }

    protected function is_email_in_use($email): bool
    {
        $employees = get_posts(array(
            'post_type' => 'employee',
            'meta_query' => array(
                array(
                    'key' => 'email',
                    'value' => $email,
                    'compare' => '=',
                )
            )
        ));

        return !empty($employees);
    }

    protected function is_national_identity_number_in_use($national_identity_number): bool
    {
        $employees = get_posts(array(
            'post_type' => 'employee',
            'meta_query' => array(
                array(
                    'key' => 'national_identity_number',
                    'value' => $national_identity_number,
                    'compare' => '=',
                )
            )
        ));

        return !empty($employees);
    }
}
