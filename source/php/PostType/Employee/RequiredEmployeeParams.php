<?php

namespace VolunteerManager\PostType\Employee;

use VolunteerManager\API\ValidateRestRequest;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Request;

class RequiredEmployeeParams extends ValidateRestRequest
{
    protected function validator(WP_REST_Request $request): WP_REST_Request|WP_Error
    {
        $required_parameter_keys = [
            'first_name',
            'surname',
            'national_identity_number',
            'email',
        ];

        $required_parameter_values = [];
        foreach ($required_parameter_keys as $key) {
            $required_parameter_values[$key] = $request->get_param($key);
        }

        foreach ($required_parameter_values as $key => $value) {
            if (empty($value)) {
                return WPResponseFactory::wp_error_response(
                    'avm_employee_registration_error',
                    __('Missing required parameter', AVM_TEXT_DOMAIN),
                    $key
                );
            }
        }

        return $request;
    }
}
