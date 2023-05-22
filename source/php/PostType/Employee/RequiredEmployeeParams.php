<?php

namespace VolunteerManager\PostType\Employee;

use VolunteerManager\API\ValidateRestRequest;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;

class RequiredEmployeeParams extends ValidateRestRequest
{
    protected function validator(array $request): array|WP_Error
    {
        $required_parameter_keys = [
            'first_name',
            'surname',
            'national_identity_number',
            'email',
        ];

        foreach ($required_parameter_keys as $key) {
            if (empty($request[$key])) {
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
