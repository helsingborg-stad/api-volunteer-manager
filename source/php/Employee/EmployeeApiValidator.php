<?php

namespace VolunteerManager\Employee;

use VolunteerManager\API\WPResponseFactory;
use WP_Error;

class EmployeeApiValidator implements IEmployeeApiValidator
{
    /**
     * @param $params
     * @return bool|WP_Error
     */
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

    /**
     * @param string $national_identity_number
     * @return bool|WP_Error
     */
    public function is_national_identity_unique(string $national_identity_number): bool
    {
        $employees = get_posts(array(
            'post_type' => 'employee',
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'national_identity_number',
                    'value' => $national_identity_number,
                    'compare' => '=',
                )
            )
        ));

        return empty($employees);
    }

    /**
     * @param string $email
     * @return bool|WP_Error
     */
    public function is_email_unique($email): bool
    {
        $employees = get_posts(array(
            'post_type' => 'employee',
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'email',
                    'value' => $email,
                    'compare' => '=',
                )
            )
        ));

        return empty($employees);
    }
}
