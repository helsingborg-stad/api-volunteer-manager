<?php

namespace VolunteerManager\Employee;

use VolunteerManager\Api;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class EmployeeApiValidator implements IEmployeeApiValidator
{
    public function validate_required_params($params)
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

    public function is_national_identity_number_in_use(string $national_identity_number): bool
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

    public function is_email_in_use($email): bool
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
}
