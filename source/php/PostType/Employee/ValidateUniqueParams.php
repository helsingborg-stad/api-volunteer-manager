<?php

namespace VolunteerManager\PostType\Employee;

use VolunteerManager\API\ValidateRestRequest;
use VolunteerManager\API\WPResponseFactory;
use WP_Error;
use WP_REST_Request;

class ValidateUniqueParams extends ValidateRestRequest
{
    public function formatRestRequest(WP_REST_Request $request): WP_REST_Request|WP_Error
    {
        $validation_result = $this->validate_unique_parameters($request);
        if (is_wp_error($validation_result)) {
            return $validation_result;
        }

        return parent::formatRestRequest($request);
    }

    private function validate_unique_parameters(WP_REST_Request $request): WP_REST_Request|WP_Error
    {
        $email_parameter_value = $request->get_param('email');

        $email_unique = (new EmployeeApiValidator())->is_email_unique($email_parameter_value);
        if (!$email_unique) {
            return WPResponseFactory::wp_error_response(
                'avm_employee_registration_error',
                __('Email already exists', AVM_TEXT_DOMAIN),
                'email'
            );
        }

        return $request;
    }
}
